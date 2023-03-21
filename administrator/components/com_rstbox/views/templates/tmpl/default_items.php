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

?>

<div class="ts-list">
    <div class="ts-item blank_popup" data-title="">
        <span class="ts-item-wrap">
            <a class="parent" href="<?php echo JURI::base() . 'index.php?option=com_rstbox&view=items&task=item.add' ?>">
                Blank Popup
                <span>Start from scratch</span>
            </a>
        </span>
    </div>
    <?php foreach ($this->templates as $group_key => $groups) { 
        $key = strtolower(JFilterInput::getInstance()->clean($group_key, 'WORD'));
    ?>
        <?php foreach ($groups as $template_key => $template) { 
                $is_fav = array_key_exists($template->id, $this->favorites);
                $image = isset($template->image) ? $template->image : '';
                $link_preview = JURI::root() . '?engagebox_preview_template=' . base64_encode($template->alias);
                $link_use = JURI::base() . 'index.php?option=com_rstbox&view=item&layout=edit&template=' . $template->alias;
            ?>
            <div class="ts-item <?php echo trim($template->css_class) ?>" data-id="<?php echo $template->id ?>" data-cat="<?php echo $key ?>" data-title="<?php echo $template->title; ?>">
                <span class="ts-item-wrap">
                    <img src="<?php echo $image; ?>"/>
                    <span class="ts-item-hover">
                        <?php if ($this->license) { ?>
                            <a class="ts-preview parent" href="<?php echo $link_use ?>">
                                <span class="icon-upload"></span>
                                <span class="ts-title"><?php echo $template->title ?></span>
                            </a>
                        <?php } else { ?>
                            <span class="ts-preview parent">
                                <span class="ts-dk"><?php echo JText::_('NR_DOWNLOAD_KEY_MISSING') ?></span>
                            </span>
                        <?php } ?>
                        <span class="ts-buttons">
                            <span>
                                <a class="preview_template" title="Preview template in the front-end" target="_blank" href="<?php echo $link_preview; ?>">
                                    <span class="icon-eye"></span>
                                    Preview
                                </a>
                            </span>
                            <span>
                                <?php if (isset($template->fields->note) && !empty($template->fields->note)) { ?>
                                    <span class="info icon-info" title="<?php echo $template->fields->note ?>"></span>
                                <?php } ?>

                                <a class="favorite_icon <?php echo $is_fav ? 'active' : '' ?>" title="Save template to favorites" href="#">
                                    <span class="up icon-heart-2"></span>
                                    <span class="active icon-heart"></span>
                                </a>
                            </span>
                        </span>
                    </span>
                </span>
            </div>
        <?php } ?>
    <?php } ?>
</div>