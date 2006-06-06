<?php

class MasterClaimList_DS extends Datasource_sql
{
	function MasterClaimList_DS($filters = array()) {
		$db =& new clniDB();
		$whereSql = array();
		
		if (is_array($filters)) {
			foreach ($filters as $fname => $fval) {
				if (!empty($fval)) {
					switch ($fname) {
						case 'revision_start':
							$timestamp =& TimestampObject::create($fval);
							$whereSql[] = 'fbc.timestamp >= ' . $db->quote($timestamp->toISO());
							break;
							
						case 'revision_end':
							$timestamp =& TimestampObject::create($fval);
							$whereSql[] = 'fbc.timestamp <= ' . $db->quote($timestamp->toISO());
							break;
							
						case 'name' :
							$qName = $db->quote('%' . $fval . '%');
							$whereSql[] = "(per.last_name LiKE {$qName} OR per.first_name like  {$qName})";
							break;
						
						case 'facility':
							$whereSql[] = 'e.building_id = '.$db->quote($fval);
							break;
							
						case 'provider':
							$whereSql[] = 'e.treating_person_id = '. (int)$fval;
							break;
							
						case 'payer':
							$insuranceProgram =& Celini::newORDO('InsuranceProgram', $fval);
							$qInsuranceName = $db->quote($insuranceProgram->get('insurance_company_name'));
							$whereSql[] = '
								(
									pa.payer_id =  ' . (int)$fval . ' OR 
									(
										fbco.name = ' . $qInsuranceName . ' AND
										fbco.type = "FBPayer"
									)
										
								)';
							break;
						
						case 'claim_identifier':
							$qClaimId = $db->quote($fval . '%');
							$whereSql[] = "
								(
									fbc.claim_identifier LIKE {$qClaimId} OR 
									chc.claim_id LIKE {$qClaimId}
								)";
							break;
						
						case 'billed_amount' :
							$qAmount = $db->quote($fval);
							$whereSql[] = "chc.total_billed = {$qAmount}";
							break;

						case 'user':
							$qUser = enforceType::int($fval);
							$whereSql[] = "u.user_id = $qUser";
							break;
					}	
				}
			}
		}
		
		$claim =& Celini::newORDO('ClearhealthClaim');
		$person =& Celini::newORDO('Person');
		$claimTableName = $claim->tableName();
		$personTableName = $person->tableName();
		
		$dateFormat = DateObject::getFormat();
		
		$this->setup(
			Celini::dbInstance(),
			array(
				'cols' 	=> '
					chc.claim_id,
					per.person_id AS patient_id,
					CONCAT_WS(", ", per.last_name, per.first_name) AS patient_name,
					chc.identifier,
					DATE_FORMAT(fbc.date_sent, "' . $dateFormat . '") AS billing_date,
					DATE_FORMAT(e.date_of_treatment,"' . $dateFormat . '") AS date_of_treatment, 
					chc.total_billed,
					chc.total_paid,
					fbco.name AS "current_payer",
					b.name facility,
					CONCAT_WS(",",pro.last_name,pro.first_name) AS provider,
					(chc.total_billed - chc.total_paid - SUM(ifnull(pcl.writeoff,0))) AS balance, 
					SUM(ifnull(pcl.writeoff,0)) AS writeoff,
					u.username user
					',
				'from' 	=> 
					$claimTableName . ' AS chc 
					INNER JOIN encounter AS e USING(encounter_id) 
					INNER JOIN ' . $personTableName . ' AS per ON(e.patient_id = per.person_id)
					LEFT JOIN payment AS pa ON(pa.foreign_id = chc.claim_id)
					LEFT JOIN payment_claimline AS pcl ON(pcl.payment_id = pa.payment_id)
					LEFT JOIN occurences AS o ON(e.occurence_id = o.id)
					LEFT JOIN buildings AS b ON(e.building_id = b.id)
					LEFT JOIN person AS pro ON(e.treating_person_id = pro.person_id)
					LEFT JOIN fbclaim AS fbc ON(chc.identifier = fbc.claim_identifier)
					LEFT JOIN fbcompany AS fbco ON(fbc.claim_id = fbco.claim_id AND fbco.type = "FBPayer" AND fbco.index = 0)
					LEFT JOIN ordo_registry AS oreg ON(e.encounter_id = oreg.ordo_id)
					LEFT JOIN user AS u ON(oreg.creator_id = u.user_id)
					',
				'where' => implode(' AND ', $whereSql),
				'groupby' => 'chc.claim_id'
			),
			array(
				'identifier' => 'Id',
				'patient_name' => 'Patient Name',
				'billing_date' => 'Billing Date',
				'date_of_treatment' => 'Date', 
				'total_billed' => 'Billed',
				'total_paid' => 'Paid',
				'balance' => 'Balance',
				'current_payer' => 'Payer Name',
				'user' => 'Entered By'
			)
		);
		
		$this->registerFilter('patient_name', array(&$this, '_patientHistoryLink'));
		$this->registerFilter('identifier', array(&$this, '_claimHistoryLink'));
	}
	
	
	/**#@+
	 * @access private
	 */
	function _patientHistoryLink($value, $row) {
		$url = Celini::link('history', 'Account') . 'id=' . $row['patient_id'];
		return "<a href='{$url}'>$value</a>"; 
	}
	
	function _claimHistoryLink($value, $row) {
		return "<a href='#details' onclick=\"selectClaim(this,'{$row['patient_id']}','{$row['claim_id']}');\">{$value}</a>";
	}
	/**#@-*/
}

