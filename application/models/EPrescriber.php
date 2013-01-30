<?php
/*****************************************************************************
*       EPrescriber.php
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


class EPrescriber extends WebVista_Model_ORM {

	protected $ePrescriberId;
	protected $buildingId;
	protected $building;
	protected $providerId;
	protected $provider;
	protected $SSID;
	protected $dateActiveStart;
	protected $dateActiveEnd;
	protected $serviceLevel;

	protected $_table = 'ePrescribers';
	protected $_primaryKeys = array('ePrescriberId');
	protected $_cascadePersist = false;

	public function __construct() {
		parent::__construct();
		$this->building = new Building();
		$this->building->_cascadePersist = false;
		$this->provider = new Provider();
		$this->provider->_cascadePersist = false;
	}

	public function populateBySPI($spi=null) {
		if ($spi === null) $spi = $this->SSID;
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('SSID = ?',(string)$spi)
				->limit(1);
		$this->populateWithSql($sqlSelect->__toString());
	}

	public function setBuildingId($buildingId) {
		$this->buildingId = (int)$buildingId;
		$this->building->buildingId = $this->buildingId;
	}

	public function setProviderId($provider) {
		$this->providerId = (int)$provider;
		$this->provider->personId = $this->providerId;
	}

	public function populateWithBuildingProvider($buildingId=null,$providerId=null) {
		if ($buildingId === null) {
			$buildingId = $this->buildingId;
		}
		if ($providerId === null) {
			$providerId = $this->providerId;
		}
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('buildingId = ?',(int)$buildingId)
				->where('providerId = ?',(int)$providerId);
		$this->populateWithSql($sqlSelect->__toString());
	}

	public function getIteratorByProviderId($providerId=null) {
		if ($providerId === null) {
			$providerId = $this->providerId;
		}
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('providerId = ?',(int)$providerId);
		return $this->getIterator($sqlSelect);
	}

	public function getIteratorByBuildingId($buildingId=null) {
		if ($buildingId === null) {
			$buildingId = $this->buildingId;
		}
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->where('buildingId = ?',(int)$buildingId);
		return $this->getIterator($sqlSelect);
	}

	public function ssCheck() {
		$ret = true;
		return $ret;
	}

}
