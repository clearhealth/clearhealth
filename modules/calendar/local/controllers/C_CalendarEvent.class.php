<?php

class C_CalendarEvent extends Controller
{

	function C_CalendarEvent(){
		parent::Controller();
		$this->view->path = 'event';
	
	}
	
	/**
	 * Handle adding an {@link Event}
	 */
	function actionAdd() {
		$this->view->assign('addMode', true);
		return $this->actionEdit();
	}
	
	
	/**
	 * Handle editing an {@link Event}
	 */
	function actionEdit() {
		$event =& Celini::newORDO('CalendarEvent', $this->GET->getTyped('event_id', 'int'));
		$this->view->assign_by_ref('event', $event);
		return $this->view->render('edit.html');
	}
	
	
	/**
	 * Generic process handling, saves an {@link Event}
	 *
	 * {@inheritdoc}
	 */
	function process() {
		$rawPost = $this->POST->getRaw('Event');
		$event =& Celini::newORDO('CalendarEvent', (int)$rawPost['id']);
		$event->populate_array($rawPost);
		$event->persist();
	}
}

