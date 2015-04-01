<?php
class Site
{
    protected $siteName = null;

    public function __construct($siteName = null)
    {
        $this->siteName = $siteName;
        $this->root = dirname(__DIR__).'/'.$siteName.'/';
        $this->initJoomla();
    }

    public function initJoomla()
    {
        ini_set('display_errors', 0);
        define('DOCUMENT_ROOT', $this->root);
        define('_JEXEC', 1);
        define('JPATH_BASE', DOCUMENT_ROOT);
        define('DS', DIRECTORY_SEPARATOR );

        // Change the path to the JPATH_BASE
        if(!is_file(JPATH_BASE.DS.'includes'.DS.'framework.php')) {
            die('Incorrect Joomla! base-path');
        }
        chdir(JPATH_BASE);

        // Include the framework
        require_once(JPATH_BASE.DS.'includes'.DS.'defines.php');
        require_once(JPATH_BASE.DS.'includes'.DS.'framework.php');
        jimport('joomla.environment.request');
        jimport('joomla.database.database');

        // Start the application
        $app = JFactory::getApplication('site');
        $app->initialise();
    }

    public function getDbo()
    {
        return JFactory::getDbo();
    }
}
