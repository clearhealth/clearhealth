<?php
/*****************************************************************************
*       DrugScheduleDaysSupplyTest.php
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
 * DrugScheduleDaysSupply
 */
require_once 'DrugScheduleDaysSupply.php';


class Models_DrugScheduleDaysSupplyTest extends TestCase {

	public function testBID() {
		$quantity = 3;
		$ret = DrugScheduleDaysSupply::BID($quantity);
		$this->assertEquals($ret,2);
	}

	public function testTID() {
		$quantity = 3;
		$ret = DrugScheduleDaysSupply::TID($quantity);
		$this->assertEquals($ret,1);
	}

	public function testMOWEFR() {
		$quantity = 3;
		$ret = DrugScheduleDaysSupply::MOWEFR($quantity);
		$this->assertEquals($ret,3);
	}

	public function testNOW() {
		$quantity = 3;
		$ret = DrugScheduleDaysSupply::NOW($quantity);
		$this->assertEquals($ret,3);
	}

	public function testONCE() {
		$quantity = 3;
		$ret = DrugScheduleDaysSupply::ONCE($quantity);
		$this->assertEquals($ret,3);
	}

	public function testQ12H() {
		$quantity = 2;
		$ret = DrugScheduleDaysSupply::Q12H($quantity);
		$this->assertEquals($ret,1);
		$quantity = 3;
		$ret = DrugScheduleDaysSupply::Q12H($quantity);
		$this->assertEquals($ret,2);
	}

	public function testQ24H() {
		$quantity = 1;
		$ret = DrugScheduleDaysSupply::Q24H($quantity);
		$this->assertEquals($ret,1);
		$quantity = 3;
		$ret = DrugScheduleDaysSupply::Q24H($quantity);
		$this->assertEquals($ret,3);
	}

	public function testQ2H() {
		$quantity = 1;
		$ret = DrugScheduleDaysSupply::Q2H($quantity);
		$this->assertEquals($ret,1);
		$quantity = 13;
		$ret = DrugScheduleDaysSupply::Q2H($quantity);
		$this->assertEquals($ret,2);
	}

	public function testQ3H() {
		$quantity = 3;
		$ret = DrugScheduleDaysSupply::Q3H($quantity);
		$this->assertEquals($ret,1);
		$quantity = 9;
		$ret = DrugScheduleDaysSupply::Q3H($quantity);
		$this->assertEquals($ret,2);
	}

	public function testQ4H() {
		$quantity = 3;
		$ret = DrugScheduleDaysSupply::Q4H($quantity);
		$this->assertEquals($ret,1);
		$quantity = 8;
		$ret = DrugScheduleDaysSupply::Q4H($quantity);
		$this->assertEquals($ret,2);
	}

	public function testQ6H() {
		$quantity = 6;
		$ret = DrugScheduleDaysSupply::Q6H($quantity);
		$this->assertEquals($ret,2);
		$quantity = 10;
		$ret = DrugScheduleDaysSupply::Q6H($quantity);
		$this->assertEquals($ret,3);
	}

	public function testQ8H() {
		$quantity = 4;
		$ret = DrugScheduleDaysSupply::Q8H($quantity);
		$this->assertEquals($ret,2);
		$quantity = 9;
		$ret = DrugScheduleDaysSupply::Q8H($quantity);
		$this->assertEquals($ret,3);
	}

	public function testQ5MIN() {
		$quantity = 3;
		$ret = DrugScheduleDaysSupply::Q5MIN($quantity);
		$this->assertEquals($ret,1);
		$quantity = 288;
		$ret = DrugScheduleDaysSupply::Q5MIN($quantity);
		$this->assertEquals($ret,1);
		$quantity = 289;
		$ret = DrugScheduleDaysSupply::Q5MIN($quantity);
		$this->assertEquals($ret,2);
	}

	public function testQDAY() {
		$quantity = 3;
		$ret = DrugScheduleDaysSupply::QDAY($quantity);
		$this->assertEquals($ret,3);
	}

}
