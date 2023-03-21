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

if (!$files = $field->value)
{
	return;
}

require_once JPATH_SITE . '/plugins/fields/acfupload/fields/uploadhelper.php';

$files  = is_string($files) ? json_decode($files, true) : (array) $files;
$layout = $fieldParams->get('layout', 'link');
$limit_files = (int) $fieldParams->get('limit_files');
$buffer = [];
$total_files = count($files);

$files = $limit_files === 1 ? [$files] : $files;

foreach ($files as $index => $value)
{
	// Make sure we have a value
	if (!$value)
	{
		continue;
	}

	$file = isset($value['value']) ? $value['value'] : $value;
	$title = isset($value['title']) && !empty($value['title']) ? $value['title'] : '';
	$description = isset($value['description']) && !empty($value['description']) ? html_entity_decode(urldecode($value['description'])) : '';

	$file_url  = ACFUploadHelper::absURL($file);

	switch ($layout)
	{
		// Image
		case 'img':
			$item = '<img src="' .  $file_url . '"/>';
			break;

		// Custom Layout
		case 'custom':
			if (!$subject = $fieldParams->get('custom_layout'))
			{
				return;
			}

			// Add the prefix "acf." to any "{file.*}" Smart Tags found
			$new_format = 'acf.file.';
			$subject = preg_replace('/{file\.([^}]*)}/', '{'.$new_format.'$1}', $subject);

			$file_full_path = JPATH_SITE . '/' . $file;
			$exists = JFile::exists($file_full_path);

            // Always use framework's pathinfo to fight issues with non latin characters.
            $filePathInfo = NRFramework\File::pathinfo($file);

			$st = new \NRFramework\SmartTags();

			$file_tags = [
				'index' => $index + 1,
				'total' => $total_files,
				'name' => $filePathInfo['basename'],
				'basename' => $filePathInfo['basename'],
				'filename' => $filePathInfo['filename'],
				'ext' => $filePathInfo['extension'],
				'extension' => $filePathInfo['extension'],
				'path' => $file,
				'url' => $file_url,
				'size' => $exists ? ACFUploadHelper::humanFilesize(filesize($file_full_path)) : 0,
				'title' => $title,
				'description' => nl2br($description)
			];
			$st->add($file_tags, 'acf.file.');

			$item = $st->replace($subject);
			break;

		// Link
		default:
			$item = '<a href="' . $file_url . '"';

			if ($fieldParams->get('force_download', true))
			{
				$item .= ' download';
			}

			$link_text = $fieldParams->get('link_text', $file);

			$st = new \NRFramework\SmartTags();

            // Always use framework's pathinfo to fight issues with non latin characters.
            $filePathInfo = NRFramework\File::pathinfo($file);
			
			$file_tags = [
				'index' => $index + 1,
				'total' => $total_files,
				'basename' => $filePathInfo['basename'],
				'filename' => $filePathInfo['filename'],
				'extension' => $filePathInfo['extension'],
				'title' => $title,
				'description' => nl2br($description)
			];
			$st->add($file_tags, 'acf.file.');

			$item .= '>' . $st->replace($link_text) . '</a>';
			break;
	}

	$buffer[] = '<span class="acfup-item">' . $item . '</span>';
}

echo implode('', $buffer);
