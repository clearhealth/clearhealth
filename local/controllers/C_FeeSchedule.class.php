<?php
require_once CELLINI_ROOT ."/includes/Grid.class.php";
require_once APP_ROOT ."/local/includes/FeeScheduleDatasource.class.php";

class C_FeeSchedule extends Controller {

	function default_action() {
		return $this->list_action();
	}

	function list_action() {

		$fs =& ORDataobject::factory('feeSchedule');
		$ds =& $fs->listFeeSchedules();
		$ds->template['label'] = '<a href="'.Cellini::link('edit').'id={$fee_schedule_id}">{$label}</a>';

		$grid =& new cGrid($ds);
		$this->assign_by_ref('grid',$grid);
		return $this->fetch(Cellini::getTemplatePath("/fee_schedule/" . $this->template_mod . "_list.html"));	
	}

	function edit_action($fee_schedule_id = 0) {

		$feeSchedule =& ORDataObject::Factory('feeSchedule',$fee_schedule_id);
		$this->Assign_By_ref('feeSchedule',$feeSchedule);
		$this->assign('FORM_ACTION',Cellini::link('edit',true,true,$fee_schedule_id));
		$this->assign('UPDATE_ACTION',Cellini::link('update',true,true,$fee_schedule_id));
		return $this->fetch(Cellini::getTemplatePath("/fee_schedule/" . $this->template_mod . "_edit.html"));	
	}

	function edit_action_process($fee_schedule_id = 0) {
		$feeSchedule =& ORDataObject::Factory('feeSchedule',$fee_schedule_id);
		$feeSchedule->populate_array($_POST['feeSchedule']);

		if ($feeSchedule->persist()) {
			if ($fee_schedule_id == 0) {
				$this->messages->addMessage('Fee Schedule Added');
			}
			else {
				$this->messages->addMessage('Fee Schedule Updated');
			}
		}
		else {
			$this->messages->addMessage('Error adding Fee Schedule');
		}
	}

	function update_action($fee_schedule_id) {

		$ds =& new FeeScheduleDatasource();
		$renderer = new Grid_Renderer_JS();
		$grid =& new cGrid($ds,$renderer);
		$grid->pageSize = 30;

		$this->assign_by_ref('grid',$grid);

		return $this->fetch(Cellini::getTemplatePath("/fee_schedule/" . $this->template_mod . "_update.html"));	
	}
}
?>
