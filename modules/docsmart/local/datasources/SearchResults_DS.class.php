<?php
$loader->requireOnce('/datasources/Tree_DS.class.php');
/**
 * Creates search results datasource
 *
 */
class SearchResults_DS extends Datasource_sql {
	var $patientId = null;
	
	function SearchResults_DS($query = '',$patientId = null) {
		$this->patientId = $patientId;
		$where = array();
		$db = Celini::dbInstance();
		$query = trim($query);
		if(empty($query)) {
			return false;
		}
		$query = $db->quote("%".$query."%");
		$queryData = array(
			'cols' => $this->getCols(),
			'from' => $this->getFrom(), 
			'where' => $this->getWhere($query),
			'orderby' => $this->getOrderBy(),
			'groupby' => $this->getGroupBy());
		$this->setup(Celini::dbInstance(), $queryData, 	$this->getLabels());

	}

        function getLabels() {
		return false;
	}


	function getCols() {
		return "*";
	}				
	
	function getFrom() { 
		return ""; 
	}	
	
	function getWhere($query) {
		return ""; 
	}

	function getGroupBy() {
		return ""; 
	}	
	
	function getOrderBy() {
		return ""; 
	}		
	
}

?>
