<?php
require_once CELLINI_ROOT ."/includes/Grid.class.php";
require_once APP_ROOT ."/local/includes/CodingDatasource.class.php";

class C_Coding extends Controller {
	var $foreign_id = 0;
	var $parent_id = 0;
	var $superbill = 1;
	
	function C_Coding($template_mod = "general") {
		parent::Controller();
		$this->_db = $GLOBALS['frame']['adodb']['db']; 
	}

	function update_action_edit($foreign_id = 0, $parent_id = 0) {
		if($foreign_id == 0)
			$foreign_id = $this->foreign_id;
		if($parent_id == 0)
			$parent_id = $this->parent_id;

			
		$code_data =& ORDataObject::factory('CodingData');
		$parent_code =& ORDataObject::factory('Code', $parent_id);
		
		$child_codes = $code_data->getChildCodes($foreign_id, $parent_id);
//		var_dump($child_codes);
		$code_list = $code_data->getCodeList($foreign_id);
		if(is_array($child_codes) && count($child_codes) > 0){
			foreach($child_codes as $code){
				if($code['coding_data_id'] != 0){
					$code_data->set('id', $code['coding_data_id']);
					
					$code_data->populate();
					break;
				}
			}
		}
//		var_dump($code_data);
		
		if ($this->superbill == 1) {
			$this->assign("superbill", 1);
		}
		
		$this->assign("EDIT_LINK", Cellini::link("update", true, true, $foreign_id));
		$this->assign_by_ref("parent_code", $parent_code);
		$this->assign("foreign_id", $foreign_id);
		$this->assign("parent_id", $parent_id);
		$this->assign_by_ref("code_data", $code_data);
		$this->assign_by_ref("child_codes", $child_codes);
		$this->assign_by_ref("code_list", $code_list);
		return $this->fetch(Cellini::getTemplatePath("/coding/" . $this->template_mod . "_update.html"));	
	}
	
	function update_action_process(){
		if($_POST['process'] != "true")
			return;

//		var_dump($_POST);
		
		$this->foreign_id = $_POST['foreign_id'];
		$this->parent_id = $_POST['parent_id'];
		if (isset($_POST['superbill']))  {
			$this->superbill = $_POST['superbill'];
		}
		else {
			$this->super = "";
		}	
		

		$encounter =& ORDataObject::factory('Encounter',$this->foreign_id);

		// get the patients primary insured relationship
		ORDataobject::factory_include('InsuredRelationship');
		$irs =& InsuredRelationship::fromPersonId($encounter->get('patient_id'));
		if (isset($irs[0])) {
			$ir =& $irs[0];
			// get the fee schedule from that insurance_program_id

			$ip =& ORDataObject::factory('InsuranceProgram',$ir->get('insurance_program_id'));
			$feeSchedule =& ORDataObject::factory('FeeSchedule',$ip->get('fee_schedule_id')); 
		}
		else {
			// patient has no payers just grab the default fee schedule
			ORdataObject::Factory_include('FeeSchedule');
			$feeSchedule =& FeeSchedule::defaultFeeSchedule();
		}



					
		$code_data =& ORdataObject::factory('CodingData');
		$code_data->populate_array($_POST);
		$code_data->clearChildCodes($_POST['foreign_id'], $_POST['parent_id']);
		$code_data->set('fee',$feeSchedule->getFeeFromCodeId($_POST['parent_id']));
		
		if(isset($_POST['child_codes']) && is_array($_POST['child_codes'])){
			foreach($_POST['child_codes'] as $code_id){
				if(intval($code_id) == 0)
					continue;
					
				$code_data->set("id", 0);
				$code_data->set('code_id', $code_id);
				$code_data->persist();
				//var_dump($code_data);
			}	
		}
		
		//return $this->update_action($_POST['foreign_id']);
	}
	
	function update_dg_action($superbill_id) {
		$icd =& new IcdCodingDatasource();
		$icd->reset();
		$renderer_icd = new Grid_Renderer_JS();
		$renderer_icd->id = "gicd";
		$gicd =& new cGrid($icd,$renderer_icd);
		$this->assign_by_ref('icd',$gicd);

		$cpt =& new CptCodingDatasource();
		$cpt->reset();
		$renderer_cpt = new Grid_Renderer_JS();
		$renderer_cpt->id = "gcpt";
		$gcpt =& new cGrid($cpt,$renderer_cpt);
		$this->assign_by_ref('cpt',$gcpt);

		return $this->fetch(Cellini::getTemplatePath("/coding/" . $this->template_mod . "_update_dg.html"));	
	}
	
	/**
	* Function that will take a search string, parse it out and return all patients from the db matching.
	* @param string $search_string - String from html form giving us our search parameters
	*/
	function find_action_process() {
		if ($_POST['process'] != "true")
			return;
			
		$search_string = $_POST['searchstring'];
		$search_string = mysql_real_escape_string($search_string);
		$search_type = $_POST['searchtype'];
		$superbill = intval($_POST['superbill']);
		
		$where = "WHERE ";
		if($search_type == "icd"){
			$where .= " c.code_type = 2 AND ";
		}elseif($search_type == "cpt"){
			$where .= " c.code_type = 3 AND ";			
		}
		
		if($superbill > 0){
			$where .= " sbd.superbill_id = $superbill AND ";
		}
		
		$where .= " (c.code LIKE '$search_string%' OR c.code_text LIKE '%$search_string%') ";
		$sql ="";	
		if ($superbill  > 0 ) {
			$sql = "SELECT c.code_id, c.code, c.code_text, sbd.superbill_id FROM codes AS c LEFT JOIN superbill_data AS sbd ON sbd.code_id = c.code_id ";
		}
		else {
			$sql = "SELECT c.code_id, c.code, c.code_text  FROM codes AS c ";
		}

		
		//$sql = "SELECT c.code_id, c.code, c.code_text, sbd.superbill_id FROM codes AS c"
		//." LEFT JOIN superbill_data AS sbd ON sbd.code_id = c.code_id ";
	       
		if ($search_type == "cpt") {
			$sql .= "inner join fee_schedule_data fsd using (code_id) "; 
			$where .= " AND fsd.data > 0";
		}
		$sql .= " $where limit 30";
		
		//print($sql);
		$result_array = $this->_db->GetAll($sql);
		
		for($i=0; $i<count($result_array); $i++){
			$result_array[$i]['string'] = $result_array[$i]['code'] . " : " . $result_array[$i]['code_text']; 	
			$result_array[$i]['id'] = $result_array[$i]['code_id'];
		}
		$this->assign('search_string',$search_string);
		$this->assign('search_type',$search_type);
		$this->assign('superbill',$superbill);
		$this->assign('result_set', $result_array);
		// we're done
		$_POST['process'] = "";
	}

	function icd_search($search_string,$superbill = 0) {
		$_POST['process'] = true;
		$_POST['searchstring'] = $search_string;
		$_POST['searchtype'] = 'icd';
		$_POST['superbill'] = $superbill;
 		
		$this->find_action_process();
		
		if (is_array($this->_tpl_vars['result_set'])) 
			return $this->_tpl_vars['result_set'];
			
		return null;
	}
	
	function cpt_search($search_string,$superbill = 0) {
		$_POST['process'] = true;
		$_POST['searchstring'] = $search_string;
		$_POST['searchtype'] = 'cpt';
		$_POST['superbill'] = $superbill;
		
		$this->find_action_process();
		
		if (is_array($this->_tpl_vars['result_set'])) 
			return $this->_tpl_vars['result_set'];
			
		return null;
	}
	
}
?>
