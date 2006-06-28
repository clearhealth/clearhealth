<?php
class FeeEstimator {

	/**
	 * Get the current fee for a code
	 *
	 * @todo grab the fee schedule from the current encounter if its set
	 */
	function standardFeeForCode($code,$modifier = -1) {

		Celini::newORDO('FeeSchedule');
		$feeSchedule =& FeeSchedule::defaultFeeSchedule();

		return $feeSchedule->getFee($code,$modifier);
	}

	function standardFeeForCodeId($codeId,$modifier = -1) {
		$c = EnforceType::int($codeId);
		$sql = "select code from codes where code_id = $c";
		$db = new clniDb();
		return $this->standardFeeForCode($db->getOne($sql),$modifier);
	}
}
?>
