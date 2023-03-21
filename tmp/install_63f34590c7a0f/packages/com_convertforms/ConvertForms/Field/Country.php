<?php

/**
 * @package         Convert Forms
 * @version         3.2.12 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace ConvertForms\Field;

use NRFramework\Countries;

defined('_JEXEC') or die('Restricted access');

class Country extends \ConvertForms\FieldChoice
{
	protected $inheritInputLayout = 'dropdown';

	/**
	 *  Set the field choices
	 *
	 *  @return  array  The field choices array
	 */
    protected function getChoices()
    {	
    	// Get list of all countries
    	$countries = Countries::getCountriesList();

		asort($countries);

		$choices = array();

		// Exclude countries
		if (!empty($this->field->exclude_countries))
		{
			$countries_ex = explode(',', $this->field->exclude_countries);

			if (is_array($countries_ex))
			{
				foreach ($countries_ex as $country)
				{
					$country = trim($country);

					// Search by country name first
					if ($key = array_search(strtolower($country), array_map('strtolower', $countries)))
					{
						unset($countries[$key]);
						continue;
					}

					unset($countries[strtoupper($country)]);
				}
			}
		}

		foreach ($countries as $countryCode => $countryName)
		{
			if ($selected = in_array(strtolower($this->field->value), [strtolower($countryCode), strtolower($countryName)]))
			{
				// Make sure the default value is set to a country code
				$this->field->value = $countryCode;
			}

			$choices[] = [
				'value'    => $countryCode,
				'label'    => $countryName,
				'selected' => $selected
			];
		}
		
		// Detect visitor's country
		if ($this->field->detectcountry && $detectedCountryCode = $this->getVisitorCountryCode())
		{
			$this->field->value = $detectedCountryCode;
		}

		// If we have a placeholder available, add it to dropdown choices.
        if (isset($this->field->placeholder) && !empty($this->field->placeholder))
        {
            array_unshift($choices, array(
                'label'    => trim($this->field->placeholder),
                'value'    => '',
                'selected' => true,
                'disabled' => true
            ));
        }

		return $choices;
    }

    /**
     *  Detect visitor's country
     *
     *  @return  string   The visitor's country code (GR)
     */
    private function getVisitorCountryCode()
    {
    	$path = JPATH_PLUGINS . '/system/tgeoip/';

    	if (!\JFolder::exists($path))
    	{
    		return;
    	}

    	if (!class_exists('TGeoIP'))
    	{
        	@include_once $path . 'vendor/autoload.php';
        	@include_once $path . 'helper/tgeoip.php';
		}
		
		$geo = new \TGeoIP();
		
		return $geo->getCountryCode();
    }

	/**
	 * Always display the Country name.
	 *
	 * @param  mixed $value		The country code (GR, US e.t.c)
	 *
	 * @return string	The name of the country
	 */
	public function prepareValue($value)	
	{
		if ($countryName = Countries::toCountryName($value))
		{
			return $countryName;
		}

		// Fallback as Countries::toCountryName() returns null if nothing found.
		return $value;
	}
}

?>