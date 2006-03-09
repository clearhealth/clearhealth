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


		// where should this code live longterm, should i put it in a ds just so its in a standard place
		$sql = "
		select
			fsdi.family_size, fsdi.fee_schedule_discount_id level, fsdi.income, fsdl.discount, fsdl.disp_order
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
			$discountLevels[$res->fields['disp_order']] = round($res->fields['discount']); // should really only round if were .00
			$familySize[$res->fields['family_size']] = $res->fields['family_size'];
			$res->MoveNext();
		}
		$familySize = array_values($familySize);




		// default case
		if (count($cells) == 0) {
			$familySize = array(1,2,3,4,5,6,7,8,9,10);
			$discountLevels = array(25,50,75,100);

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
