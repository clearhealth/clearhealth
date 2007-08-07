<?php
$loader->requireOnce('includes/abstracts/GenericFinder.abstract.php');
$loader->requireOnce('includes/ORDO/ORDOCollection.class.php');
$loader->requireOnce('includes/ORDO/ORDOFinderCriteria.class.php');

/**
 * A basic ORDO finder
 *
 * @author Travis Swicegood <tswicegood@uversainc.com>
 */
class ORDOFinder extends GenericFinder
{
	var $_ordoName = '';
	var $_ordo = null;
	var $_criteria = null;
	var $_orderBy = null;
	var $_db = null;
	var $_joins = '';
	
	/**
	 * The collection/result object name
	 *
	 * @var string
	 * @access protected
	 */
	var $_collectionName = 'ORDOCollection';
	
	
	/**
	 * Handle instantiation
	 *
	 * The first parameter is the name of the ordo we're searching for, the
	 * second is either a string, or a {@link ORDOFinderCriteria} object.  If
	 * it is a string, it should be a complete "where" clause of an SQL 
	 * statement.
	 *
	 * <i>$db</i> can be provided to pass in a database object.  Be aware that
	 * in PHP 4.4, this will be a copy, not a reference to the DB object passed
	 * in.
	 * 
	 * @param  string
	 * @param  string|ORDOFinderCriteria
	 * @param  string|ORDOFinderOrderBy
	 * @param  clniDB|null
	 * @param  string|null
	 */
	function ORDOFinder($ordoName, $criteria, $orderBy = '', $db = null, $joins=null) {
		assert('is_string($criteria) || is_object($criteria)');
		assert('is_string($orderBy) || is_object($orderBy)');
		
		$this->_ordoName = $ordoName;
		$this->_ordo =& Celini::newORDO($ordoName);
		$this->_criteria = $criteria;
		$this->_orderBy = $orderBy;
		$this->_joins=$joins;
		
		if (is_null($db)) {
			$this->_db =& new clniDB();
		}
		else {
			$this->_db =& $db;
		}
	}
	
	function preview() {
		 $sql = sprintf('SELECT %s.* FROM %s %s WHERE %s %s',
                        $this->_ordo->tableName(),
                        $this->_ordo->tableName(),
                        $this->_joins,
                        is_string($this->_criteria) ? $this->_criteria : $this->_criteria->toString(),
                        is_string($this->_orderBy) ? $this->_orderBy : $this->_orderBy->toString()
                );
		return $sql;

	}	
	/**
	 * Runs the actual search and returns a {@link ORDOCollection} object
	 *
	 * @return ORDOCollection
	 */
	function &find() {
		$sql = sprintf('SELECT %s.* FROM %s %s WHERE %s %s',
			$this->_ordo->tableName(),
			$this->_ordo->tableName(),
			$this->_joins,
			is_string($this->_criteria) ? $this->_criteria : $this->_criteria->toString(),
			is_string($this->_orderBy) ? $this->_orderBy : $this->_orderBy->toString()
		);
		
		$collectionName = $this->_collectionName;
		$return =& new $collectionName($this->_ordoName, $this->_db->execute($sql));
		return $return;
	}
	
	/**
	 * Runs search and returns array of ORDO Ids
	 *
	 * @return array
	 */
	function findIds() {
		$sql = sprintf('SELECT %s.%s FROM %s %s WHERE %s %s',
			$this->_ordo->tableName(),
			$this->_ordo->_key,
			$this->_ordo->tableName(),
			$this->_joins,
			is_string($this->_criteria) ? $this->_criteria : $this->_criteria->toString(),
			is_string($this->_orderBy) ? $this->_orderBy : $this->_orderBy->toString()
		);
		$out = array();
		$result = $this->_db->execute($sql);
		for($result->MoveFirst();!$result->EOF;$result->MoveNext()) {
			$out[] = $result->fields[$this->_relatedType->_key];
		}
		return $out;
	}

	/**
	 * Change the ordering for the collection
	 * Example: "field_name ASC"
	 * @param string $orderby
	 */
	function setOrderBy($orderby){
		$this->_orderBy = $orderby;
	}
}

