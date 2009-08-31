<?php
$loader->requireOnce('includes/transaction/TransactionEstimateClaim.class.php');

class TransactionEstimateDiscountedClaim extends TransactionEstimateClaim {
	var $resultsInMap = false;
	var $discount = array('discount' => 0,'type'=>'percentage');

	function setDiscount($discount) {
		preg_match('/(\$?)(([0-9]*)\.?([0-9]*))(%?)/', $discount, $matches);
		list(,$isDollarSign, $realDiscount) = $matches;
		if (!empty($isDollarSign)) {
			$this->discount['type'] = 'flat';
		}
		else {
			$this->discount['type'] = 'percentage';
			
		}
		$this->discount['discount'] = $realDiscount;
	}

	function processClaim() {
		$fees = parent::processClaim();
		$total = 0;
		$counter = 0;
		foreach($fees as $key => $row) {
			if ($this->discount['type'] == 'flat' && $counter == 0) {
				$dfee = $this->discount['discount'];
			}
			else if ($this->discount['type'] == 'flat') {
				$dfee = 0;
			}
			else {
				$dfee = $row['fee']*(1- ($this->discount['discount'] / 100));
			}

			if ($this->resultsInMap) {
				$fees[$row['code']] = $dfee;
			}
			else {
				$fees[$key]['fee'] = number_format($dfee,2);
				if ($key != count($fees)-1) {
					$total += $dfee;
				}
			}
			$counter++;
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
		$fsdLevel =& Celini::newOrdo('FeeScheduleDiscountLevel',array($practiceId,$income,$familySize, $this->payerId),'ByPracticeIncomeSize');

		if ($fsdLevel->isPopulated()) {
			$this->setDiscount($fsdLevel->get('discount'));
		}
	}
}
?>
