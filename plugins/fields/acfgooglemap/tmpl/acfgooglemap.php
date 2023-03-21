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

if (!$coords = $field->value)
{
	return;
}

// Get Plugin Params
$plugin = JPluginHelper::getPlugin('fields', 'acfgooglemap');
$params = new JRegistry($plugin->params);

// Setup Variables
$mapID  = 'acf_map_' . $item->id . '_' . $field->id;
$coords = explode(",", $coords);

if (!isset($coords[1]))
{
	return;
}

$width  = $fieldParams->get('width', '400px');
$height = $fieldParams->get('height', '350px');
$zoom   = $fieldParams->get('zoom', '16');

// Add Media Files
JFactory::getDocument()->addScript('//maps.googleapis.com/maps/api/js?key=' . $params->get("key"));
JHtml::script('plg_fields_acfgooglemap/gmaps.js', ['relative' => true, 'version' => 'auto']);

// Output
$buffer = '
	<style>
		#' . $mapID . ' {
			width: ' . $width . ';
			height: ' . $height . ';
		}
	</style>
	
	<div id="' . $mapID . '" class="acf-google-map-element" data-lat="' . $coords[0] . '" data-long="' . $coords[1] . '" data-zoom="' . $zoom . '"></div>

	<script>
	document.addEventListener("DOMContentLoaded", function() {
		let maps = document.querySelectorAll(".acf-google-map-element:not(.done)");

		let observer = new IntersectionObserver((entries, observer) => {
			entries.forEach(map_item => {
				if (map_item.isIntersecting) {
					let map_elem = map_item.target;

					let instance = new GMaps({
						div: map_elem,
						lat: map_elem.dataset.lat,
						lng: map_elem.dataset.long,
						zoom: parseInt(map_elem.dataset.zoom)
					});	
			
					instance.addMarker({
						lat: map_elem.dataset.lat,
						lng: map_elem.dataset.long
					});

					map_elem.classList.add("done");
					
					observer.unobserve(map_elem);
				}
			});
		}, {rootMargin: "0px 0px 0px 0px"});

		maps.forEach(function(map) {
			observer.observe(map);
		});
	});
	</script>
';

echo $buffer;
