<?php

$loader->requireOnce("controllers/Controller.class.php");
$loader->requireOnce("includes/Grid.class.php");
$loader->requireOnce("datasources/Patient_AccountHistory_DS.class.php");
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

		$provider =& Celini::newORDO('Provider');
		$user =& Celini::newOrdo('User');
		$this->view->assign('userList', $user->valueList('username'));
		
		// setup actual grid
		$ds =& new MasterAccountHistory_DS($this->filters);

		// big hack to add totals

		$sql = $ds->claims->_query;
		$sql['cols'] = "chc.claim_id";
		$db = new clniDb();
		$claimssql = "SELECT {$sql['cols']} FROM {$sql['from']} ";
		$claimssql .= $sql['where'] != '' ? "WHERE {$sql['where']}" : '';
		$claimssql .= " GROUP BY {$sql['groupby']}";
		$res = $db->execute($claimssql);
		$claims = array();
		while($res && !$res->EOF) {
			$claims[] = $res->fields['claim_id'];
			$res->MoveNext();
		}
		//var_dump($claimssql);
		$sql['cols'] = "
			SUM(sums.billed) billed,
			SUM(sums.paid) paid,
			SUM(sums.billed)-SUM(sums.paid)-SUM(w.total_writeoff) balance,
			SUM(w.total_writeoff) writeoff
			";

		// no more groupby we want a single total row
		unset($sql['groupby']);

		// you can't join on payment and payment_claimline and sum up chc.* anymore
		$sql['from'] = str_replace(
			array(
			'LEFT JOIN payment AS pa ON(pa.foreign_id = chc.claim_id)',
			'LEFT JOIN payment_claimline AS pcl ON(pcl.payment_id = pa.payment_id)',
			'LEFT JOIN fbclaimline c ON fbc.claim_id = c.claim_id',
			'LEFT JOIN codes AS c ON pcl.code_id = c.code_id'),
				'',$sql['from']);


//		$sql['where'] = str_replace('c.code','w.codes',$sql['where']);
		$sql['where'] = "chc.claim_id IN('".implode("','",$claims)."')";
		// use a subquery to get writeoff totals
		$sql['from'] = '
		clearhealth_claim chc
		LEFT JOIN (
			select chc.claim_id,
			SUM(chc.total_billed) billed,
			SUM(chc.total_paid) paid,
			(SUM(chc.total_billed) - SUM(chc.total_paid)) billedminuspaid
			FROM
			clearhealth_claim chc
			GROUP BY claim_id
		) sums ON(sums.claim_id=chc.claim_id)
		LEFT JOIN ( 
			select chc.claim_id,
			SUM(ifnull(pcl.writeoff, 0)) total_writeoff,
			group_concat(DISTINCT c.code) codes 
			FROM clearhealth_claim chc 
			LEFT JOIN payment AS pa ON(pa.foreign_id = chc.claim_id) 
			LEFT JOIN payment_claimline AS pcl ON(pcl.payment_id = pa.payment_id) 
			LEFT JOIN codes c USING(code_id)
			GROUP BY chc.claim_id
		) w ON(w.claim_id = chc.claim_id)  
		';

		$totalDs = new Datasource_sql();
		$totalDs->setup(Celini::dbInstance(),$sql,false);


		$totalGrid =& new cGrid($totalDs);
		$totalGrid->orderLinks = false;
		$totalGrid->indexCol = false;
		
		$renderer =& new Grid_Renderer_AccountHistory();
		$accountHistoryGrid =& new cGrid($ds, $renderer);
		$accountHistoryGrid->pageSize = 10;
		$accountHistoryGrid->hideExportLink = false;
		$accountHistoryGrid->setExternalId('MasterAccountHistory');
		
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
