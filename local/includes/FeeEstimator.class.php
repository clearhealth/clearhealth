<?php
class FeeEstimator {

	/**
	 * Get the current fee for a code
	 *
	 * @todo grab the fee schedule from the current encounter if its set
	 */
	function standardFeeForCode($code,$modifier = -1,$feeScheduleName = false) {
		if ($feeScheduleName == false) {
			Celini::newORDO('FeeSchedule');
			$feeSchedule =& FeeSchedule::defaultFeeSchedule();
		}
		else {
			$feeSchedule =& Celini::newOrdo('FeeSchedule',$feeScheduleName,'ByName');
		}

		return $feeSchedule->getFee($code,$modifier);
	}

	function standardFeeForCodeId($codeId,$modifier = -1,$feeScheduleName = false) {
		$c = EnforceType::int($codeId);
		$sql = "select code from codes where code_id = $c";
		$db = new clniDb();
		return $this->standardFeeForCode($db->getOne($sql),$modifier,$feeScheduleName);
	}
}
?>
