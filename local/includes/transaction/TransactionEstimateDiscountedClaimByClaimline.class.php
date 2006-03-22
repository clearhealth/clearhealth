<?php
$loader->requireOnce('includes/transaction/TransactionEstimateClaim.class.php');

class TransactionEstimateDiscountedClaimByClaimline extends TransactionEstimateClaim {
	var $resultsInMap = false;
	var $discounts = array();
	var $fees = array();

	function setDiscount($discount, $code) {
		preg_match('/(\$?)(([0-9]*)\.?([0-9]*))(%?)/', $discount, $matches);
		list(,$isDollarSign, $realDiscount) = $matches;
		if (!empty($isDollarSign)) {
			$this->discounts[$code]['type'] = 'flat';
		}
		else {
			$this->discounts[$code]['type'] = 'percentage';
			
		}
		$this->discounts[$code]['discount'] = $realDiscount;
	}

	function processClaim() {
		$fees = parent::processClaim();
		$total = 0;
		foreach($fees as $key => $row) {
			if (!isset($this->discounts[$row['code']])) {
				$this->discounts[$row['code']] = 0;
			}
			if ($this->discounts[$row['code']]['type'] == 'flat') {
				$dfee = $this->discounts[$row['code']]['discount'];
			}
			else {
				$dfee = $row['fee'] * (1- ($this->discounts[$row['code']]['discount'] / 100));
			}

			$fees[$key]['fee'] = number_format($dfee,2);
			if ($key != count($fees)-1) {
				$total += $dfee;
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
}
?>
