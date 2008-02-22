<?php

$loader->requireOnce('controllers/C_Main.class.php');

class C_XML extends C_Main
{
	function C_XML() {
		$this->C_Main('xml');
		$this->view->path = 'main';
	}
	
	
	/**
	 * Serves as the display portion of this controller.
	 *
	 * If $_GET['embedded'] is specified, this will strip out all html, head,
	 * and body tags.
	 *
	 * @param  string
	 * @return string
	 */
	function display($display = '') {
		header('Content-type: text/xml');
		$return = parent::display($display);
		if ($this->GET->exists('embedded')) {
			$return = preg_replace('/<([\/]?)(html|head|body)([^>]*)>/', '', $return);
		}
		return $return;
	}
}
