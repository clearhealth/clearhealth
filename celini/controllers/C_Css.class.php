<?php
/**
 * Serves up css files from the cellini css dir
 *
 */
class C_Css extends Controller {

	function exists($action,$method = 'action') {
		if (strstr($_SERVER['PATH_INFO'],'..')) {
			return false;
		}
		return true;
	}

	function dispatch($action,$args,$method = 'action') {
		$finder =& new FileFinder();
		$finder->initCeliniPaths();
		$etag = "";
		$cssUrls = explode(',',$_SERVER['PATH_INFO']);
		$cssFiles = array();
		$fsize = 0;	
		foreach($cssUrls as $cssUrl) {
			$finder =& new FileFinder();
			$finder->initCeliniPaths();
			$cssFile = $finder->find($cssUrl);
			if($cssFile && substr($cssFile,'-4') === ".css" && file_exists("$cssFile")) {
				$etag .= $cssFile.filemtime($cssFile);
				$fsize += filesize($cssFile);
				$cssFiles[] = $cssFile;
			}
			else {
				echo "File Not Found: " .  $cssUrl;
				return false;
			}
		}
		$etag = md5($etag);		

		if (!$this->_compareETags($etag)) {
			header('Content-type: text/css; charset=UTF-8',true);
			header('Content-Length: '.$fsize);
			foreach ($cssFiles as $cssFile) {
				echo "/*** " .  basename($cssFile) . " ***/\n";
				readfile($cssFile);
				echo "\n";
			}
		}
		exit;
	}
	
	/**
	* Send cache control headers
	* @access  private
	*/
	function _sendCacheHeaders($etag,$notModified) {
		$config= Celini::ConfigInstance();
                $force_nocache = false;

                if (isset($_REQUEST['nocache'])) $force_nocache = true;

                if ($config->get('cacheHeadersEnabled')== true && !$force_nocache) {
			$offset = $config->get('cacheHeadersDuration');
			// calc the string in GMT not localtime and add the offset output the HTTP header
			$time = time(); // or filemtime($fn), etc
			header("Cache-Control: private, max-age=$offset, pre-check=$offset, post-check=$offset");
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', $time - $offset).' GMT');
				//set a last_modified to a time in the past.
  			$expire = "Expires: " . gmdate("D, d M Y H:i:s", $time + $offset) . " GMT";
				//set a last_modified to a time in the future.
  			header($expire);
			header('Pragma: hack');
				//we set this because if we do not sometimes the webserver will set a Pragma: nocache
		}else{
				//then we are going to ask the browser not to cache css files

			header('Cache-Control: must-revalidate');
			
		}

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

	function requireLogin() {
		return false;
	}
}
?>
