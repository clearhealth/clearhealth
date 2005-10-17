<?php
/**
 * @package	com.uversainc.freestand
 */

require_once CELINI_ROOT."/ordo/ORDataObject.class.php";

/**
 * Practice Manager
 */
class M_Practice extends Manager {

	/**
	 * Handle an update from an edit or an add
	 */
	function process_update($id =0) {

		$practice =& ORdataObject::factory('Practice',$id);
		$practice->populate_array($_POST);
		$practice->persist();
		$this->controller->practice_id = $practice->get('id');

		ORDataObject::factory_include('User');

		if ($id == 0) {
			$this->messages->addMessage('Practice Created');
		}
		else {
			$this->messages->addmessage('Practice Update');
		}


		// handle sub actions that are submitted with the main one
		if (isset($_POST['number'])) {
			$this->process_phone_update($this->controller->practice_id,$_POST['number']);
		}
		if (isset($_POST['address'])) {
			$this->process_address_update($this->controller->practice_id,$_POST['address']);
		}
	}

	/**
	 * Handle updating a phone #
	 */
	function process_phone_update($practice_id,$data) {
		if (!empty($data['number']) || !empty($data['notes'])) {
			$id = 0;
			if (isset($data['number_id']) && !isset($data['add_as_new'])) {
				$id = $data['number_id'];
			}
			else {
				unset($data['number_id']);
			}
			$number =& ORDataObject::factory('PersonNumber',$id,$practice_id);
			$number->populate_array($data);
			$number->persist();
			$this->controller->number_id = $number->get('id');

			$this->messages->addMessage('Number Updated');
		}
	}

	/**
	 * Handle updating an address
	 */
	function process_address_update($practice_id,$data) {
		$process = false;
		foreach($data as $key => $val) {
			if ($key !== 'add_as_new' && $key !== "state") {
				if (!empty($val)) {
					$process = true;
					break;
				}
			}
		}
		if ($process) {
			$id = 0;
			if (isset($data['address_id']) && !isset($data['add_as_new'])) {
				$id = $data['address_id'];
			}
			else {
				unset($data['address_id']);
			}
			$number =& ORDataObject::factory('PracticeAddress',$id,$practice_id);
			$number->populate_array($data);
			$number->persist();
			$this->controller->address_id = $number->get('id');

			$this->messages->addMessage('Address Updated');
		}
	}


	/**
	 * Setup for editing a phone number
	 */
	function process_editNumber($practice_id,$number_id) {
		$this->controller->number_id = $number_id;
	}

	/**
	 * Delete a number
	 */
	function process_deleteNumber($practice_id,$number_id) {
		$number =& ORDataObject::factory('PersonNumber',$number_id,$practice_id);
		$number->drop();
		$this->messages->addmessage('Number Deleted');
	}

	/**
	 * Setup for editing an address
	 */
	function process_editAddress($practice_id,$address_id) {
		$this->controller->address_id = $address_id;
	}

	/**
	 * Delete an address
	 */
	function process_deleteAddress($practice_id,$address_id) {
		$address =& ORDataObject::factory('PersonAddress',$address_id,$practice_id);
		$address->drop();
		$this->messages->addmessage('Address Deleted');
	}

}
?>
