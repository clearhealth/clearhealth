<?php
/*****************************************************************************
*       EnumerationClosure.php
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


// New version of enumerationsClosure, supersedes the EnumerationsClosure ORM
class EnumerationClosure extends ClosureBase {

	protected $_table = 'enumerationsClosure';
	protected $_ormClass = 'Enumeration';

	public static function searchByLevels($values,$separator='->') {
		$db = Zend_Registry::get('dbAdapter');
		$matches = array();
		if (!is_array($values)) {
			$values = explode($separator,$values);
		}
		$root = $values[0];
		unset($values[0]);
		if (strlen($root) < 3) {
			return $matches;
		}
		$tthis = new self();
		$className = $tthis->_ormClass;
		$ormClass = new $className();
		$ormKey = $ormClass->_primaryKeys[0];
		$whrSelect = $db->select()
				->from(array('cc'=>$tthis->_table),'cc.descendant')
				->where('cc.ancestor != cc.descendant')
				->where('cc.descendant = c.ancestor');
		$sqlSelect = $db->select()
				->from(array('p'=>$ormClass->_table),array())
				->join(array('c'=>$tthis->_table),'p.'.$ormKey.' = c.ancestor',array())
				->where('c.ancestor = c.descendant')
				->where('c.depth = 0')
				->where('c.ancestor NOT IN ?',$whrSelect)
				->where('p.name LIKE ?',$root.'%')
				->order('c.weight ASC');
		$rootKeySuffix = '';
		$parentKeySuffix = $rootKeySuffix;
		$key = $rootKeySuffix;
		foreach ($values as $key=>$value) {
			if (strlen($value) < 1) return $matches;
			$sqlSelect->join(array('c'.$key=>$tthis->_table),'p'.$parentKeySuffix.'.'.$ormKey.' = c'.$key.'.ancestor',array())
				->join(array('p'.$key=>$ormClass->_table),'p'.$key.'.'.$ormKey.' = c'.$key.'.descendant',array())
				->where('p'.$key.'.name LIKE ?',$value.'%');
			$parentKeySuffix = $key;
		}
		$columns = array('p'.$rootKeySuffix.'.name');
		// CONCAT(parent,separator,child)
		foreach ($values as $key=>$value) {
			$columns[] = "'".$separator."'";
			$columns[] = 'p'.$key.'.name';
		}
		$sqlSelect->columns(array('p'.$key.'.enumerationId AS enumerationId','CONCAT('.implode(',',$columns).') AS name'))
			->group('p'.$key.'.enumerationId');
		//trigger_error($sqlSelect->__toString(),E_USER_NOTICE);
		return $ormClass->getIterator($sqlSelect);
	}

	public function generatePathsKeyName($id) {
		$db = Zend_Registry::get('dbAdapter');
		$className = $this->_ormClass;
		$ormClass = new $className();
		$ormKey = $ormClass->_primaryKeys[0];
		$whrSelect = $db->select()
				->from(array('ccc'=>$this->_table),'ccc.ancestor')
				->where('ccc.depth != 0')
				->where('ccc.ancestor = ?',(int)$id);
		$sqlSelect = $db->select()
				->from(array('c'=>$this->_table),"c.descendant AS node, GROUP_CONCAT(n.name ORDER BY n.enumerationId SEPARATOR ' -> ') AS path")
				->join(array('cc'=>$this->_table),'cc.descendant = c.descendant',array())
				->join(array('n'=>$ormClass->_table),'n.'.$ormKey.' = cc.ancestor',array())
				->where('c.ancestor = ?',(int)$id)
				->where('c.descendant != c.ancestor')
				//->where('cc.descendant NOT IN ?',$whrSelect)
				->group('c.descendant');
		$ret = array();
		$rows = $db->fetchAll($sqlSelect);
		if ($rows) {
			$statisticsStoreKeyAsValue = ((string)Zend_Registry::get('config')->statisticsStoreKeyAsValue== 'true')?true:false;
			foreach ($rows as $row) {
				$enumeration = new Enumeration();
				$enumeration->enumerationId = (int)$row['node'];
				$enumeration->populate();
				if ($statisticsStoreKeyAsValue) $ret[$enumeration->key] = $row['path'];
				else $ret[$enumeration->name] = $row['path'];
			}
		}
		return $ret;
	}

}
