<?php

$loader->requireOnce("controllers/Controller.class.php");
$loader->requireOnce("includes/Grid.class.php");
$loader->requireOnce("includes/Datasource_AccountHistory.class.php");
$loader->requireOnce("includes/Grid_Renderer_AccountHistory.class.php");
$loader->requireOnce("datasources/AccountNote_DS.class.php");
$loader->requireOnce("lib/PEAR/HTML/AJAX/Serializer/JSON.php");

$loader->requireOnce('datasources/MasterClaimList_DS.class.php');
$loader->requireOnce('datasources/MasterAccountHistory_DS.class.php');

/**
 * Actions for working with an Account in Clearhealth, in this context this is a Patients Account
 *
 * Current contains an procedure/payment report
 */
class C_MasterAccountHistory extends Controller {

	var $filters = "";
	var $_historyGrids = array();

	function C_MasterAccountHistory ($template_mod = "general") {
		parent::Controller();
		$this->template_mod = $template_mod;

		//unset($_SESSION['clearhealth']['filters'][get_class($this)]);
		if (!isset($_SESSION['clearhealth']['filters'][get_class($this)])) {
			$_SESSION['clearhealth']['filters'][get_class($this)] = array();
		}
		$this->filters = $_SESSION['clearhealth']['filters'][get_class($this)];
		$this->assign('filters',$this->filters);
	}

	function actionDefault_view() {
		return $this->actionView();
	}
	
	function actionView() {
		// setup filter display
		$building =& Celini::newORDO('Building');
		$this->view->assign('roomList', $building->valueList());
		
		$patient =& Celini::newORDO('Patient');
		$this->view->assign('patientList', $patient->valueList());
		
		$fbClaim =& Celini::newORDO('FBClaim');
		$this->view->assign('statusList', $fbClaim->valueList('status'));
		
		$insuranceProgram =& Celini::newORDO('InsuranceProgram');
		$this->view->assign('programList', $insuranceProgram->valueList('programs'));
		
		$provider =& Celini::newORDO('Provider');
		$this->view->assign('providerList', $provider->valueList());
		
		// setup actual grid
		$ds =& new MasterAccountHistory_DS($this->filters);
		$renderer =& new Grid_Renderer_AccountHistory();
		$accountHistoryGrid =& new cGrid($ds, $renderer);
		
		$this->view->assign_by_ref('accountHistoryGrid', $accountHistoryGrid);
		
		$this->view->assign('FILTER_ACTION', Celini::link('view', 'MasterAccountHistory'));
		return $this->view->render('view.html');
	}
	
	function processView() {
		if ($this->POST->exists('filter')) {
			$this->filters = $_SESSION['clearhealth']['filters'][get_class($this)] = $_POST['filter'];
		}
		$this->assign("filters",$this->filters);

	}
}

?>
