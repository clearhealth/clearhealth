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
	 *
	 * @todo: we are going to want to do this bridging of type on person to group on user a lot, move this to some place more reusable
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

			// update gacl groups from type
			$person =& ORDataObject::factory('Person',$person_id);
			$t_list = $person->getTypeList();
			$types = $person->get('types');

			if (count($types) > 0) {
				$type = array_shift($types);
				if ($type > 0) {
					$group = strtolower(str_replace(' ','_',$t_list[$type]));
					$gacl_groups = $this->controller->security->sort_groups();
					$flat_groups = array();
					foreach($gacl_groups as $grp) {
						foreach($grp as $k => $v) {
							$flat_groups[$k] = $v;
						}
					}
					$u->groups = array();
					foreach($flat_groups as $id => $name) {
						$data = $this->controller->security->get_group_data($id);
						if ($data[2] == $group) {
							$gid = $data[0];
							$u->groups[$gid] = array('id'=>$data[0]);
							$u->persist();
							break;
						}
					}
				}
			}
			

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
