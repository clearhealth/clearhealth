<?php
$loader->requireOnce('includes/EnumManager.class.php');
class C_OccurenceBreakdown extends Controller {

	function actionAdd() {
		return $this->actionEdit(0);
	}

	function actionEdit($id = -1) {

		$times = array();
		for($i = 5; $i < 65; $i+=5) {
			$times[$i*60] = $i;
		}
		$this->assign('times',$times);

		$manager =& EnumManager::getInstance();
		$pt = $manager->enumList('person_type');

		$types = array(0=>'All');
		for($pt->rewind(); $pt->valid(); $pt->next()) {
			$row = $pt->current();
			if ($row->extra1 == 1 && $row->status == 1) {
				$types[$row->key] = $row->value;
			}
		}
		$this->assign('types',$types);

		$ob =& Celini::newOrdo('OccurenceBreakdown');
		$breakdown = $ob->breakdownArray($id);

		if (count($breakdown) == 0) {
			$breakdown[] = array('length'=>0,'user_id'=>0,'occurence_breakdown_id'=>-1);
		}

		$this->assign('breakdown',$breakdown);
		
		return $this->view->render('edit.html');
	}

	function actionList() {
		$this->view->render('list');
	}

	function processAdd() {
		$id = $this->processEdit(0);
		header('Location: '.Celini::link('edit',true,true,$id));
	}

	function processEdit($template_id) {
		$breakdown = $this->POST->get('OccurenceBreakdown');

		foreach($breakdown as $key => $row) {
			if ($key !== 'id') {
				$ob =& Celini::newOrdo('OccurenceBreakdown');
				$ob->populateArray($row);
				$ob->set('occurence_id',$template_id);
				if ($key > 0) {
					$ob->set('id',$key);
				}
				$ob->persist();
			}
		}
	}
}
?>
