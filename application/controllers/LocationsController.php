<?php
/*****************************************************************************
*       LocationsController.php
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


class LocationsController extends WebVista_Controller_Action {


        public function init() {
                $this->_session = new Zend_Session_Namespace(__CLASS__);
        }

	public function indexAction() {
		$locationsTree = new LocationsTree();
                //$locationsTree->addNode(0,"Christus US");
		//Christus Muguerza (Institution)
		/*$locationsTree->addNode(1652,"Consulta Externa");
		$locationsTree->addNode(1652,"Triage");
		$locationsTree->addNode(1652,"Urgencias"); 
		$locationsTree->addNode(1652,"Preoperatorios"); 
		$locationsTree->addNode(1652,"Cirugía");
		$locationsTree->addNode(1652,"Recuperación"); 
		$locationsTree->addNode(1652,"Hospitalización"); 
		$locationsTree->addNode(1652,"Endoscopía");
		$locationsTree->addNode(1652,"UCI");
		$locationsTree->addNode(1652,"Admisión"); 
		$locationsTree->addNode(1652,"Cunas");
		$locationsTree->addNode(1652,"Imagenología");
		$locationsTree->addNode(1652,"Laboratorio");
		$locationsTree->addNode(1652,"Bioestadística");*/
		$this->render();
	} 

	public function addLocation($parentId, $locationName) {

	}

	public function listLocationsTreeXmlAction() {
		$locationsTree = new LocationsTree();
		//$locationsTree->addNode(0,"Christus US");
		$locationsTree->populate();
		$this->view->xmlHeader = '<?xml version=\'1.0\' encoding=\'iso-8859-1\'?>' . "\n";
		$this->view->xml = $locationsTree->toXml();
                header('content-type: text/xml');
		$this->render();
	}
}
