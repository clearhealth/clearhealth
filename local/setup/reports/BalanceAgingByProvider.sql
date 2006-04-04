---[Provider_Balances]---
SELECT
	CONCAT(per.last_name, ', ', per.first_name) AS 'patient_name',
	CONCAT(c.name, ' > ', ip.name) AS payer,
	(
		SUM(CASE WHEN e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN total_billed ELSE 0 END) - 
		(
			SUM(CASE WHEN e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN total_paid ELSE 0 END) +
			SUM(CASE WHEN e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ELSE 0 END)
		)
	) AS `current`,
	(
		SUM(CASE WHEN 
			e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND
			e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 31 DAY)
			THEN total_billed ELSE 0 END) - 
		(
			SUM(CASE WHEN
				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND
				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 31 DAY)
				THEN total_paid ELSE 0 END) +
			SUM(CASE WHEN 
				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND
				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 31 DAY)
				THEN IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ELSE 0 END)
		)
	) AS `31 - 60`,
	(
		SUM(CASE WHEN 
			e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 90 DAY) AND
			e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 61 DAY)
			THEN total_billed ELSE 0 END) - 
		(
			SUM(CASE WHEN
				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 90 DAY) AND
				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 61 DAY)
				THEN total_paid ELSE 0 END) +
			SUM(CASE WHEN 
				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 90 DAY) AND
				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 61 DAY)
				THEN IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ELSE 0 END)
		)
	) AS `61 - 90`,
	(
		SUM(CASE WHEN 
			e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 120 DAY) AND
			e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 91 DAY)
			THEN total_billed ELSE 0 END) - 
		(
			SUM(CASE WHEN
				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 120 DAY) AND
				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 91 DAY)
				THEN total_paid ELSE 0 END) +
			SUM(CASE WHEN 
				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 120 DAY) AND
				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 91 DAY)
				THEN IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ELSE 0 END)
		)
	) AS `91 - 120`,
	(
		SUM(CASE WHEN e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 121 DAY) THEN total_billed ELSE 0 END) - 
		(
			SUM(CASE WHEN e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 121 DAY) THEN total_paid ELSE 0 END) +
			SUM(CASE WHEN e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 121 DAY) THEN IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ELSE 0 END)
		)
	) AS `121+`,
	SUM(total_billed) - (SUM(total_paid) + SUM(IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff))) AS `totals`
FROM
	person AS per
	INNER JOIN provider AS pro USING(person_id)
	INNER JOIN encounter AS e ON(pro.person_id = e.treating_person_id)
	INNER JOIN clearhealth_claim AS cc USING(encounter_id)
	LEFT JOIN (
		SELECT
			foreign_id,
			SUM(writeoff) AS writeoff
		FROM
			payment 
		WHERE
			encounter_id = 0
		GROUP BY
			foreign_id
	) AS writeoffs ON(writeoffs.foreign_id = cc.claim_id)
	INNER JOIN storage_int AS current_payer ON (current_payer.foreign_key = e.encounter_id AND current_payer.value_key = "current_payer")
	INNER JOIN insurance_program AS ip ON(current_payer.value = ip.insurance_program_id)
	INNER JOIN company AS c USING(company_id)
GROUP BY
	per.person_id
ORDER BY
	per.last_name

