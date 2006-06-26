<?php
$loader->requireOnce("/includes/Grid.class.php");
$loader->requireOnce("includes/CodingDatasource.class.php");
$loader->requireOnce("includes/Datasource_array.class.php");
$loader->requireOnce("datasources/Superbill_DS.class.php");
$loader->requireOnce("includes/transaction/TransactionManager.class.php");
$loader->requireOnce('includes/LockManager.class.php');

class C_Coding extends Controller {
	var $foreign_id = 0;
	var $parent_id = 0;
	var $superbill = 1;
	
	function C_Coding($template_mod = "general") {
		parent::Controller();
		$this->_db = $GLOBALS['frame']['adodb']['db']; 

		$session =& Celini::SessionInstance();
		$this->superbill = $session->get('Superbill:id');
	}

	function _CalculateEncounterFees($encounterId) {
		$manager = new TransactionManager();
		$trans = $manager->createTransaction('EstimateClaim');
		$trans->setEncounterId($encounterId);

		$encounter =& Celini::newORDO('Encounter',$encounterId);
		$trans->setPayerId($encounter->get('current_payer'));

		$fees = $manager->processTransaction($trans);

		$ds = new Datasource_array();
		$ds->setup(array('code'=>'Code','fee'=>'Fee'),$fees);
		if ($ds->numRows() > 0 && $encounterId > 0) {
			$grid =& new cGrid($ds);
			$grid->indexCol = false;
			$this->view->assign_by_ref('feeGrid',$grid);
		}

		$practiceId = $_SESSION['defaultpractice'];

		$practiceConfig =& Celini::configInstance('Practice');
		$practiceConfig->loadPractice($_SESSION['defaultpractice']);
		$isDentist = ($practiceConfig->get('FacilityType') == 1);
		if ($isDentist) {
			$newFees = array();
			$trans = false;
			foreach ($fees as $codeFee) {
				$fsdLevel =& Celini::newORDO(
					'FeeScheduleDiscountLevel',
					array($practiceId, $codeFee),
					'ByPracticeCode');
				
				// If nothing specific is found, try wildcard lookup
				if (!$fsdLevel->isPopulated()) {
					$fsdLevel =& Celini::newORDO(
						'FeeScheduleDiscountLevel',
						array($practiceId, $codeFee),
						'ByPracticeCodeWildcard');
				}
				
				if ($fsdLevel->isPopulated()) {
					if (!$trans) {
						$trans = $manager->createTransaction('EstimateDiscountedClaimByClaimline');
						$trans->setEncounterId($encounterId);
						$trans->setPayerId($encounter->get('current_payer'));
					}
					$trans->setDiscount($fsdLevel->value('discount'), $codeFee['code']);
					$curIndex = count($newFees);
				}
			}
			
			if ($trans !== false) {
				$newFees = $manager->processTransaction($trans);
				$ds2 =& new Datasource_array();
				$ds2->setup(array('code' => 'Code', 'fee' => 'Fee'), $newFees);
				$grid2 =& new cGrid($ds2);
				$grid2->indexCol = false;
				$this->view->assign_by_ref('discountGrid', $grid2);
			}
			
		} 
		else {
			$ps =& Celini::newOrdo('PatientStatistics',$encounter->get('patient_id'));
			$familySize = $ps->get('family_size');
			$income = $ps->get('monthly_income');
			$fsdLevel =& Celini::newOrdo('FeeScheduleDiscountLevel',array($practiceId,$income,$familySize, $encounter->get('current_payer')),'ByPracticeIncomeSize');
			
			if($fsdLevel->isPopulated()) {
				$trans = $manager->createTransaction('EstimateDiscountedClaim');
				$trans->setEncounterId($encounterId);
				$trans->setPayerId($encounter->get('current_payer'));
				$trans->setDiscount($fsdLevel->value('discount'));
				$fees = $manager->processTransaction($trans);
				$ds2 = new Datasource_array();
				$ds2->setup(array('code'=>'Code','fee'=>'Fee'),$fees);
	
				$grid2 =& new cGrid($ds2);
				$grid2->indexCol = false;
				$this->view->assign_by_ref('discountGrid',$grid2);
				$this->view->assign('discountRate',$fsdLevel->value('discount'));
			}
		}
	}

	function update_action_edit($foreign_id = 0, $parent_id = 0) {
		if($foreign_id == 0)
			$foreign_id = $this->foreign_id;
		$encounter_id = $foreign_id; //Makes so much more sense...


		$ds =& new Superbill_DS($this->superbill);
		$grid =& new cGrid($ds);
		$grid->orderLinks = false;
		$this->view->assign_by_ref('sblGrid',$grid);

		$head =& Celini::HTMLHeadInstance();
		$head->addJs('scriptaculous');
		$head->addExternalCss('suggest');



		// tie in for calculating how much this encounter will be billed for when closed
		$this->_calculateEncounterFees($encounter_id);

		// The foreign id is irrelevant, it is the parent id that should drive out this process.
		// I need to know where this is called to send it the right data.	
		$code_data =& ORDataObject::factory('CodingData');
		

		//Get the REAL parent_id. From the CodingData 
		$parent_code =& ORDataObject::factory('Code', $parent_id);
	
		
		$child_codes = $foreign_id == 0 ? array() : $code_data->getChildCodes($foreign_id, $parent_id);
		$code_list = $code_data->getCodeList($encounter_id);
		$GLOBALS['currentCodeList'] = $code_list;
		if(is_array($child_codes) && count($child_codes) > 0){
			foreach($child_codes as $code){
				if($code['coding_data_id'] != 0){
					$code_data->set('id', $code['coding_data_id']);
					
					$code_data->populate();
					break;
				}
			}
		}
		
		if ($this->superbill == 1) {
			$this->assign("superbill", 1);
		}
		
		$this->assign("EDIT_LINK", Celini::link("update", true, true, $foreign_id));
		if ($parent_id > 0) {
			$this->assign_by_ref("parent_code", $parent_code);
			$this->assign("parent_id", $parent_id);
		}
		else {
			$this->assign_by_ref("parent_code", ORDataObject::factory('Code'));
		}
		$this->assign("foreign_id", $foreign_id);
		
		$this->assign_by_ref("code_data", $code_data);
		$this->assign_by_ref("child_codes", $child_codes);
		$this->assign_by_ref("code_list", $code_list);
		return $this->view->render("edit.html");
	}
	
	function update_action_process(){
		if($_POST['process'] != "true")
			return;

		// lock check
		$lockTimestamp = $this->POST->get('lockTimestamp');
		if (!empty($lockTimestamp)) {

			$changes = array();
			$ordoType = 'Coding';
			$changes['coding'] = LockManager::hasOrdoChanged($ordoType,$id,$lockTimestamp);

			$tmp = LockManager::hasOrdoChanged($ordoType,$id,$lockTimestamp);
			$changes['coding'] = array_merge($changes['coding'],$tmp);
			$this->_updateChangesSection($changes['coding'],'encounter');

			$overlappingChanges = false;
			foreach($changes as $name => $change) {
				if (count($change) > 0) {
					$overlappingChanges = true;
				}
			}
			if ($overlappingChanges) {
				$changes['_POST'] = $_POST;
				LockManager::prepareChangedAlert($changes,$this,$lockTimestamp);
				return;
			}
		}

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
			if(isset($_POST['tooth'])){
				$_POST['parent_codes'][$_POST['parent_id']]['tooth'] = $_POST['tooth'];
				$_POST['parent_codes'][$_POST['parent_id']]['toothside'] = $_POST['toothside'];
			}
		}

		$changes = array();
		$overlappingChanges = false;
		foreach($_POST['parent_codes'] as $pid=>$parent) {
			if($pid > 0) {
				$changes['Parent '.$pid] = LockManager::hasOrdoChanged('CodingData',$pid,$lockTimestamp);
			}
			if(!isset($changes['Parent '.$pid]) || (isset($changes['Parent '.$pid]) && count($changes['Parent '.$pid]) == 0)) {
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
				if(isset($parent['tooth'])){
					$this->_db->Execute("DELETE FROM coding_data_dental WHERE coding_data_id='$parent_id'");
					$sql="INSERT INTO coding_data_dental (coding_data_id,tooth,toothside) VALUES ('".$code_data->get('id')."','".mysql_real_escape_string($parent['tooth'])."','".mysql_real_escape_string($parent['toothside'])."')";
					$this->_db->Execute($sql);
				}
				//var_dump($code_data);

				unset($child_code_data);
				$child_code_data =& ORdataObject::factory('CodingData');

				if(isset($_POST['child_codes']) && is_array($_POST['child_codes'])){
					foreach($_POST['child_codes'] as $code_id){
						if(intval($code_id) == 0)
						continue;

						$changes['Coding '.$code_id] = LockManager::hasOrdoChanged('CodingData',$code_id,$lockTimestamp);
						if(count($changes['Coding '.$code_id]) == 0) {
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
			}
		}
		foreach($changes as $name => $change) {
			if (count($change) > 0) {
				$overlappingChanges = true;
			}
		}
		if ($overlappingChanges) {
			LockManager::prepareChangedAlert($changes,$this,$lockTimestamp);
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

		return $this->render("update_dg.html");
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
		$superbills = array();

		$limit = true;
		if ($superbill) {
			// get the superbill for the current encounter practice
			$session =& Celini::sessionInstance();
			$practiceId = $session->get('Encounter:practice_id',0);
			
			$sb =& Celini::newOrdo('Superbill');
			$superbills = $sb->SuperbillsForPractice($practiceId);
		}
		if ($superbill == -1) {
			$limit = false;
		}
		
		$columnList = array('c.code_id', 'c.code', 'c.code_text');
		$tableList  = array('codes AS c');
		$filterList = array("(c.code LIKE '{$search_string}%' OR c.code_text LIKE '%{$search_string}%') ");
		$groupList  = array('c.code_id');
		$orderList  = array("(c.code LIKE '{$search_string}%') DESC");

		
		switch($search_type) {
			case 'icd':
				$filterList[] = 'c.code_type = 2';
				break;
			case 'cpt':
				array_push($filterList, 'c.code_type = 3');
				if ($limit) {
					array_push($filterList, 'fsd.data > 0');
					array_push($tableList,
						'LEFT JOIN fee_schedule_data AS fsd USING (code_id)',
						'LEFT JOIN fee_schedule AS fs USING (fee_schedule_id)');
					$orderList[] = 'fs.priority DESC';
				}
				break;
			case 'cdt':
				array_push($filterList, 'c.code_type = 5');
				if ($limit) {
					array_push($filterList,'fsd.data > 0');
					array_push($tableList,
						'LEFT JOIN fee_schedule_data AS fsd USING (code_id)',
						'LEFT JOIN fee_schedule AS fs USING (fee_schedule_id)');
					$orderList[] = 'fs.priority DESC';
				}
				break;
		}
		
		if ($superbill > 0) {
			$filterList[] = 'sbd.superbill_id in ( ' . implode(',',$superbills). ') and sbd.status = 1';
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
		
		if (is_array($this->get_template_vars('result_set'))) { 
			return $this->get_template_vars('result_set');
		}
			
		return null;
	}
	
	function cpt_search($search_string,$superbill = 0) {
		$_POST['process'] = true;
		$_POST['searchstring'] = $search_string;
		$_POST['searchtype'] = 'cpt';
		$_POST['superbill'] = $superbill;
		
		$this->find_action_process();
		
		if (is_array($this->get_template_vars('result_set'))) { 
			return $this->get_template_vars('result_set');
		}
			
		return null;
	}

	function cdt_search($search_string,$superbill=0) {
		$_POST['process'] = true;
		$_POST['searchstring'] = $search_string;
		$_POST['searchtype'] = 'cdt';
		$_POST['superbill'] = $superbill;
		
		$this->find_action_process();
		
		if (is_array($this->get_template_vars('result_set'))) { 
			return $this->get_template_vars('result_set');
		}
			
		return null;
	}

	function procedure_search($search_string,$superbill=-1) {
		$profile =& Celini::getCurrentUserProfile();
		$practice =& Celini::newOrdo('Practice',$profile->getCurrentPracticeId());

		if ($practice->get('type') == 1) {
			// if dental return cdt_search
			return $this->cdt_search($search_string,$superbill);
		}
		else {
			// if medical return cpt_search
			return $this->cpt_search($search_string,$superbill);
		}
	}

	function diagnosis_search($search_string,$superbill=-1) {
		return $this->icd_search($search_string,$superbill);
	}

	function process($data,$return = false){
		$this->foreign_id = $data['foreign_id'];
		$this->parent_id = $data['parent_id'];
		if (isset($data['superbill']))  {
			$this->superbill = $data['superbill'];
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


		if (!isset($data['parent_codes'])) {
			$data['parent_codes'] = array();
		}
		// add current code in if needed
		if (intval($data['parent_id']) > 0) {
			$data['parent_codes'][$data['parent_id']]['code'] = $data['parent_id'];
			$data['parent_codes'][$data['parent_id']]['units'] = $data['units'];
			$data['parent_codes'][$data['parent_id']]['modifier'] = $data['modifier'];
			if(isset($data['tooth'])){
				$data['parent_codes'][$data['parent_id']]['tooth'] = $data['tooth'];
				$data['parent_codes'][$data['parent_id']]['toothside'] = $data['toothside'];
			}
		}

		foreach($data['parent_codes'] as $parent) {
			$thecode = $parent['code'];
			unset($code_data);
			$code_data =& ORdataObject::factory('CodingData');
			$code_data->populate_array($parent);
			$code_data->set('code_id',$thecode);
			$code_data->set('parent_id',0); // There is no parent for the parent...
			$code_data->set('foreign_id',$data['foreign_id']); 
			$code_data->set('fee',$feeSchedule->getFeeFromCodeId($thecode));
			$code_data->set("id", 0);
			$code_data->persist();
			$parent_id=$code_data->get('id');
			if(isset($parent['tooth'])){
				$this->_db->Execute("DELETE FROM coding_data_dental WHERE coding_data_id='$parent_id'");
				$sql="INSERT INTO coding_data_dental (coding_data_id,tooth,toothside) VALUES ('".$code_data->get('id')."','".mysql_real_escape_string($parent['tooth'])."','".mysql_real_escape_string($parent['toothside'])."')";
				$this->_db->Execute($sql);
			}
			//var_dump($code_data);

			unset($child_code_data);		
			$child_code_data =& ORdataObject::factory('CodingData');

			if(isset($data['child_codes']) && is_array($data['child_codes'])){
				foreach($data['child_codes'] as $code_id){
					if(intval($code_id) == 0)
						continue;
			
							
					$child_code_data->set("id", 0);
					$child_code_data->set('code_id', $code_id);
					$child_code_data->set('parent_id', $parent_id);
					$child_code_data->set('foreign_id',$data['foreign_id']); 
					$child_code_data->persist();
					// TODO: Should I set the primary_code class to 2 or something?
					//var_dump($child_code_data);
				}	
			}
		}
		if($return == true) {
			return $code_data;
		}
		
	}
}
?>
