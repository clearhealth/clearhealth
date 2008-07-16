<?php
$GLOBALS['loader']->requireOnce('includes/HL7/HL7LabParser.class.php');

class C_LabImporter extends controller {

	function actionUpload() {
		return $this->view->render('upload.html');
	}

	function processUpload() {
		if (isset($_FILES['labfile']) && $_FILES['labfile']['error'] == 0) {
			$contents = file_get_contents($_FILES['labfile']['tmp_name']);
			$this->_parse($contents);
		}
	}

	function _parse($hl7) {
		
		$parser = new HL7LabParser();
		$parser->content = $hl7;

		$parser->parse();

		$r = $parser->getResults();
		$em =& Celini::enumManagerInstance();

		$patientToOrderMap = array();
		//var_dump($r);
		foreach($r['patients'] as $key => $patient) {
			$gMap = array('F'=>'Female','M'=>'Male','U'=>'Unknown');
			$gender = $em->lookupKey('gender',$gMap[$patient['gender']]);
			$search = array('last_name' => $patient['lastName'],'first_name' => $patient['firstName'],
				'date_of_birth' => date("Y-m-d",strtotime($patient['dateOfBirth'])), 'gender' => $gender, 'record_number' => $patient['patientId']);
			$pId = $this->_matchPatient($search);
			$order =& Celini::newOrdo('LabOrder');
			$order->set('patient_id',$pId);
			$order->set('type',$r['order']['type']);
			$order->set('status',$r['order']['status']);
			$order->set('ordering_provider',$r['order']['orderingProvider']);

			//$order->persist();

			$patientToOrderMap[$key] = $order->get('id');

			$this->messages->addMessage('Processed lab results for Patient: '.$patient['lastName'].', '.$patient['firstName']);
			
		}

		foreach($r['request'] as $patient => $tmp) {

			foreach($tmp as $index => $request) {
				$t =& Celini::newOrdo('LabTest');
				$t->set('lab_order_id',			$patientToOrderMap[$patient]);
				$t->set('order_num',			$request['placerOrderNum']);
				$t->set('filer_order_num',		$request['filerOrderNum']);
				$t->set('observation_time',		$request['observationDateTime']);
				$t->set('specimen_received_time',	$request['specimenReceivedDateTime']);
				$t->set('report_time',			$request['reportDateTime']);
				$t->set('ordering_provider',		$request['orderingProvider']);
				$t->set('service',			$request['service']);
				$t->set('component_code',		$request['componentCode']);
				$t->set('status',			$request['resultStatus']);
				$t->set('clia_disclosure',		$request['disclosureInfoCLIA']);
				//$t->persist();

				$tId = $t->get('id');

				foreach($r['observation'][$patient][$index] as $ob) {
					$lr =& Celini::newOrdo('LabResult');

					$lr->set('lab_test_id',		$tId);
					$lr->set('identifier',		$ob['identifier']);
					$lr->set('value',		$ob['value']);
					$lr->set('units',		$ob['units']);
					$lr->set('reference_range',	$ob['referenceRanges']);
					$lr->set('abnormal_flag',	$ob['abnormalFlags']);
					$lr->set('result_status',	$ob['resultStatus']);
					$lr->set('observation_time',	$ob['observationDateTime']);
					$lr->set('producer_id',		$ob['producersID']);
					$lr->set('description',		$ob['description']);	
					//$lr->persist();
				}

				if (isset($r['note'][$patient][$index])) {
					$n =& Celini::newOrdo('LabNote');
					$n->set('lab_test_id',$tId);
					$n->set('note',$r['note'][$patient][$index]);
					//$n->persist();
				}
			}
		}
	}
	
	function _parseMessages() {
		$db =& new clniDb();
		$sql = "select control_id,message from hl7_message where type = 1 and processed = 0";
		
		$res = $db->execute($sql);
		
		while ($res && !$res->EOF) {
			$sql = "update hl7_message  set processed = 1  where control_id = '" . $res->fields['control_id']."'";
			//$db->execute($sql);
			$this->_parse($res->fields['message']);
			$res->MoveNext();
		}
			
	}

	/**
	 * This function tries to match a patient using the passed in params
	 *
	 * Right now im using last, first, gender, and date of birth
	 * One of the id's might match up but im not sure
	 */
	function _matchPatient($options) {
		$db =& new clniDb();
		
		if (isset($options['record_number'])) {
			$sql = "select person_id from patient pa where record_number = " . $db->quote($options['record_number']);
			unset($options['record_number']);

			$res = $db->execute($sql);
			if ($res && !$res->EOF) {
				return $res->fields['person_id'];
			}
		}
		

		$where = "";
		foreach($options as $key => $val) {
			$where .= " and $key = ".$db->quote($val);
		}

		$sql = "select person_id from person where 1=1 $where";

		$res = $db->execute($sql);
		if ($res && !$res->EOF) {
			return $res->fields['person_id'];
		}
		return 0;
	}
	
	function _run_retrieval() {
	
		$config =& Celini::ConfigInstance();
		$labConf = $config->get("labs");
    	$wsdl_name = preg_replace("/^(.*\/|.*\\/)/","",$labConf['wsdl']);
    	$wsdl_file = "";

    	if (file_exists(APP_ROOT . "/tmp/" . $wsdl_name)) {
   			$wsdl_file = APP_ROOT . "/tmp/" . $wsdl_name;
    	}
    	else {
    		$wsdlxml = file_get_contents($labConf['wsdl']);
    		$wsdl_file = APP_ROOT . "/tmp/" . $wsdl_name;
    		$f = fopen($wsdl_file,"w");
    		fwrite($f, $wsdlxml);	
    	}
		
		$GLOBALS['loader']->requireOnce('lib/PEAR/SOAP/Client.php');

		$wsdl = new SOAP_WSDL($wsdl_file);
		
		$client = $wsdl->getProxy();

		$client->__options['user'] = $labConf['user'];
		$client->__options['pass'] = $labConf['pass'];
		$client->__options['timeout'] = 15;

		$dateMaker = new SOAP_Type_dateTime();
                                                                               
		$labResults = array();
		
		
		$startDate =  date("m/d/Y h:i A",strtotime(date("Y-m-d") . " -1 day"));
		$endDate =  date("m/d/Y h:i A",strtotime(date("Y-m-d") . " 23:59:59"));
		//echo "startDate: " . $startDate . " endDate" . $endDate . "<br>"; 
		$params = array (
                "endDate" => $endDate,
                //"endDate" => '',
                "maxMessages" => null,
                //"providerAccountIds" => array("2920"),
                "providerAccountIds" => null,
                "retrieveFinalsOnly" => true,
                "retrieveObseleteResults" => false,
                "startDate" => $startDate,
                //"startDate" => ''                                               
        );
		
		//@ sign because PEAR::SOAP does not run well under E_ALL
		$results = $client->getHL7Results($params);
		$this->_loadMessages($results, $client);
		$i = 1;
		
		while ($results->isMore == true && $i--) {
			//echo "looping" . $results->requestId . "<br>";
        	$params = array("requestId" => strval($results->requestId));
        	@$results = $client->getMoreHL7Results($results->requestId);
        	//var_dump($results->HL7Messages);
        	
        	$this->_loadMessages($results, $client);
        	
		}
		
	}
		
	function _loadMessages($results, $client) {
		$acks = array();
		if(is_array($results->HL7Messages)) {
		foreach ($results->HL7Messages as $labHL7) {
			$labHL7 =  base64_decode($labHL7->message);
        	$controlId = date("YmdHi") . substr(preg_replace("/^.*\ /","",microtime()),-8);
        	preg_match_all("/^.*\|([0-9]{20})/",$labHL7,$matches);
			$hl7msg = "MSH|^~\\&||91950092|LAB|MET|".date('YmdHi')."||ACK|".$controlId."|D|2.3\rMSA|CA|" . $matches[1][0];
			$hl7ordo =& Celini::newORDO("HL7Message",array("",$matches[1][0],1));
			$hl7ordo->set("control_id", $matches[1][0]);
			$hl7ordo->set("type", 1);
			//echo "new message: " . $matches[1][0] . "<br>";
			$acks[] = $hl7msg;

			if ($hl7ordo->isPopulated()) {continue;}
			$hl7ordo->set("control_id", $matches[1][0]);
			$hl7ordo->set("message", $labHL7);
			//$hl7ordo->persist();
		}
		}
		if (count($acks) > 0) {
			//echo "here<br>";
			@$client->acknowledgeResults($results->requestId, $acks);
		}
	}
	
	function actionBatchLabs_edit() {
		$this->_run_retrieval();
		$this->_parseMessages();	
	}
}

?>
