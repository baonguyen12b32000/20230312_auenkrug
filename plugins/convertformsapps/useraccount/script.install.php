<?php

/**
 * @package         Convert Forms
 * @version         4.0.0 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

require_once __DIR__ . '/script.install.helper.php';

class PlgConvertFormsAppsUserAccountInstallerScript extends PlgConvertFormsAppsUserAccountInstallerScriptHelper
{
	public $name = 'PLG_CONVERTFORMSAPPS_USERACCOUNT';
	public $alias = 'useraccount';
	public $extension_type = 'plugin';
	public $plugin_folder = 'convertformsapps';
	public $show_message = false;
}
