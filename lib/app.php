<?php
class App
{
    public function __construct(Config $config, Db $db)
    {
        $this->config = $config;
        $this->db = $db;
        $this->root = dirname(__DIR__);

        if ($this->isAuthorized() && isset($_REQUEST['task']) && isset($_REQUEST['site'])) {
            $task = preg_replace('/([^a-zA-Z0-9]+)/', '', $_REQUEST['task']);
            $site = (int)$_REQUEST['site'];

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

    public function createSite($number)
    {
        if($this->isAuthorized() == false) {
            return false;
        }

        $site = 'joomla'.$number;
        $siteFolder = $this->root.'/'.$site;

        // Install the site
        $this->runJoomlaCmd('site:create', $site);

        // Generate a new admin password
        $this->generatePassword($site);

        // Copy the htaccess file
        copy($siteFolder.'/htaccess.txt', $siteFolder.'/.htaccess');

        // Copy the CLI file
        copy($this->root.'/source/cli/pbf.php', $siteFolder.'/cli/pbf.php');

        // Run the CLI file
        @exec('php '.$siteFolder.'/cli/pbf.php');

        // Install the extensions
        $extensions = glob('source/extensions/*');
        foreach($extensions as $extension) {
            $this->runJoomlaCmd('extension:installfile', $site, $extension);
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

    public function generatePassword($site)
    {
        $alphabet = 'abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789';
        $alphabet = str_split($alphabet);
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, count($alphabet)-1);
            $pass[$i] = $alphabet[$n];
        }
        $pass = implode('', $pass);

        $data = array('credentials' => array('username' => 'admin', 'password' => $pass));
        file_put_contents($this->root.'/'.$site.'/credentials.json', json_encode($data));
    }

    public function destroySite($number)
    {
        if($this->isAuthorized() == false) {
            return false;
        }

        $site = 'joomla'.$number;

        $this->runJoomlaCmd('site:delete', $site);
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

        $joomlaCmd = implode(' ', $joomlaCmd);
        $this->log('CMD: '.$joomlaCmd);
        @exec($joomlaCmd, $output);
        $this->log('OUTPUT: '.var_export($output, true));
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

        if (isset($_REQUEST['secret']) && $_REQUEST['secret'] == $siteSecret) {
            setcookie('secret', $_REQUEST['secret']);
            header('Location: index.php');
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
        @mkdir($this->root.'/logs');
        file_put_contents($this->root.'/logs/debug.log', $string."\n", FILE_APPEND);
    }
}
