<?php
/*****************************************************************************
*       CodeLookupController.php
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
 * Code Lookup controller
 */
class CodeLookupController extends WebVista_Controller_Action {

	public function indexAction() {
		$src = strtolower(preg_replace('/[^a-z-0-9]/i','',$this->_getParam('src')));
		$q = preg_replace('/[^a-zA-Z0-9\%\.]/','',$this->_getParam('q'));
		$rows = array();
		if (strlen($q) > 0) {
			switch ($src) {
				case 'cpt':
					$procedureCodeIterator = new ProcedureCodesCPTIterator();
					$procedureCodeIterator->setFilters($q);
					$rows = $procedureCodeIterator->toJsonArray('code',array('textLong','code'));
					break;
				case 'icd9':
					$diagnosisCodeIterator = new DiagnosisCodesICDIterator();
					$diagnosisCodeIterator->setFilter($q);
					$icd = $diagnosisCodeIterator->toJsonArray('code',array('textShort','code'));

					$diagnosisCodeSNOMEDIterator = new DiagnosisCodesSNOMEDIterator();
					$diagnosisCodeSNOMEDIterator->setFilter($q);
					$snomed = $diagnosisCodeSNOMEDIterator->toJsonArray('snomedId',array('description','snomedId'));
					$rows = array_merge($icd,$snomed);
					break;
				default:
					break;
			}
		}
		$data = array();
		$data['rows'] = $rows;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

}
