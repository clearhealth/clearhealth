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
		$encounterId = (int)$this->GET->get('encounterId');
		$this->assign("encounterId",$encounterId);
		$wf = ORDataObject::factory('WidgetForm',$widgetFormId); 

                $em =& Celini::enumManagerInstance();
		$enumName = str_replace(' ','_',strtolower($wf->get('name')."_quicklist"));
                $this->assign('dropDownList', $em->enumArray($enumName));
		
		$db =& new clniDB();
		$query = "select fd.form_data_id
                                from form_data fd 
                                inner join form f on f.form_id = fd.form_id
                                where fd.external_id = $patientId and f.form_id = " . $wf->get('form_id');
		if ($encounterId > 0) {
			$query .= " and fd.encounter_id = $encounterId";
		}
                $result = $db->execute($query);
                $form_data_id = $result->fields['form_data_id'];

                if (!$form_data_id > 0) {
			$fd = ORDataObject::factory("FormData");
			$fd->set("form_id",$wf->get("form_id"));
			$fd->set("external_id",$patientId);
			$fd->set("last_edit","CURRENT_TIMESTAMP");
			if ($encounterId > 0) {
				$fd->set("encounter_id",$encounterId);
			}
			$fd->persist();
                        $form_data_id = $fd->get("form_data_id");
                }

		$query = "select ss.value, fd.form_data_id
				from form f
				inner join form_data fd on fd.form_id = f.form_id
				inner join storage_string ss on ss.foreign_key = fd.form_data_id
				inner join widget_form wf on wf.form_id = fd.form_id
				where fd.external_id = $patientId and f.form_id = " . $wf->get('form_id');
		if ($encounterId > 0) {
			$query .= " and fd.encounter_id = $encounterId";
		}
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
		if ($this->GET->exists('returnTo')) {
                        $this->assign('returnTo',$this->GET->get('returnTo'));
                }

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
		$encounterId = (int)$_GET['encounterId'];
		$wf = ORDataObject::factory('WidgetForm',$widget_form_id);
		$form_id = $wf->get('form_id');
		$form_data_id = 0;

		$db =& new clniDB();
		$query = "select fd.form_data_id
				from form_data fd 
				inner join form f on f.form_id = fd.form_id
				where fd.external_id = $patient_id and f.form_id = $form_id
			";
		if ($encounterId > 0) {
			$fd = ORDataObject::factory("FormData",$form_data_id);
			$fd->set("encounterId",$encounterId);
			$fd->persist();
			$form_data_id = $fd->get("form_data_id");
			$query .= " and fd.encounter_id = $encounterId";
		}
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
		else {
			echo "No summary column for this plugin found!";
			exit;
		}

		header("HTTP/1.1 204 No Content");
		exit;
	}

}
?>
