<?php
/*****************************************************************************
*       NsdrController.php
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
 * Controller for Name Space Data Resolver
 */
class NsdrController extends WebVista_Controller_Action {

	// memcache handler
	protected $_memcache = null;

	// supported methods
	protected $_supportedMethods = array('GET','POST','XML','JSON');

	// namespace common identifier
	protected $_nsCommonIdentifier = '::com.clearhealth.';

	// namespace handler
	protected $_nsdrNamespace = null;

	// namespace handler value
	protected $_nsdrNamespaceValue = null;

	// cache key for system status
	protected $_statusKey = 'systemStatus';

	/**
	 * All request in this controller must follow the namespace conventions
	 * this function will initialize the namespace and its value (if persist request)
	 * taken from the REQUEST_URI
	 */
	public function init() {
		// retrieves memcache from registry and assign it in this
		$this->_memcache = Zend_Registry::get('memcache');

		// work-around for namespace as part of the URL's key
		$requestUri = $_SERVER['REQUEST_URI'];
		$requestUri = str_replace($this->view->baseUrl,'',$requestUri);
		$explodedRU = explode('?',$requestUri);
		if (count($explodedRU) != 2) {
			return;
		}
		$xpUri = explode('&',$explodedRU[1]);
		foreach ($xpUri as $kvp) {
			$explodedKVP = explode('=',$kvp);
			if (count($explodedKVP) == 0) {
				continue;
			}
			$key = $explodedKVP[0];
			$val = '';
			if (isset($explodedKVP[1])) {
				$val = $explodedKVP[1];
			}
			if (strpos($key,$this->_nsCommonIdentifier) !== false) {
				$this->_nsdrNamespace[] = $key;
				$this->_nsdrNamespaceValue[] = $val;
				break;
			}
		}
	}

	/**
	 * Argument to this action either the following:
	 * 1) form-encoded POST
	 * 2) GET QUERY
	 * 3) POST with XML payload (informal REST style) or JSON
	 * @todo: this method is temporarily not complete
	 */
	public function persistAction() {
		$method = strtoupper($this->_getParam('method'));
		if (!in_array($method,$this->_supportedMethods)) {
			$msg = __("Method ")."'$method'".__(" does not supported");
			throw new Exception($msg);
		}
		$persistMethod = "persist{$method}";
		$ret = $this->$persistMethod();
	}

	/**
	 * persist given a GET request
	 */
	protected function persistGET() {
		return NSDR2::persist($this->_nsdrNamespace);
	}

	/**
	 * persist given a POST request
	 * @todo: this method is temporarily not complete
	 */
	protected function persistPOST() {
		$ret = array();
		foreach ($this->_nsdrNamespace as $key=>$namespace) {
			$ret[$namespace] = NSDR2::persist($namespace,$this->_nsdrNamespaceValue[$key]);
		}
	}

	/**
	 * persist given a POST with XML payload request
	 */
	protected function persistXML() {
		// to be implemented...
		return true;
	}

	/**
	 * persist given a POST with JSON payload request
	 */
	protected function persistJSON() {
	}

	/**
	 * Argument to this action either the following:
	 * 1) form-encoded POST
	 * 2) GET QUERY
	 * 3) POST with XML payload (informal REST style) or JSON
	 *
	 * Expected returns to this action either the following:
	 * 1) JSON
	 * 2) PHP array
	 * 3) XML
	 * @todo: this method is temporarily not complete
	 */
	public function populateAction() {
		$method = strtoupper($this->_getParam('method'));
		if (!in_array($method,$this->_supportedMethods)) {
			$msg = __("Method ")."'$method'".__(" does not supported");
			throw new Exception($msg);
		}
		$populateMethod = "populate{$method}";
		$ret = $this->$populateMethod();
	}

	/**
	 * Returns an array
	 */
	protected function populateGET() {
		return NSDR2::populate($this->_nsdrNamespace);
		$result = '';
		$key = $this->_nsdrNamespace;
		// extract method name from namespace
		preg_match('/\[(.*)\(\)\]$/',$this->_nsdrNamespace,$matches);
		// return if no method defined
		if (!isset($matches[1])) {
			return $result;
		}
		$nsdrMethodName = $matches[1];
		if ($method = $this->_memcache->get($key)) { // get returns FALSE if error or key not found
			$nsdrBase = new NSDRBase();
			// create anonymous function and use the code from memcache
			$anonFunc = create_function('$tthis',$method);
			if ($anonFunc === false) {
				return $result;
			}
			// assign the unique function name returned by create_function to NSDRBase property 
			$nsdrBase->$nsdrMethodName = $anonFunc;
			// execute the function and pass the base as $tthis instead of $this
			$result = $nsdrBase->$nsdrMethodName($nsdrBase);
		}
		return $result;
	}

	/**
	 * Returns an array
	 */
	protected function populatePOST() {
		$ret = array();
		return $ret;
	}

	/**
	 * Returns JSON
	 */
	protected function populateJSON() {
		// to be implemented... based on XML
	}

	/**
	 * Returns an XML
	 */
	protected function populateXML() {
		// to be implemented...
		return true;
	}

	/**
	 * Argument to this action is either of the following:
	 * 1) start - starts the system, push namespace datapoint definition into memcached from database store
	 * 2) reload - reloads the system
	 * 3) unload - unloads the system
	 * 4) status - get the status on the system, returns state of the system
	 *             (states are: "started", "starting", "reloaded", "reloading", "unloaded", "unloading")
	 *
	 * Expected return to this function is the state of the system if the parameter is status,
	 * otherwise it returns nothing
	 */
	public function systemAction() {
		// valid parameters definition
		$expectedParameters = array('start','reload','unload','status');
		$event = $this->_getParam('event');
		if (!in_array($event,$expectedParameters)) {
			$msg = __("Invalid event ").$event;
			throw new Exception($msg);
		}
		$systemMethod = 'system' . ucwords($event);
		$data['msg'] = $this->{$systemMethod}();
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);

	}

	protected function systemStart() {
		// clean cache data first
		NSDR2::systemUnload();
		NSDR2::systemStart();
		return __('System successfully started.');
	}

	protected function systemReload() {
		NSDR2::systemReload();
		return __('System successfully reloaded.');
	}

	protected function systemUnload() {
		NSDR2::systemUnload();
		return __('System successfully unloaded.');
	}

	protected function systemStatus() {
		$ret = NSDR2::systemStatus();
		if ($ret === false) {
			$ret = __("Stop");
		}
		return $ret;
	}
}
