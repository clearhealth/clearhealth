<?php
/*****************************************************************************
*       PdfController.php
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
 * Medications controller
 */
class PdfController extends WebVista_Controller_Action {

	public function pdfMergeAttachmentAction() {
		
		//this function is primarily used by other controllers so parameters here are not permitted from _GET and _POST do to possible injection into the xmlData arg which would be very hard to filter
		$request = $this->getRequest();
		$request->setParamSources(array());
		$attachmentReferenceId = preg_replace('/[^a-zA-Z0-9-]/','',$this->_getParam('attachmentReferenceId'));
		$xmlData = $this->_getParam('xmlData');
		
		$attachment = new Attachment();
		$attachment->attachmentReferenceId = $attachmentReferenceId;
		//'ff560b50-75d0-11de-8a39-0800200c9a66' uuid for prescription pdf
		$attachment->populateWithAttachmentReferenceId();
		$db = Zend_Registry::get('dbAdapter');
                $sql = "select data from attachmentBlobs where attachmentId = " . $attachment->attachmentId;
                $stmt = $db->query($sql);
                $row = $stmt->fetch();
                $this->view->pdfBase64 = base64_encode($row['data']);
                $stmt->closeCursor();
		$this->view->xmlData = $xmlData;
		header('Content-type: application/vnd.adobe.xfdf');
	}

	public static function toXml($data, $rootNodeName = 'data', $xml=null,$dataGroup=null)	{
	if ($xml === null) {
		$rootNodeXml = "<$rootNodeName";
		if ($dataGroup == true) {
		$rootNodeXml .= " xfa:dataNode='dataGroup'";
		}
		$rootNodeXml .= "/>";
		$xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?>" . $rootNodeXml);
	}
	// loop through the data passed in.
	foreach($data as $key => $value) {
		// no numeric keys in our xml please!
		if (is_numeric($key))	{
			// make string key...
			$key = "unknownNode_". (string) $key;
		}
		// replace anything not alpha numeric
		$key = preg_replace('/[^a-z_0-9]/i', '', $key);

		// if there is another array found recrusively call this function
		if (strpos($key,'_') === 0) {
			continue;
		}
		elseif (strtolower($key) == "patientpicture") {
			//todo: nsdr load pat picture
		}
		elseif (strtolower($key) == "estimatedencounterfee") {
			//todo: reimplement fee estimator
		}
		else {
			if (is_array($value) || is_object($value)) {
				$node = $xml->addChild($key);
				// recrusive call.
				PDFController::toXml($value, $rootNodeName, $node);
			}
			else {
				// add single node.
				if (is_resource($value)) {
					$value = "resource";
				}
				$value = htmlentities($value);
				$node = $xml->addChild($key,$value);
			}
		}
		}
		// pass back as string. or simple xml object if you want!
		$xmlstr = $xml->asXML();
		$xmlstr = preg_replace('/dataNode=/','xfa:dataNode=',$xmlstr);
		return preg_replace('/<\?.*\?>/','',$xmlstr);
	}
}
