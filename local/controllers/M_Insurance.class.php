<?php
/**
 * @package	com.uversainc.clearhealth
 */

require_once CELLINI_ROOT."/controllers/Manager.class.php";

/**
 * Insurance Manager
 */
class M_Insurance extends Manager {

	/**
	 * Handle an update from an edit or an add
	 */
	function process_update($id =0) {

		$branch =& ORdataObject::factory('Company',$id);
		$branch->populate_array($_POST);
		if ($id == 0) {
			$branch->set_types(array(1)); // set the type to branch
		}
		$branch->persist();

		$this->controller->branch_id = $branch->get('id');

		if ($id == 0) {
			$this->messages->addMessage('Company Created');
		}
		else {
			$this->messages->addmessage('Company Update');
		}


		// handle sub actions that are submitted with the main one
		if (isset($_POST['number'])) {
			$this->process_phone_update($this->controller->branch_id,$_POST['number']);
		}
		if (isset($_POST['address'])) {
			$this->process_address_update($this->controller->branch_id,$_POST['address']);
		}
	}

	/**
	 * Handle updating a phone #
	 */
	function process_phone_update($branch_id,$data) {
		if (!empty($data['number']) || !empty($data['notes'])) {
			$id = 0;
			if (isset($data['number_id']) && !isset($data['add_as_new'])) {
				$id = $data['number_id'];
			}
			else {
				unset($data['number_id']);
			}
			$number =& ORDataObject::factory('CompanyNumber',$id,$branch_id);
			$number->populate_array($data);
			$number->persist();
			$this->controller->number_id = $number->get('id');

			$this->messages->addMessage('Number Updated');
		}
	}

	/**
	 * Handle updating an address
	 */
	function process_address_update($branch_id,$data) {
		$process = false;
		foreach($data as $key => $val) {
			if ($key !== 'add_as_new') {
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
			$number =& ORDataObject::factory('CompanyAddress',$id,$branch_id);
			$number->populate_array($data);
			$number->persist();
			$this->controller->address_id = $number->get('id');

			$this->messages->addMessage('Address Updated');
		}
	}


	/**
	 * Setup for editing a phone number
	 */
	function process_editNumber($branch_id,$number_id) {
		$this->controller->number_id = $number_id;
	}

	/**
	 * Delete a number
	 */
	function process_deleteNumber($branch_id,$number_id) {
		$number =& ORDataObject::factory('CompanyNumber',$number_id,$branch_id);
		$number->drop();
		$this->messages->addmessage('Number Deleted');
	}

	/**
	 * Setup for editing an address
	 */
	function process_editAddress($branch_id,$address_id) {
		$this->controller->address_id = $address_id;
	}

	/**
	 * Delete an address
	 */
	function process_deleteAddress($branch_id,$address_id) {
		$address =& ORDataObject::factory('CompanyAddress',$address_id,$branch_id);
		$address->drop();
		$this->messages->addmessage('Address Deleted');
	}

	/**
	 * Delete a person relation
	 */
	function process_deleteRelation($branch_id,$person_id) {
		$emp =& ORDataObject::factory('Person',$person_id);
		$emp->dropRelation($branch_id,0);
		$this->messages->addmessage('Employee Removed from Company');
	}
}
?>
