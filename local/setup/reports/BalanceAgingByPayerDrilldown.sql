---[by_payer_drilldown,hideFilter]---
SELECT
	e.date_of_treatment AS `date_of_service`,
	c.code_text AS `procedure`,
	CONCAT(per.first_name, ' ', per.last_name, ' (', pat.person_id, ')') AS `name`,
	cc.total_billed AS `billed`,
	cc.total_paid AS `paid`,
	( SUM(cc.total_billed) - ( SUM(cc.total_paid) + SUM( IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ) ) ) AS balance
FROM
	patient AS pat
	INNER JOIN encounter AS e ON(pat.person_id = e.patient_id)
	INNER JOIN clearhealth_claim AS cc USING(encounter_id)
	LEFT JOIN (
		SELECT foreign_id, SUM(writeoff) AS writeoff
		FROM payment 
		WHERE encounter_id = 0
		GROUP BY foreign_id
	) AS writeoffs ON(writeoffs.foreign_id = cc.claim_id)
	INNER JOIN person AS per ON per.person_id = pat.person_id
	LEFT JOIN coding_data AS cd ON cd.foreign_id = e.encounter_id
	LEFT JOIN codes AS c ON c.code_id = cd.code_id
GROUP BY
	name
