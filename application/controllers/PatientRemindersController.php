<?php
/*****************************************************************************
*       PatientRemindersController.php
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


class PatientRemindersController extends WebVista_Controller_Action {

	protected $_session;

	public function init() {
		$this->_session = new Zend_Session_Namespace(__CLASS__);
	}

	public function indexAction() {
		if (!isset($this->_session->filters)) {
			$this->_session->filters = array();
			$this->_session->filters['problems'] = array();
			$this->_session->filters['medications'] = array();
			$this->_session->filters['demographics'] = array();
			$this->_session->filters['labTestResults'] = array();
			$this->_session->filters['allergies'] = array();
			$this->_session->filters['hsa'] = array();
		}
		$this->render();
	}

	protected function getAddress($addressId) {
		// Address
		$address = 'Privacy';
		$addressId = (int)$addressId;
		if ($addressId > 0) {
			$addr = new Address();
			$addr->addressId = $addressId;
			$addr->populate();
			if ($addr->type == 'REMINDERS') {
				$address = $addr->line1.' '.$addr->line2.' '.$addr->city.', '.$addr->state.' '.$addr->zipCode;
			}
		}
		return $address;
	}

	protected function getPhone($phoneId) {
		// Phone
		$phone = 'Privacy';
		$numberId = (int)$phoneId;
		if ($numberId > 0) {
			$phoneNumber = new PhoneNumber();
			$phoneNumber->numberId = $numberId;
			$phoneNumber->populate();
			if ($phoneNumber->type == 'REMINDERS') {
				$phone = $phoneNumber->number;
			}
		}
		return $phone;
	}

	public function listAction() {
		$rows = array();
		$patientList = array();
		$problems = array();
		$medications = array();
		$demographics = array();
		$labTestResults = array();
		$allergies = array();
		$hsa = array();
		foreach ($this->_session->filters as $key=>$filters) {
			if ($key == 'problems') {
				$problems = Patient::listProblems($filters);
			}
			else if ($key == 'medications') {
				$medications = Patient::listMedications($filters);
			}
			else if ($key == 'demographics') {
				$filters['reminders'] = true;
				$demographics = Patient::listDemographics($filters);
			}
			else if ($key == 'labTestResults') {
				$labTestResults = Patient::listLabTestResults($filters);
			}
			else if ($key == 'allergies') {
				$allergies = Patient::listAllergies($filters);
			}
			else if ($key == 'hsa') {
				$hsa = Patient::listHSA($filters);
			}
		}

		$tmpArray = array('demographics'=>$demographics); // holds a list of rows that needs to get the intersections
		foreach ($this->_session->filters as $key=>$filters) {
			if (!isset($$key) || !is_array($$key)) continue;
			$tmpArray[$key] = $$key;
		}

		$patientList = array();
		foreach ($tmpArray as $key=>$value) {
			foreach ($value as $id=>$val) {
				if (isset($patientList[$id])) continue;
				$patientList[$id] = $val;
			}
		}
		if ($patientList === null) $patientList = array();
		$this->_session->patientList = $patientList;
		foreach ($patientList as $key=>$value) {
			$addressId = isset($value['addressId'])?(int)$value['addressId']:0;
			$address = $this->getAddress($addressId);
			$numberId = isset($value['numberId'])?(int)$value['numberId']:0;
			$phone = $this->getPhone($numberId);
			$row = array();
			$row['id'] = $key;
			$row['data'][] = $value['MRN'];
			$row['data'][] = $value['lastName'];
			$row['data'][] = $value['firstName'];
			$row['data'][] = $value['middleName'];
			$row['data'][] = $address;
			$row['data'][] = $phone;
			$row['data'][] = isset($value['problems'])?implode('<br />',$value['problems']):'';
			$row['data'][] = isset($value['medications'])?implode('<br />',$value['medications']):'';
			$row['data'][] = isset($value['demographics'])?implode('<br />',$value['demographics']):'';
			$row['data'][] = isset($value['labTestResults'])?implode('<br />',$value['labTestResults']):'';
			$row['data'][] = isset($value['allergies'])?implode('<br />',$value['allergies']):'';
			$row['data'][] = isset($value['hsa'])?implode('<br />',$value['hsa']):'';
			$rows[] = $row;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	protected function listFilters($id) {
		$rows = array();
		$filters = isset($this->_session->filters[$id])?$this->_session->filters[$id]:array();
		foreach ($filters as $key=>$value) {
			$row = array();
			$row['id'] = $key;
			$row['data'][] = $value;
			$rows[] = $row;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function filterProblemsAction() {
		$this->render();
	}

	public function processFilterProblemsAction() {
		$params = $this->_getParam('filters');
		$data = true;
		if (!is_array($params)) {
			$params = array();
			$data = false;
		}
		$this->_session->filters['problems'] = $params;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function listFilterProblemsAction() {
		$this->listFilters('problems');
	}

	public function filterMedicationsAction() {
		$this->view->chBaseMed24Url = Zend_Registry::get('config')->healthcloud->CHMED->chBaseMed24Url;
		$this->view->chBaseMed24DetailUrl = Zend_Registry::get('config')->healthcloud->CHMED->chBaseMed24DetailUrl;
		$operators = array(''=>'');
		foreach (Claim::balanceOperators() as $key=>$value) {
			$operators[$key] = $value;
		}
		$this->view->operators = $operators;
		$this->render();
	}

	public function processFilterMedicationsAction() {
		$params = $this->_getParam('filters');
		$data = true;
		if (!is_array($params)) {
			$params = array();
			$data = false;
		}
		$medications = array();
		foreach ($params as $key=>$value) {
			$value['operand1'] = date('Y-m-d',strtotime($value['operand1']));
			if ($value['operator'] == 'between') $value['operand2'] = date('Y-m-d',strtotime($value['operand2']));
			$medications[$key] = $value;
		}
		$this->_session->filters['medications'] = $medications;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function listFilterMedicationsAction() {
		$id = 'medications';
		$rows = array();
		$filters = isset($this->_session->filters[$id])?$this->_session->filters[$id]:array();
		foreach ($filters as $key=>$value) {
			$row = array();
			$row['id'] = $key;
			$row['data'][] = $value['NOT'];
			$row['data'][] = $value['medication'];
			$row['data'][] = $value['operator'];
			$row['data'][] = $value['operand1'];
			$row['data'][] = $value['operand2'];
			$rows[] = $row;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function filterDemographicsAction() {
		$psd = new PatientStatisticsDefinition();
		$psdIterator = $psd->getAllActive();
		$demographics = array(
			'age'=>array('name'=>'Age','type'=>''),
			'gender'=>array('name'=>'Gender','type'=>PatientStatisticsDefinition::TYPE_ENUM,'options'=>Enumeration::getEnumArray('Gender','key')),
			'marital_status'=>array('name'=>'Marital Status','type'=>PatientStatisticsDefinition::TYPE_ENUM,'options'=>Enumeration::getEnumArray('Marital Status','key')),
		);
		foreach ($psdIterator as $row) {
			$tmp = array();
			$tmp['name'] = GrowthChartBase::prettyName($row->name);
			$options = array();
			if ($row->type == PatientStatisticsDefinition::TYPE_ENUM) {
				$enumerationClosure = new EnumerationClosure();
				$options = $enumerationClosure->generatePathsKeyName($row->value);
			}
			$tmp['type'] = $row->type;
			$tmp['options'] = $options;
			$demographics[$row->name] = $tmp;
		}
		$this->view->demographics = $demographics;
		$this->view->filters = $this->_session->filters['demographics'];
		$operators = array(''=>'');
		foreach (Claim::balanceOperators() as $key=>$value) {
			$operators[$key] = $value;
		}
		$this->view->operators = $operators;
		$this->render();
	}

	public function processFilterDemographicsAction() {
		$params = $this->_getParam('filters');
		$data = false;
		$demographics = array();
		if (is_array($params)) {
			foreach ($params as $key=>$value) {
				if (!isset($value['enabled']) || $value['enabled'] != 'on') continue;
				$demographics[$key] = $value;
			}
			$data = true;
		}
		$this->_session->filters['demographics'] = $demographics;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function filterLabTestResultsAction() {
		$operators = array(''=>'');
		foreach (Claim::balanceOperators() as $key=>$value) {
			$operators[$key] = $value;
		}
		$this->view->operators = $operators;
		$this->render();
	}

	public function processFilterLabTestResultsAction() {
		$params = $this->_getParam('filters');
		$data = true;
		if (!is_array($params)) {
			$params = array();
			$data = false;
		}
		$this->_session->filters['labTestResults'] = $params;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function listFilterLabTestResultsAction() {
		$id = 'labTestResults';
		$rows = array();
		$filters = isset($this->_session->filters[$id])?$this->_session->filters[$id]:array();
		foreach ($filters as $key=>$value) {
			$row = array();
			$row['id'] = $key;
			$row['data'][] = $value['labTest'];
			$row['data'][] = $value['operator'];
			$row['data'][] = $value['operand1'];
			$row['data'][] = $value['operand2'];
			$row['data'][] = $value['unit'];
			$row['data'][] = $value['OR'];
			$rows[] = $row;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function filterAllergiesAction() {
		$this->render();
	}

	public function processFilterAllergiesAction() {
		$params = $this->_getParam('filters');
		$data = true;
		if (!is_array($params)) {
			$params = array();
			$data = false;
		}
		$this->_session->filters['allergies'] = $params;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function listFilterAllergiesAction() {
		$this->listFilters('allergies');
	}

	public function filterHsaAction() {
		$operators = array(''=>'');
		foreach (Claim::balanceOperators() as $key=>$value) {
			$operators[$key] = $value;
		}
		$this->view->operators = $operators;
		$this->render();
	}

	public function processFilterHsaAction() {
		$params = $this->_getParam('filters');
		$data = true;
		if (!is_array($params)) {
			$params = array();
			$data = false;
		}
		$this->_session->filters['hsa'] = $params;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function listFilterHsaAction() {
		$id = 'hsa';
		$rows = array();
		$filters = isset($this->_session->filters[$id])?$this->_session->filters[$id]:array();
		foreach ($filters as $key=>$value) {
			$row = array();
			$row['id'] = $key;
			$row['data'][] = $value['hsa'];
			$row['data'][] = $value['operator'];
			$row['data'][] = $value['operand1'];
			$row['data'][] = $value['operand2'];
			$row['data'][] = $value['OR'];
			$rows[] = $row;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function exportAction() {
		$ids = $this->_getParam('ids');
		$data = array();
		$filename = 'pr_'.uniqid('').'.csv';
		$headers = array(
			'MRN'=>'MRN',
			'lastName'=>'Last Name',
			'firstName'=>'First Name',
			'middleName'=>'Middle Name',
			'addressId'=>'Address',
			'numberId'=>'Phone',
			'problems'=>'Problems',
			'medications'=>'Medications',
			'demographics'=>'Demographics',
			'labTestResults'=>'LabTest Results',
			'allergies'=>'Allergies',
			'hsa'=>'HSA',
		);
		$patientList = array(implode(',',$headers));
		foreach (explode(',',$ids) as $id) {
			if (!isset($this->_session->patientList[$id])) continue;
			$patient = $this->_session->patientList[$id];
			$patient['addressId'] = $this->getAddress($patient['addressId']);
			$patient['numberId'] = $this->getPhone($patient['numberId']);
			$row = array();
			foreach ($headers as $key=>$value) {
				if (!isset($patient[$key])) $patient[$key] = '';
				if (is_array($patient[$key])) {
					$row[] = '"'.implode("\n",$patient[$key]).'"';
				}
				else {
					$row[] = $patient[$key];
				}
			}
			$patientList[] = implode(',',$row);
		}
		$contents = implode("\r\n",$patientList);
		$filePath = '/tmp/'.$filename;
		if (file_put_contents($filePath,$contents) !== false) {
			$data = array('filename'=>$filename);
		}
		else {
			$error = 'Failed to create file: '.$filename;
			trigger_error($error);
			$data = array('error',$error);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

}
