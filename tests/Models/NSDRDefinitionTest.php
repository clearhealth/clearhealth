<?php
/*****************************************************************************
*       NSDRDefinitionTest.php
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
 * Models_TableModels
 */
require_once 'TableModels.php';

/**
 * NSDR
 */
require_once 'NSDR.php';

/**
 * NSDRBase
 */
require_once 'NSDRBase.php';

/**
 * NSDRDefinition
 */
require_once 'NSDRDefinition.php';

/**
 * NSDRDefinitionIterator
 */
require_once 'NSDRDefinitionIterator.php';


class Models_NSDRDefinitionTest extends Models_TableModels {

	protected $_keyValues = array('uuid'=>'u-u-i-d',
				      'namespace'=>'com.clearhealth.namespace.test',
				      'aliasFor'=>'com.clearhealth.namespace.test.aliasfor',
				      'ORMClass'=>'ORMClass',);
	protected $_assertMatches = array('namespace'=>'com.clearhealth.namespace.test');
	protected $_assertTableName = 'nsdrDefinitions'; // value MUST be the same as $_table

	protected $_guids = array();

	public function setUp() {
		parent::setUp();
		$this->_guids = NSDR::generateTestData();
	}

	public function tearDown() {
		parent::tearDown();
		//$this->_nsdrDefinition->truncate();
	}

	public function testPersist() {
		// nothing to test just override the parent::testPersist()
	}

	public function testPopulate() {
		$this->_cleanUpObjects($this->_objects);
		$this->_initPersist();
		parent::testPopulate();
	}

	public function testGeneratedData() {
		$this->assertGreaterThan(0,count($this->_guids));
	}

	public function testPopulateValid() {
		$uuid = rand(0,count($this->_guids)-1);
		$nsdrDefinition = new NSDRDefinition();
		$nsdrDefinition->uuid = $this->_guids[$uuid];
		$nsdrDefinition->populate();
		$namespace = $nsdrDefinition->namespace;
		$this->assertGreaterThan(0,strlen($namespace));
	}

	public function testPopulateInvalid() {
		$uuid = rand(0,count($this->_guids)-1);
		$nsdrDefinition = new NSDRDefinition();
		$nsdrDefinition->uuid = $this->_guids[$uuid] . 'invalid';
		$nsdrDefinition->populate();
		$namespace = $nsdrDefinition->namespace;
		$this->assertEquals(0,strlen($namespace));
	}

	public function testDelete() {
		$uuid = rand(0,count($this->_guids)-1);
		$nsdrDefinition = new NSDRDefinition();
		$nsdrDefinition->uuid = $this->_guids[$uuid];
		$nsdrDefinition->setPersistMode(WebVista_Model_ORM::DELETE);
		$nsdrDefinition->persist();
		$nsdrDefinition->populate();
		$namespace = $nsdrDefinition->namespace;
		$this->assertEquals(0,strlen($namespace));
	}

	public function testPersistMethodsValid() {
		$uuid = rand(0,count($this->_guids)-1);
		$nsdrDefinition = new NSDRDefinition();
		$nsdrDefinition->uuid = $this->_guids[$uuid];
		$methods = array(array('methodName'=>'populate','method'=>'return "populate";'));
		$nsdrDefinition->persistMethods($methods);
		$nsdrDefinition->populate();
		$this->assertGreaterThan(0,count($nsdrDefinition->methods));
	}

	public function testEditDefinition() {
		$uuid = rand(0,count($this->_guids)-1);
		$nsdrParams = array('uuid'=>$uuid,'namespace'=>'com.clearhealth.person.age','aliasFor'=>'com.clearhealth.person.dateOfBirth');
		$methodParams = array('aggregateDisplay'=>'return "aggregate display";','populate'=>'return "populate";','persist'=>'');
		$objNSDR = new NSDRDefinition();
		$methods = array();

		// cannot add method if alias exists (alias must be canonical)
		if (strlen($nsdrParams['aliasFor']) == 0) {
			foreach ($methodParams as $index=>$value) {
				$tmpArray = array();
				$tmpArray['uuid'] = $uuid;
				$tmpArray['methodName'] = $index;
				$tmpArray['method'] = $value;
				$methods[] = $tmpArray;
			}
		}

		// persist methods
		$objNSDR->persistMethods($methods);

		$objNSDR->populateWithArray($nsdrParams);

		// workaround for Unknown column 'methodName'/'method' in 'field list'
		$objNSDR->methodName = array();
		$objNSDR->method = array();
		$objNSDR->persist();

		$nsdrDefinition = new NSDRDefinition();
		$nsdrDefinition->uuid = $uuid;
		$nsdrDefinition->populate();
		$this->assertTrue(strlen($nsdrDefinition->namespace) > 0);
	}

	public function testAddDefinition() {
		$uuid = NSDR::create_guid();
		$nsdrParams = array('uuid'=>$uuid,'namespace'=>'com.clearhealth.person.age','aliasFor'=>'com.clearhealth.person.dateOfBirth');
		$objNSDR = new NSDRDefinition();
		$objNSDR->populateWithArray($nsdrParams);
		// workaround for Unknown column 'methodName'/'method' in 'field list'
		$objNSDR->methodName = array();
		$objNSDR->method = array();
		$objNSDR->persist();

		$nsdrDefinition = new NSDRDefinition();
		$nsdrDefinition->uuid = $uuid;
		$nsdrDefinition->populate();
		$this->assertEquals('com.clearhealth.person.age',$nsdrDefinition->namespace);
	}
}
