<?php
/**
 * Serves up images from the cellini images dir
 *
 * @todo: do we want to use a fileloader and use this for all images including app ones ???
 */
class C_Images extends Controller {

	function exists($action,$method = 'action') {
		if (strstr($_SERVER['PATH_INFO'],'..')) {
			return false;
		}
		$img = $this->_imgPath();
		if (file_exists("$img")) {
			return true;
		}
		return false;
	}

	function _imgPath() {
		$finder =& new FileFinder();
		$finder->initCeliniPaths();
		$fileName = str_replace('Images','images',$_SERVER['PATH_INFO']);
		$imgPath = '';
		if (strtolower(substr($fileName,-3)) === "pdf") {
			$fileName = basename($fileName);
			$finder->addPath(APP_ROOT . '/user/report_templates');
			$finder->addPath(APP_ROOT . '/user/form_templates');
			$imgPath = $finder->find(str_replace('images/','',$fileName));
			header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
			header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . " GMT" );
			header( "Cache-Control: no-cache, must-revalidate" );
			header( "Pragma: no-cache" );


			
		}
		else {
			$imgPath = $finder->find(str_replace('Images','images',$fileName));
		}
		return $imgPath;
	}

	function dispatch($action,$args,$method = 'action') {
		$img = $this->_imgPath();
		$mtime = filemtime($img);
		$etag = md5($img.$mtime);
		header("Last-Modified: ".gmdate("D, d M Y H:i:s") . ' GMT');

		if (!$this->_compare($etag,$mtime)) {
			$info = getImageSize($img);
			header("Content-Type: {$info['mime']}");
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
		$config= Celini::ConfigInstance(); 
		$force_nocache = false;
		if (isset($_REQUEST['nocache'])) {$force_nocache=true;}
		if ($config->get('cacheHeadersEnabled')== true && !$force_nocache) {
			$maxage = $config->get('cacheHeadersImgMaxAge');
			header("Cache-Control: private, max-age=$maxage, pre-check=$maxage, post-check=$maxage");
			//then we are going to ask the browser to cache the image files
			$offset = $config->get('cacheHeadersDuration');

			// calc the string in GMT not localtime and add the offset
			//output the HTTP header
			$time = time(); // or filemtime($fn), etc
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', $time - $offset).' GMT');
				//set a last_modified to a time in the past.
  			$expire = "Expires: " . gmdate("D, d M Y H:i:s", $time + $offset) . " GMT";
				//set a last_modified to a time in the future.
  			header($expire);
			header('Pragma: hack');
				//we set this because if we do not sometimes the webserver will set a Pragma: nocache
		}else{
				//then we are going to ask the browser not to cache image files

			header('Cache-Control: no-cache must-revalidate max-age=0');
			
		}


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
	function _compare($serverETag,$mtime) {
		if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
			if (strcmp($_SERVER['HTTP_IF_NONE_MATCH'],$serverETag) == 0) {
				$this->_sendCacheHeaders($serverETag,true);
				return true;
			}
		}
		//var_dump($_SERVER);
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			if (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) < $mtime) {
				$this->_sendCacheHeaders($serverETag,true);
				return true;
			}
		}
		$this->_sendCacheHeaders($serverETag,false);
		return false;
	}
}
?>
