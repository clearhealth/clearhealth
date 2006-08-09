SELECT 
	{link:controller=PatientDashboard&action=view&columnName=CONCAT(pers.first_name," ",pers.last_name)&id=patient_id} AS 'Patient',
	pat.record_number `#`,
	(IFNULL(SUM(total_billed),0) - (IFNULL(SUM(total_paid),0) + IFNULL(SUM(writeoffs.writeoff),0))) AS Balance,
	CONCAT("<a href=\"", "{url:controller=Report&action=viewByCID}", 
			"cid=patient_statement&patient_id=", pat.person_id, "&set_print_view\">Generate Statement</a>") AS Action
FROM 
	encounter as e
	INNER JOIN clearhealth_claim AS cc USING(encounter_id)
	INNER JOIN storage_int as si on cc.encounter_id = si.foreign_key
	INNER JOIN insurance_program ip on ip.insurance_program_id = si.value
	INNER JOIN person as pers on e.patient_id = pers.person_id
	INNER JOIN patient as pat on e.patient_id = pat.person_id
	LEFT JOIN (
		SELECT
			foreign_id,
			IFNULL(SUM(writeoff),0) AS writeoff
		FROM
			payment 
		WHERE
			encounter_id = 0
		GROUP BY
			foreign_id
	) AS writeoffs ON(writeoffs.foreign_id = cc.claim_id)
WHERE 
	si.value_key = 'current_payer' AND
	DATE_SUB(CURDATE(),INTERVAL 30 DAY) <= e.date_of_treatment
GROUP BY 
	e.patient_id
