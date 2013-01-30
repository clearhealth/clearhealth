<?php
/*****************************************************************************
*       HealthStatusController.php
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


class HealthStatusController extends WebVista_Controller_Action {

	protected $_handler = null;
	protected $_hsaAlert = null;

	public function indexAction() {
		$this->render();
	}

	public function listPatientActiveHsaJsonAction() {
		$personId = (int)$this->_getParam('personId');
		$rows = array();
		$hsa = new HealthStatusAlert();
		$hsaIterator = $hsa->getIteratorByStatusWithPatientId('active',$personId);
		foreach ($hsaIterator as $row) {
			$tmp = array();
			$tmp['id'] = $row->healthStatusAlertId;
			$tmp['data'][] = $row->message;
			$dateDueToTime = strtotime($row->dateDue);
			$dateDue = date('m/d/Y',$dateDueToTime);
			if ($dateDue == date('m/d/Y')) {
				$dateDue = 'DUE NOW';
			}
			$tmp['data'][] = $dateDue;
			$rows[] = $tmp;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows),true);
	}

	public function listAvailableHandlersAction() {
		$handler = new HealthStatusHandler();
		$handlerIterator = $handler->getIterator();
		$rows = $handlerIterator->toJsonArray('healthStatusHandlerId',array('name'));
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function processEditSubscriptionAction() {
		$personId = (int)$this->_getParam('personId');
		$subscribe = (bool)$this->_getParam('subscribe');
		$handlerId = (int)$this->_getParam('handlerId');

		$handlerPatient = new HealthStatusHandlerPatient();
		$handlerPatient->personId = $personId;
		$handlerPatient->healthStatusHandlerId = $handlerId;
		if (!$subscribe) { // add handler to handlerPatient
			$handlerPatient->setPersistMode(WebVista_Model_ORM::DELETE);
		}
		$handlerPatient->persist();
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(true);
	}

	public function processEditStatusAction() {
		$status = $this->_getParam('status','');
		$healthStatusAlertId = (int)$this->_getParam('healthStatusAlertId');
		$healthStatusAlert = new HealthStatusAlert();
		$healthStatusAlert->healthStatusAlertId = $healthStatusAlertId;
		$healthStatusAlert->populate();
		$healthStatusAlert->status = $status;
		$healthStatusAlert->persist();
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(true);
	}

	public function listSubscriptionsAction() {
		$personId = (int)$this->_getParam('personId');
		$handlerPatient = new HealthStatusHandlerPatient();
		$handlerPatientIterator = $handlerPatient->getIteratorByPatientId($personId);
		$rows = array();
		foreach ($handlerPatientIterator as $row) {
			$handler = $row->healthStatusHandler;
			$tmp = array();
			$tmp['id'] = $handler->healthStatusHandlerId;
			$tmp['data'][] = $handler->name;
			$rows[] = $tmp;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	/**
	 * Render add/edit alert page
	 */
	public function editAlertAction() {
		$id = (int)$this->_getParam('id');
		$this->_hsaAlert = new HealthStatusAlert();
		if ($id > 0) {
			$this->_hsaAlert->healthStatusAlertId = $id;
			$this->_hsaAlert->populate();
		}

		$this->_form = new WebVista_Form(array('name'=>'edit-alert'));
		$this->_form->setAction(Zend_Registry::get('baseUrl') . 'health-status.raw/process-edit-alert');
		$this->_form->loadORM($this->_hsaAlert,'hsaAlert');
		$this->_form->setWindow('winHSAEditAlertId');
		$this->view->form = $this->_form;
		$this->view->callback = $this->_getParam('callback','');

		$this->render('edit-alert');
	}

	/**
	 * Process modifications on add/edit alert
	 */
	public function processEditAlertAction() {
		$this->editAlertAction();
		$params = $this->_getParam('hsaAlert');
		$this->_hsaAlert->populateWithArray($params);
		$this->_hsaAlert->persist();
		$this->view->message = __('Record saved successfully');
		$this->render('edit-alert');
	}

	public function viewSubscribedPatientsAction() {
		$handlerId = (int)$this->_getParam('handlerId');
		$handlerPatient = new HealthStatusHandlerPatient();
		$handlerPatientIterator = $handlerPatient->getIteratorByHandlerId($handlerId);
		$rows = array();
		foreach ($handlerPatientIterator as $row) {
			$data = array();
			$data[] = $row->person->person->getDisplayName();
			$rows[$row->personId] = $data;
		}
		$this->view->rows = $rows;
		$this->render('view-subscribed-patients');
	}

	public function editHandlerAction() {
		$healthStatusHandlerId = (int)$this->_getParam('id');
		$this->_handler = new HealthStatusHandler();
		if ($healthStatusHandlerId > 0) {
			$this->_handler->healthStatusHandlerId = $healthStatusHandlerId;
			$this->_handler->populate();
		}

		$this->view->listConditions = array('');
		if (strlen($this->_handler->handlerObject) > 0) {
			$this->_handler->condition = 'custom';
			$this->view->listConditions['custom'] = 'Custom';
		}
		foreach (Handler::listConditions() as $id=>$name) {
			$this->view->listConditions[$id] = $name;
		}

		$this->_form = new WebVista_Form(array('name'=>'edit'));
		$this->_form->setAction(Zend_Registry::get('baseUrl') . 'health-status.raw/process-edit-handler');
		$this->_form->loadORM($this->_handler,'handler');
		$this->_form->setWindow('winEditHandlerId');
		$this->view->form = $this->_form;

		$this->render('edit-handler');
	}

	/**
	 * Process modifications on handler
	 */
	public function processEditHandlerAction() {
		$this->editHandlerAction();
		$params = $this->_getParam('handler');
		$name = $params['name'];
                // remove prefix characters if it's digit/numeric
                $name = ltrim(preg_replace('/^(?P<digit>\d+)/','',$name));
		$params['name'] = $name;
		$this->_handler->populateWithArray($params);
		if ($params['handlerObject'] == $this->_handler->generateDefaultHandlerObject()) {
			$this->_handler->handlerObject = '';
		}
		$this->_handler->persist();
		$this->view->message = __('Record saved successfully');
		$this->render('edit-handler');
	}

	public function listHandlersAction() {
		$handler = new HealthStatusHandler();
		$handlerIterator = $handler->getIterator();
		$listConditions = Handler::listConditions();
		$rows = array();
		foreach ($handlerIterator as $item) {
			$condition = 'Custom';
			if (isset($listConditions[$item->condition]) && !strlen($item->handlerObject) > 0) {
				$condition = $listConditions[$item->condition];
			}
			$tmp = array();
			$tmp['id'] = $item->healthStatusHandlerId;
			$tmp['data'][] = $item->name;
			$tmp['data'][] = $condition;
			$tmp['data'][] = $item->timeframe;
			$tmp['data'][] = ($item->active)?__('Yes'):__('No');
			$tmp['data'][] = '<a href="javascript:void(0)" onclick="viewSubscribedPatients('.$item->healthStatusHandlerId.')" title="'.__('View Subscribed Patients').'">'.__('View Patients').'</a>';
			$rows[] = $tmp;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function generateDefaultCodesAction() {
		$code = $this->_getParam('code');
		$handlerName = $this->_getParam('handlerName');
		$condition = (int)$this->_getParam('condition');

		$healthStatusHandler = new HealthStatusHandler();
		$healthStatusHandler->name = $handlerName;
		$healthStatusHandler->condition = $condition;
		$data = '';
		switch ($code) {
			case 'handlerObject':
				$data = $healthStatusHandler->generateDefaultHandlerObject();
				break;
			case 'datasource':
				$data = $healthStatusHandler->generateDefaultDatasource();
				break;
			case 'template':
				$data = $healthStatusHandler->generateDefaultTemplate();
				break;
		}

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

}
