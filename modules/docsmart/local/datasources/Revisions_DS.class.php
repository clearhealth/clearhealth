<?php

/**
 * Creates revisions datasource by storable's id
 *
 */
class Revisions_DS extends Datasource_sql {
	
	function Revisions_DS($where = '') {
		$queryData = array(
			'cols' => 'revisions.*, tree.tree_id',
			'from' => 'revisions left join tree on tree.node_id=revisions.storable_id and tree.node_type="storable"', 
			'where' => $where,
			'orderby' => 'revisions.revision');		  
		$this->setup(Celini::dbInstance(), $queryData, $this->getLabels());
	}	

	function getLabels() {
		return array(
			'create_date' => "Create date",
			'user_id' => "User",
			'filesize' => "File size",
			'view' => '');
	}
	
	
}

?>