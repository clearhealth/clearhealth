<?php
$loader->requireOnce('datasources/FeeScheduleDiscount_DS.class.php');
class C_FeeScheduleDiscount extends Controller {

	function actionList() {

		$ds = new FeeScheduleDiscount_DS();
		$grid =& new cGrid($ds);
					
		$this->view->assign_by_ref('fsd', Celini::newORDO('FeeScheduleDiscount'));
		$this->view->assign('ADD_ACTION', Celini::link('add', 'FeeScheduleDiscount'));
		
		$this->view->assign_by_ref('grid',$grid);
		return $this->view->render('list.html');
	}
	
	
	function actionAdd() {
		$practiceId = $this->GET->getTyped('practiceId','int');
	
			
	
		$p =& Celini::newOrdo('Practice',$practiceId);
		$pName = $p->get('name');

		$fsd =& Celini::newOrdo('FeeScheduleDiscount');
		$fsd->set('practice_id',$practiceId);
		$fsd->set('name',"$pName Discount Table");
		$fsd->persist();

		return $this->actionEdit($fsd->get('id'));
	}

	function actionEdit($fsdId) {
		$fsdId = EnforceType::int($fsdId);
		$fsd =& Celini::newOrdo('FeeScheduleDiscount',$fsdId);

		$db = new clniDb();
		$practiceId = $fsd->practice_id;
		$sql = "SELECT count(*) AS count FROM fee_schedule_discount WHERE practice_id = $practiceId and type = 'default'";
		$result = $db->execute($sql);
		$this->view->assign('defaultExists',$result->fields['count']);

		// where should this code live longterm, should i put it in a ds just so its in a standard place
		$sql = "
		select
			fsdi.family_size, fsdi.fee_schedule_discount_id level, fsdi.income, fsdl.discount,fsdl.type, fsdl.disp_order
		from
			fee_schedule_discount_income fsdi
			inner join fee_schedule_discount_level fsdl using(fee_schedule_discount_level_id)
		where
			fsdi.fee_schedule_discount_id = $fsdId
		order by
			family_size,disp_order
		";

		$cells = array();
		$discountLevels = array();
		$familySize = array();
		$res = $db->execute($sql);
		while($res && !$res->EOF) {
			$l = $res->fields['disp_order'];
			$fs = ($res->fields['family_size']-1);
			$cells[$fs][$l] = array('size'=>$fs,'level'=>$l,'value'=>$res->fields['income']);
			if($res->fields['type'] == 'percent'){
				$discountLevels[$res->fields['disp_order']] = $res->fields['discount']."%";//round(); // should really only round if were .00
			}else{
				$discountLevels[$res->fields['disp_order']] = "$".$res->fields['discount'];//round(); // should really only round if were .00
			
			}
			
			$familySize[$res->fields['family_size']] = $res->fields['family_size'];
			$res->MoveNext();
		}
		$familySize = array_values($familySize);

		// default case
		if (count($cells) == 0) {
			$familySize = array(1,2,3,4,5,6,7,8,9,10);
			$discountLevels = array('25.00%','50.00%','75.00%','100.00%');

			foreach(array_keys($familySize) as $size) {
				foreach(array_keys($discountLevels) as $level) {
					$cells[$size][$level] = array('size'=>$size,'level'=>$level,'value'=>0);
				}
			}
		}

		$numLevels = count($discountLevels);
		$maxFamilySize = count($familySize);
				
		$this->view->assign('familySize',$familySize);
		$this->view->assign('discountLevels',$discountLevels);
		$this->view->assign('maxFamilySize',$maxFamilySize);
		$this->view->assign('numLevels',$numLevels);
		$this->view->assign('cells',$cells);
		$this->view->assign('FORM_ACTION',Celini::link(true,true,true,$fsdId));
		$this->view->assign_by_ref('fsd',$fsd);
		
		return $this->view->render('edit.html');
	}

	function process($data) {
		
		
		$fsdId = $this->GET->get(0);
		$type = $data['type'];	
		$program = $data['insurance_program_id'];
				
		$fsd =& Celini::newOrdo('FeeScheduleDiscount',$fsdId);
		$practice_id = $fsd->practice_id;
		
		unset ($data['insurance_program_id']);
		unset ($data['type']);
		
		//error checking
		$db = new clniDb();
		
		if($type == 'program'){
		
			$sql="select count(*) as count from fee_schedule_discount where insurance_program_id = $program and practice_id = $practice_id and fee_schedule_discount_id <> $fsdId";
			$result=$db->execute($sql);
			if( $result->fields['count'] > 0){
				//this means there is allready a schedule for that program 
				$this->messages->addMessage('Insurance program conflict', 'Please choose another insurance program');
			}else{
				$fsd->set('insurance_program_id',$program);
				$fsd->set('type',$type);
			}
				
		}else{
			$sql="select count(*) as count from fee_schedule_discount where type = 'default' and  practice_id = $practice_id and fee_schedule_discount_id <> $fsdId";
			$result = $db->execute($sql);
			if($result->fields['count'] > 0){
				$this->messages->addMessage('Conflict', 'You have allready designated another Fee Schedule Discount as Default');
			}else{
				$fsd->set('insurance_program_id','0');
				$fsd->set('type',$type);
			}
		}
		
		$fsd->persist();

		///below this pertains to fsdl//
		$levels = $data['level'];
		$originalLevels = $data['original_level'];
		unset($data['level']);
		unset($data['original_level']);

		$levelMap = array();
		foreach($originalLevels as $key => $discount) {
			$fsdl =& Celini::newOrdo('FeeScheduleDiscountLevel',array($fsdId,$discount),'byDiscount');
			$fsdl->set('disp_order',$key);
			$fsdl->set('discount', $levels[$key]);
			$fsdl->persist();

			$levelMap[$key] = $fsdl->get('id');
		}

		foreach($data as $key => $row) {
			$size = $key+1;

			foreach($row as $level => $income) {
				$fsdi =& Celini::newOrdo('FeeScheduleDiscountIncome',array($fsdId,$size,$levelMap[$level]),'byFamilySizeLevel');
				$fsdi->set('income',$income);
				$fsdi->persist();
			}
		}
		
		if (Celini::getCurrentAction() == 'add') {
			Celini::redirectURL(Celini::link('edit', true, true, $fsdId));
		}
	}
}
?>
