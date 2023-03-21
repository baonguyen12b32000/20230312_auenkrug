<?php

/**
 * @package         EngageBox
 * @version         5.2.2 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace EngageBox;

use Joomla\Registry\Registry;

defined('_JEXEC') or die('Restricted access');

/**
 * EngageBox Migrator Class helps us fix and prevent backward compatibility issues between release updates.
 */
class Migrator
{
    /**
     * The database class
     *
     * @var object
     */
    private $db;

    /**
     * Indicates the current installed version of the extension
     *
     * @var string
     */
    private $installedVersion;
    
    /**
     * Class constructor
     *
     * @param string $installedVersion  The current extension version
     */
    public function __construct($installedVersion, $dbSource = null, $dbDestination = null)
    {
        $this->db = $dbSource ? $dbSource : \JFactory::getDbo();
        $this->dbDestination = $dbDestination ? $dbDestination : \JFactory::getDbo();
        $this->installedVersion = $installedVersion;
    }
    
    /**
     * Start the migration process
     *
     * @return void
     */
    public function start()
    {
        $this->removeTemplatesData();
       
        if (!$data = $this->getBoxes())
        {
            return;
        }

        foreach ($data as $key => $box)
        {   
            $box->params = new Registry($box->params);

            if (version_compare($this->installedVersion, '4.0.0', '<=')) 
            {
                $this->moveCloseOpenedBoxesOptionToActions($box);
                $this->moveAutoCloseTimerToActions($box);
                $this->fixCustomCSS($box);
                $this->fixCloseButton($box);
            }
            
            if (version_compare($this->installedVersion, '5.0.1', '<=')) 
            {
                // Pass only params
                \NRFramework\Conditions\Migrator::run($box->params);
            }
            
            // Update box using id as the primary key.
            $box->params = json_encode($box->params);

            $this->dbDestination->updateObject('#__rstbox', $box, 'id');
        }
    }

    /**
     * Get all boxes from the database
     *
     * @return array
     */
    private function getBoxes()
    {
        $db = $this->db;
    
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__rstbox');
        
        $db->setQuery($query);
    
        return $db->loadObjectList();
    }

    private function fixCloseButton(&$box)
    {
        if (!$box->params->offsetExists('closebutton.hide'))
        {
            return;
        }

        $close_button_value = $box->params->get('closebutton.hide', false) ? 0 : 1;
        $box->params->set('closebutton.show', $close_button_value);
        $box->params->remove('closebutton.hide');
    }

    private function fixCustomCSS(&$box)
    {
        if (!$css = $box->params->get('customcss'))
        {
            return;
        }

        $replacements = [
            '.rstbox-heading' => '.eb-header', // rstbox-heading no longer exist
            '.rstboxes' => '',
            '.rstbox-'  => '.eb-',
            '.rstbox_'  => '.eb-',
            '#rstbox_' . $box->id  => '.eb-' . $box->id . ' .eb-dialog'
        ];
        
        $css_new = str_ireplace(array_keys($replacements), array_values($replacements), $css);
        
        if ($css == $css_new)
        {
            return;
        }

        $box->params->set('customcss', $css_new);
    }

    private function moveCloseOpenedBoxesOptionToActions(&$box)
    {
        if (!$box->params->get('closeopened', false))
        {
            return;
        }   

        $actions = (array) $box->params->get('actions', []);

        $actions[] = [
            'enabled' => true,
            'when'    => 'open',
            'do'      => 'closeall'
        ];

        $box->params->set('actions', $actions);
        $box->params->remove('closeopened');
    }

    private function moveAutoCloseTimerToActions(&$box)
    {
        if (!$box->params->get('autoclose', false))
        {
            return;
        }

        if ((int) $box->params->get('autoclosevalue', 0) == 0)
        {
            return;
        }

        $actions = (array) $box->params->get('actions', []);

        $actions[] = [
            'enabled' => true,
            'when'    => 'afterOpen',
            'do'      => 'closebox',
            'box'     => $box->id,
            'delay'   => $box->params->get('autoclosevalue', 0) / 1000 // Convert ms to sec.
        ];

        $box->params->set('actions', $actions);
        $box->params->remove('autoclose');
    }

    /**
     * Removes the templates.json & favorites.json file from EngageBox installations <= 5.2.0.
     * 
     * This is to ensure the customers download the latest templates.json file
     * the first time they upgrade to EngageBox 5.2.0 
     * from the remote endpoint as the existing templates wont work with the
     * new Templates Library.
     * 
     * Also favorites.json contains outdated IDs so we remove it to start clean.
     * 
     * @since  5.2.0
     */
    private function removeTemplatesData()
    {
        if (version_compare($this->installedVersion, '5.2.0', '>')) 
        {
            return;
        }

        $this->deleteTemplatesJSONFile();
        $this->deleteTemplatesFavoritesJSONFile();
    }

    /**
     * Removes the templates/templates.json file.
     * 
     * @return  void
     */
    private function deleteTemplatesJSONFile()
    {
        $path = JPATH_ROOT . '/media/com_rstbox/templates/templates.json';
        if (!file_exists($path))
        {
            return;
        }

        unlink($path);
    }

    /**
     * Removes the templates/favorites.json file.
     * 
     * @return  void
     */
    private function deleteTemplatesFavoritesJSONFile()
    {
        $path = JPATH_ROOT . '/media/com_rstbox/templates/favorites.json';
        if (!file_exists($path))
        {
            return;
        }

        unlink($path);
    }
}