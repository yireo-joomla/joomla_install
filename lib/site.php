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
        if(!defined('DOCUMENT_ROOT')) define('DOCUMENT_ROOT', $this->root);
        if(!defined('_JEXEC')) define('_JEXEC', 1);
        if(!defined('JPATH_BASE')) define('JPATH_BASE', DOCUMENT_ROOT);
        if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR );

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
