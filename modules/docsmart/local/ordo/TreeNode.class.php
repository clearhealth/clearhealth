<?php

$loader->requireOnce('/includes/ORDOList.class.php');
$loader->requireOnce('/includes/FolderNodeList.class.php');
$loader->requireOnce('/datasources/Tree_DS.class.php');

/**
 * Object Relational Persistence Mapping Class for table: folders
 *
 * @package com.uversainc.docsmart
 *
 * @todo prefix with DocSmart
 */
class TreeNode extends ORDataObject {

	/**#@+
	 * Fields of table: folders mapped to class members
	 */
	var $tree_id		= '';
	var $lft		= '';
	var $rght		= '';
	var $level		= '';
	var $node_id		= '';
	var $node_type		= '';
	/**#@-*/


	/**
	 * DB Table
	 */
	var $_table = 'tree';

	/**
	 * Primary Key
	 */
	var $_key = 'tree_id';

	/**
	 * Handle instantiation
	 */
	function TreeNode() {
		parent::ORDataObject();
	}

	/**
	 * Insert node into the tree structure under the element with id = parentId
	 *
	 * @param integer $parentId
	 */
	function insert($parentId) {
		$parent =& Celini::newOrdo('TreeNode', $parentId);
		
		$this->lft = $parent->rght;
		$this->rght = $parent->rght + 1;
		$this->level = $parent->level + 1;
		
		$sql = 'UPDATE tree SET '
            . 'lft=IF(lft > '.$parent->rght.', lft+2, lft), '
            . 'rght=IF(rght >= '.$parent->rght.', rght+2, rght) '
            . 'WHERE rght >= '.$parent->rght;
       	$this->_db->Execute($sql);
       	parent::persist();
	}

	/**
	 * Insert node into the tree structure below the element with id=nodeId
	 *
	 * @param integer $nodeId
	 */
	function insertNear($nodeId) {
		$node =& Celini::newOrdo('TreeNode', $nodeId);
		
		$this->lft = $node->rght + 1;
		$this->rght = $node->rght + 2;
		$this->level = $node->level;
		
		$sql = 'UPDATE tree SET '
            . 'lft=IF(lft > '.$node->rght.', lft+2, lft), '
            . 'rght=IF(rght > '.$node->rght.', rght+2, rght) '
            . 'WHERE rght >= '.$node->rght;
        $this->_db->Execute($sql);
        parent::persist();
	}	
	
	/**
	 * Returns datasource of children for specified parent node
	 *
	 * @param integer $parentId
	 */
	function getChildren() {
		$ds = new Tree_DS($this->tree_id);
		return $ds->toArray();
	}

	/**
	 * Removes node from the tree & all children if the arg is true
	 *
	 * @param boolean $deleteChildren
	 * @return boolean
	 */
	function delete($deleteChildren = false) {
		if($deleteChildren) {
			// remove node and all its children from the tree
        	$sql = 'DELETE FROM tree WHERE lft BETWEEN '.$this->lft.' AND '.$this->rght;
			$this->_db->Execute($sql);
			// clearing blank spaces in the tree
			$deltaId = ($this->rght - $this->lft) + 1;
			$sql = 'UPDATE tree SET '
            		. 'lft=IF(lft > '.$this->lft.',lft - '.$deltaId.',lft), '
            		. 'rght=IF(rght > '.$this->rght.',rght - '.$deltaId.',rght) '
            		. 'WHERE  rght > '.$this->rght;
			$this->_db->Execute($sql);
			//exit;
		}else{
        	$sql = 'DELETE FROM tree WHERE tree_id = '.$this->tree_id;
			$this->_db->Execute($sql);	
			// clearing blank spaces in the tree
			$sql = 'UPDATE tree SET '
	            . 'lft=IF(lft BETWEEN '.$this->lft.' AND '.$this->rght.',lft-1,lft),'
	            . 'rght=IF(rght BETWEEN '.$this->lft.' AND '.$this->rght.',rght-1,rght),'
	            . 'level=IF(lft BETWEEN '.$this->lft.' AND '.$this->rght.',level-1,level),'
	            . 'lft=IF(lft>'.$this->rght.',lft-2,lft),'
	            . 'rght=IF(rght>'.$this->rght.',rght-2,rght) '
	            . 'WHERE rght>'.$this->lft;
			$this->_db->Execute($sql);
		}
		return true;	
	}
	
	/**
	 * Returns path to the element from it's top level parent element
	 *
	 * @param boolean $showRoot
	 * @return TreeNodeList
	 */
	function getPath($showRoot = false) {
        $sql = "SELECT tree.tree_id, tree.lft, tree.rght, tree.level, tree.node_type, tree.node_id "
              ." FROM tree as t1, tree WHERE "
              ." t1.tree_id = '".$this->tree_id."' "
              ." AND t1.lft BETWEEN tree.lft AND tree.rght "
              .(($showRoot == false) ? " AND tree.level > 0" : "")
              ." ORDER BY tree.lft";
        $rs = $this->_db->Execute($sql);
        $path = new FolderNodeList();
        while($row = $rs->FetchRow()) {
    		$node =& Celini::newOrdo('TreeNode');
    		$node->populate_array($row);
    		$path->add($node);
        }
        return $path;
	}
	
	/**
	 * Gets and returns parent node by the specified level
	 *
	 * @param integer $level
	 * @return TreeNode
	 */
	function getParentNode($level = 1) {
        if($level < 1) {
        	return false;
        }

        $sql = "SELECT tree.tree_id, tree.lft, tree.rght, tree.level, tree.node_type, tree.node_id "
              ." FROM tree as t1, tree WHERE "
              ." t1.tree_id = '".$this->tree_id."' "
              ." AND t1.lft BETWEEN tree.lft AND tree.rght "
              ." AND tree.level = t1.level - ".(int)$level;
        $rs = $this->_db->Execute($sql);
        if(!$row = $rs->FetchRow()) {
        	return false;
        }	
    	$parent =& Celini::newOrdo('TreeNode');
    	$parent->populate_array($row);
    	return $parent;
	}
	
	/**
	 * Moves node with all its children to another parent node
	 *
	 * @param integer $parentId
	 * @return boolean
	 */
	function moveNode($parentId) {
		$parent =& Celini::newOrdo('TreeNode', $parentId);
		if($parent->tree_id == $this->tree_id || 
     	   $parent->lft == $this->lft ||
     	   ( $parent->lft >= $this->lft && $parent->lft == $this->rght ) ||
      	   ( $this->level == $parent->level+1 && $this->lft > $parent->lft && 
      	     $this->rght < $parent->rght)
		) {
			return false;			
		}	
		if ($parent->lft < $this->lft && $parent->rght > $this->rght && $parent->level < $this->level - 1 ) {
        	$sql = 'UPDATE tree SET ' 
	            . 'level=IF(lft BETWEEN '.$this->lft.' AND '.$this->rght.', level'.sprintf('%+d', -($this->level-1)+$parent->level).', level), ' 
	            . 'rght=IF(rght BETWEEN '.($this->rght+1).' AND '.($this->rght-1).', rght-'.($this->rght-$this->lft+1).', ' 
	                    .'IF(lft BETWEEN '.($this->lft).' AND '.($this->rght).', rght + '.((($parent->rght-$this->rght-$this->level+$parent->level)/2)*2 + $this->level - $parent->level - 1).', rght)),  ' 
	            . 'lft=IF(lft BETWEEN '.($this->rght+1).' AND '.($parent->rght-1).', lft - '.($this->rght-$this->lft+1).', ' 
	                    .'IF(lft BETWEEN '.$this->lft.' AND '.($this->rght).', lft + '.((($parent->rght-$this->rght-$this->level+$parent->level)/2)*2 + $this->level - $parent->level - 1).', lft)) ' 
	            . 'WHERE lft BETWEEN '.($parent->lft+1).' AND '.($parent->rght-1);
		} elseif($parent->lft < $this->lft) {
			$sql = 'UPDATE tree SET ' 
	            . 'level=IF(lft BETWEEN '.$this->lft.' AND '.$this->rght.', level'.sprintf('%+d', -($this->level-1)+$parent->level).', level), ' 
	            . 'lft=IF(lft BETWEEN '.$parent->rght.' AND '.($this->lft-1).', lft+'.($this->rght-$this->lft+1).', ' 
						. 'IF(lft BETWEEN '.$this->lft.' AND '.$this->rght.', lft-'.($this->lft-$parent->rght).', lft) ' 
	            . '), ' 
	            . 'rght=IF(rght BETWEEN '.$parent->rght.' AND '.$this->lft.', rght+'.($this->rght-$this->lft+1).', ' 
	               . 'IF(rght BETWEEN '.$this->lft.' AND '.$this->rght.', rght-'.($this->lft-$parent->rght).', rght) ' 
	            . ') ' 
	            . 'WHERE lft BETWEEN '.$parent->lft.' AND '.$this->rght 
	            .' OR rght BETWEEN '.$parent->lft.' AND '.$this->rght
	            ;
		} else {
         	$sql = 'UPDATE tree SET ' 
	            . 'level=IF(lft BETWEEN '.$this->lft.' AND '.$this->rght.', level'.sprintf('%+d', -($this->level-1)+$parent->level).', level), ' 
	            . 'lft=IF(lft BETWEEN '.$this->rght.' AND '.$parent->rght.', lft-'.($this->rght-$this->lft+1).', ' 
	               . 'IF(lft BETWEEN '.$this->lft.' AND '.$this->rght.', lft+'.($parent->rght-1-$this->rght).', lft)' 
	            . '), ' 
	            . 'rght=IF(rght BETWEEN '.($this->rght+1).' AND '.($parent->rght-1).', rght-'.($this->rght-$this->lft+1).', ' 
	               . 'IF(rght BETWEEN '.$this->lft.' AND '.$this->rght.', rght+'.($parent->rght-1-$this->rght).', rght) ' 
	            . ') ' 
	            . 'WHERE lft BETWEEN '.$this->lft.' AND '.$parent->rght 
	            . ' OR rght BETWEEN '.$this->lft.' AND '.$parent->rght
	            ;
		}
		$this->_db->Execute($sql);
		return true;
	}

}
?>
