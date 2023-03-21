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

\JHtml::_('behavior.core');

JFactory::getDocument()->addStyleDeclaration('
    html {
        overflow-y:scroll;
    }
    body {
        background-color:#f1f3f5;
    }
');

JHtml::script('com_rstbox/templates.js', ['relative' => true, 'version' => true]);

?>

<div class="ts-page ts-page-templates">
    <?php echo $this->loadTemplate('toolbar') ?>
    <div class="ts-content">
        <?php if (!$this->license) { ?>
        <div class="alert alert-danger text-center licenseInvalid">
            <?php 
                JText::script('COM_ENGAGEBOX_GALLERY_DOWNLOAD_KEY'); // Needed by script
                echo JText::_('COM_ENGAGEBOX_GALLERY_DOWNLOAD_KEY') 
            ?>
        </div>
        <?php } ?>
        <?php echo $this->loadTemplate('noresults') ?>
        <?php echo $this->loadTemplate('items') ?>
    </div>
    <?php echo $this->loadTemplate('footer') ?>
</div>
