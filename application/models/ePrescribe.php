<?php
/*****************************************************************************
*       ePrescribe.php
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


class ePrescribe {

	public static function pull() {
		$ch = curl_init();
		$ePrescribeURL = Zend_Registry::get('config')->healthcloud->URL;
		$ePrescribeURL .= 'ss-manager.raw/pull-inbounds?apiKey='.Zend_Registry::get('config')->healthcloud->apiKey;
		curl_setopt($ch,CURLOPT_URL,$ePrescribeURL);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		$output = curl_exec($ch);
		$error = '';
		$ret = 0;
		if (!curl_errno($ch)) {
			try {
				$xml = new SimpleXMLElement($output);
				foreach ($xml->data as $messages) {
					foreach ($messages as $key=>$message) {
						$rawMessage = base64_decode((string)$message->rawMessage);
						if ($key == 'refillRequest') {
							$messageId = (string)$message->messageId;
							$rxReferenceNumber = (string)$message->rxReferenceNumber;
							$prescriberOrderNumber = (string)$message->prescriberOrderNumber;
							$auditId = 0;
							$medicationId = 0;

							$xmlMessage = new SimpleXMLElement($rawMessage);
							$lastName = (string)$xmlMessage->Body->RefillRequest->Patient->Name->LastName;
							$firstName = (string)$xmlMessage->Body->RefillRequest->Patient->Name->FirstName;
							$messageInfo = ' for '.$lastName.', '.$firstName;
							$description = (string)$xmlMessage->Body->RefillRequest->MedicationPrescribed->DrugDescription;
							$datePrescribed = date('m/d/Y',strtotime((string)$xmlMessage->Body->RefillRequest->MedicationPrescribed->WrittenDate));
							$messageInfo .= ' - '.$description.' #'.$datePrescribed;
							if (strlen($prescriberOrderNumber) > 0) {
								// currently check for medicationId using the prescriberOrderNumber medication_audit
								$medAudit = explode('_',$prescriberOrderNumber);
								$medicationId = (int)$medAudit[0];
								$auditId = isset($medAudit[1])?(int)$medAudit[1]:0;
							}
							$medication = new Medication();
							$medication->medicationId = $medicationId;
							$medication->populate();
							$patientId = (int)$medication->personId;
							$unresolved = 0;

							// retrieve providerId using SPI
							$SPI = (string)$xmlMessage->Body->RefillRequest->Prescriber->Identification->SPI;
							$eprescriber = new EPrescriber();
							$eprescriber->populateBySPI($SPI);
							$providerId = (int)$eprescriber->providerId;

							if (!$patientId > 0) { // PON not set or invalid PON, try to automatch based on name, dob, medication and dates in the refreq, if only one match automatically link with correct PON

								// retrieve pharmacyId using NCPDPID
								$NCPDPID = (string)$xmlMessage->Body->RefillRequest->Pharmacy->Identification->NCPDPID;
								$pharmacy = new Pharmacy();
								$pharmacy->NCPDPID = $NCPDPID;
								$pharmacy->populatePharmacyIdWithNCPDPID();
								$pharmacyId = (string)$pharmacy->pharmacyId;

								$gender = (string)$xmlMessage->Body->RefillRequest->Patient->Gender;
								$dob = (string)$xmlMessage->Body->RefillRequest->Patient->DateOfBirth;

								// retrieve patientId using LastName, FirstName, Gender and DOB
								$db = Zend_Registry::get('dbAdapter');
								$sqlSelect = $db->select()
										->from('person','person_id')
										->where('last_name = ?',$lastName)
										->where('first_name = ?',$firstName)
										//->where('gender = ?',$gender) temporarily comment out due to gender value
										->where('date_of_birth = ?',date('Y-m-d',strtotime($dob)))
										->limit(1);
								if ($row = $db->fetchRow($sqlSelect)) {
									$patientId = $row['person_id'];
								}
								//trigger_error($sqlSelect->__toString());

								// $qualifiers = Medication::listQuantityQualifiersMapping(); TODO: since qualifier are ambiguous, temporarily not to use this qualifier
								$quantity = (string)$xmlMessage->Body->RefillRequest->MedicationPrescribed->Quantity->Value;

								$sqlSelect = $db->select()
										->from('medications')
										->where('description = ?',$description)
										->where('quantity = ?',$quantity)
										->where('personId = ?',(int)$patientId)
										->where('prescriberPersonId = ?',(int)$providerId)
										->where('pharmacyId = ?',(int)$pharmacyId);

								$writtenDate = (string)$xmlMessage->Body->RefillRequest->MedicationPrescribed->WrittenDate;
								if (strlen($writtenDate) > 0) {
									$sqlSelect->where('datePrescribed LIKE ?',date('Y-m-d',strtotime($writtenDate)).'%');
								}
								$medicationMatched = false;
								//trigger_error($sqlSelect->__toString());
								$rows = $db->fetchAll($sqlSelect);
								if (count($rows) == 1) {
									$medication = new Medication();
									$medication->populateWithArray($rows[0]);
									$medicationId = $medication->medicationId;
									$auditId = Medication::getAuditId($medicationId);
									if ($auditId > 0) {
										$xmlMessage->Body->RefillRequest->PrescriberOrderNumber = $medicationId.'_'.$auditId;
										$rawMessage = $xmlMessage->asXML();
									}
									//trigger_error($sqlSelect->__toString());
									$medicationMatched = true;
								}
								$messageInfo = ' (Invalid/Missing PON';
								if ($patientId > 0 && $medicationMatched) {
									$patient = new Patient();
									$patient->personId = $patientId;
									$patient->populate();
									$messageInfo .= ' - automatched to \''.$patient->displayName.'\' MRN#'.$patient->recordNumber;
								}
								else {
									$unresolved = 1;
								}
								$messageInfo .= ')';
							}

							$refillRequest = new MedicationRefillRequest();
							$refillRequest->messageId = $messageId;
							$refillRequest->medicationId = $medicationId;
							$refillRequest->action = '';
							$refillRequest->status = '';
							$refillRequest->dateStart = '';
							$refillRequest->details = 'Re: '.$rxReferenceNumber;
							$refillRequest->dateTime = date('Y-m-d H:i:s');
							// disable audits autoprocess, this was set at CHProcessingDaemon
							$processedAudits = Audit::$_processedAudits;
							Audit::$_processedAudits = false;
							$refillRequest->persist();
							Audit::$_processedAudits = $processedAudits;

							$messaging = new Messaging();
							$messaging->messagingId = $messageId;
							$messaging->populate();
							$messaging->messageType = 'RefillRequest';
							$messaging->objectId = $refillRequest->messageId;
							$messaging->objectClass = 'MedicationRefillRequest';
							$messaging->note = 'Refill request received - Re:'.$rxReferenceNumber.$messageInfo;
							$messaging->auditId = $auditId;
							$messaging->refills = (string)$message->refills;

							$messaging->personId = $patientId;
							$messaging->providerId = $providerId;
							$messaging->unresolved = $unresolved;
						}
						else if ($key == 'status') {
							$relatesToMessageId = (string)$message->relatesToMessageId;
							$messageId = (string)$message->messageId;
							$code = (string)$message->code;
							$description = (string)$message->description;

							$messaging = new Messaging();
							$messaging->messageType = 'Status';
							$messaging->note = 'Status received for unknown messageId: '.$relatesToMessageId;

							$tmpMsg = new Messaging();
							$tmpMsg->messagingId = $relatesToMessageId;
							if ($tmpMsg->populate()) { // populate for newRx details
								$tmpMsg->status = 'Sent and Verified';
								$x = explode('(',$tmpMsg->note);
								$tmpMsg->note = 'newRx';
								if ($tmpMsg->objectClass == 'MedicationRefillResponse') {
									$tmpMsg->note = 'Refill response';
								}
								$tmpMsg->note .= ' sent and verified';
								if (isset($x[1])) {
									unset($x[0]);
									$tmpMsg->note .= ' ('.implode('(',$x);
								}
								$tmpMsg->unresolved = 0;
								$tmpMsg->persist();
								$messaging->auditId = $tmpMsg->auditId;
								$messaging->objectId = $tmpMsg->objectId;
								$messaging->objectClass = $tmpMsg->objectClass;
								$messaging->personId = $tmpMsg->personId;
								$messaging->providerId = $tmpMsg->providerId;
								$xmlTmpMessage = new SimpleXMLElement($tmpMsg->rawMessage);
								$lastName = (string)$xmlTmpMessage->Body->NewRx->Patient->Name->LastName;
								$firstName = (string)$xmlTmpMessage->Body->NewRx->Patient->Name->FirstName;
								$messageInfo = $lastName.', '.$firstName;
								$drugDescription = (string)$xmlTmpMessage->Body->NewRx->MedicationPrescribed->DrugDescription;
								$datePrescribed = date('m/d/Y',strtotime((string)$xmlTmpMessage->Body->NewRx->MedicationPrescribed->WrittenDate));
								$messageInfo .= ' - '.$drugDescription.' #'.$datePrescribed;
								$messaging->note = 'Status received for '.$messageInfo;
							}
							$messaging->note .= "\n".$code.':'.$description;
						}
						else if ($key == 'error') {
							$relatesToMessageId = (string)$message->relatesToMessageId;
							$messageId = (string)$message->messageId;
							$code = (string)$message->code;
							$description = (string)$message->description;

							$messaging = new Messaging();
							$messaging->messageType = 'Error';
							$messaging->note = 'Error received for unknown messageId: '.$relatesToMessageId;

							$tmpMsg = new Messaging();
							$tmpMsg->messagingId = $relatesToMessageId;
							if ($tmpMsg->populate()) { // populate for newRx details
								$tmpMsg->status = 'Sent and Verified';
								$x = explode('(',$tmpMsg->note);
								$tmpMsg->note = 'newRx';
								if ($tmpMsg->objectClass == 'MedicationRefillResponse') {
									$tmpMsg->note = 'Refill response';
								}
								$tmpMsg->note .= ' sent and verified';
								if (isset($x[1])) {
									unset($x[0]);
									$tmpMsg->note .= ' ('.implode('(',$x);
								}
								$tmpMsg->unresolved = 0;
								$tmpMsg->persist();
								$messaging->auditId = $tmpMsg->auditId;
								$messaging->objectId = $tmpMsg->objectId;
								$messaging->objectClass = $tmpMsg->objectClass;
								$messaging->personId = $tmpMsg->personId;
								$messaging->providerId = $tmpMsg->providerId;
								$xmlTmpMessage = new SimpleXMLElement($tmpMsg->rawMessage);
								$lastName = (string)$xmlTmpMessage->Body->NewRx->Patient->Name->LastName;
								$firstName = (string)$xmlTmpMessage->Body->NewRx->Patient->Name->FirstName;
								$messageInfo = $lastName.', '.$firstName;
								$drugDescription = (string)$xmlTmpMessage->Body->NewRx->MedicationPrescribed->DrugDescription;
								$datePrescribed = date('m/d/Y',strtotime((string)$xmlTmpMessage->Body->NewRx->MedicationPrescribed->WrittenDate));
								$messageInfo .= ' - '.$drugDescription.' #'.$datePrescribed;
								$messaging->note = 'Error received for '.$messageInfo;
							}
							$messaging->note .= "\n".$code.':'.$description;
						}
						else {
							continue;
						}
						$messaging->rawMessage = $rawMessage;
						$messaging->rawMessageResponse = base64_decode((string)$message->rawMessageResponse);
						$messaging->status = 'Received';
						$messaging->dateStatus = date('Y-m-d H:i:s');
						$messaging->persist();
						$ret++;
					}
				}
				if ($ret > 0) {
					self::sendPullResponse();
				}
			}
			catch (Exception $e) {
				$error = __('There was an error, the response couldn\'t be parsed as XML: '.$output);
				trigger_error($error,E_USER_NOTICE);
			}
		}
		else {
			$error = __('There was an error connecting to HealthCloud. Please try again or contact the system administrator.');
			trigger_error('Curl error connecting to healthcare: '.curl_error($ch),E_USER_NOTICE);
		}
		curl_close ($ch);
		return $ret;
	}

	public static function sendPullResponse() {
		$ch = curl_init();
		$ePrescribeURL = Zend_Registry::get('config')->healthcloud->URL;
		$ePrescribeURL .= 'ss-manager.raw/pull-inbounds-response?apiKey='.Zend_Registry::get('config')->healthcloud->apiKey;
		curl_setopt($ch,CURLOPT_URL,$ePrescribeURL);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		$output = curl_exec($ch);
		$error = '';
		$ret = true;
		if (curl_errno($ch)) {
			$error = __('There was an error connecting to HealthCloud. Please try again or contact the system administrator.');
			trigger_error('Curl error connecting to healthcare: '.curl_error($ch),E_USER_NOTICE);
		}
		curl_close ($ch);
		return $ret;
	}

	public static function sendResponse($data,Messaging $messaging) {
		$messageInfo = '';
		if (strlen($data['message']) > 0) {
			$xmlMessage = new SimpleXMLElement($data['message']);
			$lastName = (string)$xmlMessage->Body->RefillRequest->Patient->Name->LastName;
			$firstName = (string)$xmlMessage->Body->RefillRequest->Patient->Name->FirstName;
			$messageInfo = $lastName.', '.$firstName;
			$description = (string)$xmlMessage->Body->RefillRequest->MedicationPrescribed->DrugDescription;
			$datePrescribed = date('m/d/Y',strtotime((string)$xmlMessage->Body->RefillRequest->MedicationPrescribed->WrittenDate));
			$messageInfo .= ' - '.$description.' #'.$datePrescribed;
		}

		$tmpMessaging = $messaging;
		$messaging = new Messaging();
//		$messaging->messagingId = $messageId;
//		$messaging->populate();
		$messaging->messageType = 'RefillResponse';
		$messaging->objectId = $tmpMessaging->messagingId;
		$messaging->objectClass = 'MedicationRefillResponse';
		$messaging->status = 'Sending';
		$messaging->note = 'Sending refill response ('.$messageInfo.')';
		$messaging->dateStatus = date('Y-m-d H:i:s');
		$messaging->auditId = $tmpMessaging->auditId;
		$messaging->persist();

		if (isset($data['medicationId'])) {
			$medicationId = $data['medicationId'];
			unset($data['medicationId']);
			$medication = new Medication();
			$medication->medicationId = $medicationId;
			if ($medication->populate()) {
				$medData = array();
				$medData['description'] = $medication->description;
				$medData['strength'] = $medication->strength;
				$medData['strengthUnits'] = '00';//$medication->unit;
				$medData['quantity'] = $medication->quantity;
				$medData['quantityUnits'] = '00';
				$medData['directions'] = $medication->directions;
				$qualifier = 'R';
				if ($medication->prn) {
					$qualifier = 'PRN';
				}
				$medData['refills'] = $medication->refills;
				$medData['refillsUnits'] = $qualifier;
				$medData['substitutions'] = $medication->substitution;
				$medData['writtenDate'] = date('Ymd',strtotime($medication->datePrescribed));
				$data['Medication'] = $medData;
			}
		}

		$query = http_build_query($data);
		$ch = curl_init();
		$ePrescribeURL = Zend_Registry::get('config')->healthcloud->URL;
		$ePrescribeURL .= 'ss-manager.raw/receive-response?apiKey='.Zend_Registry::get('config')->healthcloud->apiKey;
		curl_setopt($ch,CURLOPT_URL,$ePrescribeURL);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$query);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		$output = curl_exec($ch);
		$error = '';
		$rawMessage = '';

		$messaging->status = 'Sent';
		$messaging->note = 'Refill response pending';
		$messaging->unresolved = 1;
		if (!curl_errno($ch)) {
			try {
				$xml = new SimpleXMLElement($output);
				if (isset($xml->error)) {
					$errorCode = (string)$xml->error->code;
					$errorMsg = (string)$xml->error->message;
					if (isset($xml->error->errorCode)) {
						$errorCode = (string)$xml->error->errorCode;
					}
					if (isset($xml->error->errorMsg)) {
						$errorMsg = (string)$xml->error->errorMsg;
					}
					$error = $errorMsg;
					trigger_error('There was an error sending refill response, Error code: '.$errorCode.' Error Message: '.$errorMsg,E_USER_NOTICE);
				}
				else if (isset($xml->status)) {
					$messaging->note = 'Refill response awaiting confirmation';
					if ((string)$xml->status->code == '010') { // value 000 is for free standing error?
						$messaging->status .= ' and Verified';
						$messaging->note = 'Refill response sent and verified';
						$messaging->unresolved = 0;
					}
				}
				else {
					$error = 'Unrecognized HealthCloud response: '.$output;
				}
				if (isset($xml->rawMessage)) {
					$messaging->rawMessage = base64_decode((string)$xml->rawMessage);
					$messaging->rawMessageResponse = base64_decode((string)$xml->rawMessageResponse);
				}
			}
			catch (Exception $e) {
				$error = __('There was an error, the response couldn\'t be parsed as XML: '.$output);
				trigger_error($error,E_USER_NOTICE);
			}
		}
		else {
			$error = __('There was an error connecting to HealthCloud. Please try again or contact the system administrator.');
			trigger_error('Curl error connecting to healthcare: '.curl_error($ch),E_USER_NOTICE);
		}
		$messaging->note .= ' ('.$messageInfo.')';
		curl_close ($ch);
		$ret = true;
		if (strlen($error) > 0) {
			$messaging->status = 'Error';
			$messaging->note = $error;
			$ret = $error;
		}
		if ($messaging->resend) {
			$messaging->resend = 0;
		}
		$messaging->retries++;
		$messaging->dateStatus = date('Y-m-d H:i:s');
		$messaging->persist();
		return $ret;
	}

}
