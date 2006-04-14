<?php
$loader->requireOnce('includes/Grid_Renderer_AccountHistory.class.php');

class C_ClaimHistory extends Controller
{
	function actionView() {
		$patient =& Celini::newORDO('Patient', $this->GET->getTyped('patient_id', 'int'));
		$claim =& Celini::newORDO('ClearhealthClaim', $this->GET->getTyped('claim_id', 'int'));
		$renderer =& new Grid_Renderer_AccountHistory();
		
		$ds =& $patient->loadDatasource('ClaimHistory');
		$claimHistory =& new cGrid($ds, $renderer);
		$this->view->assign_by_ref('claimHistory', $claimHistory);
		
		$ds =& $claim->loadDatasource('Notes');
		$claimNotes =& new cGrid($ds);
		$this->view->assign_by_ref('claimNotes', $claimNotes);
		
		$paymentCollection =& $claim->getChildren('eobAdjustments');
		$i = 0;
		var_dump($paymentCollection->count());
		while ($paymentCollection->valid()) {
			$cur =& $paymentCollection->current();
			echo "Found $i:" . $cur->name() . '<br />';
			$i++;
			
			$paymentCollection->next();
		}
		
		return $this->view->render('view.html');
	}
}

