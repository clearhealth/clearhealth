<?php
/**
 * Access point for all AJAX requests using HTML_AJAX
 *
 * @package com.clear-health.celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 *
 */
class C_AJAX extends Controller {
	var $server;
	
	var $_emptyReturn = false;

	function C_AJAX () {
		$this->Controller();
		$this->server = &Celini::ajaxServerInstance();
		$this->server->initMethods = true;

		// register celini js libraries
		$this->server->registerJSLibrary('validate',array('validate.js','dateparse.js'),CELINI_ROOT.'/js/');
		$this->server->registerJSLibrary('calendar',array('calendar.js','calendar-en.js','calendar-setup.js'),CELINI_ROOT.'/js/cal/');
		$this->server->registerJSLibrary('autocomplete','autocomplete.js',CELINI_ROOT.'/js/');
		$this->server->registerJSLibrary('overlib','overlib.js',CELINI_ROOT.'/js/');
		$this->server->registerJSLibrary('ie7','ie7-standard-p.js',CELINI_ROOT.'/js/');
		$this->server->registerJSLibrary('scriptaculous',array('prototype.js','scriptaculous.js','builder.js','effects.js','dragdrop.js','controls.js','slider.js'),CELINI_ROOT.'/js/scriptaculous/');
		$this->server->registerJSLibrary('scrollbar','scrollbar.js',CELINI_ROOT.'/js/');
		$this->server->registerJSLibrary('clnigrid','clniGrid.js',CELINI_ROOT.'/js/');
		$this->server->registerJSLibrary('clniConfirmBox', 'clniConfirmBox.js', CELINI_ROOT . '/js/');
		$this->server->registerJSLibrary('clniConfirmLink', 'clniConfirmLink.js', CELINI_ROOT . '/js/');
		$this->server->registerJSLibrary('clniPopup', 'clniPopup.js', CELINI_ROOT . '/js/');
		$this->server->registerJSLibrary('hover', 'hover.js', CELINI_ROOT . '/js/');
		$this->server->registerJSLibrary('clniBehaviors', 'behavior.js', CELINI_ROOT . '/js/');
		$this->server->registerJSLibrary('clniUtil', 'clniUtil.js', CELINI_ROOT . '/js/');
		$this->server->registerJSLibrary('clniTable', 'clniTable.js', CELINI_ROOT . '/js/');
		$this->server->registerJSLibrary('clniCookie', 'clniCookie.js', CELINI_ROOT . '/js/');
		$this->server->registerJSLibrary('moofx', array('moo.fx.js','moo.fx.pack.js'), CELINI_ROOT . '/js/');

		// load custom js libs from the session
		$session =& Celini::sessionInstance();
		$libs = $session->get('AJAX:customLibs',array());
		foreach($libs as $name => $path) {
			$this->server->registerJSLibrary($name, basename($path), dirname($path).'/');
		}
				
		// load all the config classes
		$conf =& Celini::configInstance();
		$classes = $conf->get('ajaxConfClasses');
		$classes[] = 'CeliniAJAX';
		if(is_array($classes)){
			foreach($classes as $class) {
				$GLOBALS['loader']->requireOnce("includes/$class.class.php");
				$init =& new $class();
				$this->server->registerInitObject($init);
			}
		}
	}

	/**
	 * If this request is called, C_AJAX should continue, but should not display
	 * anything.
	 *
	 * @return false
	 */
	function requireLogin($arg1,$arg2) {
		if (parent::requireLogin($arg1,$arg2) === false) {
			return false;
		}
		$this->_emptyReturn = true;
		return false;
	}

	function exists() {
		return true;
	}

	function dispatch() {
		if ($this->_emptyReturn) {
			return '';
		}
		
		$this->server->handleRequest();
	}
}
?>
