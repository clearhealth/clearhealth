<?php

/**
 * A singleton for managing user messaging
 *
 * @author	Joshua Eichorn	<jeichorn@mail.com>
 * @author  Travis Swicegood <tswicegood@uversainc.com>
 * @package com.clear-health.celini
 */
class Messages
{
	/**
	 * Get an instance of the message class
	 *
	 * @return Message
	 */
	function &getInstance() {
		if (!isset($GLOBALS['Message']) || !is_a($GLOBALS['Message'],'Messages')) {
			$GLOBALS['Message'] = new Messages();
		}
		if (!isset($_SESSION['CELINI_MESSAGES'])) {
			$_SESSION['CELINI_MESSAGES'] = array();
		}
		return $GLOBALS['Message'];
	}

	/**
	 * Add a message
	 */
	function addMessage($title, $message='', $class='') {
		$_SESSION['CELINI_MESSAGES'][] = array('title' => $title, 'message' => $message, 'class' => $class);
	}

	/**
	 * Return html for the blocks of messages
	 *
	 * @return string
	 */
	function render() {
		$c = new Controller();
		$c->assign("messages",$this->getMessages());

		return $c->fetch(Celini::getTemplatepath("/message.html")); 
	}
	
	/**
	 * Return message array
	 *
	 * @return array
	 */
	function getMessages() {
		$return = $_SESSION['CELINI_MESSAGES'];
		unset($_SESSION['CELINI_MESSAGES']);
		return $return;	
	}
}
?>
