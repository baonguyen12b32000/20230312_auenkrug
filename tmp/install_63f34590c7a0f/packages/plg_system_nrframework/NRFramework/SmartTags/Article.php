<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\SmartTags;

use NRFramework\Conditions\Conditions\Component\ContentBase;
use Joomla\CMS\Factory;
use NRFramework\Cache;

defined('_JEXEC') or die('Restricted access');

class Article extends SmartTag
{
    /**
     * Fetch a property from the User object
     *
     * @param   string  $key   The name of the property to return
     *
     * @return  mixed   Null if property is not found, mixed if property is found
     */
    public function fetchValue($key)
    {
        $contentAssignment = new ContentBase();
        
        if (!$contentAssignment->isSinglePage())
        {
            return;
        }

        // Case custom fields: {article.field.age}
        if (strpos($key, 'field.') !== false)
        {
            $fieldname = str_replace('field.', '', $key);
            
            if ($fields = $this->fetchCustomFields())
            {
                if (array_key_exists($fieldname, $fields))
                {
                    return $fields[$fieldname]->value;
                }
            }

            return;
        }

        // Why the heck $isSinglePage below returns false?
        // $articleAssignment = new \NRFramework\Conditions\Component\ContentArticle();
        // $isSinglePage = $articleAssignment->pass();

        $article = $contentAssignment->getItem();

        if (!isset($article->{$key}) || is_object($article->{$key}))
        {
            return;
        }

        return $article->{$key};
    }

    /**
     * Return an assosiative array with user custoom fields
     *
     * @return mixed    Array on success, null on failure
     */
    private function fetchCustomFields()
    {
        $callback = function()
        { 
            $contentAssignment = new ContentBase();
            $article = $contentAssignment->getItem();

            \JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
    
            if (!$fields = \FieldsHelper::getFields('com_content.article', $article, true))
            {
                return;
            }

            $fieldsAssoc = [];

            foreach ($fields as $field)
            {
                $fieldsAssoc[$field->name] = $field;
            }

            return $fieldsAssoc;
        };

        return Cache::memo('fetchCustomFields', $callback);
    }
}