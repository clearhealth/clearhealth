<?php
$loader->requireOnce('controllers/Controller.class.php');
$loader->requireOnce('includes/clni/clniHTMLHead.class.php');

/**
 * An abstract "PageType" controller which contains the methods necessary for a {@link C_Main} or
 * {@link C_Print} type controller.
 *
 * @see C_Main, C_Print, Controller
 * @author Travis Swicegood <tswicegood@uversainc.com>
 * @package com.uversainc.celini
 * @abstract
 */
class C_PageType extends Controller
{
	/**#@+
	 * {@inheritdoc}
	 */
	function C_PageType() {
		parent::Controller();
	}
	
	function exists($action,$method = 'action') {
		$d = new Dispatcher();
		if (parent::exists($action,$method)) {
			return true;
		} else if ($d->controllerExists($action)) {
			if ($method === 'action') {
				return true;
			}
		}
		return false;
	}

	function dispatch($action,$args,$method = 'action') {
		if (strtolower($action) !== 'report' && parent::exists($action,$method)) {
			return parent::dispatch($action,$args,$method);
		}
		array_unshift($args,$action);
		return $this->run_child_controller($args);
	}

	function run_child_controller($args) {
		$c =& new Dispatcher();
		$display = $c->act($args);
		if (!$c->_continue_processing) {
			return $display;
		}
		if (is_object($this->_manager)) {
			$this->_manager->postProcess();
		}
		return $this->display($display);
	}
	
	function display($display = '') {
		$this->view->assign_by_ref('HTMLHead',clniHTMLHead::getInstance());
		$this->view->assign('display', $display);
		return $this->view->render($this->_determinePage() . '.html');	
	}
	/**#@-*/
	
	/**
	 * Determines and return the page name to load
	 *
	 * @return string
	 * @access protected
	 */
	function _determinePage() {
		if (isset($GLOBALS['config']['default_template'])) {
			return $GLOBALS['config']['default_template'];
		}
		return 'list';
	}
}

