<?php
/*****************************************************************************
*       EnumerationsController.php
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


class EnumerationsController extends WebVista_Controller_Action {


        public function init() {
                $this->_session = new Zend_Session_Namespace(__CLASS__);
        }

	public function indexAction() {
		$enumAr = Enumeration::getEnumArray('Gender','key');
		var_dump($enumAr);
		exit;
		$this->render();
	} 

	public function addEnumeration($parentId, $enumerationName) {

	}

	public function listEnumerationsTreeXmlAction() {
		$enumerationsTree = new EnumerationsTree();
		//$enumerationsTree->addNode(0,"Demographics");
		//$enumerationsTree->addNode(643,"Gender");
		//$enumerationsTree->addNode(645,"Male");
		//$enumerationsTree->addNode(645,"Female");
		//$enumerationsTree->addNode(645,"Other");
		//$enumerationsTree->addNode(643,"Marital Status");
		//$enumerationsTree->addNode(646,"Single");
		//$enumerationsTree->addNode(646,"Married");
		//$enumerationsTree->addNode(646,"Divorced");
		//$enumerationsTree->addNode(643,"Confidentiality");
		//$enumerationsTree->addNode(647,"Default");
		//$enumerationsTree->addNode(0,"Vitals");
		//$enumerationsTree->addNode(644,"Units");
		//$enumerationsTree->addNode(648,"Height");
		//$enumerationsTree->addNode(708,"cm");
		//$enumerationsTree->addNode(648,"Pain");
		//$enumerationsTree->addNode(709,"0 - No Pain");
		//$enumerationsTree->addNode(709,"1 - Slightly uncomfortable");
		//$enumerationsTree->addNode(709,"2");
		//$enumerationsTree->addNode(709,"3");
		//$enumerationsTree->addNode(709,"4");
		//$enumerationsTree->addNode(709,"5");
		//$enumerationsTree->addNode(709,"6");
		//$enumerationsTree->addNode(709,"7");
		//$enumerationsTree->addNode(709,"8");
		//$enumerationsTree->addNode(709,"9");
		//$enumerationsTree->addNode(709,"10 - Worst imaginable");
		//$enumerationsTree->addNode(709,"99 - Unable to respond");*/
		//$enumerationsTree->addNode(648,"Temperature");
		//$enumerationsTree->addNode(710,"C");
		//$enumerationsTree->addNode(648,"Weight");
		//$enumerationsTree->addNode(711,"Kg");
		//$enumerationsTree->deleteNode(704);
		//$enumerationsTree->addNode(0,"Routing");
		//$enumerationsTree->addNode(727,"No Station");
		//$enumerationsTree->addNode(727,"Vitals");
		//$enumerationsTree->addNode(727,"DIS");
		$enumerationsTree->populate();
		echo $enumerationsTree->toXml();
                header('content-type: text/xml');
		exit;
		//$this->render();
	}
}
