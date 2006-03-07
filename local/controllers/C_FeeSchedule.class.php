<?php
require_once CELINI_ROOT ."/includes/Grid.class.php";
require_once APP_ROOT ."/local/includes/FeeScheduleDatasource.class.php";

class C_FeeSchedule extends Controller {

	function default_action() {
		return $this->list_action();
	}

	function list_action() {

		$fs =& ORDataobject::factory('FeeSchedule');
		$ds =& $fs->listFeeSchedules();
		$ds->template['label'] = '<a href="'.Celini::link('edit').'id={$fee_schedule_id}">{$label}</a>';

		$grid =& new cGrid($ds);
		$this->assign_by_ref('grid',$grid);

		$this->view->path = 'fee_schedule';
		return $this->view->render("list.html");
	}

	function edit_action($fee_schedule_id = 0) {

		$feeSchedule =& ORDataObject::Factory('FeeSchedule',$fee_schedule_id);
		$this->assign_By_ref('feeSchedule',$feeSchedule);
		$this->assign('FORM_ACTION',Celini::link('edit',true,true,$fee_schedule_id));
		$this->assign('DEFAULT_ACTION',Celini::link('setdefault',true,true,$fee_schedule_id));
		$this->assign('UPDATE_ACTION',Celini::link('update',true,true,$fee_schedule_id));

		$this->view->path = 'fee_schedule';
		return $this->view->render("edit.html");
	}

	function setdefault_action($fee_schedule_id = 0) {
		return $this->edit_action($fee_schedule_id);
	}

	function setdefault_action_process($fee_schedule_id) {
		$feeSchedule =& ORDataObject::Factory('FeeSchedule',$fee_schedule_id);
		$feeSchedule->setDefaultValue($_POST['default_value']);
		$this->messages->addMessage('Default Value Set');

	}

	function edit_action_process($fee_schedule_id = 0) {
		$feeSchedule =& ORDataObject::Factory('FeeSchedule',$fee_schedule_id);
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

	function update_action($fee_schedule_id = 0) {

		$ds =& new FeeScheduleDatasource();
		$ds->reset();
		if ($fee_schedule_id > 0) {
			$feeSchedule =& ORDataObject::Factory('FeeSchedule',$fee_schedule_id);
			$ds->reset();
			$ds->addFeeSchedule($feeSchedule->get('name'),$feeSchedule->get('label'),$fee_schedule_id);
		}
		else {
			// add them all
			$feeSchedule =& ORDataObject::Factory('FeeSchedule',$fee_schedule_id);
			$ds->reset();

			$schedules = $feeSchedule->listFeeSchedules();
			$schedules = $schedules->toArray();

			foreach($schedules as $row) { 
				$ds->addFeeSchedule($row['name'],$row['label'],$row['fee_schedule_id']);
			}
		}
		$ds->_init_feeSessions();
		//echo $ds->preview();
		$renderer = new Grid_Renderer_JS();
		$grid =& new cGrid($ds,$renderer);
		//$grid->pageSize = 30;

		$this->assign_by_ref('grid',$grid);
		
		/*echo "fee1 26761 S2095:" . $feeSchedule->getFeeFromCodeId("26761") . "<br>";
		echo "fee2 26759 S2090: " . $feeSchedule->getFeeFromCodeId("26759") . "<br>";
		
		echo "fee3 S2095: " . $feeSchedule->getFee("S2095") . "<br>";
		echo "fee4 S2053: " . $feeSchedule->getFee("S2053") . "<br>";
		echo "fee4 S2052: " . $feeSchedule->getFee("S2052") . "<br>";
		echo "fee4 S2085: " . $feeSchedule->getFee("S2085") . "<br>";*/

		$this->view->path = 'fee_schedule';
		return $this->view->render("update.html");
	}
}
?>
