<?php
class App
{
    protected $php_binary = null;

    public function __construct(Config $config, Db $db)
    {
        $this->config = $config;
        $this->db = $db;
        $this->root = dirname(__DIR__);

        $php_binary = $this->config->get('server.php_binary');
        if (!empty($php_binary)) {
            $this->php_binary = $php_binary;
        } else {
            $this->php_binary = 'php';
        }

        if ($this->isAuthorized() && isset($_GET['task']) && isset($_GET['site'])) {
            $task = preg_replace('/([^a-zA-Z0-9]+)/', '', $_GET['task']);
            $site = (int)$_GET['site'];

            if($task == 'create') {
                $this->createSite($site);
            }

            if($task == 'destroy') {
                $this->destroySite($site);
            }

            header('Location: index.php');
            exit;
        }
    }

    public function getSites()
    {
        $sites = array();
        $number = $this->config->get('site.number');

        for($i = 1; $i <= $number; $i++) {

            $name = 'joomla'.$i;
            $folder = $name;
            $status = $this->getSiteStatus($folder);
            $actions = $this->getSiteActions($status);
            $credentials = $this->getCredentials($folder);

            $site = array(
                'number' => $i,
                'name' => $name,
                'folder' => $folder,
                'username' => $credentials['username'],
                'password' => $credentials['password'],
                'status' => $status,
                'actions' => $actions,
            );

            $sites[] = $site;
        }

        return $sites;
    }

    public function getSite($siteName)
    {
        $site = new Site($siteName);
        return $site;
    }

    public function createSite($number)
    {
        umask(0022);

        if($this->isAuthorized() == false) {
            return false;
        }

        $site = 'joomla'.$number;
        $siteFolder = $this->root.'/'.$site;
        
        if(is_writable($this->root) == false) {
            die('Folder '.$this->root.' is not writable');
        }

        // Install the site
        $this->runJoomlaCmd('site:create', $site);

        if(is_dir($siteFolder) == false) {
            die('Folder '.$siteFolder.' does not exist');
        }

        if(is_dir($siteFolder.'/libraries') == false) {
            die('Site creation failed');
        }

        // Generate a new admin password
        $this->generateCredentials($site);

        // Copy the htaccess file
        copy($siteFolder.'/htaccess.txt', $siteFolder.'/.htaccess');

        // Copy the CLI file
        copy($this->root.'/source/cli/pbf.php', $siteFolder.'/cli/pbf.php');

        // Run the CLI file
        $cmd = $this->php_binary.' '.$siteFolder.'/cli/pbf.php';
        $this->log('CMD: '.$cmd);
        exec($cmd);

        // Download any extensions to source/extensions
        $extensionUrls = $this->config->get('extensions');
        if(!empty($extensionUrls)) {
            foreach($extensionUrls as $extensionUrl) {
                $this->downloadExtension($extensionUrl);
            }
        }

        // Install the extensions
        $extensions = glob('source/extensions/*');
        foreach($extensions as $extension) {
            $this->runJoomlaCmd('extension:installfile', $site, $extension);
        }

        // Password reset on first login
        $password_reset = (bool)$this->config->get('site.password_reset');
        if($password_reset) {
            $query = 'UPDATE `#__users` SET `requireReset`=1';
            $db = $this->getSite($site)->getDbo();
            $db->setQuery($query);
            $db->execute();
        }
    }

    public function getCredentials($site)
    {
        $jsonFile = $this->root.'/'.$site.'/credentials.json';
        if (file_exists($jsonFile) == false) {
            return array('username' => null, 'password' => null);
        }

        $json = file_get_contents($jsonFile);
        $data = json_decode($json, true);
    
        if (isset($data['credentials'])) {
            return $data['credentials'];
        }

        return array('username' => null, 'password' => null);
    }

    public function generateCredentials($site)
    {
        $password_type = trim($this->config->get('site.password_type'));
        if (empty($password_type)) {
            $password_type = 'default';
        }

        switch($password_type) {
            case 'generate': 
                $username = 'admin';
                $alphabet = 'abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789';
                $alphabet = str_split($alphabet);
                for ($i = 0; $i < 8; $i++) {
                    $n = rand(0, count($alphabet)-1);
                    $password[$i] = $alphabet[$n];
                }
                $password = implode('', $password);
                break;

            case 'admin':
                $username = 'admin';
                $password = 'admin';
                break;

            case 'default':
            default:
                $username = $site;
                $password = $site;
                break;
        }

        $data = array('credentials' => array('username' => $username, 'password' => $password));
        $rt = file_put_contents($this->root.'/'.$site.'/credentials.json', json_encode($data));
        if($rt == false) {
            die('Failed to write to '.$this->root.'/'.$site.'/credentials.json');
        }
    }

    public function destroySite($number)
    {
        if($this->isAuthorized() == false) {
            return false;
        }

        $site = 'joomla'.$number;

        $this->runJoomlaCmd('site:delete', $site);
    }

    public function downloadExtension($extensionUrl)
    {
        $filename = basename($extensionUrl);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $extensionUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
        $data = curl_exec($ch);
        $fd = fopen($this->root.'/source/extensions/'.$filename, 'w');
        fwrite($fd, $data);
        fclose($fd);

        curl_close($ch);
    }

    public function runJoomlaCmd($task, $site, $arguments = null)
    {
        $mysql_username = $this->config->get('mysql.username');
        $mysql_password = $this->config->get('mysql.password');
        $mysql_prefix = $this->config->get('mysql.dbprefix');

        $joomlaCmd = array();
        $joomlaCmd[] = $this->root.'/source/joomla-console/bin/joomla';
        $joomlaCmd[] = $task;
        $joomlaCmd[] = '--www='.$this->root;
        $joomlaCmd[] = '--mysql='.$mysql_username.':'.$mysql_password;
        $joomlaCmd[] = '--mysql_db_prefix='.$mysql_prefix;

        if($task == 'site:create') {
            $joomlaCmd[] = '--sample-data=testing';
        }

        $joomlaCmd[] = $site;

        if(!empty($arguments)) {
            if(is_array($arguments)) {
                $joomlaCmd = array_merge($joomlaCmd, $arguments);
            } else {
                $joomlaCmd[] = $arguments;
            }
        }

        $joomlaCmd = implode(' ', $joomlaCmd).' 2>&1';

        $this->log('CMD: '.$joomlaCmd);
        exec($joomlaCmd);
    }

    public function getSiteStatus($folder)
    {
        $folder = $this->root.'/'.$folder;
        if(is_dir($folder) == false) {
            return 'noapp';
        }

        if(is_file($folder.'/configuration.php') == false) {
            return 'noconfig';
        }

        return 'active';
    }

    public function isAuthorized()
    {
        $siteSecret = $this->config->get('site.secret');

        if (isset($_GET['secret']) && $_GET['secret'] == $siteSecret) {
            if(headers_sent() == false) {
                setcookie('secret', $_GET['secret']);
                header('Location: index.php');
            } else {
                die('Headers already sent');
            }
            return true;
        }

        if (isset($_COOKIE['secret']) && $_COOKIE['secret'] == $siteSecret) {
            return true;
        }

        return false;
    }

    public function getSiteActions($status)
    {
        $actions = array();
        if($this->isAuthorized() == false) {
            return $actions;
        }

        if($status == 'active') {
            $actions[] = 'destroy';
        } else {
            $actions[] = 'create';
        }
        
        return $actions;
    }

    public function log($string)
    {
        if($this->config->get('site.log') == 1) {
            @mkdir($this->root.'/logs');
            $rt = file_put_contents($this->root.'/logs/debug.log', $string."\n", FILE_APPEND);
            if($rt == false) {
                die('Failed to write to '.$this->root.'/logs/debug.log');
            }
        }
    }
}
