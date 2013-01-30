<?php
/*****************************************************************************
*       InsuredRelationship.php
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


class InsuredRelationship extends WebVista_Model_ORM {

	protected $insured_relationship_id;
	protected $insurance_program_id;
	protected $insuranceProgram;
	protected $person_id;
	protected $person;
	protected $subscriber_id;
	protected $subscriber_to_patient_relationship;
	protected $subscriber;
	protected $copay;
	protected $assigning;
	protected $group_name;
	protected $group_number;
	protected $default_provider;
	protected $program_order;
	protected $effective_start;
	protected $effective_end;
	protected $active;
	protected $dateLastVerified;
	protected $verified;
	protected $desc;

	protected $_table = 'insured_relationship';
	protected $_primaryKeys = array('insured_relationship_id');
	protected $_legacyORMNaming = true;
	protected $_cascadePersist = false;

	const VERIFIED_MANUALLY = 1;
	const VERIFIED_AUTO_EACH_APPOINTMENT = 2;
	const VERIFIED_AUTO_30_DAYS = 3;

	public function __construct() {
		parent::__construct();
		$this->insuranceProgram = new InsuranceProgram();
		$this->insuranceProgram->_cascadePersist = $this->_cascadePersist;
		$this->person = new Person();
		$this->person->_cascadePersist = $this->_cascadePersist;
		$this->subscriber = new Person();
	}

	public function populate() {
		$ret = parent::populate();
		// cascade populate fix
		$this->subscriber->personId = (int)$this->subscriberId;
		$this->subscriber->populate();
		return $ret;
	}

	public function persist() {
		if ($this->_persistMode != WebVista_Model_ORM::DELETE && (int)$this->program_order === 0) $this->program_order = self::maxProgramOrder($this) + 1;
		return parent::persist();
	}

	public function setSubscriber_id($id) {
		$this->setSubscriberId($id);
	}

	public function setSubscriberId($id) {
		$this->subscriber_id = (int)$id;
		$this->subscriber->personId = $this->subscriber_id;
	}

	public function getIteratorByPersonId($personId = null) {
		if ($personId === null) {
			$personId = $this->personId;
		}
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			       ->from($this->_table)
			       ->where('person_id = ?',(int)$personId)
			       ->order('program_order');
		return $this->getIterator($dbSelect);
	}

	public function getProgramList($personId = null) {
		if ($personId === null) {
			$personId = $this->personId;
		}
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from(array('ir'=>$this->_table))
				->join(array('ip'=>'insurance_program'),'ip.insurance_program_id=ir.insurance_program_id',array('insurance_program_id','name'))
				->join(array('c'=>'company'),'c.company_id = ip.company_id',array('company_name'=>'name'))
				->where('ir.person_id = ?',(int)$personId)
				->where('ir.active = 1')
				->order('c.name');
		$insurancePrograms = array();
		foreach ($db->fetchAll($sqlSelect) as $row) {
			$insurancePrograms[$row['insurance_program_id']] = $row['company_name'].'->'.$row['name'];
		}
		return $insurancePrograms;
	}

	public function getActiveEligibility($personId = null) {
		if ($personId === null) {
			$personId = $this->personId;
		}
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			       ->from($this->_table)
			       ->where('person_id = ?',(int)$personId)
			       ->where('active = 1');
		return $this->getIterator($dbSelect);
	}

	public function getExpires() {
		return date('Y-m-d',strtotime($this->effectiveEnd));
	}

	public function getDisplayDateLastVerified() {
		return substr($this->dateLastVerified,0,10);
		if ($date == date('Y-m-d')) {
			$date = 'Today';
		}
		return $date;
	}

	public function getDisplayProgram() {
		return $this->insuranceProgram->company->name.'->'.$this->insuranceProgram->name;
	}

	public function getDisplayExpires() {
		$effectiveEnd = $this->expires;
		$expires = strtotime($effectiveEnd);
		$today = strtotime(date('Y-m-d'));
		// 86400 seconds = 1 day, 30 days = 2592000
		$days30 = 2592000;
		if ($expires < $today) {
			$color = 'red';
		}
		else if ($expires > ($today + $days30)) {
			$color = 'white';
		}
		else {
			$color = 'yellow';
		}
		return $effectiveEnd.':'.$color;
	}

	public function getDisplayVerified() {
		$ret = '';
		$result = self::getVerifiedOptions();
		if (isset($result[$this->verified])) {
			$ret = $result[$this->verified];
		}
		return $ret;
	}

	public static function getVerifiedOptions() {
		return array(
			self::VERIFIED_MANUALLY => 'Manually Check Eligibility',
			self::VERIFIED_AUTO_EACH_APPOINTMENT => 'Automatically Verify Each Appointment',
			self::VERIFIED_AUTO_30_DAYS => 'Automatically Verify 30 Days before Expiration',
		);
	}

	public static function eligibilityCheck($insuredRelationshipId) {
		$ir = new self();
		$ir->insured_relationship_id = $insuredRelationshipId;
		$ir->populate();

		$result = self::_dummyHook();
		$ir->effectiveStart = $result['start'];
		$ir->effectiveEnd = $result['end'];
		$ir->desc = $result['desc'];

		$ir->dateLastVerified = date('Y-m-d H:i:s');
		$ir->persist();
		return $ir;
	}

	protected static function _dummyHook() {
		$ret = array();
		$ret['desc'] = 'OP/25 80%'; // will be provided soon
		$ret['start'] = date('Y-m-d'); // random date
		$months = rand(6,12);
		$ret['end'] = date('Y-m-d',strtotime('+'.$months.' months'));
		return $ret;
	}

	public function getDefaultActivePayer() {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table,'insurance_program_id')
				->where('person_id = ?',(int)$this->personId)
				->where('active = 1')
				->order('program_order ASC')
				->limit(1);
		$ret = 0;
		if ($row = $db->fetchRow($sqlSelect)) {
			$ret = (int)$row['insurance_program_id'];
		}
		return $ret;
	}

	public static function maxProgramOrder(self $payer) {
		$db = Zend_Registry::get('dbAdapter');
		$table = $payer->_table;

		$sql = 'SELECT MAX(program_order) AS programOrder FROM '.$table.' WHERE person_id = '.(int)$payer->personId;
		$ret = 0;
		if ($row = $db->fetchRow($sql)) {
			$ret = (int)$row['programOrder'];
		}
		return $ret;
	}

	public static function reorder(self $from,self $to) {
		$db = Zend_Registry::get('dbAdapter');
		$table = $from->_table;
		$fromOrder = (int)$from->programOrder;
		$toOrder = (int)$to->programOrder;
		$personId = (int)$from->personId;
		if ($fromOrder > $toOrder) { // bottom to top
			$sql = 'UPDATE '.$table.' SET program_order = (program_order + 1) WHERE person_id = '.$personId.' AND program_order > '.$toOrder;
			$db->query($sql);
			$sql = 'UPDATE '.$table.' SET program_order = '.($toOrder+1).' WHERE insured_relationship_id = '.(int)$from->insuredRelationshipId;
			$db->query($sql);
			$sql = 'UPDATE '.$table.' SET program_order = (program_order - 1) WHERE person_id = '.$personId.' AND program_order > '.($fromOrder+1);
			$db->query($sql);
		}
		else if ($fromOrder < $toOrder) { // top to bottom
			$sql = 'UPDATE '.$table.' SET program_order = (program_order - 1) WHERE person_id = '.$personId.' AND program_order < '.($toOrder+1);
			$db->query($sql);
			$sql = 'UPDATE '.$table.' SET program_order = '.($toOrder).' WHERE insured_relationship_id = '.(int)$from->insuredRelationshipId;
			$db->query($sql);
		}
		else {
			return false; // nothing to reorder here
		}
		return true;
	}

	public static function filterByPayerPersonIds($insuranceProgramId,$personId) {
		$db = Zend_Registry::get('dbAdapter');
		$orm = new self();
		$sqlSelect = $db->select()
				->from($orm->_table)
				->where('insurance_program_id = ?',(int)$insuranceProgramId)
				->where('person_id = ?',(int)$personId)
				->where('active = 1')
				->order('program_order')
				->limit(1);
		$orm->populateWithSql($sqlSelect->__toString());
		return $orm;
	}

}
