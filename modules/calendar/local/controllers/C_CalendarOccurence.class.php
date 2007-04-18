<?php

/**
 * Controller for {@link Occurence} ORDO
 *
 * @package com.uversa.calendar
 */
class C_CalendarOccurence extends Controller
{

	function C_CalendarOccurence(){
		parent::Controller();
		$this->view->path = 'occurence';
	}
	
	/**
	 * Handle adding an {@link Occurence}
	 */
	function actionAdd() {
		$this->view->assign('addMode', true);
		return $this->_actionEdit();
	}
	
	
	/**
	 * Handle editing an {@link Occurence}
	 */
	function actionEdit() {
		$event =& Celini::newORDO('CalendarOccurence', $this->GET->getTyped('event_id', 'int'));
		$this->view->assign_by_ref('event', $event);
		return $this->view->render('edit.html');
	}
	
	
	/**
	 * Generic process handling, saves an {@link Occurence}
	 *
	 * {@inheritdoc}
	 */
	function process() {
		$rawPost = $this->POST->getRaw('Occurence');
		$event =& Celini::newORDO('Occurence', (int)$rawPost['id']);
		$event->populate_array($rawPost);
		$event->persist();
	}
}

