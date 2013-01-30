<?php
/*****************************************************************************
*       PatientVisitTypeIterator.php
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


class PatientVisitTypeIterator extends WebVista_Model_ORMIterator {

	public function __construct($dbSelect = null) {
		parent::__construct('PatientVisitType',$dbSelect);
	}

	public function setFilters(Array $filters) {
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			       ->from('patientVisitTypes');
		foreach ($filters as $key=>$value) {
			switch ($key) {
				case 'patientId':
				case 'visitId':
					$dbSelect->where($key.' = ?',(int)$value);
					break;
			}
		}
		$this->_dbSelect = $dbSelect;
		$this->_dbStmt = $db->query($this->_dbSelect);
	}

}
