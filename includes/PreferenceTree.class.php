<?php

require_once("Tree.class.php");

/**
 * class PreferenceTree
 * This is a class for storing user preferences using the MPTT implementation
 */

class PreferenceTree extends Tree {
	
	/*
	*	This just sits on top of the parent constructor, only a shell so that the _table var gets set
	*/
	function PreferenceTree($root,$root_type = ROOT_TYPE_ID) {
		$this->_table = $GLOBALS['frame']['config']['db_prefix']."preferences";
		parent::Tree($root,$root_type);
	}
	
	
}
?>