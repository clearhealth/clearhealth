SELECT 
	DATE_FORMAT(date_of_treatment,'%m/%d/%Y') AS date_of_treatment, 
	DATE_FORMAT(timestamp,'%m/%d/%Y') AS last_change, 
	concat_ws(', ',p.last_name,p.first_name) AS patient,
	e.encounter_id,
	b.name AS "facility"
FROM
	encounter AS e
	INNER JOIN person AS p on e.patient_id = p.person_id
	JOIN buildings AS b ON(b.id = e.building_id)
WHERE
	e.status = 'open'
