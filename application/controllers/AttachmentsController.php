<?php
/*****************************************************************************
*       AttachmentsController.php
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


class AttachmentsController extends WebVista_Controller_Action {


        public function init() {
                $this->_session = new Zend_Session_Namespace(__CLASS__);
        }

	public function addAttachmentsAction() {
		$attachmentReferenceId = preg_replace('[^a-zA-Z0-9-]','//',$this->_getParam('attachmentReferenceId'));
		$this->view->attachmentReferenceId = $attachmentReferenceId;
		$this->view->callback = $this->_getParam('callback','');
		$this->render();
	}

	public function processAddAttachmentsAction() {
		$fileData = file_get_contents($_FILES["uploadFile"]["tmp_name"]);
		$attachment = new Attachment();
		$attachment->name = $_FILES['uploadFile']['name'];
		$attachment->attachmentReferenceId = preg_replace('[^a-zA-Z0-9-]','//',$this->_getParam('attachmentReferenceId'));
		$attachment->mimeType = $_FILES['uploadFile']['type'];
		$attachment->md5sum = md5($fileData);
		$attachment->dateTime = date('Y-m-d H:i:s');
		$attachment->persist();

		$attachmentBlobArray = array();
		$attachmentBlobArray['attachmentId'] = $attachment->attachmentId;
		$attachmentBlobArray['data'] = $fileData;

		$db = Zend_Registry::get('dbAdapter');
		$db->insert('attachmentBlobs',$attachmentBlobArray);

		$acj = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $acj->suppressExit = true;
                $jsonData = $acj->direct(array("attachmentId" => $attachment->attachmentId),false);
		$this->getResponse()->setHeader('Content-Type', 'text/html');
		$this->view->result = $jsonData;
		$this->render();
	}
	
	public function viewUploadProgressAction() {
		$status = apc_fetch('upload_'.$this->_getParam('uploadKey'));
		$percent = 0;
		if ($status['current'] > 0 ) {
		$percent = $status['current']/$status['total']*100;
		}
		$acj = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $acj->suppressExit = true;
                $acj->direct($percent);

	}

	public function listAttachmentsAction() {
		$attachmentReferenceId = preg_replace('[^a-zA-Z0-9-]','//',$this->_getParam('attachmentReferenceId'));
                $db = Zend_Registry::get('dbAdapter');
                $atSelect = $db->select()
                                        ->from('attachments')
                                        ->where('attachments.attachmentReferenceId = ' . $attachmentReferenceId)
                                        ->order('attachments.dateTime DESC');
                //echo $cnSelect->__toString();
                //var_dump($db->query($atSelect)->fetchAll());exit;
                $attachments = array();
                foreach($db->query($atSelect)->fetchAll() as $row) {
                        $attachments[] = array("id" => $row['attachmentId'],"data" => array($row['mimeType'], '<a href="'.$this->view->baseUrl.'/attachments.raw/view-attachment?attachmentId=' . $row['attachmentId'] . '">' . $row['name'] . '</a>', $row['dateTime']));
                }
                //var_dump($notes);exit;

                $acj = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
                $acj->suppressExit = true;
                $acj->direct(array("rows" => $attachments));
	}
	
	public function viewAttachmentAction() {
		$attachmentId = (int)$this->_getParam('attachmentId');
		$attachment = new Attachment();
		$attachment->attachmentId = $attachmentId;
		$attachment->populate();
		$db = Zend_Registry::get('dbAdapter');
		$sql = "select data from attachmentBlobs where attachmentId = " . $attachmentId;
		$stmt = $db->query($sql);
		$row = $stmt->fetch();
		$this->view->content = $row['data'];
		switch ($attachment->mimeType) {
                       case 'image/png':
                       case 'image/jpg':
                       case 'image/jpeg':
                       case 'image/gif':
                               $this->getResponse()->setHeader('Content-Type', $attachment->mimeType);
				break;
                       case 'application/x-shockwave-flash':
                               $this->getResponse()->setHeader('Content-Type', $attachment->mimeType);
				$stmt->closeCursor();
				$this->render();
				return;
                               break;
                       default:
                               $this->getResponse()->setHeader('Content-Type', 'application/binary');
               }
                $this->getResponse()->setHeader('Content-Disposition', 'attachment; filename="' . $attachment->name . '"');
		$stmt->closeCursor();
		$this->render();
	}

}
