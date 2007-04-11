<?php

/**
 * Abstract class provides interface for different viewers
 *
 */

class Viewer {

	/**
	 * File's content which will be shown
	 *
	 * @var blob
	 */
	var $content;
	
	/**
	 * File's name which will be sent in the HTTP headers
	 *
	 * @var string
	 */
	var $filename;
	
	/**
	 * mime-type of the file
	 *
	 * @var string
	 */
	var $mimeType;
	
	/**
	 * Just Viewer::__construct
	 *
	 * @param blob $content
	 * @param string $filename
	 * @return Viewer
	 */
	function Viewer($content, $filename=null) {
		$this->content = $content;
		$this->filename = $filename;
		//if(function_exists('mime_content_type')) {
		//	$this->mimeType = mime_content_type($filename);	
		//}else{
			$this->mimeType = Viewer::mimeContentType($filename);
		//}
		
	}
	
	/**
	 * Sends HTTP headers to the browser and 
	 * than prints file's content
	 *
	 * @return false
	 */
	function run() {
		if(headers_sent()) {
			return false;
		}
		$this->sendHeaders();
		print $this->content;
		exit;
	}
	
	/**
	 * Abstarct methos which should be redefined
	 * in the child classes
	 *
	 */
	function sendHeaders() {
		header('Content-Type: '.$this->mimeType);
		header('Content-Length: '.strlen($this->content));
		if($this->mimeType != "text/plain") {
			header('Content-disposition: inline; filename="'.$this->filename.'"');			
		}	
	}
	
	/**
	 * Returns mime-type by filename.
	 * This funcion requires mime.types file which is located near this class file
	 *
	 * @param string $filename
	 * @return string
	 */
	function mimeContentType($filename) {
		$mimeType = "text/plain";
		if(!($pos = strrpos($filename, "."))) {
			return $mimeType;
		}
		$ext = substr($filename,$pos+1);
		$mimeTypeFile=dirname(__FILE__)."/mime.types";
		if(!file_exists($mimeTypeFile)) {
			return $mimeType;
		}
		$content = file($mimeTypeFile);
		foreach($content as $line) {
			if(preg_match("/^([^\t]+).*?( |\t)$ext( |$)/",$line,$m)) {
				$mimeType = $m[1];
				break;
			}
		}
		return $mimeType;
	}
}

?>