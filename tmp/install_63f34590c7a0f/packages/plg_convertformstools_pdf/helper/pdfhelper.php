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

defined('_JEXEC') or die('Restricted access');

use NRFramework\URLHelper;

class PDFHelper
{
	/**
	 * PDF HTML Template
	 * 
	 * @var  String
	 */
	public static $html = '<!DOCTYPE HTML><html><head><meta http-equiv="content-type" content="text/html; charset=utf-8"><style type="text/css">* { font-family: arial !important; }</style></head><body>[TEMPLATE]</body>';

	/**
	 * Validates and creates the PDF
	 * 
	 * @param   object  $submission
	 * 
	 * @return  void
	 */
	public static function createSubmissionPDF($submission, $overwrite = false)
	{
		// pdf enabled check
		if (!self::isPDFEnabled($submission))
		{
			return;
		}

		$save_data_to_db = !isset($submission->form->save_data_to_db) || (isset($submission->form->save_data_to_db) && $submission->form->save_data_to_db === '1');

		$pdf = $submission->form->pdf;
		
		// Prepare the PDF's template with content plugins. 
		// This enables us to create a shared template with the Custom Module and 
		// load it across different forms using the {loadmoduleid ID} syntax.
		$pdf['pdf_template'] = \JHtml::_('content.prepare', $pdf['pdf_template']);

		// set filename prefix
		$prefix = (isset($pdf['pdf_filename_prefix']) && !empty($pdf['pdf_filename_prefix'])) ? $pdf['pdf_filename_prefix'] . '_' : '';

		// append the _{submission.id] suffix to each file name
		$pdf['pdf_filename'] = $prefix . '{submission.id}';
		
		// replace Smart Tags
		$pdf = \ConvertForms\SmartTags::replace($pdf, $submission);

		/**
		 * Try transiterating the file name using the native php function
		 * 
		 * This is used in Joomla 4.0 version of makeSafe but not in 3.X.
		 * 
		 * If the given filename is non-latin, then all characters will be removed from the filename via makeSafe and thus
		 * we wont be able to upload the file.
		 * 
		 * @see https://github.com/joomla/joomla-cms/pull/27974
		 */
		if (!defined('nrJ4') && function_exists('transliterator_transliterate') && function_exists('iconv'))
		{
			// Using iconv to ignore characters that can't be transliterated
			$pdf['pdf_filename'] = iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $pdf['pdf_filename']));
		}

		// Make safe after the Smart Tags replacement
		$pdf['pdf_filename'] = self::makeSafe($pdf['pdf_filename']);

		// upload folder check
		if (!$upload_folder = $pdf['pdf_upload_folder'])
		{
			return;
		}

        $upload_folder = rtrim($upload_folder, '/');
		
		// make sure upload folder exists and is writable
		if (!\NRFramework\File::createDirs(JPATH_ROOT . '/' . $upload_folder))
		{
			return;
		}
		
		$file_path = $pdf['pdf_upload_folder'] . '/' . $pdf['pdf_filename'] . '.pdf';

        // make sure the file exists
		// Do we really need to check if the file exists? 
        if (!\JFile::exists(JPATH_ROOT . '/' . $file_path) || $overwrite)
        {
			// Convert all relative paths found in <a> and <img> elements to absolute URLs
			$pdf_template = URLHelper::relativePathsToAbsoluteURLs($pdf['pdf_template']);
			
			self::create($upload_folder, $pdf_template, $pdf['pdf_filename']);

			// Create Submission Meta record only if we are saving data to the database
			if ($save_data_to_db)
			{
				\ConvertForms\SubmissionMeta::save($submission->id, 'pdf', '', $file_path);
			}

			return JURI::root() . $file_path;
		}

		return self::getSubmissionPDF($submission);
	}

	/**
	 * Creates a PDF given the upload folder and filename
	 * 
	 * @param   string  $upload_folder  The upload folder
	 * @param   string  $content  The content of the PDF
	 * @param   string  $filename  The filename
	 * 
	 * @return  void
	 */
	public static function create($upload_folder, $content, $filename)
	{
		// include domPDF library
		require JPATH_SITE . '/plugins/convertformstools/pdf/dompdf/autoload.inc.php';

		// replace [TEMPLATE] with given pdf contents
		$pdf_html = str_replace('[TEMPLATE]', $content, self::$html);

		// reference the Dompdf namespace
		$options = new \Dompdf\Options();
		$options->setIsRemoteEnabled(true);

		// instantiate and use the dompdf class
		$dompdf = new \Dompdf\Dompdf($options);

		$dompdf->loadHtml($pdf_html);

		// Render the HTML as PDF
		$dompdf->render();
		
		$output = $dompdf->output();

		$destination_filename = \JPath::clean(JPATH_ROOT . '/' . $upload_folder . '/' . self::makeSafe($filename) . '.pdf');
		
		file_put_contents($destination_filename, $output);
	}

	/**
	 * Retrieves the submission PDF
	 * 
	 * @param   object  $submission
	 * 
	 * @return  string
	 */
	public static function getSubmissionPDF($submission)
	{
		if (!isset($submission->form->pdf))
		{
			return;
		}
		
		if (!$file_path = \ConvertForms\SubmissionMeta::getValue($submission->id, 'pdf'))
		{
			return;
		}
		
        // make sure the file exists
        if (!\JFile::exists(JPATH_ROOT . '/' . $file_path))
        {
            return;
		}
		
		return JURI::root() . $file_path;
	}

	/**
	 * Sanitizes the file name
	 * 
	 * @param   string  $filename
	 * 
	 * @return  string
	 */
	public static function makeSafe($filename)
	{
		// Sanitize filename
		$filename = \JFile::makeSafe($filename);
		// Replace spaces with underscore
		$filename = str_replace(' ', '_', $filename);

		return $filename;
	}

	/**
	 * Checks whether we have PDF enabled
	 * 
	 * @param   object  $submission
	 * 
	 * @return  boolean
	 */
	public static function isPDFEnabled($submission)
	{
        return isset($submission->form->pdf['pdf_enabled']) && $submission->form->pdf['pdf_enabled'] == '1';
	}
}