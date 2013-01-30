<?php
/*****************************************************************************
*       TableModels.php
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
 * Unit test for User Key Model
 */

require_once dirname(dirname(__FILE__)).'/TestHelper.php';

/**
 * TestCase
 */
require_once 'TestCase.php';

class Models_TableModels extends TestCase {

	protected $_obj = null;
	protected $_keyValues = array();
	protected $_className = null;
	protected $_assertMatches = array();
	protected $_assertTableName = null;

	public function setUp() {
		parent::setUp();
		$this->_initPersist();
	}

	public function tearDown() {
		parent::tearDown();
		if ($this->_obj === null || !$this->_obj instanceof ORM) {
			return;
		}
		$this->_obj->setPersistMode(WebVista_Model_ORM::DELETE);
		$this->_obj->persist();
	}

	protected function _initPersist() {
		$className = $this->_className;
		if ($this->_className === null) {
			$className = get_class($this);
			if (preg_match('/^Models_(.*)Test$/i',$className,$matches)) {
				$className = $matches[1];
			}
			if (!class_exists($className)) {
				return;
			}
		}

		$obj = new $className();
		$obj->populateWithArray($this->_keyValues);
		$this->_obj = $obj;
		$this->_prePersist();
		$obj->persist();
		$this->_postPersist();
		$this->_className = $className;
	}

	protected function _prePersist() {
	}

	protected function _postPersist() {
	}

	public function testTableNameAccess() {
		$this->assertEquals($this->_obj->_table,$this->_assertTableName,'Failed to access table name');
	}

	public function testPersist() {
		if ($this->_obj === null) {
			return;
		}
		foreach ($this->_obj->_primaryKeys as $key) {
			$this->assertTrue((($this->_obj->$key > 0 || (!is_numeric($this->_obj->$key) && strlen($this->_obj->$key) > 0))),'Failed to persist');
		}
	}

	public function testPopulate() {
		if ($this->_obj === null) {
			return;
		}
		$className = $this->_className;
		$prevObj = $this->_obj;
		$this->_obj = new $className();
		foreach ($prevObj->_primaryKeys as $key) {
			$this->_obj->$key = $prevObj->$key;
		}
		$this->_prePopulate();
		$this->_obj->populate();
		$this->_postPopulate();
		foreach ($this->_assertMatches as $key=>$value) {
			$this->assertEquals($this->_obj->$key,$value,'Failed to populate');
		}
		$this->_obj = $prevObj;
	}

	protected function _prePopulate() {
	}

	protected function _postPopulate() {
	}

}

