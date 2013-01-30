<?php
/*****************************************************************************
*       Order.php
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


class Order extends WebVista_Model_ORM implements Document {

	protected $orderId;
	protected $providerId;
	protected $provider;
	protected $patientId;
	protected $patient;
	protected $dateTime;
	protected $dateStart;
	protected $dateStop;
	protected $orderText;
	protected $status;
	protected $service;
	protected $eSignatureId;
	protected $dateDiscontinued;
	protected $hold;
	protected $release;
	protected $type;
	protected $textOnlyType;
	protected $_table = 'orders';
	protected $_primaryKeys = array('orderId');
	protected $_cascadePersist = false;

	const TYPE_LAB_TEST = 1;
	const TYPE_IMAGING = 2;
	const TEXT_ONLY_TYPE_ENUM_NAME = 'Text Only Types';
	const TEXT_ONLY_TYPE_ENUM_KEY = 'Text Only Types';

	function __construct() {
		$this->provider = new Provider();
		$this->patient = new Patient();
                parent::__construct();
        }

	public function getDisplayOrder() {
		$labels = array(
			'patient'=>__('Patient'),
			'provider'=>__('Provider'),
			'date'=>__('Date'),
			'dateStart'=>__('Date Ordered'),
			'dateStop'=>__('Date Completed'),
			'service'=>__('Service'),
			'orderText'=>__('Order Text'),
			'status'=>__('Status'),
		);
		$padLength = 0;
		foreach ($labels as $key=>$label) {
			$label .= ':';
			$labels[$key] = $label;
			$labelLen = strlen($label);
			if ($labelLen > $padLength) {
				$padLength = $labelLen;
			}
		}
		$padLength += 2;
		$content = str_pad($labels['patient'],$padLength).$this->patient->displayName;
		$content .= PHP_EOL.str_pad($labels['provider'],$padLength).$this->provider->displayName;
		$content .= PHP_EOL.str_pad($labels['date'],$padLength).$this->dateTime;
		$content .= PHP_EOL.str_pad($labels['dateStart'],$padLength).$this->dateStart;
		$content .= PHP_EOL.str_pad($labels['dateStop'],$padLength).$this->dateStop;
		$content .= PHP_EOL.str_pad($labels['service'],$padLength).$this->service;
		$content .= PHP_EOL.str_pad($labels['orderText'],$padLength).$this->orderText;
		$content .= PHP_EOL.str_pad($labels['status'],$padLength).$this->displayStatus;
		return $content;
	}

	public function getDisplayStatus() {
		$status = $this->status;
		if ($this->dateDiscontinued != '0000-00-00 00:00:00') {
			$status = __('DISCONTINUED');
		}
		else {
			if ($this->release) {
				$status = __('RELEASED');
			}
			else if (!$this->eSignatureId > 0) {
				$status = __('UNSIGNED');
			}
			else if ($this->hold) {
				$status = __('HOLD');
			}
			else {
				$status = __('ACTIVE');
			}
		}
		return $status;
	}

	function __get($key) {
		if (in_array($key,$this->ORMFields())) {
			return $this->$key;
		}
		elseif (in_array($key,$this->provider->ORMFields())) {
			return $this->provider->__get($key);
		}
		elseif (!is_null(parent::__get($key))) {
			return parent::__get($key);
		}
		elseif (!is_null($this->provider->__get($key))) {
			return $this->provider->__get($key);
		}
		return parent::__get($key);
	}

	public static function factory($orderId) {
		$orm = new self();
		$orm->orderId = (int)$orderId;
		$orm->populate();
		if ($orm->type == self::TYPE_LAB_TEST) {
			$ormObj = new OrderLabTest();
			$ormObj->orderId = $orm->orderId;
			$ormObj->populate();
		}
		else if ($orm->type == self::TYPE_IMAGING) {
			$ormObj = new OrderImaging();
			$ormObj->orderId = $orm->orderId;
			$ormObj->populate();
		}
		else {
			$ormObj = $orm;
		}
		return $ormObj;
	}

	public function setPatientId($id) {
		$this->patientId = (int)$id;
		$this->patient->personId = $this->patientId;
	}

	public function setProviderId($id) {
		$this->providerId = (int)$id;
		$this->provider->personId = $this->providerId;
	}

	public function getContent() {
		return '';
	}

	public function getSummary() {
		return $this->orderText;
	}

	public function getDocumentId() {
		return $this->orderId;
	}

	public function setDocumentId($id) {
		$this->orderId = (int)$id;
	}

	public function setSigned($eSignatureId) {
		$this->eSignatureId = $eSignatureId;
		$this->persist();
	}

	static public function getPrettyName() {
		return 'Orders';
	}

	public static function getControllerName() {
		return 'OrdersController';
	}

}
