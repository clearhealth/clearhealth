<?php
/*****************************************************************************
*       GeneralAlertsController.php
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


class GeneralAlertsController extends WebVista_Controller_Action {

	public function indexAction() {
		$this->render();
	}

	public function myAlertsAction() {
		$personId = Zend_Auth::getInstance()->getIdentity()->personId;
		$team = new TeamMember();
		$teamId = $team->getTeamByPersonId($personId);
		$rows = array();
		if (true) {
			$alertMsg = new GeneralAlert();
			$alertMsgIterator = $alertMsg->getIteratorByTeam($teamId);
			foreach ($alertMsgIterator as $alert) {
				$tmp = array();
				$tmp['id'] = $alert->generalAlertId;
				$tmp['data'][] = '<img src="'.$this->view->baseUrl.'/img/medium.png'.'" alt="'.$alert->urgency.'" /> '.$alert->urgency;
				// below are temporary data
				$objectClass = $alert->objectClass;
				if (!class_exists($objectClass)) {
					continue;
				}
				$obj = new $objectClass();
				foreach ($obj->_primaryKeys as $key) {
					$obj->$key = $alert->objectId;
				}
				$obj->populate();
				$patient = new Patient();
				$patient->personId = $obj->personId;
				$patient->populate();
				$tmp['data'][] = $patient->person->getDisplayName(); // patient
				$tmp['data'][] = ''; // location
				$tmp['data'][] = date('m/d/Y H:i',strtotime($alert->dateTime));
				$tmp['data'][] = str_replace("\n",', ',$alert->message);
				$forwardedBy = '';
				if ($alert->forwardedBy > 0) {
					$person = new Person();
					$person->personId = (int)$alert->forwardedBy;
					$person->populate();
					$forwardedBy = $person->displayName;
				}
				$tmp['data'][] = $forwardedBy; // forwarded
				$tmp['data'][] = $alert->comment; // comment
				$controllerName = call_user_func($objectClass . "::" . "getControllerName");
				$jumpLink = call_user_func_array($controllerName . "::" . "buildJSJumpLink",array($alert->objectId,$alert->userId,$objectClass));
				$js = "function jumpLink{$objectClass}(objectId,patientId) {\n{$jumpLink}\n}";
				$tmp['data'][] = $js;
				$tmp['data'][] = $objectClass.':'.$alert->objectId.':'.$patient->personId;
				$rows[] = $tmp;
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function editHandlerAction() {
		$generalAlertHandlerId = (int)$this->_getParam('id');
		$this->_handler = new GeneralAlertHandler();
		if ($generalAlertHandlerId > 0) {
			$this->_handler->generalAlertHandlerId = $generalAlertHandlerId;
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
		$this->_form->setAction(Zend_Registry::get('baseUrl') . 'general-alerts.raw/process-edit-handler');
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
		$handler = new GeneralAlertHandler();
		$handlerIterator = $handler->getIterator();
		$listConditions = Handler::listConditions();
		$rows = array();
		foreach ($handlerIterator as $item) {
			$condition = 'Custom';
			if (isset($listConditions[$item->condition]) && !strlen($item->handlerObject) > 0) {
				$condition = $listConditions[$item->condition];
			}
			$tmp = array();
			$tmp['id'] = $item->generalAlertHandlerId;
			$tmp['data'][] = $item->name;
			$tmp['data'][] = $condition;
			$tmp['data'][] = ($item->active)?__('Yes'):__('No');
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

		$generalAlertHandler = new GeneralAlertHandler();
		$generalAlertHandler->name = $handlerName;
		$generalAlertHandler->condition = $condition;
		$data = '';
		switch ($code) {
			case 'handlerObject':
				$data = $generalAlertHandler->generateDefaultHandlerObject();
				break;
			case 'datasource':
				$data = $generalAlertHandler->generateDefaultDatasource();
				break;
			case 'template':
				$data = $generalAlertHandler->generateDefaultTemplate();
				break;
		}

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function userAddAlertAction() {
		$this->render();
	}

	public function processUserAddAlertAction() {
		$alertData = $this->_getParam('alert');
		$alertData['dateTime'] = date('Y-m-d H:i:s');
		$alert = new GeneralAlert();
		$alert->populateWithArray($alertData);
		$alert->persist();
		trigger_error(print_r($alert,true),E_USER_NOTICE);
		$ret = true;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function markAlertDoneAction() {
		$generalAlertId = (int)$this->_getParam('generalAlertId');
		$alert = new GeneralAlert();
		$alert->generalAlertId = $generalAlertId;
		$alert->populate();
		$alert->status = 'processed';
		$alert->persist();
		$ret = true;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function forwardAlertAction() {
		$alertId = (int)$this->_getParam('alertId');
		$alert = new GeneralAlert();
		$alert->generalAlertId = $alertId;
		$alert->populate();

		$this->_form = new WebVista_Form(array('name'=>'forwardAlert'));
		$this->_form->setAction(Zend_Registry::get('baseUrl') . 'general-alerts.raw/process-forward-alert');
		$this->_form->loadORM($alert,'forwardAlert');
		$this->_form->setWindow('windowForwardAlertId');
		$this->view->form = $this->_form;

		$provider = new Provider();
		$providerIterator = $provider->getIter();
		$this->view->providers = $providerIterator->toArray('personId','displayName');

		$this->view->jsCallback = $this->_getParam('jsCallback');
		$this->render('forward-alert');
	}

	public function processForwardAlertAction() {
		$ret = false;
		$recipients = $this->_getParam('recipients');
		$alertData = $this->_getParam('forwardAlert');
		$alertData['dateTime'] = date('Y-m-d H:i:s');
		$alert = new GeneralAlert();
		if (isset($alertData['generalAlertId'])) {
			$alert->generalAlertId = (int)$alertData['generalAlertId'];
			if ($alert->populate()) {
				$alert->populateWithArray($alertData);
				$arrRecipients = explode(',',$recipients);
				foreach ($arrRecipients as $recipient) {
					$tmpAlert = clone $alert;
					$tmpAlert->generalAlertId = 0;
					$tmpAlert->userId = (int)$recipient;
					$tmpAlert->forwardedBy = (int)Zend_Auth::getInstance()->getIdentity()->personId;
					$tmpAlert->persist();
				}
				$ret = true;
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

}
