<?php

require_once dirname(__FILE__) . '/837p.elements.php';

$tree = array(
	'envelope_header' => array('ISA','GS'),
	'header' => array('ST','BHT', 'REF'),
	// Loop 1000A
	'SubmitterName' => array('NM1', 'PER'),
	// Loop 1000B
	'ReceiverName' => array('NM1'),
	// Loop 2000A
	'BillingPayToProviderInfo+' => array(
		'HL', 'PRV', 'CUR',
		// Loop 2010AA
		'BillingProviderName' => array('NM1', 'N3', 'N4', 'REF', 'REF', 'PER'),
		// Loop 2010AB
		'PayToProviderName' => array('NM1', 'N3', 'N4', 'REF'),
	),
	// Loop 2000B
	'SubscriberInfo+' => array(
		'HL', 'SBR', 'PAT',
		// Loop 2010BA
		'SubscriberName' => array('NM1', 'N3', 'N4', 'DMG', 'REF', 'REF'),
		// Loop 2010BB
		'PayerName' => array('NM1', 'N3', 'N4', 'REF'),
		// Loop 2010BC
		'ResponsiblePartyName' => array('NM1', 'N3', 'N4'),
		// Loop 2010BD
		'CreditDebitCardHolderName' => array('NM1', 'REF'),
		// Loop 2300
		'ClaimInfo+' => array(
			'CLM', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 
			'DTP', 'DTP', 'DTP', 'DTP', 'PWK', 'CN1', 'AMT', 'AMT', 'AMT', 'REF', 'REF', 'REF', 'REF',
			'REF', 'REF', 'REF', 'REF', 'REF', 'REF', 'REF', 'REF', 'REF', 'K3', 'NTE', 'CR1', 'CR2',
			'CRC', 'CRC', 'CRC', 'CRC', 'HI', 'HCP', 
			// Loop 2305
			'HomeHealthCarePlanInfo+' => array('CR7', 'HSD'),
			// Loop 2310A
			'ReferringProviderName+' => array('NM1', 'PRV', 'REF'),
			// Loop 2310B
			'RenderingProviderName' => array('NM1', 'PRV', 'REF'),
			// Loop 2310C
			'PurchasedServiceProviderName' => array('NM1', 'REF'),
			// Lop 2310D
			'ServiceLineFacilityLocation' => array('NM1', 'N3', 'N4', 'REF'),
			// Loop 2310E
			'SupervisingProviderName' => array('NM1', 'REF'),
			// Loop 2320
			'OtherSubscriberInfo+' => array(
				'SBR', 'CAS', 'AMT', 'AMT', 'ATM', 'AMT', 'AMT', 'AMT', 'AMT', 'ATM', 'AMT', 'AMT', 'OI', 'MOA',
				// Loop 2320A
				'OtherSubscriberName' => array('NM1', 'N3', 'N4', 'REF'),
				'OtherPayerName' => array('NM1', 'PER', 'DTP', 'REF', 'REF', 'REF'),
				'OtherPayerPatientInfo' => array('NM1', 'REF'),
				'OtherPayerReferringProvider+' => array('NM1', 'REF'),
				'OtherPayerRenderingProvider' => array('NM1', 'REF'),
				'OtherPayerPurchasedServiceProvider' => array('NM1', 'REF'),
				'OtherPayerServiceFacilityLocations' => array('NM1', 'REF'),
				'OtherPayerSupervisingProvider' => array('NM1', 'REF'),
			),
			// Loop 2400
			'ServiceLine+' => array(
				'LX', 'SV1', 'SV5', 'PWK', 'CR1', 'CR2', 'CR3', 'CR4', 'CR5', 'CRC', 'CRC', 'CRC', 'DTP',
				'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'MEA',
				'CN1', 'REF', 'REF', 'REF', 'REF', 'REF', 'REF', 'REF', 'REF', 'REF', 'REF', 'REF', 'AMT',
				'AMT', 'AMT', 'K3', 'NTE', 'PS1', 'HSD', 'HCP',
				// Loop 2410
				'DrugIdentification+' => array('LIN', 'CPT', 'REF'),
				// Loop 2420A
				'RenderingProviderName' => array('NM1', 'PRV', 'REF'),
				// Loop 2420B
				'PurchasedServiceProviderName' => array('NM1', 'REF'),
				// Loop 2420C
				'ServiceFacilityLocation' => array('NM1', 'N3', 'N4', 'REF'),
				// Loop 2430D
				'SupervisingProviderName' => array('NM1', 'REF'),
				// Loop 2420E
				'OrderingProviderName' => array('NM1', 'N3', 'N4', 'REF', 'PER'),
				// Loop 2420F
				'ReferringProviderName+' => array('NM1', 'PRV', 'REF'),
				// Loop 2420G
				'OtherPayerPriorAuthorizationOrReferralNumber+' => array('NM1', 'REF'),
				// Loop 2430
				'LineAdjudicationInfo+' => array('SVD', 'CAS', 'DTP'),
				// Loop 2440
				'FormIdentificationCode+' => array('LQ', 'FRM'),
			),
		),
	),
	// Loop 2000C
	'PatientInfo+' => array(
		'HL', 'PAT',
		// Loop 2010CA
		'PatientName' => array('NM1', 'N3', 'N4', 'DMG', 'REF', 'REF'),
		// Loop 2300
		'ClaimInfo+' => array(
			'CLM', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 
			'DTP', 'DTP', 'DTP', 'DTP', 'PWK', 'CN1', 'AMT', 'AMT', 'AMT', 'REF', 'REF', 'REF', 'REF',
			'REF', 'REF', 'REF', 'REF', 'REF', 'REF', 'REF', 'REF', 'REF', 'K3', 'NTE', 'CR1', 'CR2',
			'CRC', 'CRC', 'CRC', 'CRC', 'HI', 'HCP', 
			// Loop 2305
			'HomeHealthCarePlanInfo+' => array('CR7', 'HSD'),
			// Loop 2310A
			'ReferringProviderName+' => array('NM1', 'PRV', 'REF'),
			// Loop 2310B
			'RenderingProviderName' => array('NM1', 'PRV', 'REF'),
			// Loop 2310C
			'PurchasedServiceProviderName' => array('NM1', 'REF'),
			// Lop 2310D
			'ServiceFacilityLocation' => array('NM1', 'N3', 'N4', 'REF'),
			// Loop 2310E
			'SupervisingProviderName' => array('NM1', 'REF'),
			// Loop 2320
			'OtherSubscriberInfo+' => array(
				'SBR', 'CAS', 'AMT', 'AMT', 'ATM', 'AMT', 'AMT', 'AMT', 'AMT', 'ATM', 'AMT', 'AMT',
			),
			// Loop 2400
			'ServiceLine+' => array(
				'LX', 'SV1', 'SV5', 'PWK', 'CR1', 'CR2', 'CR3', 'CR4', 'CR5', 'CRC', 'CRC', 'CRC', 'DTP',
				'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'DTP', 'MEA',
				'CN1', 'REF', 'REF', 'REF', 'REF', 'REF', 'REF', 'REF', 'REF', 'REF', 'REF', 'REF', 'AMT',
				'AMT', 'AMT', 'K3', 'NTE', 'PS1', 'HSD', 'HCP',
				'Loop2410+' => array('LIN', 'CPT', 'REF'),
				'Loop2420A' => array('NM1', 'PRV', 'REF'),
				'Loop2420B' => array('NM1', 'REF'),
				'Loop2420C' => array('NM1', 'N3', 'N4', 'REF'),
				'Loop2420D' => array('NM1', 'REF'),
				'Loop2420E' => array('NM1', 'N3', 'N4', 'REF', 'PER'),
				'Loop2420F+' => array('NM1', 'PRV', 'REF'),
				'Loop2420G+' => array('NM1', 'REF'),
				'Loop2430+' => array('SVD', 'CAS', 'DTP'),
				'Loop2440+' => array('LQ', 'FRM'),
			),
		),
	),

	'summary' => array('PLB','SE'),
	'envelope_footer' => array('GE','IEA'),
	);

$children = array(
);
?>
