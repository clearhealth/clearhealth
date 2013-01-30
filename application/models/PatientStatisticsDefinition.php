<?php
/*****************************************************************************
*       PatientStatisticsDefinition.php
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


class PatientStatisticsDefinition extends WebVista_Model_ORM {

	protected $patientStatisticsDefinitionId;
	protected $guid;
	protected $name;
	protected $type;
	protected $value;
	protected $active;

	protected $_primaryKeys = array('patientStatisticsDefinitionId');
	protected $_table = 'patientStatisticsDefinitions';

	protected $_types = array(
		1 => 'Enumeration',
		2 => 'Input',
		3 => 'Checkbox',
		4 => 'Date',
	);
	protected $_dataTypes = array(
		1 => 'varchar(255)',
		2 => 'varchar(255)',
		3 => 'tinyint',
		4 => 'datetime',
	);
	protected $_origName = '';

	const TYPE_ENUM = 1;
	const TYPE_INPUT = 2;
	const TYPE_CHECKBOX = 3;
	const TYPE_DATE = 4;

	public function populate() {
		$ret = parent::populate();
		$this->_origName = $this->name;
		return $ret;
	}

	public function persist() {
		$db = Zend_Registry::get('dbAdapter');
		if ($this->_persistMode == WebVista_Model_ORM::DELETE) {
			if (!strlen($this->name) > 0) {
				$this->populate();
			}
			$sql = 'ALTER TABLE `patientStatistics` DROP COLUMN `'.$this->name.'`';
		}
		else {
			$dataType = $this->_dataTypes[$this->type];
			$sql = 'ALTER TABLE `patientStatistics` ADD `'.$this->name.'` '.$dataType.' NOT NULL';
			if (strlen($this->_origName) > 0) {
				$sql = 'ALTER TABLE `patientStatistics` CHANGE `'.$this->_origName.'` `'.$this->name.'` '.$dataType.' NOT NULL';
			}
		}
		try {
			$db->query($sql);
			parent::persist();
		}
		catch (Exception $e) {
			trigger_error($e->getMessage(),E_USER_ERROR);
		}
	}

	public function getDisplayedValue() {
		$ret = null;
		if ($this->type != self::TYPE_ENUM) {
			return $ret;
		}
		$enumeration = new Enumeration();
		$enumeration->enumerationId = (int)$this->value;
		$enumeration->populate();
		return $enumeration->name;
	}

	public function isNameExists($name) {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table,$this->_primaryKeys)
				->where('name = ?',$name)
				->where('name != ?',$this->name);
		$ret = false;
		if ($row = $db->fetchRow($sqlSelect)) {
			$ret = true;
		}
		return $ret;
	}

	public function getAllActive() {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('active = 1');
		return $this->getIterator($sqlSelect);
	}

	public static function getPatientStatistics($personId) {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('patientStatistics')
				->where('personId = ?',(int)$personId);
		return $db->fetchRow($sqlSelect);
	}

	public static function updatePatientStatistics($personId,$name,$value) {
		$db = Zend_Registry::get('dbAdapter');
		$ret = false;
		$data = array();
		$data['personId'] = $personId;
		if ($stat = self::getPatientStatistics($personId)) {
			$data = $stat;
		}
		$data[$name] = $value;
		try {
			if (!$stat) {
				$db->insert('patientStatistics',$data);
			}
			else {
				$db->update('patientStatistics',$data,'personId = '.(int)$personId);
			}
			$ret = true;
		}
		catch (Exception $e) {
			trigger_error($e->getMessage(),E_USER_ERROR);
		}
		return $ret;
	}

	public function getDisplayName() {
		$name = trim($this->name);
		return ucwords(str_replace('_',' ',$name));
	}

	public static function listRaceCodes() {
		$race = array(
			'1002-5'=>'American Indian or Alaska Native',
			'2028-9'=>'Asian',
			'2076-8'=>'Native Hawaiian or Other Pacific Islander',
			'2054-5'=>'Black or African American',
			'2106-3'=>'White',
			'2131-1'=>'Other',
			''=>'Unknown/undetermined',
		);
		return $race;
	}

}
