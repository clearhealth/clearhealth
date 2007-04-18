<?php
/**
 * Class for building elements in the header of an html page
 *
 * This class is a singleton
 */
class clniHTMLHead {

	var $elements = array();

	var $_css = array();
	var $_js = array();

	/**
	 * Singleton method
	 */
	function &getInstance() {
		if (!isset($GLOBALS['_CACHE']['clniHTMLHead'])) {
			$GLOBALS['_CACHE']['clniHTMLHead'] =& new clniHTMLHead();
		}
		return $GLOBALS['_CACHE']['clniHTMLHead'];
	}

	/**
	 * Add any html you want to the head, $key is option but if set allows this html to be overridden
	 */
	function addElement($html,$key = false) {
		if (!$key) {
			$this->elements[] = $html;
		}
		else {
			$this->elements[$key] = $html;
		}
	}

	/**
	 * Add an external css file to the page using the css controller
	 */
	function addExternalCss($name) {
		if(empty($name)) return;
		$url = Celini::link($name . '.css', 'css',false,false);
		$this->addElement('<link  href="' . $url . '" rel="stylesheet" type="text/css" />', '_css'. $name);
	}

	/**
	 * Add some css inline
	 */
	function addInlineCss($css,$key = false) {
		if ($key === false) {
			$key = count($this->_css);
		}
		$this->_css[$key] = $css;
	}
	
	/**

	/**
	 * Add some javascript inline
	 */
	function addInlineJs($javascript,$key = false) {
		if ($key === false) {
			$key = count($this->_js);
		}
		$this->_js[$key] = $javascript;
	}
	
	/**
	 * Add a javascript file to the AJAX controller
	 */
	function addJs($files, $key = false) {
		settype($files, 'array');
		if(empty($files)) return;
		$ajax =& Celini::ajaxInstance();
		if ($key === false) {
			$key = count($ajax->jsLibraries);
		}
		$ajax->jsLibraries[$key] = $files;
	}

	/**
	 * Add a javascript file in the templates dir
	 */
	function addNewJs($name,$path) {
		$fullPath = $GLOBALS['finder']->find($path);

		$session =& Celini::sessionInstance();
		$session->merge('AJAX:customLibs',array($name=>$fullPath));
		$this->addJs($name);
	}

	/**
	 * Render the elements out as a string
	 */
	function render() {
		if (count($this->_css) > 0) {
			$this->elements['inlineCss'] = '<style type="text/css">' . implode("\n",$this->_css) . '</style>';
		}
		if (count($this->_js) > 0) {
			$this->elements['inlineJs'] = '<script type="text/javascript">' . implode("\n",$this->_js) . '</script>';
		}
		return implode("\n",$this->elements);
	}
}
?>