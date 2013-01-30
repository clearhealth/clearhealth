<?php
/*****************************************************************************
*       Schedule.php
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


class Schedule extends WebVista_Model_ORM {

	protected $schedule_id;
	protected $title;
	protected $description_long;
	protected $description_short;
	protected $schedule_code;
	protected $provider_id;
	protected $provider;
	protected $room_id;
	protected $room;
	protected $_table = 'schedule';
	protected $_primaryKeys = array('schedule_id');
	protected $_legacyORMNaming = true;
	protected $_cascadePersist = false;

	public function __construct() {
		parent::__construct();
		$this->provider = new Provider();
		$this->provider->_cascadePersist = false;
		$this->room = new Room();
		$this->room->_cascadePersist = false;
	}

	public function setProvider_id($val) {
		$this->setProviderId($val);
	}

	public function setProviderId($val) {
		$this->provider_id = (int)$val;
		$this->provider->personId = $this->provider_id;
	}

	public function setRoom_id($val) {
		$this->setRoomId($val);
	}

	public function setRoomId($val) {
		$this->room_id = (int)$val;
		$this->room->roomId = $this->room_id;
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
		elseif (!is_null(parent::__get($key))) {
			return parent::__get($key);
		}
		elseif (!is_null($this->provider->__get($key))) {
			return $this->provider->__get($key);
		}
		elseif (!is_null($this->room->__get($key))) {
			return $this->room->__get($key);
		}
		return parent::__get($key);
	}

	public function populateByProviderRoomId() {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('provider_id = ?',(int)$this->providerId)
				->where('room_id = ?',(int)$this->roomId)
				->limit(1);
		$ret = false;
		if ($row = $db->fetchRow($sqlSelect)) {
			$this->populateWithArray($row);
			$ret = true;
		}
		$this->provider->populate();
		$this->room->populate();
		return $ret;
	}

}
