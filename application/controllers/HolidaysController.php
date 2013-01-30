<?php
/*****************************************************************************
*       HolidaysController.php
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


class HolidaysController extends WebVista_Controller_Action {

	public function indexAction() {
		$this->render();
	}

	public function listAction() {
		$holiday = new Holiday();
		$holidayIterator = $holiday->getIterator();
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$holidayIterator->toJsonArray('holidayId',array('date','description'))));
	}

	public function getContextMenuAction() {
		header('Content-Type: application/xml;');
		$this->render();
	}

	public function processEditAction() {
		$params = $this->_getParam('holiday');
		$holiday = new Holiday();
		$holiday->populateWithArray($params);
		$holiday->persist();
		$data = true;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processDeleteAction() {
		$id = (int)$this->_getParam('id');
		$holiday = new Holiday();
		$holiday->holidayId = $id;
		$holiday->setPersistMode(WebVista_Model_ORM::DELETE);
		$holiday->persist();
		$data = true;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

}
