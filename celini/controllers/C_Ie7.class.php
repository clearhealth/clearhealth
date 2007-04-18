<?php
/**
 * Serves up ie7 js files from the cellini css dir
 *
 */
class C_Ie7 extends Controller {

	function exists($action,$method = 'action') {
		if (strstr($_SERVER['PATH_INFO'],'..')) {
			return false;
		}
		$img = CELINI_ROOT.'/js'.$_SERVER['PATH_INFO'];
		if (file_exists("$img")) {
			return true;
		}
		return false;
	}

	function dispatch($action,$args,$method = 'action') {
		$img = CELINI_ROOT.'/js'.$_SERVER['PATH_INFO'];

		$etag = md5($img.filemtime($img));

		if (!$this->_compareETags($etag)) {
			$info = getImageSize($img);
			header('Content-type: text/javascript; charset=UTF-8',true);
			header('Content-Length: '.filesize($img));
			readfile($img);
		}
		exit;
	}

	/**
	* Send cache control headers
	* @access  private
	*/
	function _sendCacheHeaders($etag,$notModified) {
		header('Cache-Control: must-revalidate');
		//header('Cache-Control: max-age=900');
		header('ETag: '.$etag);
		if ($notModified) {
			header('HTTP/1.0 304 Not Modified',false,304);
		}
	}

	/**
	 * Compare eTags
	 *
	 * @param   string  $serverETag server eTag
	 * @return  boolean
	 * @access  private
	 */
	function _compareEtags($serverETag) {
		if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
			if (strcmp($_SERVER['HTTP_IF_NONE_MATCH'],$serverETag) == 0) {
				$this->_sendCacheHeaders($serverETag,true);
				return true;
			}
		}
		$this->_sendCacheHeaders($serverETag,false);
		return false;
	}
}
?>
