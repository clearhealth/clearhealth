<?php
require_once CELLINI_ROOT ."/includes/Grid.class.php";

class C_FeeSchedule extends Controller {

	function edit_action($fee_schedule_id = 0) {

		$feeSchedule =& ORDataObject::Factory('feeSchedule',$fee_schedule_id);
		$this->Assign_By_ref('feeSchedule',$feeSchedule);
		$this->assign('FORM_ACTION',Cellini::link('edit'));
		return $this->fetch(Cellini::getTemplatePath("/fee_schedule/" . $this->template_mod . "_edit.html"));	
	}
}
?>
