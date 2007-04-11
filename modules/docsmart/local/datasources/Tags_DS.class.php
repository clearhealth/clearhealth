<?php

/**
 * Creates notes datasource by storable's id
 *
 */
class Tags_DS extends Datasource_sql {
	
	function Tags_DS($storableId = null) {
		$where = '';
		if($storableId) {
			$where .= "tags_storables.storable_id=".$storableId;
		}
		$queryData = array(
			'cols' => 'tags.tag_id, tags.tag',
			'from' => 'tags left join tags_storables on tags_storables.tag_id=tags.tag_id', 
			'where' => $where,
			'orderby' => 'tags.tag');
		$this->setup(Celini::dbInstance(), $queryData, false);	
	}	
	
}

?>