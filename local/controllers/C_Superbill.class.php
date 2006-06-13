<?php
$loader->requireOnce('includes/Grid.class.php');
$loader->requireOnce('datasources/SuperbillList_DS.class.php');
$loader->requireOnce('datasources/Superbill_DS.class.php');

class C_Superbill extends Controller {
	var $_ordo = false;

	function actionList() {
		$ds =& new SuperbillList_DS();
		$ds->template['name'] = "<a href='".Celini::link('edit')."superbill_id={\$superbill_id}'>{\$name}</a>";
		$grid =& new cGrid($ds);

		$this->assign_by_ref('grid',$grid);

		return $this->view->render("list.html");
	}

	function actionAdd() {
		return $this->actionEdit();
	}

	function actionEdit() {
		$id = $this->getDefault('superbill_id',0);
		$session =& Celini::SessionInstance();
		$session->set('Superbill:id',$id);

		$head =& Celini::HTMLHeadInstance();
		$head->addJs('scriptaculous');
		$head->addExternalCss('suggest');

		$ajax =& Celini::AjaxInstance();
		$ajax->stubs[] = 'Controller';

		if (!$this->_ordo) {
			$this->_ordo =& Celini::newOrdo('Superbill',$id);
		}
		$this->view->assign_by_ref('superbill',$this->_ordo);

		$p =& Celini::newOrdo('Practice');
		$practices = $p->valueList('name');

		$this->view->assign('practices',$practices);
		$this->view->assign('FORM_ACTION',Celini::link('edit',true,false)."superbill_id=$id");

		$ds =& new Superbill_DS($id, 'procedure');
		$ds->registerTemplate('code','<a href="#remove" onclick="removeCode({$code_id},this)">{$code}</a>');
		$grid =& new cGrid($ds);
		$grid->orderLinks = false;
		$grid->indexCol = false;
		$this->view->assign_by_ref('procedureGrid',$grid);


		$ds =& new Superbill_DS($id, 'diagnosis');
		$ds->registerTemplate('code','<a href="#remove" onclick="removeCode({$code_id},this)">{$code}</a>');
		$grid =& new cGrid($ds);
		$grid->orderLinks = false;
		$grid->indexCol = false;
		$this->view->assign_by_ref('diagnosisGrid',$grid);


		if ($this->POST->get('ajax')) {
			return $this->view->render('chunkForm.html');
		}
		else {
			return $this->view->render('edit.html');
		}
	}
	
	function process($data) {
		if (!$this->_ordo) {
			$id = EnforceType::int($data['id']);
			$this->_ordo =& Celini::newOrdo('Superbill',$id);
		}
		$this->_ordo->populateArray($data);
		$this->_ordo->persist();
	}

	var $type = 'procedure';
	function actionAddCode() {
		$session =& Celini::SessionInstance();
		$id = $session->get('Superbill:id');

		$ds =& new Superbill_DS($id,$this->type);
		$ds->registerTemplate('code','<a href="#remove" onclick="removeCode({$code_id},this)">{$code}</a>');

		$grid =& new cGrid($ds);
		$grid->orderLinks = false;
		$grid->indexCol = false;
		return array('type'=>$this->type,'html'=>$grid->render());
	}

	function processAddCode() {
		$session =& Celini::SessionInstance();
		$id = $session->get('Superbill:id');
		$this->_ordo =& Celini::newOrdo('Superbill',$id);
		if ($id == 0) {
			$this->_ordo->persist();
			$id = $this->_ordo->get('id');
			$session->set('Superbill:id',$id);
		}

		$sd =& Celini::newOrdo('SuperbillData',array($id,$_POST['codeId']),'BySuperbillCode');
		$sd->set('status',1);
		$sd->persist();

		$code =& Celini::newOrdo('Code',$_POST['codeId']);
		switch($code->get('code_type')) {
			case 2: 
				$this->type = 'diagnosis';
				break;
			case 3:
			case 4:
				$this->type = 'procedure';
				break;
		}
	}

	function actionRemoveCode() {
	}
	function processRemoveCode() {
		$session =& Celini::SessionInstance();
		$id = $session->get('Superbill:id');
		$this->_ordo =& Celini::newOrdo('Superbill',$id);
		if ($id == 0) {
			$this->_ordo->persist();
			$id = $this->_ordo->get('id');
			$session->set('Superbill:id',$id);
		}

		$sd =& Celini::newOrdo('SuperbillData',array($id,$_POST['codeId']),'BySuperbillCode');
		$sd->set('status',0);
		$sd->persist();
	}
}
?>
