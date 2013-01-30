<?php
/*****************************************************************************
*       NSDR2.php
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

class NSDR2 extends NSDR {

	public static function systemStart() {
		$memcache = Zend_Registry::get('memcache');
		$memcache->set(self::$_statusKey,self::$_states[1]);

		// By default all NSDR methods will return an empty/false value
		$methods = array(
			'persist' => 'return false;',
			'populate' => 'return "";',
			'aggregateDisplay' => 'return "";',
			'aggregateDisplayByLine' => 'return "";',
			'iterator' => 'return array();',
			'mostRecent' => 'return array();',
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
			// default methods
			foreach ($methods as $method=>$value) {
				$keySuffix = '['.$method.'()]';
				$key = $row->namespace.$keySuffix;
				if ($namespaceAlias !== null) {
					$value = $namespaceAlias.$keySuffix;
				}
				else if ($ORMClass !== null) {
					$value = $ORMClass; // override $value
				}
				trigger_error('NSDR: '.$key.' = '.$value);
				$memcache->set($key,$value);
			}
			// user-defined NSDR methods
			$nsdrMethods = $row->methods;
			if ($namespaceAlias !== null) continue;
			foreach ($nsdrMethods as $method) {
				if (!strlen($method->methodName) > 0 || !strlen($method->method) > 0) continue;
				$value = $method->method;
				$keySuffix = '['.$method->methodName.'()]';
				$key = $row->namespace.$keySuffix;
				trigger_error('NSDR Method: '.$key.' = '.$value);
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
		$memcache = Zend_Registry::get('memcache');
		$memcache->set(self::$_statusKey,self::$_states[5]);

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
			foreach ($methods as $method=>$value) {
				$keySuffix = '['.$method.'()]';
				$key = $row->namespace.$keySuffix;
				self::removeNamespace($key);
			}
		}

		$memcache->set(self::$_statusKey,self::$_states[4]);
	}

	/**
	 * Execute memcache value specified by namespace
	 *
	 * @param NSDRBase $nsdrBase
	 * @param mixed $context
	 * @param string $namespace
	 * @param mixed $data
	 * @param int $level Prevents loops for alias
	 * @return mixed
	 */
	/*protected static function _populateMethod(NSDRBase $nsdrBase,$context,$namespace,$data = null,$level = 0) {
		// $namespace here is in the form of space.name[method()]
		$memcache = Zend_Registry::get('memcache');
		// retrieves logic code in memcache
		$result = $memcache->get($namespace);
		if ($result !== false) { // has cached entry
			if (preg_match('/(.*)\[(.*)\(\)\]$/',$namespace,$matches)) {
				$method = ucfirst($matches[2]);
			}

			if (preg_match('/^([A-Z]+):(.*)/',$result,$resultMatches)) {
				$keyPrefix = $resultMatches[1];
				$value = $resultMatches[2];
				switch ($keyPrefix) {
					case 'ORMCLASS':
						$obj = new $value();
						$methodCall = 'nsdrPopulate';
						if (isset($method)) {
							if (method_exists($obj,'nsdr'.$method)) {
								$methodCall = 'nsdr'.$method;
							}
							else {
								// temporarily lookup the method from NSDRBase
								if (method_exists($nsdrBase,$method)) {
									return $nsdrBase->$method($nsdrBase,$context,$data);
								}
								$msg = __('Method specified does not exists').': '.lcfirst($method).'()';
								throw new Exception($msg);
							}
						}
						return $obj->$methodCall($nsdrBase,$context,$data);
						break;
					case 'ALIAS':
						if ($level < 1) { // prevents loops
							$level++;
							return self::_populateMethod($nsdrBase,$context,$value,$data,$level);
						}
						break;
					default: // unrecognized prefix
						$msg = __('Unrecognized prefix').': '.$keyPrefix;
						break;
				}
			}
			else {
				self::_createShutdownHandler($namespace,'populate');
				$nsdrBase->methods[$namespace] = create_function('$tthis,$context,$data',$result);
				return $nsdrBase->methods[$namespace]($nsdrBase,$context,$data);
			}
		}
		else {
			$msg = __('Namespace does not exist in memcache').': '.$namespace;
		}
		throw new Exception($msg);
	}*/

	protected static function _createShutdownHandler($namespace,$method) {
		$code = 'if ($error === null) return;
			$type = $error[\'type\'];
			$message = $error[\'message\'];
			$file = $error[\'file\'];
			if (!preg_match(\'/runtime-created function/\',$file)) return;
			if ($type == E_ERROR || $type == E_CORE_ERROR || $type == E_COMPILE_ERROR) { // catch fatal errors';
		if ($method == 'populate') {
			$code .= '
				die(\'Retreiving the data for query : \'.$namespace.\' timed out, please contact the administrator\');';
		}
		else if ($method == 'persist') {
			$code .= '
				die(\'Persisting the data for query : \'.$namespace.\' timed out, please contact the administrator\');';
		}
		else {
			$code .= '
				die(\'Data for query : \'.$namespace.\' timed out, please contact the administrator\');';
		}
		$code .= '
			}';
		register_shutdown_function(create_function('$func','$func(error_get_last(),\''.$namespace.'\');'),create_function('$error,$namespace',$code));
	}

	/**
	 * Modified populate version
	 *
	 * @param string $request
	 * @return mixed
	 */
	public static function populate($request) {
		if (is_array($request)) {
			$ret = array();
			foreach ($request as $key=>$value) {
				$ret[$key] = self::populate($value);
			}
			return $ret;
		}
		$memcache = Zend_Registry::get('memcache');
		$request = trim($request);
		// $request is in the form of 1234::com.clearhealth.person[populate()]
		// tokenize $request
		if (self::systemStatus() != self::$_states[0] && self::systemStatus() != self::$_states[2]) {
			return __('NSDR sub-system is not running and needs to be started').': '.$request;
		}
		$tokens = explode('::',$request);
		if (count($tokens) !== 2) {
			return __('Invalid request').': '.$request;
		}
		$context = $tokens[0]; // contains context
		$namespace = $tokens[1]; // set second token to $namespace as default value

		$methods = array(); //array('populate()'); // set default populate() method, this must exist
		if (preg_match('/(.*)\[(.*)\]$/',$tokens[1],$matches)) {
			if (isset($matches[1])) { // namespace exists name.space
				$namespace = $matches[1]; // override $namespace default value
			}
			if (isset($matches[2])) { // functions/arguments exists method1(@arg1=arg1value,@argn=argnvalue),methodn(@arg1=arg1value,@argn=argnvalue)
				$methods = self::_extractMethods($matches[2],'populate');
			}
		}

		$result = null;
		$nsdrBase = new NSDRBase();
		$nsdrBase->_nsdrNamespace = $namespace;
		$nsdrBase->_aliasedNamespace = $request;
		if (!isset($methods[0])) $methods[] = array('name'=>'populate()','attributes'=>array()); // set default method, this must exist
		foreach ($methods as $method) {
			$nsdrBase->_attributes = $method['attributes'];
			$key = $namespace.'['.$method['name'].']';
			//trigger_error('METHOD: '.$method['name'].' ATTRIBS: '.print_r($method['attributes'],true));
			if ($result !== null) {
				$result = self::_execMethod('populate',$nsdrBase,$context,$key,$result);
			}
			else {
				$result = self::_execMethod('populate',$nsdrBase,$context,$key);
			}
		}
		return $result;
	}

	protected static function _extractAttributes($data) {
		$attributes = array();
		$arrAttribs = preg_split('/,\ {0,}@/',$data);
		if (isset($arrAttribs[0])) {
			$arrAttribs[0] = substr($arrAttribs[0],1); // remove @
			foreach ($arrAttribs as $attr) {
				if (!strlen($attr) > 0) continue;
				$equalPos = strpos($attr,'=');
				$attribKey = $attr;
				$attribVal = '';
				if ($equalPos !== false) {
					$attribKey = substr($attr,0,$equalPos);
					$attribVal = substr($attr,($equalPos+1));
				}
				$attributes[$attribKey] = $attribVal;
			}
		}
		return $attributes;
	}

	protected static function _extractMethods($data,$defaultMethod='populate') {
		$methods = array();
		$attributes = array();
		$arrData = preg_split('/\)\ {0,},/',$data);
		$ctr = count($arrData) - 1;
		for ($i = 0; $i <= $ctr; $i++) {
			$val = trim($arrData[$i]);
			if (!strlen($val) > 0) continue;
			if ($i == $ctr && substr($val,-1) == ')') $val = substr($val,0,-1);
			if (substr($val,0,1) == '@') { // considered as attributes
				$attribs = $val;
				$attributes = self::_extractAttributes($attribs);
				$methods[] = array('name'=>$defaultMethod.'()','attributes'=>$attributes);
				continue;
			}
			// extract method name
			$attributes = array();
			$pos = strpos($val,'(');
			if ($pos !== false) {
				$attribs = substr($val,($pos+1)); // +1 = exclude character (
				$attributes = self::_extractAttributes($attribs);
				$val = substr($val,0,$pos);
			}
			$methods[] = array('name'=>$val.'()','attributes'=>$attributes);
		}
		if (!isset($methods[0])) {
			$methods[] = array('name'=>$defaultMethod.'()','attributes'=>$attributes); // set default method, this must exist
		}
		return $methods;
	}

	/**
	 * Execute logic code defined in memcache specified by namespace
	 *
	 * @param string $method Either populate or persist, default to populate if invalid method specified
	 * @param NSDRBase $nsdrBase
	 * @param mixed $context
	 * @param string $namespace
	 * @param mixed $data
	 * @param int $level Prevents loops for alias
	 * @return mixed
	 */
	protected static function _execMethod($method,NSDRBase $nsdrBase,$context,$namespace,$data = null,$level = 0) {
		$definedMethods = array('populate','persist');
		if (!in_array($method,$definedMethods)) {
			$method = 'populate';
		}
		$defaultMethod = ucfirst($method);
		// $namespace here is in the form of space.name[method()]
		$memcache = Zend_Registry::get('memcache');
		// retrieves logic code in memcache
		$result = $memcache->get($namespace);
		trigger_error($context.'::'.$namespace.' = '.$result);
		if ($result !== false) { // has cached entry
			if (preg_match('/(.*)\[(.*)\(\)\]$/',$namespace,$matches)) {
				$nsMethod = ucfirst($matches[2]);
			}
			if (preg_match('/^([A-Z]+):(.*)/',$result,$resultMatches)) {
				$keyPrefix = $resultMatches[1];
				$value = $resultMatches[2];
				switch ($keyPrefix) {
					case 'ORMCLASS':
						$obj = new $value();
						$methodCall = 'nsdr'.$defaultMethod;
						if (isset($nsMethod)) {
							if ($nsMethod == 'Iterator') {
								$getIterMethods = array();
								$getIterMethods[] = 'getIterator';
								$getIterMethods[] = 'getIter';
								$setFilterMethods = array();
								$setFilterMethods[] = 'setFilter';
								$setFilterMethods[] = 'setFilters';
								foreach ($getIterMethods as $iterMethod) {
									if (!method_exists($obj,$iterMethod)) continue;
									$iter = $obj->$iterMethod();
									foreach ($setFilterMethods as $filterMethod) {
										if (!method_exists($iter,$filterMethod)) continue;
										$filters = $nsdrBase->_attributes;
										if (!$filters) $filters = array();
										$filters['context'] = $context;
										$iter->$filterMethod($filters);
										break;
									}
									return $iter;
								}
							}
							if (method_exists($obj,'nsdr'.$nsMethod)) {
								$methodCall = 'nsdr'.$nsMethod;
							}
							else {
								// temporarily lookup the method from NSDRBase
								if (method_exists($nsdrBase,$nsMethod)) {
									return $nsdrBase->$nsMethod($nsdrBase,$context,$data);
								}
								$msg = __('Method specified does not exists').': '.lcfirst($nsMethod).'()';
								throw new Exception($msg);
							}
						}
						return $obj->$methodCall($nsdrBase,$context,$data);
						break;
					case 'ALIAS':
						if ($level < 1) { // prevents loops
							$level++;
							return self::_execMethod($method,$nsdrBase,$context,$value,$data,$level);
						}
						break;
					default: // unrecognized prefix
						$msg = __('Unrecognized prefix').': '.$keyPrefix;
						break;
				}
			}
			else {
				self::_createShutdownHandler($namespace,$method);
				$nsdrBase->methods[$namespace] = create_function('$tthis,$context,$data',$result);
				return $nsdrBase->methods[$namespace]($nsdrBase,$context,$data);
			}
		}
		else {
			$msg = __('Namespace does not exist in memcache').': '.$namespace;
		}
		trigger_error($msg,E_USER_NOTICE);
		return $msg;
	}

	/**
	 * Persist the given namespace/s
	 *
	 * @param string $request
	 * @param array $data
	 * @return boolean
	 * @throw Exception
	 */
	public static function persist($request,$data) {
		$memcache = Zend_Registry::get('memcache');
		$request = trim($request);
		// $request is in the form of 1234::com.clearhealth.person[populate()]
		// tokenize $request
		if (self::systemStatus() != self::$_states[0] && self::systemStatus() != self::$_states[2]) {
			return __('NSDR sub-system is not running and needs to be started').': '.$request;
		}
		$tokens = explode('::',$request);
		if (count($tokens) !== 2) {
			return __('Invalid request').': '.$request;
		}
		$context = $tokens[0]; // contains context
		$namespace = $tokens[1]; // set second token to $namespace as default value


		$methods = array(); //array('persist()'); // set default persist() method, this must exist
		if (preg_match('/(.*)\[(.*)\]$/',$tokens[1],$matches)) {
			if (isset($matches[1])) { // namespace exists name.space
				$namespace = $matches[1]; // override $namespace default value
			}
			if (isset($matches[2])) { // functions/arguments exists method1(@arg1=arg1value,@argn=argnvalue),methodn(@arg1=arg1value,@argn=argnvalue)
				$methods = self::_extractMethods($matches[2],'persist');
			}
		}

		$result = null;
		$nsdrBase = new NSDRBase();
		$nsdrBase->_nsdrNamespace = $namespace;
		$nsdrBase->_aliasedNamespace = $request;
		if (!isset($methods[0])) $methods[] = array('name'=>'persist()','attributes'=>array()); // set default method, this must exist
		foreach ($methods as $method) {
			$nsdrBase->_attributes = $method['attributes'];
			$key = $namespace.'['.$method['name'].']';
			if ($result !== null) {
				$result = self::_execMethod('persist',$nsdrBase,$context,$key,$result);
			}
			else {
				$result = self::_execMethod('persist',$nsdrBase,$context,$key);
			}
		}
		return $result;
	}

	public static function extractNamespace($namespace) {
		$x = explode('.',$namespace);
		if (!isset($x[1])) return $namespace;
		$first = array_shift($x);
		$last = array_pop($x);
		$fx = explode('::',$first);
		if (isset($fx[1])) {
			$first = $fx[1];
		}
		array_unshift($x,$first);
		$lx = explode('[',$last);
		array_push($x,$lx[0]);
		$namespace = implode('.',$x);
		return $namespace;
	}

	public static function removeNamespace($key) {
		$memcache = Zend_Registry::get('memcache');
		trigger_error('Delete memcache key:'.$key,E_USER_NOTICE);
		$ret = $memcache->delete($key,0);
		return $ret;
	}

}

