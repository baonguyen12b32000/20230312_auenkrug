<?php

/**
 * @package         Advanced Custom Fields
 * @version         2.3.0 Pro
 *
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die;

if (!$videoID = $field->value)
{
	return;
}

if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $field->value, $match))
{
	$videoID = $match[1];
}

// Setup Variables
$id                = 'acf_yt_' . $item->id . '_' . $field->id;
$size              = $fieldParams->get('size', 'fixed');
$width             = $fieldParams->get('width', '480');
$height            = $fieldParams->get('height', '270');
$width_height_atts = ($size == 'fixed') ? 'width="' . $width . '" height="' . $height . '"' : '';
$autoplay_att 	   = '';
$query             = $videoID;


$queryParams = [
	'controls' 		 => $fieldParams->get('controls', '1'),
	'autoplay' 		 => $fieldParams->get('autoplay', '0'),
	'cc_load_policy' => $fieldParams->get('cc_load_policy', '0'),
	'color' 		 => $fieldParams->get('color', 'red'),
	'disablekb' 	 => $fieldParams->get('disablekb', '0'),
	'start' 		 => $fieldParams->get('start', '0'),
	'end' 			 => $fieldParams->get('end', '0'),
	'fs' 			 => $fieldParams->get('fs', '1'),
	'hl' 			 => JFactory::getApplication()->getLanguage()->getTag(),
	'iv_load_policy' => $fieldParams->get('iv_load_policy', '1') ? '1' : '3',
	'loop' 			 => $fieldParams->get('loop', '0'),

	// When the color is set to White, the logo is always shown
	'modestbranding' => $fieldParams->get('modestbranding', '0'),
	'rel' 			 => $fieldParams->get('rel', '1'),
];

// Override the global start time with the URL's one
$url_params = parse_url($field->value, PHP_URL_QUERY);
parse_str($url_params, $url_params);
/**
 * ?t was used previously by YouTube when getting the embed code.
 * ?start appears to be used as of right now.
 * 
 * We support both at the moment.
 */
$override_start_time = isset($url_params['t']) ? (int) $url_params['t'] : (isset($url_params['start']) ? (int) $url_params['start'] : false);
if ($override_start_time)
{
	$queryParams['start'] = $override_start_time;
}

$query = $videoID . '?' . http_build_query($queryParams);
$autoplay_att = $queryParams['autoplay'] ? ' allow="autoplay; encrypted-media"' : '';


$youTubeBaseURL = 'https://www.youtube' . ($fieldParams->get('privacy_mode') ? '-nocookie' : '') . '.com/embed/';

// Output
$buffer = '
	<iframe
		id="' . $id . '"
		class="acf_yt"
		' . $width_height_atts . '
		src="' . $youTubeBaseURL . $query . '"
		frameborder="0"
		' . $autoplay_att . '
		allowfullscreen>
	</iframe>
';

if ($size == 'responsive')
{
    JHtml::stylesheet('plg_system_acf/responsive_embed.css', ['relative' => true, 'version' => 'auto']);
	$buffer = '<div class="acf-responsive-embed">' . $buffer . '</div>';
}

echo $buffer;