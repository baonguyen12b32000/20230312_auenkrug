<?php

/**
 * @package         Convert Forms
 * @version         3.2.12 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2018 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace ConvertForms\Field;

defined('_JEXEC') or die('Restricted access');

class EmptySpace extends \ConvertForms\Field
{
	/**
	 * Indicates the default required behavior on the form
	 *
	 * @var bool
	 */
	protected $required = false; 

	/**
	 *  Remove common fields from the form rendering
	 *
	 *  @var  mixed
	 */
	protected $excludeFields = [
		'name',
		'placeholder',
		'browserautocomplete',
		'size',
		'required',
		'label',
		'description',
		'cssclass',
		'hidelabel',
		'inputcssclass',
		'value'
	];
}

?>