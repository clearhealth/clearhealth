<?php
/*****************************************************************************
*       ClaimRulesController.php
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


class ClaimRulesController extends WebVista_Controller_Action {

	public function indexAction() {
		$this->render();
	}

	public function addAction() {
		$this->_edit();
	}

	public function editAction() {
		$this->_edit((int)$this->_getParam('groupId'));
	}

	protected function _edit($groupId=null) {
		$groupId = (int)$groupId;
		$claimRule = new ClaimRule();
		$claimRule->event = ClaimRule::EVENT_WARNING;
		$action = 'add';
		if ($groupId > 0) {
			$claimRule->groupId = $groupId;
			$claimRule->populateWithGroupId();
			$claim = new ClaimRule();
			$claim->title = $claimRule->title;
			$claim->message = $claimRule->message;
			$claim->groupId = $claimRule->groupId;
			$claim->event = $claimRule->event;
			$claimRule = $claim; // swap, we only need the hidden fields
			$action = 'edit';
		}
		$this->view->action = $action;
		$form = new WebVista_Form(array('name'=>'claimRule'));
		$form->setAction(Zend_Registry::get('baseUrl').'claim-rules.raw/process-'.$action);
		$form->loadORM($claimRule,'claimRule');
		$form->setWindow('windowEditClaimRuleId');
		$this->view->form = $form;
		$this->view->claimRule = $claimRule;

		$modifiers = array(''=>'');
		$enumeration = new Enumeration();
		$enumeration->populateByUniqueName('Procedure Modifiers');
		$closure = new EnumerationClosure();
		$descendants = $closure->getAllDescendants($enumeration->enumerationId,1,true);
		foreach ($descendants as $row) {
			$modifiers[$row->key] = $row->key.': '.$row->name;
		}
		$this->view->modifiers = $modifiers;

		$insurancePrograms = array(''=>'');
		foreach (InsuranceProgram::getInsurancePrograms() as $key=>$value) {
			$insurancePrograms[$key] = $value;
		}
		$this->view->insurancePrograms = $insurancePrograms;

		$this->render('edit');
	}

	public function processAddAction() {
		$this->_processEdit();
	}

	public function processEditAction() {
		$this->_processEdit();
	}

	protected function _processEdit() {
		$title = $this->_getParam('title');
		$message = $this->_getParam('message');
		$groupId = (int)$this->_getParam('groupId');
		$event = (int)$this->_getParam('event');
		$rules = $this->_getParam('rules');
		$ret = false;
		if (is_array($rules)) {
			$validIds = array();
			if ($groupId > 0) {
				$iterator = new ClaimRuleIterator();
				$iterator->setFilters(array('groupId'=>$groupId));
				foreach ($iterator as $claimRule) {
					if (!isset($rules[$claimRule->claimRuleId])) { // delete  orphaned rule
						$claimRule->setPersistMode(WebVista_Model_ORM::DELETE);
						$claimRule->persist();
					}
					else $validIds[$claimRule->claimRuleId] = $claimRule->claimRuleId;
				}
			}
			else {
				$groupId = WebVista_Model_ORM::nextSequenceId();
			}
			foreach ($rules as $claimRuleId=>$value) {
				$claimRule = new ClaimRule();
				$claimRule->populateWithArray($value);
				if (isset($validIds[$claimRuleId])) {
					$claimRule->claimRuleId = $claimRuleId;
				}
				else {
					$claimRule->claimRuleId = 0;
					$claimRule->dateTime = date('Y-m-d H:i:s');
				}
				$claimRule->title = $title;
				$claimRule->message = $message;
				$claimRule->groupId = $groupId;
				$claimRule->event = $event;
				$claimRule->persist();
			}
			$ret = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(true);
	}

	public function listGroupsAction() {
		$rows = array();
		foreach (ClaimRule::listGroups() as $groupId=>$value) {
			$row = array();
			$row['id'] = $groupId;
			$row['data'] = array();
			$row['data'][] = $value['title'];
			$row['data'][] = $value['displayEvent'];
			$row['data'][] = $value['message'];
			$rows[] = $row;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function getMenuAction() {
		header('Content-Type: application/xml;');
		$this->render('get-menu');
	}

	public function processDeleteGroupAction() {
		$ret = false;
		$groupId = (int)$this->_getParam('groupId');
		if ($groupId > 0) {
			// TODO: needs to be enclosed in transaction?
			$iterator = new ClaimRuleIterator();
			$iterator->setFilters(array('groupId'=>$groupId));
			foreach ($iterator as $claimRule) {
				$claimRule->setPersistMode(WebVista_Model_ORM::DELETE);
				$claimRule->persist();
			}
			$ret = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function listRulesAction() {
		$groupId = (int)$this->_getParam('groupId');
		$rows = array();
		$iterator = new ClaimRuleIterator();
		$iterator->setFilters(array('groupId'=>$groupId));
		foreach ($iterator as $claimRule) {
			$row = array();
			$row['id'] = $claimRule->claimRuleId;
			$row['data'] = array();
			$row['data'][] = '';
			$row['data'][] = '';
			$row['data'][] = '';
			$row['data'][] = '';
			$row['userdata']['type'] = $claimRule->type;
			$row['userdata']['operator'] = $claimRule->operator;
			$row['userdata']['code'] = $claimRule->code;
			$row['userdata']['value'] = $claimRule->value;
			$row['userdata']['operand'] = $claimRule->operand;
			$rows[] = $row;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

}
