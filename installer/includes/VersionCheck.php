<?php
/*
 * VersionCheck class
 * 
 */

class VersionCheck{

	function VersionCheck(){
	}
	
	function getCurrentVersion(){
		return FALSE; 	
	}
	
	function getSpecialActions($old_version){
		return FALSE;
	}
	
	function updateFields(&$fields){
		return FALSE;
	}
}
?>
