<?php
$loader->requireOnce('datasources/Lab_DS.class.php');
$loader->requireOnce('datasources/Order_DS.class.php');
class C_Labs extends Controller {

	function actionList() {
		$patientId = 0;
		if ($this->get('patient_id','c_patient') > 0) $patient_id = $this->get('patient_id','c_patient');
		$ds =& new Lab_DS($patientId);
		$grid =& new cGrid($ds);

		$this->view->assign_by_ref('grid',$grid);
		return $this->view->render('list.html');
	}
	function actionOrderList() {
		$patientId = 0;
		if ($this->get('patient_id','c_patient') > 0) $patient_id = $this->get('patient_id','c_patient');

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
		$sql = "select * from lab_order where lab_order_id = $orderId";
                $r = $db->execute($sql);
                if($r && !$r->EOF) {
                        $order = $r->fields;
                }
		return $order;
	}
	function actionEdit_edit($labOrderId=0) {
		$labOrderId = (int)$labOrderId;
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
