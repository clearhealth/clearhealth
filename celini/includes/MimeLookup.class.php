<?php
/**
 * @package	com.clear-health.celini
 */
 
/**
 * Provides utiltity funtions to map from a mime/type to a user displayable name, 
 * also has a method to tell if the type is displayable in the browser
 *
 * Mainly lookup tables
 *
 * @package	com.clear-health.celini
 */
class MimeLookup {

	/**
	 * Display lookup array
	 */
	var $mimeLookup = array(
		'audio/wav'			=> 'Wave sound file',
		'font/ttf'			=> 'TrueType Font',
		'image/gif'			=> 'GIF image',
		'image/jpeg'			=> 'JPEG image',
		'image/tiff'			=> 'TIFF image',
		'image/bmp'			=> 'Bitmap image',
		'image/png'			=> 'PNG image',
		'text/html'			=> 'HTML file',
		'text/plain'			=> 'Plain text',
		'video/mpeg'			=> 'MPEG video',
		'video/quicktime'		=> 'Quicktime video',
		'application/pdf'		=> 'Portable Document Format (PDF) File',
		'application/msexcell' 		=> 'Microsoft Excel file',
		'application/mspowerpoint' 	=> 'Microsoft Powerpoint file',
		'application/msword'		=> 'Microsoft Word file',
		);
	
	/**
	 * Mime cleanup mapping array
	 */
	var $mimeCleanup = array(
		'plain/text'			=> 'text/plain',
		'image/jpg'			=> 'image/jpeg',
		'text/vnd.ms-word'		=> 'application/msword',
		'application/vnd.ms-excel' 	=> 'application/msexcell',
		);

	/**
	 * List of mimetypes which are directly displayable in the browser
	 */
	var $displayable = array(
		'image/gif'	=> 'image',
		'image/jpeg'	=> 'image',
		'image/png'	=> 'image',
		'text/plain'	=> 'document',
		'text/html'	=> 'document',
		'application/pdf'	=> 'document',
		);

	/**
	 * Lookup a display name from a mime/type
	 */
	function lookup($mimetype) {
		if (isset($this->mimeLookup[$mimetype])) {
			return $this->mimeLookup[$mimetype];
		}
		return $mimetype;
	}

	/**
	 * Cleanup a mimetype changing a secondary type to its primary
	 */
	function cleanup($mimetype) {
		if (isset($this->mimeCleanup[$mimetype])) {
			return $this->mimeCleanup[$mimetype];
		}
		return $mimetype;
	}

	/**
	 * Is the mimetype displayable in the browser
	 */
	function displayable($mimetype) {
		if (isset($this->displayable[$mimetype])) {
			return true;
		}
		return false;
	}
}
?>
