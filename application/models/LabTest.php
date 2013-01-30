<?php
/*****************************************************************************
*       LabTest.php
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


class LabTest extends WebVista_Model_ORM {
	protected $lab_test_id;
	protected $lab_order_id;
	protected $labOrder;
	protected $order_num;
	protected $filer_order_num;
	protected $observation_time;
	protected $specimen_received_time;
	protected $report_time;
	protected $ordering_provider;
	protected $service;
	protected $component_code;
	protected $status;
	protected $clia_disclosure;
	protected $_table = "lab_test";
	protected $_primaryKeys = array("lab_test_id");
	protected $_legacyORMNaming = true;
	protected $_cascadePersist = false;

	function __construct() {
		parent::__construct();
		$this->labOrder = new LabOrder();
	}

	public function populateByLabOrderId($labOrderId=null) {
		$db = Zend_Registry::get('dbAdapter');
		if ($labOrderId === null) $labOrderId = $this->lab_order_id;
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('lab_order_id = ?',(int)$labOrderId)
				->limit(1);
		return parent::populateWithSql($sqlSelect->__toString());
	}

}
