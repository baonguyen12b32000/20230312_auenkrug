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

class plgConvertFormsMailChimp extends \ConvertForms\Plugin
{
	/**
	 *  Main method to store data to service
	 *
	 *  @return  void
	 */
	function subscribe()
	{
		$api = new NR_MailChimp(array('api' => $this->lead->campaign->api));
		$api->subscribe(
			$this->lead->email,
			$this->lead->campaign->list,
			$this->lead->params,
			$this->lead->campaign->updateexisting,
			$this->lead->campaign->doubleoptin
		);
		
		if (!$api->success())
		{
			$error = $api->getLastError();
			$error_parts = explode(' ', $error);

			if (function_exists('mb_strpos'))
			{
				// Make MalChimp errors translatable
				if (mb_strpos($error, 'is already a list member') !== false)
				{
					$error = JText::sprintf('COM_CONVERTFORMS_ERROR_USER_ALREADY_EXIST', $error_parts[0]);
				}
	
				if (mb_strpos($error, 'fake or invalid') !== false)
				{
					$error = JText::sprintf('COM_CONVERTFORMS_ERROR_INVALID_EMAIL_ADDRESS', $error_parts[0]);
				}
			}

			throw new Exception($error);
		}
	}
}