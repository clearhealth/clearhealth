<?php
$loader->requireOnce('/controllers/C_CRUD.class.php');
$loader->requireOnce("includes/Grid.class.php");
$loader->requireOnce('includes/transaction/TransactionManager.class.php');

class C_WidgetForm extends C_CRUD {
	
	var $_ordoName = "WidgetForm";
	var $form_data_id = 0;
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
		$wfds = new WidgetForm_DS();

		$wfds->rewind();
		$widgets = array();
		$return_link = Celini::link(true,true, true, $patient_id);
		while(($row = $wfds->get()) && $wfds->valid()) {
			// Setup form data block
                        $wflist_ds = $p->loadDatasource('WidgetFormCriticalList');
                        $wflist_ds->set_form_type($row["form_id"]);
                        $wflist_ds->_build_case_sql($row["form_id"]);
                        $wflist_ds->buildquery($patient_id, $row["form_id"]);
                        $wflist_ds->set_form_type($row["form_id"]);

			$wfDataGrid =& new sGrid($wflist_ds);
			$wfDataGrid->name = "wfDataGrid" . $row['form_id'];
			$wfDataGrid->registerTemplate('last_edit','<a href="'.Celini::link('data','Form').'id={$form_data_id}&returnTo=' . $return_link . '">{$last_edit}</a>');
			$tmpar = array();
                        if ($wflist_ds->get_form_type($row["widget_form_id"]) == 3) {
                                $widgets[$row["name"]] = array("grid" => $wfDataGrid->render() , "form_edit_link" => Celini::link('edit', $wflist_ds->get_controller_name($row["widget_form_id"]), true, $patient_id). "&returnTo=" . $return_link);
                        }
                        else {
                                $widgets[$row["name"]] = array("grid" => $wfDataGrid->render() , "form_add_link" => Celini::link('fillout',"Form",true, $row["form_id"]). "&returnTo=" . $return_link, "form_list_link" => Celini::link('list',"Form",true, $row["form_id"]). "&returnTo=" . $return_link);
                        }
                        $wfds->next();
                }

		$this->assign_by_ref("widgets", $widgets);

		return $this->view->render("criticalsblock.html");
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


        function actionEdit($id) {
                $id = $this->getDefault($this->_ordoName . "_id", '0');
                $ordo =& Celini::newORDO($this->_ordoName, $id);
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

	function actionAdd() {
		
                $id = $this->getDefault($this->_ordoName . "_id", '0');
		$ordo = "";
		if(is_object($this->_ordo)) {
                  $ordo = $this->_ordo;
		}else {
                  $ordo = Celini::newORDO($this->_ordoName, $id);
		}
                $this->assign("EDIT_ACTION", Celini::managerLink($id));
                $this->view->assign_by_ref('ordo', $ordo);
		$column_id = '';
                $widget_form_id = '';
                $field_name = '';
                $pretty_name = '';
                $table_name = '';
		$db =& new clniDB();
		if (isset($_GET['column_id']) && isset($_GET['widget_form_id']) && isset($_GET['field_name']) && isset($_GET['pretty_name']) && isset($_GET['table_name'])) {
		$column_id = (int)$_GET['column_id'];
		$widget_form_id = (int)$_GET['widget_form_id'];
		$field_name = $db->quote($_GET['field_name']);
		$pretty_name = $db->quote($_GET['pretty_name']);
		$table_name = $db->quote($_GET['table_name']);
		}

		$sql = "select count(*) as count from summary_columns where summary_column_id = '$column_id' and widget_form_id = '" . (int)$widget_form_id . "'";
		$results = $db->execute($sql);

		if ($results->fields["count"] == 0) {
                	$sql = "insert into summary_columns (summary_column_id, widget_form_id, name, pretty_name, table_name) values ($column_id, $widget_form_id, $field_name, $pretty_name, $table_name)";
                	$results = $db->execute($sql);
		
			header("HTTP/1.1 204 No Content");
			exit;
		}


		$this->assign("error_message", "An error occured when adding a new column, please submit your column name again.");

                return $this->view->render('edit.html');
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
