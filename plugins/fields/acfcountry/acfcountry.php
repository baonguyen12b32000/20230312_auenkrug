<?php

/**
 * @package         Advanced Custom Fields
 * @version         2.3.0 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

JLoader::register('ACF_Field', JPATH_PLUGINS . '/system/acf/helper/plugin.php');

if (!class_exists('ACF_Field'))
{
	JFactory::getApplication()->enqueueMessage('Advanced Custom Fields System Plugin is missing', 'error');
	return;
}

class PlgFieldsACFCountry extends ACF_Field
{	
	/**
	 *  Override the field type
	 *
	 *  @var  string
	 */
	protected $overrideType = 'NR_Geo';

	/**
	 * Transforms the field into a DOM XML element and appends it as a child on the given parent.
	 *
	 * @param   stdClass    $field   The field.
	 * @param   DOMElement  $parent  The field node parent.
	 * @param   Form        $form    The form.
	 *
	 * @return  DOMElement
	 *
	 * @since   3.7.0
	 */
	public function onCustomFieldsPrepareDom($field, DOMElement $parent, Joomla\CMS\Form\Form $form)
	{
		if (!$fieldNode = parent::onCustomFieldsPrepareDom($field, $parent, $form))
		{
			return $fieldNode;
		}

		$fieldNode->setAttribute('multiple', (bool) $field->fieldparams->get('multiple_selection', false));
		$fieldNode->setAttribute('detect_visitor_country', (bool) $field->fieldparams->get('detect_visitor_country', false));
		return $fieldNode;
	}

	/**
	 * Prepares the field value for the (front-end) layout
	 *
	 * @param   string    $context  The context.
	 * @param   stdclass  $item     The item.
	 * @param   stdclass  $field    The field.
	 *
	 * @return  string
	 */
	public function onCustomFieldsPrepareField($context, $item, $field)
	{
		// Check if the field should be processed by us
		if (!$this->isTypeSupported($field->type))
		{
			return;
		}

		$countries = $field->value;

		if ($field->fieldparams->get('countrydisplay', 'name') == 'name')
		{
			if (!is_array($countries))
			{
				$countries = array($countries);
			}
			
			$countries_temp = array();
			
			foreach ($countries as $c)
			{
				if (!$country = \NRFramework\Countries::getCountry($c))
				{
					continue;
				}
				
				$countries_temp[] = $country['name'];
			}
		
			$countries = $countries_temp;
		}

		$field->value = (is_array($countries)) ? implode (', ', $countries) : $countries;

		return parent::onCustomFieldsPrepareField($context, $item, $field);
	}
}
