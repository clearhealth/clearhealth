<?php
/*****************************************************************************
*       AuditLogViewerController.php
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


class AuditLogViewerController extends WebVista_Controller_Action {

	protected $_session;

	public function init() {
		$this->_session = new Zend_Session_Namespace(__CLASS__);
	}

	public function indexAction() {
		if (!isset($this->_session->dateStart)) $this->_session->dateStart = date('Y-m-d');
		if (!isset($this->_session->dateEnd)) $this->_session->dateEnd = date('Y-m-d');
		if (!isset($this->_session->startTime)) $this->_session->startTime = date('00:00:00');
		if (!isset($this->_session->endTime)) $this->_session->endTime = date('23:59:59');
		$this->view->dateStart = $this->_session->dateStart;
		$this->view->dateEnd = $this->_session->dateEnd;
		$this->view->startTime = $this->_session->startTime;
		$this->view->endTime = $this->_session->endTime;
		$this->render();
	} 

	public function listAction() {
		$start = $this->_getParam('start','');
		if ($start == '') $start = date('Y-m-d');
		$end = $this->_getParam('end','');
		if ($end == '') $end = $start;

		$startTime = $this->_getParam('startTime','00:00:00');
		$endTime = $this->_getParam('endTime','23:59:59');

		$this->_session->dateStart = $start;
		$this->_session->dateEnd = $end;
		$this->_session->startTime = $startTime;
		$this->_session->endTime = $endTime;
		$dateStart = date('Y-m-d 00:00:00',strtotime($start));
		$dateEnd = date('Y-m-d 23:59:59',strtotime($end));
		$db = Zend_Registry::get("dbAdapter");
		$dbSelect = $db->select()
                               ->from('audits')
                                ->joinLeft('user', 'user.person_id = audits.userId')
                                ->joinLeft('person', 'person.person_id = audits.patientId')
                                ->joinLeft('patient', 'patient.person_id = audits.patientId')
                               ->where("dateTime >= '{$start} {$startTime}' AND dateTime <= '{$end} {$endTime}'")
                               ->order('dateTime DESC');
		$dbStmt = $db->query($dbSelect);
		//$audit = new Audit();
		//$iterator = $audit->getIteratorByDateRange($dateStart,$dateEnd);
		$rows = array();
		foreach ($dbStmt->fetchAll() as $orm) {
			$row = array();
			$row['id'] = $orm['auditId'];
			$row['data'] = array();
			$dateTime = explode(' ',date('Y-m-d H:i:s',strtotime($orm['dateTime'])));
			$row['data'][] = $dateTime[0]; // Date
			$row['data'][] = $dateTime[1]; // Time
			$message = '';
			if ($orm['patientId'] > 0) {
				$message .= 'Name:' . $orm['last_name'] . "," . $orm['first_name'] . ' MRN #' . $orm['record_number']; // Patient
			}
			$message .= ' ' . $orm['message'];
			$row['data'][] = $message;
			$row['data'][] = $orm['username'] . " " . $orm['ipAddress']; // User
			$row['data'][] = $orm['objectClass']; // Action
			$rows[] = $row;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct(array('rows'=>$rows));
	}
	public function auditContextMenuAction() {
                //placeholder function, template is xml and autorenders when called as audit-context-menu.raw
        }
	public function exportAction() {
		$start = $this->_getParam('start','');
                if ($start == '') $start = date('Y-m-d');
                $end = $this->_getParam('end','');
                if ($end == '') $end = $start;

                $startTime = $this->_getParam('startTime','00:00:00');
                $endTime = $this->_getParam('endTime','23:59:59');

                $this->_session->dateStart = $start;
                $this->_session->dateEnd = $end;
                $this->_session->startTime = $startTime;
                $this->_session->endTime = $endTime;
                $dateStart = date('Y-m-d 00:00:00',strtotime($start));
                $dateEnd = date('Y-m-d 23:59:59',strtotime($end));
                $db = Zend_Registry::get("dbAdapter");
                $dbSelect = $db->select()
                               ->from('audits')
                                ->joinLeft('user', 'user.person_id = audits.userId')
                                ->joinLeft('person', 'person.person_id = audits.patientId')
                                ->joinLeft('patient', 'patient.person_id = audits.patientId')
                               ->where("dateTime >= '{$start} {$startTime}' AND dateTime <= '{$end} {$endTime}'")
                               ->order('dateTime DESC');
                $dbStmt = $db->query($dbSelect);
                //$audit = new Audit();
                //$iterator = $audit->getIteratorByDateRange($dateStart,$dateEnd);
                $rows = array();
                foreach ($dbStmt->fetchAll() as $orm) {
                        $row = array();
                        $row['id'] = $orm['auditId'];
                        $row['data'] = array();
                        $dateTime = explode(' ',date('Y-m-d H:i:s',strtotime($orm['dateTime'])));
                        $row['data'][] = $dateTime[0]; // Date
                        $row['data'][] = $dateTime[1]; // Time
                        $message = '';
                        if ($orm['patientId'] > 0) {
                                $message .= 'Name:' . $orm['last_name'] . "," . $orm['first_name'] . ' MRN #' . $orm['record_number']; // Patient
                        }
                        $message .= ' ' . $orm['message'];
                        $row['data'][] = $message;
                        $row['data'][] = $orm['username'] . " " . $orm['ipAddress']; // User
                        $row['data'][] = $orm['objectClass']; // Action
                        $rows[] = $row;
                }
                $data = array();
                $filename = 'al_'.uniqid('').'.csv';
                $headers = array(
                        'auditid'=>'Audit Id',
                        'date'=>'Date',
                        'time'=>'Time',
                        'patientormessage'=>'Patient/Message',
                        'user'=>'User',
                        'action'=>'Action'
                );
                $auditList = array(implode(',',$headers));
                foreach ($rows as $auditId => $row) {
                        $auditList[] = '"' . $row['id'] . '","' . implode('","',$row['data']) . '"';
                }
                $contents = implode("\r\n",$auditList);
                $filePath = '/tmp/'.$filename;
                if (file_put_contents($filePath,$contents) !== false) {
                        $data = array('filename'=>$filename);
                }
                else {
                        $error = 'Failed to create file: '.$filename;
                        trigger_error($error);
                        $data = array('error',$error);
                }
                $json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $json->suppressExit = true;
                $json->direct($data);
        }
	public function downloadExportedAction() {
                $filename = $this->_getParam('filename','');
                $filePath = '/tmp/'.$filename;
                if (!strlen($filename) > 0) {
                        $contents = 'Invalid filename.';
                }
                else if (!file_exists($filePath)) {
                        $contents = "File '$filename' does not exists.";
                        trigger_error($contents);
                }
                else {
                        $contents = file_get_contents($filePath);
                }
                $this->view->contents = $contents;
                $this->getResponse()->setHeader('Content-Type','application/binary');
                $this->getResponse()->setHeader('Content-Disposition','attachment; filename="'.$filename.'"');
                $this->render();
        }


}
