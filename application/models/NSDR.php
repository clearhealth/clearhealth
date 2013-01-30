<?php
/*****************************************************************************
*       NSDR.php
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
 * Independent class specific for NSDR functionality
 */

class NSDR {

	protected static $_ORMClass = null;
	// valid _states definition
	protected static $_states = array('started','starting','reloaded','reloading','unloaded','unloading');
	// cache key for system status
	protected static $_statusKey = 'systemStatus';

	public static function populate($request) {
		$nsdrDefinitions = array();
		$memcache = Zend_Registry::get('memcache');
		// enclosed it in array if $request is not an array
		if (!is_array($request)) {
			$request = array($request);
		}

		$ret = array();
		$nsdrBase = new NSDRBase();
		foreach ($request as $data) {
			if (self::systemStatus() != self::$_states[0] && self::systemStatus() != self::$_states[2]) {
				$ret[$data] = __("NSDR sub-system is not running and needs to be started: {$data}");
				continue;
			}

			// check if $data has a cached entry
			$result = $memcache->get($data);
			if ($result !== false) { // has cached entry
				// use the cache entry
				$ret[$data] = $result;
				continue;
			}
			$nsdrBase->_requestedNamespace = $data;

			// tokenize request
			$tokens = explode('::',$data);
			if (count($tokens) == 1) {
				$context = '';
				$namespace = $tokens[0];
			}
			else {
				$context = $tokens[0];
				$namespace = $tokens[1];
			}

			if (!array_key_exists($namespace,$nsdrDefinitions)) {
				// populate NSDR Definition
				if (preg_match('/(.*)\[(.*)\]$/',$namespace,$matches)) {
					$nsdrNamespace = $matches[0];
				}
				else {
					$nsdrNamespace = $namespace;
				}
				if (array_key_exists(1,$matches)) {
					$nsdrNamespace = $matches[1];
				}
				$nsdr = new NSDRDefinition();
				$nsdr->populateByNamespace($nsdrNamespace);
				// check if namespace exists in NSDR using its primary key uuid
				if (strlen($nsdr->uuid) <= 0) {
					$ret[$data] = __("NSDR definition does not exist: {$data}");
					continue;
				}
				if (strlen($nsdr->aliasFor) > 0) {
					$nsdrBase->_aliasedNamespace = $nsdr->aliasFor;
					if ($nsdrAlias = $memcache->get($nsdr->aliasFor)) {
						if (!self::isORM($nsdrAlias)) {
							$ret[$data] = $nsdrAlias;
							continue;
						}
						self::$_ORMClass = new $nsdrAlias($context,$nsdr->namespace);
					}
					else {
						$nsdrAlias = new NSDRDefinition();
						$nsdrAlias->populateByNamespace($nsdr->aliasFor);
						// check if namespace exists in NSDR using its primary key uuid
						if (strlen($nsdrAlias->uuid) <= 0) {
							$ret[$data] = __("NSDR definition does not exist: {$data}");
							continue;
						}
						$nsdr = $nsdrAlias;
					}
				}
				$nsdrBase->_aliasedNamespace = $namespace;
				$nsdrDefinitions[$namespace] = $nsdr;
			}
			else {
				$nsdrBase->_aliasedNamespace = $namespace;
				$nsdr = $nsdrDefinitions[$namespace];
			}
			// check if ORMClass has a value and that class exists
			if (self::$_ORMClass === null && self::isORM($nsdr->ORMClass)) {
				self::$_ORMClass = new $nsdr->ORMClass($context);
			}

			// use namespace as key
			$key = $namespace;

			// check for multiple methods
			//preg_match('/(.*)\[(.*\(\))+\]$/',$key,$matches);
			preg_match('/(.*)\[(.*\(.*\))+\]/',$key,$matches);
			if (isset($matches[2])) { // methods exists
				// $matches[1] is the new namespace
				$newNamespace = $matches[1];
				$methods = $matches[2];
				$newMethods = explode('),',$methods);
				if (count($newMethods) > 1) { // found multiple methods requested
					// execute methods one by one and pass the result of the current to the next method as parameter
					$result = null;
					foreach ($newMethods as $val) {
						if (substr($val,-1,1) != ')') {
							$val .= ')';
						}
						$key = $newNamespace . "[{$val}]";
						if (self::$_ORMClass === null) {
							$result = self::getNSDRMethod($nsdrBase,$key,$context,$result);
						}
						else {
							$result = self::getNSDRORMMethod($nsdrBase,$key,$context,$val,$result);
						}
					}
				}
				else {
					if (substr($newMethods[0],-1,1) != ')') {
						$newMethods[0] .= ')';
					}
					$key = $newNamespace . "[{$newMethods[0]}]";
					if (self::$_ORMClass === null) {
						$result = self::getNSDRMethod($nsdrBase,$key,$context);
					}
					else {
						$result = self::getNSDRORMMethod($nsdrBase,$key,$context,$newMethods[0]);
					}
				}
			}
			else {
				if (self::$_ORMClass === null) {
					$result = self::getNSDRMethod($nsdrBase,$key,$context);
				}
				else {
					$result = self::getNSDRORMMethod($nsdrBase,$key,$context,"populate()");
				}
			}
			$ret[$data] = $result;

			if (self::$_ORMClass !== null) {
				self::$_ORMClass = null;
			}
		}
		return $ret;
	}

	protected static function isORM($ORMClass) {
		$ret = false;
		if (strlen($ORMClass) > 0 && class_exists($ORMClass)) {
			$classImplements = class_implements($ORMClass);
			// check if ORMClass implements NSDRMethods interface
			if (in_array('NSDRMethods',$classImplements)) {
				$ret = true;
			}
		}
		return $ret;
	}

	protected static function hasORMClass(NSDRDefinition $nsdr) {
		return self::isORM($nsdr->ORMClass);
	}

	protected static function getNSDRORMMethod(NSDRBase $nsdrBase,$key,$context,$method,$data=null) {
		$memcache = Zend_Registry::get('memcache');
		// check to see if key exists in memcache
		$result = $memcache->get($key);
		preg_match("/(.*)\((.*)\)/",$method,$matches);
		$methodName = $matches[1];
		$contextArray[$context] = array('filters' => array());
		if ($result === false) { // key not found
			if (!strlen($method) > 0) return array('error'=>__('Invalid method'));
			// extract the method context methodName($context)
			if (array_key_exists(2,$matches)) {
				// replace context with the one found on method call method(context)
				$methodArgs = $matches[2];
				if (strlen($methodArgs) > 0 && $methodArgs{0} == '@') {
					$str = str_replace(',@','&@',$methodArgs);
					parse_str($str,$array);
					foreach ($array as $k=>$v) {
						if (get_magic_quotes_gpc()) {
							$v = stripslashes($v);
						}
						$key = trim(str_replace('@','',$k));
						$contextArray[$context]['filters'][$key] = trim($v);
					}
				}
				else {
					// retain the methodName(value)
					$contextArray[$context]['filters'][$methodArgs] = '';
				}
			}
			if ($methodName == 'iterator') {
				$getIterMethods = array();
				$getIterMethods[] = 'getIterator';
				$getIterMethods[] = 'getIter';
				$setFilterMethods = array();
				$setFilterMethods[] = 'setFilter';
				$setFilterMethods[] = 'setFilters';
				foreach ($getIterMethods as $iterMethod) {
					if (method_exists(self::$_ORMClass,$iterMethod)) {
						$iter = self::$_ORMClass->$iterMethod();
						foreach ($setFilterMethods as $filterMethod) {
							if (method_exists($iter,$filterMethod)) {
								if (!is_array($context)) {
									$context = array($context);
								}
								$iter->$filterMethod($context);
								return $iter;
							}
						}
						break;
					}
				}
			}

			$methodName[0] = strtoupper($methodName[0]);
			$nsdrMethodName = 'nsdr'.$methodName;
			// check if method prefix with nsdr exists in ORMClass
			if (method_exists(self::$_ORMClass,$nsdrMethodName)) {
				$result = self::$_ORMClass->$nsdrMethodName($nsdrBase,$contextArray,$data);
			}
			else {
				// check if method exists in NSDRBase
				if (method_exists($nsdrBase,$methodName)) {
					$result = $nsdrBase->$methodName($nsdrBase,$contextArray,$data);
				}
				else {
					// otherwise, do the ordinary routine
					$result = self::getNSDRMethod($nsdrBase,$key,$contextArray);
				}
			}
		}
		else {
			if (self::isORM($result)) {
				$nsdr = new NSDRDefinition();
				$nsdr->populateByNamespace($key);
				if (strlen($nsdr->aliasFor) > 0) {
					self::$_ORMClass = new $result($contextArray,$key);
				}
				else {
					self::$_ORMClass = new $result($contextArray);
				}
				$methodName[0] = strtoupper($methodName[0]);
				$nsdrMethodName = 'nsdr'.$methodName;
				if (method_exists(self::$_ORMClass,$nsdrMethodName)) {
					$result = self::$_ORMClass->$nsdrMethodName($nsdrBase,$contextArray,$data);
				}
			}
		}
		return $result;
	}

	protected static function getNSDRMethod(NSDRBase $nsdrBase,$key,$context,$data=null) {
		$memcache = Zend_Registry::get('memcache');

		// check to see if key exists in memcache
		$result = $memcache->get($key);
		if ($result === false) { // key not found
			return array('error'=>__('key does not exists in memcached'));
		}
		if (!isset($nsdrBase->methods[$key])) {
			preg_match('/\[(.*\(\))+\]$/',$key,$matches);
			if (isset($matches[1])) { // function exists
				if ($data !== null) {
					$nsdrBase->methods[$key] = create_function('$tthis,$context,$data',$result);
				}
				else {
					$nsdrBase->methods[$key] = create_function('$tthis,$context',$result);
				}
			}
			else { // no function exists
				// check to see if key exists in memcache
				$keyWithContext = $context . '::' . $key;
				$resultKeyWithContext = $memcache->get($keyWithContext);
				if ($resultKeyWithContext === false) { // key not found
					$keyWithMethod = $key . '[populate()]';
					if (!isset($nsdrBase->methods[$keyWithMethod])) {
						return array('error'=>__('key does not exists'));
					}
					// alters key
					$key = $keyWithMethod;
				}
				else {
					$nsdrBase->methods[$keyWithContext] = create_function('$tthis,$context',$resultKeyWithContext);
					// alters key
					$key = $keyWithContext;
				}
			}
		}
		if ($data !== null) {
			$ret = $nsdrBase->methods[$key]($nsdrBase,$context,$data);
		}
		else {
			$ret = $nsdrBase->methods[$key]($nsdrBase,$context);
		}
		return $ret;
	}

	public static function systemStart() {
		$memcache = Zend_Registry::get('memcache');
		$memcache->set(self::$_statusKey,self::$_states[1]);

		// By default all NSDR methods will return an empty/false value
		$methods = array(
			'persist' => 'return false;',
			'populate' => 'return "";',
			'aggregateDisplay' => 'return "";',
			'aggregateDisplayByLine' => 'return "";',
		);

		$nsdrDefinition = new NSDRDefinition();
		$nsdrDefinitionIterator = $nsdrDefinition->getIterator();
		foreach ($nsdrDefinitionIterator as $row) {
			$namespaceAlias = null;
			$ORMClass = null;
			if (strlen($row->aliasFor) > 0 && $row->isNamespaceExists($row->aliasFor)) { // Alias must check first, alias must be canonical
				// temporary implemented this way, it can be changed later
				$namespaceAlias = 'ALIAS:'.$row->aliasFor; // prefix with ALIAS
			}
			else if (self::hasORMClass($row)) {
				$ORMClass = 'ORMCLASS:'.$row->ORMClass; // prefix with ORMCLASS
			}
			foreach ($methods as $method=>$value) {
				$keySuffix = '['.$method.'()]';
				$key = $row->namespace.$keySuffix;
				if ($namespaceAlias !== null) {
					$value = $namespaceAlias.$keySuffix;
				}
				else if ($ORMClass !== null) {
					$value = $ORMClass; // override $value
				}
				$memcache->set($key,$value);
			}
		}

		$memcache->set(self::$_statusKey,self::$_states[0]);
	}

	public static function systemReload() {
		$ret = false;
		$memcache = Zend_Registry::get('memcache');
		$memcache->set(self::$_statusKey,self::$_states[3]);
		self::systemUnload();
		self::systemStart();
		$ret = true;
		$memcache->set(self::$_statusKey,self::$_states[2]);
		return $ret;
	}

	public static function systemUnload() {
		$ret = false;
		$memcache = Zend_Registry::get('memcache');
		$memcache->set(self::$_statusKey,self::$_states[5]);
		$memcache->flush();
		$ret = true;
		$memcache->set(self::$_statusKey,self::$_states[4]);
		return $ret;
	}

	public static function systemStatus() {
		$memcache = Zend_Registry::get('memcache');
		// retrieve status info from memcache
		return $memcache->get(self::$_statusKey);
	}

	public static function generateTestData() {
		$guids = array();
		$nsdrDefinition = new NSDRDefinition();
		$nsdrDefinition->truncate();
		$namespaces = array();
		$namespaces['com'] = array();
		$namespaces['com.clearhealth'] = array();
		$namespaces['com.clearhealth.enumerations'] = array('ORMClass'=>'Enumeration');
		$namespaces['com.clearhealth.enumerations.gender'] = array('aliasFor'=>'com.clearhealth.enumerations');
		$namespaces['com.clearhealth.person'] = array('methods'=>array(array('methodName'=>'aggregateDisplay','method'=>'return "aggregateDisplay";'),array('methodName'=>'populate','method'=>'return "populated data";'),array('methodName'=>'persist','method'=>'return "persisted data";'),));
		$namespaces['com.clearhealth.person.salutation'] = array();
		$namespaces['com.clearhealth.person.lastName'] = array();
		$namespaces['com.clearhealth.person.firstName'] = array();
		$namespaces['com.clearhealth.person.middleName'] = array();
		$namespaces['com.clearhealth.person.gender'] = array();
		$namespaces['com.clearhealth.person.dataOfBirth'] = array();
		$namespaces['com.clearhealth.person.problemList'] = array('ORMClass'=>'ProblemList');
		$namespaces['com.clearhealth.person.vitalSignsGroup'] = array('ORMClass'=>'VitalSignGroup');

		foreach ($namespaces as $namespace=>$val) {
			$nsdr = clone $nsdrDefinition;
			$nsdr->uuid = NSDR::create_guid();
			$guids[] = $nsdr->uuid;
			$nsdr->namespace = $namespace;

			// workaround for Unknown column 'methodName'/'method' in 'field list'
			$nsdr->methodName = array();
			$nsdr->method = array();

			if(count($val) > 0) {
				$nsdr->populateWithArray($val);
			}

			$nsdr->persist();

			if (isset($val['methods'])) {
				$nsdr->persistMethods($val['methods']);
			}
		}
		return $guids;
	}

	public static function create_guid() {
		if (function_exists('com_create_guid')) {
			return com_create_guid();
		}
		else {
			mt_srand((double)microtime()*10000);
			$charid = strtolower(md5(uniqid(rand(),true)));
			$hyphen = chr(45);// "-"
			$uuid = substr($charid,0,8) . $hyphen
			      . substr($charid,8,4) . $hyphen
			      . substr($charid,12,4) . $hyphen
			      . substr($charid,16,4) . $hyphen
			      . substr($charid,20,12);
			return $uuid;
		}
	}

}

