<?php
/*****************************************************************************
*       PatientListController.php
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


class PatientListController extends WebVista_Controller_Action {

	protected $_session;

	public function init() {
		$this->_session = new Zend_Session_Namespace(__CLASS__);
	}

	public function indexAction() {
		if (!isset($this->_session->filters)) {
			$this->_session->filters = array();
			$this->_session->filters['problem'] = array();
			$this->_session->filters['medication'] = array();
			$this->_session->filters['demographics'] = array();
			$this->_session->filters['labTestResults'] = array();
			$this->_session->filters['allergies'] = array();
		}
		$this->render();
	}

	public function listAction() {
		$rows = array();
		$problem = array();
		$medication = array();
		$demographics = array();
		$labTestResults = array();
		$allergies = array();
		foreach ($this->_session->filters as $key=>$filters) {
			if ($key == 'problem') {
				$$key = Patient::listProblems($filters);
			}
			else if ($key == 'medication') {
				$$key = Patient::listMedications($filters);
			}
			else if ($key == 'demographics') {
				$$key = Patient::listDemographics($filters);
			}
			else if ($key == 'labTestResults') {
				$$key = Patient::listLabTestResults($filters);
			}
			else if ($key == 'allergies') {
				$$key = Patient::listAllergies($filters);
			}
		}

		// intersect only for those that have filters
		$tmpArray = array(); // holds a list of rows that needs to get the intersections
		foreach ($this->_session->filters as $key=>$filters) {
			if (!$filters || !isset($$key)) continue;
			$k = $key;
			if ($k == 'problem' || $k == 'medication') $k .= 's';
			$tmpArray[$k] = $$key;
		}

		$patientList = null;
		foreach ($tmpArray as $key=>$value) {
			if ($patientList === null) {
				$patientList = $value;
				continue;
			}
			$tmp = $patientList;
			$patientList = array();
			foreach ($tmp as $id=>$val) {
				if (!isset($value[$id])) continue;
				$val[$key] = $value[$id][$key];
				$patientList[$id] = $val;
			}
		}
		if ($patientList === null) $patientList = array();
		$this->_session->patientList = $patientList;
		foreach ($patientList as $key=>$value) {
			$row = array();
			$row['id'] = $key;
			$row['data'][] = $value['MRN'];
			$row['data'][] = $value['lastName'];
			$row['data'][] = $value['firstName'];
			$row['data'][] = $value['middleName'];
			$row['data'][] = isset($value['problems'])?implode('<br />',$value['problems']):'';
			$row['data'][] = isset($value['medications'])?implode('<br />',$value['medications']):'';
			$row['data'][] = isset($value['demographics'])?implode('<br />',$value['demographics']):'';
			$row['data'][] = isset($value['labTestResults'])?implode('<br />',$value['labTestResults']):'';
			$row['data'][] = isset($value['allergies'])?implode('<br />',$value['allergies']):'';
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

	public function filterProblemAction() {
		$this->render();
	}

	public function processFilterProblemAction() {
		$params = $this->_getParam('filters');
		$data = true;
		if (!is_array($params)) {
			$params = array();
			$data = false;
		}
		$this->_session->filters['problem'] = $params;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function listFilterProblemAction() {
		$this->listFilters('problem');
	}

	public function filterMedicationAction() {
		$this->view->chBaseMed24Url = Zend_Registry::get('config')->healthcloud->CHMED->chBaseMed24Url;
		$this->view->chBaseMed24DetailUrl = Zend_Registry::get('config')->healthcloud->CHMED->chBaseMed24DetailUrl;
		$operators = array(''=>'');
		foreach (Claim::balanceOperators() as $key=>$value) {
			$operators[$key] = $value;
		}
		$this->view->operators = $operators;
		$this->render();
	}

	public function processFilterMedicationAction() {
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
		$this->_session->filters['medication'] = $medications;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function listFilterMedicationAction() {
		$id = 'medication';
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
			asort($options);
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

	protected function export(Array $ids) {
		$filename = 'pl_'.uniqid('').'.csv';
		$headers = array(
			'MRN'=>'MRN',
			'lastName'=>'Last Name',
			'firstName'=>'First Name',
			'middleName'=>'Middle Name',
			'problems'=>'Problems',
			'medications'=>'Medications',
			'demographics'=>'Demographics',
			'labTestResults'=>'LabTest Results',
			'allergies'=>'Allergies',
		);
		$patientList = array(implode(',',$headers));
		foreach ($ids as $id) {
			if (!isset($this->_session->patientList[$id])) continue;
			$patient = $this->_session->patientList[$id];
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
		$filename = 'pl_'.uniqid('').'.csv';
		return array('filename'=>$filename,'data'=>$patientList);
	}

	protected function export2PublicHealth($ids) {
		$data = array();
		$data[] = 'FHS|^~\&';
		$data[] = 'BHS|^~\&';
		$ctr = count($ids);
		for ($i = 0; $i < $ctr; $i++) {
			$id = (int)$ids[$i];
			if (!isset($this->_session->patientList[$id])) continue;
			$problemList = isset($this->_session->patientList[$id]['problemList'])?$this->_session->patientList[$id]['problemList']:array();
			$patient = new Patient();
			$patient->personId = $id;
			$patient->populate();
			$person = $patient->person;
			$dateTime = date('YmdHi');
			$messageDateTime = date('YmdHiO');
			$data[] = 'MSH|^~\&|CLEARHEALTH||||'.$dateTime.'||ADT^A04|'.$messageDateTime.'|P|2.3.1';
			$dateOfOnset = isset($problemList[0]['dateOfOnset'])?$problemList[0]['dateOfOnset']:date('YmdHis');
			$data[] = 'EVN||'.date('YmdHi',strtotime($dateOfOnset));
			// Address
			$address = new Address();
			$address->personId = $id;
			$addressIterator = $address->getIteratorByPersonId();
			foreach ($addressIterator as $address) {
				break; // retrieves the top address
			}
			// Telecom
			$phone = null;
			$phoneNumber = new PhoneNumber();
			$phoneNumber->personId = $id;
			foreach ($phoneNumber->getPhoneNumbers(false) as $phone) {
				break; // retrieves the top phone
			}
			$telecom = '';
			if ($phone && strlen($phone['number']) > 0) {
				$telecom = $phone['number'];
			}
			$data[] = 'PID|1||'.$patient->recordNumber.'||'.strtoupper($person->lastName).'^'.strtoupper($person->firstName).'^'.strtoupper($person->middleName).'||'.date('Ymd',strtotime($person->dateOfBirth)).'|'.$person->gender.'||U|'.$address->line1.'^'.$address->line2.'^'.$address->city.'^'.$address->state.'^'.$address->zipCode.'^US||'.$telecom;
			$visit = new Visit();
			$visit->patientId = $id;
			$visit->populateLatestVisit();
			$data[] = 'PV1|1|O||R||||||||||||||||||||||||||||||||||||||||'.date('YmdHis',strtotime($visit->dateOfTreatment));
			foreach ($problemList as $key=>$problem) {
				$data[] = 'DG1|'.($key+1).'||'.$problem['code'].'^'.$problem['code'].' '.$problem['codeTextShort'].'^I9C|||F|||||||||1';
			}
		}
		$data[] = 'BTS|'.$ctr;
		$data[] = 'FTS|1';
		$filename = 'ph_'.uniqid('').'.er7';
		return array('filename'=>$filename,'data'=>$data);
	}

	public function exportAction() {
		$ids = explode(',',$this->_getParam('ids'));
		$pubHealth = (int)$this->_getParam('pubHealth');
		if ($pubHealth) {
			if ($this->_session->filters['problem']) {
				$result = $this->export2PublicHealth($ids);
			}
			else {
				$data = array('error'=>'At least one problem filter is needed for public health export');
			}
		}
		else {
			$result = $this->export($ids);
		}
		if (!isset($data)) {
			$data = array();
			$filename = $result['filename'];
			$contents = implode("\r\n",$result['data']);

			$filePath = '/tmp/'.$filename;
			if (file_put_contents($filePath,$contents) !== false) {
				$data = array('filename'=>$filename);
			}
			else {
				$error = 'Failed to create file: '.$filename;
				trigger_error($error);
				$data = array('error',$error);
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function downloadExportedAction() {
		$filename = $this->_getParam('filename','');
		$filePath = '/tmp/'.$filename;
		if (!strlen($filename) > 0) {
			$contents = 'Invalid filename.';
		}
		else if (!file_exists($filePath)) {
			$contents = "File '$filename' does not exists.";
			trigger_error($contents);
		}
		else {
			$contents = file_get_contents($filePath);
		}
		$this->view->contents = $contents;
		$this->getResponse()->setHeader('Content-Type','application/binary');
		$this->getResponse()->setHeader('Content-Disposition','attachment; filename="'.$filename.'"');
		$this->render();
	}

}
