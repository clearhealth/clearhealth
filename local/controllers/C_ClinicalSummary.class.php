<?php
$loader->requireOnce("includes/Grid.class.php");
$loader->requireOnce('includes/transaction/TransactionManager.class.php');
$loader->requireOnce('datasources/Person_ClinicalSummary_DS.class.php');

class C_ClinicalSummary extends Controller {

	function __construct() {
		parent::Controller();
	}

	function actionView_view($personId = 0) {
		if (!$personId > 0 ) {
                        $personId = $this->get("patient_id","c_patient");
                }
		$personId = (int)$personId;
		$csDS = new Person_ClinicalSummary_DS($personId);
		$csGrid = new cGrid($csDS);
		$csGrid->name = "clinicalSummaryGrid";
		$csGrid->setPageSize(20);
		$this->assign('csGrid',$csGrid);
		return $this->view->render('view.html');

	}

}
?>
