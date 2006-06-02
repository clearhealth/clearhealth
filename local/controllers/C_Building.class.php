<?php
$loader->requireOnce('ordo/FacilityCode.class.php');

class C_Building extends Controller
{
	var $_ordo = null;
	
	function C_Building() {
		parent::Controller();
		$this->view->path = 'location';
	}
	
	function actionAdd() {
		return $this->actionEdit(0);
	}
	
	function actionEdit($id = 0) {
		if (!is_object($this->_ordo)) {
			$this->_ordo = Celini::newORDO('Building', $id);
		}
		
		$this->assign("building",$this->_ordo);
		$s =& Celini::newORDO('Practice');
		$this->assign("practices",$this->utility_array($s->practices_factory(),"id","name"));
		
		$fc = &new FacilityCode();
		$this->assign('facilityCodeList', $fc->valueListForDropDown()); 

		$this->assign("process",true);
		$this->view->assign('FORM_ACTION', Celini::link('edit', 'Building', true, $this->_ordo->get('id')));
		return $this->view->render("edit_building.html");
	}
	
	
	function processEdit() {
		$this->_ordo =& Celini::newORDO('Building', $_POST['id']);
		$this->_ordo->populate_array($_POST);
		$this->_ordo->set('identifier',$_POST['identifier']);
		$this->_ordo->persist();
	}

}

