<?php

$loader->requireOnce('/datasources/SearchResults_DS.class.php');

class NodeSearchResults_DS extends SearchResults_DS {

	function getOrderBy() {
		return "tree.lft"; 
	}			
	
	function getCols() {
		return "tree.* ";
	}
	
	function toArray($assoc_key = false, $assoc_val = false) {
		$result = array();
		$data = parent::toArray($assoc_key, $assoc_val);
		foreach($data as $row) {
			if($row['level'] > 1) {
				$node =& Celini::newOrdo("TreeNode");
				$node->populate_array($row);
				$path = $node->getPath();
				$row['path']=$path->toArray();
			}else{
				$row['folder'] = array('folder_id' => $row['node_id'], 
				                       'label' => $row['name']);
				$row['path'] = array($row);
			}
			array_push($result, $row);
		}
		return $result;
	}
	
}

?>
