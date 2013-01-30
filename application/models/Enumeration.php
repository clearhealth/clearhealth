<?php
/*****************************************************************************
*       Enumeration.php
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


class Enumeration extends WebVista_Model_ORM implements NSDRMethods {

	protected $enumerationId;
	protected $guid;
	protected $name;
	protected $key;
	protected $active;
	protected $category;
	protected $ormClass;
	protected $ormId;
	protected $ormEditMethod;

	protected $_table = "enumerations";
	protected $_primaryKeys = array('enumerationId');

	protected $_context = '*';
	protected $_alias = null;

	public function __construct($context='*',$alias=null) {
		$this->_context = $context;
		$this->_alias = $alias;
		parent::__construct();
	}

	public function populateByGuid($guid = null) {
		if ($guid === null) {
			$guid = $this->guid;
		}
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			       ->from($this->_table)
			       ->where('guid = ?',$guid);
		$retval = $this->populateWithSql($dbSelect->__toString());
		$this->postPopulate();
		return $retval;
	}

	public function populateByUniqueName($name) {
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			       ->from(array('e'=>$this->_table))
			       ->join(array('ec'=>'enumerationsClosure'),'e.enumerationId = ec.ancestor')
			       ->where('name = ?',$name)
			       ->where('enumerationId = descendant')
			       ->where('depth = 0');
		$retval = $this->populateWithSql($dbSelect->__toString());
		$this->postPopulate();
		return $retval;
	}

	public function nsdrPersist($tthis,$context,$data) {
	}

	public function nsdrPopulate($tthis,$context,$data) {
		$ret = '';
		if (preg_match('/^com\.clearhealth\.enumerations\.(.*)$/',$this->_alias,$matches)) {
			$name = $matches[1];
			$enumeration = new self();
			$enumeration->populateByFilter('key',$name);
			$enumerationsClosure = new EnumerationsClosure();
			$enumerationIterator = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
			$data = array();
			foreach ($enumerationIterator as $enum) {
				if ($this->_context != '*' && $this->_context == $enum->key) {
					$data = $enum->toArray();
					break;
				}
				$data[] = $enum->toArray();
			}
			$ret = $data;
		}
		return $ret;
	}

	public function nsdrMostRecent($tthis,$context,$data) {
	}

	static public function getIterByEnumerationId($enumerationId) {
		$enumerationId = (int) $enumerationId;
		$enumeration = new self();
		$db = Zend_Registry::get('dbAdapter');
		$enumSelect = $db->select()
			->from($enumeration->_table)
			->where('parentId = ' . $enumerationId);
		$iter = $enumeration->getIterator($enumSelect);
		return $iter;
		
	}

	static public function getIterByEnumerationName($name) {
                $enumeration = new self();
                $db = Zend_Registry::get('dbAdapter');
                $enumSelect = $db->select()
                        ->from($enumeration->_table)
                        ->where('parentId = (select enumerationId from enumerations where name=' . $db->quote($name) .')');
                $iter = $enumeration->getIterator($enumSelect);
                return $iter;

        }
	static public function getEnumArray($name,$key='key',$value='name') {
                //$iter = self::getIterByEnumerationName($name);
                //return $iter->toArray($key, $value);

		$enumeration = new self();
		$enumeration->populateByEnumerationName($name);
		$enumerationsClosure = new EnumerationsClosure();
		$enumerationIterator = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
		$ret = array();
		foreach ($enumerationIterator as $enumeration) {
			$ret[$enumeration->$key] = $enumeration->$value;
		}
		return $ret;
        }

	/**
	 * Get Enumeration Iterator by distinct category
	 *
	 * @return WebVista_Model_ORMIterator
	 * @access public static
	 */
	public static function getIterByDistinctCategories() {
		$enumeration = new self();
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			       ->from($enumeration->_table)
			       ->where("category != ''")
			       ->group("category");
		return $enumeration->getIterator($dbSelect);
	}

	/**
	 * Get Enumeration Iterator by distinct name given an enumerationId of category
	 *
	 * @param int $enumerationId Enumeration ID of category
	 * @return WebVista_Model_ORMIterator
	 * @access public static
	 */
	public static function getIterByDistinctNames($enumerationId) {
		$enumeration = new self();
		$db = Zend_Registry::get('dbAdapter');
		$innerDbSelect = $db->select()->from($enumeration->_table,"category")
				    ->where('enumerationId = ?',(int)$enumerationId);
		$dbSelect = $db->select()->from(array('e1'=>$enumeration->_table))
			       ->joinLeft(array('e2'=>$enumeration->_table),'e2.category = e1.category',array())
			       ->where("e2.category = ({$innerDbSelect})")
			       ->group('e1.name');
		//trigger_error($dbSelect,E_USER_NOTICE);
		return $enumeration->getIterator($dbSelect);
	}


	/**
	 * Get Enumeration Iterator by name given an enumeration's name
	 *
	 * @param string $name Enumeration's name
	 * @return WebVista_Model_ORMIterator
	 * @access public static
	 */
	public static function getIterByName($name) {
		$name = preg_replace('/[^a-z_0-9-]/i','',$name);
		$enumeration = new self();
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()->from($enumeration->_table)
			       ->where('name = ?',$name);
		//trigger_error($dbSelect,E_USER_NOTICE);
		return $enumeration->getIterator($dbSelect);
	}

	public function populateByEnumerationName($name) {
		return $this->populateByFilter('name',$name);
	}

	public function populateByFilter($key,$val) {
		$db = Zend_Registry::get('dbAdapter');
		$dbSelect = $db->select()
			       ->from($this->_table)
			       ->where("`{$key}` = ?",$val);
		$retval = $this->populateWithSql($dbSelect->__toString());
		$this->postPopulate();
		return $retval;
	}

	public static function generateTestData($force = false) {
		EnumGenerator::generateTestData($force);
	}

	public static function enumerationToJson($name) {
		$enumeration = new self();
		$enumeration->populateByEnumerationName($name);
		$enumerationsClosure = new EnumerationsClosure();
		$enumerationIterator = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
		return $enumerationIterator->toJsonArray('enumerationId',array('name'));
	}

	public function ormEditMethod($ormId,$isAdd) {
		$controller = Zend_Controller_Front::getInstance();
		$request = $controller->getRequest();
		$enumerationId = (int)$request->getParam("enumerationId");

		$view = Zend_Layout::getMvcInstance()->getView();
		$params = array();
		if ($isAdd) {
			$params['parentId'] = $enumerationId;
			unset($_GET['enumerationId']); // remove enumerationId from params list
			$params['grid'] = 'enumItemsGrid';
		}
		else {
			$closure = new EnumerationClosure();
			$params['parentId'] = $closure->getParentById($enumerationId);
			$params['enumerationId'] = $enumerationId;
			$params['grid'] = 'enumItemsGrid';
		}
		return $view->action('edit','enumerations-manager',null,$params);
	}

}
