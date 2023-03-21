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

if (!$gallery_data = $field->value)
{
	return;
}

require_once JPATH_SITE . '/plugins/fields/acfgallery/fields/helper.php';

if (is_string($gallery_data) && !$gallery_data = json_decode($gallery_data, true))
{
    return;
}

$gallery_items = isset($gallery_data['items']) ? $gallery_data['items'] : [];

$style = $fieldParams->get('style', 'grid');

echo \NRFramework\Widgets\Helper::render('Gallery', [
    'items' => ACFGalleryHelper::prepareItems($gallery_items),
    'ordering' => $fieldParams->get('ordering', 'default'),
    'lightbox' => $fieldParams->get('lightbox', '0') == '1',
    'module' => $fieldParams->get('module', ''),
    'thumb_width' => $fieldParams->get('thumb_width', ''),
    'thumb_height' => $style === 'grid' ? $fieldParams->get('thumb_height', '0') : null,
    'style' => $style,
    'columns' => $fieldParams->get('devices_columns.columns', []),
    'gap' => $fieldParams->get('devices_gap.gap', [])
]);