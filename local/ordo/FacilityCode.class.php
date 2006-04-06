<?php
/**
 * This file contains {@link FacilityCode}
 *
 * @package  com.uversainc.clearhealth
 * @author   Travis Swicegood <tswicegood@uversainc.com>
 */
 
/** 
 * Facility Codes utilized by x12 generators.
 *
 * These will generally be used in conjunction with {@link Building},
 * {@link Practice}, etc.
 * 
 */
class FacilityCode extends ORDataObject
{
	var $code = '';
	var $name = '';
	
	
	var $_table = 'facility_codes';
	
	
	/**
	 * Retrieves a values of all FacilityCodes and formats it formats it for 
	 * output via drop down.
	 *
	 * @return array
	 */
	function valueListForDropDown() {
		$res = $this->valueList();
		
		$returnArray = array();
		while($res && !$res->EOF) {
			$returnArray[$res->fields['facility_code_id']] = sprintf(
				'%s : %s', $res->fields['code'], $res->fields['name']);
			$res->MoveNext();
		}
		return $returnArray;
	}
	
	
	/**
	 * Retrieves a value list of all FacilityCodes
	 *
	 * @internal Should be static in PHP 5
	 * @return object 
	 *    An ADOdb result set
	 */
	function valueList() {
		$sql = "SELECT * FROM {$this->_table}";
		$res = $this->_execute($sql);
		
		return $res;
	}
		
}

