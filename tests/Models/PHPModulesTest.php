<?php
/*****************************************************************************
*       PHPModulesTest.php
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
 * Unit test for PHP Modules
 */

require_once dirname(dirname(__FILE__)).'/TestHelper.php';

/**
 * TestCase
 */
require_once 'TestCase.php';

class Models_PHPModulesTest extends TestCase {
	protected $_modulesList = array();

	public function setUp() {
		parent::setUp();
		$this->_modulesList['imagecreatefrompng'] = 'php-gd';
		$this->_modulesList['curl_exec'] = 'php-curl';
		$this->_modulesList['apc_add'] = 'php-apc';
		$this->_modulesList['PDO'] = 'php-pdo';
		$this->_modulesList['simplexml_load_string'] = 'php-xml';
		$this->_modulesList['mysql_connect'] = 'php-mysql';
		$this->_modulesList['XSLTProcessor'] = 'php-xsl';
		$this->_modulesList['uuid_create'] = 'php-uuid';
	}

	public function testPHPModulesExist() {
		foreach ($this->_modulesList as $moduleFunc => $module) {
			$this->assertTrue((function_exists($moduleFunc) || class_exists($moduleFunc)),"Module: {$module} must be installed.");
		}
	}

}

