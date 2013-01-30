<?php
/*****************************************************************************
*       ClaimsController.php
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


class ClaimsController extends WebVista_Controller_Action {

	protected $_session;

	public function init() {
		$this->_session = new Zend_Session_Namespace(__CLASS__);
	}

	public function indexAction() {
		if (!isset($this->_session->filters)) {
			$filters = array();
			$filters['DOSStart'] = date('Y-m-d',strtotime('-1 week'));
			$filters['DOSEnd'] = date('Y-m-d');
			$filters['facilities'] = array();
			$filters['payers'] = array();
			$tmp = array('active'=>0,'operator'=>'=','operand1'=>'','operand2'=>'');
			$filters['total'] = $tmp;
			$filters['billed'] = $tmp;
			$filters['paid'] = $tmp;
			$filters['writeoff'] = $tmp;
			$filters['balance'] = $tmp;
			$filters['mrn'] = '';
			$this->_session->filters = $filters;
		}
		$this->view->filters = $this->_session->filters;

		$this->view->options = Claim::listOptions();
		$this->render();
	}

	public function advancedFiltersAction() {
		$this->view->balanceOperators = Claim::balanceOperators();
		$filters = $this->_session->filters;
		if (!isset($filters['total'])) {
			$filters['total'] = array('active'=>0,'operator'=>'=','operand1'=>'','operand2'=>'');
		}
		if (!isset($filters['billed'])) $filters['billed'] = '';
		if (!isset($filters['paid'])) $filters['paid'] = '';
		if (!isset($filters['writeoff'])) $filters['writeoff'] = '';
		if (!isset($filters['openClosed'])) $filters['openClosed'] = 2;
		if (!isset($filters['mrn'])) $filters['mrn'] = '';
		$this->view->filters = $filters;
		$facilityIterator = new FacilityIterator();
		$facilityIterator->setFilter(array('Practice','Building','Room'));
		$facilities = array();
		foreach($facilityIterator as $facility) {
			$key = $facility['Practice']->practiceId.'_'.$facility['Building']->buildingId.'_'.$facility['Room']->roomId;
			$name = $facility['Practice']->name.'->'.$facility['Building']->name.'->'.$facility['Room']->name;
			$facilities[$key] = $name;
		}
		$this->view->facilities = $facilities;
		$this->render();
	}

	public function setFiltersAction() {
		$params = $this->_getParam('filters');
		if (is_array($params)) {
			$filters = $this->_session->filters;
			if (!isset($params['batchHistoryId']) && isset($filters['batchHistoryId'])) unset($filters['batchHistoryId']);
			foreach ($params as $key=>$value) {
				$filters[$key] = $value;
			}
			$this->_session->filters = $filters;
		}
		$data = true;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function getContextMenuAction() {
		$this->view->type = $this->_getParam('type','');
		header('Content-Type: application/xml;');
		$this->render('get-context-menu');
	}

	public function setBatchVisitsAction() {
		$type = $this->_getParam('type');
		$ids = $this->_getParam('ids');
		$data = false;
		if (strlen($ids) > 0) {
			foreach (explode(',',$ids) as $id) {
				$visit = new Visit();
				$visit->visitId = (int)$id;
				if (!$visit->populate()) continue;
				if ($type == 'open') {
					$visit->closed = 0;
				}
				else if ($type == 'closed') {
					$visit->closed = 1;
				}
				else {
					continue;
				}
				$visit->persist();
				$data = true;
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function listAction() {
		$posStart = $this->_getParam('posStart');
		if ($posStart < 0) {
			$msg = 'Invalid request';
			trigger_error($msg);
			throw new Exception($msg);
		}
		$claimList = isset($this->_session->claimList)?$this->_session->claimList:array();
		$sessions = $this->_session->filters;
		if (!isset($sessions['DOSStart'])) {
			$sessions['DOSStart'] = date('Y-m-d',strtotime('-1 week'));
		}
		if (!isset($sessions['DOSEnd'])) {
			$sessions['DOSEnd'] = date('Y-m-d');
		}
		$filters = array();
		if (isset($sessions['batchHistoryId'])) {
			$claimFile = new ClaimFile();
			$claimFile->claimFileId = (int)$sessions['batchHistoryId'];
			$claimFile->populate();
			$filters['batchHistoryId'] = $claimFile->claimIds;
		}
		else {
			$filters['dateRange'] = $sessions['DOSStart'].':'.$sessions['DOSEnd'];
			if (isset($sessions['facilities']) && count($sessions['facilities']) > 0) { // practiceId_buildingId_roomId
				foreach ($sessions['facilities'] as $key=>$value) {
					if (!$value) continue;
					$x = explode('_',$key);
					$practiceId = $x[0];
					$buildingId = $x[1];
					$roomId = $x[2];
					if (!isset($filters['facilities'])) $filters['facilities'] = array();
					$filters['facilities'][] = array('practice'=>$practiceId,'building'=>$buildingId,'room'=>$roomId);
				}
			}
			if (isset($sessions['payers'])&& count($sessions['payers']) > 0) {
				foreach ($sessions['payers'] as $key=>$value) {
					if (!$value) continue;
					if (!isset($filters['payers'])) $filters['payers'] = array();
					$filters['payers'][] = $key;
				}
			}
			$filters['total'] = isset($sessions['total'])?$sessions['total']:0;
			$filters['billed'] = isset($sessions['billed'])?$sessions['billed']:0;
			$filters['paid'] = isset($sessions['paid'])?$sessions['paid']:0;
			$filters['writeoff'] = isset($sessions['writeoff'])?$sessions['writeoff']:0;
			$filters['balance'] = isset($sessions['balance'])?$sessions['balance']:0;
			$filters['openClosed'] = isset($sessions['openClosed'])?(int)$sessions['openClosed']:2;
		}
		if (isset($sessions['mrn']) && strlen($sessions['mrn']) > 0) $filters['mrn'] = $sessions['mrn'];

		$rows = array();
		$claimIterator = array();
		$visitIterator = new VisitIterator();
		//$filters['void'] = 0; // exclude void from list
		$visitIterator->setFilters($filters);
		foreach ($visitIterator as $visit) {
			$visitId = (int)$visit->visitId;
			$appointmentId = (int)$visit->appointmentId;
			$acct = $visit->accountSummary;
			$total = $acct['total'];
			$billed = $acct['billed'];
			$writeoff = $acct['writeoff'];
			$paid = $acct['payment'];
			$balance = $acct['balance'];
			$payerId = isset($acct['claimLine'])?$acct['claimLine']->insuranceProgramId:$visit->activePayerId;

			$names = array('total','billed','paid','writeoff','balance');
			foreach ($names as $name) {
				if (!isset($filters[$name]) || !$filters[$name]['active']) continue;
				$operator = $filters[$name]['operator'];
				$operand1 = $filters[$name]['operand1'];
				$operand2 = $filters[$name]['operand2'];
				if ($operator == '=' && !($$name == $operand1)) continue 2;
				else if ($operator == '>' && !($$name > $operand1)) continue 2;
				else if ($operator == '>=' && !($$name >= $operand1)) continue 2;
				else if ($operator == '<' && !($$name < $operand1)) continue 2;
				else if ($operator == '<=' && !($$name <= $operand1)) continue 2;
				else if ($operator == 'between' && $operand2 > 0 && !($$name >= $operand1 && $$name <= $operand2)) {
					continue 2;
				}
			}
 			$paid += $writeoff;

			$dateOfService = substr($visit->dateOfTreatment,0,10);
			$personId = (int)$visit->patientId;

			$person = new Person();
			$person->personId = $personId;
			$person->populate();

			$color = '';
			if ($visit->closed) {
				if ($total > 0 && $balance <= 0) {
					$color = '#82CA9D'; // pastel green for fully zeroed claims
				}
				else if ($paid > 0) {
					$color = '#F7977A'; // pastel red for claims partly paid or with a denial status
				}
			}
			if ($color == '') {
				$color = '#FFF79A'; // pastel yellow for rows transmitted
			}
			$facility = $visit->facility;
			$total = number_format(abs($total),2,'.',',');
			$billed = number_format(abs($billed),2,'.',',');
			$paid = number_format(abs($paid),2,'.',',');
			$balance = number_format(abs($balance),2,'.',',');
			$url = $this->view->baseUrl.'/claims.raw/list-claim-files?visitId='.$visitId;
			if (isset($acct['claimLine'])) $url .= '&claimId='.$acct['claimLine']->claimId;
			$row = array();
			$row['id'] = $visitId;
			$row['data'] = array();
			$row['data'][] = $url;
			$row['data'][] = $dateOfService;
			$row['data'][] = $person->displayName;
			$row['data'][] = '$'.$total;
			$row['data'][] = '$'.$billed;
			$row['data'][] = '$'.$paid;
			$row['data'][] = '$'.$balance;
			$row['data'][] = InsuranceProgram::getInsuranceProgram($payerId);
			$row['data'][] = $facility;
			$row['data'][] = $visit->displayStatus;
			$row['userdata']['pid'] = $personId;
			$row['userdata']['color'] = $color;
			$rows[] = $row;
			$claimList[$visitId] = array(
				'billed'=>$billed,
				'paid'=>$paid,
				'balance'=>$balance,
			);
		}
		$this->_session->claimList = $claimList;
		$data = array('rows'=>$rows);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function listPatientClaimsAction() {
		$visitId = (int)$this->_getParam('visitId');
		$rows = array();
		if ($visitId > 0) {
			$visit = new Visit();
			$visit->visitId = $visitId;
			$visit->populate();
			$personId = (int)$visit->patientId;

			$claimLineIterator = new ClaimLineIterator();
			$claimLineIterator->setFilters(array('visitId'=>$visitId));
			foreach ($claimLineIterator as $claimLine) {
				$rows[] = $this->_generateClaimRow($claimLine);
			}
		}
		$claimLine = new ClaimLine();
		$rows[] = $this->_generateClaimRow($claimLine);
		$data = array();
		$data['rows'] = $rows;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function diagnosesModifiersXmlAction() {
		header('Content-Type: application/xml;');
		$this->render('diagnosis-modifiers-xml');
	}

	protected function _generateClaimRow(ClaimLine $claimLine) {
		$row = array();
		$row['id'] = $claimLine->claimLineId;
		$row['data'] = array();
		$row['data'][] = $this->view->baseUrl.'/claims.raw/diagnoses-modifiers.xml';//$claimLine->procedureCode;
		$row['data'][] = $claimLine->procedureCode;
		$subrows = array();
		$subrow = array();
		$subrow['id'] = 1;
		$subrow['data'][] = 'Diagnoses';
		$subrow['rows'] = array(array('id'=>'3','data'=>array('401.1 Benign Hypertension')));
		$subrows[] = $subrow;
		$subrow = array();
		$subrow['id'] = 2;
		$subrow['data'][] = 'Modifiers';
		$subrow['rows'] = array(array('id'=>'4','data'=>array('A1 Ambulance')));
		$subrows[] = $subrow;
		$row['rows'] = $subrows;//= array(array('id'=>'123','data'=>array($claimLine->procedureCode,'diagnosis','modifiers')));
		//$row['data'][] = $claimLine->diagnosisCode1;
		//$row['data'][] = $claimLine->modifier1;
		$row['data'][] = '';//$claimLine->excludeFromDiscount;
		$row['data'][] = $claimLine->baseFee;
		$row['data'][] = $claimLine->adjustedFee;
		$row['userdata']['xDisc'] = $claimLine->excludeFromDiscount;
		$row['userdata']['xClaim'] = $claimLine->excludeFromClaim;
		return $row;
	}

	public function processEditClaimAction() {
		$params = $this->_getParam('claimLine');
		$ret = false;
		if (is_array($params) && isset($params['claimLineId']) && $params['claimLineId'] > 0) {
			$claimLineId = (int)$params['claimLineId'];
			$claimLine = new ClaimLine();
			$claimLine->claimLineId = $claimLineId;
			$claimLine->populate();
			$claimLine->populateWithArray($params);
			$claimLine->persist();
			$ret = $this->_generateClaimRow($claimLine);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function ajaxGetMenuAction() {
		$this->view->type = $this->_getParam('type');
		header('Content-Type: application/xml;');
		$this->render('ajax-get-menu');
	}

	public function listProceduresAction() {
		$visitId = (int)$this->_getParam('visitId');
		$rowId = (int)$this->_getParam('id');
		$rows = array();
		$visit = new Visit();
		$visit->visitId = $visitId;
		if ($visit->populate()) {
			$patientId = (int)$visit->patientId;
			$fees = $visit->calculateFees();
			foreach ($fees['details'] as $values) {
				$orm = $values['orm'];
				$id = $orm->patientProcedureId;
				$code = $orm->code;
				$procedure = $orm->procedure;

				$fee = $values['fee'];
				$feeDiscounted = $values['feeDiscounted'];
				$row = array();
				$row['id'] = $id;
				$row['data'] = array();
				$row['data'][] = 'dummy.xml';
				$row['data'][] = $code.': '.$procedure;
				if ($fee != '-.--') {
					$fee = number_format($fee,2,'.',',');
				}
				$row['data'][] = $fee;
				if ($feeDiscounted != '-.--') {
					$feeDiscounted = number_format($feeDiscounted,2,'.',',');
				}
				$row['data'][] = $feeDiscounted;
				if (!isset($rows[0])) {
					$row['userdata']['discountApplied'] = implode(',',$fees['discountApplied']);
					$row['userdata']['total'] = (float)$fees['total'];
					$row['userdata']['discounted'] = (float)$fees['discounted'];
				}
				$rows[] = $row;
				if ($rowId > 0 && $rowId == $id) {
					$row['userdata'] = $rows[0]['userdata'];
					$rows = array($row);
					break;
				}
			}
		}
		$data = array('rows'=>$rows);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function listClaimsAction() {
		$visitId = (int)$this->_getParam('visitId');
		$rows = array();
		if ($visitId > 0) {
			$baseUrl = Zend_Registry::get('baseUrl');
			$visit = new Visit();
			$visit->visitId = $visitId;
			$visit->populate();
			$list = array(
				'procedures'=>'Procedures',
				'misc-charges'=>'Misc Charges',
				'misc-payments'=>'Misc Payments',
				'totals'=>'Totals',
			);
			foreach ($list as $key=>$value) {
				$row = array();
				$row['id'] = $key;
				$row['data'] = array();
				$url = $baseUrl.'claims.raw/list-'.$key.'?visitId='.$visitId;
				if ($key == 'totals') {
					$url = '';
				}
				$row['data'][] = $url;
				$row['data'][] = '<strong>'.$value.'</strong>';
				$rows[] = $row;
			}
		}
		$data = array();
		$data['rows'] = $rows;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function listMiscChargesAction() {
		$visitId = (int)$this->_getParam('visitId');
		$rows = array();
		if ($visitId > 0) {
			$visit = new Visit();
			$visit->visitId = $visitId;
			$visit->populate();

			$ctr = 1;
			$iterator = MiscCharge::getIteratorByIds($visit);
			foreach ($iterator as $result) {
				$row = array();
				$row['id'] = $ctr++;
				$row['data'] = array();
				$row['data'][] = '';
				$row['data'][] = $result->note;
				$amount = number_format((float)$result->amount,2,'.',',');
				$row['data'][] = $amount;
				$row['data'][] = $amount;
				$rows[] = $row;
			}
		}
		$data = array();
		$data['rows'] = $rows;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function listMiscPaymentsAction() {
		$visitId = (int)$this->_getParam('visitId');
		$rows = array();
		if ($visitId > 0) {
			$visit = new Visit();
			$visit->visitId = $visitId;
			$visit->populate();

			$ctr = 1;
			$paymentIterator = Payment::getIteratorByIds($visit);
			foreach ($paymentIterator as $pay) {
				if (!$pay->visitId > 0 && !$pay->unallocated > 0) continue;
				$row = array();
				$row['id'] = $ctr++;
				$row['data'] = array();
				$row['data'][] = '';
				$row['data'][] = $pay->title;
				$amount = number_format((float)$pay->amount,2,'.',',');
				$row['data'][] = $amount;
				$row['data'][] = $amount;
				$rows[] = $row;
			}
		}
		$data = array();
		$data['rows'] = $rows;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function listDiagnosesAction() {
		$visitId = (int)$this->_getParam('visitId');
		$id = (int)$this->_getParam('id');
		$rows = array();
		if ($visitId > 0) {
			$visit = new Visit();
			$visit->visitId = $visitId;
			$visit->populate();

			$patientProcedure = new PatientProcedure();
			$patientProcedure->patientProcedureId = $id;
			$patientProcedure->populate();
			$diagnoses = array();
			for ($i = 1; $i <= 8; $i++) {
				$field = 'diagnosisCode'.$i;
				$key = $patientProcedure->$field;
				if (strlen($key) > 0) {
					$diagnoses[$key] = $key;
				}
			}

			$enabled = array();
			$disabled = array();
			$patientDiagnosisIterator = new PatientDiagnosisIterator();
			$patientDiagnosisIterator->setFilters(array('patientId'=>(int)$visit->patientId,'visitId'=>$visitId));
			foreach ($patientDiagnosisIterator as $row) {
				$tmp = array();
				$tmp['id'] = $row->code;
				$tmp['data'] = array();
				$tmp['data'][] = isset($diagnoses[$row->code])?'1':'';
				$diagnosis = $row->code.': '.$row->diagnosis;
				if ($row->isPrimary) $diagnosis = '<strong>'.$diagnosis.'</strong>';
				$tmp['data'][] = $diagnosis;
				if ($tmp['data'][0] == '1') {
					$enabled[$diagnoses[$row->code]] = $tmp;
				}
				else {
					$disabled[] = $tmp;
				}
			}
			$tmp = $enabled;
			$enabled = array();
			foreach ($diagnoses as $diagnosis) {
				$enabled[] = $tmp[$diagnosis];
			}
			$rows = array_merge($enabled,$disabled);
		}
		$data = array();
		$data['rows'] = $rows;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function listModifiersAction() {
		$visitId = (int)$this->_getParam('visitId');
		$id = (int)$this->_getParam('id');
		$rows = array();
		if ($visitId > 0) {
			$visit = new Visit();
			$visit->visitId = $visitId;
			$visit->populate();

			$patientProcedure = new PatientProcedure();
			$patientProcedure->patientProcedureId = $id;
			$patientProcedure->populate();
			$modifiers = array();
			for ($i = 1; $i <= 4; $i++) {
				$field = 'modifier'.$i;
				$key = $patientProcedure->$field;
				if (strlen($key) > 0) {
					$modifiers[$key] = $key;
				}
			}

			$enabled = array();
			$disabled = array();
			$enumeration = new Enumeration();
			$enumeration->populateByUniqueName('Procedure Modifiers');
			$closure = new EnumerationClosure();
			$descendants = $closure->getAllDescendants($enumeration->enumerationId,1,true);
			foreach ($descendants as $row) {
				$tmp = array();
				$tmp['id'] = $row->key;
				$tmp['data'] = array();
				$tmp['data'][] = isset($modifiers[$row->key])?'1':'';
				$tmp['data'][] = $row->key.': '.$row->name;
				if ($tmp['data'][0] == '1') {
					$enabled[$modifiers[$row->key]] = $tmp;
				}
				else {
					$disabled[] = $tmp;
				}
			}
			$tmp = $enabled;
			$enabled = array();
			foreach ($modifiers as $modifier) {
				$enabled[] = $tmp[$modifier];
			}
			$rows = array_merge($enabled,$disabled);
		}
		$data = array();
		$data['rows'] = $rows;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processSetDiagnosesAction() {
		$id = (int)$this->_getParam('id');
		$state = (int)$this->_getParam('state');
		$code = $this->_getParam('code');
		$ret = $this->_processSetDiagnosisModifier('Diagnosis',$id,$state,$code);
		if (!$ret) {
			if ($state) {
				$ret = __('Maximum diagnoses reached');
			}
			else {
				$ret = __('Selected diagnosis does not exist');
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function processSetModifiersAction() {
		$id = (int)$this->_getParam('id');
		$state = (int)$this->_getParam('state');
		$code = $this->_getParam('code');
		$ret = $this->_processSetDiagnosisModifier('Modifier',$id,$state,$code);
		if (!$ret) {
			if ($state) {
				$ret = __('Maximum modifiers reached');
			}
			else {
				$ret = __('Selected modifier does not exist');
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	protected function _processSetDiagnosisModifier($type,$id,$state,$code) {
		$ret = false;
		$patientProcedure = new PatientProcedure();
		$patientProcedure->patientProcedureId = (int)$id;
		if (strlen($code) > 0 && $patientProcedure->populate()) {
			$method = 'setUnset'.$type;
			if ($patientProcedure->$method($code,$state)) {
				$patientProcedure->persist();
				$ret = true;
			}
		}
		return $ret;
	}

	public function processReorderDiagnosesAction() {
		$claimLineId = (int)$this->_getParam('claimId');
		$from = $this->_getParam('from');
		$to = $this->_getParam('to');
		$ret = $this->_processReorderDiagnosisModifier('Diagnosis',$claimLineId,$from,$to);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function processReorderModifiersAction() {
		$claimLineId = (int)$this->_getParam('claimId');
		$from = (int)$this->_getParam('from');
		$to = $this->_getParam('to');
		$ret = $this->_processReorderDiagnosisModifier('Modifier',$claimLineId,$from,$to);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	protected function _processReorderDiagnosisModifier($type,$claimLineId,$from,$to) {
		$ret = __('Failed to reorder');
		$claim = new ClaimLine();
		$claim->claimLineId = $claimLineId;
		if (strlen($from) > 0 && strlen($to) > 0 && $claim->populate()) {
			$method = 'reorder'.$type;
			$claim->$method($from,$to);
			$claim->persist();
			$ret = true;
		}
		return $ret;
	}

	public function processClaimsAction() {
		$type = $this->_getParam('type');
		$data = '';

		$claimList = isset($this->_session->claimList)?$this->_session->claimList:array();
		$visitIds = isset($this->_session->visitIds)?$this->_session->visitIds:array();

		$claimIds = ClaimLine::listAllMostRecentClaimIds($visitIds);
		$destinations = Claim::listOptions();
		$claimFile = new ClaimFile();
		$claimFile->destination = $type;
		$claimFile->claimIds = implode(',',$claimIds);
		$claimFile->status = 'transmitted';
		$claimFile->dateTime = date('Y-m-d H:i:s');
		$claimFile->userId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$claimFile->persist();

		if (isset($claimIds[0])) {
			switch ($type) {
				case 'healthcloud': // takes 4010A files and send to healthcloud
					$this->getResponse()->setHeader('Content-Type','text/plain');
					$this->getResponse()->setHeader('Content-Disposition','attachment; filename="4010A1.txt"');
					$data = Claim::render4010A1($claimFile,$claimIds);
					$claimFile->persistData($data);
					$claimFile->transmit($data);
					break;
				case 'download4010A1':
					$this->getResponse()->setHeader('Content-Type','text/plain');
					$this->getResponse()->setHeader('Content-Disposition','attachment; filename="4010A1.txt"');
					$data = Claim::render4010A1($claimFile,$claimIds);
					break;
				case 'download5010':
					break;
				case 'CMS1500PDF':
					$data = $this->_downloadCMSPDF($claimIds,1500);
					break;
				case 'CMS1450PDF':
					$data = $this->_downloadCMSPDF($claimIds,1450);
					break;
				case 'previewStatements':
					$data = $this->_previewStatements($claimIds);
					break;
				case 'publishStatements':
					$data = $this->_download4010A1($claimFile,$claimIds);
					$xml = new SimpleXMLElement('<data/>');
					$result = $this->_previewStatements($claimIds,$xml);
					$statementData = $xml->asXML();
					$claimFile->persistData($data,$statementData);
					$claimFile->transmit($data,$statementData);
					$data = $result;
					break;
				default:
					break;
			}
		}
		$this->_session->visitIds = array();
		$this->view->content = $data;
		$this->render('download');
	}

	protected function _previewStatements(Array $claimIds,SimpleXMLElement $xml = null) {
		$refId = 'a2f8d4b3-73cf-4637-9e2e-2904ab55d64d';
		$attachment = new Attachment();
		$attachment->attachmentReferenceId = $refId;
		$attachment->populateWithAttachmentReferenceId();
		if ($xml === null) $xml = new SimpleXMLElement('<data/>');

		foreach ($claimIds as $claimId) {
			$this->_generateXMLStatements($xml->addChild('statements'),$claimId);
		}

		try {
			$content = ReportBase::mergepdfset($xml,$attachment->rawData);
			$this->getResponse()->setHeader('Content-Type','application/pdf');
		}
		catch (Exception $e) {
			$content = '<script>alert("'.$e->getMessage().'")</script>';
		}
		return $content;
	}

	protected function _generateXMLStatements(SimpleXMLElement $xml,$claimId) {
		$iterator = new ClaimLineIterator();
		$iterator->setFilters(array('claimId'=>$claimId));
		$visitId = null;
		$totalPayments = 0;
		$totalCharges = 0;
		$claims = array();
		foreach ($iterator as $claimLine) {
			$claimLineId = (int)$claimLine->claimLineId;
			$visitId = (int)$claimLine->visitId;
			$payerId = (int)$claimLine->insuranceProgramId;
			$procedureCode = $claimLine->procedureCode;
			$charges = (float)$claimLine->baseFee;
			$totalCharges += $charges;
			if (!isset($visit)) {
				$visit = new Visit();
				$visit->visitId = $visitId;
				$visit->populate();
			}
			if (!isset($patientName)) {
				$patientId = (int)$visit->patientId;
				$patient = new Patient();
				$patient->personId = $patientId;
				$patient->populate();
				$patientName = $patient->person->firstName.' '.$patient->person->middleName.' '.$patient->person->lastName;
			}
			if (!isset($providerName)) {
				$providerId = (int)$visit->providerId;
				$provider = new Provider();
				$provider->personId = $providerId;
				$provider->populate();
				$providerName = $provider->person->firstName.' '.$provider->person->middleName.' '.$provider->person->lastName;
			}

			$claim = array();
			$claim['date'] = date('m/d/Y',strtotime($claimLine->dateTime));
			$claim['patient'] = $patientName;
			$claim['provider'] = $providerName;
			$claim['description'] = $claimLine->procedure;
			$claim['charges'] = number_format($charges,2);
			$claim['credits'] = '';
			if (!isset($claims[$visitId])) $claims[$visitId] = array();
			$claims[$visitId][] = $claim;
		}

		if ($visitId === null) return;

		$miscCharge = new MiscCharge();
		$iterator = $miscCharge->getIteratorByVisitId($visitId);
		foreach ($iterator as $charge) {
			$amount = (float)$charge->amount;
			$totalCharges += $amount;
			$claim = array();
			$claim['date'] = date('m/d/Y',strtotime($charge->chargeDate));
			$claim['patient'] = $patientName;
			$claim['provider'] = $providerName;
			$description = $charge->chargeType;
			$title = $charge->title;
			if (strlen($title) > 0) $description .= ': '.$title;
			$claim['description'] = $description;
			$claim['charges'] = number_format($amount,2);
			$claim['credits'] = '';
			$id = (int)$charge->miscChargeId;
			if (!isset($claims[$id])) $claims[$id] = array();
			$claims[$id][] = $claim;
		}

		$iterator = new PostingJournalIterator();
		$iterator->setFilters(array('visitId'=>$visitId));
		foreach ($iterator as $journal) {
			$amount = (float)$journal->amount;
			$totalPayments += $amount;
			$claim = array();
			$claim['date'] = date('m/d/Y',strtotime($journal->datePosted));
			$claim['patient'] = $patientName;
			$claim['provider'] = $providerName;
			$claim['description'] = $journal->note;
			$claim['charges'] = '';
			$claim['credits'] = number_format($amount,2);
			$id = (int)$journal->postingJournalId;
			if (!isset($claims[$id])) $claims[$id] = array();
			$claims[$id][] = $claim;
		}

		$iterator = new WriteOffIterator();
		$iterator->setFilters(array('visitId'=>$visitId));
		foreach ($iterator as $writeoff) {
			$amount = (float)$writeoff->amount;
			$totalPayments += $amount;
			$claim = array();
			$claim['date'] = date('m/d/Y',strtotime($writeoff->timestamp));
			$claim['patient'] = $patientName;
			$claim['provider'] = $providerName;
			$claim['description'] = $writeoff->title;
			$claim['charges'] = '';
			$claim['credits'] = number_format($amount,2);
			$id = (int)$writeoff->writeOffId;
			if (!isset($claims[$id])) $claims[$id] = array();
			$claims[$id][] = $claim;
		}

		$practiceId = (int)$visit->practiceId;
		if (!$practiceId > 0) $practiceId = (int)$provider->person->primaryPracticeId;
		$practice = new Practice();
		$practice->practiceId = $practiceId;
		$practice->populate();

		$practiceName = $practice->name;
		$practicePrimaryLine1 = $practice->primaryAddress->line1;
		$practicePrimaryCityStateZip = $practice->primaryAddress->city.', '.$practice->primaryAddress->state.' '.$practice->primaryAddress->postalCode;
		$practiceMainPhone = $practice->mainPhone->number;

		$xmlPractice = $xml->addChild('practice');
		$this->_addChild($xmlPractice,'name',$practiceName);
		$this->_addChild($xmlPractice,'primaryLine1',$practicePrimaryLine1);
		$this->_addChild($xmlPractice,'primaryCityStateZip',$practicePrimaryCityStateZip);
		$this->_addChild($xmlPractice,'mainPhone',$practiceMainPhone);

		$insuredRelationship = InsuredRelationship::filterByPayerPersonIds($payerId,$patientId);
		$subscriberId = (int)$insuredRelationship->subscriberId;
		if ($subscriberId > 0) $subscriber = $insuredRelationship->subscriber;
		else $subscriber = $insuredRelationship->person;

		$xmlSubscriber = $xml->addChild('subscriber');
		$subscriberName = $subscriber->firstName.' '.$subscriber->middleName.' '.$subscriber->lastName;
		$this->_addChild($xmlSubscriber,'name',$subscriberName);
		$address = $subscriber->address;
		$this->_addChild($xmlSubscriber,'line1',$address->line1);
		$subscriberCityStateZip = $address->city.', '.$address->state.' '.$address->postalCode;
		$this->_addChild($xmlSubscriber,'cityStateZip',$subscriberCityStateZip);

		$xmlBiller = $xml->addChild('biller');
		$this->_addChild($xmlBiller,'name',$practiceName);
		$this->_addChild($xmlBiller,'line1',$practicePrimaryLine1);
		$this->_addChild($xmlBiller,'cityStateZip',$practicePrimaryCityStateZip);

		$xmlClaims = $xml->addChild('claims');

		$claim = array();
		$claim['date'] = ' ';
		$claim['patient'] = ' ';
		$claim['provider'] = ' ';
		$claim['description'] = ' ';
		$claim['charges'] = ' ';
		$claim['credits'] = ' ';
		ksort($claims);
		$claims = array_pad($claims,12,array($claim));
		foreach ($claims as $key=>$values) {
			foreach ($values as $claim) {
				$xmlClaimsDetails = $xmlClaims->addChild('details');
				$this->_addChild($xmlClaimsDetails,'date',$claim['date']);
				$this->_addChild($xmlClaimsDetails,'patientName',$claim['patient']);
				$this->_addChild($xmlClaimsDetails,'providerName',$claim['provider']);
				$this->_addChild($xmlClaimsDetails,'description',$claim['description']);
				$this->_addChild($xmlClaimsDetails,'charges',$claim['charges']);
				$this->_addChild($xmlClaimsDetails,'credits',$claim['credits']);
			}
		}
		$date = date('m/d/Y');
		$accountNumber = $patient->recordNumber;;
		$totalOutstanding = $totalCharges - $totalPayments;
		$pendingInsurance = 0;
		$amount = $totalOutstanding - $pendingInsurance;
		$patientResponsibility = $amount;
		$current = $amount;
		$pastDue = 0;
		$amountDue = $amount;

		$xmlClaimsInfo = $xmlClaims->addChild('info');
		$this->_addChild($xmlClaimsInfo,'statement',$date);
		$this->_addChild($xmlClaimsInfo,'accountNo',$accountNumber);
		$this->_addChild($xmlClaimsInfo,'amount',number_format($amountDue,2));

		$xmlClaimsSummary = $xmlClaims->addChild('summary');
		$this->_addChild($xmlClaimsSummary,'accountNumber',$accountNumber);
		$this->_addChild($xmlClaimsSummary,'totalOutstanding',number_format($totalOutstanding,2));
		$this->_addChild($xmlClaimsSummary,'patientResponsibility',number_format($patientResponsibility,2));
		$this->_addChild($xmlClaimsSummary,'pendingInsurance',number_format($pendingInsurance,2));
		$this->_addChild($xmlClaimsSummary,'current',number_format($current,2));
		$this->_addChild($xmlClaimsSummary,'pastDue',number_format($pastDue,2));
		$this->_addChild($xmlClaimsSummary,'amountDue',number_format($amountDue,2));
		file_put_contents('/tmp/claims.xml',$xml->asXML());
	}

	protected function _downloadCMSPDF(Array $claimIds,$type) {
		$this->getResponse()->setHeader('Content-Type','application/pdf');
		$attachment = new Attachment();
		$attachment->attachmentReferenceId = $type;
		$attachment->populateWithAttachmentReferenceId();
		$inputFile = tempnam('/tmp','cms_');
		$outputFile = tempnam('/tmp','cms_');
		$xmlFile = tempnam('/tmp','cms_');
		file_put_contents($inputFile,$attachment->rawData);
		$xmlFile = $this->_generateCMSXML($claimIds,$type);
		$pdfset = exec('which pdfset');
		if (!strlen($pdfset) > 0) $pdfset = '/usr/local/bin/pdfset';
		$output = `$pdfset -i $inputFile -X $xmlFile -o $outputFile`;
		$ret = file_get_contents($outputFile);
		// cleanup mess
		unlink($inputFile);
		unlink($outputFile);
		unlink($xmlFile);
		//trigger_error(print_r($claimIds,true));
		//$cmd = "pdfset -i $inputFile -X $xmlFile -o $outputFile";
		//trigger_error($cmd);
		return $ret;
	}

	protected function _generateCMSXML(Array $claimIds,$type,$retFile=true) {
		//$xml = new SimpleXMLElement('<cmspage/>');
		$xml = new SimpleXMLElement('<cms/>');
		foreach ($claimIds as $claimId) {
			$claimLine = new ClaimLine();
			$claimLine->populateByClaimId($claimId);
			if ($type == 1450) {
				$cmspage = $xml->addChild('cmspage');
				$this->_populateCMS1450XML($cmspage,$claimLine);
			}
			else if ($type == 1500) {
				$cmspage = $xml->addChild('cmspage');
				$this->_populateCMS1500XML($cmspage,$claimLine);
			}
		}
		$data = preg_replace('/<\?.*\?>/','',$xml->asXML());
		if ($retFile) {
			$filename = tempnam('/tmp','cms_');
			file_put_contents($filename,$data);
			$data = $filename;
		}
		return $data;
	}

	protected function _populateCMS1450XML(SimpleXMLElement $xml,ClaimLine $claimLine) {
		$claimId = (int)$claimLine->claimId;
		$visitId = (int)$claimLine->visitId;
		$payerId = (int)$claimLine->insuranceProgramId;
		$visit = new Visit();
		$visit->visitId = $visitId;
		$visit->populate();

		$dateOfTreatment = $visit->dateOfTreatment;
		$patientId = (int)$visit->patientId;

		$insuredRelationship = InsuredRelationship::filterByPayerPersonIds($payerId,$patientId);

		$person = new Person();
		$person->personId = $patientId;
		$person->populate();

		$provider = new Provider();
		$provider->personId = (int)$visit->treatingPersonId;
		$provider->populate();

		$buildingId = (int)$visit->buildingId;
		$building = new Building();
		$building->buildingId = $buildingId;
		$building->populate();

		$address = $building->practice->primaryAddress;

		$total = 0;
		$claimLines = array();
		$iterator = new ClaimLineIterator();
		$iterator->setFilters(array('claimId'=>$claimId));
		foreach ($iterator as $orm) {
			$amount = (float)$orm->baseFee;
			$total += $amount;
			$row = array();
			$row['amount'] = $amount;
			$row['units'] = $orm->units;
			$diagnoses = array();
			for ($i = 1; $i <= 8; $i++) {
				$field = 'diagnosisCode'.$i;
				$key = $orm->$field;
				if (strlen($key) > 0) {
					$diagnoses[$key] = $key;
				}
			}
			$row['diagnoses'] = $diagnoses;
			$claimLines[] = $row;
		}

		$xmlPatient = $xml->addChild('patient');
		$this->_addChild($xmlPatient,'last_name',$person->lastName);
		$this->_addChild($xmlPatient,'first_name',$person->firstName);
		$this->_addChild($xmlPatient,'middle_name',$person->middleName);
		$this->_addChild($xmlPatient,'date_of_birth',$person->dateOfBirth);

		$xmlClaim = $xml->addChild('claim');
		$this->_addChild($xmlClaim,'id',$claimId);
		$this->_addChild($xmlClaim,'amount_total',$total);

		$xmlClaimLine = null;
		foreach ($claimLines as $row) {
			if ($xmlClaimLine === null) $xmlClaimLine = $xml->addChild('claim_lines');
			$xmlClaimLineArr = $xmlClaimLine->addChild('array');
			$this->_addChild($xmlClaimLineArr,'date_of_treatment',$dateOfTreatment);
			$this->_addChild($xmlClaimLineArr,'amount',$row['amount']);
			$this->_addChild($xmlClaimLineArr,'units',$row['units']);
		}

		if ($address->addressId > 0) {
			$xmlBillingFacility = $xml->addChild('billing_facility');
			$xmlFbaddress = $xmlBillingFacility->addChild('fbaddress');
			$this->_addChild($xmlFbaddress,'line1',$address->line1);
			$this->_addChild($xmlFbaddress,'line2',$address->line2);
			$this->_addChild($xmlFbaddress,'city',$address->city);
			$this->_addChild($xmlFbaddress,'state',$address->state);
			$this->_addChild($xmlFbaddress,'zip',$address->zipCode);
			$this->_addChild($xmlBillingFacility,'name',$building->name);
			$this->_addChild($xmlBillingFacility,'identifier',$building->identifier);
		}

		$xmlDiagnoses = null;
		foreach ($claimLines as $row) {
			foreach ($row['diagnoses'] as $diagnosis) {
				if ($xmlDiagnoses === null) $xmlDiagnoses = $xml->addChild('diagnoses');
				$this->_addChild($xmlDiagnoses,'array',$diagnosis);
			}
		}

		if (strlen($insuredRelationship->groupNumber) > 0) {
			$xmlSubscriber = $xml->addChild('subscriber');
			$this->_addChild($xmlSubscriber,'group_number',$insuredRelationship->groupNumber);
		}

		if (strlen($provider->identifier) > 0) {
			$xmlProvider = $xml->addChild('provider');
			$this->_addChild($xmlProvider,'identifier',$provider->identifier);
		}
	}

	protected function _populateCMS1500XML(SimpleXMLElement $xml,ClaimLine $claimLine) {
		$claimId = (int)$claimLine->claimId;
		$visitId = (int)$claimLine->visitId;
		$payerId = (int)$claimLine->insuranceProgramId;
		$visit = new Visit();
		$visit->visitId = $visitId;
		$visit->populate();

		$dateOfTreatment = $visit->dateOfTreatment;
		$patientId = (int)$visit->patientId;
		$providerId = (int)$visit->treatingPersonId;

		$insuranceProgram = new InsuranceProgram();
		$insuranceProgram->insuranceProgramId = $payerId;
		$insuranceProgram->populate();
		$insuranceAddress = $insuranceProgram->address;

		$insuredRelationship = InsuredRelationship::filterByPayerPersonIds($payerId,$patientId);

		$patient = new Patient();
		$patient->personId = $patientId;
		$patient->populate();
		$patientAddress = $patient->address;
		$patientPhone = $patient->phoneNumber;

		$provider = new Provider();
		$provider->personId = $providerId;
		$provider->populate();
		$providerAddress = $provider->person->address;
		$providerPhone = $provider->phoneNumber;

		$buildingId = (int)$visit->buildingId;
		$building = new Building();
		$building->buildingId = $buildingId;
		$building->populate();

		$address = $building->practice->primaryAddress;

		$amountTotal = 0;
		$claimLines = array();
		$iterator = new ClaimLineIterator();
		$iterator->setFilters(array('claimId'=>$claimId));
		foreach ($iterator as $orm) {
			$amount = (float)$orm->baseFee;
			$amountTotal += $amount;
			$row = array();
			$row['procedure'] = $orm->procedureCode;
			$modifiers = array();
			for ($i = 1; $i <= 4; $i++) {
				$field = 'modifier'.$i;
				$key = $orm->$field;
				if (strlen($key) > 0) {
					$modifiers[$i] = $key;
				}
			}
			$row['modifier'] = implode(',',$modifiers);
			$row['amount'] = $amount;
			$row['units'] = $orm->units;
			$diagnoses = array();
			for ($i = 1; $i <= 8; $i++) {
				$field = 'diagnosisCode'.$i;
				$key = $orm->$field;
				if (strlen($key) > 0) {
					$diagnoses[$key] = $key;
				}
			}
			$row['diagnoses'] = $diagnoses;
			$row['diagnosis_pointer'] = ''; // TODO: need to resolve on the issue of multiple diagnoses per claimLine
			$claimLines[] = $row;
		}
		$amountPaid = $claimLine->paid;
		$netAmountTotal = $amountTotal - $amountPaid;

		if ($payerId > 0) {
			$xmlPayer = $xml->addChild('payer');
			$this->_addChild($xmlPayer,'name',InsuranceProgram::getInsuranceProgram($payerId));
			if ($insuranceAddress->addressId > 0) {
				$xmlFbaddress = $xmlPayer->addChild('fbaddress');
				$this->_addChild($xmlFbaddress,'line1',$insuranceAddress->line1);
				$this->_addChild($xmlFbaddress,'line2',$insuranceAddress->line2);
				$this->_addChild($xmlFbaddress,'city',$insuranceAddress->city);
				$this->_addChild($xmlFbaddress,'state',$insuranceAddress->state);
				$this->_addChild($xmlFbaddress,'zip',$insuranceAddress->zipCode);
			}
		}

		$xmlPatient = $xml->addChild('patient');
		$this->_addChild($xmlPatient,'record_number',$patient->recordNumber);
		$this->_addChild($xmlPatient,'last_name',$patient->lastName);
		$this->_addChild($xmlPatient,'first_name',$patient->firstName);
		$this->_addChild($xmlPatient,'middle_name',$patient->middleName);
		if ($patientAddress->addressId > 0) {
			$xmlFbaddress = $xmlPatient->addChild('fbaddress');
			$this->_addChild($xmlFbaddress,'line1',$patientAddress->line1);
			$this->_addChild($xmlFbaddress,'line2',$patientAddress->line2);
			$this->_addChild($xmlFbaddress,'city',$patientAddress->city);
			$this->_addChild($xmlFbaddress,'state',$patientAddress->state);
			$this->_addChild($xmlFbaddress,'zip',$patientAddress->zipCode);
		}
		$this->_addChild($xmlPatient,'phone_number',$patientPhone->number);
		$this->_addChild($xmlPatient,'date_of_birth',$patient->dateOfBirth);

		$subscriber = $insuredRelationship->subscriber;
		$subscriberAddress = $subscriber->address;

		if ($subscriber->personId > 0) {
			$xmlSubscriber = $xml->addChild('subscriber');
			$this->_addChild($xmlSubscriber,'phone_number',$subscriber->phoneNumber->number);
			if ($subscriberAddress->addressId > 0) {
				$xmlFbaddress = $xmlSubscriber->addChild('fbaddress');
				$this->_addChild($xmlFbaddress,'line1',$subscriberAddress->line1);
				$this->_addChild($xmlFbaddress,'line2',$subscriberAddress->line2);
				$this->_addChild($xmlFbaddress,'city',$subscriberAddress->city);
				$this->_addChild($xmlFbaddress,'state',$subscriberAddress->state);
				$this->_addChild($xmlFbaddress,'zip',$subscriberAddress->zipCode);
			}
			$this->_addChild($xmlSubscriber,'last_name',$subscriber->lastName);
			$this->_addChild($xmlSubscriber,'first_name',$subscriber->firstName);
			$this->_addChild($xmlSubscriber,'middle_name',$subscriber->middleName);
			$this->_addChild($xmlSubscriber,'group_number',$insuredRelationship->groupNumber);
			$this->_addChild($xmlSubscriber,'gender',$subscriber->gender);
			$this->_addChild($xmlSubscriber,'date_of_birth',$subscriber->dateOfBirth);
		}

		if ($building->buildingId > 0) {
			$xmlPractice = $xml->addChild('practice');
			$this->_addChild($xmlPractice,'identifier',$building->practice->identifier);

			$xmlTreatingFacility = $xml->addChild('treating_facility');
			$this->_addChild($xmlTreatingFacility,'identifier',$building->identifier);
			$this->_addChild($xmlTreatingFacility,'name',$building->name);

			$xmlFbaddress = $xmlTreatingFacility->addChild('fbaddress');
			$this->_addChild($xmlFbaddress,'line1',$building->line1);
			$this->_addChild($xmlFbaddress,'line2',$building->line2);
			$this->_addChild($xmlFbaddress,'city',$building->city);
			$this->_addChild($xmlFbaddress,'state',$building->state);
			$this->_addChild($xmlFbaddress,'zip',$building->zipCode);
		}
		$xmlProvider = $xml->addChild('provider');
		$this->_addChild($xmlProvider,'identifier',$provider->identifier);
		$this->_addChild($xmlProvider,'first_name',$provider->firstName);
		$this->_addChild($xmlProvider,'last_name',$provider->lastName);
		if ($providerAddress->addressId > 0) {
			$xmlFbaddress = $xmlProvider->addChild('fbaddress');
			$this->_addChild($xmlFbaddress,'line1',$providerAddress->line1);
			$this->_addChild($xmlFbaddress,'line2',$providerAddress->line2);
			$this->_addChild($xmlFbaddress,'city',$providerAddress->city);
			$this->_addChild($xmlFbaddress,'state',$providerAddress->state);
			$this->_addChild($xmlFbaddress,'zip',$providerAddress->zipCode);
		}
		$xmlDiagnoses = null;
		foreach ($claimLines as $row) {
			foreach ($row['diagnoses'] as $diagnosis) {
				if ($xmlDiagnoses === null) $xmlDiagnoses = $xml->addChild('diagnoses');
				$this->_addChild($xmlDiagnoses,'array',$diagnosis);
			}
		}
		$xmlClaimLine = null;
		$dateOfService = date('m   d   y',strtotime($dateOfTreatment));
		foreach ($claimLines as $row) {
			if ($xmlClaimLine === null) $xmlClaimLine = $xml->addChild('claim_lines'); // 0 - 5 ONLY
			$xmlClaimLineArr = $xmlClaimLine->addChild('array');
			$this->_addChild($xmlClaimLineArr,'date_of_treatment',$dateOfService);
			$this->_addChild($xmlClaimLineArr,'procedure',$row['procedure']);
			$this->_addChild($xmlClaimLineArr,'modifier',$row['modifier']);
			$this->_addChild($xmlClaimLineArr,'diagnosis_pointer',$row['diagnosis_pointer']);
			$this->_addChild($xmlClaimLineArr,'amount',$row['amount']);
			$this->_addChild($xmlClaimLineArr,'units',$row['units']);
		}
		$xmlClaim = $xml->addChild('claim');
		$this->_addChild($xmlClaim,'amount_total',$amountTotal);
		$this->_addChild($xmlClaim,'amount_paid',$amountPaid);
		$this->_addChild($xmlClaim,'net_amount_total',$netAmountTotal);
		//$doc = new DOMDocument();
		//$doc->formatOutput = true;
		//$doc->loadXML($xml->asXML());
		//file_put_contents('/tmp/claims.xml',$doc->saveXML());
	}

	protected function _addChild(SimpleXMLElement $xml,$key,$value,$checked=true) {
		if (is_object($value)) trigger_error($key.'='.get_class($value));
		if (!$checked || (strlen($key) > 0 && strlen($value) > 0)) $xml->addChild($key,htmlentities($value));
	}

	public function setSessionBatchIdsAction() {
		$ids = $this->_getParam('ids');
		$visitIds = array();
		foreach (explode(',',$ids) as $id) {
			if (!$id > 0) continue;
			$visitIds[] = $id;
		}
		$this->_session->visitIds = $visitIds;
		$data = true;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function listClaimFilesAction() {
		$visitId = (int)$this->_getParam('visitId');
		$rows = array();
		$iterator = array();
		if ($visitId > 0) {
			$iterator = new ClaimFileIterator();
			$iterator->setFilters(array('visitId'=>$visitId));
		}
		foreach ($iterator as $claimFile) {
			$claimLine = ClaimFile::claimLine($visitId,explode(',',$claimFile->claimIds));
			$claimLineId = (int)$claimLine->claimLineId;
			$total = 0;
			$billed = 0;
			$paid = 0;
			$writeoff = 0;
			$balance = 0;
			if ($claimLineId > 0) {
				$miscCharge = $claimLine->totalMiscCharge;
				$fees = $claimLine->getTotal(true);
				$baseFee = $fees['baseFee'];
				$adjustedFee = $fees['adjustedFee'];
				$total = $baseFee + $miscCharge;
				//$billed = $miscCharge;
				$billed += (float)$claimLine->baseFee;
				//if ($baseFee > 0) $billed += $baseFee - $adjustedFee;
				$writeoff = $claimLine->overallWriteOff;
				$paid = $claimLine->overallPayment + $writeoff;
				$balance = abs($billed) - $paid;
			}

			$color = '';
			if ($total > 0 && $balance <= 0) {
				$color = '#82CA9D'; // pastel green for fully zeroed claims
			}
			else if ($paid > 0) {
				$color = '#F7977A'; // pastel red for claims partly paid or with a denial status
			}

			$row = array();
			$id = (int)$claimFile->claimFileId;
			$row['id'] = $id;
			$row['data'] = array();
			$row['data'][] = $claimFile->dateTime;
			$row['data'][] = $billed;
			$row['data'][] = $paid;
			$row['data'][] = InsuranceProgram::getInsuranceProgram($claimLine->insuranceProgramId);
			$row['data'][] = $claimFile->status;
			$row['userdata']['color'] = $color;
			$rows[] = $row;
		}
		$data = array('rows'=>$rows);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function setSessionIdsAction() {
		$ids = $this->_getParam('ids','');
		$data = false;
		if (strlen($ids) > 0) {
			$data = true;
		}
		$this->_session->ids = $ids;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function makePaymentAction() {
		$ids = explode(',',$this->_session->ids);

		$data = array(
			'claims'=>array(),
			'patients'=>array(),
			'payers'=>array(),
			'facilities'=>array(),
			'funds'=>array(),
		);
		$visits = array();
		foreach ($ids as $id) {
			$visitId = (int)$id;
			if (!$visitId > 0) continue;
			$visit = new Visit();
			$visit->visitId = $visitId;
			if (!$visit->populate()) continue;
			$funds = $visit->unallocatedFunds;
			$claims = $visit->uniqueClaims;
			$data['claimIds'][$visitId] = array(
				'personId'=>(int)$visit->patientId,
				'unallocatedFunds'=>$funds['total'],
				'claimIds'=>$claims['claimId'],
				'payerIds'=>$claims['payerId'],
			);
		}
		$this->view->data = $data;

		$payment = new Payment();
		$form = new WebVista_Form(array('name'=>'editPayment'));
		$form->setAction(Zend_Registry::get('baseUrl').'claims.raw/process-payments');
		$form->loadORM($payment,'payment');
		$form->setWindow('windowClaimsMakePaymentId');
		$this->view->form = $form;
		$payers = array(''=>'');
		foreach (InsuranceProgram::getInsurancePrograms() as $key=>$value) {
			$payers[$key] = $value;
		}
		$this->view->payers = $payers;
		$this->view->visitIds = $ids;
		$this->view->payerSelf = InsuranceProgram::lookupSystemId('Self Pay'); // ID of System->Self Pay

		$this->render();
	}

	public function processPaymentsAction() {
		$visitId = (int)$this->_getParam('visitId');
		$params = $this->_getParam('payment');
		$paidAmounts = $this->_getParam('paid');
		$writeOffAmounts = $this->_getParam('writeOff');
		$note = $this->_getParam('note');
		$sourceOfFunds = $this->_getParam('sourceOfFunds');
		$checkNo = $this->_getParam('checkNo');
		$userId = (int)Zend_Auth::getInstance()->getIdentity()->personId;

		// TODO: need to record payment as a postingJournal entry against the new payment row that represents the new check # or other source of unallocated funds
		if (is_array($params)) {
			$visit = new Visit();
			$visit->visitId = $visitId;
			$visit->populate();
			$payment = new Payment();
			$payment->populateWithArray($params);
			$payerId = (int)$payment->payerId;
			$personId = (int)$payment->personId;
			$visitId = (int)$payment->visitId;
			$paymentDate = $payment->paymentDate;

			$otm = new WebVista_Model_ORMTransactionManager();
			if (is_array($paidAmounts)) {
				$payments = array();
				if ($sourceOfFunds == 'checkFundsId') { // check funds
					// uniqueCheckNumbers
					$checkFunds = Payment::listCheckFunds($checkNo);
					$payments = $checkFunds['details'];
				}
				else { // unallocated funds
					$unAllocatedFunds = $visit->unallocatedFunds;
					$payments = $unAllocatedFunds['details'];
				}

				$firstRow = (strlen($note) > 0)?false:true;
				foreach ($paidAmounts as $claimLineId=>$amount) {
					$amount = (float)$amount;
					if (!$amount > 0) continue;
					$billable = $amount;
					foreach ($payments as $paymentId=>$payment) {
						$amount = (float)$payment->unallocated;
						if (!$amount > 0) {
							unset($payments[$paymentId]);
							continue;
						}
						if ($amount > $billable) $amount = $billable;
						$payment->allocated += $amount;
						$payment->persist();
						$otm->addORM($payment);
						$payments[$paymentId] = $payment;

						$postingJournal = new PostingJournal();
						$postingJournal->paymentId = (int)$payment->paymentId;
						$postingJournal->patientId = $personId;
						$postingJournal->payerId = $payerId;
						$postingJournal->claimLineId = $claimLineId;
						$postingJournal->visitId = $visitId;
						$postingJournal->amount = $amount;
						$postingJournal->note = $note;
						$postingJournal->userId = $userId;
						$dateTime = date('Y-m-d H:i:s');
						$postingJournal->datePosted = $paymentDate;
						$postingJournal->dateTime = $dateTime;
						$postingJournal->persist();
						$otm->addORM($postingJournal);
						$billable -= $amount;
						if ($billable <= 0) break;
					}
					if (!$firstRow) { // persist payment note on first claim
						$claimLine = new ClaimLine();
						$claimLine->claimLineId = (int)$claimLineId;
						if ($claimLine->populate()) {
							$claimLine->note = $note;
							$claimLine->persist();
							$otm->addORM($claimLine);
							$firstRow = true;
						}
					}
				}
			}
			if (is_array($writeOffAmounts)) {
				$writeOff = new WriteOff();
				$writeOff->populateWithArray($params);
				$writeOff->userId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
				$writeOff->timestamp = date('Y-m-d H:i:s');
				$writeOff->payerId = $payerId;
				foreach ($writeOffAmounts as $claimLineId=>$amount) {
					if (!$amount > 0) continue;
					$writeOff->writeOffId = 0;
					$writeOff->claimLineId = (int)$claimLineId;
					$writeOff->amount = $amount;
					$writeOff->persist();
					$otm->addORM($writeOff);
				}
			}
			if (count($otm->getQueries()) > 0 && !$otm->persist()) {
				trigger_error(__('Failed to save.'));
			}
		}

		$unallocatedFunds = 0;
		if (isset($visit)) {
			$funds = $visit->unallocatedFunds;
			$unallocatedFunds = (float)$funds['total'];
		}
		if ($unallocatedFunds < 0) $unallocatedFunds = 0;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(sprintf('%.2f',$unallocatedFunds));
	}

	public function listPaymentHistoryAction() {
		$visitId = (int)$this->_getParam('visitId');
		$filters = array('visitId'=>$visitId,'unposted'=>true);
		$iterators = ClaimLine::getPaymentHistory($filters);
		$rows = array();

		foreach ($iterators as $iterator) {
			foreach ($iterator as $item) {
				$row = array();
				$row['data'] = array();
				if ($item instanceof PostingJournal) {
					$row['id'] = $item->postingJournalId;
					$row['data'][] = $item->datePosted;
					$row['data'][] = '';
					$row['data'][] = $item->amount;
					$row['data'][] = '';
					$row['data'][] = InsuranceProgram::getInsuranceProgram($item->payerId);
					$row['data'][] = $item->note;
					$row['data'][] = 'P';
				}
				else if ($item instanceof Payment) {
					$row['id'] = $item->paymentId;
					$row['data'][] = $item->paymentDate;
					$row['data'][] = '';
					$row['data'][] = $item->unallocated;
					$row['data'][] = '';
					$row['data'][] = InsuranceProgram::getInsuranceProgram($item->payerId);
					$row['data'][] = $item->title;
					$row['data'][] = 'U';
				}
				else {
					$row['id'] = $item->writeOffId;
					$row['data'][] = substr($item->timestamp,0,10);
					$row['data'][] = '';
					$row['data'][] = '';
					$row['data'][] = $item->amount;
					$row['data'][] = InsuranceProgram::getInsuranceProgram($item->payerId);
					$row['data'][] = $item->title;
					$row['data'][] = 'U';
				}
				$rows[] = $row;
			}
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function listCodesAction() {
		$claimId = (int)$this->_getParam('claimId');
		$visitId = (int)$this->_getParam('visitId');
		$rows = array();

		$visit = new Visit();
		$visit->visitId = $visitId;
		$visit->populate();
		$fees = $visit->calculateFees();
		foreach ($fees['details'] as $patientProcedureId=>$values) {
			$patientProcedure = $values['orm'];
			$claimLine = $patientProcedure->claimLine;
			$fee = $values['fee'];
			$feeDiscounted = $values['feeDiscounted'];

			$paid = $claimLine->totalPaid($claimId);
			$writeoff = $claimLine->totalWriteOff($claimId);
			$carry = $fee - ($paid + $writeoff);
			$row = array();
			$row['id'] = $patientProcedureId;
			$row['data'] = array();
			$row['data'][] = $claimLine->procedureCode.' : '.$claimLine->procedure;
			$row['data'][] = abs($fee);
			$row['data'][] = '';
			$row['data'][] = '';
			$row['data'][] = abs($carry);
			$row['data'][] = abs($paid);
			$row['data'][] = abs($writeoff);
			$rows[] = $row;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function electronicEobAction() {
		$this->render();
	}

	public function listEobFilesAction() {
		$rows = array();
		$row = array();
		$row['id'] = '1234';
		$row['data'] = array();
		$row['data'][] = '2011-01-10';
		$row['data'][] = 'Test EOB';
		$row['data'][] = '320138';
		$row['data'][] = 99.99;
		$rows[] = $row;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function processPaymentRebillToAction() {
		$claimId = (int)$this->_getParam('claimId');
		$payerId = (int)$this->_getParam('payerId');;
		$ret = false;
		if ($claimId > 0 && $payerId > 0) {
			$this->_sendToPayer($claimId,$payerId);
			$ret = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	protected function _sendToPayer($claimId,$payerId) {
		$id = WebVista_Model_ORM::nextSequenceId('claimSequences');
		$userId = (int)Zend_Auth::getInstance()->getIdentity()->personId;
		$iterator = new ClaimLineIterator();
		$iterator->setFilters(array('claimId'=>(int)$claimId));
		$visits = array();
		//$discountPayerId = InsuranceProgram::lookupSystemId('Discounts'); // ID of System->Discounts
		foreach ($iterator as $claimLine) {
			$oldPayerId = (int)$claimLine->insuranceProgramId;
			$claimLine->claimLineId = 0;
			$claimLine->claimId = $id;
			$claimLine->insuranceProgramId = (int)$payerId;
			$claimLine->dateTime = date('Y-m-d H:i:s');
			$oldBaseFee = (float)$claimLine->baseFee;
			$visitId = (int)$claimLine->visitId;
			if (!isset($visits[$visitId])) {
				$visit = new Visit();
				$visit->visitId = $visitId;
				$visit->populate();
				$visits[$visitId] = $visit;
			}
			$visit = $visits[$visitId];
			// recalculates baseFee for new payer
			$claimLine->recalculateBaseFee($visit);
			$newBaseFee = (float)$claimLine->baseFee;
			$claimLine->persist();

			// if new amount billed is less than than last amount billed then a writeoff is added to account for the difference
			if ($newBaseFee < $oldBaseFee) {
				$diff = $oldBaseFee - $newBaseFee;
				// add writeoffs
				$writeOff = new WriteOff();
				$writeOff->personId = (int)$visit->patientId;
				$writeOff->claimLineId = $claimLine->claimLineId;
				$writeOff->visitId = $visitId;
				$writeOff->appointmentId = $visit->appointmentId;
				$writeOff->amount = $diff;
				$writeOff->userId = $userId;
				$writeOff->timestamp = date('Y-m-d H:i:s');
				$writeOff->title = '';
				$writeOff->payerId = $oldPayerId;
				$writeOff->persist();
			}
		}
	}

	public function processPaymentSendToAction() {
		$claimId = (int)$this->_getParam('claimId');
		$payerId = 0;
		$insurance = $this->_getParam('insurance');
		switch ($insurance) {
			case 'Collections':
			case 'Patient Responsibility':
				foreach (InsuranceProgram::getInsurancePrograms() as $key=>$value) {
					if ($value == 'System->'.$insurance) {
						$payerId = (int)$key;
						break;
					}
				}
				break;
		}
		$ret = false;
		if ($claimId > 0 && $payerId > 0) {
			$this->_sendToPayer($claimId,$payerId);
			$ret = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($ret);
	}

	public function getClaimDetailsAction() {
		$claimId = (int)$this->_getParam('claimId');
		$data = array();
		$claimLine = new ClaimLine();
		$claimLine->claimId = $claimId;
		$claimLine->populateByClaimId();

		$visitId = (int)$claimLine->visitId;
		$visit = new Visit();
		$visit->visitId = $visitId;
		$visit->populate();
		$personId = (int)$visit->patientId;
		$data['claimId'] = $claimId;
		$data['dos'] = substr($visit->dateOfTreatment,0,10);
		$data['dateBilled'] = substr($claimLine->dateTime,0,10);
		$patient = new Patient();
		$patient->personId = $personId;
		$patient->populate();
		$data['patientId'] = $personId;
		$data['patient'] = $patient->displayName;
		$data['payerId'] = $claimLine->insuranceProgramId;
		$data['facility'] = $visit->facility;
		$checkNos = array(''=>array('checkNo'=>'','unallocated'=>''));
		foreach ($claimLine->checkNumbers as $chk) {
			$checkNos[(int)($chk['paymentId'])] = array('checkNo'=>$chk['checkNo'],'unallocated'=>(float)$chk['unallocated']);
		}
		$data['checkNos'] = $checkNos;

		$insured = new InsuredRelationship();
		$insured->personId = $personId;
		$data['listPayers'] = $insured->programList;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function listClaimLinesAction() {
		$claimId = (int)$this->_getParam('claimId');
		$rows = array();
		$iterator = new ClaimLineIterator();
		$iterator->setFilters(array('claimId'=>$claimId));
		foreach ($iterator as $claimLine) {
			$row = array();
			$row['id'] = $claimLine->claimLineId;
			$row['data'] = array();
			$row['data'][] = $claimLine->procedureCode.' : '.$claimLine->procedure; // Code
			//$billed = (float)$claimLine->amountBilled;
			$billed = (float)$claimLine->baseFee;
			$paid = (float)$claimLine->overallPayment;
			$writeOff = (float)$claimLine->overallWriteOff;
			$balance = $billed - ($paid + $writeOff);
			if ($balance < 0) $balance = 0; // in the case of rebill
			$row['data'][] = number_format($billed,2); // Amount Billed
			$row['data'][] = number_format($paid,2); // Paid
			$row['data'][] = number_format($writeOff,2); // WriteOff
			$row['data'][] = number_format($balance,2); // Balance
			$rows[] = $row;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function processCloseVisitAction() {
		$visitId = (int)$this->_getParam('visitId');
		$data = array();
		$visit = new Visit();
		$visit->visitId = $visitId;
		if ($visitId > 0 && $visit->populate()) {
			$visit->closed = 1;
			$visit->persist();
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function checkRulesAction() {
		$visitId = (int)$this->_getParam('visitId');
		$data = array();
		$visit = new Visit();
		$visit->visitId = $visitId;
		if ($visitId > 0 && $visit->populate()) {
			$retVisit = ClaimRule::checkRules($visit);
			$data = $retVisit->_claimRule;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function batchHistoryAction() {
		$this->view->balanceOperators = Claim::balanceOperators();
		$filters = $this->_session->filters;
		if (!isset($filters['total'])) {
			$filters['total'] = array('active'=>0,'operator'=>'=','operand1'=>'','operand2'=>'');
		}
		if (!isset($filters['billed'])) $filters['billed'] = '';
		if (!isset($filters['paid'])) $filters['paid'] = '';
		if (!isset($filters['writeoff'])) $filters['writeoff'] = '';
		if (!isset($filters['openClosed'])) $filters['openClosed'] = 2;
		$this->view->filters = $filters;
		$facilityIterator = new FacilityIterator();
		$facilityIterator->setFilter(array('Practice','Building','Room'));
		$facilities = array();
		foreach($facilityIterator as $facility) {
			$key = $facility['Practice']->practiceId.'_'.$facility['Building']->buildingId.'_'.$facility['Room']->roomId;
			$name = $facility['Practice']->name.'->'.$facility['Building']->name.'->'.$facility['Room']->name;
			$facilities[$key] = $name;
		}
		$this->view->facilities = $facilities;
		$this->render();
	}

	public function listFilesAction() {
		$filters = array();
		$iterator = new ClaimFileIterator();
		$iterator->setFilters($filters);
		foreach ($iterator as $claimFile) {
			$row = array();
			$id = (int)$claimFile->claimFileId;
			$row['id'] = $id;
			$row['data'] = array();
			$row['data'][] = $claimFile->dateTime;
			$row['data'][] = $claimFile->user->displayName;
			$row['data'][] = $claimFile->displayDestination;
			$row['data'][] = $claimFile->status;
			$rows[] = $row;
		}
		$data = array('rows'=>$rows);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function inquireAction() {
		$visitIds = $this->_getParam('ids');
		$data = array();
		foreach (explode(',',$visitIds) as $visitId) {
			$visitId = (int)$visitId;
			$data[$visitId] = ClaimFile::inquire($visitId);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processSaveCheckAction() {
		$params = $this->_getParam('payment');
		$data = false;
		if (is_array($params)) {
			$payment = new Payment();
			$payment->populateWithArray($params);
			$payment->paymentType = 'CHECK';
			$date = date('Y-m-d H:i:s');
			$payment->paymentDate = $date;
			$payment->timestamp = $date;
			$payment->persist();
			$data = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processSaveAdjustmentAction() {
		$params = $this->_getParam('adj');
		$data = false;
		if (is_array($params)) {
			$eobAdjustmentId = isset($params['eobAdjustmentId'])?(int)$params['eobAdjustmentId']:0;
			$eobAdjustment = new EobAdjustment();
			if ($eobAdjustment->eobAdjustmentId > 0) {
				$eobAdjustment->eobAdjustmentId = $eobAdjustmentId;
				$eobAdjustment->populate();
			}
			$eobAdjustment->populateWithArray($params);
			$eobAdjustment->persist();
			$data = $this->_generateAdjustmentRowData($eobAdjustment);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	protected function _generateAdjustmentRowData(EobAdjustment $eobAdjustment) {
		$row = array();
		$row['id'] = $eobAdjustment->eobAdjustmentId;
		$row['data'] = array();
		$row['data'][] = (int)$eobAdjustment->claimLineId;
		$row['data'][] = (string)$eobAdjustment->type;
		$row['data'][] = (string)$eobAdjustment->value;
		return $row;
	}

	public function listAdjustmentsAction() {
		$claimId = (int)$this->_getParam('claimId');
		$rows = array();
		$iterator = new EobAdjustmentIterator();
		$iterator->setFilters(array('claimId'=>$claimId));
		foreach ($iterator as $eobAdjustment) {
			$rows[] = $this->_generateAdjustmentRowData($eobAdjustment);
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function unallocatedFundsAction() {
		$personId = (int)$this->_getParam('personId');
		$person = new Person();
		$person->personId = $personId;
		$person->populate();
		$this->view->person = $person;
		$this->render();
	}

	public function listUnallocatedFundsAction() {
		$personId = (int)$this->_getParam('personId');
		$full = (int)$this->_getParam('full');
		$rows = array();
		$filters = array();
		$filters['personId'] = $personId;
		$iterator = new PaymentIterator();
		if ($full) { // ONLY payments that are 100% unallocated
			$filters['100%unallocated'] = true;
		}
		$iterator->setFilters($filters);
		foreach ($iterator as $payment) {
			$row = array();
			$row['id'] = $payment->paymentId;
			$row['data'][] = $payment->paymentDate;
			$row['data'][] = $payment->paymentType;
			$row['data'][] = number_format($payment->amount,2);
			$row['data'][] = $payment->title;
			$rows[] = $row;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function processDeleteUnallocatedFundsAction() {
		$paymentId = (int)$this->_getParam('paymentId');
		$payment = new Payment();
		$payment->paymentId = $paymentId;
		$payment->populate();
		// double check if paymentId is really an unallocated fund
		$result = false;
		if ($payment->allocated == 0
		    && $payment->visitId == 0
		    && $payment->appointmentId == 0) {
			$payment->setPersistMode(WebVista_Model_ORM::DELETE);
			$payment->persist();

			$funds = Payment::listUnallocatedFunds($payment->personId);
			$unallocatedFunds = (float)$funds['total'];
			if ($unallocatedFunds < 0) $unallocatedFunds = 0;
			$result = sprintf('%.2f',$unallocatedFunds);
		}

		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($result);
	}

}
