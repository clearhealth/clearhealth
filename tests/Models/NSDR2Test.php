<?php
/*****************************************************************************
*       NSDR2Test.php
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
 * NSDR2
 */
require_once 'NSDR2.php';

/**
 * NSDRBase
 */
require_once 'NSDRBase.php';

/**
 * NSDRDefinition
 */
require_once 'NSDRDefinition.php';

class Models_NSDR2Test extends TestCase {

	protected $_definitions = array(
			'com'=>null,
			'com.clearhealth'=>null,
			'com.clearhealth.person'=>'Person',
			'com.clearhealth.person.firstName'=>null,
			'com.clearhealth.person.lastName'=>null,
			'com.clearhealth.person.middleName'=>'Person',
		);

	public function setUp() {
		parent::setUp();
		foreach ($this->_definitions as $key=>$val) {
			$nsdrDefinition = new NSDRDefinition();
			if ($nsdrDefinition->isNamespaceExists($key)) {
				continue;
			}
			$nsdrDefinition->namespace = $key;
			if ($val !== null) {
				$nsdrDefinition->ORMClass = $val;
			}
			$nsdrDefinition->persist();
		}
		NSDR2::systemReload();
	}

	public function tearDown() {
		parent::tearDown();
		$nsdrDefinition = new NSDRDefinition();
		foreach ($this->_definitions as $key=>$val) {
			$nsdrDefinition->removeByNamespace($key);
		}
		NSDR2::systemReload();
	}

	public function testPersist() {
		// test case 3
		$data = array();
		$data['first_name'] = 'John';
		$data['last_name'] = 'Doe';
		$data['middle_name'] = 'C';
		$result = NSDR2::persist('1234::com.clearhealth.person',$data);

		// verify if persist successful
		$result = NSDR2::populate('1234::com.clearhealth.person');
		$this->assertEquals($result['first_name'],$data['first_name'],'TEST CASE 3 failed.');
		$this->assertEquals($result['last_name'],$data['last_name'],'TEST CASE 3 failed.');
		$this->assertEquals($result['middle_name'],$data['middle_name'],'TEST CASE 3 failed.');

		// test case 4
		$result = NSDR2::persist('1234::com.clearhealth.person.middleName',array('harriet'));

		// verify if persist successful
		$result = NSDR2::populate('1234::com.clearhealth.person.middleName');
		$this->assertEquals($result,'harriet','TEST CASE 4 failed.');

		$result = NSDR2::populate('1234::com.clearhealth.person');
		$this->assertEquals($result['first_name'],$data['first_name'],'TEST CASE 4 failed.');
		$this->assertEquals($result['last_name'],$data['last_name'],'TEST CASE 4 failed.');
		$this->assertNotEquals($result['middle_name'],$data['middle_name'],'TEST CASE 4 failed.');
	}

	public function testPopulate() {
		$person = new Person();
		$person->personId = 1234;
		$person->populate();
		$firstName = $person->firstName;
		$person->firstName = 'John';
		$lastName = $person->lastName;
		$person->lastName = 'Doe';
		$middleName = $person->middleName;
		$person->middleName = 'C';

		$person->persist();

		// test case 1
		$result = NSDR2::populate('1234::com.clearhealth.person');
		$this->assertEquals($result['first_name'],$person->firstName,'TEST CASE 1 failed.');
		$this->assertEquals($result['last_name'],$person->lastName,'TEST CASE 1 failed.');
		$this->assertEquals($result['middle_name'],$person->middleName,'TEST CASE 1 failed.');

		// test case 2
		$result = NSDR2::populate('1234::com.clearhealth.person.middleName');
		$this->assertEquals($result,'C','TEST CASE 2 failed.');

		// test case 5
		$result = NSDR2::populate('1234::com.clearhealth.person[aggregateDisplay()]');
		$this->assertContains('Doe John  C',$result,'TEST CASE 5 failed.');

		// revert the person
		$person->firstName = $firstName;
		$person->lastName = $lastName;
		$person->middleName = $middleName;
		$person->persist();
	}

}
