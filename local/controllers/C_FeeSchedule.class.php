<?php
$loader->requireOnce('includes/Grid.class.php');
$loader->requireOnce('includes/FeeScheduleDatasource.class.php');

class C_FeeSchedule extends Controller {
	var $_ordo = null;
	
	function actionUpdateFees() {
		$fsId = $this->getDefault('fee_schedule_id');

		$fs =& Celini::newOrdo('FeeSchedule',$fsId);

		$this->view->assign('EDIT_ACTION',Celini::link('UpdateFee','FeeSchedule',false).'fee_schedule_id='.$fsId.'&');

		$this->view->assign_by_ref('fs',$fs);

		$up =& Celini::getCurrentUserProfile();
		$cp = $up->getCurrentPracticeId();

		$sql = "select superbill_id, name from superbill where status = 1 and (practice_id = $cp or practice_id = 0)";
		$db = new clniDb();
		$this->view->assign('superbills',$db->getAssoc($sql));
		$this->view->assign('SUPERBILL_ACTION',Celini::link('superbill','FeeSchedule',false)."fee_schedule_id=$fsId&");

		$head =& Celini::HTMLHeadInstance();
		$head->addExternalCss('suggest');
		return $this->view->render('updateFees.html');
	}

	function actionUpdateFee() {
		$codeId = $this->GET->getTyped('code_id','int');
		$fsId = $this->GET->getTyped('fee_schedule_id','int');

		$code =& Celini::newOrdo('Code',$codeId);
		$fsd  =& Celini::newOrdo('FeeScheduleData',array($codeId,$fsId),'ByCodeFeeSchedule');

		$fs =& Celini::newOrdo('FeeSchedule',$fsId);
		$this->view->assign_by_ref('fs',$fs);

		$em =& Celini::enumManagerInstance();
		$modifiers = $em->enumArray('code_modifier');

		$modData = array();
		foreach($modifiers as $key => $mod) {
			$modData[$key] =& Celini::newOrdo('FeeScheduleDataModifier',array($fsId,$codeId,$key),'ByFeeScheduleCodeModifier');
		}

		$this->view->assign_by_ref('code',$code);
		$this->view->assign_by_ref('fsd',$fsd);
		$this->view->assign_by_ref('modData',$modData);
		$this->view->assign('modifiers',$modifiers);
		$this->view->assign('UPDATE_ACTION',Celini::link('UpdateFee','FeeSchedule',false).'fee_schedule_id='.$fsId.'&code_id='.$codeId);

		return $this->view->render('updateFee.html');
	}

	function processUpdateFee() {
		$codeId = $this->GET->getTyped('code_id','int');
		$fsId = $this->GET->getTyped('fee_schedule_id','int');

		$fsd  =& Celini::newOrdo('FeeScheduleData',array($codeId,$fsId),'ByCodeFeeSchedule');
		$fsd->populateArray($this->POST->get('FeeScheduleData'));
		$fsd->persist();

		$modData = $this->POST->get('FeeScheduleDataModifier');

		foreach($modData as $key => $mod) {
			if (!empty($mod['id']) && trim($mod['fee']) == '') {
				$m =& Celini::newOrdo('FeeScheduleDataModifier',$mod['id']);
				$m->drop();
			}
			else if (!trim($mod['fee']) == '') {
				$m =& Celini::newOrdo('FeeScheduleDataModifier',$mod['id']);
				$m->set('fee',$mod['fee']);
				$m->set('fee_schedule_id',$fsId);
				$m->set('code_id',$codeId);
				$m->set('modifier',$key);
				$m->persist();
			}
		}
		return '<br><div class="statusMessage"><div><h1>Fees Updated</h1></div></div>';
	}

	function actionSuperbill() {
		$superbillId = $this->GET->getTyped('superbill_id','int');
		$feeScheduleId = $this->GET->getTyped('fee_schedule_id','int');

		$GLOBALS['loader']->requireOnce('datasources/Superbill_DS.class.php');
		$ds = new Superbill_DS($superbillId,'procedure',$feeScheduleId);
		$ds->registerTemplate('code','<a href="#selectCode{$code}" onclick="selectCode(\'{$code_id}\')">{$code}</a>');


		$grid = new cGrid($ds);
		$this->view->assign_by_ref('grid',$grid);

		return $this->view->render('superbill.html');
	}

	function default_action() {
		return $this->list_action();
	}

	function actionList() {

		$fs =& ORDataobject::factory('FeeSchedule');
		$ds =& $fs->listFeeSchedules();
		$ds->template['label'] = '<a href="'.Celini::link('edit').'id={$fee_schedule_id}">{$label}</a>';

		$grid =& new cGrid($ds);
		$this->assign_by_ref('grid',$grid);

		return $this->view->render("list.html");
	}

	
	function actionAdd() {
		return $this->actionEdit(0);
	}
	
	function actionEdit($fee_schedule_id = 0) {
		if (!is_null($this->_ordo)) {
			$feeSchedule =& $this->_ordo;
			$fee_schedule_id = $feeSchedule->get('id');
		}
		else {
			$feeSchedule =& ORDataObject::Factory('FeeSchedule',$fee_schedule_id);
		}
		
		$this->assign_By_ref('feeSchedule',$feeSchedule);
		$this->assign('FORM_ACTION',Celini::link('edit',true,true,$fee_schedule_id));
		$this->assign('DEFAULT_ACTION',Celini::link('setdefault',true,true,$fee_schedule_id));
		$this->assign('UPDATE_ACTION',Celini::link('updateFees',true,true,$fee_schedule_id));

		return $this->view->render("edit.html");
	}

	function setdefault_action($fee_schedule_id = 0) {
		return $this->actionEdit($fee_schedule_id);
	}

	function setdefault_action_process($fee_schedule_id) {
		$feeSchedule =& ORDataObject::Factory('FeeSchedule',$fee_schedule_id);
		$feeSchedule->setDefaultValue($_POST['default_value']);
		$this->messages->addMessage('Default Value Set');

	}

	function processEdit($fee_schedule_id = 0) {
		$feeSchedule =& Celini::NewORDO('FeeSchedule',$fee_schedule_id);
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
		
		$this->_ordo =& $feeSchedule;
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

		return $this->view->render("update.html");
	}

}
?>
