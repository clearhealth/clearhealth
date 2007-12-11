<?php

/**
 * Controller Clearhealth Medication actions
 */
class C_Medication extends Controller {

	function actionEdit() {
		$head =& Celini::HTMLHeadInstance();
                $head->addExternalCss('suggest');
		return $this->view->render('edit.html');
	}

	function medication_search ($search) {
		$db = $GLOBALS['frame']['adodb']['db'];
		$sql = "select ch_id as id, ndc.* from ndc.ndc where ndc.drug like '" . mysql_real_escape_string($search) . "%' limit 25";
		$result_array = $db->GetAll($sql);	
		$this->assign('resultSet',$result_array);
		if (is_array($this->get_template_vars('resultSet'))) {
                        return $this->get_template_vars('resultSet');
                }

		return null;
	}
	function actionTest() {
		return $this->ajaxInteractionSearch();
	}
	
	function ajaxInteractionSearch($medId = '') {
		$db = $GLOBALS['frame']['adodb']['db'];
                $sql = "select * from ndc.ndc inner join ndc.ndc_interaction ndci on ndci.drug1 = ndc.drug where ndc.ch_id = " . (int)$medId . " group by ndci.drug2";
                $res = $db->GetAll($sql);
		$string  = 'This drug interacts with one or more of the following:<br />';
		foreach ($res as $row) {
			$string .= $row['drug2'] . "<br />";

		}
		return $string;	
	}
}
?>
