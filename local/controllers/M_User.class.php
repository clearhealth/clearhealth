<?php

require_once APP_ROOT . "/local/controllers/M_Patient.class.php";

class M_User extends M_Patient {

	var $messageType = "User";

	function process_update($id = 0) {
		if (!$this->_continueProcessing($id)) {
			return;
		}		
		parent::process_update($id,true);
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
	 * Returns <i>TRUE</i> if {@link process_update()} should continue
	 * processing, <i>FALSE</i> otherwise.
	 *
	 * @return boolean
	 * @access private
	 */
	function _continueProcessing($id) {
		$username = $_POST['user']['username'];
		$u =& User::fromUsername($username);
		if ($username == 'admin' && $u->get('person_id') != $id) {
			$this->messages->addMessage('Admin user already tied to an existing person');
			return false;
		}
		elseif ($username != 'admin' && $u->isPopulated()) {
			$user_id = $_POST['user']['user_id'];
			if (($user_id == 0) ||
			    ($user_id > 0 && $user_id != $u->get('id'))) 
			{
				$escapedUsername = htmlspecialchars($username); 
				$this->messages->addMessage("Username \"{$escapedUsername}\" already taken.");
			}
		}
		
		return true;
	}

	/**
	 * Handle updating login info
	 *
	 * @todo: we are going to want to do this bridging of type on person to group on user a lot, move this to some place more reusable, this should be moved into the persist method of a user. Note: the inject_user.php script uses a cut and paste of this code.
	 */
	function process_user_update($person_id,$data) {
			/* 
			 * If this username is 'admin', attempt to load it and tie this 
			 * new person to it
			 */
			if ($data['username'] == 'admin') {
				$u =& User::fromUsername('admin');
			}
			else {
				$u =& User::fromPersonId($person_id);
				
				// What does this do?
				if ($u->get('id') == 0) {
					$u->set('disabled','no');
				}
			}
			
			$u->set('person_id',$person_id);
			$u->populate_array($data);
			$u->persist();
			$this->controller->user_id = $u->get('id');


			// Determine the user types of this new person
			$person =& ORDataObject::factory('Person',$person_id);
			$t_list = $person->getTypeList();
			$types = $person->get('types');
			
			// update gacl groups from type
			if ($data['username'] != 'admin') {
				// Setup user groups
				$groups = array();
				if (isset($data['groups'])) {
					$groups = $data['groups'];
					unset($data['groups']);
				}
				
				// Run through all the types setting the appropriate GACL.
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
						foreach($groups as $id) {
							$u->groups[$id] = array('id'=>$id);
						}
						foreach($flat_groups as $id => $name) {
							$data = $this->controller->security->get_group_data($id);
							if ($data[2] == $group) {
								$gid = $data[0];
								$u->groups[$gid] = array('id'=>$data[0]);
								// move persist outside this loop for efficiency
								break;
							}
						}
					}
				}
				$u->persist();
			}

			if($t_list[$person->get('type')] === "Provider") {
				// create default ps schedule if no ps schedule exists

				ORDataObject::factory_include('Schedule');
				$schedules = Schedule::fromUserId($u->get('id'));

				if (count($schedules) == 0) {
					// get the default practice
					$practice_id = $u->get('DefaultPracticeId');

					// create a ps schedule for the provider
					$schedule =& ORDataObject::factory('Schedule');
					$schedule->set('user_id',$u->get('id'));
					$schedule->set('schedule_code','PS');
					$schedule->set('name',$person->get('salutation').' '.$person->get('last_name')."'s Schedule");
					$schedule->set('practice',$practice_id);
					$schedule->persist();

					// create an event for each room
					ORDataObject::factory_include('Room');
					$rooms = Room::rooms_factory();
					foreach($rooms as $room) {
						unset($e);
						$e =& ORDataObject::factory('Event');
						$e->set('title',$room->get('name'));
						$e->set('foreign_id',$schedule->get('id'));
						$e->persist();
					}
					$this->messages->addMessage('Default Schedule Created');

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

			//$this->controller->provider_to_insurance_id = $pti->get('id');
			$this->messages->addMessage('Insurance Program Updated');
		}
	}
	
	/**
	 * Placeholder for the eventual editing of provider to insurance ID # 
	 * mapping.
	 *
	 * @access protected
	 * @todo Fill in this stub
	 */
	function processEditProviderToInsurance_edit() {
		
	}
}
?>
