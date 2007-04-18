<?php
class PCCAJAX {
	function initrefProgramMemberSlot() {
		$GLOBALS['loader']->requireOnce('ordo/refProgramMemberSlot.class.php');
		$instance =& new refProgramMemberSlot();

		$this->server->registerClass($instance,'refProgramMemberSlot',array('updateByExternalIDYearMonth'));
	}
	
	function initnonReferralUtility() {
		$GLOBALS['loader']->requireOnce('includes/nonReferralUtility.class.php');
		$class =& new nonReferralUtility();
		$this->server->registerClass($class,'nonReferralUtility',array('ajax_changeprovider'));
	}
}
?>
