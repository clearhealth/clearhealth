<?php
ini_set("soap.wsdl_cache_enabled", "0");
ini_set("memory_limit", "128M");
// soap gateway to freeb2

require_once '../../../celini/bootstrap.php';
// first, include the SOAP/Server class
require_once 'SOAP/Server.php';

$server = new SOAP_Server;
/* tell server to translate to classes we provide if possible */
$server->_auto_translation = true;


// create a gateway
$loader->requireOnce('controllers/C_FreeBGateway.class.php');
$loader->requireOnce('ordo/ORDataObject.class.php');

class FreeBGateway extends C_FreeBGateway {
	var $__dispatch_map = array();

	function FreeBGateway() {
		parent::C_FreeBGateway();

		$this->__dispatch_map['openClaim'] = array(
			'in' => array('claim_identifier' => 'string', 'revision' => 'string','claim_mode' => 'string'),
			'out' => array('status' => 'int'),
		);
		$this->__dispatch_map['registerData'] = array(
			'in' => array('claim_identifier' => 'string', 'type' => 'string','data_array' => 'array'),
			'out' => array('status' => 'int'),
		);
		$this->__dispatch_map['closeClaim'] = array(
			'in' => array('claim_identifier' => 'string', 'revision' => 'string'),
			'out' => array('status' => 'int'),
		);
		$this->__dispatch_map['claimLastError'] = array(
			'in' => array('claim_identifier' => 'string'),
			'out' => array('error' => 'string'),
		);
		$this->__dispatch_map['test'] = array(
			'in' => array('claim_identifier' => 'array'),
			'out' => array('error' => 'string'),
		);
		$this->__dispatch_map['cci_get_patient'] = array(
			'in' => array('person_id' => 'string'),
			'out' => array('patient_data' => 'string'),
		);
	}

	function registerData($claim_identifier,$type,$data_array) {
		if(is_object($data_array)) {
			$tmp = array();
			foreach($data_array as $key => $val) {
				if (is_object($val)) {
					foreach($val as $k => $v) {
						$tmp[$key][$k] = $v;
					}
				}
				else {
					$tmp[$key] = $val;
				}
			}
			$data_array = $tmp;
		}
		return parent::registerData($claim_identifier,$type,$data_array);
	}

	function test($data_array) {
		if(is_object($data_array)) {
			$tmp = array();
			foreach($data_array as $key => $val) {
				if (is_object($val)) {
					foreach($val as $k => $v) {
						$tmp[$key][$k] = $v;
					}
				}
				else {
					$tmp[$key] = $val;
				}
			}
			$data_array = $tmp;
		}
		return parent::test($data_array);
	}
	function cci_get_patient($data_array) {
		if(is_object($data_array)) {
			$tmp = array();
			foreach($data_array as $key => $val) {
				if (is_object($val)) {
					foreach($val as $k => $v) {
						$tmp[$key][$k] = $v;
					}
				}
				else {
					$tmp[$key] = $val;
				}
			}
			$data_array = $tmp;
		}
		$person_id = (int)$data_array;
		$p =& ORDataObject::factory('Patient', $person_id);
		return "1234";
	}
}
$soapclass = new FreebGateway();
$server->addObjectMap($soapclass,'urn:FreeBGateway');

if (isset($_SERVER['REQUEST_METHOD']) &&
    $_SERVER['REQUEST_METHOD']=='POST') {
    $server->service($HTTP_RAW_POST_DATA);
} else if (isset($_SERVER['QUERY_STRING']) &&
       strcasecmp($_SERVER['QUERY_STRING'],'run')==0) {

	$fb = new FreeBGateway();
	var_dump($fb->cci_get_patient("1000577"));

} else {
    require_once 'SOAP/Disco.php';
    $disco = new SOAP_DISCO_Server($server,'FreeBGateway');
    header("Content-type: text/xml");
    if (isset($_SERVER['QUERY_STRING']) &&
       strcasecmp($_SERVER['QUERY_STRING'],'wsdl')==0) {
        echo $disco->getWSDL();
    } else {
        echo $disco->getDISCO();
    }
    exit;
}
?>
