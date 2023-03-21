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

<div class="ts-toolbar">
    <div class="tb-left">
        <div>
            <div class="cf-select">
                <select name="cat" id="select_category" data-search="true">
                    <option value="">All</option>
                    <?php foreach ($this->templates as $group_key => $groups) { 
                        $value = strtolower(JFilterInput::getInstance()->clean($group_key, 'WORD'));    
                    ?>
                        <option value="<?php echo $value ?>"><?php echo $group_key ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="my-favorites">
            <a href="#" class="favorite_icon search_favorite">
                <span class="up icon-heart-2"></span>
                <span class="active icon-heart"></span>
                <?php echo JText::_('COM_RSTBOX_MY_FAVORITES') ?>
            </a>
        </div>
    </div>
    <div class="tb-right">
        <div class="ts-search">
            <input type="search" id="search_template" data-search="true" placeholder="Search" name="ts-search">
            <span class="icon-search"></span>
        </div>
    </div>
</div>