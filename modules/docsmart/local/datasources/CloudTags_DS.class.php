<?php

/**
 * Creates notes datasource by storable's id
 *
 */
class CloudTags_DS extends Datasource_sql {
	
	function CloudTags_DS($storableId = null) {
		$storableId = isset($storableId) ? $storableId : "NULL";
		$queryData = array(
			'cols' => 'tags.tag_id, tags.tag, count(tags_storables.tag_id) as tags, if(_tags_storables.storable_id IS NULL, 0, 1) as visible',
			'from' => 'tags_storables LEFT JOIN tags_storables AS _tags_storables ON _tags_storables.tag_id=tags_storables.tag_id AND _tags_storables.storable_id='.$storableId.' LEFT JOIN tags ON tags.tag_id=tags_storables.tag_id',
			'groupby' => 'tags_storables.tag_id',
			'orderby' => 'tags.tag');		  
		$this->setup(Celini::dbInstance(), $queryData, false);	
	}	
	
	function toArray($assoc_key = false, $assoc_val = false) {
		
		$tags = parent::toArray($assoc_key, $assoc_val);
		if(count($tags) == 0) {
			return array();
		}
		$sorted = $tags;
		usort($sorted, create_function('$a, $b', 'if ($a[\'tags\']==$b[\'tags\']) return 0; return ($a[\'tags\'] < $b[\'tags\']) ? -1 : 1;'));
		$max = $sorted[count($sorted)-1]['tags'];
		$min = $sorted[0]['tags'];
		$d = ($max - $min) / 3;
		foreach($tags as $key => $tag) {
			if($tag['tags'] == $min) {
				$class=1;
			}if($tag['tags'] == $max) {
				$class=5;
			}elseif($tag['tags'] > $min + $d*2) {
				$class=4;
			}elseif($tag['tags'] > $min + $d) {
				$class=3;
			}else{
				$class=2;
			}
			$tags[$key]['class'] = $class;
		}
		return $tags;
	}

	
}

?>
