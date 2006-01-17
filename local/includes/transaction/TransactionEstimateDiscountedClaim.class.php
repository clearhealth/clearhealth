<?php
class TransactionEstimateDiscountedClaim extends TransactionEstimateClaim {
	var $discount = 1;

	function setDiscount($discount) {
		$this->discount = $discount/100;
	}

	function processClaim() {
		$fees = parent::processClaim();

		$total = 0;
		foreach($fees as $key => $row) {
			$dfee = $row['fee']*$this->discount;
			$fees[$key]['fee'] = number_format($dfee,2);
			if ($key != count($fees)-1) {
				$total += $dfee;
			}
		}
		// we retotal fee here in the hope to avoid rounding errors
		if (isset($key)) {
			$fees[$key]['fee'] = number_format($total,2);
		}
		return $fees;
	}
}
?>
