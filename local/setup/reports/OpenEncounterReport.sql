SELECT 
	DATE_FORMAT(date_of_treatment,'%m/%d/%Y') AS date_of_treatment, 
	DATE_FORMAT(timestamp,'%m/%d/%Y') AS last_change, 
	concat_ws(', ',p.last_name,p.first_name) AS patient,
	e.encounter_id,
	b.name AS "facility",
	CONCAT_WS(', ', prov_person.last_name, prov_person.first_name) AS provider,
	insurer.name AS insurance,
	c.code_text AS 'Primary Diagnosis'
FROM
	encounter AS e
	INNER JOIN person AS p on e.patient_id = p.person_id
	JOIN buildings AS b ON(b.id = e.building_id)
	JOIN provider AS prov ON(e.treating_person_id = prov.person_id)
	JOIN person AS prov_person USING(person_id)
	LEFT JOIN storage_int AS curprog ON(e.encounter_id = curprog.foreign_key AND curprog.value_key = 'current_payer') 
	LEFT JOIN insurance_program AS insurer ON(curprog.value = insurer.insurance_program_id)
	LEFT JOIN coding_data AS cd ON (e.encounter_id = cd.foreign_id)
	LEFT JOIN codes AS c ON (cd.code_id = c.code_id)
WHERE
	e.status = 'open' AND
        (cd.primary_code = 1 OR cd.primary_code IS NULL) AND
	IF ('[after]', e.date_of_treatment >= '[after:date]', 1) AND
	IF ('[before]', e.date_of_treatment <= '[before:date]', 1) AND
	IF ('[facility]', e.building_id = '[facility:query:SELECT id, name FROM buildings ORDER BY name]', 1) AND
	IF ('[provider]', e.treating_person_id = '[provider:query:SELECT prov.person_id, CONCAT(per.first_name, " ", last_name) FROM provider AS prov JOIN person AS per USING(person_id)]', 1) AND
	IF ('[insurance]', insurer.insurance_program_id = '[insurance:query:SELECT insurance_program_id, name FROM insurance_program WHERE LENGTH(name) > 0]', 1)
GROUP BY
	e.encounter_id

