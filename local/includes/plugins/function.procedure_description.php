<?php
/**
* Smarty plugin
* -------------------------------------------------------------
* File:     function.procedure_description.php
* Type:     function
* Name:     Procedure Description
* Purpose:  outputs description for a procedure (cpt code) from a databse
* -------------------------------------------------------------
*
*
* @param array
* @param Smarty
* @return string
**/


	function smarty_function_procedure_description($params, &$smarty){

		foreach($params as $key => $val) {
			if ($key =='code'){
				$$key = (string)$val;
			}
			if ($key =='length'){
				$$key = (string)$val;
			}

		}


		$query = 'SELECT code_text FROM codes WHERE code = "'.$code.'"';
		
		$db = Celini::DbInstance();
		$query_result = $db->execute($query);
		$desc = $query_result->fields['code_text'];
		if (isset($length)){
			if (strlen($desc) > $length) {
				$desc = substr($desc,0,$length);
			} elseif (strlen($desc) < $length) {
				$desc = str_pad($desc,$length);
			}
		}
		return $desc;
	}
?>
