<?php
/*****************************************************************************
*       ExamsController.php
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
 * Exams controller
 */
class ExamsController extends WebVista_Controller_Action {

	public function listAction() {
                $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
                $json->direct(array('rows'=>Enumeration::enumerationToJson(PatientExam::ENUM_PARENT_NAME)),true);
        }

	protected function _getEnumerationByName($name) {
		$enumeration = new Enumeration();
		$enumeration->populateByEnumerationName($name);
		$enumerationsClosure = new EnumerationsClosure();
		$enumerationIterator = $enumerationsClosure->getAllDescendants($enumeration->enumerationId,1);
		$ret = array();
		foreach ($enumerationIterator as $enumeration) {
			$ret[$enumeration->key] = $enumeration->name;
		}
		return $ret;
	}

	public function listSectionAction() {
		$sectionId = (int)$this->_getParam('section');
		$enumerationsClosure = new EnumerationsClosure();
		$enumerationIterator = $enumerationsClosure->getAllDescendants($sectionId,1);
		$rows = array();
		foreach ($enumerationIterator as $enum) {
			$tmp = array();
			$tmp['id'] = $enum->key;
			$tmp['data'][] = '';
			$tmp['data'][] = $enum->name;
			$tmp['data'][] = $enum->key;
			$rows[] = $tmp;
		}
                $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
                $json->direct(array('rows'=>$rows),true);
        }

	public function lookupAction() {
		$this->view->jsCallback = $this->_getParam('callback','');
		$this->view->listExams = $this->_getEnumerationByName(PatientExam::ENUM_OTHER_PARENT_NAME);
		$this->render('lookup');
	}

	public function processPatientExamsAction() {
		$patientId = (int)$this->_getParam('patientId');
		$exams = $this->_getParam('exams');
		if ($patientId > 0) {
			$patientExamIterator = new PatientExamIterator();
			$patientExamIterator->setFilters(array('patientId'=>$patientId));
			$existingExams = $patientExamIterator->toArray('code','patientId');
			if (is_array($exams)) {
				foreach ($exams as $code=>$exam) {
					if (isset($existingExams[$code])) {
						unset($existingExams[$code]);
					}
					$exam['code'] = $code;
					$exam['patientId'] = $patientId;
					$patientExam = new PatientExam();
					$patientExam->populateWithArray($exam);
					$patientExam->persist();
				}
			}
			// delete un-used records
			foreach ($existingExams as $code=>$patientId) {
				$patientExam = new PatientExam();
				$patientExam->code = $code;
				$patientExam->patientId = $patientId;
				$patientExam->setPersistMode(WebVista_Model_ORM::DELETE);
				$patientExam->persist();
			}
		}
		$data = array();
		$data['msg'] = __('Record saved successfully');
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function listPatientExamsAction() {
		$patientId = (int)$this->_getParam('patientId');
		$rows = array();
		if ($patientId > 0) {
			$patientExamIterator = new PatientExamIterator();
			$patientExamIterator->setFilters(array('patientId'=>$patientId));
			//$listResults = $this->_getEnumerationByName(PatientExam::ENUM_RESULT_PARENT_NAME);
			foreach ($patientExamIterator as $exam) {
				//$result = '';
				//if (isset($listResults[$exam->result])) {
				//	$result = $listResults[$exam->result];
				//}
				$tmp = array();
				$tmp['id'] = $exam->code;
				$tmp['data'][] = $exam->dateExamined;
				$tmp['data'][] = $exam->result;
				$tmp['data'][] = $exam->exam;
				$tmp['data'][] = $exam->refused;
				$tmp['data'][] = $exam->comments;
				$rows[] = $tmp;
			}
		}
		$data = array();
		$data['rows'] = $rows;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processEditExamAction() {
		$exams = $this->_getParam('exams');
		$patientExam = new PatientExam();
		$patientExam->populateWithArray($exams);
		$patientExam->persist();
		$data = array(
			'id'=>$patientExam->code,
			'data'=>array(
				date('Y-m-d',strtotime($patientExam->dateExamined)),
				(string)$patientExam->result,
				(string)$patientExam->exam,
				(string)$patientExam->refused,
				(string)$patientExam->comments,
			),
		);
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processDeleteExamAction() {
		$code = $this->_getParam('code');
		$patientExam = new PatientExam();
		$patientExam->code = $code;
		$patientExam->setPersistMode(WebVista_Model_ORM::DELETE);
		$patientExam->persist();
		$data = true;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function examContextMenuAction() {
		header('Content-Type: application/xml;');
		$this->render();
	}

}
