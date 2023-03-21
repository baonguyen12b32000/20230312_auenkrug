<?php

/**
 * @package         Convert Forms
 * @version         3.2.12 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

class plgConvertFormsConvertKit extends \ConvertForms\Plugin
{
	/**
	 *  Main method to store data to service
	 *
	 *  @return  void
	 */
	function subscribe()
	{
		$api = new NR_ConvertKit($this->lead->campaign->api);
		$api->subscribe(
			$this->lead->email,
			$this->lead->campaign->formid,
			$this->lead->params
		);
		
		if (!$api->success())
		{
			throw new Exception($api->getLastError());
		}
	}
}