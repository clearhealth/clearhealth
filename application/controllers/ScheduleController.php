<?php
/*****************************************************************************
*       ScheduleController.php
*
*       Author:  ClearHealth Inc. (www.clear-health.com)        2009
*       
*       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
*       respective logos, icons, and terms are registered trademarks 
*       of ClearHealth Inc.
*
*       Though this software is open source you MAY NOT use our 
*       trademarks, graphics, logos and icons without explicit permission. 
*       Derivitive works MUST NOT be primarily identified using our 
*       trademarks, though statements such as "Based on ClearHealth(TM) 
*       Technology" or "incoporating ClearHealth(TM) source code" 
*       are permissible.
*
*       This file is licensed under the GPL V3, you can find
*       a copy of that license by visiting:
*       http://www.fsf.org/licensing/licenses/gpl.html
*       
*****************************************************************************/


/**
 * Schedule controller
 */
class ScheduleController extends WebVista_Controller_Action {

	protected $_calendarSession;

	public function init() {
		// calendarSession
		$this->_calendarSession = new Zend_Session_Namespace('CalendarController');
	}

	public function indexAction() {
		$this->newAction();
	}

	public function newAction() {
		$colIndex = $this->_getParam('colIndex');
		$sessionFilters = $this->_calendarSession->filter;
		if (!isset($sessionFilters->columns[$colIndex])) {
			$msg = __('Cannot generate column with that index, there is no filter defined for that column Index: ') . $colIndex;
			throw new Exception($msg);
		}
		$column = $sessionFilters->columns[$colIndex];

		$providerId = (int)$column['providerId'];
		$this->view->providerId = $providerId;
		$headerText = '';
		if ($providerId > 0) {
			$provider = new Provider();
			$provider->setPersonId($providerId);
			$provider->populate();
			$headerText = $provider->displayName;
		}
		$roomId = 0;
		if (isset($column['roomId'])) {
			$roomId = $column['roomId'];
		}
		$this->view->roomId = $roomId;
		if ($roomId > 0) {
			$room = new Room();
			$room->id = $roomId;
			$room->populate();
			$headerText .= ' -> '.$room->name;
		}
		if (isset($column['dateFilter'])) {
			$headerText .= " ({$column['dateFilter']})";
		}
		$this->view->headerText = $headerText;

		$templates = array(''=>'');
		$templates['tpl1'] = 'Provider 1 Template';
		$templates['tpl2'] = 'Provider 2 Template';
		$templates['tpl3'] = 'Provider 3 Template';
		$this->view->templates = $templates;

		// $this->_calendarSession->filter; calendar filter
		$this->render('new');
	}

	public function toolbarXmlAction() {
		header('Content-Type: text/xml');
		$this->render('toolbar-xml');
	}

	public function getEventsContextMenuAction() {
		header('Content-Type: text/xml');
		$this->render('get-events-context-menu');
	}

	public function listEventsAction() {
		$filters = array();
		$filters['start'] = date('Y-m-d 00:00:00');
		$filters['providerId'] = (int)$this->_getParam('providerId');
		$filters['roomId'] = (int)$this->_getParam('roomId');
		$scheduleEventIterator = new ScheduleEventIterator();
		$scheduleEventIterator->setFilters($filters);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$scheduleEventIterator->toJsonArray('scheduleEventId',array('title','start','end'))));
	}

	public function processDeleteEventsAction() {
		$ids = $this->_getParam('id');
		$arrIds = explode(',',$ids);
		$scheduleEvent = new ScheduleEvent();
		foreach ($arrIds as $id) {
			$scheduleEvent->scheduleEventId = $id;
			$scheduleEvent->setPersistMode(WebVista_Model_ORM::DELETE);
			$scheduleEvent->persist();
		}
		$data = true;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

}
