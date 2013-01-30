<?php
/*****************************************************************************
*       ProblemListIterator.php
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


class ProblemListIterator extends WebVista_Model_ORMIterator implements Iterator {

	protected $_data = array();
	protected $_dataCount = 0;

	public function __construct($dbSelect = null) {
		parent::__construct("ProblemList",$dbSelect);
	}

	public function rewind() {
		$this->_offset = 0;
		return $this;
	}

	public function valid() {
		if ($this->_offset + 1 > $this->_dataCount) {
			return false;
		}
		return true;
	}

	public function key() {
		return $this->_offset;
	}

	public function current() {
		return $this->_data[$this->_offset];
	}

	public function seek($offset) {
		$this->_offset = $offset;
		return $this->current();
	}

	public function next() {
		$this->_offset++;
	}

	public function setFilters(Array $filters) {
		if (isset($filters['context'])) unset($filters['context']);
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			       ->from(array('pl'=>'problemLists'))
			       ->joinLeft(array('plc'=>'problemListComments'),"pl.problemListId=plc.problemListId",array('problemListCommentId','date','authorId','comment'));
		if (!isset($filters[0]) || $filters[0] != '*') {
			foreach ($filters as $fieldName=>$fieldValue) {
				if (is_array($fieldValue)) {
					$orWhere = array();
					foreach ($fieldValue as $val) {
						$val = preg_replace('/[^a-zA-Z0-9\%\.]/','',$val);
						$orWhere[] = "pl.$fieldName = '$val'";
					}
					$dbSelect->where(implode(' OR ',$orWhere));
				} else {
					switch ($fieldName) {
						case 'dateRange':
							$dateRange = explode(';',$fieldValue);
							$start = isset($dateRange[0])?date('Y-m-d 00:00:00',strtotime($dateRange[0])):date('Y-m-d 00:00:00');
							$end = isset($dateRange[1])?date('Y-m-d 23:59:59',strtotime($dateRange[1])):date('Y-m-d 23:59:59',strtotime($start));
							$dbSelect->where("pl.dateOfOnset BETWEEN '{$start}' AND '{$end}'");
							break;
						default:
							$dbSelect->where("pl.$fieldName = ?",$fieldValue);
					}
				}
			}
		}
		$dbSelect->order("code ASC");
		$rows = $db->fetchAll($dbSelect);

		$dataRows = array();
		foreach ($rows as $row) {
			if (!isset($dataRows[$row['problemListId']])) {
				$problemList = new ProblemList();
				$problemList->populateWithArray($row);
				$dataRows[$row['problemListId']] = $problemList;
			}
			if ((int)$row['problemListCommentId'] > 0) {
				$dataRows[$row['problemListId']]->addComment($row);
			}
		}

		$this->_dataCount = 0;
		$this->_data = array();
		foreach ($dataRows as $row) {
			$this->_dataCount++;
			$this->_data[] = $row;
		}
	}

}
