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

class JFormFieldACFGallery extends JFormFieldText
{
    /**
	 * Generates the Gallery Field
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
        $data = [
            'value' => $this->prepareValue(),
            'required' => (string) $this->element['required'] == 'true' ? true : false,
            'name' => (int) $this->element['limit_files'] == 1 ? $this->name . '[items][0]' : $this->name . '[items][ITEM_ID]',
            'limit_files' => (string) $this->element['limit_files'],
            'max_file_size' => (string) $this->element['max_file_size'],
            'allowed_file_types' => '.jpg, .jpeg, .png, .gif',
            'style' => (string) $this->element['style'],
            'original_image_resize' => (string) $this->element['original_image_resize'] === '1',
            'original_image_resize_quality' => (string) $this->element['original_image_resize_quality'],
            'original_image_resize_width' => (string) $this->element['original_image_resize_width'],
            'thumb_width' => (string) $this->element['thumb_width'],
            'thumb_height' => (string) $this->element['thumb_height'],
            'thumb_resize_method' => (string) $this->element['resize_method'],
            'thumb_resize_quality' => (string) $this->element['resize_quality'],
            'css_class' => ' ordering-' . (string) $this->element['ordering'],
            'disabled' => $this->disabled,
            'field_id' => (int) $this->element['field_id'],
            'id' => $this->id
        ];

        JHtml::script('plg_fields_acfgallery/acfgallery.js', ['relative' => true, 'version' => 'auto']);
        
        return \NRFramework\Widgets\Helper::render('GalleryManager', $data);
    }

    /**
     * The list of uploaded Gallery Items.
     * 
     * @return  mixed
     */
    private function prepareValue()
    {
        if (empty($this->value))
        {
            return;
        }

        $this->value = is_string($this->value) ? json_decode($this->value, true) : (array) $this->value;

        if (!isset($this->value['items']))
        {
            return;
        }

        $value = [];

        foreach ($this->value['items'] as $key => $file)
        {
            $file = new \JRegistry($file);

            $value[] = [
                'thumbnail_url' => JURI::root() . $file->get('thumbnail'),
                'filename' => $file->get('image'),
                'exists' => JFile::exists(JPath::clean(implode(DIRECTORY_SEPARATOR, [JPATH_ROOT, $file->get('thumbnail')]))),
                'caption' => $file->get('caption', ''),
                'thumbnail' => $file->get('thumbnail', ''),
                'is_media_uploader_file' => ($file->get('media_upload_source', 'false') == 'true')
            ];
        }

        return $value;
    }
}
