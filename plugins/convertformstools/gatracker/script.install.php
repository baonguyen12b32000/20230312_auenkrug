<?php

/**
 * @package         Convert Forms
 * @version         4.0.0 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2019 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

require_once __DIR__ . '/script.install.helper.php';

class PlgConvertFormsToolsGATrackerInstallerScript extends PlgConvertFormsToolsGATrackerInstallerScriptHelper
{
	public $name = 'gatracker';
	public $alias = 'gatracker';
	public $extension_type = 'plugin';
	public $plugin_folder = 'convertformstools';
	public $show_message = false;
	public $autopublish = false;
}