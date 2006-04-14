<?php
$loader->requireOnce('includes/Grid_Renderer_AccountHistory.class.php');
$loader->requireOnce('datasources/Payment_EobAdjustment_DS.class.php');

class C_ClaimHistory extends Controller
{
	function actionView() {
		$patient =& Celini::newORDO('Patient', $this->GET->getTyped('patient_id', 'int'));
		$claim =& Celini::newORDO('ClearhealthClaim', $this->GET->getTyped('claim_id', 'int'));
		$renderer =& new Grid_Renderer_AccountHistory();
		
		$ds =& $patient->loadDatasource('ClaimHistory');
		$claimHistory =& new cGrid($ds, $renderer);
		$this->view->assign_by_ref('claimHistory', $claimHistory);
		
		$nds =& $claim->loadDatasource('Notes');
		$claimNotes =& new cGrid($nds);
		$this->view->assign_by_ref('claimNotes', $claimNotes);

		$adjustments = array();
		$ds =& $patient->loadDatasource('ClaimHistory');
		for($ds->rewind(); $ds->valid(); $ds->next()) {
			$row = $ds->get();
			if (isset($row['payment_id'])) {
				$pds =& new Payment_EobAdjustment_DS($row['payment_id']);
				if ($pds->numRows() > 0) {
					$adjustments[$row['payment_id']]['grid'] = new cGrid($pds);
					$adjustments[$row['payment_id']]['row'] = $row;
				}
			}

		}

		$this->view->assign_by_ref('adjustments',$adjustments);

		return $this->view->render('view.html');
	}
}

