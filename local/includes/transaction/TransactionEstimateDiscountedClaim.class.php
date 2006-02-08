<?php
$loader->requireOnce('includes/transaction/TransactionEstimateClaim.class.php');

class TransactionEstimateDiscountedClaim extends TransactionEstimateClaim {
	var $resultsInMap = false;
	var $discount = 0;

	function setDiscount($discount) {
		$this->discount = $discount/100;
	}

	function processClaim() {
		$fees = parent::processClaim();
		$total = 0;
		foreach($fees as $key => $row) {
			$dfee = $row['fee']*(1-$this->discount);

			if ($this->resultsInMap) {
				$fees[$row['code']] = $dfee;
			}
			else {
				$fees[$key]['fee'] = number_format($dfee,2);
				if ($key != count($fees)-1) {
					$total += $dfee;
				}
			}
		}
		// we retotal fee here in the hope to avoid rounding errors
		if (!$this->resultsInMap) {
			if (isset($key)) {
				$fees[$key]['fee'] = number_format($total,2);
			}
		}
		return $fees;
	}

	function setAllFromEncounterId($encounterId) {
		$this->encounterId = $encounterId;

		$encounter =& Celini::newOrdo('Encounter',$encounterId);
		$this->payerId = $encounter->get('current_payer');

		// calculate discount
		$ps =& Celini::newOrdo('PatientStatistics',$encounter->get('patient_id'));
		$familySize = $ps->get('family_size');
		$income = $ps->get('monthly_income');
		$practiceId = $_SESSION['defaultpractice'];
		$fsdLevel =& Celini::newOrdo('FeeScheduleDiscountLevel',array($practiceId,$income,$familySize),'ByPracticeIncomeSize');

		if ($fsdLevel->isPopulated()) {
			$this->discount = $fsdLevel->get('discount')/100;
		}
	}
}
?>
