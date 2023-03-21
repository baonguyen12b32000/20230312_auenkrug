<?php

/**
 * @package         Advanced Custom Fields
 * @version         2.3.0 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2019 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

use Joomla\Registry\Registry;

class ACFGalleryHelper
{
	/**
	 * Prepares the Gallery Manager Widget uploaded files prior to being passed
	 * to the Gallery Widget to display the Gallery on the front-end.
	 * 
	 * @param   array  $items
	 * 
	 * @return  array
	 */
	public static function prepareItems($items)
	{
		foreach ($items as $key => &$item)
		{
			// Skip items that have not saved properly(items were still uploading and we saved the item)
			if ($key === 'ITEM_ID')
			{
				unset($items[$key]);
				continue;
			}

			$item = array_merge($item, [
				'url' =>  JURI::root() . $item['image'],
				'thumbnail_url' => JURI::root() . $item['thumbnail']
			]);
		}

		return $items;
	}
}