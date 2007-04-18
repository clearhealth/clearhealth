<?php

class chlTestNames
{
	/**
	 * Returns an array of loinc_code/test_name results from the DB
	 *
	 * Pulled from includes/lab_tests.php in CHLCare
	 *
	 * @return array
	 */
	function toArray() {
		$db =& new clniDB();
		$sql = 'SELECT SQL_CACHE loinc_code, test_name FROM ' . chlUtility::chlCareTable('loinc_codes') . ' ORDER BY test_name';
		$results = $db->execute($sql);
		$return = array();
		while (!$results->EOF) {
			$return[$results->fields['loinc_code']] = $results->fields['test_name']; 
			$results->moveNext();
		}
		
		return $return;
	}
	
	/**
	 * Returns an exploded array of the dropdown_list field
	 *
	 * Would be used as part of the person_handler.php code
	 *
	 * @param  int
	 * @return array 
	 */
	function testResultInput($loinc_code) {
		if ((int)$loinc_code == 0) {
			return '<input name="chlLabTests[lab_results]" />';
		}
		
		$db =& new clniDB();
		$view =& new clniView();
		$view->path = 'chlloinc_codes';
		
		$sql = 'SELECT SQL_CACHE dropdown_list, result_type FROM ' . chlUtility::chlCareTable('loinc_codes' ) . ' WHERE loinc_code = ' . (int)$loinc_code;
		$results = $db->execute($sql);
		
		if ($results->fields['result_type'] == 'text_box') {
			return '<input type="text" name="chlLabTests[lab_results]" size="12" value="">';
		}
		else {
			$list_items = @explode("::||::",$results->fields['dropdown_list']);
			$options = "";				
			foreach($list_items AS $key=>$val) {
				$options .= "\n\t<option value=\"$val\">$val</option>";
			}
			return '<select name="chlLabTests[lab_results]"><option value=""></option>'.$options.'</select>';
		}
	}
	
	
	/**
	 * Returns an array of AJAX-methods for this object
	 *
	 * @return array
	 */
	function ajaxMethods() {
		return array('testResultInput');
	}
}
