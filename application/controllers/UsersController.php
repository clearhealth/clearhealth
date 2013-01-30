<?php
/*****************************************************************************
*       UsersController.php
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


class UsersController extends WebVista_Controller_Action {

	protected $user;
	protected $xmlPreferences = null;

	public function init() {
		$auth = Zend_Auth::getInstance();
		$user = new User();
		$user->personId = (int)$auth->getIdentity()->personId;
		$user->populateWithPersonId();
		if (strlen($user->preferences) > 0) {
			$this->xmlPreferences = new SimpleXMLElement($user->preferences);
		}
		$this->user = $user;
	}

	public function preferencesAction() {
		$mainTabs = Menu::getMainTabs($this->view->baseUrl);
		$data = array();
		$tabs = array();
		foreach ($mainTabs as $key=>$value) {
			$tabs[$key] = $key;
		}
		$this->view->tabs = $tabs;
		$visibleTabs = array();
		$defaultTab = '';
		$currentLocation = '';
		if ($this->xmlPreferences !== null) {
			foreach ($this->xmlPreferences->tabs as $tab) {
				$tab = (string)$tab;
				$visibleTabs[$tab] = $tab;
			}
			$defaultTab = (string)$this->xmlPreferences->defaultTab;
			$currentLocation = (string)$this->xmlPreferences->currentLocation;
		}
		$data['tabs'] = $visibleTabs;
		$data['defaultTab'] = $defaultTab;
		$data['currentLocation'] = $currentLocation;
		$this->view->data = $data;

		$facilityIterator = new FacilityIterator();
		$facilityIterator->setFilter(array('Practice','Building', 'Room'));
		$facilities = array();
		foreach($facilityIterator as $facility) {
			$name = $facility['Practice']->name.'->'.$facility['Building']->name.'->'.$facility['Room']->name;
			$facilities[$facility['Room']->roomId] = $name;
		}
		$this->view->facilities = $facilities;
		$this->render();
	}

	public function processPreferencesAction() {
		$params = $this->_getParam('usersPreferences');

		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><preferences/>');
		if (isset($params['tabs'])) {
			$tabs = $params['tabs'];
			if (!is_array($tabs)) {
				$tabs = array($tabs);
			}
			foreach ($tabs as $tab) {
				$xml->addChild('tabs',$tab);
			}
		}
		if (isset($params['defaultTab'])) {
			$xml->addChild('defaultTab',$params['defaultTab']);
		}
		if (isset($params['currentLocation'])) {
			$xml->addChild('currentLocation',$params['currentLocation']);
		}
		$this->user->preferences = $xml->asXML();
		$this->user->_cascadePersist = false;
		$this->user->persist();

		$data = true;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function autoCompleteAction() {
        	$match = $this->_getParam('name');
		$match = preg_replace('/[^a-zA-Z-0-9]/','',$match);
		$matches = array();
		if (!strlen($match) > 0) $this->_helper->autoCompleteDojo($matches);
		$db = Zend_Registry::get('dbAdapter');
		$match = $db->quote($match.'%');
		$patSelect = $db->select()
				->from('user')
				->joinLeftUsing('person','person_id')
				->joinLeftUsing('provider','person_id')
				->where('person.last_name LIKE '.$match)
				->orWhere('person.first_name LIKE '.$match)
				->orWhere('user.username LIKE '.$match)
				->order('person.last_name DESC')
				->order('person.first_name DESC');
				//->limit(50);

		$dbStmt = $db->query($patSelect);

		$excludeColumns = array();
		$excludeColumns['provider'] = array('person_id');
		$excludeColumns['user'] = array('person_id');

		$data = array();
		$offset = 0;
		$rowCount = $dbStmt->rowCount();
		for ($offset = 0; $offset < $rowCount; $offset++) {
			//$row = $dbStmt->fetch(null,null,$offset);
			$row = $dbStmt->fetch(PDO::FETCH_NUM,null,$offset);
			$columnMeta = array();
			$columnMetaCount = 0;
			if ($offset == 0) {
				for($i = 0, $ctr = count($row); $i < $ctr; $i++) {
					$columnMeta[$i] = $dbStmt->getColumnMeta($i);
					$columnMetaCount++;
				}
			}
			$tmp = array();
			for ($col = 0; $col < $columnMetaCount; $col++) {
				if (isset($excludeColumns[$columnMeta[$col]['table']]) && in_array($columnMeta[$col]['name'],$excludeColumns[$columnMeta[$col]['table']])) {
					continue;
				}
				$tmp[$columnMeta[$col]['name']] = $row[$col];
			}
			if (isset($tmp['person_id'])) {
				$data[] = $tmp;
			}
		}
		$dbStmt->closeCursor();

		foreach ($data as $row) {
			$matches[$row['person_id']] = $row['last_name'] . ', ' . $row['first_name'] . ' ' . substr($row['middle_name'],0,1) . ' (' . $row['username'] .")"; 
		}

        	$this->_helper->autoCompleteDojo($matches);
	}

}
