<?php

/**
 * @package         EngageBox
 * @version         5.1.4 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.view');
 
/**
 * Templates View
 */
class RstboxViewTemplates extends JViewLegacy
{
    /**
     * Items view display method
     * 
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     * 
     * @return  mixed  A string if successful, otherwise a JError object.
     */
    public function display($tpl = null) 
    {
        $this->config    = JComponentHelper::getParams('com_convertforms');
        $this->templates = EngageBox\Templates::getList();
        $this->favorites = EngageBox\Templates::getFavorites();
        $this->license   = EngageBox\Helper::licenseIsValid();

        // Check for errors.
        if (!is_null($this->get('Errors')) && count($errors = $this->get('Errors')))
        {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return false;
        }

        // Set the toolbar
        $this->addToolBar();

        JFactory::getDocument()->addStyleDeclaration('
            .subhead-collapse {
                display:none;
            }
        ');

        JFactory::getDocument()->addScriptDeclaration('
            document.addEventListener("DOMContentLoaded", function() {
                var els = document.querySelectorAll(".ts-page a.parent");
                els.forEach(function(el) {
                    el.addEventListener("click", function(e) {
                        e.preventDefault();
                        window.parent.location = el.getAttribute("href");
                        window.parent.jQuery("#ebSelectTemplate").modal("hide");
                    });
                });
            });
        ');

        // Display the template
        parent::display($tpl);
    }

    /**
     *  Add Toolbar to layout
     */
    protected function addToolBar() 
    {
        JToolBarHelper::title(JText::_('COM_RSTBOX') . ": " . JText::_('COM_RSTBOX_TEMPLATES'));
    }
}