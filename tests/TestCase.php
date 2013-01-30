<?php
/*****************************************************************************
*       TestCase.php
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
 * PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

class TestCase extends PHPUnit_Framework_TestCase {
	// workaround for error: PDOException: You cannot serialize or unserialize PDO instances
	protected $backupGlobals = false;

	protected $_autoLoggedIn = true;
	protected $_objects = array();

	public function setUp() {
		set_error_handler('errorTestHandler');
		$this->_setUpEnv();
		$this->_setUpDB();
		$this->_setUpCache();
		$this->_setUpACL();
		$this->testPrepare();
	}

	protected function _initSequenceTables() {
		$db = Zend_Registry::get('dbAdapter');
		$seqTableNames = array(Zend_Registry::get('config')->orm->sequence->table,
				   Zend_Registry::get('config')->audit->sequence->table);

		foreach ($seqTableNames as $tableName) {
			$id = $db->fetchOne('SELECT id FROM ' . $tableName);
			if (!strlen($id) > 0) {
				$db->insert($tableName,array('id'=>1));
			}
		}
	}

	public function testPrepare() {
		$this->_initSequenceTables();
		// test audit first
		$audit = new Audit();
		$audit->_ormPersist = true;
		$audit->objectClass = 'StdClass';
		$audit->persist();
		$this->assertTrue(($audit->auditId > 0),'Audit: Failed to persist');
		if ($audit->auditId > 0) {
			$audit->setPersistMode(WebVista_Model_ORM::DELETE);
			$audit->persist();
		}

		$auditValue = new AuditValue();
		$auditValue->_ormPersist = true;
		$auditValue->key = 'Key';
		$auditValue->value = 'Value';
		$auditValue->persist();
		$this->assertTrue(($auditValue->auditValueId > 0),'AuditValue: Failed to persist');
		if ($auditValue->auditValueId > 0) {
			$auditValue->setPersistMode(WebVista_Model_ORM::DELETE);
			$auditValue->persist();
		}
		if ($this->_autoLoggedIn) {
			$this->_setupAutoLogin();
		}
	}

	private function _setUpEnv() {
		error_reporting(E_ALL | E_STRICT);
		try {
			date_default_timezone_set(TEST_DATE_TIMEZONE);
		}
		catch (Zend_Exception $e) {
			die($e->getMessage());
		}
	}

	private function _setUpDB() {
		try {
			$dbAdapter = Zend_Db::factory(Zend_Registry::get('config')->database);
			$dbAdapter = Zend_Db::factory(TEST_DB_ADAPTER, array('host'=>TEST_DB_HOST,'username'=>TEST_DB_USERNAME,'password'=>TEST_DB_PASSWORD,'dbname'=>TEST_DB_DBNAME));

			$dbAdapter->query("SET NAMES 'utf8'");
		}
		catch (Zend_Exception $e) {
			die ($e->getMessage());
		}
		Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);
		Zend_Registry::set('dbAdapter',$dbAdapter);
	}

	private function _setUpAutoLogin() {
		$user = new User();
		$user->username = TEST_LOGIN_USERNAME;
		$user->populateWithUsername();
		if (!$user->userId > 0) {
			$person = new Person();
			$person->_shouldAudit = false;
			$person->last_name = 'Administrator';
			$person->first_name = 'ClearHealth';
			$person->middle_name = 'U';
			$person->persist();

			$user->_shouldAudit = false;
			$user->person = $person;
			$user->password = TEST_LOGIN_PASSWORD;
			$user->userId = $person->personId;
			$user->personId = $person->personId;
			$user->permissionTemplateId = 'superadmin';
			$user->persist();
		}
		Zend_Auth::getInstance()->getStorage()->write($user);
	}

	private function _setUpCache() {
		$frontendOptions = array('lifetime' => 3600, 'automatic_serialization' => true);
		$backendOptions = array('file_name_prefix' => 'clearhealth', 'hashed_directory_level' => 1, 'cache_dir' => '/tmp/', 'hashed_directory_umask' => 0700);
		$cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
		Zend_Registry::set('cache', $cache);

		$cache = new Memcache();
		$cache->connect('127.0.0.1',11211);
		$status = $cache->getServerStatus('127.0.0.1',11211);
		if ($status === 0) {
			// memcache server failed, do error trapping?
		}
		Zend_Registry::set('memcache', $cache);
	}

	private function _setUpACL() {
		$memcache = Zend_Registry::get('memcache');
		$key = 'acl';
		$acl = $memcache->get($key);
		if ($acl === false) {
			$acl = WebVista_Acl::getInstance();
			// populate acl from db
			$acl->populate();
			// save to memcache
			$memcache->set($key,$acl);
		}
		Zend_Registry::set('acl',$acl);
	}

	public function tearDown() {
		parent::tearDown();
		$this->_cleanUpObjects($this->_objects);
	}

	protected function _cleanUpObjects(Array $objects) {
		foreach ($objects as $object) {
			if (!$object instanceof ORM) {
				continue;
			}
			$object->setPersistMode(WebVista_Model_ORM::DELETE);
			$object->persist();
		}
	}

}

function errorTestHandler($errno, $errstr, $errfile, $errline) {
}
