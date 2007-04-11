<?php

$loader->requireOnce('/includes/viewer/Viewer.class.php');

/**
 * Special Viewer which is used for sending headers 
 * for downloading file
 *
 */
class FileDownLoader extends Viewer {

	/**
	 * Sends HTTP headers which needs for file download
	 *
	 */
	function sendHeaders() {
		if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'],'MSIE')) {
			header('Content-Type: application/force-download');
		} else {
			header('Content-Type: application/octet-stream');
		}	
		header('Content-Length: '.strlen($this->content));
		header('Content-disposition: attachment; filename="'.$this->filename.'"');
	}	
	
}

?>