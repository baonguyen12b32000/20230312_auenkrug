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

defined('_JEXEC') or die('Restricted access');

JFormHelper::loadFieldClass('text');

class JFormFieldACFUpload extends JFormFieldText
{
    /**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
        if ($this->element['limit_files'] != 1)
        {
            JHtml::script('https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js');
        }

        JHtml::script('https://cdn.jsdelivr.net/npm/dropzone@5.9.2/dist/dropzone.min.js');
        JHtml::script('plg_fields_acfupload/acfupload.js', ['relative' => true, 'version' => 'auto']);
        JHtml::stylesheet('plg_fields_acfupload/acfupload.css', ['relative' => true, 'version' => 'auto']);

        // Add language strings used by script
        JText::script('ACF_UPLOAD_FILETOOBIG');
        JText::script('ACF_UPLOAD_INVALID_FILE');
        JText::script('ACF_UPLOAD_FALLBACK_MESSAGE');
        JText::script('ACF_UPLOAD_RESPONSE_ERROR');
        JText::script('ACF_UPLOAD_CANCEL_UPLOAD');
        JText::script('ACF_UPLOAD_CANCEL_UPLOAD_CONFIRMATION');
        JText::script('ACF_UPLOAD_REMOVE_FILE');
        JText::script('ACF_UPLOAD_MAX_FILES_EXCEEDED');
        JText::script('ACF_UPLOAD_FILE_MISSING');
        JText::script('ACF_UPLOAD_REMOVE_FILE_CONFIRM');

        // Render File Upload Field
        $data = [
            'id'                  => $this->id,
            'value'               => $this->prepareValue(),
            'input_name'          => $this->element['limit_files'] == 1 ? $this->name : $this->name . '[INDEX]',
            'required'            => ((string) $this->element['required'] == 'true' ? true : false),
            'field_id'            => $this->element['field_id'],
            'max_file_size'       => $this->element['max_file_size'],
            'limit_files'         => $this->element['limit_files'],
            'multiple'            => $this->element['limit_files'] == 1 ? false : true,
            'upload_types'        => $this->element['upload_types'],
            'disabled'            => $this->disabled,
            'show_download_links' => isset($this->element['show_download_links']) && $this->element['show_download_links'] == 1 ? true : false,
            'base_url'            => JURI::base() // AJAX endpoint works on both site and backend.
        ];

		$layout = new \JLayoutFile('acfuploadlayout', __DIR__);
        return $layout->render($data);
    }

    /**
     * Prepare the value.
     * 
     * @return  void
     */
    private function prepareValue()
    {
        if (empty($this->value))
        {
            return;
        }

        require_once __DIR__ . '/uploadhelper.php';

        $limit_files = (int) $this->element['limit_files'];

        $files = [];

        /**
         * This handles backwards compatibility
         */
        // Handle one file
        if ($limit_files === 1)
        {
            if (is_string($this->value))
            {
                if ($tmp_files = json_decode($this->value, true))
                {
                    $files = [$tmp_files];
                }
                else
                {
                    $files = [['value' => $this->value]];
                }
            }
            else
            {
                $files = $this->value;
            }
        }
        // Handle multiple files
        else
        {
            if (is_string($this->value))
            {
                if ($tmp_files = json_decode($this->value, true))
                {
                    $files = $tmp_files;
                }
                else
                {
                    $files = [['value' => $this->value]];
                }
            }
            else
            {
                $files = json_decode(json_encode($this->value), true);
            }
        }
        
        $return = [];
        
        foreach ($files as $value)
        {
            if (!$value)
            {
                continue;
            }

            $file = isset($value['value']) ? $value['value'] : $value;
            $title = isset($value['title']) ? $value['title'] : '';
            $description = isset($value['description']) ? $value['description'] : '';

            // Always use framework's pathinfo to fight issues with non latin characters.
            $filePathInfo = NRFramework\File::pathinfo($file);

            $file_path = JPath::clean(JPATH_ROOT . '/' . $file);
            $exists    = JFile::exists($file_path);
            $file_size = $exists ? filesize($file_path) : 0;

            $return[] = [
                'title' => $title,
                'description' => $description,
                'name'    => $filePathInfo['basename'],
                'path'    => $file,
                'encoded' => base64_encode($file),
                'url'     => ACFUploadHelper::absURL($file_path),
                'size'    => $file_size,
                'exists'  => $exists
            ];
        }

        return $return;
    }
}
