<?php
$map = array(
	'ST' => array(
		'name' 	=> 'Transaction Set Header',
		'id'	=> 'Header',
		'fields'=> array(
			array(
				'name'	=> 'ID Code',
				'id'	=> 'IDCode',
			),
			array(
				'name'	=> 'Control Number',
				'id'	=> 'ControlNumber',
			),
		)
	),
	'BPR' => array(
		'name'	=> 'Beginning Segment for Payment Order/Remittance Advice',
		'id'	=> 'PaymentOrder',
		'fields'=> array(
			array(
				'name'	=> 'Transaction Handle Code',
				'id'	=> 'TransactionCode',
				'code'	=> 'BPR01',
			),
			array(
				'name'	=> 'Monetary Amount',
				'id'	=> 'MonetaryAmount',
				'code'	=> 'BPR02',
			),
			array(
				'name'	=> 'Cred/Debit Flag Code',
				'id'	=> 'CredDebitFlag',
				'code'	=> 'BPR03',
			),
			array(
				'name'	=> 'Payment Method Code',
				'id'	=> 'PaymentMethod',
				'code'	=> 'BPR04',
			),
			array(
				'name'	=> 'Payment Format',
				'id'	=> 'PaymentFormat',
				'code'	=> 'BPR05',
			),
			array(
				'name'	=> 'DFI ID No Qualifier',
				'id'	=> 'DFIIDQualifier',
				'code'	=> 'BPR06'
			),
			array(
				'name'	=> 'DFI ID Number',
				'id'	=> 'DFIIDNum',
				'code'	=> 'BPR07',
			),
			array(
				'name'	=> 'Acct Number Qualifier',
				'id'	=> 'AcctNumberQualifer',
				'code'	=> 'BPR08',
			),
			array(
				'name'	=> 'Account Number',
				'id'	=> 'AcctNumber',
				'code'	=> 'BPR09',
			),
			array(
				'name'	=> 'Originating Company ID',
				'id'	=> 'OrigCompId',
				'code'	=> 'BPR10',
			),
			array(
				'name'	=> 'Orginating Co Code',
				'id'	=> 'OrigCompCode',
				'code'	=> 'BPR11',
			),
			array(
				'name'	=> 'DFI ID No Qualifier',
				'id'	=> 'DFIIDQualifier2',
				'code'	=> 'BPR12'
			),
			array(
				'name'	=> 'DFI ID Number',
				'id'	=> 'DFIIDNum2',
				'code'	=> 'BPR13',
			),
			array(
				'name'	=> 'Acct Number Qualifier',
				'id'	=> 'AcctNumberQualifer2',
				'code'	=> 'BPR14',
			),
			array(
				'name'	=> 'Account Number',
				'id'	=> 'AcctNumber2',
				'code'	=> 'BPR15',
			),
			array(
				'name'	=> 'Date',
				'id'	=> 'Date',
				'code'	=> 'BPR16',
			),
			array(
				'name'	=> 'Business Funct Code',
				'id'	=> 'BusinessFunctCode',
				'code'	=> 'BPR17',
			),
			array(
				'name'	=> 'DFI ID No Qualifier',
				'id'	=> 'DFIIDQualifier3',
				'code'	=> 'BPR18'
			),
			array(
				'name'	=> 'DFI ID Number',
				'id'	=> 'DFIIDNum3',
				'code'	=> 'BPR19',
			),
			array(
				'name'	=> 'Acct Number Qualifier',
				'id'	=> 'AcctNumberQualifer3',
				'code'	=> 'BPR20',
			),
			array(
				'name'	=> 'Account Number',
				'id'	=> 'AcctNumber3',
				'code'	=> 'BPR21',
			)
		)
	),
	'TRN' => array(
		'name'	=> 'Trace',
		'id'	=> 'Trace',
		'fields'=> array(
			array(
				'name'	=> 'Trace Type Code',
				'id'	=> 'TraceTypeCode',
				'code'	=> 'TRN01'
			),
			array(
				'name'	=> 'Reference Ident',
				'id'	=> 'ReferenceIdent',
				'code'	=> 'TRN02',
			),
			array(
				'name'	=> 'Origination Company ID',
				'id'	=> 'OrigCompID',
				'code'	=> 'TRN03',
			),
			array(
				'name'	=> 'Reference Ident',
				'id'	=> 'ReferenceIdent',
				'code'	=> 'TRN04',
			)
		)
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
		'id'	=> 'Address',
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
		'id'	=> 'Address',
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
	)
);

$tree = array(
	'header' => array('ST','BPR','TRN','CUR','REF','REF','DTM','payer','payee'),
	'detail' => array(array('+','header_number','claim_payment_info'),array('+','header_number','service_payment_information')),
	'summary' => array('PLB','SE')
	);

$children = array(
	'payer' 		=> array('N1','N3','N4','REF','PER'),
	'payee'			=> array('N1','N3','N4','REF'),
	'header_number' 	=> array('LX','TS3','TS2'),
	'claim_payment_info' 	=> array('CLP','CAS+','NM1','NM1','NM1','NM1','NM1','NM1','MIA','MOA','REF','REF','DTM+','PER','AMT','QTY'),
	'service_payment_information' => array('SVC','DTM+','CAS+','CLP','CAS','NM1','REF','REF','AMT','QTY','LQ'),
);
?>
