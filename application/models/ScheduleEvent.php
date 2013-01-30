<?php
/*****************************************************************************
*       ScheduleEvent.php
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


class ScheduleEvent extends WebVista_Model_ORM {
    protected $scheduleEventId;
    protected $title;
    protected $scheduleCode;
    protected $providerId;
    protected $provider;
    protected $roomId;
    protected $room;
    protected $scheduleId;
	protected $schedule;
    protected $start;
    protected $end;
	protected $buildingId;
    protected $_table = "scheduleEvents";
    protected $_primaryKeys = array("scheduleEventId");
	protected $_cascadePersist = false;

	public function __construct() {
		parent::__construct();
		$this->provider = new Provider();
		$this->provider->_cascadePersist = false;
		$this->room = new Room();
		$this->room->_cascadePersist = false;
		$this->schedule = new Schedule();
		$this->schedule->_cascadePersist = false;
	}

	public function setProviderId($val) {
		$this->providerId = (int)$val;
		$this->provider->personId = $this->providerId;
	}

	public function setRoomId($val) {
		$this->roomId = (int)$val;
		$this->room->roomId = $this->roomId;
	}

	public function setScheduleId($val) {
		$this->scheduleId = (int)$val;
		$this->schedule->scheduleId = $this->scheduleId;
	}

    public function populateWithFilter($eventFilter) {
	$db = Zend_Registry::get('dbAdapter');
        $dbjSelect = $db->select()->from($this->_table);
        foreach ($eventFilter as $name=>$value) {
            $dbSelect->where("$name = ?", $value);
        }
    }

	public function getTitleByProviderId($providerId) {
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()->from($this->_table)
				->where('providerId=?', $providerId);
		$row = $db->fetchRow($dbSelect);
		$this->populateWithArray($row);
	}

	public function __get($key) {
		if (in_array($key,$this->ORMFields())) {
			return $this->$key;
		}
		elseif (in_array($key,$this->provider->ORMFields())) {
			return $this->provider->__get($key);
		}
		elseif (in_array($key,$this->room->ORMFields())) {
			return $this->room->__get($key);
		}
		elseif (in_array($key,$this->schedule->ORMFields())) {
			return $this->schedule->__get($key);
		}
		elseif (!is_null(parent::__get($key))) {
			return parent::__get($key);
		}
		elseif (!is_null($this->provider->__get($key))) {
			return $this->provider->__get($key);
		}
		elseif (!is_null($this->room->__get($key))) {
			return $this->room->__get($key);
		}
		elseif (!is_null($this->schedule->__get($key))) {
			return $this->schedule->__get($key);
		}
		return parent::__get($key);
	}

	public function deleteByDateRange() {
		$db = Zend_Registry::get('dbAdapter');
		$where = array();
		//$where[] = 'scheduleId = '.(int)$this->scheduleId;
		$where[] = 'providerId = '.(int)$this->providerId;
		$where[] = 'roomId = '.(int)$this->roomId;
		$where[] = 'start >= '.$db->quote($this->start);
		$where[] = 'end <= '.$db->quote($this->end);
		$where = implode(' AND ',$where);
		$db->delete($this->_table,$where);
	}

	public static function computeWeekDates($date) {
		$ret = array('start'=>$date,'end'=>$date);
		$strtotime = strtotime($date);
		$weekday = date('N',$strtotime); // 1 = Monday to 7 = Sunday
		$monday = 1;
		$sunday = 7;
		$weekend = $sunday - $weekday;
		$weekstart = $weekday - $monday;
		if ($weekend == 0) {
			$weekstart = 6;
		}
		else if ($weekstart == 0) {
			$weekend = 6;
		}
		$ret['start'] = date('Y-m-d',strtotime("-{$weekstart} days",$strtotime));
		$ret['end'] = date('Y-m-d',strtotime("+{$weekend} days",$strtotime));
		return $ret;
	}

	public static function getNumberOfEvents($providerId,$roomId,$dateStart,$dateEnd) {
		$ret = 0;
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from('scheduleEvents','COUNT(*) AS ctr')
				->where("roomId = ?",(int)$roomId)
				->where("providerId = ?",(int)$providerId)
				->where("start >= ?",$dateStart)
				->where("end <= ?",$dateEnd);
		trigger_error($sqlSelect->__toString(),E_USER_NOTICE);
		if ($row = $db->fetchRow($sqlSelect)) {
			$ret = $row['ctr'];
		}
		return $ret;
	}

	public static function getScheduleEventByFields($providerId,$roomId,$dateStart,$dateEnd,$fields) {
		$ret = array();
		$db = Zend_Registry::get('dbAdapter');
		$orm = new self();
		$sqlSelect = $db->select()
				->from($orm->_table)
				->where('roomId = ?',(int)$roomId)
				->where('providerId = ?',(int)$providerId)
				->where('start >= ?',$dateStart)
				->where('end <= ?',$dateEnd);
		trigger_error($sqlSelect->__toString(),E_USER_NOTICE);
		if ($rows = $db->fetchAll($sqlSelect)) {
			foreach ($rows as $row) {
				if (!is_array($fields)) {
					$tmp = $row[$fields];
				}
				else {
					$tmp = array();
					foreach ($fields as $field) {
						$tmp[$field] = $row[$field];
					}
				}
				$ret[] = $tmp;
			}
		}
		return $ret;
	}

	public function bookingAppointmentDetails($minutesInterval=1) {
		list($usec,$sec) = explode(' ',microtime());
		$appTs = ((float)$usec + (float)$sec);

		$ret = array();

		$providerId = (int)$this->providerId;
		$roomId = (int)$this->roomId;
		$dateStart = date('Y-m-d H:i:s',strtotime($this->start));
		$endTime = strtotime($this->end);
		$dateEnd = date('Y-m-d H:i:s',$endTime);
		// check date end's time if it's 23:59, if so then add one day and set it's time to 00:00:00
		$x = explode(' ',$dateEnd);
		if (substr($x[1],0,5) == '23:59') $dateEnd = date('Y-m-d 00:00:00',strtotime('+1 day',$endTime));

		$db = Zend_Registry::get('dbAdapter');
		$appSelect = $db->select()
				->from('appointments',array('start','end'))
				->where('roomId = ?',$roomId)
				->where('providerId = ?',$providerId)
				->where('start >= ?',$dateStart)
				->where('end <= ?',$dateEnd)
				->where('start <= end')
				->where("appointmentCode != 'CAN'")
				->order('start ASC');
		/*$sql = $appSelect->__toString();
		$sql = str_replace('SELECT','SELECT SQL_NO_CACHE',$sql);
		$sqlAppointment = $sql;*/

		$sqlSelect = $db->select()
				->from($this->_table,array('start','end'))
				->where('providerId = ?',$providerId)
				->where('roomId = ?',$roomId)
				->where('start >= ?', $dateStart)
				->where('end <= ?',$dateEnd)
				->where('start <= end')
				->order('start ASC');
		/*$sql = $sqlSelect->__toString();
		$sql = str_replace('SELECT','SELECT SQL_NO_CACHE',$sql);
		$sqlSchedule = $sql;*/
		$stmtSchedule = $db->query($sqlSelect);
		$stmtSchedule->setFetchMode(Zend_Db::FETCH_ASSOC);

		$stmtAppointment = $db->query($appSelect);
		$stmtAppointment->setFetchMode(Zend_Db::FETCH_ASSOC);

		$currentDate = null;
		$appStack = array();
		while ($row = $stmtAppointment->fetch()) {
			$date = substr($row['start'],0,10);
			if ($currentDate === null) $currentDate = $date;
			if ($date == $currentDate) {
				array_push($appStack, $row);
			}
			else {
				$hasUnbooked = $this->_checkUnbooked($ret,$currentDate,$appStack,$stmtSchedule,$minutesInterval);
				if ($hasUnbooked !== null) $ret[$currentDate] = $hasUnbooked;

				$appStack = array();
				array_push($appStack, $row);
				$currentDate = $date;
			}
		}
		if (isset($appStack[0])) {
			$hasUnbooked = $this->_checkUnbooked($ret,$currentDate,$appStack,$stmtSchedule,$minutesInterval);
			if ($hasUnbooked !== null) $ret[$currentDate] = $hasUnbooked;
		}
		while ($rowSchedule = $stmtSchedule->fetch()) {
			$dateSchedule = substr($rowSchedule['start'],0,10);
			$ret[$dateSchedule] = true; // default: has free time unless proven that all time schedules have appointments
		}

		$stmtAppointment->closeCursor();
		$stmtSchedule->closeCursor();

		list($usec,$sec) = explode(' ',microtime());
		$appTe = ((float)$usec + (float)$sec);
		trigger_error('overall compute time: '.($appTe-$appTs));
		//file_put_contents('/tmp/schedule.txt',print_r($ret,true));
		return $ret;
	}

	protected function _checkUnbooked(&$data,$currentDate,Array $appStack,$stmtSchedule,$minutesInterval) {
		//file_put_contents('/tmp/schedule.txt',$currentDate.' APPSTACK = '.print_r($appStack,true),FILE_APPEND);
		// plot schedule of the day
		$ranges = array();
		$currentRowSchedule = null;
		while ($rowSchedule = $stmtSchedule->fetch()) {
			$dateSchedule = substr($rowSchedule['start'],0,10);
			$data[$dateSchedule] = true; // default: has free time unless proven that all time schedules have appointments
			if ($dateSchedule == $currentDate) {
				self::fillupTimeRange($rowSchedule['start'],$rowSchedule['end'],$ranges,false,$minutesInterval);
				$currentRowSchedule = $rowSchedule;
			}
			else if ($currentRowSchedule !== null || strtotime($dateSchedule) > strtotime($currentDate)) {
				break;
			}
		}
		$hasUnbooked = null;
		if ($currentRowSchedule === null) { // no schedule event
			return $hasUnbooked;
		}

		for ($i=0,$ctr=count($appStack);$i<$ctr;$i++) {
			self::fillupTimeRange($appStack[$i]['start'],$appStack[$i]['end'],$ranges,true,$minutesInterval);
		}
		//file_put_contents('/tmp/schedule.txt',$currentDate.' = '.print_r($ranges,true),FILE_APPEND);

		$hasUnbooked = false;
		foreach ($ranges as $value) {
			if (!$value) {
				$hasUnbooked = true;
				break;
			}
		}
		return $hasUnbooked;
	}

	public static function fillupTimeRange($start,$end,&$ranges,$value,$minutesInterval=1) {
		$startToTime = strtotime($start);
		$endToTime = strtotime($end);
		// we need to deduct a minute on end time
		$endToTime = strtotime('-1 minute',$endToTime);
		//trigger_error(date('Y-m-d H:i:s',$endToTime));
		$incTime = $startToTime;
		do {
			$min = date('H:i',$incTime);
			$ranges[$min] = $value;
			$incTime = strtotime('+'.$minutesInterval.' minutes',$incTime);
		} while ($incTime <= $endToTime);
	}

	public function getIter() {
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
				->where('providerId = ?',$providerId)
				->where('roomId = ?',$roomId)
				->where('start >= ?', $dateStart)
				->where('end <= ?',$dateEnd)
				->where('start <= end')
				->order('start ASC');
		return new ScheduleEventIterator($sqlSelect);
	}

}
