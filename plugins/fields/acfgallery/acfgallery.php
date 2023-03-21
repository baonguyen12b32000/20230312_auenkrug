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

defined('_JEXEC') or die;

JLoader::register('ACF_Field', JPATH_PLUGINS . '/system/acf/helper/plugin.php');

use NRFramework\Helpers\Widgets\GalleryManager;
use Joomla\Registry\Registry;

class PlgFieldsACFGallery extends ACF_Field
{
	/**
	 *  The validation rule will be used to validate the field on saving
	 *
	 *  @var  string
	 */
	protected $validate = 'acfrequired';

	public function onContentAfterSave($context, $item, $isNew, $data = [])
	{
		if (!is_array($data))
		{
			return true;
		}
		
		if (!isset($data['com_fields']))
		{
			return true;
		}
		
		// Create correct context for category
		if ($context == 'com_categories.category')
		{
			$context = $item->get('extension') . '.categories';
		}

        // Load Fields Component Helper class
		JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');

		// Check the context
		$parts = FieldsHelper::extract($context, $item);

		if (!$parts)
		{
			return true;
		}

		// Compile the right context for the fields
		$context = $parts[0] . '.' . $parts[1];

		// Loading the fields
		$fields = FieldsHelper::getFields($context, $item);

		if (!$fields)
		{
			return true;
		}

		// Get the fields data
		$fieldsData = !empty($data['com_fields']) ? $data['com_fields'] : [];

		// Whether we should clean up the temp folder at the end of this process
		$should_clean = false;

		// Get the Fields Model
		if (!defined('nrJ4'))
		{
			$model = JModelLegacy::getInstance('Field', 'FieldsModel', ['ignore_request' => true]);
		}
		else
		{
			$model = \JFactory::getApplication()->bootComponent('com_fields')->getMVCFactory()->createModel('Field', 'Administrator', ['ignore_request' => true]);
		}

		// Cache subform fields
		$subform_fields = [];

		// Loop over the fields
		foreach ($fields as $field)
		{
			$field_type = $field->type;
			
			/**
			 * Check whether a Gallery field is used within the Subform field.
			 */
			if ($field_type === 'subform')
			{
				// Ensure it has a value
				if (!$subform_value = json_decode($field->value, true))
				{
					continue;
				}

				foreach ($subform_value as $key => &$value)
				{
					foreach ($value as $_key => &$_value)
					{
						// Get Field ID
						$field_id = str_replace('field', '', $_key);
						
						// Get Field by ID
						$subform_field = isset($subform_fields[$field_id]) ? $subform_fields[$field_id] : $model->getItem($field_id);

						// Only proceed for this field type
						if ($subform_field->type !== $this->_name)
						{
							continue;
						}

						// Cache field
						if (!isset($subform_fields[$field_id]))
						{
							$subformfields[$field_id] = $subform_field;
						}

						// We should run our cleanup routine at the end
						$should_clean = true;
						
						// If the value is an array, decode it to get the items and make the value an array in order to prepare it to store the items
						if (is_string($_value))
						{
							$items = json_decode($_value, true);
							$items = $items['items'];
							$_value = [];
						}
						else
						{
							$items = $_value['items'];
						}

						// Move to destination
						GalleryManager::moveTempItemsToDestination($items, $this->getDestinationFolder($subform_field, $item));

						$_value['items'] = $items;
					}
				}

				// Update subform field
				$model->setFieldValue($field->id, $item->get('id'), json_encode($subform_value));
			}
			else
			{
				// Only proceed for this field type
				if ($field_type !== $this->_name)
				{
					continue;
				}
	
				// Determine the value if it is available from the data
				$value = array_key_exists($field->name, $fieldsData) ? $fieldsData[$field->name] : null;
	
				if (!$value)
				{
					continue;
				}
	
				// We should run our cleanup routine at the end
				$should_clean = true;

				// If the value is an array, decode it to get the items and make the value an array in order to prepare it to store the items
				if (is_string($value))
				{
					$items = json_decode($value, true);
					$items = $items['items'];
					$value = [];
				}
				else
				{
					$items = $value['items'];
				}
				
				// Move to final temp folder
				GalleryManager::moveTempItemsToDestination($items, $this->getDestinationFolder($field, $item));
				
				$value['items'] = $items;
				
				// Setting the value for the field and the item
				$model->setFieldValue($field->id, $item->get('id'), json_encode($value));
			}
		}

		if ($should_clean)
		{
			// Clean old files from temp folder
			GalleryManager::clean();
		}
	}

	/**
	 * Returns the destination folder.
	 * 
	 * @param   object  $field
	 * @param   array   $item
	 * 
	 * @return  string
	 */
	private function getDestinationFolder($field, $item)
	{
		$ds = DIRECTORY_SEPARATOR;
		$destination_folder = null;

		$field_id = $field->id;
		$item_id = $item->id;
		
		// Make field params use Registry
		if (!$field->fieldparams instanceof Registry)
		{
			$field->fieldparams = new Registry($field->fieldparams);
		}

		switch ($field->fieldparams->get('upload_folder_type', 'auto'))
		{
			case 'auto':
			default:
				// Get context and remove `com_` part
				$context = preg_replace('/^com_/', '', JFactory::getApplication()->input->get('option'));
				$destination_folder = ['media', 'acfgallery', $context, $item_id, $field_id];
				break;
			case 'custom':
				$upload_folder = trim(ltrim($field->fieldparams->get('upload_folder'), $ds), $ds);

				// Smart Tags Instance
				$st = new \NRFramework\SmartTags();

				// Add custom Smart Tags
				$custom_tags = [
					'field_id' => $field_id,
					'item_id' => $item_id,
					'item_alias' => isset($item->alias) ? $item->alias : ''
				];
				$st->add($custom_tags, 'field.');

				// Replace Smart Tags
				$upload_folder = $st->replace($upload_folder);

				$destination_folder = [$upload_folder];
				break;
		}

		return implode($ds, array_merge([JPATH_ROOT], $destination_folder)) . $ds;
	}

    /**
	 * Transforms the field into a DOM XML element and appends it as a child on the given parent.
	 *
	 * @param   stdClass    $field   The field.
	 * @param   DOMElement  $parent  The field node parent.
	 * @param   JForm       $form    The form.
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
        
		$fieldNode->setAttribute('field_id', $field->id);

		return $fieldNode;
	}

	/**
	 * The form event. Load additional parameters when available into the field form.
	 * Only when the type of the form is of interest.
	 *
	 * @param   JForm     $form  The form
	 * @param   stdClass  $data  The data
	 *
	 * @return  void
	 */
	public function onContentPrepareForm(Joomla\CMS\Form\Form $form, $data)
	{
		// Make sure we are manipulating the right field.
		if (isset($data->type) && $data->type != $this->_name)
		{
			return;
		}

		$result = parent::onContentPrepareForm($form, $data);

		// Display the server's maximum upload size in the field's description
		$max_upload_size_str = \JHtml::_('number.bytes', \JUtility::getMaxUploadSize());
		$field_desc = $form->getFieldAttribute('max_file_size', 'description', null, 'fieldparams');
		$form->setFieldAttribute('max_file_size', 'description', JText::sprintf($field_desc, $max_upload_size_str), 'fieldparams');
		
		// Set the Field ID in Upload Folder Type description (if field is saved), otherwise, show FIELD_ID placeholder.
		// ITEM_ID is not replaceable in the field settings.
		$field_id = isset($data->id) ? $data->id : 'FIELD_ID';
		$upload_folder_type_desc = $form->getFieldAttribute('upload_folder_type', 'description', null, 'fieldparams');
		$form->setFieldAttribute('upload_folder_type', 'description', JText::sprintf($upload_folder_type_desc, $field_id), 'fieldparams');

		return $result;
	}
}