<?php
/*****************************************************************************
*       PatientEducation.php
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


class PatientEducation extends WebVista_Model_ORM {

	protected $code;
	protected $patientId;
	protected $level;
	protected $education;
	protected $comments;
	protected $dateTime;

	protected $_primaryKeys = array('code','patientId');
	protected $_table = 'patientEducations';

	const ENUM_PARENT_NAME = 'Patient Education Preferences';
	const ENUM_TOPIC_PARENT_NAME = 'Education Topic Preferences';
	const ENUM_LEVEL_PARENT_NAME = 'Education Level Preferences';

	const ENUM_EDUC_PARENT_NAME = 'Education Preferences';
	const ENUM_EDUC_SECTION_NAME = 'Section';
	const ENUM_EDUC_SECTION_OTHER_NAME = 'Other';
	const ENUM_EDUC_SECTION_COMMON_NAME = 'Common';
	const ENUM_EDUC_LEVEL_NAME = 'Level';

	public function persist() {
		if (!$this->dateTime || $this->dateTime == '0000-00-00 00:00:00') {
			$this->dateTime = date('Y-m-d H:i:s');
		}
		return parent::persist();
	}

	public function getPatientEducationId() {
		return $this->code.';'.$this->patientId;
	}

	public function populate() {
		if ($this->code != $this->patientId) return parent::populate();
		$x = explode(';',$this->code);
		$this->code = $x[0];
		if (isset($x[1])) {
			$this->patientId = (int)$x[1];
		}
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('code = ?',(string)$this->code)
				->where('patientId = ?',(string)$this->patientId)
				->limit(1);
		$ret = $this->populateWithSql($sqlSelect->__toString());
		$this->postPopulate();
		return $ret;
	}

	public function getPersonId() {
		return $this->patientId;
	}

}
