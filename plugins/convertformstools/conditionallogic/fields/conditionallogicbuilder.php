<?php

/**
 * @package         Convert Forms
 * @version         4.0.0 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

class JFormFieldConditionalLogicBuilder extends JFormFieldHidden
{
    /**
     * Method to get a list of options for a list input.
     *
     * @return      array           An array of JHtml options.
     */
    protected function getInput()
    {
        // Disable form rendering on textarea change
        $this->class .= ' norender';

        JHtml::script('plg_convertformstools_conditionallogic/builder.js', ['relative' => true, 'version' => 'auto']);
        JHtml::stylesheet('plg_convertformstools_conditionallogic/builder.css', ['relative' => true, 'version' => 'auto']);

        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_ENTER_TITLE');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_ENTER_VALUE');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_NO_CONDITIONS');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_ADD_CONDITION');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_NEW_CONDITION');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_DELETE_CONDITION');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_COPY_CONDITION');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_CONDITION_ELSE');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_ACTIONS');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_ACTIONS_ALIAS');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_ADD_RULE_GROUP');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_ADD_RULE');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_RULES');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_RULES_ALIAS');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_DELETE_ACTION');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_DELETE_RULE');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_ADD_ACTION');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_SELECT_ACTION');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_SHOW_FIELD');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_HIDE_FIELD');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_CHANGE_VALUE');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_SELECT_OPTION');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_DESELECT_OPTION');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_SHOW_OPTION');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_HIDE_OPTION');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_SHOW_ALL_OPTIONS');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_HIDE_ALL_OPTIONS');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_SELECT_OPERATOR');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_EQUALS');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_HAS_SELECTED');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_CONTAINS');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_STARTS_WITH');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_ENDS_WITH');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_REGEX');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_NOT_EQUALS');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_NOT_SELECTED');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_NOT_CONTAIN');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_NOT_START_WITH');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_NOT_END_WITH');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_NOT_REGEX');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_LESS_THAN');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_LESS_THAN_EQUAL');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_GREATER_THAN');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_GREATER_THAN_EQUAL');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_IS_CHECKED');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_NOT_CHECKED');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_SELECT_FIELD');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_EMPTY');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_NOT_EMPTY');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_TOTAL_CHECKED_EQUAL');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_TOTAL_CHECKED_NOT_EQUALS');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_TOTAL_CHECKED_LESS');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_TOTAL_CHECKED_LESS_THAN_OR');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_TOTAL_CHECKED_GREATER');
        JText::script('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_TOTAL_CHECKED_GREATER_THAN_OR');
        JText::script('NR_OR');

        $rulesOnly = isset($this->element['rulesOnly']) ? (bool) $this->element['rulesOnly'] : false;

        if ($rulesOnly)
        {
            return '
                <h4>' . JText::_('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_PROCESS_THIS') . '</h4>
                <div class="clb">
                    <div id="clr-' . $this->id . '"></div>
                    ' . parent::getInput() . '
                </div>
            ';
        }

        return '<button type="submit" data-bs-target="#cfcl"  data-target="#cfcl" data-bs-toggle="modal" data-toggle="modal" class="cf-btn clstart">' . JText::_('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_SETUP') . '</button>' . parent::getInput();
    }
}