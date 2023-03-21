<?php

/**
 * @package         EngageBox
 * @version         5.1.4 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace EngageBox;

defined('_JEXEC') or die('Restricted access');

use Joomla\Registry\Registry;

class Templates
{
    public static function getPath()
    {
        return JPATH_ROOT . '/media/com_rstbox/templates/';
    }

    public static function addOrRemoveFavorite($template_id)
    {
        $favorites = self::getFavorites();

        if (array_key_exists($template_id, $favorites))
        {  
            self::removeFromFavorites($template_id);
            return;
        }

        self::addToFavorites($template_id);
    }

    public static function addToFavorites($template_id)
    {
        $favorites = self::getFavorites();

        if (array_key_exists($template_id, $favorites))
        {  
            return;
        }

        $favorites[$template_id] = true;

        self::saveFavorites($favorites);
    }

    public static function saveFavorites($content)
    {
        $file = self::getPath() . 'favorites.json';
        return \JFile::write($file, json_encode($content));
    }

    public static function removeFromFavorites($template_id)
    {
        $favorites = self::getFavorites();
        unset($favorites[$template_id]);
        self::saveFavorites($favorites);
    }

    public static function getFavorites()
    {
        $file = self::getPath() . 'favorites.json';

        if (!\JFile::exists($file))
        {
            return [];
        }

        return (array) json_decode(file_get_contents($file), true);
    }

    public static function getList()
    {
        $file = self::getPath() . 'templates.json';

        if (!\JFile::exists($file))
        {
            return;
        }

        return json_decode(file_get_contents($file));
    }

    public static function find($template_alias)
    {
        if (!$templates = self::getList())
        {
            return;
        }

        foreach ($templates as $group)
        {
            foreach ($group as $template)
            {
                if ($template->alias == $template_alias)
                {
                    $template->template->params = json_decode($template->template->params, true);
                    return $template;
                }
            }
        }
    }

    public static function render($template_alias)
    {
        $template = self::find($template_alias);

        $box = $template->template;

        // Set the popup to appear side
        $box->params['display_conditions_type'] = '';

        // Remove local conditions like Limit Impressions
        foreach ($box->params as $key => $value)
        {
            if (strpos($key, 'assign') !== false)
            {   
                unset($box->params[$key]);
            }
        }

        $box->params = new Registry($box->params);

        // Override the trigger point and display the template on page load
        if (empty($template->fields->dontoverridetrigger))
        {
            $box->triggermethod = 'pageload';
        }

        // Set id to 0 for making front-end script happy
        $box->id = 0;

        // Enable Test Mode to prevent cookies
        $box->testmode = true;

        // Disable Statistics to prevent impressions from being tracked into the database
        $box->stats = 0;

        return Box::render($box);
    }
}