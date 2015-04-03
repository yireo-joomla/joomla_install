<?php
/**
 * @package    Joomla.Cli
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// Make sure we're being called from the command line, not a web interface
if (PHP_SAPI !== 'cli')
{
	die('This is a command line only application.');
}

// We are a valid entry point.
const _JEXEC = 1;

// Load system defines
if (file_exists(dirname(__DIR__) . '/defines.php'))
{
	require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', dirname(__DIR__));
	require_once JPATH_BASE . '/includes/defines.php';
}

// Get the framework.
require_once JPATH_LIBRARIES . '/import.legacy.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';

// Import the configuration.
require_once JPATH_CONFIGURATION . '/configuration.php';

// System configuration.
$config = new JConfig;

// Configure error reporting to maximum for CLI output.
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load Library language
$lang = JFactory::getLanguage();

// Try the files_joomla file in the current language (without allowing the loading of the file in the default language)
$lang->load('files_joomla.sys', JPATH_SITE, null, false, false)
// Fallback to the files_joomla file in the default language
|| $lang->load('files_joomla.sys', JPATH_SITE, null, true);

/**
 * A command line script to run some tasks for PBF sessions
 *
 * @since  3.0
 */
class PbfCli extends JApplicationCli
{
	/**
	 * Entry point for CLI script
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function doExecute()
	{
        $_SERVER['HTTP_HOST'] = 'domain.com';
		JFactory::getApplication('site');

		$this->togglePlugin('debug', 'system', 0);

		$this->resetPassword();
	}

    public function resetPassword()
    {
        $jsonFile = JPATH_ROOT.'/credentials.json';

        if (file_exists($jsonFile) == false)
        {
            return false;
        }

        $data = json_decode(file_get_contents($jsonFile), true);

        if (empty($data))
        {
            return false;
        }

        $username = $data['credentials']['username'];
        $password = $data['credentials']['password'];

        $password = JUserHelper::hashPassword($password); 

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query
            ->update($db->quoteName('#__users'))
            ->set($db->quoteName('password') . ' = ' . $db->quote($password))
            ->set($db->quoteName('username') . ' = ' . $db->quote($username))
            ->where(
                array(
                    $db->quoteName('username') . '= "admin"'
                )
            );

        $db->setQuery($query);
        $db->execute();

        return true;
    }

    public function togglePlugin($name, $folder, $state = 0)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query
            ->update($db->quoteName('#__extensions'))
            ->set($db->quoteName('enabled') . ' = ' . (int)$state)
            ->where(
                array(
                    $db->quoteName('type') . '=' . $db->quote('plugin'),
                    $db->quoteName('element') . '=' . $db->quote($name),
                    $db->quoteName('folder') . '=' . $db->quote($folder)
                )
            );

        $db->setQuery($query);
        $db->execute();

        return true;
    }
}

// Instantiate the application object, passing the class name to JCli::getInstance
// and use chaining to execute the application.
JApplicationCli::getInstance('PbfCli')->execute();
