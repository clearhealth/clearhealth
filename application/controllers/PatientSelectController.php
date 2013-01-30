<?php
/*****************************************************************************
*       PatientSelectController.php
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


class PatientSelectController extends WebVista_Controller_Action
{
    protected $_session;

    public function init()
    {
        $this->_session = new Zend_Session_Namespace(__CLASS__);
    }

	public function indexAction() {
		$memcache = Zend_Registry::get('memcache');
		$defaultQuickList = $memcache->get('defaultQuickList');
		if ($defaultQuickList === false) {
			$defaultQuickList = array();
			$defaultQuickList['type'] = 'mostRecent';
			$defaultQuickList['id'] = 'mostRecent';
			$defaultQuickList['text'] = 'Most Recent';
		}
		else {
			$defaultQuickList = unserialize($defaultQuickList);
		}
		$this->view->defaultQuickList = $defaultQuickList;
		$this->view->quickList = $this->_getQuickList();

		$providerIterator = new ProviderIterator();
		$this->view->providerIterator = $providerIterator;

		$name = TeamMember::ENUM_PARENT_NAME;
		$enumeration = new Enumeration();
		$enumeration->populateByEnumerationName($name);
		$enumerationsClosure = new EnumerationsClosure();
		$enumerationIterator = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
		$this->view->teamList = $enumerationIterator;

		$this->render();
	}

	public function processSetDefaultQuicklistAction() {
		$memcache = Zend_Registry::get('memcache');
		$type = $this->_getParam('type');
		$id = $this->_getParam('id');
		$text = $this->_getParam('text');

		$defaultQuickList = array();
		$defaultQuickList['type'] = $type;
		$defaultQuickList['id'] = $id;
		$defaultQuickList['text'] = $text;
		$memcache->set('defaultQuickList',serialize($defaultQuickList));

		$quickList = $memcache->get('quickList');
		if ($quickList !== false) {
			$quickList = unserialize($quickList);
		}
		else {
			$quickList = array();
		}
		$quickList[$type][$id] = $defaultQuickList;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
                $json->direct(true);
	}

	public function listPatientsAction() {
		$type = $this->_getParam('type');
		$rows = array();
		$quickList = $this->_getQuickList();
		if (array_key_exists($type,$quickList)) {
			switch ($type) {
				case 'mostRecent':
					$rows = $this->_getPatientByMostRecent();
					break;
				case 'providers':
					$rows = $this->_getPatientByProvider();
					break;
				case 'team':
					$rows = $this->_getPatientByTeam();
					break;
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($rows);
	}

	protected function _getPatientByProvider() {
		$id = (int)$this->_getParam('id');
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from(array('enc'=>'encounter'))
				->joinLeft(array('pat'=>'patient'),'pat.person_id = enc.patient_id')
				->joinLeft(array('per'=>'person'),'per.person_id = pat.person_id')
				->where('enc.treating_person_id = ?',$id)
				->order('enc.timestamp DESC')
				->order('per.last_name ASC')
				->limit(40);
		$rows = array();
		foreach($db->query($sqlSelect)->fetchAll() as $row) {
			$rows[$row['person_id']] = $row['last_name'] . ', ' . $row['first_name'] . ' ' . substr($row['middle_name'],0,1) . ' #' . $row['record_number']; 
		}
		return $rows;
	}

	protected function _getPatientByTeam() {
		$id = preg_replace('/[^0-9_a-z-\.]/i','',$this->_getParam('id',''));
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from(array('pat'=>'patient'))
				->joinLeft(array('per'=>'person'),'per.person_id = pat.person_id')
				->where('pat.teamId = ?',$id)
				->order('per.last_name ASC')
				->limit(40);
		$rows = array();
		foreach($db->query($sqlSelect)->fetchAll() as $row) {
			$rows[$row['person_id']] = $row['last_name'] . ', ' . $row['first_name'] . ' ' . substr($row['middle_name'],0,1) . ' #' . $row['record_number']; 
		}
		return $rows;
	}

	protected function _getPatientByMostRecent() {
		$memcache = Zend_Registry::get('memcache');
		$mostRecentRaw = $memcache->get('mostRecent');
		if ($mostRecentRaw === false) {
			$mostRecent = array();
		}
		else {
			$mostRecent = unserialize($mostRecentRaw);
		}
		$currentUserId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		if (!array_key_exists($currentUserId,$mostRecent)) {
			$mostRecent[$currentUserId] = array();
		}
		// format: $mostRecent[$currentUserId][$personId] = array('name'=>$name,'teamId'=>$teamId);
		$rows = array();
		$data = array_reverse($mostRecent[$currentUserId],true);
		$ctr = 0;
		foreach ($data as $personId=>$person) {
			$rows[$personId] = $person['name'];
			if (++$ctr >= 40) { // limit 40
				break;
			}
		}
		return $rows;
	}

	protected function _getQuickList() {
		$quickList = array();
		$quickList['providers'] = __('Providers');
		//$quickList['clinics'] = __('Clinics');
		//$quickList['specialties'] = __('Specialties');
		$quickList['team'] = __('Team');
		//$quickList['units'] = __('Units');
		$quickList['mostRecent'] = __('Most Recent');
		return $quickList;
	}

	public function autoCompleteAction() {
        	$match = trim($this->getRequest()->getQuery('patientSelect', ''));
		$match = preg_replace('/[^a-zA-Z-0-9-\.\/\ ]/','',$match);
		$matches = array();
		if (!strlen($match) > 0) $this->_helper->autoCompleteDojo($matches);
		$db = Zend_Registry::get('dbAdapter');
		$patSelect = $db->select()
					->from('patient')
					->joinUsing('person','person_id')
					->orWhere('identifier = ? ', $match)
					->orWhere('date_of_birth = ?',$match)
					->order('person.last_name ASC')
					->order('person.first_name ASC')
					->limit(50);
		if (preg_match('/([a-zA-Z]*)\ ([a-zA-Z].*)/',$match,$nameParts)) { 
			$patSelect->orWhere('first_name like ?', $nameParts[2] .'%');
			$patSelect->where('last_name like ?', $nameParts[1].'%');
		}
		elseif(preg_match('/[0-9]+/',$match)) {
			$patSelect->orWhere('record_number = ? ', (preg_match('/[0-9-\/\.]+/',$match)) ? $match : 'false');
		}
		elseif(preg_match('/[0-9-\/\.]+[\.\\-]+[0-9]+/',$match)) {
			$patSelect->orWhere('date_of_birth = ?', $match);
		}
		else {
			$patSelect->orWhere('last_name like ?', $match . "%");
		}
		$patSelect->where("person.inactive = 0");
		trigger_error($patSelect->__toString(),E_USER_NOTICE);
		//var_dump($db->query($patSelect)->fetchAll());exit;
		foreach($db->query($patSelect)->fetchAll() as $row) {
			$matches[$row['person_id']] = $row['last_name'] . ', ' . $row['first_name'] . ' ' . substr($row['middle_name'],0,1) . ' #' . $row['record_number']; 
		}
		//var_dump($matches);exit;
		//$matches = array("name1" => $match, "name2" =>"value3");
        	$this->_helper->autoCompleteDojo($matches);
	}

	public function detailAction() {
        	$personId = (int)$this->_getParam('personId');
		if (!$personId > 0) $this->_helper->autoCompleteDojo($personId);
		$db = Zend_Registry::get('dbAdapter');
		$patient = new Patient();
		$patient->personId = (int) $personId;
		$patient->populate();
		$patient->person->populate();
		$outputArray = $patient->toArray();
		$outputArray['displayGender'] = $patient->displayGender;
		$outputArray['age'] = $patient->age;
		$acj = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $acj->suppressExit = true;
                $acj->direct($outputArray);
	}

	public function getMostRecentVisitAction() {
		$memcache = Zend_Registry::get('memcache');
		$personId = (int)$this->_getParam('personId');

		$mostRecentVisit = array();
		$mostRecentVisitRaw = $memcache->get('mostRecentVisit');
		if ($mostRecentVisitRaw !== false) {
			$mostRecentVisit = unserialize($mostRecentVisitRaw);
		}
		$recentVisit = 0;
		if (isset($mostRecentVisit[$personId])) {
			$recentVisit = $mostRecentVisit[$personId];
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
                $json->direct($recentVisit);
	}

	public function processMostRecentVisitAction() {
		$memcache = Zend_Registry::get('memcache');
		$visitId = (int)$this->_getParam('visitId');
		$personId = (int)$this->_getParam('personId');

		$mostRecentVisit = array();
		$mostRecentVisitRaw = $memcache->get('mostRecentVisit');
		if ($mostRecentVisitRaw !== false) {
			$mostRecentVisit = unserialize($mostRecentVisitRaw);
		}
		$mostRecentVisit[$personId] = $visitId;
		$memcache->set('mostRecentVisit',serialize($mostRecentVisit));
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
                $json->direct(true);
	}

}
