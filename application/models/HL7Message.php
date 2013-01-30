<?php
/*****************************************************************************
*       HL7Message.php
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


class HL7Message extends WebVista_Model_ORM {

	protected $hl7MessageId;
	protected $message;
	protected $type;
	protected $_table = "hl7Messages";
	protected $_primaryKeys = array("hl7MessageId");


	public static function generateTestData() {
		$message = new self();
		$message->message = 'PID|1234|doe^john^h';
		$message->type = 'HL7';
		$message->persist();
	}

	public static function generateMSHData(Audit $audit) {
		$msh = array();
		$msh['field_separator'] = '|'; // 1
		$msh['char_encoding'] = '^~\&'; // 4
		$msh['sending_app'] = 'CLEARHEALTH'; // 180
		$msh['sending_facility'] = 'CLEARHEALTH'; // 180
		$msh['receiving_app'] = 'ClearHealth'; // 180
		$msh['receiving_facility'] = 'ClearHealth'; // 180
		$msh['message_datetime'] = date('YmdHi'); // 26
		$msh['security'] = ''; // 40
		$msh['message_type'] = 'ADT'; // 7 = message_type + event_type
		$msh['event_type'] = self::_getEventType($audit);
		$msh['message_control_id'] = $audit->auditId; // 20
		$msh['processing_id'] = 'P'; // 3, options: D (debugging), P (production), T (training)
		$msh['version_id'] = '2.3'; // 8
		$msh['sequence_number'] = ''; // 15
		$msh['continuation_pointer'] = ''; // 180
		$msh['accept_ack_type'] = 'AL'; // 2, option: AL (always), NE (never), ER (error/reject conditions only), SU (successful completion only)
		$msh['app_ack_type'] = 'NE'; // 2
		$msh['country_code'] = ''; // 2
		$msh['char_set'] = ''; // 6
		$msh['principal_lang_message'] = ''; // 60
		return $msh;
	}

	protected static function _getEventType(Audit $audit) {
		$type = 'A08';
		return $type;
	}

	public static function MSHEventTypes() {
		$types = array();
		$types['A01'] = 'ADT/ACK - Admit / visit notification';
		$types['A02'] = 'ADT/ACK - Transfer a patient';
		$types['A03'] = 'ADT/ACK - Discharge/end visit';
		$types['A04'] = 'ADT/ACK - Register a patient';
		$types['A05'] = 'ADT/ACK - Pre-admit a patient';
		$types['A06'] = 'ADT/ACK - Change an outpatient to an inpatient';
		$types['A07'] = 'ADT/ACK - Change an inpatient to an outpatient';
		$types['A08'] = 'ADT/ACK - Update patient information';
		$types['A09'] = 'ADT/ACK - Patient departing - tracking';
		$types['A10'] = 'ADT/ACK - Patient arriving - tracking';
		$types['A11'] = 'ADT/ACK - Cancel admit/visit notification';
		$types['A12'] = 'ADT/ACK - Cancel transfer';
		$types['A13'] = 'ADT/ACK - Cancel discharge/end visit';
		$types['A14'] = 'ADT/ACK - Pending admit';
		$types['A15'] = 'ADT/ACK - Pending transfer';
		$types['A16'] = 'ADT/ACK - Pending discharge';
		$types['A17'] = 'ADT/ACK - Swap patients';
		$types['A18'] = 'ADT/ACK - Merge patient information';
		$types['A19'] = 'QRY/ADR - Patient query';
		$types['A20'] = 'ADT/ACK - Bed status update';
		$types['A21'] = 'ADT/ACK - Patient goes on a "leave of absence"';
		$types['A22'] = 'ADT/ACK - Patient returns from a "leave of absence"';
		$types['A23'] = 'ADT/ACK - Delete a patient record';
		$types['A24'] = 'ADT/ACK - Link patient information';
		$types['A25'] = 'ADT/ACK - Cancel pending discharge';
		$types['A26'] = 'ADT/ACK - Cancel pending transfer';
		$types['A27'] = 'ADT/ACK - Cancel pending admit';
		$types['A28'] = 'ADT/ACK - Add person information';
		$types['A29'] = 'ADT/ACK - Delete person information';
		$types['A30'] = 'ADT/ACK - Merge person information';
		$types['A31'] = 'ADT/ACK - Update person information';
		$types['A32'] = 'ADT/ACK - Cancel patient arriving - tracking';
		$types['A33'] = 'ADT/ACK - Cancel patient departing - tracking';
		$types['A34'] = 'ADT/ACK - Merge patient information - patient ID only';
		$types['A35'] = 'ADT/ACK - Merge patient information - account number only';
		$types['A36'] = 'ADT/ACK - Merge patient information - patient ID and account number';
		$types['A37'] = 'ADT/ACK - Unlink patient information';
		$types['A38'] = 'ADT/ACK - Cancel pre-admit';
		$types['A39'] = 'ADT/ACK - Merge person - external ID';
		$types['A40'] = 'ADT/ACK - Merge patient - internal ID';
		$types['A41'] = 'ADT/ACK - Merge account - patient account number';
		$types['A42'] = 'ADT/ACK - Merge visit - visit number';
		$types['A43'] = 'ADT/ACK - Move patient information - internal ID';
		$types['A44'] = 'ADT/ACK - Move account information - patient account number';
		$types['A45'] = 'ADT/ACK - Move visit information - visit number';
		$types['A46'] = 'ADT/ACK - Change external ID';
		$types['A47'] = 'ADT/ACK - Change internal ID';
		$types['A48'] = 'ADT/ACK - Change alternate patient ID';
		$types['A49'] = 'ADT/ACK - Change patient account number';
		$types['A50'] = 'ADT/ACK - Change visit number';
		$types['A51'] = 'ADT/ACK - Change alternate visit ID';
		$types['C01'] = 'CRM - Register a patient on a clinical trial';
		$types['C02'] = 'CRM - Cancel a patient registration on clinical trial (for clerical mistakes only)';
		$types['C03'] = 'CRM - Correct/update registration information';
		$types['C04'] = 'CRM - Patient has gone off a clinical trial';
		$types['C05'] = 'CRM - Patient enters phase of clinical trial';
		$types['C06'] = 'CRM - Cancel patient entering a phase (clerical mistake)';
		$types['C07'] = 'CRM - Correct/update phase information';
		$types['C08'] = 'CRM - Patient has gone off phase of clinical trial';
		$types['C09'] = 'CSU - Automated time intervals for reporting, like monthly';
		$types['C10'] = 'CSU - Patient completes the clinical trial';
		$types['C11'] = 'CSU - Patient completes a phase of the clinical trial';
		$types['C12'] = 'CSU - Update/correction of patient order/result information';
		$types['CNQ'] = 'QRY/EQQ/SPQ/VQQ/RQQ - Cancel query';
		$types['G01'] = 'PGL/ACK - Patient goal';
		$types['I01'] = 'RQI/RPI - Request for insurance information';
		$types['I02'] = 'RQI/RPL - Request/receipt of patient selection display list';
		$types['I03'] = 'RQI/RPR - Request/receipt of patient selection list';
		$types['I04'] = 'RQD/RPI - Request for patient demographic data';
		$types['I05'] = 'RQC/RCI - Request for patient clinical information';
		$types['I06'] = 'RQC/RCL - Request/receipt of clinical data listing';
		$types['I07'] = 'PIN/ACK - Unsolicited insurance information';
		$types['I08'] = 'RQA/RPA - Request for treatment authorization information';
		$types['I09'] = 'RQA/RPA - Request for modification to an authorization';
		$types['I10'] = 'RQA/RPA - Request for resubmission of an authorization';
		$types['I11'] = 'RQA/RPA - Request for cancellation of an authorization';
		$types['I12'] = 'REF/RRI - Patient referral';
		$types['I13'] = 'REF/RRI - Modify patient referral';
		$types['I14'] = 'REF/RRI - Cancel patient referral';
		$types['I15'] = 'REF/RRI - Request patient referral status';
		$types['M01'] = 'MFN/MFK - Master file not otherwise specified (for backward compatibility only)';
		$types['M02'] = 'MFN/MFK - Master file - Staff Practioner';
		$types['M03'] = 'MFN/MFK - Master file - Test/Observation (for backward compatibility only)';
		$types['varies'] = 'MFQ/MFR - Master files query (use event same as asking for e.g., M05 - location)';
		$types['M04'] = 'MFN/MFK - Master files charge description';
		$types['M05'] = 'MFN/MFK - Patient location master file';
		$types['M06'] = 'MFN/MFK - Clinical study with phases and schedules master file';
		$types['M07'] = 'MFN/MFK - Clinical study without phases but with schedules master file';
		$types['M08'] = 'MFN/MFK - Test/observation (Numeric) master file';
		$types['M09'] = 'MFN/MFK - Test/Observation (Categorical) master file';
		$types['M10'] = 'MFN/MFK - Test /observation batteries master file';
		$types['M11'] = 'MFN/MFK - Test/calculated observations master file';
		$types['O01'] = 'ORM - Order message (also RDE, RDS, RGV, RAS)';
		$types['O02'] = 'ORR - Order response (also RRE, RRD, RRG, RRA)';
		$types['P01'] = 'BAR/ACK - Add patient accounts';
		$types['P02'] = 'BAR/ACK - Purge patient accounts';
		$types['P03'] = 'DFT/ACK - Post detail financial transaction';
		$types['P04'] = 'QRY/DSP - Generate bill and A/R statements';
		$types['P05'] = 'BAR/ACK - Update account';
		$types['P06'] = 'BAR/ACK - End account';
		$types['P07'] = 'PEX - Unsolicited initial individual product experience report';
		$types['P08'] = 'PEX - Unsolicited update individual product experience report';
		$types['P09'] = 'SUR - Summary product experience report';
		$types['PC1'] = 'PPR - PC/ Problem Add';
		$types['PC2'] = 'PPR - PC/ Problem Update';
		$types['PC3'] = 'PPR - PC/ Problem Delete';
		$types['PC4'] = 'PRQ - PC/ Problem Query';
		$types['PC5'] = 'PRR - PC/ Problem Response';
		$types['PC6'] = 'PGL - PC/ Goal Add';
		$types['PC7'] = 'PGL - PC/ Goal Update';
		$types['PC8'] = 'PGL - PC/ Goal Delete';
		$types['PC9'] = 'PGQ - PC/ Goal Query';
		$types['PCA'] = 'PGR - PC/ Goal Response';
		$types['PCB'] = 'PPP - PC/ Pathway (Problem-Oriented) Add';
		$types['PCC'] = 'PPP - PC/ Pathway (Problem-Oriented) Update';
		$types['PCD'] = 'PPP - PC/ Pathway (Problem-Oriented) Delete';
		$types['PCE'] = 'PTQ - PC/ Pathway (Problem-Oriented) Query';
		$types['PCF'] = 'PTR - PC/ Pathway (Problem-Oriented) Query Response';
		$types['PCG'] = 'PPG - PC/ Pathway (Goal-Oriented) Add';
		$types['PCH'] = 'PPG - PC/ Pathway (Goal-Oriented) Update';
		$types['PCJ'] = 'PPG - PC/ Pathway (Goal-Oriented) Delete';
		$types['PCK'] = 'PTU - PC/ Pathway (Goal-Oriented) Query';
		$types['PCL'] = 'PTV - PC/ Pathway (Goal-Oriented) Query Response';
		$types['Q01'] = 'QRY/DSR - Query sent for immediate response';
		$types['Q02'] = 'QRY/QCK - Query sent for deferred response';
		$types['Q03'] = 'DSR/ACK - Deferred response to a query';
		$types['Q05'] = 'UDM/ACK - Unsolicited display update message';
		$types['Q06'] = 'OSQ/OSR - Query for order status';
		$types['R01'] = 'ORU/ACK - Unsolicited transmission of an observation message';
		$types['R02'] = 'QRY - Query for results of observation';
		$types['R03'] = 'QRY/DSR Display-oriented results, query/unsol. update (for backward compatibility only)';
		$types['R04'] = 'ORF - Response to query; transmission of requested observation';
		$types['R05'] = 'QRY/DSR-query for display results';
		$types['R06'] = 'UDM-unsolicited update/display results';
		$types['RAR'] = 'RAR - Pharmacy administration information query response';
		$types['RDR'] = 'RDR - Pharmacy dispense information query response';
		$types['RER'] = 'RER - Pharmacy encoded order information query response';
		$types['RGR'] = 'RGR - Pharmacy dose information query response';
		$types['ROR'] = 'ROR - Pharmacy prescription order query response';
		$types['S01'] = 'SRM/SRR - Request new appointment booking';
		$types['S02'] = 'SRM/SRR - Request appointment rescheduling';
		$types['S03'] = 'SRM/SRR - Request appointment modification';
		$types['S04'] = 'SRM/SRR - Request appointment cancellation';
		$types['S05'] = 'SRM/SRR - Request appointment discontinuation';
		$types['S06'] = 'SRM/SRR - Request appointment deletion';
		$types['S07'] = 'SRM/SRR - Request addition of service/resource on appointment';
		$types['S08'] = 'SRM/SRR - Request modification of service/resource on appointment';
		$types['S09'] = 'SRM/SRR - Request cancellation of service/resource on appointment';
		$types['S10'] = 'SRM/SRR - Request discontinuation of service/resource on appointment';
		$types['S11'] = 'SRM/SRR - Request deletion of service/resource on appointment';
		$types['S12'] = 'SIU/ACK - Notification of new appointment booking';
		$types['S13'] = 'SIU/ACK - Notification of appointment rescheduling';
		$types['S14'] = 'SIU/ACK - Notification of appointment modification';
		$types['S15'] = 'SIU/ACK - Notification of appointment cancellation';
		$types['S16'] = 'SIU/ACK - Notification of appointment discontinuation';
		$types['S17'] = 'SIU/ACK - Notification of appointment deletion';
		$types['S18'] = 'SIU/ACK - Notification of addition of service/resource on appointment';
		$types['S19'] = 'SIU/ACK - Notification of modification of service/resource on appointment';
		$types['S20'] = 'SIU/ACK - Notification of cancellation of service/resource on appointment';
		$types['S21'] = 'SIU/ACK - Notification of discontinuation of service/resource on appointment';
		$types['S22'] = 'SIU/ACK - Notification of deletion of service/resource on appointment';
		$types['S23'] = 'SIU/ACK - Notification of blocked schedule time slot(s)';
		$types['S24'] = 'SIU/ACK - Notification of opened ("unblocked") schedule time slot(s)';
		$types['S25'] = 'SQM/SQR - Schedule query message and response';
		$types['S26'] = 'Notification that patient did not show up for schedule appointment';
		$types['T01'] = 'MDM/ACK - Original document notification';
		$types['T02'] = 'MDM/ACK - Original document notification and content';
		$types['T03'] = 'MDM/ACK - Document status change notification';
		$types['T04'] = 'MDM/ACK - Document status change notification and content';
		$types['T05'] = 'MDM/ACK - Document addendum notification';
		$types['T06'] = 'MDM/ACK - Document addendum notification and content';
		$types['T07'] = 'MDM/ACK - Document edit notification';
		$types['T08'] = 'MDM/ACK - Document edit notification and content';
		$types['T09'] = 'MDM/ACK - Document replacement notification';
		$types['T10'] = 'MDM/ACK - Document replacement notification and content';
		$types['T11'] = 'MDM/ACK - Document cancel notification';
		$types['T12'] = 'QRY/DOC - Document query';
		$types['V01'] = 'VXQ - Query for vaccination record';
		$types['V02'] = 'VXX - Response to vaccination query returning multiple PID matches';
		$types['V03'] = 'VXR - Vaccination record response';
		$types['V04'] = 'VXU - Unsolicited vaccination record update';
		$types['W01'] = 'ORU - Waveform result, unsolicited transmission of requested information';
		$types['W02'] = 'QRF - Waveform result, response to query';
		$types['X01'] = 'PEX - Product experience';
		return $types;
	}

}
