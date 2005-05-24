<?php

require_once CELLINI_ROOT."/includes/Tree.class.php";

/**
 * class CategoryTree
 * This is a class for storing document categories using the MPTT implementation
 */
class CategoryTree extends Tree {

	var $_relation_table = "category_to_document";
	var $_document_table = "document";

	
	/*
	*	This just sits on top of the parent constructor, only a shell so that the _table var gets set
	*/
	function CategoryTree($root,$root_type = ROOT_TYPE_ID) {
		$this->_table = "category";
		parent::Tree($root,$root_type);
	}
	
	function _get_categories_array($patient_id,$group = false) {
	  $categories = array();
	  
	  $g = "";
	  if (is_array($group)) {
		  $g = " and group_id in(".implode(',',$group).") ";
	  }
		$this->_db->execute("drop temporary table if exist _firstNote");
		$sql = "create temporary table _firstNote select min(id) id, foreign_id from note group by foreign_id";
		$result = $this->_db->execute($sql);

	  $sql = "SELECT c.id, c.name, d.id AS document_id, d.type, d.url, n.note 
	  		FROM $this->_table AS c, $this->_document_table AS d 
		  	LEFT JOIN $this->_relation_table AS c2d ON c.id = c2d.category_id 
			left join _firstNote fn on d.id = fn.foreign_id
			left join note n on fn.id = n.id and n.foreign_id = d.id
		  WHERE c2d.document_id = d.id";
	  if (is_numeric($patient_id)) {
	  		$sql .= " AND d.foreign_id = '" . $patient_id . "'";
	  }
	  $sql .= $g;
	  //echo $sql;
	  $result = $this->_db->Execute($sql);
	  
	  while ($result && !$result->EOF) {
	  	$categories[$result->fields['id']][$result->fields['document_id']] = $result->fields;
	  	$result->MoveNext();
	  }
	  
	  return $categories;
		
	}

	function _get_category_names($id) {
		$sql = "SELECT c.id, c.name from $this->_table as c";
		$res = $this->_db->_execute($sql);
		return $res->getAssoc();
	}

	// used in cateogory view, temp table stuff was added before i saw that so its never been tested
	function getDataForParent($id,$group = array(1),$foreign_id = 0) {
		settype($id,'int');
		settype($foreign_id,'int');

		$g = implode(',',$group);
		$this->_db->execute("drop temporary table if exist _firstNote");
		$sql = "create temporary table _firstNote select min(id) id, foreign_id from note group by foreign_id";
		$this->_db->execute($sql);
		$sql = "SELECT d.*, n.note FROM $this->_table c
			inner JOIN $this->_relation_table AS c2d ON c.id = c2d.category_id 
			inner join $this->_document_table d on c2d.document_id = d.id
			left join _firstNote fn on d.id = fn.foreign_id
			left join note n on fn.id = n.id and n.foreign_id = d.id
			where c.id = $id and d.foreign_id = $foreign_id and group_id in($g)";

		$result = $this->_db->Execute($sql);
	  
		$ret = array();
		while ($result && !$result->EOF) {
			$result->fields['file_name'] = basename(preg_replace("|^(.*)://|","",$result->fields['url']));
			if (empty($result->fields['name'])) {
				$result->fields['name'] = $result->fields['file_name'];
			}
			$result->fields['mime'] = str_replace('/','-',$result->fields['mimetype']);
			$result->fields['mime_base'] = array_shift(explode('-',$result->fields['mime']));
			$ret[$result->fields['id']] = $result->fields;
			$result->MoveNext();
		}
		return $ret;
	}
}
?>
