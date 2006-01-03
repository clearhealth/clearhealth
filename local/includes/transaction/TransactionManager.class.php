<?php
class TransactionManager {

	/**
	 * Create a new transaction object
	 */
	function createTransaction($type) {
		$class = "Transaction$type";
		if (!class_exists($class)) {
			$GLOBALS['loader']->requireOnce("includes/transaction/$class.class.php");
		}
		$trans = new $class();
		return $trans;
	}

	/**
	 * Apply a transaction
	 */
	function processTransaction($transaction) {
		return $transaction->processClaim();
	}
}
?>
