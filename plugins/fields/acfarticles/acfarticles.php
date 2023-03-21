<?php

/**
 * @package         Advanced Custom Fields
 * @version         2.3.0 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2023 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

JLoader::register('ACF_Field', JPATH_PLUGINS . '/system/acf/helper/plugin.php');

if (!class_exists('ACF_Field'))
{
	JFactory::getApplication()->enqueueMessage('Advanced Custom Fields System Plugin is missing', 'error');
	return;
}

use Joomla\Registry\Registry;
use NRFramework\Cache;

class PlgFieldsACFArticles extends ACF_Field
{
	
	public function onContentBeforeSave($context, $item, $isNew, $data = [])
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

		$error = false;

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
				
				foreach ($subform_value as $key => $value)
				{
					foreach ($value as $_key => $_value)
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
							$subform_fields[$field_id] = $subform_field;
						}

						$check_value = isset($fieldsData[$field->name][$key][$_key]) ? $fieldsData[$field->name][$key][$_key] : false;

						if (!$check_value)
						{
							break;
						}
						
						$fieldParams = new Registry($subform_field->fieldparams);

						if ($min_articles = (int) $fieldParams->get('min_articles', 0))
						{
							if (count($check_value) < $min_articles)
							{
								JFactory::getApplication()->enqueueMessage(sprintf(JText::_('ACF_ARTICLES_MIN_ITEMS_REQUIRED'), $subform_field->title, $min_articles), 'error');
								$error = true;
								break;
							}
						}
		
						if ($max_articles = (int) $fieldParams->get('max_articles', 0))
						{
							if (count($check_value) > $max_articles)
							{
								JFactory::getApplication()->enqueueMessage(sprintf(JText::_('ACF_ARTICLES_MAX_ITEMS_REQUIRED'), $subform_field->title, $max_articles), 'error');
								$error = true;
								break;
							}
						}
					}
				}
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

				if ($min_articles = (int) $field->fieldparams->get('min_articles', 0))
				{
					if (count($value) < $min_articles)
					{
						JFactory::getApplication()->enqueueMessage(sprintf(JText::_('ACF_ARTICLES_MIN_ITEMS_REQUIRED'), $field->title, $min_articles), 'error');
						return false;
					}
				}

				if ($max_articles = (int) $field->fieldparams->get('max_articles', 0))
				{
					if (count($value) > $max_articles)
					{
						JFactory::getApplication()->enqueueMessage(sprintf(JText::_('ACF_ARTICLES_MAX_ITEMS_REQUIRED'), $field->title, $max_articles), 'error');
						return false;
					}
				}
			}
		}

		return !$error;
	}
	
	
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

		$fieldNode->setAttribute('multiple', true);

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

		$value = array_filter((array) $field->value);
		
		if (!$value)
		{
			return;
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.*')
			->from($db->quoteName('#__content', 'a'))
			->where($db->quoteName('a.id') . ' IN (' . implode(',', $value) . ')')
			->where($db->quoteName('a.state') . ' = 1');

		$db->setQuery($query);

		if (!$articles = $db->loadAssocList())
		{
			return;
		}

		
		// Get and set the articles values
		foreach ($articles as &$article)
		{
			$article['custom_fields'] = $this->fetchCustomFields($article);
		}
		

		$field->value = $articles;

		return parent::onCustomFieldsPrepareField($context, $item, $field);
	}

	
    /**
     * Returns an article's custom fields.
     *
     * @return  array
     */
    private function fetchCustomFields($article = null)
    {
        if (!$article)
        {
            return [];
        }

        $callback = function() use ($article)
        {
			\JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');

            if (!$fields = \FieldsHelper::getFields('com_content.article', $article, true))
            {
                return [];
            }

            $fieldsAssoc = [];

            foreach ($fields as $field)
            {
                $fieldsAssoc[$field->name] = $field->value;
            }

            return $fieldsAssoc;
        };

        return Cache::memo('ACFArticlesFetchCustomFields' . $article['id'], $callback);
    }
	
}