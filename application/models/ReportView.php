<?php
/*****************************************************************************
*       ReportView.php
*
*       Author:  ClearHealth Inc. (www.clear-health.com)        2009
*       
*       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
*       respective logos, icons, and terms are registered trademarks 
*       of ClearHealth Inc.
*
*       Though this software is open source you MAY NOT use our 
*       trademarks, graphics, logos and icons without explicit permission. 
*       Derivitive works MUST NOT be primarily identified using our 
*       trademarks, though statements such as "Based on ClearHealth(TM) 
*       Technology" or "incoporating ClearHealth(TM) source code" 
*       are permissible.
*
*       This file is licensed under the GPL V3, you can find
*       a copy of that license by visiting:
*       http://www.fsf.org/licensing/licenses/gpl.html
*       
*****************************************************************************/


class ReportView extends WebVista_Model_ORM {

	protected $reportViewId;
	protected $reportBaseId;
	protected $reportBase;
	protected $displayName = '';
	protected $systemName = '';
	protected $columnDefinitions = '';
	protected $customizeColumnNames = 0;
	protected $runQueriesImmediately = 0;
	protected $active = 1;
	protected $viewOrder = 1;
	protected $showResultsIn = '';
	protected $showResultsOptions = '';

	protected $_primaryKeys = array('reportViewId');
	protected $_table = 'reportViews';

	public function __construct() {
		parent::__construct();
		$this->reportBase = new ReportBase();
		$this->reportBase->_cascadePersist = false;
	}

	public function persist() {
		if ($this->_persistMode == WebVista_Model_ORM::DELETE) return parent::persist();
		$db = Zend_Registry::get('dbAdapter');
		$reportViewId = (int)$this->reportViewId;
		$data = $this->toArray();
		unset($data['reportBase']);
		if ($reportViewId > 0) {
			$ret = $db->update($this->_table,$data,'reportViewId = '.$reportViewId);
		}
		else {
			$this->reportViewId = WebVista_Model_ORM::nextSequenceId();
			$data['reportViewId'] = $this->reportViewId;
			$data['viewOrder'] = $this->_getNextViewOrder();
			$ret = $db->insert($this->_table,$data);
		}
		return $this;
	}

	protected function _getNextViewOrder() {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table,'MAX(viewOrder) AS nextViewOrder')
				->where('reportBaseId = ?',(int)$this->reportBaseId);
		$nextViewOrder = 0;
		if ($row = $db->fetchRow($sqlSelect)) {
			$nextViewOrder = (int)$row['nextViewOrder'];
		}
		return ($nextViewOrder + 1);
	}

	public function setReportBaseId($id) {
		$this->reportBaseId = (int)$id;
		$this->reportBase->reportBaseId = $this->reportBaseId;
	}

	public function getIteratorByBaseId($baseId=null) {
		if ($baseId === null) {
			$baseId = (int)$this->reportBaseId;
		}
		return $this->getIteratorByFilters(array('reportBaseId'=>$baseId));
	}

	public function getIteratorByFilters(Array $filters) {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->order('viewOrder ASC');

		foreach ($filters as $key=>$value) {
			switch ($key) {
				case 'reportBaseId':
					$sqlSelect->where('reportBaseId = ?',(int)$value);
					break;
				case 'active':
					$sqlSelect->where('active = ?',(int)$value);
					break;
			}
		}
		return $this->getIterator($sqlSelect);
	}

	public function generateMetaDataFromQueries(Array $queries) {
		$ret = array();
		$config = Zend_Registry::get('config');
		$dbName = $config->database->params->dbname;
		$db = Zend_Registry::get('dbAdapter');

		$reportQuery = new ReportQuery();
		$reportQuery->reportBaseId = $this->reportBaseId;
		$reportQueryIterator = $reportQuery->getIteratorByBaseId();
		foreach ($reportQueryIterator as $query) {
			$content = $query->query;
			if (isset($queries[$query->reportQueryId])) {
				$content = $queries[$query->reportQueryId];
			}
			switch ($query->type) {
				case ReportQuery::TYPE_SQL:
					try {
						$stmt = $db->query($content,array(),Zend_Db::FETCH_NUM);
						$columns = $stmt->columnCount();
						for ($i = 0; $i < $columns; $i++) {
							$row = self::generateMappingObject($query->displayName);
							$row->queryId = $query->reportQueryId;
							$columnMeta = $stmt->getColumnMeta($i);
							$row->resultSetName = $dbName.'.'.$columnMeta['table'].'.'.$columnMeta['name'];
							$row->displayName = self::metaDataPrettyName($row->resultSetName);
							$ret[$row->id] = $row;
						}
					}
					catch (Exception $e) {
						trigger_error($content.$e->getMessage(),E_USER_NOTICE);
						throw $e;
						return false;
					}
					break;
				case ReportQuery::TYPE_NSDR:
					$nsdr = explode("\n",$content);
					foreach ($nsdr as $value) {
						$row = self::generateMappingObject($query->displayName);
						$row->queryId = $query->reportQueryId;
						$row->resultSetName = self::extractNamespace($value);
						$row->displayName = self::metaDataPrettyName($row->resultSetName);
						$ret[$row->id] = $row;
					}
					break;
			}
		}
		return $ret;
	}

	public static function extractNamespace($nsdr) {
		$x = explode('::',$nsdr);
		$nsdr = $x[0];
		if (isset($x[1])) {
			$nsdr = $x[1];
		}
		$namespace = $nsdr;
		if (preg_match('/(.*)\[(.*)\]$/',$namespace,$matches))  {
			$namespace = $matches[1];
		}
		return $namespace;
	}

	public static function metaDataPrettyName($name) {
		$x = explode('.',$name);
		$name = array_pop($x);
		$name = preg_replace('/_/',' ',$name);
		$name = preg_replace('/([A-Z])(?![A-Z])/',' $1',$name);
		$name = ucwords($name);
		return trim($name);
	}

	public static function generateMappingObject($queryName) {
		$id = uniqid('',true);
		$row = new StdClass();
		$row->id = $id;
		$row->queryId = 0;
		$row->queryName = $queryName;
		$row->resultSetName = '';
		$row->displayName = '';
		$row->transforms = array();
		return $row;
	}

	public function getUnserializedColumnDefinitions() {
		$ret = null;
		if (strlen($this->columnDefinitions) > 0) {
			$ret = unserialize($this->columnDefinitions);
		}
		return $ret;
	}

	public function setSerializedColumnDefinitions($value) {
		$this->columnDefinitions = serialize($value);
	}

	public function getUnserializedShowResultsOptions() {
		$ret = null;
		if (strlen($this->showResultsOptions) > 0) {
			$ret = unserialize($this->showResultsOptions);
		}
		return $ret;
	}

	public function setSerializedShowResultsOptions($value) {
		$this->showResultsOptions = serialize($value);
	}

	public static function reorderViews($baseId,$from,$to) {
		$db = Zend_Registry::get('dbAdapter');
		$viewFrom = new self();
		$viewFrom->reportViewId = (int)$from;
		$viewFrom->populate();
		$viewTo = new self();
		$viewTo->reportViewId = (int)$to;
		$viewTo->populate();
		$viewFrom->viewOrder = $viewTo->viewOrder + 1;
		$viewFrom->persist();
		$sql = 'UPDATE `'.$viewFrom->_table.'` SET viewOrder = (viewOrder + 1) WHERE reportBaseId = '.(int)$baseId.' AND viewOrder > '.$viewFrom->viewOrder;
		$db->query($sql);
	}

	public static function getTransformTypes() {
		$types = array();
		$types['ucase'] = 'Uppercase';
		$types['lcase'] = 'Lowercase';
		$types['ucwords'] = 'Upper Case Words';
		$types['squote'] = 'Single Quote';
		$types['dquote'] = 'Double Quote';
		$types['pad'] = 'Pad';
		$types['truncate'] = 'Truncate';
		$types['customLink'] = 'Custom Link';
		$types['regex'] = 'Regex';
		$types['enumLookup'] = 'Enum Lookup';
		$types['total'] = 'Total (SUM)';
		$types['dateFormat'] = 'Date Format';
		return $types;
	}

	public static function getShowResultOptions() {
		$results = array();
		$results['grid'] = 'Grid';
		$results['file'] = 'Flatfile export';
		$results['xml'] = 'XML export';
		$results['pdf'] = 'PDF';
		$results['graph'] = 'Graph';
		//$results['pqri'] = 'PQRI';
		return $results;
	}

	public static function getLineEndingOptions() {
		return array('windows'=>'windows=\r\n','mac'=>'mac=\r','linux'=>'linux=\n');
	}

}
