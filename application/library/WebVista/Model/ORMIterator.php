<?php
/*****************************************************************************
*       ORMIterator.php
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



class WebVista_Model_ORMIterator implements SeekableIterator {
	protected $_ormClass;
	protected $_dbSelect;
	protected $_dbStmt;
	protected $_offset = 0;

	public function __construct($class, $dbSelect = null) {
		$db = Zend_Registry::get('dbAdapter');
		if (is_object($class)) { // && $class instanceof ORM) {
			$this->_ormClass = get_class($class);
			$obj = $class;
		}
		else {
			$this->_ormClass = $class;
			$obj = new $this->_ormClass();
		}
		if (is_null($dbSelect)) {
			$dbSelect = $db->select()->from($obj->_table);
		}
		$this->_dbSelect = $dbSelect;
	}

	public function __destroy() {
		$this->_dbStmt->closeCursor();
	}
	
	public function rewind() {
		$this->_offset = 0;
		return $this;
	}
	
	public function first() {
		$ormObj = new $this->_ormClass();
		if ($this->valid()) {
			$row = $this->_dbStmt->fetch(null,null,0);
			$ormObj->populateWithArray($row);
		}
		return $ormObj;
	}
	public function valid() {
		if (is_null($this->_dbStmt)) {
                        $this->_initDbStmt();
                }
		if ($this->_offset + 1 > $this->_dbStmt->rowCount()) {
			return false;
		}
		return true;
	}

	public function key() {
		return $this->_offset;
	}

	public function current() {
		$ormObj = new $this->_ormClass();
		$row = $this->_dbStmt->fetch(null,null,$this->_offset);
		$ormObj->populateWithArray($row);
		return $ormObj;
	}
	public function seek($offset) {
		$this->_offset = $offset;
		return $this->current();
	}

	public function next() {
		$this->_offset++;

	}

	public function toArray($key = null, $value) {
		$array = array();
		foreach($this as $count => $obj) {
			if (is_null($key)) {
				$array[$count] = $obj->$value;
			}
			else {
				if (is_array($value)) {
					foreach($value as $val)  {
						$array[$obj->$key][] = $obj->$val;
					}
				}
				else {
					$array[$obj->$key] = $obj->$value;
				}
			}
		}
		return $array;
	}

	public function toJsonArray($idKey,$value,$associative = false) {
                $array = array();
                foreach($this as $count => $obj) {
			$tmpArray = array();
                    	$tmpArray['id'] = $obj->$idKey;
                       	foreach($value as $val)  {
				if ($associative) {
                               		$tmpArray[$val] = $obj->$val;
				}
				else {
                               		$tmpArray['data'][] = $obj->$val;
				}
                        }
			$array[] = $tmpArray;
                }
                return $array;
        }

	private function _initDbStmt() {
		$db = Zend_Registry::get('dbAdapter');
		//$this->_dbStmt = $db->prepare($this->_dbSelect,array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
                $this->_dbStmt = $db->query($this->_dbSelect);
	}

	public function setColumns($columns,$additive = false) {
		if (!$additive) {
			$this->_dbSelect->reset('columns');
		}
                $this->_dbSelect->columns($columns);
        }

	public function getDbColumns() {
		$columns = array();
		foreach($this->_dbSelect->getPart('columns') as $item) {
			array_push($columns,$item[1]);
		}
		return array_unique($columns);
	}
}
