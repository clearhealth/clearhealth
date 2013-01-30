<?php
/*****************************************************************************
*       DiscountTable.php
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


class DiscountTable extends WebVista_Model_ORM {

	protected $guid;
	protected $discountId;
	protected $name;
	protected $insuranceProgramIds;
	protected $discount; // either a percent, a negative number or a positive number
	protected $discountType;
	protected $dateStart;
	protected $dateEnd;
	protected $rowOrder;
	protected $familySize1;
	protected $income1;
	protected $familySize2;
	protected $income2;
	protected $familySize3;
	protected $income3;
	protected $familySize4;
	protected $income4;
	protected $familySize5;
	protected $income5;
	protected $familySize6;
	protected $income6;
	protected $familySize7;
	protected $income7;
	protected $familySize8;
	protected $income8;
	protected $familySize9;
	protected $income9;
	protected $familySize10;
	protected $income10;

	protected $_table = 'discountTables';
	protected $_primaryKeys = array('guid','discountId');
	protected $_incomes = array();

	const DISCOUNT_TYPE_ENUM_NAME = 'Discount Type';
	const DISCOUNT_TYPE_ENUM_KEY = 'DISCOUNT';

	const DISCOUNT_TYPE_FLAT_VISIT = 'FLAT_VISIT';
	const DISCOUNT_TYPE_FLAT_CODE = 'FLAT_CODE';
	const DISCOUNT_TYPE_PERC_VISIT = 'PERC_VISIT';
	const DISCOUNT_TYPE_PERC_CODE = 'PERC_CODE';

	public function persist() {
		if (!strlen($this->guid) > 0) {
			$this->guid = str_replace('-','',NSDR::create_guid());
		}
		return parent::persist();
	}

	public function addIncome(self $income) {
		trigger_error($income->discountId);
		$this->_incomes[] = $income;
	}

	public function getDiscountTypes() {
		$discountTypes = array();
		$enumeration = new Enumeration();
		$enumeration->populateByUniqueName(self::DISCOUNT_TYPE_ENUM_NAME);
		$enumerationClosure = new EnumerationClosure();
		$enumerationIterator = $enumerationClosure->getAllDescendants($enumeration->enumerationId,1);
		foreach ($enumerationIterator as $enum) {
			$discountTypes[$enum->key] = $enum->name;
		}
		return $discountTypes;
	}

	public function getDiscountTableId() {
		return $this->guid;
	}

	public function getIteratorByDistinctGuid() {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->group('guid')
				->order('name');
		return $this->getIterator($sqlSelect);
	}

	public function getIteratorByGuid($guid=null) {
		$db = Zend_Registry::get('dbAdapter');
		if ($guid === null) {
			$guid = $this->guid;
		}
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('guid = '.$db->quote($guid))
				->order('name');
		return $this->getIterator($sqlSelect);
	}

	public function populateByGuid($guid=null) {
		$db = Zend_Registry::get('dbAdapter');
		if ($guid === null) {
			$guid = $this->guid;
		}
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('guid = '.$db->quote($guid))
				->limit(1);
		return $this->populateWithSql($sqlSelect);
	}

	public function updateGuid($oldGuid,$newGuid) {
		$db = Zend_Registry::get('dbAdapter');
		if (!strlen($newGuid) > 0) {
			$newGuid = str_replace('-','',NSDR::create_guid());
		}
		$sql = 'UPDATE `'.$this->_table.'` SET `guid` = '.$db->quote($newGuid).' WHERE (`guid` = '.$db->quote($oldGuid).')';
		return $db->query($sql);
	}

	public function deleteByGuid($guid=null) {
		if ($guid === null) $guid = $this->guid;
		$db = Zend_Registry::get('dbAdapter');
		$sql = 'DELETE FROM `'.$this->_table.'` WHERE `guid` = '.$db->quote($guid);
		return $db->query($sql);
	}

	public function hasConflicts() {
		$db = Zend_Registry::get('dbAdapter');
		$ret = false;
		$dateStart = $db->quote($this->dateStart);
		$dateEnd = $db->quote($this->dateEnd);
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('guid != '.$db->quote($this->guid))
				->where('('.$dateStart.' >= dateStart AND '.$dateStart.' <= dateEnd) OR ('.$dateEnd.' >= dateStart AND '.$dateEnd.' <= dateEnd)');
		$orWhere = array();
		foreach (explode(',',$this->insuranceProgramIds) as $ip) {
			$ip = (int)$ip;
			if (!$ip > 0) continue;
			$orWhere[] = "`insuranceProgramIds` LIKE '%{$ip}%'";
		}
		if (isset($orWhere[0])) {
			$sqlSelect->where(implode(' OR ',$orWhere));
		}
		$sqlSelect->limit(1);
		WebVista::debug($sqlSelect->__toString());
		if ($row = $db->fetchRow($sqlSelect)) {
			$ret = true;
		}
		return $ret;
	}

	public static function checkDiscount($insuranceProgramId,$dateOfVisit,$familySize,$monthlyIncome) {
		$db = Zend_Registry::get('dbAdapter');
		$ret = false;
		$familySize = $db->quote((int)$familySize);
		$monthlyIncome = $db->quote((float)$monthlyIncome);
		$orWhere = array();
		for ($i = 1; $i <= 10; $i++) {
			$orWhere[] = '(familySize'.$i.' != 0 AND familySize'.$i.' = '.$familySize.' AND income'.$i.' >= '.$monthlyIncome.')';
		}
		$orm = new self();
		$sqlSelect = $db->select()
				->from($orm->_table)
				->where(implode(' OR ',$orWhere))
				->where($db->quote($dateOfVisit).' BETWEEN `dateStart` AND `dateEnd`')
				->where("insuranceProgramIds LIKE '%".(int)$insuranceProgramId."%'")
				->limit(1);
		if ($row = $db->fetchRow($sqlSelect)) {
			for ($i = 1; $i <= 10; $i++) {
				if ($row['familySize'.$i] != $familySize) continue;
				$row['income'] = $row['income'.$i];
			}
			$ret = $row;
		}
		return $ret;
	}

}
