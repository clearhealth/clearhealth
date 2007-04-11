<?php

require_once CELINI_ROOT . '/includes/Datasource_sql.class.php';

/**
 * Create storables tree datasource
 *
 */
class Tree_DS extends Datasource_sql {
		
	var $_key = 'tree_id';
	var $_table = 'tree';
	var $orderBy = '';
	
	function getCols() {
		return $this->_table.'.'.$this->_key.', '.$this->_table.'.lft, '.$this->_table.'.rght, '.$this->_table.'.level, '.$this->_table.'.node_id, '.$this->_table.'.node_type ';
	}
	
	function getFrom() {
		return $this->_table.' AS t1, '.$this->_table;
	}	
	
	function getWhere($parentId, $startLevel) {
		return ' t1.'.$this->_key.'='.$parentId.' AND '.$this->_table.'.lft BETWEEN t1.lft and t1.rght AND '.$this->_table.'.level > '.$startLevel;
	}
	
	function getOrderBy() {
		return $this->orderBy;
	}

	function setOrderBy($orderBy = '') {
		$this->orderBy = $orderBy;
	}

	function Tree_DS($parentId = 1, $startLevel = 1, $endLevel = null, $orderBy = null) {
		$where = "";
		if($endLevel) {
			$where .= " AND ".$this->_table.".level <= '".$endLevel."'";
		}
		if(!isset($orderBy)) {
			$orderBy = $this->_table.'.lft';
		}
		$this->setOrderBy($orderBy);
		$queryData = array(
			'cols' => $this->getCols(),
			'from' => $this->getFrom(), 
			'where' => $this->getWhere($parentId, $startLevel).$where,
			'orderby' => $this->getOrderBy(),
			'groupby' => $this->getGroupBy());
		$this->setup(Celini::dbInstance(), $queryData, $this->getLabels());	
	}
	
	function getLabels() {
		return false;
	}
	
	function getGroupBy() {
		return false;
	}
}

?>