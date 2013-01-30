<?php
/*****************************************************************************
*       NSDRTest.php
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
 * Unit test for NSDR Definition Model
 */

require_once dirname(dirname(__FILE__)).'/TestHelper.php';

/**
 * TestCase
 */
require_once 'TestCase.php';

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


class Models_NSDRTest extends TestCase {
	protected $_guids = array();

	public function setUp() {
		$this->_guids = NSDR::generateTestData();
		Enumeration::generateTestData(true);
		NSDR::systemUnload();
		NSDR::systemStart();
	}

	public function testPopulateValid() {
		$request = array("8384448::com.clearhealth.person[aggregateDisplay()]","83844423::com.clearhealth.person[aggregateDisplay()]");
		$result = NSDR::populate($request);
		$this->assertTrue(array_key_exists('8384448::com.clearhealth.person[aggregateDisplay()]',$result));
		$this->assertTrue(array_key_exists('83844423::com.clearhealth.person[aggregateDisplay()]',$result));
		$this->assertContains('aggregateDisplay',$result);
	}

	public function testPopulateInvalid() {
		$request = array("8384448::com.clearhealth.person[aggregateDisplay()]","83844423::com.clearhealth.person[aggregateDisplay()]");
		$result = NSDR::populate($request);
		$this->assertFalse(array_key_exists('884448::com.clearhealth.person[aggregateDisplay()]',$result));
		$this->assertFalse(array_key_exists('8844423::com.clearhealth.person[aggregateDisplay()]',$result));
		$this->assertNotContains('populate',$result);
	}

	public function testPersistValid() {
		$request = array("8384448::com.clearhealth.person"=>array('firstName'=>'John','lastName'=>'Doe'),"83844423::com.clearhealth.person"=>array('firstName'=>'Paul','lastName'=>'Smith'));
		$result = NSDR::persist($request);
		$this->assertContains('persisted data',$result);
	}

	public function testPersistInvalid() {
		$request = array("8384448::com.clearhealth.person"=>array('firstName'=>'John','lastName'=>'Doe'),"83844423::com.clearhealth.person"=>array('firstName'=>'Paul','lastName'=>'Smith'));
		$result = NSDR::persist($request);
		$this->assertNotContains('Paul',$result);
	}

	public function testSystemStartValid() {
		$memcache = Zend_Registry::get('memcache');
		$memcache->flush();
		NSDR::systemStart();
		$this->assertEquals($memcache->get('com.clearhealth.person[aggregateDisplay()]'),'return "aggregateDisplay";');
		$this->assertEquals($memcache->get('com.clearhealth.person[populate()]'),'return "populated data";');
		$this->assertEquals($memcache->get('com.clearhealth.person[persist()]'),'return "persisted data";');
	}

	public function testSystemStartInvalid() {
		$memcache = Zend_Registry::get('memcache');
		$memcache->flush();
		NSDR::systemStart();
		$this->assertNotEquals($memcache->get('com.clearhealth.person[aggregateDisplay()]'),'return "populated data";');
		$this->assertNotEquals($memcache->get('com.clearhealth.person[populate()]'),'return "aggregateDisplay";');
		$this->assertNotEquals($memcache->get('com.clearhealth.person[persist()]'),'return "persist data";');
	}

	public function testEnumerations() {
		$memcache = Zend_Registry::get('memcache');
		$memcache->flush();
		NSDR::systemStart();
		$namespace = "*::com.clearhealth.enumerations.gender";
		$nsdr = NSDR::populate($namespace);
		//$this->assertEquals(count($nsdr[$namespace]),3);

                $namespace = "F::com.clearhealth.enumerations.gender";
                $nsdr = NSDR::populate($namespace);
		//$this->assertEquals(isset($nsdr[$namespace]['key']),true);
		//$this->assertEquals($nsdr[$namespace]['key'],'F');
	}
}
