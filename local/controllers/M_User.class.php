<?php

require_once APP_ROOT . "/local/controllers/M_Patient.class.php";

class M_User extends M_Patient {

	var $messageType = "User";

	function process_update($id = 0) {
		parent::process_update($id);
		$this->controller->person_id = $this->controller->patient_id;
		if (isset($_POST['user'])) {
			$this->process_user_update($this->controller->person_id,$_POST['user']);
		}
		if (isset($_POST['provider'])) {
			$this->process_provider_update($this->controller->person_id,$_POST['provider']);
		}
		if (isset($_POST['providerToInsurance'])) {
			$this->process_providerToInsurance_update($this->controller->person_id,$_POST['providerToInsurance']);
		}
	}

	/**
	 * Handle updating login info
	 */
	function process_user_update($person_id,$data) {
			$u =& User::fromPersonId($person_id);
			if ($u->get('id') == 0) {
				$u->set('disabled','no');
			}
			$u->set('person_id',$person_id);
			$u->populate_array($data);
			$u->persist();
			$this->controller->user_id = $u->get('id');

			$this->messages->addMessage('Login Information Updated');
	}

	function process_provider_update($person_id,$data) {
		$provider = ORDAtaObject::factory('Provider',$person_id);
		$provider->populate_array($data);
		$provider->persist();

		$this->messages->addMessage('Provider Details Updated');
	}

	function process_providerToInsurance_update($person_id,$data) {
		$id = (int)$data['provider_to_insurance_id'];

		if (!empty($data['provider_number'])) {
			$pti = ORDataObject::factory('ProviderToInsurance',$id,$person_id);
			$pti->populate_array($data);
			$pti->persist();

			$this->controller->provider_to_insurance_id = $pti->get('id');
			$this->messages->addMessage('Insurance Program Updated');
		}
	}
}
?>
