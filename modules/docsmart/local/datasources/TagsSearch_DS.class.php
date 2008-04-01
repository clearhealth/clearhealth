<?php

$loader->requireOnce('/datasources/NodeSearchResults_DS.class.php');

class TagsSearch_DS extends NodeSearchResults_DS {

	function getCols() {
		return  parent::getCols() .
			", storables.filename as name";
	}				
	
	function getFrom() {
	  return ' tree'
			.' LEFT JOIN storables ON tree.node_id=storables.storable_id '
			.' LEFT JOIN tags_storables ON tags_storables.storable_id=storables.storable_id '
			.' LEFT JOIN tags ON tags.tag_id=tags_storables.tag_id';
	}	
	
	function getWhere($query) {
		return 'tree.node_type=\'storable\' AND tags.tag like '.$query.
			' and storables.patient_id = '.(int)$this->patientId;		
	}	

	function getGroupBy() {
		return 'tree.tree_id';		
	}	
	
	 function getLabels() {
		return array(
			'test' => '',
			'delete' => '',
			'name' => "Filename",
		);
	}

}

?>
