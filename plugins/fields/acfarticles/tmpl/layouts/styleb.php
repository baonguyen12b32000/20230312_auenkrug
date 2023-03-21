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

JHtml::stylesheet('plg_fields_acfarticles/style.css', ['relative' => true, 'version' => 'auto']);

use Joomla\Component\Content\Site\Helper\RouteHelper;

foreach ($articles as $article)
{
	$image = '';
	if (isset($article['images']) && $image = json_decode($article['images']))
	{
		$image = $image->image_intro ?: $image->image_fulltext;
	}

	$html .= '<div class="acfarticle-item">';
	
	if ($image)
	{
		$html .= '<img src="' . $image . '" class="acfarticle-item--image" alt="' . $article['title'] . '" loading="lazy" />';
	}

	$html .= '<div class="acfarticle-item--content">';
	
	$html .= '<a href="' . JRoute::_(RouteHelper::getArticleRoute($article['id'], $article['catid'], $article['language'])) . '" class="acfarticle-item--content--title">' . $article['title'] . '</a>';

	// Get the article excerpt
	if ($excerpt = substr(strip_tags($article['introtext']), 0, 200))
	{
		// Add the excerpt
		$html .= '<div class="acfarticle-item--content--excerpt">' . $excerpt . '...</div>';
	}
	
	$html .= '</div></div>';
}