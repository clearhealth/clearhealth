<?php
/*****************************************************************************
*       PatientPicturesController.php
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


class PatientPicturesController extends WebVista_Controller_Action {

	public function indexAction() {
		$personId = (int)$this->_getParam('personId');
		$this->view->personId = $personId;
		$this->render();
	}

	public function listPicturesAction() {
		$personId = (int)$this->_getParam('personId');
		$person = new Person();
		$person->personId = $personId;
		$person->populate();
		$attachment = new Attachment();
		$attachmentIterator = $attachment->getIteratorByAttachmentReferenceId($personId);
		$xml = '<data>';
		foreach($attachmentIterator as $file) {
			$description = '';
			$active = 0;
			if ($person->activePhoto == $file->attachmentId) {
				$active = 1;
				$description = '(Active)';
			}
			$xml .= '<item id="'.$file->attachmentId.'" active="'.$active.'">';
			$xml .= '<title>'.$file->name.'</title>';
			$xml .= '<description>'.$description.'</description>';
			$xml .= '</item>';
		}
		$xml .= '</data>';
		$this->view->xml = $xml;
		header('Content-type: text/xml');
		$this->render();
	}

	public function processSetActivePictureAction() {
		$personId = (int)$this->_getParam('personId');
		$attachmentId = (int)$this->_getParam('id');
		$data = false;
		if ($personId > 0 && $attachmentId > 0) {
			$person = new Person();
			$person->personId = $personId;
			$person->populate();
			$person->activePhoto = $attachmentId;
			$person->persist();
			$data = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function processDeletePictureAction() {
		$personId = (int)$this->_getParam('personId');
		$attachmentId = (int)$this->_getParam('id');
		$data = false;
		if ($attachmentId > 0) {
			$attachment = new Attachment();
			$attachment->attachmentId = $attachmentId;
			$attachment->setPersistMode(WebVista_Model_ORM::DELETE);
			$attachment->persist();
			$person = new Person();
			$person->personId = $personId;
			$person->populate();
			if ($person->activePhoto == $attachmentId) {
				$person->activePhoto = 0;
				$person->persist();
			}
			$data = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}
	function thumbnailAction () {
                $size = preg_replace('/[^0-9x]/','',$this->_getParam('size'));
                $personId = (int)$this->_getParam('personId');
                list($height, $width) = preg_split('/x/',$size);

                $patientPicture = $this->_makeThumbnail($height, $width, $personId);

                header('Content-type: image/png');
                imagepng($patientPicture->image);
                Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender();
                return;
        }

        function thumbnailFileAction () {
                $size = preg_replace('/[^0-9x]/','',$this->_getParam('size'));
                $personId = (int)$this->_getParam('personId');
                list($height, $width) = preg_split('/x/',$size);

                $patientPicture = $this->_makeThumbnail($height, $width, $personId);
                $thumbnailFile = tempnam("/tmp","thumbnail");
                imagepng($patientPicture->image,$thumbnailFile);
                Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender();
                $response = $this->getResponse();
                $response->setBody($thumbnailFile);
                return;
        }

        function _makeThumbnail($height, $width, $personId) {
		$person = new Person();
		$person->personId = $personId;
		$person->populate();
		$picFile = '';
		if ($person->activePhoto > 0) {
			$attachmentId = $person->activePhoto;
	                $attachment = new Attachment();
	                $attachment->attachmentId = $attachmentId;
	                $attachment->populate();
	                $db = Zend_Registry::get('dbAdapter');
	                $sql = "select data from attachmentBlobs where attachmentId = " . $attachmentId;
	                $stmt = $db->query($sql);
	                $row = $stmt->fetch();
			$picFile = tempnam('/tmp','patpic');
			file_put_contents($picFile,$row['data']);
			$stmt->closeCursor();
		}
		else {
        		$patientDir = Zend_Registry::get('config')->document->legacyStorePath . '/' . $personId;

                	if ($personId == 0 || !file_exists($patientDir)) {$this->noPictureAction();}
                	$dir = dir($patientDir);
                	$picturePath = array();
                	while (false !== ($entry = $dir->read())) {
                	        if (preg_match('/.*_pat_pic([0-9]+.*).jpg/',$entry,$matches)) {
                	                $timestamp = strtotime($matches[1]);
                	                if (!isset($picturePath['timestamp']) ||
                	                        (isset($picturePath['timestamp']) &&
                	                         $timestamp > $picturePath['timestamp'])) {
                	                        $picturePath['timestamp'] = $timestamp;
                	                        $picturePath['path'] = $patientDir . '/' . $entry;
                	                }
                	        }
                	}
			$picFile = $picturePath['path'];
		}
                if (!file_exists($picFile)) {$this->noPictureAction();}
                $patientPicture = new ResizeImage();
                $patientPicture->load($picFile);
                $patientPicture->resize((int)$width,(int)$height);
                return $patientPicture;
        }
	function noPictureAction() {
                header('Content-type: image/png');
                readfile(Zend_Registry::get('basePath') . "/img/no-person-image.png");
                Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender();
                return;

        }
	
	function flashUploadAction() {
		$attachmentReferenceId = (int) $this->_getParam('attachmentReferenceId');
		$fileData = base64_decode($this->_getParam('fileUpload'));
		$attachment = new Attachment();
		if (!isset($_FILES['uploadFile']['name'])) {
			$attachment->name = date('Y-m-d');
		}
		else {
			$attachment->name = $_FILES['uploadFile']['name'];
		}
                $attachment->attachmentReferenceId = $attachmentReferenceId;
                $attachment->mimeType = 'image/jpeg';
                $attachment->md5sum = md5($fileData);
                $attachment->dateTime = date('Y-m-d H:i:s');
                $attachment->persist();

                $attachmentBlobArray = array();
                $attachmentBlobArray['attachmentId'] = $attachment->attachmentId;
                $attachmentBlobArray['data'] = $fileData;

                $db = Zend_Registry::get('dbAdapter');
                $db->insert('attachmentBlobs',$attachmentBlobArray);
		$person = new Person();
		$person->personId = $attachmentReferenceId;
		$person->populate();
		$person->activePhoto = (int)$attachment->attachmentId;
		$person->persist();
		Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender();
		return;

	}

}
