<?php
/*****************************************************************************
*       LabResult.php
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


class LabResult extends WebVista_Model_ORM implements NSDRMethods {
	protected $lab_result_id;
	protected $lab_test_id;
	protected $labTest;
	protected $identifier;
	protected $value;
	protected $units;
	protected $reference_range;
	protected $abnormal_flag;
	protected $result_status;
	protected $observation_time;
	protected $producer_id;
	protected $description;
	protected $extra;
	protected $cliaPerformingOrg;
	protected $_table = "lab_result";
	protected $_primaryKeys = array("lab_result_id");
	protected $_legacyORMNaming = true;
	protected $_cascadePersist = false;

	function __construct() {
		parent::__construct();
		$this->labTest = new LabTest();
	}
	public function nsdrPersist($tthis,$context,$data) {
                return true;
        }

        public function nsdrPopulate($tthis,$context,$data) {
                $ret = array();
                //debug_print_backtrace();
		$labsIter = new LabsIterator();
                $filters = array();
                $filters['patientId'] = key($context);
		if (isset($context[key($context)]['filters']['description'])) {
                	$filters['description'] = $context[key($context)]['filters']['description'];
		}
                $filters['limit'] = '1';
                $labsIter->setFilters($filters);
                $ret = $labsIter->first()->toArray();
                return $ret;
        }

        public function nsdrMostRecent($tthis,$context,$data) {
                $ret = array();
                return $ret;
        }

	public function populateByFilter($key,$val) {
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			       ->from($this->_table)
			       ->where("$key = ?",$val);
		$retval = $this->populateWithSql($dbSelect->__toString());
		$this->postPopulate();
		return $retval;
	}

}
