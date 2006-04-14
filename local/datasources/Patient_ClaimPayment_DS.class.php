<?php
$loader->requireOnce('includes/Datasource_sql.class.php');

class Patient_ClaimPayment_DS extends Datasource_sql
{
	
	function Patient_ClaimPayment_DS($patient_id) {
		$get =& Celini::filteredGet();
		$qClaimId = clniDB::quote($get->getTyped('claim_id', 'int'));
		$qPatientId = clniDB::quote($patient_id);
		
		$claim =& Celini::newORDO('ClearhealthClaim');
		$claimTableName = $claim->tableName();
		
		$this->setup(
			Celini::dbInstance(),
			array(
				'cols' 	=> '
					chc.claim_id, 
					chc.identifier,
					date_format(fbc.date_sent, "%Y-%m-%d") AS billing_date,
					date_format(e.date_of_treatment,"%Y-%m-%d") AS date_of_treatment, 
					chc.total_billed,
					chc.total_paid,
					fbco.name AS "current_payer",
					b.name facility,
					concat_ws(",",pro.last_name,pro.first_name) AS provider,
					(chc.total_billed - chc.total_paid - SUM(pcl.writeoff)) AS balance, 
					SUM(pcl.writeoff) AS writeoff',
				'from' 	=> 
					$claimTableName . ' AS chc 
					INNER JOIN encounter AS e USING(encounter_id)
					LEFT JOIN payment AS pa ON(pa.foreign_id = chc.claim_id)
					LEFT JOIN payment_claimline AS pcl ON(pcl.payment_id = pa.payment_id)
					LEFT JOIN occurences AS o ON(e.occurence_id = o.id)
					LEFT JOIN buildings AS b ON(e.building_id = b.id)
					LEFT JOIN person AS pro ON(e.treating_person_id = pro.person_id)
					LEFT JOIN fbclaim AS fbc ON(chc.identifier = fbc.claim_identifier)
					LEFT JOIN fbcompany AS fbco ON(fbc.claim_id = fbco.claim_id AND fbco.type = "FBPayer" AND fbco.index = 0)
					',
				'where' => "e.patient_id = {$qPatientId} AND chc.claim_id = {$qClaimId}",
				'groupby' => 'chc.claim_id'
			),
			array(
				'identifier' => 'Id',
				'billing_date' => 'Billing Date',
				'date_of_treatment' => 'Date', 
				'total_billed' => 'Billed',
				'total_paid' => 'Paid',
				'balance' => 'Balance',
				'current_payer' => 'Payer Name'
			)
			);
	}
}

?>
