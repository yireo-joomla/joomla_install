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

            $site = array(
                'number' => $i,
                'name' => $name,
                'folder' => $folder,
                'username' => 'pbf',
                'password' => 'pbf',
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
        $this->db->createDb($site);

        $folder = $this->root.'/'.$site;
        @exec('cp -R source/joomla-cms '.$folder);
    }

    public function destroySite($number)
    {
        if($this->isAuthorized() == false) {
            return false;
        }

        $this->db->dropDb('joomla'.$number);

        // @todo: Find a very very secure way to do this?
        $cmd = 'rm -r '.$this->root.'/joomla'.$number;
        exec($cmd);
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
        // @todo: Create some kind of authentication here
        return true;
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
}
