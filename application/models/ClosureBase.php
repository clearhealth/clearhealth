<?php
/*****************************************************************************
*       ClosureBase.php
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


abstract class ClosureBase extends WebVista_Model_ORM {

	protected $ancestor;
	protected $descendant;
	protected $depth;
	protected $weight;

	protected $_primaryKeys = array('ancestor','descendant');

	protected $_table = '';
	protected $_ormClass = '';

	public function __construct($table=null,$ormClass=null) {
		parent::__construct();
		if ($table !== null) {
			$this->_table = $table;
		}
		if ($ormClass !== null) {
			$this->_ormClass = $ormClass;
		}
	}

	public function getClosureTreeById($id) {
		$db = Zend_Registry::get('dbAdapter');
		$className = $this->_ormClass;
		$ormClass = new $className();
		$ormKey = $ormClass->_primaryKeys[0];
		$sqlSelect = $db->select()
				->from(array('p'=>$ormClass->_table))
				->join(array('c'=>$this->_table),'p.'.$ormKey.' = c.descendant',array())
				->where('c.ancestor = ?',(int)$id)
				->where('c.ancestor != c.descendant')
				->order('c.depth ASC')
				->order('c.weight ASC');
		return $ormClass->getIterator($sqlSelect);
	}

	public function getParentById($id) {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table,'ancestor')
				->where('descendant = ?',(int)$id)
				->where('ancestor != descendant')
				->order('depth ASC')
				->limit(1);
		$parentId = 0;
		if ($row = $db->fetchRow($sqlSelect)) {
			$parentId = $row['ancestor'];
		}
		return $parentId;
	}

	public function reorder($idFrom,$idTo) {
		if ($idFrom <= 0) {
			return;
		}
		$idFrom = (int)$idFrom;
		$idTo = (int)$idTo;

		$parentIdFrom = $this->getParentById($idFrom);
		$parentIdTo = $this->getParentById($idTo);
		// check if $idFrom and $idTo are of the same parent
		if ($parentIdFrom == $parentIdTo) {
			//return;
		}

		$db = Zend_Registry::get('dbAdapter');

		$db->beginTransaction();
		try {
			// === FROM ===
			$closureMap = 'closure_map_'.str_replace('.','_',uniqid('',true));
			$sql = 'CREATE TEMPORARY TABLE `'.$closureMap.'` (
					`ancestor` INT NOT NULL,
					`descendant` INT NOT NULL,
					`weight` INT NOT NULL
				);';
			$db->query($sql);
			// retrieve all its descendants (FROM)
			$sqlSelect = $db->select()
					->from($this->_table)
					->where('`ancestor` = ?',(int)$idFrom);
			foreach ($db->fetchAll($sqlSelect) as $row) {
				$ancestor = $row['ancestor'];
				if ($row['ancestor'] == $row['descendant']) {
					$ancestor = $idTo;
				}
				$bind = array('ancestor'=>$ancestor,'descendant'=>$row['descendant'],'weight'=>$row['weight']);
				$db->insert($closureMap,$bind);
			}
			// remove all its descendants (FROM)
			$this->removeAllDescendants($idFrom);

			$sqlSelect = $db->select()
					->from($closureMap);
			foreach ($db->fetchAll($sqlSelect) as $row) {
				$this->insertClosures($row['ancestor'],$row['descendant']);
			}

			$db->commit();
		}
		catch (Exception $e) {
			$db->rollBack();
			trigger_error($e->getMessage(),E_USER_NOTICE);
		}
	}

	public function removeAllDescendants($ancestor) {
		$db = Zend_Registry::get('dbAdapter');
		$sql = 'DELETE `c` FROM `'.$this->_table.'` AS `c`
			JOIN `'.$this->_table.'` `cc` USING (`descendant`)
			WHERE cc.`ancestor` = '.(int)$ancestor;
		$db->query($sql);
	}

	public function insertClosures($ancestor,$descendant) {
		$db = Zend_Registry::get('dbAdapter');
		$ancestor = (int)$ancestor;
		$descendant = (int)$descendant;
		$sql = 'INSERT INTO `'.$this->_table.'` (`ancestor`,`descendant`,`depth`)
			  SELECT `ancestor`,'.$descendant.',`depth`+1 FROM `'.$this->_table.'`
			  WHERE `descendant` = '.$ancestor.'
			  UNION ALL
			  SELECT '.$descendant.','.$descendant.',0';
		$db->query($sql);
	}

	protected function _getAll($type,$id,$depth=null,$active=true) {
		$db = Zend_Registry::get('dbAdapter');
		$className = $this->_ormClass;
		$ormClass = new $className();
		$ormKey = $ormClass->_primaryKeys[0];
		$sqlSelect = $db->select()
				->from(array('p'=>$ormClass->_table))
				->join(array('c'=>$this->_table),'p.'.$ormKey.' = c.descendant',array())
				->where('c.'.$type.' = ?',(int)$id);
		if ($depth !== null) {
			$sqlSelect->where('c.depth = ?',(int)$depth);
		}
		$sqlSelect->where('p.active = ?',(int)$active);
		//trigger_error($sqlSelect,E_USER_NOTICE);
		return $ormClass->getIterator($sqlSelect);
	}

	public function getAllDescendants($id,$depth=null,$active=true) {
		return $this->_getAll('ancestor',$id,$depth,$active);
	}

	public function getAllAncestors($id,$depth=null,$active=true) {
		return $this->_getAll('descendant',$id,$depth,$active);
	}

	public function deleteClosure($id) {
		$db = Zend_Registry::get('dbAdapter');

		$className = $this->_ormClass;
		$ormClass = new $className();
		$ormKey = $ormClass->_primaryKeys[0];
		$ormClass->$ormKey = (int)$id;
		$ormClass->setPersistMode(WebVista_Model_ORM::DELETE);
		$ormClass->persist();

		// delete recursively
		$iterator = $this->getAllDescendants($id,1);
		foreach ($iterator as $row) {
			$ormKey = $row->_primaryKeys[0];
			$this->deleteClosure($row->$ormKey);
		}
		$this->removeAllDescendants($id);
	}

	public function getDepthById($id) {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('descendant = ?',(int)$id)
				->order('depth DESC')
				->limit(1);
		$depth = 0;
		if ($row = $db->fetchRow($sqlSelect)) {
			$depth = $row['depth'];
		}
		return $depth;
	}

	public function generatePaths($id) {
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
		/*
		select d.descendant AS enumerationId, GROUP_CONCAT(e.name order by e.enumerationId separator ' -> ') as path
		from enumerationsClosure d
		join enumerationsClosure a on (a.descendant = d.descendant)
		join enumerations e on (e.enumerationId = a.ancestor)
		where d.ancestor = 1 and d.descendant != d.ancestor
		group by d.descendant

		SELECT depth AS level, COUNT(level) AS count
		FROM my_tree 
		WHERE ancestor_id = 1
		GROUP BY depth
		*/

		//trigger_error($sqlSelect->__toString(),E_USER_NOTICE);
		$ret = array();
		$rows = $db->fetchAll($sqlSelect);
		if ($rows) {
			foreach ($rows as $row) {
				$ret[$row['node']] = $row['path'];
			}
		}
		return $ret;
	}

	public function getAllTopLevelRoots() {
		$db = Zend_Registry::get('dbAdapter');
		$className = $this->_ormClass;
		$ormClass = new $className();
		$ormKey = $ormClass->_primaryKeys[0];
		$whrSelect = $db->select()
				->from(array('cc'=>$this->_table),'cc.descendant')
				->where('cc.ancestor != cc.descendant')
				->where('cc.descendant = c.ancestor');
		$sqlSelect = $db->select()
				->from(array('p'=>$ormClass->_table))
				->join(array('c'=>$this->_table),'p.'.$ormKey.' = c.ancestor',array())
				->where('c.ancestor = c.descendant')
				->where('c.depth = 0')
				->where('c.ancestor NOT IN ?',$whrSelect)
				->order('c.weight ASC');
		//trigger_error($sqlSelect->__toString(),E_USER_NOTICE);
		return $ormClass->getIterator($sqlSelect);
	}

}
