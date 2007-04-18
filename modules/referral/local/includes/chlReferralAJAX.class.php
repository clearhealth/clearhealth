<?php

class chlReferralAJAX
{
	function initchlZipcodeLookup() {
		global $loader;
		$loader->requireOnce('includes/chlZipcodeLookup.class.php');
		$lookup =& new chlZipcodeLookup();

		$this->server->registerClass($lookup,'chlZipcodeLookup', $lookup->ajaxMethods());
	}
	
	function initC_Chlpatientquick() {
		global $loader;
		$loader->requireOnce('controllers/C_Chlpatientquick.class.php');
		$controller =& new C_Chlpatientquick();
		$this->server->registerClass($controller, 'C_Chlpatientquick', array('duplicateDisplay'));
	}
	
	function initC_Refpractice() {
		global $loader;
		//require_once APP_ROOT . '/modules/referral/includes/C_Refpractice.class.php';
		$loader->requireOnce('controllers/C_Refpractice.class.php');
		$controller =& new C_Refpractice();
		$this->server->registerClass($controller, 'C_Refpractice', $controller->ajaxMethods());
	}
	
	function initchlTestNames() {
		global $loader;
		$loader->requireOnce('includes/chlTestNames.class.php');
		$testNames =& new chlTestNames();
		$this->server->registerClass($testNames, 'chlTestNames', $testNames->ajaxMethods());
	}
	
	function initLockManager() {
		$GLOBALS['loader']->requireOnce('includes/LockManager.class.php');
		$lockManager =& new LockManager();
		$this->server->registerClass($lockManager, 'LockManager', array('hasOrdoTypeHappened'));
	}
}

