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
		$encounter_id = $foreign_id; //Makes so much more sense...
	//	echo "encounter id = $encounter_id <br>\n";
	//	echo "codelookup id = $parent_id <br>\n";
		//if($parent_id == 0)
		//	$parent_id = $this->parent_id;

		// The foreign id is irrelevant, it is the parent id that should drive out this process.
		// I need to know where this is called to send it the right data.	
		$code_data =& ORDataObject::factory('CodingData');
		

		//Get the REAL parent_id. From the CodingData 
		

		$parent_code =& ORDataObject::factory('Code', $parent_id);
	
		
	//OldWay
		$child_codes = $code_data->getChildCodes($foreign_id, $parent_id);
	//NewWay	$parent_codes = $code_data->getParentCodes($encounter_id);
	///NewWay	foreach $parent_codes as $parent_code
	//NewWay	$child_codes[$parent_code['code_data_id']] = $code_data->getChildCodes($parent_code);
//		var_dump($child_codes);
		$code_list = $code_data->getCodeList($encounter_id);
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
		if ($parent_id > 0) {
			$this->assign_by_ref("parent_code", $parent_code);
			$this->assign("parent_id", $parent_id);
		}
		else {
			$this->assign_by_ref("parent_code", ORDataObject::factory('Code'));
		}
		$this->assign("foreign_id", $foreign_id);
		
		$this->assign_by_ref("code_data", $code_data);
	//	echo "DEBUG C_Coding: code_data <br>\n";
	//	echo $code_data->printme()."<br>\n";
		$this->assign_by_ref("child_codes", $child_codes);
	//	echo "DEBUG C_Coding: child_codes <br>\n";
	//	var_dump($child_codes); echo "<br>\n";
		$this->assign_by_ref("code_list", $code_list);
	//	echo "DEBUG C_Coding: code_list <br>\n";
	//	printf('<pre>%s</pre>', var_export($code_list , true));
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


		if (!isset($_POST['parent_codes'])) {
			$_POST['parent_codes'] = array();
		}
		// add current code in if needed
		if (intval($_POST['parent_id']) > 0) {
			$_POST['parent_codes'][$_POST['parent_id']]['code'] = $_POST['parent_id'];
			$_POST['parent_codes'][$_POST['parent_id']]['units'] = $_POST['units'];
			$_POST['parent_codes'][$_POST['parent_id']]['modifier'] = $_POST['modifier'];
		}

		foreach($_POST['parent_codes'] as $parent) {
			$thecode = $parent['code'];
			unset($code_data);
			$code_data =& ORdataObject::factory('CodingData');
			$code_data->populate_array($parent);
			$code_data->set('code_id',$thecode);
			$code_data->set('parent_id',0); // There is no parent for the parent...
			$code_data->set('foreign_id',$_POST['foreign_id']); 
	//		$code_data->clearChildCodes($_POST['foreign_id'], $parent['parent_id']);
	// This should not happen here...
			$code_data->set('fee',$feeSchedule->getFeeFromCodeId($thecode));
			$code_data->set("id", 0);
			$code_data->persist();
			$parent_id=$code_data->get('id');
			//var_dump($code_data);

			unset($child_code_data);		
			$child_code_data =& ORdataObject::factory('CodingData');

			if(isset($_POST['child_codes']) && is_array($_POST['child_codes'])){
				foreach($_POST['child_codes'] as $code_id){
					if(intval($code_id) == 0)
						continue;
			
							
					$child_code_data->set("id", 0);
					$child_code_data->set('code_id', $code_id);
					$child_code_data->set('parent_id', $parent_id);
					$child_code_data->set('foreign_id',$_POST['foreign_id']); 
					$child_code_data->persist();
					// TODO: Should I set the primary_code class to 2 or something?
					//var_dump($child_code_data);
				}	
			}
		}
		
		//return $this->update_action($_POST['foreign_id']);
	}
	
	function delete_claimline($parent_id) {

			
		$code_data =& ORdataObject::factory('CodingData');
		$code_data->delete_claimline($parent_id);


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
		
		$columnList = array('c.code_id', 'c.code', 'c.code_text');
		$tableList  = array('codes AS c');
		$filterList = array("(c.code LIKE '{$search_string}%' OR c.code_text LIKE '%{$search_string}%') "	);
		$groupList  = array('c.code_id');
		$orderList  = array();
		
		if ($search_type == "icd") {
			$filterList[] = 'c.code_type = 2';
		}
		elseif ($search_type == 'cpt') {
			array_push($filterList, 'c.code_type = 3', 'fsd.data > 0');
			array_push($tableList,
				'INNER JOIN fee_schedule_data AS fsd USING (code_id)',
				'JOIN fee_schedule AS fs USING (fee_schedule_id)');
			$orderList[] = 'fs.priority ASC';
		}
		
		if ($superbill > 0) {
			$filterList[] = 'sbd.superbill_id = ' . $superbill;
			$columnList[] = 'sbd.superbill_id';
			$tableList[]  = 'LEFT JOIN superbill_data AS sbd ON (sbd.code_id = c.code_id)';
		}

		$sql = sprintf('SELECT %s FROM %s WHERE %s %s %s LIMIT 30',
			implode(', ',    $columnList),
			implode(' ',     $tableList),
			implode(' AND ', $filterList),
			count($groupList) > 0 ? 
				'GROUP BY ' . implode(', ', $groupList) :
				null,
			count($orderList) > 0 ?
				'ORDER BY ' . implode(', ', $orderList) :
				null
			);
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
