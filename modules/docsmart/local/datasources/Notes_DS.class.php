<?php

/**
 * Creates notes datasource by storable's id
 *
 */
class Notes_DS extends Datasource_sql {
	
	function Notes_DS($storableId = null) {
		$where = '';
		if($storableId) {
			$where .= "storable_id = ".$storableId;
		}
		$queryData = array(
			'cols' => 'notes.user_id, notes.note_id, notes.note, notes.create_date, notes.revision_id, revisions.revision',
			'from' => 'notes left join revisions on revisions.revision_id=notes.revision_id', 
			'where' => $where,
			'orderby' => 'note_id');		  
		$this->setup(Celini::dbInstance(), $queryData, false);	
	}	
	
}

?>