<?php
/*****************************************************************************
*       DashboardComponentsManagerController.php
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


class DashboardComponentsManagerController extends WebVista_Controller_Action {

	public function indexAction() {
		$this->view->types = $this->_getTypes();
		$this->render('index');
	}

	protected function _getTypes() {
		$types = array();
		$types[] = 'JS';
		$types[] = 'PHP';
		$types[] = 'URL';
		return $types;
	}

	public function listAction() {
		$rows = array();
		$dashboardComponent = new DashboardComponent();
		$dashboardComponentIterator = $dashboardComponent->getIterator();
		foreach ($dashboardComponentIterator as $dashboard) {
			$row = array();
			$row['id'] = $dashboard->dashboardComponentId;
			$row['data'][] = $dashboard->name;
			$row['data'][] = $dashboard->systemName;
			$row['data'][] = $dashboard->type;
			$row['data'][] = $dashboard->content;
			$rows[] = $row;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function processEditAction() {
		$dashboardComponentId = $this->_getParam('dashboardComponentId');
		$field = $this->_getParam('field');
		$value = $this->_getParam('value');
		$dashboardComponent = new DashboardComponent();
		$dashboardComponent->dashboardComponentId = $dashboardComponentId;
		$dashboardComponent->populate();
		$data = array();
		if (in_array($field,$dashboardComponent->ORMFields())) {
			$dashboardComponent->$field = $value;
			$dashboardComponent->persist();
			$data['id'] = $dashboardComponent->dashboardComponentId;
			$data['value'] = $dashboardComponent->$field;
		}
		else {
			$data['error'] = __('Invalid column').': '.$field;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

}
