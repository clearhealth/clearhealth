<?php
/*****************************************************************************
*       Appointment.php
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


class Appointment extends WebVista_Model_ORM {
    protected $appointmentId;
    protected $arrived;
    protected $title;
    protected $reason;
    protected $walkin;
    protected $createdDate;
    protected $lastChangeId;
    protected $lastChange;
    protected $lastChangeDate;
    protected $creatorId;
    protected $creator;
    protected $practiceId;
    protected $providerId;
    protected $patientId;
    protected $provider;
    protected $patient;
    protected $roomId;
    protected $appointmentCode;
    protected $start;
    protected $end;
    protected $_table = "appointments";
    protected $_primaryKeys = array("appointmentId");

	protected $_cascadePersist = false;

	function __construct() {
		parent::__construct();
		$this->patient = new Patient();
		$this->patient->_cascadePersist = false;
		$this->provider = new Provider();
		$this->provider->_cascadePersist = false;
		$this->creator = new User();
		$this->creator->_cascadePersist = false;
		$this->lastChange = new User();
		$this->lastChange->_cascadePersist = false;
	}

	public function populate() {
		$ret = parent::populate();
		$this->patient = new Patient();
		$this->patient->setPersonId($this->patientId);
		$this->patient->populate();
		$this->provider = new Provider();
		$this->provider->setPersonId($this->providerId);
		$this->provider->populate();
		$this->creator->userId = $this->creatorId;
		$this->creator->populate();
		$this->lastChange->userId = $this->lastChangeId;
		$this->lastChange->populate();
		return $ret;
	}

    public static function getObject($mxdFilters = array()) {
        $objApp = new Appointment();
        $db = Zend_Registry::get('dbAdapter');
        $objSelect = $db->select()
                        ->from('appointments');

        if (is_string($mxdFilters)) {
            $objSelect->where($mxdFilters);
        }
        else if (is_array($mxdFilters)) {
            foreach ($mxdFilters as $fieldName=>$mxdValue) {
                // set the default operator to ==
                $fieldOperator = '=';
                $fieldValue = '';
                if (is_array($mxdValue)) {
                    $ctr = count($mxdValue);
                    // if empty array, just continue to the next item
                    if ($ctr < 1) {
                        continue;
                    }
                    else {
                        switch ($ctr) {
                            case 1:
                                $fieldValue = array_pop($mxdValue);
                                break;
                            case 2:
                                if (isset($mxdValue['operator'])) {
                                    $fieldOperator = $mxdValue['operator'];
                                    unset($mxdValue['operator']);
                                    $fieldValue = array_pop($mxdValue);
                                }
                                else {
                                    // use the first element of the array as its operator
                                    $fieldOperator = array_shift($mxdValue);
                                    // use the 2nd element of the array as its value
                                    $fieldValue = array_shift($mxdValue);
                                }
                                break;
                            default:
                                continue;
                                break;
                        }
                    }
                }

                if ($fieldValue == '') {
                    continue;
                }
                $objSelect->where("$fieldName $fieldOperator ?", $fieldValue);
            }
        }
        $objIterator = $objApp->getIterator($objSelect);
        return $objIterator;
    }

    function getIterator($objSelect = null) {
        return new AppointmentIterator($objSelect);
    }

	public function checkRules() {
		$ret = false;
		// check double booking
		if ($this->isDoubleBook()) {
			$ret = __('Double booking');
		}
		// check outside of schedule time
		if ($this->isOutsideScheduleTime()) {
			if ($ret !== false) $ret .= ' AND ';
			$ret .= __('Outside of schedule time');
		}
		return $ret;
	}

	public function isDoubleBook() {
		$ret = false;
		$db = Zend_Registry::get('dbAdapter');
		$start = $db->quote(date('Y-m-d H:i:s',strtotime($this->start)));
		$end = $db->quote(date('Y-m-d H:i:s',strtotime($this->end)));
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('appointmentId != ?',(int)$this->appointmentId)
				->where('providerId = ?',(int)$this->providerId)
				->where('roomId = ?',(int)$this->roomId)
				->where('(('.$start.' >= `start` AND '.$start.' < `end`) OR ('.$end.' > `start` AND '.$end.' < `end`)) OR ((`start` >= '.$start.' AND `start` < '.$end.') OR (`end` > '.$start.' AND `end` < '.$end.'))')
				->where('appointmentCode != ?','CAN')
				->where('start <= end')
				->limit(1);
		//trigger_error($sqlSelect->__toString());
		if ($row = $db->fetchRow($sqlSelect)) {
			$ret = true;
		}
		return $ret;
	}

	public function isOutsideScheduleTime() {
		$ret = true;
		$scheduleEvent = new ScheduleEvent();
		$db = Zend_Registry::get('dbAdapter');
		$start = $db->quote(date('Y-m-d H:i:s',strtotime($this->start)));
		$end = $db->quote(date('Y-m-d H:i:s',strtotime($this->end)));
		$sqlSelect = $db->select()
				->from($scheduleEvent->_table)
				->where('providerId = ?',(int)$this->providerId)
				->where($start.' >= `start`')
				->where($start.' <= `end`')
				->where($end.' >= `start`')
				->where($end.' <= `end`')
				->where('start <= end')
				->limit(1);
		//trigger_error($sqlSelect->__toString(),E_USER_NOTICE);
		$roomId = (int)$this->roomId;
		if ($roomId > 0) {
			$sqlSelect->join('buildings','buildings.id = '.$scheduleEvent->_table.'.buildingId')
				->join('rooms','rooms.building_id = buildings.id')
				->where('rooms.id = ?',$roomId);
		}
		if ($row = $db->fetchRow($sqlSelect)) {
			$ret = false;
		}
		return $ret;
	}

	public function getIteratorByPatientId($patientId=null) {
		if ($patientId === null) $patientId = $this->patientId;
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('patientId = ?',(int)$patientId)
				->order('start DESC');
		return $this->getIterator($sqlSelect);
	}

	public function getIter($excludeCancelled=true) {
		$db = Zend_Registry::get('dbAdapter');
		$providerId = (int)$this->providerId;
		$roomId = (int)$this->roomId;
		$start = $this->start;
		$end = $this->end;
		if (!strlen($start) > 0) $start = date('Y-m-d 00:00:00');
		if (!strlen($end) > 0) $end = date('Y-m-d 23:59:59',strtotime($start));
		$dateStart = date('Y-m-d H:i:s',strtotime($start));
		$dateEnd = date('Y-m-d H:i:s',strtotime($end));

		// check date end's time if it's 23:59, if so then add one day and set it's time to 00:00:00
		$x = explode(' ',$dateEnd);
		if (substr($x[1],0,5) == '23:59') $dateEnd = date('Y-m-d 00:00:00',strtotime('+1 day',$endTime));

		$sqlSelect = $db->select()
				->from($this->_table)
				->where('roomId = ?',$roomId)
				->where('providerId = ?',$providerId)
				->where('start >= ?',$dateStart)
				->where('end <= ?',$dateEnd)
				->where('start <= end')
				->order('start ASC');
		if ($excludeCancelled) $sqlSelect->where("appointmentCode != 'CAN'");
		return new AppointmentIterator($sqlSelect);
	}

}
