<?php

/**
 * @package         Advanced Custom Fields
 * @version         2.3.0 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2019 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

if (!$map_value = $field->value)
{
	return;
}

// Setup Variables
$coords = $map_value;

if ((is_string($map_value) && $map_value = json_decode($map_value, true)) || is_array($map_value))
{
	$coords = $map_value['coordinates'];
	
	$marker_tooltip_label = isset($map_value['tooltip']) ? $map_value['tooltip'] : '';
	
}
$coords = explode(',', $coords);

if (!isset($coords[1]))
{
	return;
}

\JHtml::_('behavior.core');

$width = $fieldParams->get('width', '400px');
$height = $fieldParams->get('height', '350px');
$zoom = $fieldParams->get('zoom', 4);
$extra_atts[] = 'data-marker-image="' . JURI::root() . 'media/plg_fields_acfosm/img/marker.png"';


$marker_image = JURI::root() . $fieldParams->get('marker_image', 'media/plg_fields_acfosm/img/marker.png');
$scale = $fieldParams->get('scale', '0');
$extra_atts[0] = 'data-marker-image="' . $marker_image . '"';
$extra_atts[] = 'data-scale="' . $scale . '"';
JHtml::stylesheet('plg_fields_acfosm/acf_osm_map.css', ['relative' => true, 'version' => 'auto']);


// Add Media Files
JHtml::stylesheet('https://www.tassos.gr/media/products/advanced-custom-fields/ol.css');
JHtml::script('https://www.tassos.gr/media/products/advanced-custom-fields/ol.js');
JHtml::script('plg_fields_acfosm/acf_osm_map.js', ['relative' => true, 'version' => 'auto']);
JHtml::script('plg_fields_acfosm/acf_osm_map_loader.js', ['relative' => true, 'version' => 'auto']);

$buffer = '<div class="osm nr-address-component"><div class="osm_map_item" data-zoom="' . $zoom . '" data-lat="' . trim($coords[0]) . '" data-long="' . trim($coords[1]) . '" ' . implode(' ', $extra_atts) . ' style="width:' . $width . ';height:' . $height . ';max-width:100%;"></div>';


$tooltipEnabled = (bool) $fieldParams->get('show_tooltip', '0');
if ($tooltipEnabled && !empty($marker_tooltip_label))
{
	$text = !empty($marker_tooltip_label) ? '<div class="tooltip-body">' . $marker_tooltip_label . '</div>' : '';
	$buffer .= '<div class="marker-tooltip" style="display:none;">' . $text . '<div class="arrow"></div></div>';
}


$buffer .= '</div>';

echo $buffer;