<?php
/*****************************************************************************
*       EnumerationsClosure.php
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


class EnumerationsClosure extends WebVista_Model_ORM {
	protected $ancestor;
	protected $descendant;
	protected $depth;
	protected $weight;

	protected $_table = "enumerationsClosure";
	protected $_primaryKeys = array("ancestor","descendant");

	public function getIterByDistinctNames($category) {
		$category = preg_replace('/[^a-z_0-9- ]/i','',$category);
		$db = Zend_Registry::get("dbAdapter");
		$dbSelect = $db->select()->from(array("e"=>"enumerations"))
			       ->join(array("ec"=>"enumerationsClosure"),"e.enumerationId = ec.descendant", array())
			       ->where("ec.ancestor = ec.descendant")
			       ->where("e.category = ?",$category)
			       ->group("e.name")
			       ->order("ec.weight ASC")
			       ->order("e.name ASC");
		//trigger_error($dbSelect,E_USER_NOTICE);
		return $this->getIterator($dbSelect);
	}

	public static function getEnumerationTreeById($enumerationId) {
		$db = Zend_Registry::get("dbAdapter");
		$enumeration = new Enumeration();
		$dbSelect = $db->select()->from(array("e"=>$enumeration->_table))
			       ->join(array("ec"=>"enumerationsClosure"),"e.enumerationId = ec.descendant")
			       ->where("ec.ancestor = ?",(int)$enumerationId)
			       ->where("ec.ancestor != ec.descendant")
			       ->where('ec.depth = 1')
			       ->order("ec.depth ASC")
			       ->order("ec.weight ASC")
			       ->order("e.name ASC");
		return $enumeration->getIterator($dbSelect);
	}

	public function getParentById($enumerationId) {
		$db = Zend_Registry::get("dbAdapter");
		$dbSelect = $db->select()->from($this->_table)
			       ->where("descendant = ?",(int)$enumerationId)
			       ->where("ancestor != descendant")
			       ->order("depth ASC")
			       ->limit(1);
		$parentId = 0;
		if ($row = $db->fetchRow($dbSelect)) {
			$parentId = $row['ancestor'];
		}
		return $parentId;
	}

	public function reorder($idFrom,$idTo) {
		if ($idFrom <= 0 || $idTo <= 0) {
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

		$db = Zend_Registry::get("dbAdapter");

		$db->beginTransaction();
		try {
			// === FROM ===
			$sql = "CREATE TEMPORARY TABLE `closureMap` (
					`ancestor` INT NOT NULL,
					`descendant` INT NOT NULL,
					`weight` INT NOT NULL
				);";
			$db->query($sql);
			// retrieve all its descendants (FROM)
			$sql = "SELECT * FROM `enumerationsClosure` WHERE `ancestor` = {$idFrom}";
			foreach ($db->query($sql)->fetchAll() as $enum) {
				$ancestor = $enum['ancestor'];
				if ($enum['ancestor'] == $enum['descendant']) {
					$ancestor = $idTo;
				}
				$sql = "INSERT INTO `closureMap` (`ancestor`,`descendant`,`weight`)
					VALUES ({$ancestor},{$enum['descendant']},{$enum['weight']})";
				$db->query($sql);
			}
			// remove all its descendants (FROM)
			$sql = "DELETE `ec1` FROM `enumerationsClosure` AS `ec1`
				JOIN `enumerationsClosure` `ec2` USING (`descendant`)
				WHERE ec2.`ancestor` = {$idFrom}";
			$db->query($sql);

			$sql = "SELECT * FROM `closureMap`";
			foreach ($db->query($sql)->fetchAll() as $row) {
				$sql = "INSERT INTO `enumerationsClosure` (`ancestor`,`descendant`,`depth`)
					  SELECT `ancestor`,{$row['descendant']},`depth`+1 FROM `enumerationsClosure`
					  WHERE `descendant` = {$row['ancestor']}
					  UNION ALL
					  SELECT {$row['descendant']},{$row['descendant']},0";
				$db->query($sql);
			}

			$db->commit();
		}
		catch (Exception $e) {
			$db->rollBack();
			trigger_error($e->getMessage(),E_USER_NOTICE);
		}
	}

	public function getAllParentsByCategory($category) {
		$category = preg_replace('/[^a-z_0-9- \.]/i','',$category);
		$db = Zend_Registry::get("dbAdapter");
		// descendant must only 1 row
		$sql = "SELECT e.* FROM enumerations e
			INNER JOIN enumerationsClosure ec ON (e.enumerationId = ec.descendant)
			WHERE (ec.depth = 0) AND 
				(ec.descendant = ec.ancestor AND ec.ancestor = e.enumerationId) AND
				e.category = '$category'
			ORDER BY e.name ASC";
		return $db->query($sql)->fetchAll();
	}

	public function getAllDescendants($enumerationId,$depth=null) {
		$db = Zend_Registry::get("dbAdapter");
		$dbSelect = $db->select()->from(array("e"=>"enumerations"))
			       ->join(array("ec"=>$this->_table),"e.enumerationId = ec.descendant", array())
			       ->where('e.active = 1')
			       ->where("ec.ancestor = ?",(int)$enumerationId)
			       ->order("e.name ASC");
		if ($depth !== null) {
			$dbSelect->where("ec.depth = ?",(int)$depth);
		}
		//trigger_error($dbSelect,E_USER_NOTICE);
		$enumeration = new Enumeration();
		return $enumeration->getIterator($dbSelect);
	}

	public function getAllAncestors($enumerationId,$depth=null) {
		$db = Zend_Registry::get("dbAdapter");
		$dbSelect = $db->select()->from(array("e"=>"enumerations"))
			       ->join(array("ec"=>$this->_table),"e.enumerationId = ec.ancestor", array())
			       ->where('e.active = 1')
			       ->where("ec.descendant = ?",(int)$enumerationId)
			       ->order("e.name ASC");
		if ($depth !== null) {
			$dbSelect->where("ec.depth = ?",(int)$depth);
		}
		//trigger_error($dbSelect,E_USER_NOTICE);
		$enumeration = new Enumeration();
		return $enumeration->getIterator($dbSelect);
	}

	public function deleteEnumeration($enumerationId) {
		$db = Zend_Registry::get("dbAdapter");

		$enumeration = new Enumeration();
		$enumeration->enumerationId = (int)$enumerationId;
		$enumeration->setPersistMode(WebVista_Model_ORM::DELETE);
		$enumeration->persist();

		// delete recursively
		$enumerationIterator = $this->getAllDescendants($enumeration->enumerationId,1);
		foreach ($enumerationIterator as $enum) {
			$this->deleteEnumeration($enum->enumerationId);
		}

		$db->beginTransaction();
		try {
			// remove all its descendants
			$sql = "DELETE `ec1` FROM `enumerationsClosure` AS `ec1`
				JOIN `enumerationsClosure` `ec2` USING (`descendant`)
				WHERE ec2.`ancestor` = {$enumerationId}";
			$db->query($sql);

			$db->commit();
		}
		catch (Exception $e) {
			$db->rollBack();
			trigger_error($e->getMessage(),E_USER_NOTICE);
		}
	}

	public function insertEnumeration($data,$parentId=0) {
		$db = Zend_Registry::get("dbAdapter");
		$enumParent = new Enumeration();
		$enumParent->enumerationId = $parentId;
		$enumParent->populate();

		$enumeration = new Enumeration();
		$fields = $enumeration->ormFields();
		foreach ($data as $key=>$value) {
			if (!in_array($key,$fields)) {
				continue;
			}
			$enumeration->$key = $value;
		}
		if (!strlen($enumeration->ormClass) > 0) {
			// check if parent item has an ormClass and id defined and use that to its child
			if (strlen($enumParent->ormClass) > 0) {
				$enumeration->ormClass = $enumParent->ormClass;
				// we only need to use the parent ormId if child's ormId less than or equal to 0
				if ($enumeration->ormId <= 0) {
					// temporarily comment out
					//$enumeration->ormId = $enumParent->ormId;
				}
			}
		}
		// we only need to use the parent ormEditMethod if child's ormEditMethod not defined
		if (!strlen($enumeration->ormEditMethod) > 0) {
			$enumeration->ormEditMethod = $enumParent->ormEditMethod;
		}
		$enumeration->persist();
		$enumerationId = $enumeration->enumerationId;

		$sql = "INSERT INTO enumerationsClosure (ancestor,descendant,depth)
			  SELECT ancestor,$enumerationId,depth+1 FROM enumerationsClosure
			  WHERE descendant = $parentId
			  UNION ALL
			  SELECT $enumerationId,$enumerationId,0";
		$db->query($sql);

		return $enumerationId;
	}

	public function getDepthById($enumerationId) {
		$db = Zend_Registry::get("dbAdapter");
		$dbSelect = $db->select()->from($this->_table)
			       ->where("descendant = ?",(int)$enumerationId)
			       ->order("depth DESC")
			       ->limit(1);
		$depth = 0;
		if ($row = $db->fetchRow($dbSelect)) {
			$depth = $row['depth'];
		}
		return $depth;
	}

	public function getEnumerationsClosureId() {
		return $this->ancestor;
	}

}
