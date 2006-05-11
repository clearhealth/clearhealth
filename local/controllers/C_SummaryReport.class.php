<?php
/**
 * Patient Summary Report
 */
class C_SummaryReport extends Controller {

	function actionPatient_view($options = false) {
		$patient_id = $this->get('patient_id','c_patient');
		if (!$patient_id) {
			return "A Patient must be selected before running the patient summary report";
		}

		if ($options) {
			$this->summary_report_action_process(true);
		}
		

		// register data for selecting sections
		$sections = array();

		// demographic sections are static
		$sections['Demographics'] = array(
			array('display'=>'Basic Demographics','id'=>'bd','selected'=>true),
			array('display'=>'Phone Numbers','id'=>'pn','selected'=>true),
			array('display'=>'Addresses','id'=>'a','selected'=>true),
			array('display'=>'Payers','id'=>'p','selected'=>true),
			array('display'=>'Related People','id'=>'rp','selected'=>true),
			array('display'=>'Name History','id'=>'nh','selected'=>true),
			array('display'=>'Statistics','id'=>'s','selected'=>true),
			array('display'=>'Notes','id'=>'n','selected'=>true),
		);

		$fd =& ORDataObject::factory('FormData');

		// encounter information
		$sections['Encounters'] = array();
		$encounter =& ORDataObject::factory('Encounter');
		$eds = $encounter->encounterList($patient_id);
		for($eds->rewind(); $eds->valid(); $eds->next()) {
			$row = $eds->get();
			$sections['Encounters'][$row['encounter_id']] = 
				array('display' => "$row[encounter_reason] on $row[date_of_treatment]",'id'=>$row['encounter_id']);

			$fds =& $fd->dataListByExternalId($row['encounter_id']);

			for($fds->rewind(); $fds->valid(); $fds->next()) {
				$r = $fds->get();
				$sections['Encounters'][$row['encounter_id']]['Forms'][] = 
					array('id'=>$r['form_data_id'],'display'=>"$r[name] completed on $r[last_edit]");
			}
		}


		// patient forms
		$sections['Forms'] = array();
		$fds =& $fd->dataListByExternalId($patient_id);

		for($fds->rewind(); $fds->valid(); $fds->next()) {
			$row = $fds->get();
			$sections['Forms'][] = array('date'=>$row['last_edit'],'name'=>$row['name'],'id'=>$row['form_data_id'],'display'=>"$row[name] completed on $row[last_edit]");
		}

		$this->assign('sections',$sections);


		$patient =& ORDataObject::factory('Patient',$patient_id);
		$this->assign_by_ref('patient',$patient);

		return $this->view->render('summary_report.html');
	}

	function processPatient_view($options = false) {
		$data = array();

		if ($options == false) {
			$this->set('sr_options',$_POST);
		}
		else {
			$_POST = $this->get('sr_options');
		}

		foreach($_POST['sections'] as $section => $d) {
			$data[$section] = array();

			switch($section) {
				case 'Demographics':
					$data[$section] = $this->_summaryReportDemo($d);
					break;
				case 'Encounters':
					$data[$section] = $this->_summaryReportEncounters($d);
					break;
				case 'Forms':
					$data[$section] = $this->_summaryReportForms($d);
					break;
			}



		}
		$this->assign('data',$data);
		if (!$options) {
			$this->assign('PRINT_ACTION',Celini::link('summary_report','patient','util').'options=current');
		}
	}	

	function _summaryReportEncounters($d) {
		$ret = array();
		foreach($d as $encounter_id => $data) {
			$e =& ORDataObject::factory('Encounter',$encounter_id);

			$ret[$encounter_id]['_title'] = $e->get('encounter_reason_print') . ' on '.$e->get('date_of_treatment');
			$ret[$encounter_id]['Status'] = $e->get('status');
			$ret[$encounter_id]['Facility'] = $e->get('facility_name');
			$ret[$encounter_id]['Treating Provider'] = $e->get('treating_person_print');
			$ret[$encounter_id]['Date of Treatment'] = $e->get('date_of_treatment');
			$ret[$encounter_id]['Reason'] = $e->get('encounter_reason_print');
			$ret[$encounter_id]['Appointment'] = $e->get('appointment_print');

			$cd =& ORDataObject::factory('CodingData');
			$claims = $cd->getCodeList($encounter_id);
			if (count($claims) > 0) {
				$ret[$encounter_id]['Claims']['Claims'] = array();
				foreach($claims as $claim) {
					$ret[$encounter_id]['Claims']['Claims'][] = '<b>'.$claim['description'].', '
						.$cd->lookupModifier($claim['modifier']) .' '.$claim['units'].'</b>';
					$childs = $cd->getChildCodes($claim['coding_data_id']);
					foreach($childs as $child) {
						$ret[$encounter_id]['Claims']['Claims'][] = $child['description'];
					}
				}
			}

			$ed =& ORDataObject::factory('EncounterDate');
			$list = $ed->encounterDateList($encounter_id);

			$ret[$encounter_id]['Claims']['Dates'] = array();
			for($list->rewind(); $list->valid(); $list->next()) {
				$row = $list->get();
				$ret[$encounter_id]['Claims']['Dates'][$row['date']] = $row['date_type'];
			}

			$ed =& ORDataObject::factory('EncounterPerson');
			$list = $ed->encounterPersonList($encounter_id);

			$ret[$encounter_id]['Claims']['People'] = array();
			for($list->rewind(); $list->valid(); $list->next()) {
				$row = $list->get();
				$ret[$encounter_id]['Claims']['People'][$row['person']] = $row['person_type'];
			}


			$ed =& ORDataObject::factory('EncounterValue');
			$list = $ed->encounterValueList($encounter_id);

			$ret[$encounter_id]['Claims']['Values'] = array();
			for($list->rewind(); $list->valid(); $list->next()) {
				$row = $list->get();
				$ret[$encounter_id]['Claims']['Values'][$row['value']] = $row['value_type'];
			}



			$fd =& ORDataObject::factory('FormData');
			$list =& $fd->dataListByExternalId($encounter_id);

			$ret[$encounter_id]['Claims']['Forms'] = array();
			for($list->rewind(); $list->valid(); $list->next()) {
				$row = $list->get();
				$title = $row['name'].' completed on '.$row['last_edit'];
				$ret[$encounter_id]['Claims'][$title] = array();
				$fd->setup($row['form_data_id']);
				$data = $fd->allData();
				foreach($data as $key => $val) {
					$key = ucfirst(str_replace('_',' ',$key));
					$ret[$encounter_id]['Claims'][$title][$key] = $val['value'];
				}
			}

		}
		return $ret;
	}

	function _summaryReportForms($d) {
		$ret = array();
		foreach(array_keys($d) as $form_id) {
			$fd =& ORDataObject::factory('FormData',$form_id);

			$title = $fd->get('form_name').' completed on '.$fd->get('last_edit');
			$ret[$title] = array();

			$data = $fd->allData();
			foreach($data as $key => $val) {
				$key = ucfirst(str_replace('_',' ',$key));
				$ret[$title][$key] = $val['value'];
			}
		}
		return $ret;
	}

	function _personDemo($patient_id) {
		$patient =& ORDataObject::factory('Patient',$patient_id);
		$ret['Last Name'] = $patient->get('last_name');
		$ret['First Name'] = $patient->get('first_name');
		$ret['Record Number'] = $patient->get('record_number');

		$it = $patient->get('print_identifier_type');
		if (empty($it)) {
			$it = 'SSN';
		}
		$ret[$it] = $patient->get('identifier');
		$ret['Date of Birth'] = $patient->get('date_of_birth');
		$ret['Gender'] = $patient->get('print_gender');
		$ret['Marital Status'] = $patient->get('print_marital_status');
		$ret['Default Provider'] = $patient->get('print_default_provider');

		foreach($ret as $key => $val) {
			if (empty($val) || ($val === '00/00/0000')) {
				unset($ret[$key]);
			}
		}
		return $ret;
	}

	function _summaryReportDemo($sections) {
		$patient_id = $this->get('patient_id');
		$ret = array();
		foreach($sections as $section => $value) {
			if ($value == 1) {
				switch($section) {
				case 'bd':
					$ret['Basic Demographics'] = $this->_personDemo($patient_id);
					break;
				case 'pn':
					$number =& ORDataObject::factory('PersonNumber');
					$list = $number->numberList($patient_id);
					$ret['Phone Numbers']['table'] = array('Type','Number','Notes','Do Not Call?');

					foreach($list as $val) {
						$row = array();
						$row['Type'] = $val['number_type'];
						$row['Number'] = $val['number'];
						$row['Notes'] = $val['notes'];
						$row['Do Not Call?'] = $val['active'] ? 'no':'yes';
						$ret['Phone Numbers'][] = $row;
					}
					break;
				case 'a':
					$ret['Addresses']['table'] = array('Type','Name','Address','City','State','Zip','Notes');
					$address =& ORDataObject::Factory('PersonAddress');
					$list = $address->addressList($patient_id);
					if (empty($list)) $list = array();

					foreach($list as $val) {
						$row = array();
						$row[] = $val['type'];
						$row[] = $val['name'];
						$row[] = $val['line1']."<br>".$val['line2'];
						$row[] = $val['city'];
						$row[] = $val['state'];
						$row[] = $val['postal_code'];
						$row[] = nl2br($val['notes']);
						$ret['Addresses'][] = $row;
					}
					break;
				case 'p':
					$ret['Payers'] = array();

					ORDataObject::Factory_Include('InsuredRelationship');
					$payers =& InsuredRelationship::fromPersonId($patient_id);

					foreach($payers as $payer) {
						$ret['Payers'][$payer->get('id')] = array(
							'Company' => $payer->get('insurance_company_name'),
							'Program' => $payer->get('program_name'),
							'Group Name' => $payer->get('group_name'),
							'Group Number' => $payer->get('group_number'),
							'Co-Pay' => $payer->get('copay'),
							'Effective Date Range' => $payer->get('effective_start') .' to '.$payer->get('effective_end'),
							'Active' => $payer->get('active') ? 'yes':'no',
							'Subscriber Relationship' => $payer->get('subscriber_to_patient_relationship_name')
							);
						if ($payer->get('subscriber_to_patient_relationship_name') !== 'self')  {
							$ret['Payers'][$payer->get('id')]['Subscriber'] = $this->_personDemo($payer->get('subscriber_id'));

							$address =& ORDataObject::Factory('PersonAddress');
							$list = $address->addressList($payer->get('subscriber_id'));

							if (is_array($list)) {
							$ret['Payers'][$payer->get('id')]['Subscriber']['Address'] = $address->lookup(array_shift(array_keys($list)));
							}

						}
					}

					break;
				case 'rp':
					$ret['Related People'] = array();
					$ret['Related People']['table'] = array('Name','Relation Of','Name');
					//$pp =& ORDataObject::factory('PersonPerson');
					//$list =& $pp->relatedList($patient_id);

					$GLOBALS['loader']->requireOnce("datasources/Person_RelatedList_DS.class.php");
					$list = new Person_RelatedList_DS($patient_id); 

					for($list->rewind(); $list->valid(); $list->next()) {
						$row = $list->get();
						unset($row['person_person_id']);
						$ret['Related People'][] = $row;
					}
					break;
				case 'nh':
					$ret['Name History'] = array();
					$ret['Name History']['table'] = array('First Name','Last Name','Middle Initial','Date Changed');
					$nh =& ORDataObject::factory('NameHistory');
					$list =& $nh->nameHistoryList($patient_id);
					for($list->rewind(); $list->valid(); $list->next()) {
						$row = $list->get();
						$ret['Name History'][] = $row;
					}
					
					break;
				case 's':
					$ret['Secondary Identifiers'] = array();
					$ret['Secondary Identifiers']['table'] = array('Identifier','Type');

					$i =& ORDataObject::Factory('Identifier');
					$list =& $i->identifierList($patient_id);
					for($list->rewind(); $list->valid(); $list->next()) {
						$row = $list->get();
						unset($row['identifier_id']);
						$ret['Secondary Identifiers'][] = $row;
					}
					break;
				case 'n':
					$ret['Patient Notes'] = array();

					$pn =& ORDataObject::factory('PatientNote');
					$list = $pn->listNotes($patient_id);
					unset($list->filter['note']);
					unset($list->template['deprecated']);
					for($list->rewind(); $list->valid(); $list->next()) {
						$row = $list->get();

						$note = "<table border=1 cellpadding=2 cellspacing=0>
								<tr><td colspan=2 style='padding-left: 7px'>$row[priority]</td></tr>
								<tr><td>Date: $row[note_date]</td><td>Deprecated: $row[deprecated]</td></tr>
								<tr><td colspan=2>".nl2br($row['note'])."</td></tr>
								<tr><td colspan=2>Posted by: $row[username]</td></tr></table>";

						$ret['Patient Notes'][] = $note;
					}
				}
			}
		}
		return $ret;
	}
}
?>
