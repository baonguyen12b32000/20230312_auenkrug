<?php

/**
 * @package         EngageBox
 * @version         5.1.4 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2019 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

?>

<div class="ts-no-results">
    <span class="icon-smiley-sad-2"></span>
    <h2><?php echo JText::_('COM_RSTBOX_LIBRARY_NO_RESULTS') ?></h2>
    <p><?php echo JText::_('COM_RSTBOX_LIBRARY_NO_RESULTS_DESC') ?></p>
    <p><a class="parent" href="<?php echo JURI::base() ?>index.php?option=com_rstbox&view=items&task=item.add"><?php echo JText::_('COM_RSTBOX_CREATE_FROM_SCRATCH') ?></a></p>
</div>