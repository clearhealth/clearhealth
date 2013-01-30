<?php
/*****************************************************************************
*       ReportQuery.php
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


class ReportQuery extends WebVista_Model_ORM {

	protected $reportQueryId;
	protected $reportBaseId;
	protected $reportBase;
	protected $type; // SQL or NSDR
	protected $displayName;
	protected $query;

	protected $_filters = array();

	protected $_primaryKeys = array('reportQueryId');
	protected $_table = 'reportQueries';
	protected $_cascadePersist = false;
	protected $_data = array();

	const TYPE_SQL = 'SQL';
	const TYPE_NSDR = 'NSDR';

	public function __construct() {
		parent::__construct();
		$this->reportBase = new ReportBase();
		$this->reportBase->_cascadePersist = false;
	}

	public function persist() {
		$db = Zend_Registry::get('dbAdapter');
		$reportQueryId = (int)$this->reportQueryId;
		if ($this->_persistMode == self::DELETE) {
			try {
				$db->delete($this->_table,'reportQueryId = '.$reportQueryId);
				return true;
			}
			catch (Exception $e) {
				return false;
			}
		}
		$data = $this->toArray();
		unset($data['reportBase']);
		if ($reportQueryId > 0) {
			$ret = $db->update($this->_table,$data,'reportQueryId = '.$reportQueryId);
		}
		else {
			$this->reportQueryId = WebVista_Model_ORM::nextSequenceId();
			$data['reportQueryId'] = $this->reportQueryId;
			$ret = $db->insert($this->_table,$data);
		}
		return $ret;
	}

	public function setReportBaseId($id) {
		$this->reportBaseId = (int)$id;
		$this->reportBase->reportBaseId = $this->reportBaseId;
	}

	public function getIteratorByBaseId($baseId = null,$type = null) {
		if ($baseId === null) {
			$baseId = (int)$this->reportBaseId;
		}
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('reportBaseId = ?',(int)$baseId)
				->order('displayName ASC');
		if ($type !== null) {
			$sqlSelect->where('type = ?',$type);
		}
		return $this->getIterator($sqlSelect);
	}

	function execute() {
		$db = Zend_Registry::get('dbAdapter');
		$this->_data = $db->query($this->_filterQuery())->fetchAll();
		return $this;
	}

	function _filterQuery() {
		$query = $this->query;
		$savedFilters = new Zend_Session_Namespace('ReportsController');
		foreach($this->_filters as $key => $filter) {
			switch ($filter['type']) {
				case 'eval':
					$val = eval($filter['eval']);
					$query = preg_replace("/\[$key\]/",$val,$query);
					break;
			}
		}
		return $query;
	}

	function postPopulate() {
		$this->_filters = unserialize($this->filters);
	}

	function toXml() {
		return WebVista_Model_ORM::recurseXml($this->_data,$this->systemName);
	}

	public function executeQuery() {
		$ret = array();
		if (!strlen($this->query) > 0) return $ret;
		if ($this->type == ReportQuery::TYPE_SQL) {
			$db = Zend_Registry::get('dbAdapter');
			$rows = $db->fetchAll($this->query,array(),Zend_Db::FETCH_NUM);
			foreach ($rows as $row) {
				$ret[] = array('id'=>$row[0],'name'=>$row[1]);
			}
		}
		else if ($this->type == ReportQuery::TYPE_NSDR) {
			// TODO: to be implemented
		}
		return $ret;
	}
}
