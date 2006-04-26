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

		$head =& Celini::HTMLHeadInstance();
		$head->addJs('scriptaculous');
		$ajax =& Celini::ajaxInstance();
		$ajax->stubs[] = 'controller';
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


		// big hack to add totals

		$sql = $ds->claims->_query;

		$sql['cols'] = "
			SUM(chc.total_billed) billed,
			SUM(chc.total_paid) paid,
			SUM(chc.total_billed) - SUM(chc.total_paid) - SUM(ifnull(pcl.writeoff,0)) AS balance,
			SUM(ifnull(pcl.writeoff,0)) AS writeoff";
		unset($sql['groupby']);
		$totalDs = new Datasource_sql();
		$totalDs->setup(Celini::dbInstance(),$sql,false);


		$totalGrid =& new cGrid($totalDs);
		$totalGrid->orderLinks = false;
		$totalGrid->indexCol = false;
		
		$renderer =& new Grid_Renderer_AccountHistory();
		$accountHistoryGrid =& new cGrid($ds, $renderer);
		$accountHistoryGrid->pageSize = 10;
		
		$this->view->assign_by_ref('accountHistoryGrid', $accountHistoryGrid);
		$this->view->assign_by_ref('totalGrid', $totalGrid);
		
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
