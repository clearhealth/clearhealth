<?php
/**
 * Controller for editing a clearhealth practice
 */
class C_Practice extends Controller {
	var $location = false;

	function actionAdd() {
		return $this->actionEdit(0);
	}

	function actionEdit($id = 0) {
		if (!is_object($this->location)) {
			$this->location =& Celini::newORDO('Practice',$id);
		}
		
		$this->assign_by_ref("practice",$this->location);
		
		$this->assign("process",true);
		$this->assign("FORM_ACTION",Celini::link('edit',true,true,$id));
		return $this->view->render("edit.html");
	}
	
	function processEdit($id) {
		if ($_POST['practice_id'] == 0) {
			$this->sec_obj->acl_qcheck("add",$this->_me,"","practice",$this,false);
		}

		$this->location =& Celini::newORDO('Practice',$_POST['practice_id']);
		$this->location = new Practice($_POST['practice_id']);
		$this->location->populate_array($_POST);
		$this->location->persist();
		
		$this->location->populate();
		$_POST['process'] = "";
	}
	

}
?>
