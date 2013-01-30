<?php
/*****************************************************************************
*       AdminServicesController.php
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


class AdminServicesController extends WebVista_Controller_Action {

	public function indexAction() {
		$this->render();
	}

	public function listServicesAction() {
		$rows = array();
		$services = Service::getServices();
		foreach ($services as $service) {
			$tmp = array();
			$tmp['id'] = $service['id'];
			$tmp['data'][] = $service['name'];
			$tmp['data'][] = $service['status'];
			$rows[] = $tmp;
		}

		$memcache = Zend_Registry::get('memcache');
		$serviceNominal = $memcache->get(Service::SERVICE_NOMINAL);
		$rows[0]['userdata']['serviceNominal'] = $serviceNominal;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	/**
	 * Toolbar xml structure
	 */
	public function toolbarAction() {
		// utilize the common toolbar method defined at WebVista_Controller_Action
		$this->_renderToolbar();
	}

	public function processPermissionAction() {
		$service = $this->_getParam('service');
		$validServices = array('start'=>'serviceStart','stop'=>'serviceStop','reload'=>'serviceReload');
		if (!isset($validServices[$service])) {
			$service = 'start';
		}
		$method = $validServices[$service];
		PermissionTemplate::$method();
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(true);
	}

}
