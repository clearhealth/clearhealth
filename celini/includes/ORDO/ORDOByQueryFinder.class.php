<?php
$loader->requireOnce('includes/abstracts/GenericFinder.abstract.php');
$loader->requireOnce('includes/ORDO/ORDOCollection.class.php');
$loader->requireOnce('includes/ORDO/ORDOFinderCriteria.class.php');

/**
 * Returns an ORDOCollection based on a particular query.
 *
 * @author Travis Swicegood <tswicegood@uversainc.com>
 */
class ORDOByQueryFinder extends GenericFinder
{
	/**#@+
	 * @access private
	 */
	var $_ordoName = '';
	var $_query = '';
	var $_db = null;
	/**#@-*/
	
	/**
	 * @access protected
	 */
	var $_collectionName = 'ORDOCollection';
	
	
	/**
	 * Handle initialization
	 *
	 * @param  string
	 * @param  string
	 */
	function ORDOByQueryFinder($ordoName, $query) {
		$this->_ordoName = $ordoName;
		$this->_query = $query;
		$this->_db =& new clniDB();
	}
	
	
	/**
	 * {@inheritdoc}
	 */	
	function &find() {
		$collectionName = $this->collectionName();
		$collection =& new $collectionName($this->_ordoName, $this->_db->execute($this->_query));
		return $collection;
	}
}

