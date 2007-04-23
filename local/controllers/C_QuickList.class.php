<?php
$loader->requireOnce("includes/Grid.class.php");

class C_QuickList extends Controller {
	
	var $form_id = '';

	function actionList($patient_id,$widgetFormId) {
		$wf = ORDataObject::factory((int)$widgetFormId); 
		return $this->view->render("list.html");
	}

	function actionEdit($patientId,$widgetFormId) {
		$patientId = (int)$patientId;
		$widgetFormId = (int)$widgetFormId;
		$wf = ORDataObject::factory('WidgetForm',$widgetFormId); 

                $em =& Celini::enumManagerInstance();
		$enumName = str_replace(' ','_',strtolower($wf->get('name')."_quicklist"));
                $this->assign('dropDownList', $em->enumArray($enumName));
		
		$db =& new clniDB();
		$query = "select fd.form_data_id
                                from form_data fd 
                                inner join form f on f.form_id = fd.form_id
                                where fd.external_id = $patientId and f.form_id = " . $wf->get('form_id');
                $result = $db->execute($query);
                $form_data_id = $result->fields['form_data_id'];

                if (!$form_data_id > 0) {
                        $new_id = $db->nextId("sequences");
                        $form_data_id = $new_id;

                        $sql = "insert into form_data (form_data_id, form_id, external_id, last_edit) values ('$new_id', " .$wf->get('form_id') . ", $patientId, CURRENT_TIMESTAMP)";
                        $result = $db->execute($sql);
                }

		$query = "select ss.value, fd.form_data_id
				from form f
				inner join form_data fd on fd.form_id = f.form_id
				inner join storage_string ss on ss.foreign_key = fd.form_data_id
				inner join widget_form wf on wf.form_id = fd.form_id
				where fd.external_id = $patientId and f.form_id = " . $wf->get('form_id');
		$result = $db->execute($query);

		$selectedItems = array();

		while ($result && !$result->EOF) {
			$selectedItems[] = $result->fields['value'];
			$result->moveNext();
		}

		$this->assign('patient_id', $patientId);
		$this->assign('form_data_id', $form_data_id);
		$this->assign('wf',$wf);
		$this->assign('selectedItems', $selectedItems);

		return $this->view->render("edit.html");
	}
	
	function actionRemove($foreignKey = '',$value = '') {
		$db = new clniDB();
		$foreignKey = (int)$_GET['form_data_id'];
		$value = $db->quote($_GET['value']);

		$query = "delete from storage_string where foreign_key = $foreignKey and value = $value";
		$db->execute($query);


		header("HTTP/1.1 204 No Content");
		exit;
	}

	function actionAdd() {
		$patient_id = (int)$_GET['patient_id'];
		$storage_data = $_GET['storage_data'];
		$widget_form_id = (int)$_GET['widget_form_id'];
		$wf = ORDataObject::factory('WidgetForm',$widget_form_id);
		$form_id = $wf->get('form_id');

		$db =& new clniDB();
		$query = "select fd.form_data_id
				from form_data fd 
				inner join form f on f.form_id = fd.form_id
				where fd.external_id = $patient_id and f.form_id = $form_id
			";
		$result = $db->execute($query);
		$form_data_id = $result->fields['form_data_id'];

		$sql = "select name from summary_columns sm where  sm.widget_form_id = '" . (int)$widget_form_id . "'";
                $results = $db->execute($sql);
		if ($results && !$results->EOF) {
			$summary_column = $results->fields['name'];
			$sql = "SELECT MAX(array_index)+1 as array_index from storage_string where foreign_key = $form_data_id and value_key='$summary_column' group by value_key";
			$result = $db->execute($sql);
			$array_index = 0;
			if ($result && !$result->EOF) {
				$array_index = $result->fields['array_index'];
			}
			$sql = "insert into storage_string (foreign_key, value_key, array_index, value) values ('" . $form_data_id . "', '" . $summary_column . "', $array_index , '$storage_data')";
			$result = $db->execute($sql);
		}

		header("HTTP/1.1 204 No Content");
		exit;
	}

}
?>
