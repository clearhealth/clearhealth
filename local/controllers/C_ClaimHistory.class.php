<?php
$loader->requireOnce('includes/Grid_Renderer_AccountHistory.class.php');
$loader->requireOnce('datasources/Payment_EobAdjustment_DS.class.php');

class C_ClaimHistory extends Controller
{
	function actionView() {
		$post =& Celini::filteredPost();
		if ($post->exists('patient_id')) {
			$patientId = $post->getTyped('patient_id', 'int');
		}
		else {
			$patientId = $this->GET->getTyped('patient_id', 'int');
		}
		if ($post->exists('claim_id')) {
			$claimId = $post->getTyped('claim_id', 'int');
			$this->GET->set('claim_id',$claimId); // this is needed because the Patient_Claim_DS checks GET for claim_id which it shouldn't do
		}
		else {
			$claimId = $this->GET->getTyped('claim_id', 'int');
		}

		if ($post->exists('ajax')) {
			$this->view->assign('ajax',true);
		}

		$patient =& Celini::newORDO('Patient', $patientId);
		$claim =& Celini::newORDO('ClearhealthClaim', $claimId);
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

