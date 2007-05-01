<?php
$loader->requireOnce('/controllers/C_CRUD.class.php');
$loader->requireOnce("includes/Grid.class.php");
$loader->requireOnce('includes/transaction/TransactionManager.class.php');
$loader->requireOnce('datasources/FormDataByExternalByFormId_DS.class.php');

class C_WidgetForm extends Controller {
	
	var $_ordoName = "WidgetForm";
	var $form_data_id = 0;
	var $formId = 0;
	var $widget_form_id = '';
	var $column_id = '';
	var $controller_name = '';

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
			$wfDataGrid->registerTemplate('last_edit','<a href="'.Celini::link('data','Form').'id={$form_data_id}&returnTo=' . $return_link . '">{$last_edit}</a>');
			$wfDataGrid->pageSize = 10;
			$tmpar = array();
			$widgets[$row["name"]] = array("grid" => $wfDataGrid->render() , "form_add_link" => Celini::link('fillout',"Form",true, $row["form_id"]). "&returnTo=" . $return_link, "form_list_link" => Celini::link('list',"Form",true, $row["form_id"]). "&returnTo=" . $return_link); 
			$wfds->next();
		}
		$this->assign_by_ref("widgets", $widgets);
		
		return $this->view->render("widgetformblock.html");
	}
	
	function actionShowCritical_view($patient_id) {
		$p = Celini::newOrdo("Patient",$patient_id);

		$m = new Menu();
		$form =& Celini::newORDO("Form"); 
		$mf =& Celini::newORDO("MenuForm");
		
		$GLOBALS['loader']->requireOnce("datasources/WidgetForm_DS.class.php");
		$wfds = new WidgetForm_DS('2,3,4');

		$wfds->rewind();
		$widgets = array();
		$return_link = Celini::link(true,true, true, $patient_id);
		while(($row = $wfds->get()) && $wfds->valid()) {
			// Setup form data block
			$wflist_ds = '';
			// 4 is straight controller
			if ($row['type'] == 4) {
				$dsName = "WidgetForm_" . $row['controller_name'] . "_DS";
				$GLOBALS['loader']->requireOnce('datasources/' . $dsName . ".class.php");
				$wflist_ds = new $dsName($patient_id);
			}
			else {
                        $wflist_ds = $p->loadDatasource('WidgetFormCriticalList');
                        $wflist_ds->set_form_type($row["form_id"]);
                        $wflist_ds->_build_case_sql($row["form_id"]);
                        $wflist_ds->buildquery($patient_id, $row["form_id"]);
                        $wflist_ds->set_form_type($row["form_id"]);
			}

			$wfDataGrid =& new sGrid($wflist_ds);
			$wfDataGrid->name = "wfDataGrid" . $row['widget_form_id'];
			$wfDataGrid->registerTemplate('last_edit','<a href="'.Celini::link('data','Form').'id={$form_data_id}&returnTo=' . $return_link . '">{$last_edit}</a>');
			$tmpar = array();
                        if ($row["type"] == 3) {
                                $widgets[$row["name"]] = array("grid" => $wfDataGrid->render() , "form_edit_link" => Celini::link('edit', $row['controller_name'], true, $patient_id). "&widgetFormId=".$row['widget_form_id']."&returnTo=" . $return_link);
                        }
                        elseif ($row['type'] == 4) {
                                $widgets[$row["name"]] = array("grid" => $wfDataGrid->render() , "form_edit_link" => Celini::link('edit', $row['controller_name'], true, $patient_id). "&widgetFormId=".$row['widget_form_id']."&returnTo=" . $return_link);
                        }
                        else {
                                //$widgets[$row["name"]] = array("grid" => $wfDataGrid->render() , "form_add_link" => Celini::link('fillout',"Form",true, $row["form_id"]). "&returnTo=" . $return_link, "form_list_link" => Celini::link('list',"Form",true, $row["form_id"]). "&returnTo=" . $return_link);
                                $widgets[$row["name"]] = array("grid" => $wfDataGrid->render() , "form_add_link" => Celini::link('fillout',"Form",true, $row["form_id"]). "&returnTo=" . $return_link);
                        }
                        $wfds->next();
                }

		$this->assign_by_ref("widgets", $widgets);

		return $this->view->render("criticalsblock.html");
	}
	function _getFormDataId($filloutType = 'encounter',$formId) {
		$formDataId = 0;
		switch($filloutType) {
                  case 'encounter':
                        $fdDS =  new FormDataByExternalByFormId_DS($this->get('encounter_id','c_encounter'),$formId);
			//return $fdDS->preview();
                        $fdDS->rewind();
                        $row = $fdDS->get();

                        if (is_array($row)) {
                                $formDataId = $row['form_data_id'];
                        }
                }
		return $formDataId;
	}
	function ajaxFillout($formId) {
		return $this->actionFillout_view($formId);
	}
	

	function actionFillout_view($formId=0,$filloutType='encounter') {
		if ($this->formId > 0) {
			$formId = $this->formId;
		}
		$formDataId = $this->_getFormDataId($filloutType,$formId);	
		$this->form_data_id = $formDataId;
		$GLOBALS['loader']->requireOnce("controllers/C_Form.class.php");
		$form_controller = new C_Form();
		return $form_controller->actionFillout_edit($formId, $this->form_data_id);
	}
	function ajaxProcessFillout($formId) {
		return $this->processFillout_edit($formId);
	}
	
	function processFillout_edit($formId) {
		$formId = (int)$_POST['form_id'];
		$formDataId = $this->_getFormDataId($_POST['filloutType'],$formId);	
		$this->form_data_id = $formDataId;
		$this->formId = $formId;
		$GLOBALS['loader']->requireOnce("controllers/C_Form.class.php");
		$form_controller = new C_Form();
		$form_controller->setExternalId($this->get('encounter_id','c_encounter'));
		$form_controller->processFillout_edit($formId, $this->form_data_id);
		$this->form_data_id = $form_controller->form_data_id;
	}


        function actionEdit($id=0) {
                $id = (int)$id;
		$ordo = '';
		if (is_object($this->_ordo)) {
			$ordo = $this->_ordo;
		}
		else {
                	$ordo = Celini::newORDO($this->_ordoName, $id);
		}
		$em =& Celini::enumManagerInstance();
 		$this->assign("em",$em);
		$form = ORDataObject::factory("Form"); 
		$formList = $form->simpleFormList();
		$this->assign("formList",$formList);
                $this->assign("EDIT_ACTION", Celini::managerLink($id));
                $this->view->assign_by_ref('ordo', $ordo);


                return $this->view->render('edit.html');
        }

	function actionRemove() {
		$column_id = (int)$_GET['column_id'];
		$widget_form_id = (int)$_GET['form_id'];

		$db =& new clniDB();
                $sql = "delete from summary_columns where summary_column_id = '" . (int)$column_id . "' and widget_form_id = '" . (int)$widget_form_id . "'";
                $results = $db->execute($sql);

		header("HTTP/1.1 204 No Content");
		exit;
	}

        function getWidgetVisibility() {
                $session =& Celini::SessionInstance();
                $vis = $session->get('WidgetVisibility');
                return $vis;
        
        }
        
        function actionsetWidgetVisibility($mode) {
                $session =& Celini::SessionInstance();
                $session->set('WidgetVisibility', $mode);

		header("HTTP/1.1 204 No Content");
		exit;
        }

}
?>
