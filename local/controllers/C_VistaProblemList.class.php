<?php
$loader->requireOnce("includes/Grid.class.php");

/**
 * Controller Clearhealth Vista Problem List actions
 */
class C_VistaProblemList extends Controller {

	var $form_id = '';
	var $height = 250;
	var $width = 250;

	function problem_list_search ($search) {
		$db = $GLOBALS['frame']['adodb']['db'];
		$sql = "select problem_list_id as id, vpl.* from va_problem_list vpl where vpl.description  like '%" . mysql_real_escape_string($search) . "%' limit 25";
		$result_array = $db->GetAll($sql);	
		$this->assign('resultSet',$result_array);
		if (is_array($this->get_template_vars('resultSet'))) {
                        return $this->get_template_vars('resultSet');
                }

		return null;
	}
	function render($patientId, $widgetFormId) {
		return $this->actionList($patientId, $widgetFormId);
	}
	function actionTest() {
		return $this->ajaxInteractionSearch();
	}

	function actionList($patientId, $widgetFormId) {
		$this->view->assign("patientId",$patientId);
		return $this->view->render("list.html");
	}

	function actionEdit($patientId,$widgetFormId) {
		$head =& Celini::HTMLHeadInstance();
                $head->addExternalCss('suggest');
		$patientId = (int)$patientId;
		$widgetFormId = (int)$widgetFormId;
		$wf = ORDataObject::factory('WidgetForm',$widgetFormId); 
                $em =& Celini::enumManagerInstance();
		
		$db =& new clniDB();
		$query = "select fd.form_data_id
                                from form_data fd 
                                inner join form f on f.form_id = fd.form_id
                                where fd.external_id = $patientId and f.form_id = " . $wf->get('form_id');
                $result = $db->execute($query);
                $form_data_id = $result->fields['form_data_id'];

                if (!$form_data_id > 0) {
			$fd = ORDataObject::factory("FormData");
			$fd->set("form_id",$wf->get("form_id"));
			$fd->set("external_id",$patientId);
			$fd->set("last_edit","CURRENT_TIMESTAMP");
			$fd->persist();
                        $form_data_id = $fd->get("form_data_id");
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
		$wf = ORDataObject::factory('WidgetForm',$widget_form_id);
		$form_id = $wf->get('form_id');
		$form_data_id = 0;

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
		else {
			echo "No summary column for this plugin found!";
			exit;
		}

		header("HTTP/1.1 204 No Content");
		exit;
	}
	function getWidth() {
                return $this->width;
        }
        function getHeight() {
                return $this->height;
        }


}
?>
