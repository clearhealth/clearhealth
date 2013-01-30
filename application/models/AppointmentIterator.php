<?php
/*****************************************************************************
*       AppointmentIterator.php
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


class AppointmentIterator extends WebVista_Model_ORMIterator implements Iterator {

	public function __construct($dbSelect = null,$autoload=true) {
		$this->_ormClass = 'Appointment';
		if ($autoload) parent::__construct($this->_ormClass,$dbSelect);
	}

    public function current() {
        $ormObj = new $this->_ormClass();
        $row = $this->_dbStmt->fetch(null,null,$this->_offset);
        $ormObj->populateWithArray($row);
        return $ormObj;
    }


	public function setFilter(Array $filters) {
		$this->setFilters($filters);
	}

	public function setFilters(Array $filters) {
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()->from('appointments');
		foreach ($filters as $field=>$value) {
			switch($field) {
				case 'roomId':
				case 'providerId':
				case 'patientId':
					$dbSelect->where("{$field} = ?",(int)$value);
					break;
				case 'start':
					$dbSelect->where("start >= ?", $value);
					break;
				case 'end':
					$dbSelect->where("end <= ?", $value);
					break;
				case 'showCancelledAppointments':
					if (!$value) { // do not show cancelled appointments
						$where = "appointmentCode != 'CAN'";
						$dbSelect->where($where);
					}
					break;
			}
		}
		$dbSelect->order('start ASC');
		//trigger_error($dbSelect->__toString(),E_USER_NOTICE);
		$this->_dbSelect = $dbSelect;
		$this->_dbStmt = $db->query($this->_dbSelect);
	}

}
