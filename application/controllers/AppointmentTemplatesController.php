<?php
/*****************************************************************************
*       AppointmentTemplatesController.php
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


class AppointmentTemplatesController extends WebVista_Controller_Action {

	protected $_appointmentTemplate = null;

	public function editAction() {
		$ormId = (int)$this->_getParam('ormId');
		$enumerationId = (int)$this->_getParam('enumerationId');
		$enumerationsClosure = new EnumerationsClosure();
		$depth = (int)$enumerationsClosure->getDepthById($enumerationId);
		if ($depth === 0) {
			$this->view->message = __('Only appointment template entries can be edited');
		}
		else {
			$this->_appointmentTemplate = new AppointmentTemplate();
			$this->_appointmentTemplate->appointmentTemplateId = $ormId;
			$this->_appointmentTemplate->populate();
			$breakdowns = array();
			if (strlen($this->_appointmentTemplate->breakdown) > 0) {
				$breakdowns = unserialize($this->_appointmentTemplate->breakdown);
			}
			$this->view->breakdowns = $breakdowns;

			$this->_form = new WebVista_Form(array('name'=>'edit'));
			$this->_form->setAction(Zend_Registry::get('baseUrl') . 'appointment-templates.raw/process-edit');
			$this->_form->loadORM($this->_appointmentTemplate,'appointmentTemplate');
			$this->_form->setWindow('windowEditORMObjectId');
			$this->view->form = $this->_form;
			$this->view->enumerationId = $enumerationId;
		}
		$this->render('edit');
	}

	public function processEditAction() {
		$this->editAction();
		$enumerationId = (int)$this->_getParam('enumerationId');
		$params = $this->_getParam('appointmentTemplate');
		$appointmentTemplateId = (int)$params['appointmentTemplateId'];
		$this->_appointmentTemplate->populateWithArray($params);
		$breakdownNames = $this->_getParam('breakdownName');
		$breakdownLength = $this->_getParam('breakdownLength');
		$breakdownType = $this->_getParam('breakdownType');
		$breakdowns = array();
		if (is_array($breakdownNames) && is_array($breakdownLength) && is_array($breakdownType)) {
			foreach ($breakdownNames as $key=>$value) {
				if (!isset($breakdownLength[$key]) || !isset($breakdownType[$key])) {
					continue;
				}
				$data = array();
				$data['n'] = $value;
				$data['l'] = (int)$breakdownLength[$key];
				$data['t'] = $breakdownType[$key];
				$breakdowns[] = $data;
			}
			if (count($breakdowns) > 0) {
				$this->_appointmentTemplate->breakdown = serialize($breakdowns);
			}
		}
		$this->_appointmentTemplate->persist();
		if ($appointmentTemplateId === 0 && $enumerationId !== 0) {
			$enumeration = new Enumeration();
			$enumeration->enumerationId = $enumerationId;
			$enumeration->populate();
			$enumeration->ormId = $this->_appointmentTemplate->appointmentTemplateId;
			$enumeration->persist();
		}
		$this->view->message = __("Record saved successfully");
		$this->render('edit');
	}

	public function getContextMenuAction() {
		header('Content-Type: application/xml;');
		$this->render('get-context-menu');
	}

}
