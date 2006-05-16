<?php
$loader->requireOnce('/controllers/C_CRUD.class.php');
$loader->requireOnce("includes/Grid.class.php");
$loader->requireOnce('includes/transaction/TransactionManager.class.php');

class C_WidgetForm extends C_CRUD {
	
	var $_ordoName = "WidgetForm";
	var $form_data_id = 0;

	function actionShowForm_view($patient_id) {
		$p = Celini::newOrdo("Patient",$patient_id);
		
		$m = new Menu();
		$form =& Celini::newORDO("Form"); 
		$mf =& Celini::newORDO("MenuForm");
		
		$GLOBALS['loader']->requireOnce("datasources/WidgetForm_DS.class.php");
		$wfds = new WidgetForm_DS();
		
		$wfds->rewind();
		$widgets = array();
		$return_link = Celini::link(true,true, true, $patient_id);
		while(($row = $wfds->get()) && $wfds->valid()) {
			// Setup form data block 
			$wflist_ds = $p->loadDatasource('WidgetFormDataList');
			$wflist_ds->set_form_type($row["form_id"]);
			
			$wfDataGrid =& new cGrid($wflist_ds);
			$wfDataGrid->name = "wfDataGrid" . $row['form_id'];
			$wfDataGrid->registerTemplate('name','<a href="'.Celini::link('data','Form').'id={$form_data_id}&returnTo=' . $return_link . '">{$name}</a>');
			$wfDataGrid->pageSize = 10;
			$tmpar = array();
			$widgets[$row["name"]] = array("grid" => $wfDataGrid->render() , "form_link" => Celini::link('fillout',true,true, $row["form_id"]). "&returnTo=" . $return_link); 
			$wfds->next();
		}
		$this->assign_by_ref("widgets", $widgets);
		
		
		return $this->view->render("widgetformblock.html");
	}
	
	function actionFillout($form_id) {
		$GLOBALS['loader']->requireOnce("controllers/C_Form.class.php");
		$form_controller = new C_Form();
		return $form_controller->actionFillout_edit($form_id, $this->form_data_id);
	}
	
	function processFillout_edit($form_id) {
		$GLOBALS['loader']->requireOnce("controllers/C_Form.class.php");
		$form_controller = new C_Form();
		$form_controller->processFillout_edit($form_id, $this->form_data_id);
		$this->form_data_id = $form_controller->form_data_id;
		
	}

}
?>
