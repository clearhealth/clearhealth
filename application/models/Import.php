<?php
/*****************************************************************************
*       Import.php
*
*       Author:  ClearHealth Inc. (www.clear-health.com)        2009
*       
*       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
*       respective logos, icons, and terms are registered trademarks 
*       of ClearHealth Inc.
*
*       Though this software is open source you MAY NOT use our 
*       trademarks, graphics, logos and icons without explicit permission. 
*       Derivitive works MUST NOT be primarily identified using our 
*       trademarks, though statements such as "Based on ClearHealth(TM) 
*       Technology" or "incoporating ClearHealth(TM) source code" 
*       are permissible.
*
*       This file is licensed under the GPL V3, you can find
*       a copy of that license by visiting:
*       http://www.fsf.org/licensing/licenses/gpl.html
*       
*****************************************************************************/


class Import {

	public static function hl7Lab($hl7msg,$personId) {
		$hl7msg = preg_replace("/\r/","\n",$hl7msg);
		$db = Zend_Registry::get('dbAdapter');
		$xml = new HL7XML($hl7msg);
		$xml->parse();
		
		file_put_contents('/tmp/xml', print_r($xml,true));
		$orders = $xml->xml->xpath("//MSH");
		$ordersCtr = count($orders);
		for ($o=0;$o<$ordersCtr;$o++) {
			$orderXml = simplexml_load_string($orders[$o]->asXml());
			$pid = 0;
			if (strlen((string)current($orderXml->xpath("//PID.3"))) > 0 && 
				isset($mrn2pid[(string)current($orderXml->xpath("//PID.3"))])) {

				$pid = $mrn2pid[(string)current($orderXml->xpath("//PID.3"))];
			}
			else {
				$sqlSelect = $db->select()
						->from("patient")
						->join("person","person.person_id = patient.person_id")
						->where("patient.record_number = ?",(string)current($orderXml->xpath("//PID.3")));
				$patients = $db->query($sqlSelect)->fetchAll();
				if (!count($patients) > 0) continue;
				$pid = $patients[0]['person_id'];
				$mrn2pid[(string)current($orderXml->xpath("//PID.3"))] = $pid;
				if (!$pid > 0) continue;
				// last name, first name and DOB
			}
			$order = new LabOrder();
			$order->_shouldAudit = false;
			$order->_cascadePersist = false;
			$order->patientId = $pid;
			//$order->type = '';
			$order->status = 'F';
			//$order->manualOrderDate = date('Y-m-d',strtotime((string)current($orderXml->xpath("//ORC/ORC.9"))));

			$orderingProvider = current($orderXml->xpath("//ORC.12.3"));
			$orderingProvider .= " " . current($orderXml->xpath("//ORC.12.2"));
			$orderingProvider .= " " .current($orderXml->xpath("//ORC.12.1"));
			$orderingProvider .= " (". current($orderXml->xpath("//ORC.12.0")) . ")";
			$order->ordering_provider = $orderingProvider;
			$order->persist();
			$testsGroup = $orderXml->xpath("//ORC");
			//var_dump($testsGroup);continue;
			$tgCtr = count($testsGroup);
			for ($x=0;$x<$tgCtr;$x++) {
				//echo "test\n";
				$test = simplexml_load_string($testsGroup[$x]->asXml());
				//var_dump($test);
				$t = new LabTest();
				$t->_shouldAudit = false;
				$t->_cascadePersist = false;
				$t->labOrderId = $order->labOrderId;
				$t->orderNum = (string)current($test->xpath("//OBR.2.0"));
				$t->filer_order_num = (string)current($test->xpath("//OBR.3.0"));
				$observation_time = preg_replace('/([0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]).*/','${1}',current($test->xpath("//OBR.22")));
				$t->observation_time = date('Y-m-d H:i:s',strtotime($observation_time));
				$manual_order_date = preg_replace('/([0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]).*/','${1}',current($test->xpath("//OBR.7")));
				$order->manual_order_date = date('Y-m-d H:i:s',strtotime($manual_order_date));
				$order->persist();
				$specimen_received_time = preg_replace('/([0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]).*/','${1}',current($test->xpath("//OBR.14")));
				$t->specimen_received_time = date('Y-m-d H:i:s',strtotime($observation_time));
				$report_time = preg_replace('/([0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]).*/','${1}',current($test->xpath("//OBR.22")));
				$t->report_time = date('Y-m-d H:i:s',strtotime($report_time));
				//$t->ordering_provider = ;
				$t->service = (string)current($test->xpath("//OBR.4.1")) . " " . current($test->xpath("//OBR.4.2"));
				$t->component_code = (string)current($test->xpath("//OBR.3.0"));
				$t->status = (string)current($test->xpath("//OBR.25"));
				$t->clia_disclosure = (string)current($test->xpath("//OBR.21.0")) . " " . (string)current($test->xpath("//OBR.21.1")) . " " . (string)current($test->xpath("//OBR.21.2")) . " " . (string)current($test->xpath("//OBR.21.3")) . " " . (string)current($test->xpath("//OBR.21.4")) . " " . (string)current($test->xpath("//OBR.21.4")) . " ". (string)current($test->xpath("//OBR.21.5")) . " " . (string)current($test->xpath("//OBR.21.6"));
				$t->persist();
				//echo $t->toString();
				$resultsGroup = $test->xpath("//OBR/OBX");
				//var_dump($t->toString()); continue;
				$rgCtr = count($resultsGroup);
				for ($i=0;$i<$rgCtr;$i++) {
					$result = simplexml_load_string($resultsGroup[$i]->asXml());
					//var_dump($result->asXml());
					$lr = null;
					$lr = new LabResult();
					$lr->_shouldAudit = false;
					$lr->_cascadePersist = false;
					$lr->labTestId = $t->labTestId;
					$lr->description = (string)current($result->xpath("//OBX.3.1")) . " " . (string)current($result->xpath("//OBX.3.2")) . "(" . (string)current($result->xpath("//OBX.3.0")) . ")";
					//$lr->extra = ',$row['Group']);
					$lr->identifier = (string)current($result->xpath("//OBX.3"));
					$lr->reference_range = (string)current($result->xpath("//OBX.7"));
					$lr->abnormal_flag = (string)current($result->xpath("//OBX.8")) ;
					$lrobservation_time = preg_replace('/([0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]).*/','${1}',current($test->xpath("//OBR.22")));
					$lr->observation_time = date('Y-m-d H:i:s',strtotime($lrobservation_time));
					$lr->units = (string)current($result->xpath("//OBX.6"));
					$lr->value = (string)current($result->xpath("//OBX.5"));
					$lr->result_status = (string)current($result->xpath("//OBX.11"));
					//echo $lr->toString() . "\n\n";
					$lr->persist();
					if (count($result->xpath("//NTE")) > 0) {
						$note = "";
						foreach($result->xpath("//NTE") as $noteLine) {
							$note .= trim($noteLine->{'NTE.3'}) . "\n";
						}
						$labNote = new LabNote();
						$labNote->_shouldAudit = false;
						$labNote->_cascadePersist = false;
						$labNote->labTestId = $t->labTestId;
						$labNote->labResultId = $lr->labResultId;
						$labNote->note = $note;
						$labNote->persist();
					}
				}
			}
		}
	}

}
