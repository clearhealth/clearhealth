<?php
/*****************************************************************************
*       FacilitiesController.php
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
 * Facilities controller
 */
class FacilitiesController extends WebVista_Controller_Action {

	public function editPracticeAction() {
		$id = (int)$this->_getParam('id');
		$enumerationId = (int)$this->_getParam('enumerationId');
		$orm = new Practice();
		if ($id > 0) {
			$orm->practiceId = $id;
			$orm->populate();
		}
		$form = new WebVista_Form(array('name'=>'edit-practice'));
		$form->setAction(Zend_Registry::get('baseUrl').'facilities.raw/process-edit-practice');
		$form->loadORM($orm,'Practice');
		$form->setWindow('windowEditPracticeId');
		$this->view->form = $form;
		$this->view->enumerationId = $enumerationId;
		$this->view->statesList = Address::getStatesList();
		$this->render('edit-practice');
	}

	function processEditPracticeAction() {
		$enumerationId = (int)$this->_getParam('enumerationId');
		$params = $this->_getParam('practice');
		$id = (int)$params['id'];
		$params['id'] = $id;

		$orm = $this->_populateAndPersist('Practice',$id,$params,$enumerationId);

		$msg = __('Record Saved for Practice: ') . ucfirst($params['name']);
		$data = array();
		$data['msg'] = $msg;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function editBuildingAction() {
		$id = (int)$this->_getParam('id');
		$enumerationId = (int)$this->_getParam('enumerationId');
		$enumerationsClosure = new EnumerationsClosure();
		$parentId = $enumerationsClosure->getParentById($enumerationId);
		$enumeration = new Enumeration();
		$enumeration->enumerationId = $parentId;
		$enumeration->populate();
		$orm = new Building();
		if ($id > 0) {
			$orm->buildingId = $id;
			$orm->populate();
		}
		$orm->practiceId = $enumeration->ormId;
		$form = new WebVista_Form(array('name'=>'edit-building'));
		$form->setAction(Zend_Registry::get('baseUrl').'facilities.raw/process-edit-building');
		$form->loadORM($orm,'Building');
		$form->setWindow('windowEditBuildingId');
		$this->view->form = $form;
		$this->view->enumerationId = $enumerationId;
		$this->view->statesList = Address::getStatesList();
		$enumeration = new Enumeration();
		$enumeration->guid = '22fb4e1e-a37a-4e7a-9dae-8e220ba939e8';
		$enumeration->populateByGuid();
		$enumerationClosure = new EnumerationClosure();
		$descendants = $enumerationClosure->getAllDescendants($enumeration->enumerationId,1,true);
		$facilityCodes = array(''=>'');
		foreach ($descendants as $descendant) {
			$facilityCodes[$descendant->key] = $descendant->key.' : '.$descendant->name;
		}
		$this->view->facilityCodes = $facilityCodes;
		$this->render('edit-building');
	}

	function processEditBuildingAction() {
		$enumerationId = (int)$this->_getParam('enumerationId');
		$params = $this->_getParam('building');
		$id = (int)$params['id'];
		$params['id'] = $id;

		$orm = $this->_populateAndPersist('Building',$id,$params,$enumerationId);

		$msg = __('Record Saved for Building: ') . ucfirst($params['name']);
		$data = array();
		$data['msg'] = $msg;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	public function editRoomAction() {
		$id = (int)$this->_getParam('id');
		$enumerationId = (int)$this->_getParam('enumerationId');
		$enumerationsClosure = new EnumerationsClosure();
		$parentId = $enumerationsClosure->getParentById($enumerationId);
		$enumeration = new Enumeration();
		$enumeration->enumerationId = $parentId;
		$enumeration->populate();
		$orm = new Room();
		if ($id > 0) {
			$orm->roomId = $id;
			$orm->populate();
		}
		$orm->buildingId = $enumeration->ormId;
		$form = new WebVista_Form(array('name'=>'edit-room'));
		$form->setAction(Zend_Registry::get('baseUrl').'facilities.raw/process-edit-room');
		$form->loadORM($orm,'Room');
		$form->setWindow('windowEditRoomId');
		$this->view->form = $form;

		$routingStations = Enumeration::getEnumArray(Routing::ENUM_PARENT_NAME);
		$routingStations = array_merge(array('' => ''),$routingStations);
		$this->view->colors = Room::getColorList();
		$this->view->routingStations = $routingStations;
		$this->view->enumerationId = $enumerationId;
		$this->render('edit-room');
	}

	function processEditRoomAction() {
		$enumerationId = (int)$this->_getParam('enumerationId');
		$params = $this->_getParam('room');
		$id = (int)$params['id'];
		$params['id'] = $id;

		$orm = $this->_populateAndPersist('Room',$id,$params,$enumerationId);

		$msg = __('Record Saved for Room: ') . ucfirst($params['name']);
		$data = array();
		$data['msg'] = $msg;
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

	private function _populateAndPersist($class,$id,$data,$enumerationId) {
		// this method assumes that is being called in this controller only and that $class is valid and exists
		$orm = new $class();
		if ($id > 0) {
			$orm->id = $id;
			$orm->populate();
		}
		$orm->populateWithArray($data);
		$orm->persist();

		if (!$id > 0 && $enumerationId > 0) {
			$enumeration = new Enumeration();
			$enumeration->enumerationId = $enumerationId;
			$enumeration->populate();
			$enumeration->ormId = $orm->id;
			$enumeration->persist();
		}
		return $orm;
	}

	public function processImport2xFacilitiesAction() {
		$import = (int)$this->_getParam('import');
		$parentId = (int)$this->_getParam('parentId');
		$data = false;
		if ($import > 0 && $parentId > 0) {
			$db = Zend_Registry::get('dbAdapter');
			$enumClosure = new EnumerationsClosure();
			$sql = 'SELECT * FROM practices';
			$practices = $db->fetchAll($sql);
			$ctr = 1;
			foreach ($practices as $practice) {
				$p = new Practice();
				$p->name = $practice['name'];
				$p->website = $practice['website'];
				$p->identifier = $practice['identifier'];
				//$p->persist();

				// add addresses (primary = 4, secondary = 5)
				$sql = 'SELECT * FROM practice_address AS pa INNER JOIN address a ON a.address_id=pa.address_id WHERE pa.practice_id='.$practice['id'].' AND (pa.address_type=4 OR pa.address_type=5)';
				$addresses = $db->fetchAll($sql);
				foreach ($addresses as $address) {
					if ($address['address_type'] == 4) {
						$addr = $p->primaryAddress;
					}
					else if ($address['address_type'] == 5) {
						$addr = $p->secondaryAddress;
					}
					else {
						continue;
					}
					$addr->populateWithArray($address);
					$addr->addressId = 0;
					$addr->type = $address['address_type'];
					$addr->active = 1;
					$addr->practiceId = $practice['id'];
					//$addr->persist();
				}

				// add phones (main = 3, secondary = 1, fax = 5)
				$sql = 'SELECT * FROM practice_number AS pn INNER JOIN number n ON n.number_id=pn.number_id WHERE pn.practice_id='.$practice['id'].' AND (n.number_type=1 OR n.number_type=3 OR n.number_type=5)';
				$phones = $db->fetchAll($sql);
				foreach ($phones as $phone) {
					$pn = new PhoneNumber();
					$pn->populateWithArray($phone);
					$pn->type = $phone['number_type'];
					$pn->numberId = 0;
					//$pn->persist();
				}

				//$practiceId = $p->practiceId;
				$practiceId = $practice['id'];
				$params = array();
				$params['name'] = $practice['name'];
				$params['key'] = $ctr++;
				$params['active'] = '1';
				$params['ormClass'] = 'Practice';
				$params['ormId'] = $practiceId;
				$params['ormEditMethod'] = 'ormEditMethod';
				$buildingParentId = $enumClosure->insertEnumeration($params,$parentId);

				$sql = 'SELECT * FROM buildings WHERE practice_id='.$practice['id'];
				$buildings = $db->fetchAll($sql);
				foreach ($buildings as $building) {
					$b = new Building();
					$b->description = $building['description'];
					$b->name = $building['name'];
					$b->practiceId = $p->practiceId;
					$b->identifier = $building['identifier'];
					$b->facilityCodeId = $building['facility_code_id'];
					//$b->phoneNumber = $building[''];
					//$b->persist();

					//$buildingId = $b->buildingId;
					$buildingId = $building['id'];
					$params = array();
					$params['name'] = $building['name'];
					$params['key'] = $ctr++;
					$params['active'] = '1';
					$params['ormClass'] = 'Building';
					$params['ormId'] = $buildingId;
					$params['ormEditMethod'] = 'ormEditMethod';
					$roomParentId = $enumClosure->insertEnumeration($params,$buildingParentId);

					// add phone number

					// add address
					$sql = 'SELECT * FROM building_address AS ba INNER JOIN address a ON a.address_id=ba.address_id WHERE ba.building_id='.$building['id'];
					$addresses = $db->fetchAll($sql);
					foreach ($addresses as $address) {
						$addr = new Address();
						$addr->populateWithArray($address);
						$addr->addressId = 0;
						$addr->type = $address['address_type'];
						$addr->active = 1;
						$addr->practiceId = $practice['id'];
						//$addr->persist();
					}

					$sql = 'SELECT * FROM rooms WHERE building_id='.$building['id'];
					$rooms = $db->fetchAll($sql);
					foreach ($rooms as $room) {
						$r = new Room();
						$r->populateWithArray($room);
						$r->roomId = 0;
						//$r->persist();

						//$roomId = $r->roomId;
						$roomId = $room['id'];
						$params = array();
						$params['name'] = $room['name'];
						$params['key'] = $ctr++;
						$params['active'] = '1';
						$params['ormClass'] = 'Room';
						$params['ormId'] = $roomId;
						$params['ormEditMethod'] = 'ormEditMethod';
						$enumerationId = $enumClosure->insertEnumeration($params,$roomParentId);
					}
				}
			}
			$data = true;
		}
		$json = Zend_Controller_Action_HelperBroker::getStaticHelper('json');
		$json->suppressExit = true;
		$json->direct($data);
	}

}

