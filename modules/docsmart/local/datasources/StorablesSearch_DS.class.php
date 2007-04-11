<?php

$loader->requireOnce('/datasources/NodeSearchResults_DS.class.php');

class StorablesSearch_DS extends NodeSearchResults_DS {

	function getCols() {
		return parent::getCols()
			.",storables.filename as name";
	}					
	
	function getFrom() {
	  return ' tree'
			.' LEFT JOIN storables ON tree.node_id=storables.storable_id ';
	}	
	
	function getWhere($query) {
		return 'tree.node_type=\'storable\' AND storables.filename like '.$query;		
	}
	
}

?>