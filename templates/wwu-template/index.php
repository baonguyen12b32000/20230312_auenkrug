<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.wwu
 *
 * @copyright   (C) WWU Medien GmbH 2023
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * This is a heavily stripped down/modified version of the default Cassiopeia template, designed to build new templates off of.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/** @var Joomla\CMS\Document\HtmlDocument $this */

$app = Factory::getApplication();
$wa  = $this->getWebAssetManager();

// Add Favicon from images folder
// use this for favicon.ICO 
//$this->addHeadLink(HTMLHelper::_('image', 'favicon.ico', '', [], true, 1), 'icon', 'rel', ['type' => 'image/x-icon']);
// use this for favicon.png
//$this->addHeadLink(HTMLHelper::_('image', 'favicon.png', '', [], true, 1), 'icon', 'rel', ['type' => 'image/png']);
$this->addHeadLink(HTMLHelper::_('image', 'favicon.svg', '', [], true, 1), 'icon', 'rel', ['type' => 'image/svg+xml']);

// Detecting Active Variables
$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');
$itemid   = $app->input->getCmd('Itemid', '');
$sitename = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');
$menu     = $app->getMenu()->getActive();
$pageclass = $menu !== null ? $menu->getParams()->get('pageclass_sfx', '') : '';

//Get params from template styling
//If you want to add your own parameters you may do so in templateDetails.xml
$testparam =  $this->params->get('testparam');

//uncomment to see how this works on site... it just shows 1 or 0 depending on option selected in style config.
//You can use this style to get/set any param according to instructions at
//echo('the value of testparam is: '.$testparam);

// Get this template's path
$templatePath = 'templates/' . $this->template;

// Load our frameworks
JHtml::_('bootstrap.framework');
JHtml::_('jquery.framework');

//Register our web assets (Css/JS)
$wa->useStyle('template.wwu-template.mainstyles');
$wa->useScript('template.wwu-template.scripts');
$wa->useScript('fontawesome.wwu-template.scripts');
$wa->useScript('fa-style.wwu-template.scripts');

//Set viewport
$this->setMetaData('viewport', 'width=device-width, initial-scale=1');


if ($this->countModules('left'))
{
	$col = 'col-xl-7 col_component';
}
else
{
	$col = 'col-xl-12 col_component';
}
// wwu end

?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<jdoc:include type="metas" />
		<jdoc:include type="styles" />
		<jdoc:include type="scripts" />
		
	</head>

<body class="site <?php echo $pageclass; ?>">
	<header class="header ">
		<div class="container-fluid top-container">
			<div class="container-xxl">
				<div class="row">
				
					<?php if ($this->countModules('logo')) : ?>
						<div class="logo col-xl-5 col-md-4 col-12">
							<jdoc:include type="modules" name="logo" style="html5" />
						</div>
					<?php endif; ?>
					<?php if ($this->countModules('navi')) : ?>
						<div class="navi col-xl-7 col-md-8 col-12">
							<jdoc:include type="modules" name="navi" style="html5" />
						</div>
						<div class="mobile_container">
							<div class="mobile_nav">
								 <i class="fa fa-bars"></i>
							</div>
							<div class="mobile_menu">
								<jdoc:include type="modules" name="navi" style="html5" />
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<?php if ($this->countModules('slide-image')) : ?>
			<div class="slide-image">
				<jdoc:include type="modules" name="slide-image" style="html5" />
			</div>
		<?php endif; ?>
	</header>

	<?php if ($this->countModules('content-top', true)) : ?>
		<div class="container-fluid">
			<div id="content-top" class="row">
				<jdoc:include type="modules" name="content-top" style="html5" />
			</div>
		</div>
	<?php endif; ?>
	
		<jdoc:include type="message" />

	<div id="main" class="container-xl"><!-- you also can "-fluid" or ohter -->
		<div class="row">
			<?php if ($this->countModules('left', true)) : ?>
				<div class="col-xl-5 left_container">
					<div id="left">
						<jdoc:include type="modules" name="left" style="html5" />
					</div>
				</div>
			<?php endif; ?>

				<div class="<?php echo $col; ?>">
					<?php if ($this->countModules('left')): ?>
					<div class="line_box"><div class="line_dot"></div></div>
					<?php endif; ?>
					<jdoc:include type="component" />
				</div>
		</div>
	</div>


	<?php if ($this->countModules('content-bottom', true)) : ?>
			<div class="main container-fluid">
				<div class="content-bottom">
					<jdoc:include type="modules" name="content-bottom" style="html5" />
				</div>
			</div>
	<?php endif; ?>
	

	<?php if ($this->countModules('footer', true)) : ?>
		<footer class="footer ">
			<div class="main container-xxl">
			<jdoc:include type="modules" name="footer" style="html5" />
			</div>
		</footer>
	<?php endif; ?>

	<?php if ($this->countModules('fix', true)) : ?>
		<div class="fix">
			<jdoc:include type="modules" name="fix" style="html5" />
		</div>
	<?php endif; ?>


	<jdoc:include type="modules" name="debug" style="none" />
  
</body>
</html>
