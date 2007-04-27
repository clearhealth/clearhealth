<?php
$loader->requireOnce("includes/Grid.class.php");
$loader->requireOnce('includes/transaction/TransactionManager.class.php');
$loader->requireOnce('datasources/Person_SelfMgmtGoalsList_DS.class.php');
$loader->requireOnce('datasources/Person_GenericNotes_DS.class.php');

class C_MedicalHistory extends Controller {

	function __construct() {
		parent::Controller();
	}

	function actionView_view($personId = 0) {
		$personId = (int)$personId;
		if (!$personId > 0 ) {
			$personId = $this->get("patient_id","c_patient");
		}
		
		$p = Celini::newOrdo("Patient",$personId);
		
		$m = new Menu();
		$form =& Celini::newORDO("Form"); 
		$mf =& Celini::newORDO("MenuForm");
		
		$GLOBALS['loader']->requireOnce("datasources/WidgetForm_DS.class.php");
		$wfds = new WidgetForm_DS("*");
		
		$wfds->rewind();
		$widgets = array();
		$return_link = Celini::link(true,true, true, $personId);
		while(($row = $wfds->get()) && $wfds->valid()) {
			// Setup form data block 
			$wflist_ds = $p->loadDatasource('WidgetFormDataList');
			$wflist_ds->set_form_type($row["form_id"]);
			
			$wfDataGrid =& new cGrid($wflist_ds);
			$wfDataGrid->name = "wfDataGrid" . $row['form_id'];
			$wfDataGrid->registerTemplate('last_edit','<a href="'.Celini::link('data','Form').'id={$form_data_id}&returnTo=' . $return_link . '">{$last_edit}</a>');
			$wfDataGrid->pageSize = 10;
			$tmpar = array();
			$widgets[$row["name"]] = array("grid" => $wfDataGrid->render() , "form_add_link" => Celini::link('fillout',"Form",true, $row["form_id"]). "&returnTo=" . $return_link, "form_list_link" => Celini::link('list',"Form",true, $row["form_id"]). "&returnTo=" . $return_link); 
			$wfds->next();
		}
		$this->assign_by_ref("widgets", $widgets);
		
		return $this->view->render('view.html');
	}
}
?>
