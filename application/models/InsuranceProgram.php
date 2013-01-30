<?php
/*****************************************************************************
*       InsuranceProgram.php
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


class InsuranceProgram extends WebVista_Model_ORM {

	protected $insurance_program_id;
	protected $payer_type;
	protected $company_id;
	protected $company;
	protected $name;
	protected $fee_schedule_id;
	protected $x12_sender_id;
	protected $x12_receiver_id;
	protected $x12_version;
	protected $address_id;
	protected $address;
	protected $funds_source;
	protected $program_type;
	protected $payer_identifier;

	protected $_table = 'insurance_program';
	protected $_primaryKeys = array('insurance_program_id');
	protected $_legacyORMNaming = true;
	protected $_cascadePersist = false;

	const INSURANCE_ENUM_NAME = 'Insurance Preferences';
	const INSURANCE_ENUM_KEY = 'INSPREF';
	const INSURANCE_ASSIGNING_ENUM_NAME = 'Assigning';
	const INSURANCE_ASSIGNING_ENUM_KEY = 'ASSIGNING';
	const INSURANCE_SUBSCRIBER_ENUM_NAME = 'Subscriber';
	const INSURANCE_SUBSCRIBER_ENUM_KEY = 'SUBSCRIBER';
	const INSURANCE_PAYER_TYPE_ENUM_NAME = 'Payer Type';
	const INSURANCE_PAYER_TYPE_ENUM_KEY = 'PAYERTYPE';
	const INSURANCE_PROGRAM_TYPE_ENUM_NAME = 'Program Type';
	const INSURANCE_PROGRAM_TYPE_ENUM_KEY = 'PROGTYPE';
	const INSURANCE_FUNDS_SOURCE_ENUM_NAME = 'Funds Source';
	const INSURANCE_FUNDS_SOURCE_ENUM_KEY = 'FUNDSSRC';

	public function __construct() {
		parent::__construct();
		$this->company = new Company();
		$this->company->_cascadePersist = $this->_cascadePersist;
		$this->address = new Address();
		$this->address->_cascadePersist = $this->_cascadePersist;
	}

	public static function getInsurancePrograms() {
		static $insurancePrograms = null; // to minimize multiple queries on multiple calls
		if ($insurancePrograms !== null) return $insurancePrograms;
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			       ->from(array('ip'=>'insurance_program'),array('insurance_program_id','name'))
			       ->join(array('c'=>'company'),'c.company_id = ip.company_id',array('company_name'=>'name'))
			       ->order('c.name')
			       ->order('ip.name');
		$insurancePrograms = array();
		foreach ($db->fetchAll($dbSelect) as $row) {
			$insurancePrograms[$row['insurance_program_id']] = $row['company_name'].'->'.$row['name'];
		}
		return $insurancePrograms;
	}

	public function getIteratorByCompanyId($companyId = null) {
		if ($companyId === null) {
			$companyId = $this->companyId;
		}
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			       ->from($this->_table)
			       ->where('company_id = ?',(int)$companyId)
			       ->order('name');
		return $this->getIterator($dbSelect);
	}

	public static function getListProgramTypes() {
		$enumeration = new Enumeration();
		$enumeration->populateByUniqueName(self::INSURANCE_ENUM_NAME);

		$enumerationsClosure = new EnumerationsClosure();
		$enumerationIterator = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
		$ret = array();
		foreach ($enumerationIterator as $enum) {
			if ($enum->key != self::INSURANCE_PROGRAM_TYPE_ENUM_KEY) continue;
			$iterator = $enumerationsClosure->getAllDescendants($enum->enumerationId,1);
			$ret = $iterator->toArray('key','name');
			break;
		}
		return $ret;
	}

	public static function getInsuranceProgram($insuranceProgramId) {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from(array('ip'=>'insurance_program'),array('insurance_program_id','name'))
				->join(array('c'=>'company'),'c.company_id = ip.company_id',array('company_name'=>'name'))
				->where('ip.insurance_program_id = ?',(int)$insuranceProgramId)
				->order('c.name')
				->order('ip.name');
		$insuranceProgram = '';
		if ($row = $db->fetchRow($sqlSelect)) {
			$insuranceProgram = $row['company_name'].'->'.$row['name'];
		}
		return $insuranceProgram;
	}

	public static function getInsuranceProgramsByIds($ids) {
		$x = explode(',',$ids);
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from(array('ip'=>'insurance_program'),array('insurance_program_id','name'))
				->join(array('c'=>'company'),'c.company_id = ip.company_id',array('company_name'=>'name'))
				->order('c.name')
				->order('ip.name');
		foreach ($x as $id) {
			$sqlSelect->orWhere('ip.insurance_program_id = ?',(int)$id);
		}
		$insurancePrograms = array();
		if ($rows = $db->fetchAll($sqlSelect)) {
			foreach ($rows as $row) {
				$insurancePrograms[$row['insurance_program_id']] = $row['company_name'].'->'.$row['name'];
			}
		}
		return $insurancePrograms;
	}

	public static function getListInsurancePreferences() {
		$enumeration = new Enumeration();
		$enumeration->populateByUniqueName(self::INSURANCE_ENUM_NAME);
		$enumerationsClosure = new EnumerationsClosure();
		$enumerationIterator = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
		$ret = array();
		foreach ($enumerationIterator as $enum) {
			$ret[$enum->key] = $enumerationsClosure->getAllDescendants($enum->enumerationId,1)->toArray('key','name');
		}
		return $ret;
	}

	public function getDisplayFeeSchedule() {
		$data = array();
		$id = (int)$this->insurance_program_id;
		if ($id > 0) {
			$db = Zend_Registry::get('dbAdapter');
			$sqlSelect = $db->select()
					->from('feeSchedules',array('name','dateOfServiceStart','dateOfServiceEnd'))
					->where('insuranceProgramIds LIKE ?','%'.$id.'%')
					->group('guid')
					->order('name');
			$stmt = $db->query($sqlSelect);
			while ($row = $stmt->fetch()) {
				$data[] = $row['name'].' '.date('Y-m-d',strtotime($row['dateOfServiceStart'])).', '.date('Y-m-d',strtotime($row['dateOfServiceEnd']));
			}
		}
		return implode("\n",$data);
	}

	public static function lookupSystemId($programName) {
		$payerId = 0;
		foreach (self::getInsurancePrograms() as $key=>$value) {
			if ($value == 'System->'.$programName) {
				$payerId = (int)$key;
				break;
			}
		}
		return $payerId;
	}

}
