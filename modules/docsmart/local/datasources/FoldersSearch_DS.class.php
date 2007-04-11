<?php

$loader->requireOnce('/datasources/NodeSearchResults_DS.class.php');

class FoldersSearch_DS extends NodeSearchResults_DS {

	function getCols() {
		return parent::getCols()
			.",folders.label as name";
	}				
	
	function getFrom() {
	  return ' tree'
			.' LEFT JOIN folders ON tree.node_id=folders.folder_id ';
	}	
	
	function getWhere($query) {
		return 'tree.node_type=\'folder\' AND folders.label like '.$query;		
	}
	
}

?>