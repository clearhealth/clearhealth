<?php
/*****************************************************************************
*       Messaging.php
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


class Messaging extends WebVista_Model_ORM {

	protected $messagingId;
	protected $objectId;
	protected $objectClass;
	protected $object;
	protected $objectType;
	protected $status;
	protected $dateStatus;
	protected $note;
	protected $rawMessage;
	protected $messageType;
	protected $relatesToMessagingId;
	protected $personId;
	protected $person;
	protected $providerId;
	protected $provider;
	protected $unresolved; // 0 = resolved , 1 = unresolved
	protected $rawMessageResponse;

	protected $_table = 'messaging';
	protected $_primaryKeys = array('messagingId');
	protected $_cascadePopulate = false;
	protected $_cascadePersist = false;

	const AUTO_ATTACH = 'messagingAutoAttach';
	const DEFAULT_CLINICAL_NOTE = 'messagingDefaultClinicalNote';
	const TYPE_EPRESCRIBE = 1;
	const TYPE_INBOUND_FAX = 2;
	const TYPE_OUTBOUND_FAX = 3;

	public function __construct($objectType = self::TYPE_EPRESCRIBE) {
		parent::__construct();
		$this->person = new Person();
		$this->person->_cascadePersist = false;
		$this->provider = new Provider();
		$this->provider->_cascadePersist = false;
		$this->setObjectType($objectType);
	}

	public function persist() {
		$db = Zend_Registry::get('dbAdapter');
		if (!strlen($this->messagingId) > 0) {
			$this->messagingId = WebVista_Model_ORM::nextSequenceId();
		}
		$db->delete('messaging','messagingId='.$db->quote($this->messagingId));
		if ($this->_persistMode != WebVista_Model_ORM::DELETE) {
			$data = $this->toArray();
			foreach ($data as $key=>$value) {
				if (!is_array($value) && strlen($value) > 0) continue;
				unset($data[$key]);
			}
			$db->insert('messaging',$data);
		}
		if ($this->object !== null) {
			$this->object->persist();
		}
		return $this;
	}

	public function setPersonId($value) {
		$this->personId = (int)$value;
		$this->person->personId = $this->personId;
	}

	public function setProviderId($value) {
		$this->providerId = (int)$value;
		$this->provider->personId = $this->providerId;
	}

	public function populate() {
		$parent = parent::populate();
		$person = $this->person->populate();
		$provider = $this->provider->populate();
		$this->object->messagingId = $this->messagingId;
		$object = $this->object->populate();
		return ($parent || $person || $provider || $object);
	}

	public function setObjectType($objectType = null) {
		$this->objectType = $objectType;
		switch ($this->objectType) {
			case self::TYPE_INBOUND_FAX:
				$this->object = new MessagingInboundFax();
				break;
			case self::TYPE_OUTBOUND_FAX:
				$this->object = new MessagingOutboundFax();
				break;
			case self::TYPE_EPRESCRIBE:
			default:
				$this->object = new MessagingEPrescribe();
				break;
		}
	}

	public function __get($key) {
		if (in_array($key,$this->ORMFields())) {
			return $this->$key;
		}
		elseif (in_array($key,$this->object->ORMFields())) {
			return $this->object->__get($key);
		}
		elseif (!is_null(parent::__get($key))) {
			return parent::__get($key);
		}
		elseif (!is_null($this->object->__get($key))) {
			return $this->object->__get($key);
		}
		return parent::__get($key);
	}

	public function __set($key,$value) {
		if (in_array($key,$this->ORMFields())) {
			parent::__set($key,$value); // use this to have a chance to call those methods start with set
		}
		elseif (in_array($key,$this->object->ORMFields())) {
			$this->object->__set($key,$value);
		}
		else {
			parent::__set($key,$value);
		}
		return $this;
	}

	public function setMessagingId($id) {
		$messagingId = preg_replace('/[^0-9a-z_A-Z-\.]/','',$id);
		$this->messagingId = $messagingId;
		$this->object->messagingId = $this->messagingId;
	}

	public function getDisplayType() {
		$type = 'EPrescribe';
		switch ($this->objectType) {
			case self::TYPE_INBOUND_FAX:
				$type = 'Inbound Fax';
				break;
			case self::TYPE_OUTBOUND_FAX:
				$type = 'Outbound Fax';
				break;
		}
		return $type;
	}

	public static function settingsGetAutoAttach() {
		$config = self::_getConfig(self::AUTO_ATTACH);
		return (int)$config->value;
	}

	public static function settingsSetAutoAttach($value) {
		$config = self::_getConfig(self::AUTO_ATTACH);
		$config->value = $value;
		$config->persist();
	}

	public static function settingsGetDefaultClinicalNote() {
		$config = self::_getConfig(self::DEFAULT_CLINICAL_NOTE);
		return (int)$config->value;
	}

	public static function settingsSetDefaultClinicalNote($value) {
		$config = self::_getConfig(self::DEFAULT_CLINICAL_NOTE);
		$config->value = $value;
		$config->persist();
	}

	protected static function _getConfig($configId) {
		$configItem = new ConfigItem();
		$configItem->configId = $configId;
		$configItem->populate();
		return $configItem;
	}

	public static function convertXMLMessage(SimpleXMLElement $xml,&$output,$level=-1,$pad=' ') {
		static $transformKeys = array(
			'NCPDPID'=>'NCPDPID',
			'FileID'=>'File ID',
			'SPI'=>'SPI',
		);
		$level++;
		foreach ($xml as $key=>$value) {
			$key = (string)$key;
			if (isset($transformKeys[$key])) {
				$key = $transformKeys[$key];
			}
			else {
				$key = preg_replace('/([A-Z])(?![A-Z])/',' $1',$key);
				$key = trim(ucwords($key));
			}
			$children = $value->children();
			if ($children) {
				if ($level > 0) {
					$output[] = str_repeat($pad,($level-2)+1).' '.$key;
				}
				self::convertXMLMessage($children,$output,$level,$pad);
			}
			else {
				$value = (string)$value;
				$output[] = str_repeat($pad,($level-2)+1).$key.': '.$value;
			}
		}
		return $output;
	}

}
