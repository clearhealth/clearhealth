<?php


$tree = array(
	'envelope_header' => array('ISA','GS'),
	'transaction+' => array(
		'header' => array('ST','BPR','TRN','CUR','REF','REF','DTM'),
		'payer' => array( 'N1','N3','N4','REF','PER'),
		'payee' => array( 'N1','N3','N4','REF'),
		'header_number' => array('LX','TS3','TS2'),
		'detail+' => array(
			'claim_payment_info' => array('CLP','CAS','NM1','NM1','NM1','NM1','NM1','NM1','MIA','MOA','REF','REF','DTM','DTM','DTM','DTM','PER','AMT','QTY'),
			'service_payment_information' => array('SVC','DTM','DTM','DTM','CAS','CAS','CAS','CAS','NM1','REF','REF','AMT','QTY','LQ','LQ')
		),
		'summary' => array('PLB','SE'),
	),
	'envelope_footer' => array('GE','IEA'),
);
$children = array();
?>
