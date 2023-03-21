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

$value = $field->value;

if (is_string($value) && !$value = json_decode($value, true))
{
    return;
}

if (is_string($value) && !$value = json_decode($value, true))
{
    return;
}

$payload = $value + $fieldParams->flatten();
$payload['value'] = !empty($payload['address']['latitude']) && !empty($payload['address']['longitude']) ? $payload['address']['latitude'] . ',' . $payload['address']['longitude'] : null;
$payload['show_map'] = $payload['show_map'] === '0' ? false : $payload['show_map'];
$payload['autocomplete'] = $payload['autocomplete'] === '1';
$payload['showAddressDetails'] = $fieldParams->get('address_details_info', []);

echo \NRFramework\Widgets\Helper::render('MapAddress', $payload);