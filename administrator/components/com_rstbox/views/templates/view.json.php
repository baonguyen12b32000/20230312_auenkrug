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
        $input = JFactory::getApplication()->input;
        $task  = $input->get('task');

        if ($task == 'favorites_toggle')
        {
            EngageBox\Templates::addOrRemoveFavorite($input->get('template_id'));
        }

        echo json_encode(EngageBox\Templates::getFavorites());
    }
}