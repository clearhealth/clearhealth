<?php
$loader->requireOnce('datasources/Lab_DS.class.php');
$loader->requireOnce('datasources/Order_DS.class.php');
class C_Labs extends Controller {

	function actionList() {
		$patientId = 0;
		if ($this->get('patient_id','c_patient') > 0) $patientId = $this->get('patient_id','c_patient');
		$ds =& new Lab_DS($patientId);
		$grid =& new cGrid($ds);

		$this->view->assign_by_ref('grid',$grid);
		return $this->view->render('list.html');
	}

	function actionOrderList() {
		$patientId = 0;
		if ($this->get('patient_id','c_patient') > 0) $patientId = $this->get('patient_id','c_patient');
		$ds =& new Order_DS($patientId);
		$grid =& new cGrid($ds);

		$this->view->assign_by_ref('grid',$grid);
		return $this->view->render('orderList.html');
	}

	function actionView($orderId) {

		$orderId = EnforceType::int($orderId);

		$db =& new clniDb();

		$order = $this->_getOrder($orderId);
		$tests = $this->_getTests($orderId);
		$results = $this->_getResults($orderId);
		$notes = $this->_getNotes($orderId);
		
		//var_dump($results);
		$config =& Celini::ConfigInstance();
		$settings = $config->get("labs");
		$this->assign('highlightAbnormal',$settings['highlightAbnormal']);
		$this->assign('order',$order);
		$this->assign('tests',$tests);
		$this->assign('results',$results);
		$this->assign('notes',$notes);

		return $this->view->render('view.html');
	}
	function actionViewByDay($date='') {
		if ($date == '') $date = date('Y-m-d');
		$patientId = 0;
                if ($this->get('patient_id','c_patient') > 0) $patientId = $this->get('patient_id','c_patient');
		$sql = "select lo.lab_order_id, MIN(lt.report_time) as time from lab_order lo inner join lab_test lt on lt.lab_order_id =lo.lab_order_id where patient_id = " . (int)$patientId . " group by lo.lab_order_id order by time DESC";
		$db = Celini::dbInstance();
		$labOrderIds = $db->getAll($sql);
		$labs = array();
		foreach ($labOrderIds as $labOrderId) {

		$orderId = $labOrderId['lab_order_id'];

		$order = $this->_getOrder($orderId);
		$tests = $this->_getTests($orderId);
		$results = $this->_getResults($orderId);
		$notes = $this->_getNotes($orderId);
		$labs[] = array('order'=>$order,'tests'=>$tests,'results'=>$results, 'notes'=>$notes);
		}		
		//var_dump($results);
		$config =& Celini::ConfigInstance();
		$settings = $config->get("labs");
		$this->assign('highlightAbnormal',$settings['highlightAbnormal']);
		$this->assign('labs',$labs);

		return $this->view->render('viewByDay.html');
	}
	function _getTests($orderId) {
		$ta = array();
		$db =& new clniDb();
		$sql = "select * from lab_test where lab_order_id = $orderId";
                $tests = array();
                $r = $db->execute($sql);
                while($r && !$r->EOF) {
                        $ta[$r->fields['lab_test_id']] = $r->fields;
                        $r->moveNext();
                }
		return $ta;

	}
	function _getResults($orderId,$finals = false) {
		$ta = array();
		$db =& new clniDb();
		$sql = "select distinct r.* from lab_result r inner join lab_test using(lab_test_id) where lab_order_id = $orderId";
		if ($finals) {
			$sql .= " and result_status = 'F'";
		}
                $results = array();
                $r = $db->execute($sql);
                while($r && !$r->EOF) {
                        $ta[$r->fields['lab_test_id']][] = $r->fields;
                        $r->moveNext();
                }
		return $ta;
	}
	function _getNotes($orderId) {
		$ta = array();
		$db =& new clniDb();
		$sql = "select distinct n.* from lab_note n inner join lab_test using(lab_test_id) where lab_order_id = $orderId";
                $r = $db->execute($sql);
                while($r && !$r->EOF) {
                        $ta[$r->fields['lab_test_id']] = $r->fields;
                        $r->moveNext();
                }
		return $ta;
	}
	function _getOrder($orderId) {
		$db =& new clniDb();
		$order = '';
		$sql = "select lo.*,concat(per.first_name, ' ', per.last_name, ' MRN: ', pat.record_number) as patient_info, MIN(lt.report_time) as report_time from lab_order lo 
		inner join lab_test lt on lt.lab_order_id = lo.lab_order_id
		inner join person per on per.person_id = lo.patient_id
		left join patient pat on pat.person_id = per.person_id
		where lo.lab_order_id = $orderId group by lab_order_id";
                $r = $db->execute($sql);
                if($r && !$r->EOF) {
                        $order = $r->fields;
                }
		return $order;
	}
	function actionEdit_edit($labOrderId=0) {
		$labOrderId = (int)$labOrderId;
		$externalId = $this->GET->get('externalId','int');
		if ($externalId > 0) {
			$this->set("externalId",$externalId);
		}
		$order= ORDataObject::factory("LabOrder",$labOrderId);
		$em =& Celini::enumManagerInstance();
                $this->view->assign('em',$em);	
                $this->view->assign('labBlock',$this->_generateEditView($labOrderId));	
		$this->assign('patientId',$this->get('patient_id', 'c_patient'));
		$this->assign("order",$order);
		return $this->view->render("manualEdit.html");
	}
	function _generateEditView($labOrderId) {
		$this->assign("editMode",true);
		$order = ORDataObject::factory("LabOrder",(int)$labOrderId);
		$this->assign("order",$order);
		$em =& Celini::enumManagerInstance();
                $this->view->assign('em',$em);	
		if ($order->get("lab_order_id") > 0 && count($this->_getResults($labOrderId,true)) > 0) {
                        return $this->actionView($order->get("lab_order_id"));
                }
		elseif (count($this->_getTests($labOrderId)) > 0) {
			if (!isset($this->view->_tpl_vars['note'])) {
			  $note_id = 0;
			  $notes = $this->_getNotes($labOrderId);
			  if (count($notes) > 0) {
				$notes = array_shift($notes);
				$note_id = $notes['lab_note_id'];
			  }
			  $note = ORDataObject::factory("LabNote",$note_id);
			  $this->assign("note",$note);
			}
			return $this->actionView($order->get("lab_order_id")) . $this->view->render('manualEditResultRow.html');
		}
		elseif($order->get("lab_order_id") > 0) {
			return $this->view->render("manualEditLabBlock.html");
		}
	}
	function ajaxEditOrder($data) {
		$labOrderId = 0;
		if (isset($data['lab_order_id'])) $labOrderId = (int)$data['lab_order_id'];
		$order = ORDataObject::factory("LabOrder",$labOrderId);
		$order->populateArray($data);
		if ($this->get('externalId') > 0) {
			$order->set('external_id',$this->get('externalId'));
			$this->set('externalId',0);
		}
		$order->persist();
		$this->messages->addMessage("Order added successfully");
		return $this->_generateEditView($order->get('lab_order_id'));
	}
	function ajaxEditTest($data) {
                $test = ORDataObject::factory("LabTest");
                $test->populateArray($data);
                $test->persist();
		$this->assign("test",$test);
                $this->messages->addMessage("Result Header added successfully");
                return $this->_generateEditView($test->get('lab_order_id'));
        }
	function ajaxEditResultRow($data) {
		$labNoteId = 0;
		if (isset($data['lab_note_id'])) $labNoteId = $data['lab_note_id'];
                $note = ORDataObject::factory("LabNote",$labNoteId);
		$note->set('lab_test_id',$data['lab_test_id']);
		$note->set('note',$data['note']);
		$note->persist();
		$this->assign("note",$note);
                $result = ORDataObject::factory("LabResult");
                $result->populateArray($data);
                $result->persist();
                $this->messages->addMessage("Result row added successfully");
                return $this->_generateEditView($data['lab_order_id']);

	}
	function ajaxDeleteResult($resultId) {
		$result = ORDataObject::factory("LabResult",(int)$resultId);
		$test = ORDataObject::factory("LabTest",$result->get('lab_test_id'));
		$result->drop();
                $this->messages->addMessage("Result row cleared");
                return $this->_generateEditView($test->get('lab_order_id'));
	}
	function ajaxConfirmResults($labOrderId) {
		$tests = $this->_getResults($labOrderId);
		$order = ORDataObject::factory("LabOrder",$labOrderId);
		$order->set('status','F');
		$order->persist();
		$string = '';
		foreach($tests as $test) {
		foreach($test as $result) {
			$robj = ORDataObject::factory("LabResult",$result['lab_result_id']);
			$robj->set('result_status',"F");
			$robj->persist();
		}
		}
		return $this->_generateEditView((int)$labOrderId).$string;
	}
}
?>
