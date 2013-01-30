<?php
/*****************************************************************************
*       TemplatedTextManagerController.php
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


class TemplatedTextManagerController extends WebVista_Controller_Action {

	public function indexAction() {
		$this->render('index');
	}

	public function listAction() {
		$rows = array();
		$templatedText = new TemplatedText();
		$templatedTextIterator = $templatedText->getIterator();
		foreach ($templatedTextIterator as $template) {
			$row = array();
			$row['id'] = $template->templateId;
			$row['data'][] = $template->name;
			$row['data'][] = $template->template;
			$rows[] = $row;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}

	public function processEditAction() {
		$templateId = (int)$this->_getParam('templateId');
		$field = $this->_getParam('field');
		$value = $this->_getParam('value');
		$templateText = new TemplatedText();
		if ($templateId > 0) {
			$templateText->templateId = $templateId;
			$templateText->populate();
		}
		$data = array();
		if (in_array($field,$templateText->ORMFields())) {
			$templateText->$field = $value;
			$templateText->persist();
			$data['id'] = $templateText->templateId;
			$data['value'] = $templateText->$field;
		}
		else {
			$data['error'] = __('Invalid column').': '.$field;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

}
