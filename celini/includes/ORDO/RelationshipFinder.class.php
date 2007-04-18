<?php
/**
 * @package com.uversainc.celini
 */
 
/**#@+
 * Load required file
 */
$GLOBALS['loader']->requireOnce('includes/ORDO/ORDOFinder.class.php');
$GLOBALS['loader']->requireOnce('includes/ORDO/RelationshipCollection.class.php');
/**#@-*/

/**
 * Handles finding related ORDOs.
 *
 * This will generally never be called directly, instead it will be retrieved via
 * {@link ORDataObject::relationshipFinder()}.
 *
 * If you need to work on the actual {@link Relationship}, use {@link ORDOFinder}.
 *
 * @see ORDataObject::relationshipFinder()
 * @todo Consider removing the inheritance from {@link ORDOFinder} - may not be required any longer
 */
class RelationshipFinder extends ORDOFinder
{
	/**#@+
	 * @access private
	 */
	var $_relatedType = null;
	var $_extraCriteria = array();
	
	var $_children = array();
	var $_parents = array();
	var $_groupBy = '';
	/**#@-*/
	
	/**
	 * Handles initialization
	 */
	function RelationshipFinder() {
		
		$this->_db = new clniDB();
	}
	
	/**
	 * Shorthand for setting a single child.
	 * This will clear out any other children set.
	 *
	 * @param  ORDataObject
	 */
	function setChild(&$ordo) {
		$this->_children = array(&$ordo);
	}
	
	/**
	 * Shorthand for setting a single parent.
	 * This will clear out any other parents set.
	 *
	 * @param  ORDataObject
	 */
	function setParent(&$ordo) {
		$this->_parents = array(&$ordo);
	}
	
	/**
	 * Adds a child ordo to the relationship
	 *
	 * @param ORDataObject $ordo
	 */
	function addChild(&$ordo) {
		$this->_children[] =& $ordo;
	}
	
	/**
	 * Adds a parent ordo to the relationship
	 *
	 * @param ORDataObject $ordo
	 */
	function addParent(&$ordo) {
		$this->_parents[] =& $ordo;
	}
	
	/**
	 * Adds an extra criteria for the relationship search
	 *
	 * @param string $criteria Example: "event.start = '2006-03-16'
	 */
	function addCriteria($criteria) {
		$this->_extraCriteria[] = $criteria;
	}
	
	/**
	 * Adds a join statement to the finder
	 *
	 * @param string $join
	 * @example addJoin('user on user.id=person.user_id')
	 */
	function addJoin($join) {
		$this->_joins .= ' '.$join;
	}
	
	function addOrderBy($orderby) {
		if(empty($this->_orderBy)) {
			$this->_orderBy = $orderby;
		} else {
			$this->_orderBy .= ', '.$orderby;
		}
	}
	
	/**
	 * Add group-by to sql
	 *
	 * @param string $groupby
	 * @example $this->addGroupBy('event.event_id');
	 */
	function addGroupBy($groupby) {
		$this->_groupBy = $groupby;
	}
	
	/**
	 * Sets the type of ORDO we want back
	 * This is required.
	 *
	 * @param string|ordo $type
	 */
	function setRelatedType($type){
		if(is_string($type)){
			$this->_relatedType =& Celini::newORDO($type);
		} else {
			$this->_relatedType =& $type;
		}
	}
	
	/**
	 * Builds the criteria to use for searching
	 * @access private
	 * @todo refractor this into something more friendly...
	 */

	function _buildCriteriaForChild($ordo) {
		$db =& new clniDB();
		$selector = 'parent_id';
		$criteria[] = 'child_type = ' . $db->quote($ordo->name());
		if($ordo->get('id') > 0){
			$criteria[] = 'child_id = ' . $db->quote($ordo->get('id'));
		}
		$criteria[] = 'parent_type = '. $db->quote($this->_relatedType->name());
		if($this->_relatedType->get('id') > 0){
			$criteria[] = 'parent_id = '.$db->quote($this->_relatedType->get('id'));
		}
		$key = $this->_relatedType->tableName().'.id';
		if(!empty($this->_relatedType->_key)){
			$key = $this->_relatedType->tableName().'.'.$this->_relatedType->_key;
		}
		$return = "$key IN ( SELECT $selector from relationship WHERE ". implode(' AND ', $criteria)." )";
		return $this->_extraCriteria[] = $return;
	}

	function _buildCriteriaForParent($ordo) {
		$db =& new clniDB();
		$selector = 'child_id';
		$criteria[] = 'parent_type = ' . $db->quote($ordo->name());
		if($ordo->get('id') > 0){
			$criteria[] = 'parent_id = ' . $db->quote($ordo->get('id'));
		}
		$criteria[] = 'child_type = '. $db->quote($this->_relatedType->name());
		if($this->_relatedType->get('id') > 0){
			$criteria[] = 'child_id = '.$db->quote($this->_relatedType->get('id'));
		}
		$key = $this->_relatedType->tableName().'.id';
		if(!empty($this->_relatedType->_key)){
			$key = $this->_relatedType->tableName().'.'.$this->_relatedType->_key;
		}
		
		$return = "$key IN ( SELECT $selector from relationship WHERE ". implode(' AND ', $criteria)." )";
		return $this->_extraCriteria[] = $return;
	}

	/**
	 * Runs the actual search and returns a {@link ORDOCollection} object
	 *
	 * @return ORDOCollection
	 */
	function &find() {
		foreach($this->_parents as $ordo){
			$this->_buildCriteriaForParent($ordo);
		}
		foreach($this->_children as $ordo){
			$this->_buildCriteriaForChild($ordo);
		}
		$this->_criteria = implode(' AND ',$this->_extraCriteria);
		$sql = sprintf('SELECT %s.* FROM %s %s WHERE %s %s %s',
			$this->_relatedType->tableName(),
			$this->_relatedType->tableName(),
			(empty($this->_joins) ? '' : $this->_joins),
			(empty($this->_criteria) ? '1' : $this->_criteria),
			(empty($this->_groupBy) ? '' : 'GROUP BY '.$this->_groupBy),
			(empty($this->_orderBy) ? '' : 'ORDER BY '.$this->_orderBy)
		);
		$this->_ordoName = $this->_relatedType->name();
		$collectionName = $this->_collectionName;
		$return =& new $collectionName($this->_ordoName, $this->_db->execute($sql));
		return $return;

	}
	
	/**
	 * Runs the actual search and returns array of Ids
	 *
	 * @return array
	 */
	function findIds() {
		foreach($this->_parents as $ordo){
			$this->_buildCriteriaForParent($ordo);
		}
		foreach($this->_children as $ordo){
			$this->_buildCriteriaForChild($ordo);
		}
		$this->_criteria = implode(' AND ',$this->_extraCriteria);
		$sql = sprintf('SELECT %s.%s FROM %s %s WHERE %s %s %s',
			$this->_relatedType->tableName(),
			$this->_relatedType->_key,
			$this->_relatedType->tableName(),
			(empty($this->_joins) ? '' : $this->_joins),
			(empty($this->_criteria) ? '1' : $this->_criteria),
			(empty($this->_groupBy) ? '' : $this->_groupBy),
			(empty($this->_orderBy) ? '' : 'ORDER BY '.$this->_orderBy)
		);
		$result = $this->_db->execute($sql);
		$out = array();
		for($result->MoveFirst();!$result->EOF;$result->MoveNext()) {
			$out[] = $result->fields[$this->_relatedType->_key];
		}
		return $out;
	}

	/**
	 * For debugging
	 *
	 */
	function debug() {
		foreach($this->_parents as $ordo){
			$this->_buildCriteriaForParent($ordo);
		}
		foreach($this->_children as $ordo){
			$this->_buildCriteriaForChild($ordo);
		}
		$this->_criteria = implode(' AND ',$this->_extraCriteria);
		$sql = sprintf('SELECT %s.* FROM %s %s WHERE %s %s',
			$this->_relatedType->tableName(),
			$this->_relatedType->tableName(),
			(empty($this->_joins) ? '' : $this->_joins),
			(empty($this->_criteria) ? '1' : $this->_criteria),
			(empty($this->_orderBy) ? '' : 'ORDER BY '.$this->_orderBy)
		);
		var_dump($sql);
	}
}

