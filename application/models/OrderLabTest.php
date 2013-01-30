<?php
/*****************************************************************************
*       OrderLabTest.php
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


class OrderLabTest extends WebVista_Model_ORM {

	protected $orderId;
	protected $order;
	protected $labTest;
	protected $collectionSample;
	protected $specimen;
	protected $urgency;
	protected $comments;
	protected $collectionType;
	protected $dateCollection;
	protected $schedule;
	protected $daysSupply;
	protected $_table = 'orderLabTests';
	protected $_primaryKeys = array('orderId');

	protected $_labTests = null;
	protected $_types = null;
	protected $_collectionSamples = null;
	protected $_specimens = null;
	protected $_urgencies = null;
	protected $_collectionTypes = null;
	protected $_schedules = null;

	const LAB_ENUM_NAME = 'LabTest Preferences';
	const LAB_ENUM_KEY = 'LABTEST';
	const LAB_TYPES_ENUM_NAME = 'Lab Types';
	const LAB_TYPES_ENUM_KEY = 'TYPES';
	const LAB_COLLECTION_SAMPLES_ENUM_NAME = 'Collection Samples';
	const LAB_COLLECTION_SAMPLES_ENUM_KEY = 'COLSAMPLES';
	const LAB_SPECIMENS_ENUM_NAME = 'Specimens';
	const LAB_SPECIMENS_ENUM_KEY = 'SPECIMENS';
	const LAB_URGENCIES_ENUM_NAME = 'Urgencies';
	const LAB_URGENCIES_ENUM_KEY = 'URGENCIES';
	const LAB_COLLECTION_TYPES_ENUM_NAME = 'Collection Types';
	const LAB_COLLECTION_TYPES_ENUM_KEY = 'COLTYPES';
	const LAB_SCHEDULES_ENUM_NAME = 'Schedules';
	const LAB_SCHEDULES_ENUM_KEY = 'SCHEDULES';

	public function __construct() {
		$this->order = new Order();
		$this->order->type = Order::TYPE_LAB_TEST;
	}

	public function getDisplayOrder() {
		$content = $this->order->displayOrder;
		$content .= PHP_EOL;
		$labels = array(
			'labTest'=>__('Lab Test'),
			'collectionSample'=>__('Collection Sample'),
			'specimen'=>__('Specimen'),
			'urgency'=>__('Urgency'),
			'collectionType'=>__('Collection Type'),
			'dateCollection'=>__('Date Collection'),
			/*'schedule'=>__('Often'),
			'daysSupply'=>__('Days Supply'),*/
			'comments'=>__('Comments'),
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
		$content .= PHP_EOL.str_pad($labels['labTest'],$padLength).$this->getDisplayLabTest();
		$content .= PHP_EOL.str_pad($labels['collectionSample'],$padLength).$this->getDisplayCollectionSample();
		$content .= PHP_EOL.str_pad($labels['specimen'],$padLength).$this->specimen;
		$content .= PHP_EOL.str_pad($labels['urgency'],$padLength).$this->urgency;
		$content .= PHP_EOL.str_pad($labels['collectionType'],$padLength).$this->collectionType;
		$content .= PHP_EOL.str_pad($labels['dateCollection'],$padLength).$this->dateCollection;
		//$content .= PHP_EOL.str_pad($labels['schedule'],$padLength).$this->schedule;
		//$content .= PHP_EOL.str_pad($labels['daysSupply'],$padLength).$this->daysSupply;
		$content .= PHP_EOL.str_pad($labels['comments'],$padLength).$this->comments;
		return $content;
	}

	public function setOrderId($id) {
		$this->order->orderId = (int)$id;
		$this->orderId = $this->order->orderId;
	}

	public function __get($key) {
		if (in_array($key,$this->ORMFields())) {
			return $this->$key;
		}
		elseif (in_array($key,$this->order->ORMFields())) {
			return $this->order->__get($key);
		}
		elseif (!is_null(parent::__get($key))) {
			return parent::__get($key);
		}
		elseif (!is_null($this->order->__get($key))) {
			return $this->order->__get($key);
		}
		return parent::__get($key);
	}

	public function getDisplayLabTest() {
		$loinc = new ProcedureCodesLOINC();
		$loinc->loincNum = $this->labTest;
		$loinc->populate();
		$ret = $loinc->shortname;
		if (!strlen($ret) > 0) {
			$ret = $loinc->longCommonName;
		}
		return $ret;
	}

	public function getDisplayCollectionSample() {
		$collectionSample = $this->collectionSample;
		$sampleTypesTable = ProcedureCodesLOINC::sampleTypesTable();
		if (isset($sampleTypesTable[$collectionSample])) {
			$collectionSample = $sampleTypesTable[$collectionSample];
		}
		return $collectionSample;
	}

	protected function _populateLabTests() {
		if ($this->_labTests !== null) return;
		$enumeration = new Enumeration();
		$enumeration->populateByUniqueName(self::LAB_ENUM_NAME);
		$enumerationClosure = new EnumerationClosure();
		$labEnums = $enumerationClosure->getAllDescendants($enumeration->enumerationId,1);
		$this->_labTests = array();
		foreach ($labEnums as $labEnum) {
			$this->_labTests[$labEnum->key] = $labEnum;
		}
	}

	protected function _populateTypes() {
		$this->_populateLabTests();
		if ($this->_types !== null) return;
		$this->_types = array();
		$enumerationClosure = new EnumerationClosure();
		foreach ($this->_labTests as $key=>$value) {
			if ($key != OrderLabTest::LAB_TYPES_ENUM_KEY) continue;
			foreach ($enumerationClosure->getAllDescendants($value->enumerationId,1) as $enum) {
				$this->_types[$enum->key] = $enum;
			}
			break;
		}
	}


	public function getIteratorByPersonId($personId) {
		$db = Zend_Registry::get('dbAdapter');
		$sqlSelect = $db->select()
				->from($this->_table)
				->joinInner('orders','orders.orderId='.$this->_table.'.orderId')
				->where('orders.patientId = ?',(int)$personId);
		return $this->getIterator($sqlSelect);
	}

}
