<?php

$loader->requireOnce('controllers/Controller.class.php');

class C_Refpatient extends Controller
{	
	function actionInfo($patient_id) {
		$patient =& Celini::newORDO('refPatient', (int)$patient_id);
		$this->view->assign_by_ref('patient', $patient);
		
		return $this->view->render('info.html');
		//return "patient info";
	}
	
	function actionViewHistory_list($patient_id) {
		$patient =& Celini::newORDO('refPatient', $this->_enforcer->int($patient_id));
		$requestList =& $patient->loadDatasource('refRequestListKept');
		
		$historyGrid =& new cGrid(&$requestList);
		$historyGrid->name = "formDataGrid";
		$historyGrid->indexCol = false;
		$historyGrid->prepare();
		
		$this->view->assign('historyGrid', $historyGrid);
		$this->view->assign('embedded', $this->GET->exists('embedded'));
		return $this->view->render('viewhistory.html');
	}
}
?>
