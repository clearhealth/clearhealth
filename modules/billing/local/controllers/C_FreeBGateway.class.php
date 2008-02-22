<?php

$loader->requireOnce('includes/ElectronicClaimRenderer.class.php');
$loader->requireOnce('includes/DestinationProcessorManager.class.php');

/**
 * Load all FB* class files
 * @ignore
 */
$ordoClassDirectory = dirname(__FILE__) . '/../ordo'; 
$directory = opendir($ordoClassDirectory);
while (false !== ($filename = readdir($directory))) {
	if (preg_match('/^FB.+\.class\.php$/', $filename)) {
		$loader->requireOnce('ordo/' . $filename);
	}
}
// cleanup
closedir($directory);
unset($directory);
unset($ordoClassDirectory);

/**
* class FreeBGateway
* This class is the simplified API for ClearHealth FreeB 2.0, previous versions
* of FreeB suffered from a wealth of API functions (more than 200) which the
* this new class greatly simplifies. This new API offers long term extensibility
* through the use of associated arrays instead of specific function arguments.
* Please review the function documentation closely.
* 
* @package com.clear-health.freeb2
*/

class C_FreeBGateway extends Controller {
	
	var $last_error = array();
	var $variation_cache = "";
	var $total_x12_segments = 0;
	var $hl_count = 0;
	var $x12_id = 0;

	var $current_revision = false;
	
	function C_FreeBGateway() {
		parent::Controller();
		$this->view->_loadCeliniPlugins();
		$this->view->compile_check = true;
		ORDataObject::Factory_Include("FBClaim");
	}
	
	function openClaim_action($claim_identifier, $revision = 0, $claim_mode = "P") {
		return $this->openClaim($claim_identifier, $revision, $claim_mode);
	}
	
	function openClaim($claim_identifier, $revision = 0, $claim_mode = "P", $rebill = false) {
	
		$c =& FBClaim::fromClaimId($claim_identifier, $revision);
		//$c->set("claim_mode",$claim_mode);
		//claim identifier was found and successfully instantiated
		if ($c->shouldCreateNewRevision()) {
			$entities =& $c->childEntities("",$c->get("claim_id"));
			//var_dump($entities);
			//clear id because this is a now a new revision
			$c->set("claim_id",'');
			//set claim open status to open
			$c->set("status",'new');
			$c->persist(true);
			
			/**
			 * @internal
			 *  This whole foreach() definition is a hack to make it work in PHP 4.
			 *
			 * Once in PHP 5, the following should work again as expected.
			 * 
			 * <code>foreach ($entities as $entity)</code>
			 */
			$entityKeys = array_keys($entities);
			foreach ($entityKeys as $entityKey) {
				$entity =& $entities[$entityKey];
				// end hack
				
				if (is_a($entity,"fbperson")) {
					$entity->set("person_id","");
					$entity->set("address_id","");
				}
				elseif (is_a($entity,"fbcompany")) {
					$entity->set("company_id","");
					$entity->set("address_id","");	
				}
				elseif (is_a($entity,"fbclaimline")) {
					$entity->set("claimline_id","");	
				}
				$entity->set("claim_id",$c->get("claim_id"));
				$entity->persist();
			}
			
			$this->current_revision = $c->get("revision");	
			return $this->current_revision;
		}
		//claim identifier was not found and a new one was created
		elseif (($c->get("claim_id") == 0 || $c->get("claim_id") == "")) {
			$c->set("status","new");
			$c->persist(true);
			return 1;
		}
		elseif ($c->get('claim_id') > 0 && $rebill) {
			$c->set("claim_id",'');
			//set claim open status to open
			$c->set("status",'new');
			$c->persist(true);
			$this->current_revision = $c->get("revision");	
			return $this->current_revision;
		}
		//claim is already open and cannot be opened again or other fatal problem
		$this->last_error = array("100","Claim already open or other fatal system error.");
		return 0;	
	}

	/**
	 * Get the max revision of a claim
	 */
	function maxClaimRevision($claim_identifier) {
		$c =& FBClaim::fromClaimId($claim_identifier);
		return $c->get('revision');
	}

	/**
	 * @todo Determine why this is here and whether or not it is needed
	 */
	function registerData_action($claim_identifier, $type, $data_array) {
		return $this->registerData($claim_identifier, $type, $data_array);
	}

	/**
	 * Registers data for a given claim
	 *
	 * @param  string  The indentifier of a claim
	 * @param  string  The type of data that is being registered
	 * @param  array   The array of data to be registered
	 * @param  int     If this is specified as a number, registerData will
	 *                 attempt to load an existing child entity instead of 
	 *                 registering a new child.
	 * @return int
	 *
	 * @todo Fully document this method
	 * @todo Consider breaking out into its own object
	 */
	function registerData($claim_identifier, $type, $data_array, $index = '') {
		$c =& FBClaim::fromClaimId($claim_identifier,$this->current_revision);
		if (!($c->get("id") > 0)) {
			/* printf('<pre>%s</pre>', var_export($claim_identifier , true));
			printf('<pre>%s</pre>', var_export($this->current_revision , true));
			printf('<pre>%s</pre>', var_export($c->toString() , true)); */
			$this->last_error = array("100","Claim not found with id: $claim_identifier,  or other fatal system error.");
			return 0;
		}
		
		if ($c->get("status") != "new") {
			$this->last_error = array('100', 'Claim status not new: fatal system error.');
			return 0;
		}
		
		// Transform address into proper FB prefixed name
		if (isset($data_array['address'])) {
			$data_array['fbaddress'] = $data_array['address'];
			unset($data_array['address']);
		}

		$fbtype = "FB" . $type;
		switch($type) {
			case 'Patient':
			case 'Provider':
			case 'ReferringProvider':
			case 'SupervisingProvider':
			case 'Subscriber':
			case 'ResponsibleParty':
				$ce = null;
				if(is_numeric($index)) {
					$ce =& $c->childEntity($fbtype, $c->get("claim_id"), $index);
				}
				else {
					$ce =& ORDataObject::factory($fbtype,0,$c->get("claim_id"),$index);
				}
				
				if (isset($data_array['first_name']) && isset($data_array['last_name'])) {
					$ce->populate_array($data_array);
					$ce->persist();
				}
				else{
					$this->last_error = array("800","Name fields must be set to register a $type");
					return 0;
				}
				return 1;
			case 'Claimline':
				
				//only a single claimline was sent and so it does not have to be nested
				if (isset($data_array['procedure'])) {
					if (is_numeric($index)) {
						$ce =& $c->childEntity($fbtype, $c->get('claim_id'), $index);
					}
					else {
						$ce =& $c->childEntity($fbtype);
					}
					$ce->populate_array($data_array);
					$ce->persist();
				}
				//multiple claimlines were sent in an array so register all of them
				elseif (is_array($data_array[0])) {
					$i=1;
					foreach($data_array as $ref => $da) {
						$ce =& $c->childEntity($fbtype);
						$ce->populate_array($da);
						if (empty($da['reference'])) {
							$ce->set("reference",$i);
						}
						$ce->persist();
						$i++;	
					}
				}
				else {
					$this->last_error = array("800","Procedure value must be set, or no nested data found");
					return 0;
				}
				return 1;
			case 'Practice':
			case 'Payer':
			case 'BillingFacility':
			case 'TreatingFacility':
			case 'ClearingHouse':
			case 'BillingContact':
				$ce = null;
				if(is_numeric($index)) {
					$ce =& $c->childEntity($fbtype, $c->get("claim_id"), $index);
				}
				else {
					$ce =& ORDataObject::factory($fbtype,0,$c->get("claim_id"),$index);
				}
				if (isset($data_array['name']) && isset($data_array['fbaddress'])) {
					$ce->populate_array($data_array);
					$ce->persist();
				}
				else{
					$this->last_error = array("810","Name and address fields must be set to register a $type");
					return 0;
				}
				return 1;
			case 'Claim':
				if (isset($data_array['date_last_seen'])) { 
					$patient =& $c->childEntity('FBPatient');
					$patient->set('date_last_seen', $data_array['date_last_seen']);
					$patient->persist();
				}
				$c->populate_array($data_array);
				$c->persist();
				return 1;
			default:
				$this->last_error = array("110","Unknown registration name.");
				return 0;
		}
	
	}
	
	function clearClaim_action($claim_identifier) {
		$this->clearClaim($claim_identifier);
	}
	
	function clearClaim($claim_identifier) {
		
	}
	
	function closeClaim_action($claim_identifier) {
		return $this->closeClaim($claim_identifier);	
	}
	
	function closeClaim($claim_identifier,$revision = "") {
		$c =& FBClaim::fromClaimId($claim_identifier,$revision);
		if ($c->get("status") == "new") {
			$c->set("status",'pending');
			$c->persist();
			return 1;
		}
		else {
			$this->last_error = array("120","Claim does not have new status.");
			return 0;
		}
	}
	
	function archiveClaim_action($claim_identifier) {
		return $this->archiveClaim($claim_identifier);	
	}
	
	function archiveClaim($claim_identifier,$revision = "") {
		$c =& FBClaim::fromClaimId($claim_identifier,$revision);
		if ($c->get("status") == "sent") {
			$c->set("status",'archive');
			$c->persist();
			return 1;
		}
		else {
			$this->last_error = array("120","Claim does not have sent status.");
			return 0;
		}
	}
	
	function claimResult_action_view($batch, $format,$package = "txt",$destination = "browser") {
		return $this->claimResult($batch,$format,$package,$destination);
	}

	function claimResult($batch,$format,$package = "txt", $destination = "browser") {
		if (!preg_match("/^(hcfa|x12)_.*/", $format, $matches)) {
			$this->last_error = array("2000","Claim variation/format must begin with x12_ or hcfa_.");
			return false;
		}

		$format_type = $matches[1];

		if (array_search($format, $this->claimVariationList()) === false) {
			$this->last_error = array("2100","Claim variation/format $format_type is not supported.");
			return false;
		}

		if($format_type == "x12") {
			//other electronic types go here!!
			$claimresult = $this->electronicClaimResult($batch,$format);
		}
		else{
			$claimresult = $this->paperClaimResult($batch,$format);
		}
		$first_claim_id = key($batch);
		$c =& ORDataObject::factory('FBClaim',$first_claim_id); 
		//The first claims id is the batch id...

		$dpm =& Celini::dpmInstance();
		$processor =& $dpm->processorInstance($destination);
		$processor->processPackage($claimresult, $c, $format);
		$claimresult = $processor->outputResults();
		$packaged_result = $this->_package_result($claimresult,$package);
		
		foreach ($batch as $batchClaim_id => $status) {
			if ($status['on'] != 1) {
				continue;
			}
			$batchClaim =& Celini::newORDO('FBClaim', $batchClaim_id);
			$batchClaim->set("status","sent");
			$batchClaim->set("date_sent",date("Y-m-d H:i:s"));
			$batchClaim->persist();
		}
		
		return $packaged_result;
	}

	function paperClaimResult($batch,$format) {
		$config =& Celini::configInstance();
		$generatePendingClaimsOnly = $config->get('generatePendingClaimsOnly', false);
		// $all_claimresults = "There are ".count($batch)." claims in this batch"; //For testing... 
		$all_claimresults = '';
		//this is a simple loop that repeats and creates one claim after another...
	
		$include_me = CELINI_ROOT."/includes/plugins/prefilter.stripedi.php";
		//echo $include_me;
		include_once $include_me;
		$this->view->register_prefilter("smarty_prefilter_stripedi");

		foreach($batch as $claim_id => $claim_array) {
		$c =& Celini::newORDO('FBClaim', $claim_id);

		if ($c->get("status") != "pending" && $generatePendingClaimsOnly) {
			$this->last_error = array("130","Could not return result, claim must have pending status to get result."); 
			return false;	
		}
		
		$this->assign_by_ref("patient", $c->childEntity("FBPatient"));
		$this->assign_by_ref("provider", $c->childEntity("FBProvider"));
		$this->assign_by_ref("referring_provider", $c->childEntity("FBReferringProvider"));
		$this->assign_by_ref("supervising_provider", $c->childEntity("FBSupervisingProvider"));
		$this->assign_by_ref("subscriber", $c->childEntity("FBSubscriber"));
		$this->assign_by_ref("subscribers", $c->childEntities("FBSubscriber"));
		$this->assign_by_ref("billing_contact", $c->childEntity("FBBillingContact"));
		$this->assign_by_ref("responsible_party", $c->childEntity("FBResponsibleParty"));
		$this->assign_by_ref("practice", $c->childEntity("FBPractice"));
		$this->assign_by_ref("billing_facility", $c->childEntity("FBBillingFacility"));
		$this->assign_by_ref("treating_facility", $c->childEntity("FBTreatingFacility"));
		$this->assign_by_ref("payer", $c->childEntity("FBPayer"));
		$this->assign_by_ref("payers", $c->childEntities("FBPayer"));
		$this->assign_by_ref("clearing_house", $c->childEntity("FBClearingHouse"));
		$this->assign_by_ref("identifier", $c->childEntity("FBProvider"));
		$this->assign_by_ref("claim_identifier", $c);
		$this->assign_by_ref("claim", $c);
		$c->set("format",$format);
		
		$claimresult = "";
		$claimlines = $c->childEntities("FBClaimline");
		
		$format_type = "hcfa";//when adding new paper types put a regexp to 
					//discover the type here...
		if ($format_type === "hcfa") {//add new sections for other paper types
			global $loader;
			$loader->requireOnce("includes/HCFAClaimlinePager.class.php");			
			//add new page control character
			$hcfa_pager = new HCFAClaimlinePager($claimlines);
			$this->assign("total_pages",$hcfa_pager->get_total_pages());
			while ($hcfa_page = $hcfa_pager->next()) {
				$this->assign("current_page",$hcfa_pager->get_current_page());
				if (!$hcfa_pager->EOF) {
					$this->assign("claim_continues",1);
				}
				else {
					$this->assign("claim_continues",0);
				}
				$this->assign("diagnoses",$hcfa_pager->get_diagnoses());
				for($i=0;$i<count($hcfa_page);$i++) {
					$hcfa_page[$i]->set("diagnosis_pointer",$hcfa_pager->get_diagnosis_pointer($hcfa_page[$i]));
				}
				$this->assign_by_ref("claim_lines", $hcfa_page);
				$claimresult .= $this->fetch(Celini::getTemplatePath("/variations/" . preg_replace("/[^A-Za-z0-9_]/","",$format) . "/" . preg_replace("/[^A-Za-z0-9_]/","",$format) ."_header.html"));
			}			
			$claimresult = $this->_postfilter_margin($claimresult);
		}//end hcfa-only section...

		//well thats one hcfa... lets add it to the pile of hcfas
		$all_claimresults = $all_claimresults . $claimresult; 
		
		}//end of the main claim loop... 
		return $all_claimresults;	
	}

	function electronicClaimResult($batch,$format) {
		$renderer =& new ElectronicClaimRenderer($batch, $format);
		return $renderer->render();
	}


	
	function claimLastError_action($claim_identifier) {
		return $this->claimLastError($claim_identifier);
	}
	
	function claimLastError($claim_identifier) {
		return $this->last_error;
	}
	
	function claimVariationList() {
		if (is_array($this->variation_cache)) {
			return $this->variation_cache;
		}
		$variations = array();
		
		if ($handle = opendir(Celini::getTemplatePath("/variations"))) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != ".." && is_dir(Celini::getTemplatePath("/variations/" . $file)) && file_exists(Celini::getTemplatePath("/variations/" . $file) . "/" . $file . "_header.html")) {
					$variations[basename($file)] = basename($file);	
				}
			}
		closedir($handle);
		}
		asort($variations);
		$this->variation_cache = $variations; 
		return $variations; 
	}
	
	function claimDestinationList() {
		//$dpm =& new  DestinationProcessorManager();	// old
		$dpm =& Celini::dpmInstance();
		return $dpm->getProcessorList();
	}

	function send_claim_edihealthcare($user, $passwd, $filename) {
		// This function interfaces with the webform avialable from
		// edihealthcare.com


		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://trymcs.com/cgi-win/cgi.plc');
		curl_setopt($ch, CURLOPT_USERPWD, "$user:$passwd");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, 'http://trymcs.com/mainssl.html');
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		$postfields = array();

		// The upload post variables as defined in the upload form..
		$postfields['function'] = urlencode('01');
		$postfields['intype'] = urlencode('1');
		$postfields['filenami'] = "@$filename";
		$postfields['submit'] = "UPLOAD";
	
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);

		$results = curl_exec($ch);
	
		curl_close($ch);

		// uncomment this to see what the results of the upload are...
		//echo $results;

		if(strpos($results,"X12 file received."))
		{	
			return(true);
		}else{

			return(false);
		}
		/*New stuff, replace function contents with following two lines
		Celini::deprecatedWarning("C_FreeBGateway::send_claim_edihealthcare");
		return ClearinghouseDestination::send_claim_edihealthcare($user, $passwd, $filename);
		*/
	}


	
	function _package_result($claimresult, $package) {
		if ($package === "txt") {
			return $claimresult;
		}
		else if ($package === "pdf") {
			return "<pre>" . $claimresult . "</pre><!-- PAGE BREAK -->";	
		}
	}
	
	function _postfilter_margin($claimresult, $margin = 1) {
		if (is_null($margin)) {
			$config =& Celini::configInstance();
			$margin = $config->get('printMargin', 1);
		}
			$lines = split("\n",$claimresult);
			$result = "";
			foreach($lines as $line) {
				$result .= str_repeat(' ',$margin) . $line . "\n";
			}
			return $result;
	}
	
	/**
	 * Replaced by {@link EDIHelper::postfilterSegmentCount()}
	 *
	 * @todo remove
	 */
	function _postfilter_edi_segement_count($text) {
		// this is a simple count of every tilde in the file. 

		$count = substr_count($text,'~');	
		
		//each tilde represents a segment, count them up
		return $count;
	}

	/**
	 * Replaced by {@link EDIHelper::postfilterSEReplacement()}
	 *
	 * @todo remove
	 */
	function _postfilter_edi_SE_replacement($text) {

		// We know that the count will be off by 4. There are three segments that 
		// are not between the ST and the SE segments.
		// ISA does not get counted
		// GS does not get counted
		// ST counts
		// everything in between counts
		// SE does not count
		// GE does not count
		// so we subtract 4 from the count of segments

		$segments_between_ST_and_SE = $this->total_x12_segments - 4;	
		//put the count in place of the POSTFILTER_SEGEMENT_COUNT token 
		return preg_replace("/POSTFILTER_SEGEMENT_COUNT/",$segments_between_ST_and_SE,$text);	
	}
	
	function test($in) {
		return var_export($in,true);
	}
}


?>
