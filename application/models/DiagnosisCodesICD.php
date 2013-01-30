<?php
/*****************************************************************************
*       DiagnosisCodesICD.php
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


class DiagnosisCodesICD extends WebVista_Model_ORM {

	protected $code;
	protected $textShort;
	protected $textLong;
	protected $_table = "diagnosisCodesICD";
	protected $_primaryKeys = array("code");

	public function ormVisitTypeEditMethod($ormId,$isAdd) {
		return $this->ormEditMethod($ormId,$isAdd);
	}

	public function ormEditMethod($ormId,$isAdd) {
		$controller = Zend_Controller_Front::getInstance();
		$request = $controller->getRequest();
		$enumerationId = (int)$request->getParam('enumerationId');

		$view = Zend_Layout::getMvcInstance()->getView();
		$params = array();
		if ($isAdd) {
			$params['parentId'] = $enumerationId;
			unset($_GET['enumerationId']); // remove enumerationId from params list
			$params['grid'] = 'enumItemsGrid';
		}
		else {
			$params['enumerationId'] = $enumerationId;
			$params['ormId'] = $ormId;
		}
		return $view->action('edit-type','visit-details',null,$params);
	}

	public function getDiagnosisCodesICDId() {
		return $this->code;
	}

}
