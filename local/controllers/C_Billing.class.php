<?php

require_once CELLINI_ROOT."/controllers/Controller.class.php";

class C_Billing extends Controller {

	var $template_mod;

	function C_Default ($template_mod = "general") {
		parent::Controller();
		$this->template_mod = $template_mod;
	}

	function default_action() {
		//return $this->fetch($GLOBALS['frame']['config']['template_dir'] ."/default/" . $this->template_mod . "_default.html");
		header('Location: '.$GLOBALS['config']['translate']['freeb2'].Cellini::link('list','Claim','freeb2',false,false,false));
		die();
	}
	
	function sendclaim_action($encounter_id) {
		$encounter =& ORDataObject::factory('Encounter',$encounter_id);
		$patient = & ORDataObject::factory('Patient',$encounter->get("patient_id"));
		$program_data_source = $patient->insuredRelationShipList();
		$program_array = $program_data_source->toArray("program","insurance_program_id");
		$this->assign_by_ref("program_array",array_flip($program_array));
		
		$this->assign("payment_type_array",array_flip($encounter->_load_enum("payment_type",false)));
		$this->assign("claim_type_array",array_flip($encounter->_load_enum("claim_type",false)));
		//$this->assign("insurance_programs",);
		return $this->fetch($GLOBALS['frame']['config']['template_dir'] ."/billing/" . $this->template_mod . "_sendclaim.html");
	}


}

?>
