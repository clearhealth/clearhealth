<?php
$loader->requireOnce("includes/Grid.class.php");

class C_PlannedCare extends Controller {
	
	var $form_id = '';

	function actionList($patient_id) {
		$db =& new clniDB();
		$query = "select form_id from widget_form_controller where controller_name = 'PlannedCare'";
		
		$result = $db->execute($query);
		if ($result && !$result->EOF) {
			$form_id = $result->fields['form_id'];
		}

		return $this->view->render("list.html");
	}

	function actionEdit($patient_id) {
		$db =& new clniDB();

		$query = "select wf.form_id from widget_form_controller wfc
				inner join widget_form wf on wf.widget_form_id = wfc.form_id
				 where controller_name = 'PlannedCare'";
		$result = $db->execute($query);
		if ($result && !$result->EOF) {
			$form_id = $result->fields['form_id'];
		}

                $em =& Celini::enumManagerInstance();
                $this->assign('planned_care_codes', $em->enumArray('planned_care_codes'));
		$query = "select form_id from form where name = 'Problem: Planned Care'";
		$result = $db->execute($query);
		if ($result && !$result->EOF) {
			$form_id = $result->fields['form_id'];
		}

		$query = "select ss.value
				from form f
				inner join form_data fd on fd.form_id = f.form_id
				inner join storage_string ss on ss.foreign_key = fd.form_data_id
				inner join widget_form wf on wf.form_id = fd.form_id
				inner join widget_form_controller wfc on wfc.form_id = wf.widget_form_id
				where fd.external_id = $patient_id
					and wfc.controller_name = 'PlannedCare'
			";
		$result = $db->execute($query);

		$planned_care_items = array();

		while ($result && !$result->EOF) {
			$planned_care_items[] = $result->fields['value'];
			$result->moveNext();
		}

		$this->assign('form_id', $form_id);
		$this->assign('patient_id', $patient_id);
		$this->assign('planned_care_items', $planned_care_items);

		return $this->view->render("edit.html");
	}
	
	function actionRemove() {
		$patient_id = $_GET['patient_id'];
		$form_id = $_GET['form_id'];
		$storage_data = $_GET['storage_data'];

		$db =& new clniDB();
                $query = "select ss.foreign_key
                                from form f
                                inner join form_data fd on fd.form_id = f.form_id
                                inner join storage_string ss on ss.foreign_key = fd.form_data_id
                                inner join widget_form wf on wf.form_id = fd.form_id
                                inner join widget_form_controller wfc on wfc.form_id = wf.widget_form_id
                                where fd.external_id = $patient_id
                                        and ss.value = '$storage_data'
                                        and wfc.controller_name = 'PlannedCare'
			";

                $result = $db->execute($query);

		if ($result && !$result->EOF) {
			$foreign_key = $result->fields['foreign_key'];

			$query = "delete from storage_string where foreign_key = '$foreign_key' and value = '$storage_data'";
			$db->execute($query);

			$query = "delete from form_data where form_data_id = '$foreign_key' and form_id = '$form_id'";
			$db->execute($query);
		}

		header("HTTP/1.1 204 No Content");
		exit;
	}

	function actionAdd() {
		$patient_id = $_GET['patient_id'];
		$storage_data = $_GET['storage_data'];
		$form_id = $_GET['form_id'];

		$db =& new clniDB();
		$query = "select ss.value
				from form f
				inner join form_data fd on fd.form_id = f.form_id
				inner join storage_string ss on ss.foreign_key = fd.form_data_id
				inner join widget_form wf on wf.form_id = fd.form_id
				inner join widget_form_controller wfc on wfc.form_id = wf.widget_form_id
				where fd.external_id = $patient_id
					and ss.value = '$storage_data'
					and wfc.controller_name = 'PlannedCare'
			";
		$result = $db->execute($query);

		if (!$result->fields['value']) {
			$new_id = $db->nextId("sequences");

			$sql = "insert into form_data (form_data_id, form_id, external_id, last_edit) values ('$new_id', '$form_id', '$patient_id', CURRENT_TIMESTAMP)";
			$result = $db->execute($sql);

			$sql = "insert into storage_string (foreign_key, value_key, value) values ('$new_id', 'problem', '$storage_data')";
			$result = $db->execute($sql);
		}

		header("HTTP/1.1 204 No Content");
		exit;
	}

}
?>
