<?php
/*****************************************************************************
*       TeamManagerController.php
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
 * Team Manager controller
 */
class TeamManagerController extends WebVista_Controller_Action {

	public function indexAction() {
		$this->render();
	}

	public function editAction() {
		$ormId = (int)$this->_getParam("ormId");
		$enumerationId = (int)$this->_getParam("enumerationId");
		$this->view->enumerationId = $enumerationId;
		$enumerationsClosure = new EnumerationsClosure();
		$isRole = false;
		$depth = (int)$enumerationsClosure->getDepthById($enumerationId);
		if ($depth > 1) {
			$isRole = true;
		}
		if (!$isRole) {
			if ($depth === 0) {
				$this->view->message = __('Only team member role entries can be edited');
			}
			else {
				$this->view->message = __('There is nothing to edit on the team definition, add roles beneath it to link users to the team');
			}
		}
		else {
			$enumeration = new Enumeration();
			$enumeration->enumerationId = $enumerationId;
			$enumeration->populate();

			$enumerationsClosure = new EnumerationsClosure();
			$parentId = $enumerationsClosure->getParentById($enumerationId);
			$enumerationIterator = $enumerationsClosure->getAllDescendants($parentId,1);
			$this->view->roleList = $enumerationIterator;

			$hasUserParent = false;
			// check if there is a non-user parent
			$depth = $enumerationsClosure->getDepthById($parentId);
			if ($depth > 1) {
				$hasUserParent = true;
			}
			$this->view->hasUserParent = $hasUserParent;

			$teamMember = new TeamMember();
			$teamMember->teamMemberId = $ormId;
			$teamMember->populate();
			$this->view->teamMember = $teamMember;

			$user = new User();
			$user->populateWithPersonId($teamMember->personId);
			$name = array();
			$name[$user->person_id] = '';
			if (strlen($user->username) > 0) {
				$name[$user->person_id] = $user->last_name . ', ' . $user->first_name . ' ' . substr($user->middle_name,0,1) . ' (' . $user->username .")";
			}
			$this->view->defaultUser = $name;
		}
		$this->render();
	}

	public function processEditAction() {
		$enumerationId = (int)$this->_getParam("enumerationId");
		$params = $this->_getParam("team");
		$teamMemberId = (int)$params['teamMemberId'];
		$teamMember = new TeamMember();
		if ($teamMemberId !== 0) {
			$teamMember->teamMemberId = $teamMemberId;
			$teamMember->populate();
		}
		$teamMember->populateWithArray($params);
		$teamMember->persist();
		if ($teamMemberId === 0 && $enumerationId !== 0) {
			$enumeration = new Enumeration();
			$enumeration->enumerationId = $enumerationId;
			$enumeration->populate();
			$enumeration->ormId = $teamMember->teamMemberId;
			$enumeration->persist();
		}

		$data = array();
		$data['msg'] = __("Record saved successfully");
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function userSelectAutoCompleteAction() {
		$match = $this->_getParam("patientSelect");
		$match = preg_replace('/[^a-zA-Z-0-9]/','',$match);
		$matches = array();
		if (!strlen($match) > 0) $this->_helper->autoCompleteDojo($matches);
		$db = Zend_Registry::get('dbAdapter');
		$usrSelect = $db->select()
				->from('user')
				->joinLeftUsing('provider','person_id')
				->joinLeftUsing('person','person_id')
				->where('person.last_name like ' . $db->quote($match.'%'))
				->orWhere('person.first_name like ' . $db->quote($match.'%'))
				->orWhere('user.username like ' . $db->quote($match.'%'))
				->order('person.last_name DESC')
				->order('person.first_name DESC')
				->limit(50);
		//trigger_error($usrSelect->__toString(),E_USER_NOTICE);
		foreach($db->query($usrSelect)->fetchAll() as $row) {
			$matches[$row['person_id']] = $row['last_name'] . ', ' . $row['first_name'] . ' ' . substr($row['middle_name'],0,1) . ' (' . $row['username'] .")";
		}
		$this->_helper->autoCompleteDojo($matches);
	}

	public function selectAction() {
		$patientId = (int)$this->_getParam("patientId");
		$patient = new Patient();
		$patient->personId = $patientId;
		$patient->populate();
		$patient->person->populate();
		$hasTeam = false;
		if (strlen($patient->teamId) > 0) {
			$hasTeam = true;
		}
		$name = TeamMember::ENUM_PARENT_NAME;
		$enumeration = new Enumeration();
		$enumeration->populateByUniqueName($name);

		$enumerationsClosure = new EnumerationsClosure();
		$rowset = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);

		$teamList = array(''=>'');
		//trigger_error('sql: '.$dbSelect->__toString(),E_USER_NOTICE);
		$patientEnumerationId = 0;
		foreach ($rowset as $row) {
			if ($patient->teamId == $row->key) {
				$patientEnumerationId = $row->enumerationId;
			}
			$teamList[$row->key] = $row->name;
		}

		$this->view->teamList = $teamList;
		$this->view->hasTeam = $hasTeam;
		//$this->view->hasTeam = true;
		$this->view->patient = $patient;

		$teamMemberList = '';
		if ($patientEnumerationId !== 0) {
			$teamMemberList = TeamMember::generateTeamTree($patientEnumerationId);
		}

		$this->view->teamMemberList = $teamMemberList;

		$this->render();
	}

	public function processSelectAction() {
		$teamId = preg_replace('/[^0-9_a-z-\.]/i','',$this->_getParam('teamId',''));
		$patientId = (int)$this->_getParam("patientId");
		$patient = new Patient();
		$patient->personId = $patientId;
		$patient->populate();
		$patient->teamId = $teamId;
		$patient->persist();
		$data = array();
		$data['msg'] = __("Record saved successfully");
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function listTeamJsonAction() {

		$this->_helper->autoCompleteDojo(array());

		$patientId = (int)$this->_getParam("patientId");
		$patient = new Patient();
		$patient->personId = $patientId;
		$patient->populate();
		$patient->person->populate();
		$db = Zend_Registry::get("dbAdapter");
		$dbSelect = $db->select()
			       ->from("patient")
			       ->where("teamId = ?",$patient->teamId);
		$patientIterator = $patient->getIterator($dbSelect);
		foreach ($patientIterator as $pat) {
			$tmp = array();
			$tmp['id'] = $pat->person_id;
			$tmp['data'][] = $pat->person->getDisplayName();
			$tmp['data'][] = $pat->email;
			$tmp['data'][] = $pat->phone_number;
			$rows[] = $tmp;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function getExternalMenuAction() {
		header('Content-Type: application/xml;');
		$this->render();
	}

	public function listExternalTeamAction() {
		$patientId = (int)$this->_getParam('patientId');
		$rows = array();
		$externalTeamMemberIterator = new ExternalTeamMemberIterator();
		$externalTeamMemberIterator->setFilters(array('personId'=>$patientId));
		foreach ($externalTeamMemberIterator as $team) {
			$tmp = array();
			$tmp['id'] = $team->externalTeamMemberId;
			$tmp['data'][] = $team->practice;
			$tmp['data'][] = $team->provider;
			$tmp['data'][] = $team->role;
			$tmp['data'][] = $team->phone;
			$tmp['data'][] = $team->fax;
			$tmp['data'][] = $team->active;
			$rows[] = $tmp;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function processEditExternalByFieldAction() {
		$personId = (int)$this->_getParam('personId');
		$id = (int)$this->_getParam('id');
		$field = $this->_getParam('field');
		$value = $this->_getParam('value');

		$obj = new ExternalTeamMember();
		$obj->personId = $personId;

		$retVal = false;
		if (in_array($field,$obj->ormFields())) {
			if ($id > 0) {
				$obj->externalTeamMemberId = $id;
				$obj->populate();
			}
			$obj->$field = $value;
			$obj->persist();
			$retVal = true;
		}

		if ($retVal) {
			$data = true;
		}
		else {
			$data = array('error' => __('There was an error attempting to update the selected record.'));
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processDeleteExternalAction() {
		$personId = (int)$this->_getParam('personId');
		$id = (int)$this->_getParam('id');

		$retVal = false;
		if ($id > 0) {
			$obj = new ExternalTeamMember();
			$obj->externalTeamMemberId = $id;
			$obj->setPersistMode(WebVista_Model_ORM::DELETE);
			$obj->persist();
			$retVal = true;
		}
		if ($retVal) {
			$data = true;
		}
		else {
			$data = array('error' => __('There was an error attempting to delete the selected record.'));
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

}

