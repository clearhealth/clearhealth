SELECT 
	patient.encounter_id,
	patient.Patient,
	patient.`Patient ID`,
	patient.Payer,
	IFNULL(current.total_balance,0) as `Current`,
	IFNULL(30day.total_balance,0) as `30 Day`,
	IFNULL(60day.total_balance,0) as `60 Day`,
	IFNULL(90day.total_balance,0) as `90 Day`,
	IFNULL(120day.total_balance,0) as `120 Day`
from 
(
	SELECT 
		patient_id,
		pers.person_id,
		e.encounter_id, 
		ip.name as `Payer`,
		pers.person_id as `Patient ID`,
		ip.company_id payer_id,
		CONCAT(pers.first_name," ",pers.last_name)as `Patient`
	FROM encounter as e
	INNER JOIN clearhealth_claim AS cc USING(encounter_id)
	INNER JOIN storage_int as si on cc.encounter_id = si.foreign_key
	INNER JOIN insurance_program ip on ip.insurance_program_id = si.value
	INNER JOIN person as pers on e.patient_id = pers.person_id
)
patient
LEFT JOIN (	
	SELECT patient_id,e.encounter_id,ip.company_id payer_id,
	(IFNULL(SUM(total_billed),0) - (IFNULL(SUM(total_paid),0) + IFNULL(SUM(writeoffs.writeoff),0))) AS total_balance
	FROM encounter as e
	INNER JOIN clearhealth_claim AS cc USING(encounter_id)
	INNER JOIN storage_int as si on cc.encounter_id = si.foreign_key
	INNER JOIN insurance_program ip on ip.insurance_program_id = si.value
	INNER JOIN person as pers on e.patient_id = pers.person_id
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
		si.value_key = 'current_payer'
	AND
		DATE_SUB(CURDATE(),INTERVAL 30 DAY) <= e.date_of_treatment

	GROUP BY e.patient_id,cc.identifier, ip.insurance_program_id

)as `current` on current.patient_id = patient.person_id and current.payer_id = patient.payer_id
LEFT JOIN (	
	SELECT patient_id,e.encounter_id,ip.company_id payer_id,
	(IFNULL(SUM(total_billed),0) - (IFNULL(SUM(total_paid),0) + IFNULL(SUM(writeoffs.writeoff),0))) AS total_balance
	FROM encounter as e
	INNER JOIN clearhealth_claim AS cc USING(encounter_id)
	INNER JOIN storage_int as si on cc.encounter_id = si.foreign_key
	INNER JOIN insurance_program ip on ip.insurance_program_id = si.value
	INNER JOIN person as pers on e.patient_id = pers.person_id
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
		si.value_key = 'current_payer'
	AND
		DATE_SUB(CURDATE(),INTERVAL 30 DAY) > e.date_of_treatment
	AND
		DATE_SUB(CURDATE(),INTERVAL 60 DAY) <= e.date_of_treatment

	GROUP BY e.patient_id,cc.identifier, ip.insurance_program_id
	
)as `30day`  ON patient.person_id = 30day.patient_id and patient.payer_id = 30day.payer_id

LEFT JOIN 
(
SELECT patient_id,ip.company_id payer_id,
	(IFNULL(SUM(total_billed),0) - (IFNULL(SUM(total_paid),0) + IFNULL(SUM(writeoffs.writeoff),0))) AS total_balance
	FROM encounter as e
	INNER JOIN clearhealth_claim AS cc USING(encounter_id)
	INNER JOIN storage_int as si on cc.encounter_id = si.foreign_key
	INNER JOIN insurance_program ip on ip.insurance_program_id = si.value
	INNER JOIN person as pers on e.patient_id = pers.person_id
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
		si.value_key = 'current_payer'
	AND
		DATE_SUB(CURDATE(),INTERVAL 60 DAY) > e.date_of_treatment
	AND
		DATE_SUB(CURDATE(),INTERVAL 90 DAY) <= e.date_of_treatment

	GROUP BY e.patient_id,cc.identifier, ip.insurance_program_id

) as `60day` ON patient.person_id = 60day.patient_id and patient.payer_id = 60day.payer_id
LEFT JOIN 
(
SELECT patient_id, ip.company_id payer_id,
	(IFNULL(SUM(total_billed),0) - (IFNULL(SUM(total_paid),0) + IFNULL(SUM(writeoffs.writeoff),0))) AS total_balance
	FROM encounter as e
	INNER JOIN clearhealth_claim AS cc USING(encounter_id)
	INNER JOIN storage_int as si on cc.encounter_id = si.foreign_key
	INNER JOIN insurance_program ip on ip.insurance_program_id = si.value
	INNER JOIN person as pers on e.patient_id = pers.person_id
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
		si.value_key = 'current_payer'
	AND
		DATE_SUB(CURDATE(),INTERVAL 90 DAY) > e.date_of_treatment
	AND
		DATE_SUB(CURDATE(),INTERVAL 120 DAY) <= e.date_of_treatment

	GROUP BY e.patient_id,cc.identifier, ip.insurance_program_id

) as `90day` ON patient.person_id = 90day.patient_id and patient.payer_id = 90day.payer_id
LEFT JOIN 
(
SELECT patient_id,ip.company_id payer_id,
	(IFNULL(SUM(total_billed),0) - (IFNULL(SUM(total_paid),0) + IFNULL(SUM(writeoffs.writeoff),0))) AS total_balance
	FROM encounter as e
	INNER JOIN clearhealth_claim AS cc USING(encounter_id)
	INNER JOIN storage_int as si on cc.encounter_id = si.foreign_key
	INNER JOIN insurance_program ip on ip.insurance_program_id = si.value
	INNER JOIN person as pers on e.patient_id = pers.person_id
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
		si.value_key = 'current_payer'
	AND
		DATE_SUB(CURDATE(),INTERVAL 120 DAY) >e.date_of_treatment
	
	GROUP BY e.patient_id,cc.identifier, ip.insurance_program_id

) as `120day` ON patient.person_id = 120day.patient_id and patient.payer_id = 120day.payer_id
/* end from */
WHERE 
	current.total_balance <> 0
OR
	30day.total_balance <> 0
OR
	60day.total_balance <> 0
OR
	90day.total_balance <> 0
OR
	120day.total_balance <> 0
GROUP BY
	patient.person_id, patient.payer_id
