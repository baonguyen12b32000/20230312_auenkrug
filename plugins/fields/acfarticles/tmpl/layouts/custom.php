<?php

/**
 * @package         Advanced Custom Fields
 * @version         2.3.0 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2023 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

use Joomla\Component\Content\Site\Helper\RouteHelper;

$customLayout = \JHtml::_('content.prepare', $customLayout);

$st = new \NRFramework\SmartTags();

// Add total articles
$st->add(['total' => count($articles)], 'acf.articles.');

// Set the pattern
$pattern = '/\{acf\.(article\.)?(?!articles\.)(.+?)\}/';

foreach ($articles as $index => $article)
{
	// Add article index
	$st->add(['index' => $index + 1], 'acf.articles.');
	
	// Set the replacement
	$replacement = '{article.$2 --id=' . $article['id'] . '}';

	// Get updated layout
	$tmpCustomLayout = preg_replace($pattern, $replacement, $customLayout);

	$html .= $st->replace($tmpCustomLayout);
}