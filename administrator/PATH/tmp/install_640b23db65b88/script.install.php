<?php
/**
 * @package         Novarain Extensions Installer
 * @author          Tassos Marinos <info@novarain.com>
 * @link            http://www.novarain.com
 * @copyright       Copyright © 2015 Novarain All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class PlgSystemNovaRainInstallerInstallerScript
{
	/**
	 *  Minimum Joomla required version
	 *
	 *  @var  string
	 */
	private $min_joomla_version = '3.8.0';

	/**
	 *  Minimum PHP required version
	 *
	 *  @var  string
	 */
	private $min_php_version = '7.0.0';

	/**
	 *  Joomla Global Application object
	 *
	 *  @var  object
	 */
	private $app;

	/**
	 *  Class Constructor
	 */
	function __construct()
	{
		$this->app = JFactory::getApplication();
	}

	/**
	 *  Requirements Checks before installation
	 *
	 *  @param   object             $route
	 *  @param   JInstallerAdapter  $adapter
	 *
	 *  @return  bool
	 */
	public function preflight($route, $adapter)
	{
		// Load language for messaging
		JFactory::getLanguage()->load('plg_system_novaraininstaller', __DIR__);

		// Check Joomla and PHP minimum required version
		if (!$this->passMinimumRequirementVersion("joomla"))
		{
			$this->uninstallInstaller();
			return false;
		}

		if (!$this->passMinimumRequirementVersion("php"))
		{
			$this->uninstallInstaller();
			return false;
		}

		// To prevent XML not found error
		$this->createExtensionRoot();

		return true;
	}

	/**
	 *  Trigger After Installer Installation
	 *
	 *  @param   object             $route    
	 *  @param   JInstallerAdapter  $adapter  
	 *
	 *  @return  mixed  False on fail
	 */
	public function postflight($route, $adapter)
	{
		if (!in_array($route, array('install', 'update')))
		{
			return;
		}

		// Install the Framework
		$this->installFramework();

		// Then install the rest of the packages
		if (!$this->installPackages())
		{
			// Uninstall this installer
			$this->uninstallInstaller();
			return false;
		}

		// Update Update-sites
		$this->updateSites();
		
		// Uninstall Installer
		$this->uninstallInstaller();
	}

	private function createExtensionRoot()
	{
		$destination = JPATH_PLUGINS . '/system/novaraininstaller';
		JFolder::create($destination);
		JFile::copy(__DIR__ . '/novaraininstaller.xml', $destination . '/novaraininstaller.xml');
	}

	private function updateSites()
	{
		// Set Download Key & fix Update Sites
		$upds = new NRFramework\Updatesites();
		$upds->update();
	}

	private function installPackages()
	{
		$packages = JFolder::folders(__DIR__ . '/packages');
		$packages = array_diff($packages, array('plg_system_nrframework'));

		foreach ($packages as $package)
		{
			if (!$this->installPackage($package))
			{
				return false;
			}
		}

		return true;
	}

	private function installPackage($package)
	{
		$packagePath = __DIR__ . '/packages/' . $package;
		
		if (!JFolder::exists($packagePath))
		{
			return;
		}

		$tmpInstaller = new JInstaller;
		return $tmpInstaller->install($packagePath);
	}

	private function installFramework()
	{
		$this->installPackage('plg_system_nrframework');

		if (is_file(JPATH_PLUGINS . '/system/nrframework/autoload.php'))
		{
			include_once JPATH_PLUGINS . '/system/nrframework/autoload.php';
		}
	}

	private function passMinimumRequirementVersion($type = "joomla")
	{
		switch ($type)
		{
			case 'joomla':
				if (version_compare(JVERSION, $this->min_joomla_version, '<'))
				{
					$this->app->enqueueMessage(
						JText::sprintf('NRI_NOT_COMPATIBLE_UPDATE', JVERSION, $this->min_joomla_version),
						'error'
					);
					return false;
				}
				break;
			case 'php':
				if (version_compare(PHP_VERSION, $this->min_php_version, '<'))
				{
					$this->app->enqueueMessage(
						JText::sprintf('NRI_NOT_COMPATIBLE_PHP', PHP_VERSION, $this->min_php_version),
						'error'
					);
					return false;
				}
				break;
		}

		return true;
	}

	private function uninstallInstaller()
	{
		if (!JFolder::exists(JPATH_SITE . '/plugins/system/novaraininstaller'))
		{
			return;
		}

		$this->deleteFolders(array(
			JPATH_SITE . '/plugins/system/novaraininstaller/language',
			JPATH_SITE . '/plugins/system/novaraininstaller',
		));

		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->delete('#__extensions')
			->where($db->quoteName('element') . ' = ' . $db->quote('novaraininstaller'))
			->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
			->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
		$db->setQuery($query);
		$db->execute();
	}
	
	public function deleteFolders($folders = array())
	{
		foreach ($folders as $folder)
		{
			if (!is_dir($folder))
			{
				continue;
			}

			JFolder::delete($folder);
		}
	}
}

?>
