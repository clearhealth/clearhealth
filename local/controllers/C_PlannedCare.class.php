<?php
$loader->requireOnce("includes/Grid.class.php");

class C_PlannedCare extends Controller {
	

	function actionList() {
		return $this->view->render("list.html");
	}
	
	function actionRemove() {
		$column_id = $_GET['column_id'];
		$form_id = $_GET['form_id'];

		$db =& new clniDB();
                $sql = "delete from summary_columns where summary_column_id = '$column_id' and form_id = '$form_id'";
                $results = $db->execute($sql);

		header("HTTP/1.1 204 No Content");
		exit;
	}

	function actionAdd() {
                $id = $this->getDefault($this->_ordoName . "_id", '0');
                $ordo =& Celini::newORDO($this->_ordoName, $id);
                $this->assign("EDIT_ACTION", Celini::managerLink($id));
                $this->view->assign_by_ref('ordo', $ordo);

		$column_id = $_GET['column_id'];
		$form_id = $_GET['form_id'];
		$field_name = $_GET['field_name'];

		$db =& new clniDB();
		$sql = "select count(1) as count from summary_columns where summary_column_id = '$column_id' and form_id = '$form_id'";
		$results = $db->execute($sql);

		if ($results->fields["count"] == 0) {
                	$sql = "insert into summary_columns (summary_column_id, form_id, name, pretty_name, table_name) values ('$column_id', '$form_id', '$field_name', '$pretty_name', '$table_name')";
                	$results = $db->execute($sql);
		
			header("HTTP/1.1 204 No Content");
			exit;
		}


		$this->assign("error_message", "An error occured when adding a new column, please submit your column name again.");

                return $this->view->render('edit.html');
	}

}
?>
