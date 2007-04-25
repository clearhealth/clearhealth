<?php
/**
 * Contains a list of block elements required that may be contained in an 837p
 *
 * @access private
 */

/**
 * Require defaults
 */
require_once dirname(__FILE__) . '/x12_default.elements.php';


/**
 * 837p specific elements
 */
$knownElements = array(
	'ISA' => array(
		'name'   => 'Interchange Control Header',
		'id'     => 'ISA',
		'fields' => array(
			array(
				'name'	=> 'Authorization Information Qualifier',
				'id'	=> 'AuthorizationInformationQualifier',
				'code'	=> 'ISA01',
			),
			array(
				'name'	=> 'Authorization Information',
				'id'	=> 'AuthorizationInformation',
				'code'	=> 'ISA02',
			),
			array(
				'name'	=> 'Security Information Qualifier',
				'id'	=> 'SecurityInformationQualifier',
				'code'	=> 'ISA03',
			),
			array(
				'name'	=> 'Security Information',
				'id'	=> 'SecurityInformation',
				'code'	=> 'ISA04',
			),
			array(
				'name'	=> 'Interchange ID Qualifier',
				'id'	=> 'InterchangeIDQualifier_ISA05',
				'code'	=> 'ISA05',
			),
			array(
				'name'	=> 'Interchange Sender ID',
				'id'	=> 'InterchangeSenderID',
				'code'	=> 'ISA06',
			),
			array(
				'name'	=> 'Interchange ID Qualifier',
				'id'	=> 'InterchangeIDQualifier_ISA07',
				'code'	=> 'ISA07',
			),
			array(
				'name'	=> 'Interchange Receiver ID',
				'id'	=> 'InterchangeReceiverID',
				'code'	=> 'ISA08',
			),
			array(
				'name'	=> 'Interchange Date',
				'id'	=> 'InterchangeDate',
				'code'	=> 'ISA09',
			),
			array(
				'name'	=> 'Interchange Time',
				'id'	=> 'InterchangeTime',
				'code'	=> 'ISA10',
			),
			array(
				'name'	=> 'Interchange Control Standards Identifier',
				'id'	=> 'InterchangeControlStandardsIdentifier',
				'code'	=> 'ISA11',
			),
			array(
				'name'	=> 'Interchange Control Version Number',
				'id'	=> 'InterchangeControlVersionNumber',
				'code'	=> 'ISA12',
			),
			array(
				'name'	=> 'Interchange Control Number',
				'id'	=> 'InterchangeControlNumber',
				'code'	=> 'ISA13',
			),
			array(
				'name'	=> 'Acknowledgment Requested',
				'id'	=> 'AcknowledgmentRequested',
				'code'	=> 'ISA14',
			),
			array(
				'name'	=> 'Usage Indicator',
				'id'	=> 'UsageIndicator',
				'code'	=> 'ISA15',
			),
			array(
				'name'	=> 'Component Element Separator',
				'id'	=> 'ComponentElementSeparator',
				'code'	=> 'ISA16',
			)
		)
	),
	'GS' => array(
		'name'   => 'Unknown: GS',
		'id'     => 'GS',
		'fields' => array()
	),
	'ST' => array(
		'name'   => 'Unknown: ST',
		'id'     => 'ST',
		'fields' => array()
	),
	'BHT' => array(
		'name' => 'Unknown: BHT',
		'id' => 'BHT',
		'fields' => array()
	),
	'REF' => array(
		'name'	=> 'Reference identification',
		'id'	=> 'ReferenceIdentification',
		'fields'=> array(
			array(
				'name'	=> 'Reference Ident Qual',
				'id'	=> 'ReferenceIdentQual',
				'code'	=> 'REF01',
			),
			array(
				'name'	=> 'Reference Ident',
				'id'	=> 'ReferenceIdent',
				'code'	=> 'REF02'
			)
		)
	),
	'DTM' => array(
		'name'	=> 'Date/Time Reference',
		'id'	=> 'DateTime',
		'fields'=> array(
			array(
				'name'	=> 'Date/Time Qualifier',
				'id'	=> 'DateTimeQualifier',
				'code'	=> 'DTM01',
			),
			array(
				'name'	=> 'Date',
				'id'	=> 'Date',
				'code'	=> 'DTM02',
			)
		)
	),
	'N1' => array(
		'name'	=> 'Name',
		'id'	=> 'Name',
		'fields'=> array(
			array(
				'name'	=> 'Entity ID Code',
				'id'	=> 'EntityIDCode',
				'code'	=> 'N101',
			),
			array(
				'name'	=> 'Name',
				'id'	=> 'Name',
				'code'	=> 'N102',
			),
			array(
				'name'	=> 'ID Code Qualifier',
				'id'	=> 'IDCodeQualifier',
				'code'	=> 'N103',
			),
			array(
				'name'	=> 'ID Code',
				'id'	=> 'IDCode',
				'code'	=> 'N104',
			),
		)
	),
	'N3' => array(
		'name'	=> 'Address Information',
		'id'	=> 'Address_Street',
		'fields'=> array(
			array(
				'name'	=> 'Address Infromation',
				'id'	=> 'AddressInfo1',
				'code'	=> 'N301'
			),
			array(
				'name'	=> 'Address Information',
				'id'	=> 'AddressInfo2',
				'code'	=> 'N302'
			)
		)
	),
	'N4' => array(
		'name'	=> 'Geographic Location',
		'id'	=> 'Address_CityState',
		'fields'=> array(
			array(
				'name'	=> 'City Name',
				'id'	=> 'City',
				'code'	=> 'N401'
			),
			array(
				'name'	=> 'State or Prov Code',
				'id'	=> 'StateCode',
				'code'	=> 'N402'
			),
			array(
				'name'	=> 'Postal Code',
				'id'	=> 'PostalCode',
				'code'	=> 'N403'
			)
		)
	),
	'LX' => array(
		'name'	=> 'Assigned Number',
		'id'	=> 'AssignedNumber',
		'fields'=> array(
			array(
				'name'	=> 'Assigned Number',
				'id'	=> 'AssignedNumber',
				'code'	=> 'LX01'
			)
		)
	),
	'TS3' => array(
		'name'	=> 'Transaction Statistics',
		'id'	=> 'TransactionStatistics',
		'fields'=> array(
			array(
				'name'	=> 'Reference Ident',
				'id'	=> 'ReferenceIdent',
				'code'	=> 'TS301'
			),
			array(
				'name'	=> 'Facility Code',
				'id'	=> 'FacilityCode',
				'code'	=> 'TS302',
			),
			array(
				'name'	=> 'Date',
				'id'	=> 'Date',
				'code'	=> 'TS303'
			),
			array(
				'name'	=> 'Quanity',
				'id'	=> 'quanity',
				'code'	=> 'TS304'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount',
				'code'	=> 'TS305'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount2',
				'code'	=> 'TS306'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount3',
				'code'	=> 'TS307'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount4',
				'code'	=> 'TS308'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount5',
				'code'	=> 'TS309'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount6',
				'code'	=> 'TS310'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount7',
				'code'	=> 'TS311'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount8',
				'code'	=> 'TS312'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount9',
				'code'	=> 'TS313'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount10',
				'code'	=> 'TS314'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount11',
				'code'	=> 'TS315'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount12',
				'code'	=> 'TS316'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount13',
				'code'	=> 'TS317'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount14',
				'code'	=> 'TS318'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount15',
				'code'	=> 'TS319'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount16',
				'code'	=> 'TS320'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount17',
				'code'	=> 'TS321'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount18',
				'code'	=> 'TS322'
			),
			array(
				'name'	=> 'Quanity',
				'id'	=> 'Quanity2',
				'code'	=> 'TS323'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount19',
				'code'	=> 'TS324'
			),
		)
	),
	'TS2' => array(
		'name'	=> 'Transaction Supplemental Statistics',
		'id'	=> 'TransactionSupplementalStatistics',
		'fields'=> array(
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount',
				'code'	=> 'TS201'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount2',
				'code'	=> 'TS202'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount3',
				'code'	=> 'TS203'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount4',
				'code'	=> 'TS204'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount5',
				'code'	=> 'TS205'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount6',
				'code'	=> 'TS206'
			),
		)
	),
	'CLP' => array(
		'name'	=> 'Claim Level Data',
		'id'	=> 'ClaimLevelData',
		'fields'=> array(
			array(
				'name'	=> 'Claim Submt Identifier',
				'id'	=> 'ClaimSubmtIdentifier',
				'code'	=> 'CLP01'
			),
			array(
				'name'	=> 'Claim Status Code',
				'id'	=> 'ClaimStatusCode',
				'code'	=> 'CLP02'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount',
				'code'	=> 'CLP03',
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount2',
				'code'	=> 'CLP04',
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount3',
				'code'	=> 'CLP05',
			),
			array(
				'name'	=> 'Claim File Ind Code',
				'id'	=> 'ClaimFileIndCode',
				'code'	=> 'CLP06',
			),
			array(
				'name'	=> 'Reference Ident',
				'id'	=> 'ReferenceIdent',
				'code'	=> 'CLP07',
			),
			array(
				'name'	=> 'Facility Code',
				'id'	=> 'FacilityCode',
				'code'	=> 'CLP08',
			),
			array(
				'name'	=> 'Claim Freq Type Code',
				'id'	=> 'ClaimFreqTypeCode',
				'code'	=> 'CLP09',
			),
			array(
				'name'	=> 'Patient Status Code',
				'id'	=> 'PatientStatusCode',
				'code'	=> 'CLP10',
			),
			array(
				'name'	=> 'DRG Code',
				'id'	=> 'DRGCode',
				'code'	=> 'CLP11',
			),
			array(
				'name'	=> 'Quanity',
				'id'	=> 'Quanity',
				'code'	=> 'CLP12',
			),
			array(
				'name'	=> 'Percent',
				'id'	=> 'Percent',
				'code'	=> 'CLP13',
			),
		)
	),
	'CAS' => array(
		'name'	=> 'Claims Adjustment',
		'id'	=> 'ClaimsAdjustment',
		'fields'=> array(
			array(
				'name'	=> 'Claim Adj Group Code',
				'id'	=> 'ClaimAdjGroupCode',
				'code'	=> 'CAS01'
			),
			array(
				'name'	=> 'Claim Adj Reason Code',
				'id'	=> 'ClaimAdhReasonCode',
				'code'	=> 'CAS02'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount',
				'code'	=> 'CAS03'
			),
			array(
				'name'	=> 'Quanity',
				'id'	=> 'Quanity',
				'code'	=> 'CAS04',
			),
			array(
				'name'	=> 'Claim Adj Reason Code',
				'id'	=> 'ClaimAdjReasonCode2',
				'code'	=> 'CAS05',
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount2',
				'code'	=> 'CAS06'
			),
			array(
				'name'	=> 'Quanity',
				'id'	=> 'Quanity2',
				'code'	=> 'CAS07',
			),
			array(
				'name'	=> 'Claim Adj Reason Code',
				'id'	=> 'ClaimAdjReasonCode3',
				'code'	=> 'CAS08',
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount3',
				'code'	=> 'CAS09'
			),
			array(
				'name'	=> 'Quanity',
				'id'	=> 'Quanity3',
				'code'	=> 'CAS10',
			),
		)
	),
	'NM1' => array(
		'name'	=> 'Individual or Organizational Name',
		'id'	=> 'IndividualName',
		'fields'=> array(
			array(
				'name'	=> 'Enitity ID Code',
				'id'	=> 'EnitityIDCode',
				'code'	=> 'NM101'
			),
			array(
				'name'	=> 'Entity Type Qualifier',
				'id'	=> 'EntityTypeQualifier',
				'code'	=> 'NM102'
			),
			array(
				'name'	=> 'Name Last/Org Name',
				'id'	=> 'NameLast',
				'code'	=> 'NM103',
			),
			array(
				'name'	=> 'Name First',
				'id'	=> 'NameFirst',
				'code'	=> 'NM104'
			),
			array(
				'name'	=> 'Name Middle',
				'id'	=> 'NameMiddle',
				'code'	=> 'NM105'
			),
			array(
				'name'	=> 'Name Prefix',
				'id'	=> 'NamePrefix',
				'code'	=> 'NM106'
			),
			array(
				'name'	=> 'Name Suffix',
				'id'	=> 'NameSuffix',
				'code'	=> 'NM107'
			),
			array(
				'name'	=> 'ID Code Qualifier',
				'id'	=> 'IDCodeQualifier',
				'code'	=> 'NM108',
			),
			array(
				'name'	=> 'ID Code',
				'id'	=> 'IDCode',
				'code'	=> 'NM109'
			)
		)
	),
	'MIA' => array(
		'name'	=> 'Medicare Inpatient Adjudication',
		'id'	=> 'MedicareInpatientAdjudication',
		'fields'=> array(
			array(
				'name'	=> 'Quantity',
				'id'	=> 'Quantity',
				'code'	=> 'MIA01'
			),
			array(
				'name'	=> 'Quantity',
				'id'	=> 'Quantity2',
				'code'	=> 'MIA02'
			),
			array(
				'name'	=> 'Quantity',
				'id'	=> 'Quantity3',
				'code'	=> 'MIA03'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount',
				'code'	=> 'MIA04'
			),
		)
	),
	'QTY' => array(
		'name'	=> 'Quantity',
		'id'	=> 'Quantity',
		'fields'=> array(
			array(
				'name'	=> 'Quantity Qualifier',
				'id'	=> 'QuantityQualifier',
				'code'	=> 'QTY01'
			),
			array(
				'name'	=> 'Quantity',
				'id'	=> 'Quantity',
				'code'	=> 'QTY02'
			)
		)
	),
	'MOA' => array(
		'name'	=> 'Medicare Outpatient Adjudication',
		'id'	=> 'MedicareOutpatientAdjudication',
		'fields'=> array(
			array(
				'name'	=> 'Percent',
				'id'	=> 'Percent',
				'code'	=> 'MOA01'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount',
				'code'	=> 'MOA02'
			),
			array(
				'name'	=> 'Reference Ident',
				'id'	=> 'ReferenceIdent',
				'code'	=> 'MOA03'
			),
			array(
				'name'	=> 'Reference Ident',
				'id'	=> 'ReferenceIdent2',
				'code'	=> 'MOA04'
			),
			array(
				'name'	=> 'Reference Ident',
				'id'	=> 'ReferenceIdent3',
				'code'	=> 'MOA05'
			),
			array(
				'name'	=> 'Reference Ident',
				'id'	=> 'ReferenceIdent4',
				'code'	=> 'MOA06'
			),
			array(
				'name'	=> 'Reference Ident',
				'id'	=> 'ReferenceIdent5',
				'code'	=> 'MOA04'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount2',
				'code'	=> 'MOA04'
			),
		)
	),
	'PLB' => array(
		'name'	=> 'Provider Level Adjustment',
		'id'	=> 'ProviderLevelAdjustment',
		'fields'=> array(
			array(
				'name'	=> 'Reference Ident',
				'id'	=> 'ReferenceIdent',
				'code'	=> 'PLB01'
			),
			array(
				'name'	=> 'Date',
				'id'	=> 'Date',
				'code'	=> 'PLB02',
			),
			array(
				'name'	=> 'Adjustment Identifier',
				'id'	=> 'AdjustmentIdentifier',
				'code'	=> 'PLB03',
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount',
				'code'	=> 'PLB04'
			),
		)
	),
	'SE' => array(
		'name'	=> 'Transaction Set Trailer',
		'id'	=> 'TransactionSetTrailer',
		'fields'=> array(
			array(
				'name'	=> 'Number of Inc Segs',
				'id'	=> 'NumOfIncSegs',
				'code'	=> 'SE01'
			),
			array(
				'name'	=> 'TS Control Number',
				'id'	=> 'TSControlNum',
				'code'	=> 'SE02'
			)
		)
	),
	'SVC' => array(
		'name'	=> 'Service Payment Information',
		'id'	=> 'ServicePmntInfo',
		'fields'=> array(
			array(
				'name'	=> 'Comp. Med. Proced. ID',
				'id'	=> 'CompMedProcedID',
				'code'	=> 'SVC01'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount',
				'code'	=> 'SVC02'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount2',
				'code'	=> 'SVC03'
			),
			array(
				'name'	=> 'Product/Service ID',
				'id'	=> 'ProductServiceID',
				'code'	=> 'SVC04'
			),
			array(
				'name'	=> 'Quantity',
				'id'	=> 'Quantity',
				'code'	=> 'SVC05'
			),
			array(
				'name'	=> 'Comp. Med. Proced. ID',
				'id'	=> 'CompMedProcedID2',
				'code'	=> 'SVC06'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount3',
				'code'	=> 'SVC07'
			),
		)
	),
	'AMT' => array(
		'name'	=> 'Monetary Amount',
		'id'	=> 'amount',
		'fields'=> array(
			array(
				'name'	=> 'Amount Qual Code',
				'id'	=> 'AmtQualCode',
				'code'	=> 'AMT01'
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount',
				'code'	=> 'AMT02'
			)
		)
	),
	'PER' => array(
		'name'	=> 'Administrative Communication',
		'id'	=> 'AdminComm',
		'fields'=> array(
			array(
				'name'	=> 'Contact Funct Code',
				'id'	=> 'ContactFunctCode',
				'code'	=> 'PER01'
			),
			array(
				'name'	=> 'Name',
				'id'	=> 'Name',
				'code'	=> 'PER02',
			),
			array(
				'name'	=> 'Comm Number Qual',
				'id'	=> 'CommNumQual',
				'code'	=> 'PER03',
			),
			array(
				'name'	=> 'Comm Number',
				'id'	=> 'CommNum',
				'code'	=> 'PER04'
			),
			array(
				'name'	=> 'Comm Number Qual',
				'id'	=> 'CommNumQual2',
				'code'	=> 'PER06',
			),
			array(
				'name'	=> 'Comm Number',
				'id'	=> 'CommNum2',
				'code'	=> 'PER06'
			),
			array(
				'name'	=> 'Comm Number Qual',
				'id'	=> 'CommNumQual3',
				'code'	=> 'PER07',
			),
			array(
				'name'	=> 'Comm Number',
				'id'	=> 'CommNum3',
				'code'	=> 'PER00'
			)
		)
	),
	'LQ'  => array(
		'name'	=> 'Industry Code',
		'id'	=> 'IndustryCode',
		'fields'=> array(
			array(
				'name'	=> 'Code List Qual Code',
				'id'	=>  'CodeListQualCode',
				'code'	=> 'LQ01',
			),
			array(
				'name'	=> 'Industry Code',
				'id'	=> 'IndustryCode',
				'code'	=> 'LQ02'
			)
		)
	),
	'SV1' => array(
		'name'   => 'Professional Service',
		'id'     => 'ProfessionalService',
		'fields' => array(
			array(
				'name' => 'Composite Medical Procedure Identifier',
				'id'   => 'CompositeMedicalProcedureIdentifier',
				'code' => 'SV101'
			),
			array(
				'name' => 'Monetary Amount',
				'id'   => 'MonetaryAmount',
				'code' => 'SV102'
			),
			array(
				'name' => 'Unit or Basis for Measurement Code',
				'id'   => 'UnitOrBasisForMeasurementCode',
				'code' => 'SV103'
			),
			array(
				'name' => 'Quantity',
				'id'   => 'Quantity',
				'code' => 'SV104'
			),
			array(
				'name' => 'Facility Code Value',
				'id'   => 'FacilityCodeValue',
				'code' => 'SV105'
			),
			array(
				'name' => 'Service Type Code',
				'id'   => 'ServiceTypeCode',
				'code' => 'SV106'
			),
			array(
				'name' => 'Composite Diagnosis Code Pointer',
				'id'   => 'CompositeDiagnosisCodePointer',
				'code' => 'SV107',
				'type' => 'array',
			),
			array(
				'name' => 'Monetary Amount',
				'id'   => 'MonetaryAmount',
				'code' => 'SV108'
			),
			array(
				'name' => 'Yes/No Condition or Response Code',
				'id'   => 'YesNoConditionOrResponseCode',
				'code' => 'SV109'
			),
			array(
				'name' => 'Multiple Procedure Code',
				'id'   => 'MultipleProcedureCode',
				'code' => 'SV110'
			),
			array(
				'name' => 'Yes/No Condition or Response Code',
				'id'   => 'YesNoConditionOrResponseCode',
				'code' => 'SV111'
			),
			array(
				'name' => 'Yes/No Condition or Response Code',
				'id'   => 'YesNoConditionOrResponseCode',
				'code' => 'SV112'
			),
			array(
				'name' => 'Review Code',
				'id'   => 'ReviewCode',
				'code' => 'SV113'
			),
			array(
				'name' => 'National or Local Assigned Review Value',
				'id'   => 'NationalOrLocalAssignedReviewValue',
				'code' => 'SV114'
			),
			array(
				'name' => 'Copay Status Code',
				'id'   => 'CopayStatusCode',
				'code' => 'SV115'
			),
			array(
				'name' => 'Health Care Professional Shortage Area Code',
				'id'   => 'HealthCareProfessionalShortageAreaCode',
				'code' => 'SV116'
			),
			array(
				'name' => 'Health Care Professional Shortage Area Code',
				'id'   => 'HealthCareProfessionalShortageAreaCode',
				'code' => 'SV116'
			),
			array(
				'name' => 'Reference Identification',
				'id'   => 'ReferenceIdentification',
				'code' => 'SV117'
			),
			array(
				'name' => 'Postal Code',
				'id'   => 'PostalCode',
				'code' => 'SV118'
			),
			array(
				'name' => 'Monetary Amount',
				'id'   => 'MonetaryAmount',
				'code' => 'SV119'
			),
			array(
				'name' => 'Level of Care Code',
				'id'   => 'LevelOfCareCode',
				'code' => 'SV120'
			),
			array(
				'name' => 'Provider Agreement Code',
				'id'   => 'ProviderAgreementCode',
				'code' => 'SV121'
			),
		)
	),
	'HI'  => array(
		'name'	=> 'Health Care Diagnosis Code',
		'id'	=> 'HealthCareDiagnosisCode',
		'fields'=> array(
			array(
				'name'	=> 'Health Care Code Information',
				'id'	=> 'Health Care Code Information',
				'code'	=> 'HI01',
			),
			array(
				'name'	=> 'Health Care Code Information',
				'id'	=> 'Health Care Code Information',
				'code'	=> 'HI02',
			),
			array(
				'name'	=> 'Health Care Code Information',
				'id'	=> 'Health Care Code Information',
				'code'	=> 'HI03',
			),
			array(
				'name'	=> 'Health Care Code Information',
				'id'	=> 'Health Care Code Information',
				'code'	=> 'HI04',
			),
			array(
				'name'	=> 'Health Care Code Information',
				'id'	=> 'Health Care Code Information',
				'code'	=> 'HI05',
			),
			array(
				'name'	=> 'Health Care Code Information',
				'id'	=> 'Health Care Code Information',
				'code'	=> 'HI06',
			),
			array(
				'name'	=> 'Health Care Code Information',
				'id'	=> 'Health Care Code Information',
				'code'	=> 'HI07',
			),
			array(
				'name'	=> 'Health Care Code Information',
				'id'	=> 'Health Care Code Information',
				'code'	=> 'HI08',
			),
			array(
				'name'	=> 'Health Care Code Information',
				'id'	=> 'Health Care Code Information',
				'code'	=> 'HI09',
			),
			array(
				'name'	=> 'Health Care Code Information',
				'id'	=> 'Health Care Code Information',
				'code'	=> 'HI10',
			),
			array(
				'name'	=> 'Health Care Code Information',
				'id'	=> 'Health Care Code Information',
				'code'	=> 'HI11',
			),
			array(
				'name'	=> 'Health Care Code Information',
				'id'	=> 'Health Care Code Information',
				'code'	=> 'HI12',
			),
		)
	),
	
	'HL'  => array(
		'name'	=> 'Unknown: HL',
		'id'	=> 'HL',
		'fields'=> array()
	),
	'SBR'  => array(
		'name'	=> 'Unknown: SBR',
		'id'	=> 'SBR',
		'fields'=> array()
	),
	'DMG'  => array(
		'name'	=> 'Unknown: DMG',
		'id'	=> 'DMG',
		'fields'=> array()
	),
	'CLM'  => array(
		'name'	=> 'Unknown: CLM',
		'id'	=> 'CLM',
		'fields'=> array()
	),
	'DTP'  => array(
		'name'	=> 'Unknown: DTP',
		'id'	=> 'DTP',
		'fields'=> array()
	),
	'GE'  => array(
		'name'	=> 'Unknown: GE',
		'id'	=> 'GE',
		'fields'=> array()
	),
	'IEA'  => array(
		'name'	=> 'Unknown: IEA',
		'id'	=> 'IEA',
		'fields'=> array()
	),
	
);
?>
