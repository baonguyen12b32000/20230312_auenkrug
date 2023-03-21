<?php

/**
 * @package         EngageBox
 * @version         5.2.2 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

$box = $displayData;
$params = EngageBox\Helper::getParams();

// @deprecated - Remove Legacy script on 01/01/2023
$legacy = $params->get('loadLegacyScript', false);

// Add polyfills for Internet Explorer
if (NRFramework\WebClient::getBrowser()['name'] == 'ie')
{
	JFactory::getDocument()->addScript('https://polyfill.io/v3/polyfill.min.js?features=NodeList.prototype.forEach%2CElement.prototype.closest%2CArray.prototype.forEach%2CArray.prototype.find%2CIntersectionObserver%2CIntersectionObserverEntry');
}

// Load core.js as we need to read the csrf.token and system.paths values.
JHtml::_('behavior.core');

if ($params->get('loadVelocity', true))
{
	JHtml::script('com_rstbox/vendor/velocity.js', ['relative' => true, 'version' => 'auto']);
	JHtml::script('com_rstbox/vendor/velocity.ui.js', ['relative' => true, 'version' => 'auto']);

	if (strpos($box->params->get('animationin'), 'rstbox') !== false || strpos($box->params->get('animationout'), 'rstbox') !== false)
	{
		JHtml::script('com_rstbox/animations.js', ['relative' => true, 'version' => 'auto']);
	}
}

if ($legacy)
{
	JHtml::_('jquery.framework');
}

JHtml::script('com_rstbox/engagebox.js', ['relative' => true, 'version' => 'auto']);

if ($legacy)
{
	JHtml::script('com_rstbox/legacy.js', ['relative' => true, 'version' => 'auto']);
}

if ($params->get('loadCSS', true))
{
	JHtml::stylesheet('com_rstbox/engagebox.css', ['relative' => true, 'version' => 'auto']);
}

// Add Custom CSS
JFactory::getDocument()->addStyleDeclaration($box->params->get('customcss'));

// In case of pageslide mode, load the respective script
if ($box->params->get('mode') == 'pageslide')
{
	JHtml::script('com_rstbox/pageslide_mode.js', ['relative' => true, 'version' => 'auto']);
}

// If we are tracking with Google Analytics, load the respective script
if ($params->get('gaTrack', false))
{
	JHtml::script('com_rstbox/gatracker.js', ['relative' => true, 'version' => 'auto']);
}

$close_button = (int) $box->params->get('closebutton.show', 1);

?>

<div data-id="<?php echo $box->id ?>" 
	class="eb-inst eb-hide <?php echo implode(' ', $box->classes) ?>"
	data-options='<?php echo json_encode($box->settings, JSON_HEX_APOS) ?>'
	data-type='<?php echo $box->params->get('mode') ?>'
	<?php if (!empty($box->styles_container)) { ?>style="<?php echo $box->styles_container; ?>"<?php } ?>
	<?php if ($box->rtl) { ?>dir="rtl"<?php } ?>>

	<?php if ($close_button == 2) { echo $this->sublayout('closebutton', $box); } ?>

	<div class="eb-dialog <?php echo implode(' ', $box->dialog_classes) ?>" style="<?php echo $box->style ?>" role="dialog" tabindex="-1">
		
		<?php if ($close_button == 1) { echo $this->sublayout('closebutton', $box); } ?>
	
		<div class="eb-container">
			<?php if ($box->params->get('showtitle', true)) { ?>
				<div class="eb-header">
					<?php echo $box->name ?>
				</div>
			<?php } ?>
			<div class="eb-content">
				<?php
					echo $box->content;
					echo $params->get("globalFooter");
				?>
			</div>
		</div>
		<?php echo $box->params->get("customcode"); ?>
	</div>	
</div>