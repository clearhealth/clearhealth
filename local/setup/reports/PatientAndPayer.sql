---[by_payer]---
SELECT
	CONCAT('<a href="', '{url:controller=PatientDashboard&action=view}', 'id=', per.person_id, '">', per.last_name, ', ', per.first_name, '</a>') AS patient_name,
	IF(
		ir.insured_relationship_id IS NULL,
		"No Payer",
		CONCAT(c.name, '->', ip.name)
	) AS payer
FROM
	person AS per
	INNER JOIN patient AS pat USING(person_id)
	LEFT JOIN insured_relationship AS ir USING(person_id)
	LEFT JOIN insurance_program AS ip USING(insurance_program_id)
	LEFT JOIN company AS c USING(company_id)
WHERE
	ir.active = 1
ORDER BY ir.insurance_program_id
