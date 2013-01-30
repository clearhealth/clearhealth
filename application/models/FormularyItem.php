<?php
/*****************************************************************************
*       FormularyItem.php
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


/**
 * Formulary Model which acts as an ConfigItem and ORM at the same
 */
class FormularyItem extends WebVista_Model_ORM {

	// ORM properties
	protected $fullNDC;
	protected $directions;
	protected $comments;
	protected $schedule;
	protected $price;
	protected $labelId;
	protected $externalUrl;
	protected $qty;
	protected $keywords;
	protected $vaclass;
	protected $deaSchedule;
	protected $print;
	protected $description;
	protected $dose;
	protected $route;
	protected $prn;
	protected $quantityQualifier;
	protected $refills;
	protected $daysSupply;
	protected $substitution;
	protected $_table = 'formularyDefault';
	protected $_primaryKeys = array("fullNDC");

	// Formulary properties used to save to ConfigItem
	/**
	 * Used to store name of the formulary table and config ID for ConfigItem
	 * @var string
	 */
	protected $_name = null;

	/**
	 * The status of the table either active or inactive
	 * @var boolean
	 */
	protected $_isActive = true;

	/**
	 * Hold either this model is default or not default
	 * @var boolean
	 */
	protected $_isDefault = false;

	/**
	 * Hold either this default is set
	 * @var boolean
	 */
	protected $_isDefaultSet = false;

	/**
	 * Configuration object
	 * @var ConfigItem
	 */
	protected $_config;

	/**
	 * Configuration object
	 * @var string
	 */
	protected $_defaultFormularyName = null;

	/**
	 * Constant for the default formulary configId used for ConfigItem
	 * @var string
	 */
	const DEFAULTFORMULARYID = 'defaultFormulary';


	/**
	 * Class constructor
	 *
	 * @param string $name tableName/configId
	 * @return void
	 */
	public function __construct($name = null) {
		parent::__construct();
		$this->_config = new ConfigItem();
		if ($name !== null) {
			$this->setName($name);
		}
		else {
			// set current table to config table
			$this->_table = $this->_config->_table;
		}
	}

	/**
	 * Populate __sleep values, restore serialize values specified on __sleep() method
	 *
	 * @return self
	 */
	public function populateWithItself(self $formulary) {
		$this->_name = $formulary->getName();
		$this->_isActive = $formulary->isActive();
		$this->_isDefault = $formulary->isDefault();
		return $this;
	}

	/**
	 * Use when serializing, this is implemented in order to minimize the size
	 *
	 * @return array
	 */
	public function __sleep() {
		// only the necessary properties is saved
		return array('_name','_isActive','_isDefault');
	}

	/**
	 * Use when unserializing, initialize $_config
	 *
	 * @return void
	 */
	public function __wakeup() {
		$this->_config = new ConfigItem();
		if ($this->_name === null) {
			$this->_table = $this->_config->_table;
		}
	}

	/**
	 * Populate all the data and maps the result to this ORM properties
	 *
	 * @param boolean $actual TRUE when referring to actual ORM, FALSE when referring to ConfigItem (default to TRUE)
	 * @return boolean
	 */
	public function populate($actual = true) {
		if ($actual) {
			$this->_table = $this->_name;
			$db = Zend_Registry::get('dbAdapter');
			$sqlSelect = $db->select()
					->from($this->_table)
					->where('fullNDC = ?',$this->fullNDC);
			//parent::populate();
			$retval = $this->populateWithSql($sqlSelect->__toString());
			$this->postPopulate();
			return $retval;
		}
		$ret = false;
		$this->_config->configId = $this->_name;
		$this->_config->populate();

		$formularyItem = unserialize($this->_config->value);
		if ($formularyItem !== false && $formularyItem instanceof self) {
			$this->populateWithItself($formularyItem);
			$ret = true;
		}
		return $ret;
	}

	/**
	 * Populate like starts with or ends with or both
	 *
	 * @param string $configId Configuration ID
	 * @param int $mode Mode of the query or position of the wildcard character (0-LEFT, 1-RIGHT, 2-BOTH)
	 * @return WebVista_Model_ORMIterator
	 */
	public function populateLike($configId,$mode = 2) {
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()->from($this->_table);
		$value = preg_replace('/[^a-z_0-9-]/i','',$configId);
		$mode = (int) $mode;
		switch ($mode) {
			case 0:
				$value = '%'.$value;
				break;
			case 1:
				$value .= '%';
				break;
			case 2:
				$value = '%'.$value.'%';
				break;
			default:
				break;
		}
		$dbSelect->where("configId LIKE ?",$value);
		$dbSelect->order("value ASC");
		//trigger_error($dbSelect,E_USER_NOTICE);
		return $this->getIterator($dbSelect);
	}

	/**
	 * Persist all the data to the datastore
	 *
	 * @param boolean $actual TRUE when referring to actual ORM, FALSE when referring to ConfigItem (default to TRUE)
	 * @return boolean
	 */
	public function persist($actual = true) {
		if ($actual) {
			$this->_table = $this->_name;
			parent::persist();
			return true;
		}
		$defaultTableName = $this->_defaultFormularyName;
		if ($defaultTableName == null) {
			$defaultTableName = self::getDefaultFormularyTable();
		}
		$db = Zend_Registry::get('dbAdapter');
		if ($this->_persistMode == WebVista_Model_ORM::DELETE) {
			$queries = array();
			$queries[] = "DROP TABLE {$this->_name}";
			$queries[] = "DELETE FROM {$this->_config->_table} WHERE configId='{$this->_name}'";
			$db->query(implode(';'.PHP_EOL,$queries));
		}
		else {
			// create new table based on default table
			$tableName = $this->_name;
			if (!self::isTableExists($tableName)) {
				$sql = 'CREATE TABLE ' . $tableName . ' LIKE ' . $defaultTableName;
				$sql .= '; INSERT INTO ' . $tableName . ' SELECT * FROM ' . $defaultTableName;
trigger_error($sql,E_USER_NOTICE);
				$db->query($sql);
			}

			$this->_config->configId = $this->_name;
			$this->_config->value = serialize($this);
			$this->_config->setPersistMode($this->_persistMode);
			$this->_config->persist();
		}

		if ($this->_isDefaultSet) {
			// backup configId and value
			$configId = $this->_config->configId;
			$value = $this->_config->value;

			$this->_config->configId = $defaultTableName;
			$this->_config->populate();
			$formularyItem = unserialize($this->_config->value);
			if ($formularyItem !== false && $formularyItem instanceof self
			   && $formularyItem->getName() != $this->getName()) {
				// unset the default
				$formularyItem->unsetDefault();
				$formularyItem->persist(false);
			}

			// set the default formulary table name to this table name
			$this->_config->configId = self::DEFAULTFORMULARYID;
			$this->_config->value = $this->_name;
			$this->_config->persist();

			// restore the previous data for configId and value
			$this->_config->configId = $configId;
			$this->_config->value = $value;
		}
		// temporarily return true, this might change later if needed
		return true;
	}

	/**
	 * Retrieve if status is active or inactive
	 *
	 * @return boolean
	 */
	public function isActive() {
		return $this->_isActive;
	}

	/**
	 * Set the status to active
	 *
	 * @return void
	 */
	public function activate() {
		$this->_isActive = true;
	}

	/**
	 * Set the status to inactive
	 *
	 * @return void
	 */
	public function deactivate() {
		$this->_isActive = false;
	}

	/**
	 * Retrieve if this model is default or not default
	 *
	 * @return boolean
	 */
	public function isDefault() {
		return $this->_isDefault;
	}

	/**
	 * Set this model to default
	 *
	 * @return void
	 */
	public function setDefault() {
		$this->_isDefault = true;
		$this->_isDefaultSet = true;
	}

	/**
	 * Unset the default value for this model
	 *
	 * @return void
	 */
	public function unsetDefault() {
		$this->_isDefault = false;
	}

	/**
	 * Construct pretty name based on its table name without the prefix "formulary"
	 *
	 * @return string
	 */
	public function getPrettyName() {
		$name = $this->_name;
		if (isset($name[9])) {
			$name = ltrim(preg_replace('/([A-Z]{1})/',' \1',substr($name,9)));
		}
		return $name;
	}

	/**
	 * Retrieve table name or config id
	 *
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * Set the table name or config id
	 *
	 * @param string $name
	 * @return void
	 */
	public function setName($name) {
		$this->_name = preg_replace('/[^a-zA-Z]+/','',$name);
		$this->_table = $this->_name;
	}

	/**
	 * Static method to retrieve the default formulary table name or config id
	 *
	 * @return mixed FALSE if default formulary table does not exists (boolean), otherwise the default table name (string)
	 */
	public static function getDefaultFormularyTable() {
		// use a separate ConfigItem object instead of $this->_config for isolation purposes
		$config = new ConfigItem();
		$config->configId = self::DEFAULTFORMULARYID;
		$config->populate();
		$ret = $config->value;
		if (strlen($ret) <= 0) {
			$ret = false;
		}
		return $ret;
	}

	/**
	 * Retrieve if table name exists
	 *
	 * @param string $tableName Name of the table to check
	 * @return boolean
	 */
	public static function isTableExists($tableName) {
		$ret = false;
		$db = Zend_Registry::get('dbAdapter');
		$sql = "SHOW TABLES LIKE '{$tableName}'";
		$dbStmt = $db->query($sql);
		if ($dbStmt->rowCount() > 0) {
			$ret = true;
		}
		return $ret;
	}

	/**
	 * Create ConfigItem for default formulary table
	 *
	 * @return void
	 */
	public static function createDefaultConfigIfNotExists() {
		$defaultTable = self::getDefaultFormularyTable();
		if ($defaultTable !== false) {
			$formulary = new self($defaultTable);
			if (!$formulary->populate(false)) {
				$formulary->activate();
				$formulary->setDefault();
				$formulary->persist(false);
			}
		}
	}

	/**
	 * Retrieve an iterator for this model
	 *
	 * @return WebVista_Model_ORMIterator
	 */
	public function getIterator($dbSelect = null) {
		if (is_null($dbSelect)) {
			$db = Zend_Registry::get('dbAdapter');
                	$dbSelect = $db->select()
				->from(array('f'=>$this->_table))
				->join(array('hbm24'=>'chmed.basemed24'),'hbm24.full_ndc = f.fullNDC');
		}
		return new WebVista_Model_ORMIterator($this,$dbSelect);
	}

	/**
	 * Retrieve an all data for this model
	 *
	 * @return array
	 */
	public function getAllRows($dbSelect = null) {
		$db = Zend_Registry::get('dbAdapter');
		if (is_null($dbSelect)) {
                	$dbSelect = $db->select()
				->from(array('f'=>$this->_table))
				->join(array('hbm24'=>'chmed.basemed24'),'hbm24.full_ndc = f.fullNDC',array('fda_drugname','chmed_dose'=>'dose','strength','rxnorm','tradename'));
		}
		return $db->query($dbSelect)->fetchAll();
	}

	public function getFormularyItemId() {
		return $this->fullNDC;
	}

}

