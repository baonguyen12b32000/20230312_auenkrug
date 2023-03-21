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

use NRFramework\HTML;
use Joomla\CMS\Form\Form;

/**
 *  Advanced Custom Fields System Plugin
 */
class PlgSystemACF extends JPlugin
{
    /**
     *  Auto load plugin's language file
     *
     *  @var  boolean
     */
    protected $autoloadLanguage = true;
    
    /**
     *  Application Object
     *
     *  @var  object
     */
    protected $app;

    /**
     *  The loaded indicator of helper
     *
     *  @var  boolean
     */
    private $init;
    
    
    /**
     *  onCustomFieldsBeforePrepareField Event
     */
    public function onCustomFieldsBeforePrepareField($context, $item, &$field)
    {
        // Validate supported component/views
        if (!in_array($context, [
            'com_content.article', 
            'com_dpcalendar.event'
        ]))
        {
            return;
        }

        // Get Helper
        if (!$this->getHelper())
        {
            return;
        }

        // Only if assignments option is enabled in the plugin settings
        if (!$this->params->get('assignments', true))
        {
            return;
        }

        ACFHelper::checkConditions($field);
    }

    /**
     *  Append publishing assignments XML to the
     *
     *  @param   Form   $form  The form to be altered.
     *  @param   mixed  $data  The associated data for the form.
     *
     *  @return  boolean
     */
	public function onContentPrepareForm(Form $form, $data)
    {
        // Run only on backend
        if (!$this->app->isClient('administrator') || !$form instanceof Form)
        {
            return;
        }

        if (!in_array($form->getName(), [
            'com_fields.field.com_users.user',
            'com_fields.field.com_content.article',
            'com_fields.field.com_contact.contact',
            'com_fields.field.com_dpcalendar.event'
        ]))
        {
            return;
        }

        // Load Publishing Rules tab if assignments option is enabled in the plugin settings
        if ($this->params->get('assignments', true))
        {
            $form->loadFile(__DIR__ . '/form/conditions.xml', false);
        }

        // Always load our stylesheet even if it's a non-ACF field. The Publishing Rules tab shows up on all fields.
        JHtml::stylesheet('plg_system_acf/acf-backend.css', ['relative' => true, 'version' => 'auto']);

        if (defined('nrJ4'))
        {
            JHtml::stylesheet('plg_system_nrframework/joomla4.css', ['relative' => true, 'version' => 'auto']);
            HTML::fixFieldTooltips();
        } else 
        {
            JHtml::stylesheet('plg_system_acf/joomla3.css', ['relative' => true, 'version' => 'auto']);
        }

        return true;
    }
    

    /**
     *  Loads the helper classes of plugin
     *
     *  @return  bool
     */
    private function getHelper()
    {
        // Return if is helper is already loaded
        if ($this->init)
        {
            return true;
        }

        // Return if we are not in frontend
        if (!$this->app->isClient('site'))
        {
            return false;
        }

        // Load Novarain Framework
        if (!@include_once(JPATH_PLUGINS . '/system/nrframework/autoload.php'))
        {
            return;
        }

        // Load Plugin Helper
        JLoader::register('ACFHelper', __DIR__ . '/helper/helper.php');

        return ($this->init = true);
    }

    /**
     * Let each condition check the value before it's savced into the database
     *
     * @param   string  $context
     * @param   object  $article
     * @param   bool    $isNew
     * 
     * @return  void
     */
    public function onContentBeforeSave($context, $article, $isNew)
    {
        if (!in_array($context, ['com_fields.field']))
        {
            return;
        }
        
        if (!isset($article->params))
        {
            return;
        }

        $params = json_decode($article->params, true);
        if (!isset($params['rules']))
        {
            return;
        }       
        
        NRFramework\Conditions\ConditionsHelper::getInstance()->onBeforeSave($params['rules']);

        $article->params = json_encode($params);
    }
}
