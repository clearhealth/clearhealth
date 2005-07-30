SELECT 
	date_format(date_of_treatment,'%m/%d/%Y') date_of_treatment, 
	date_format(timestamp,'%m/%d/%Y') last_change, 
	concat_ws(', ',p.last_name,p.first_name) patient,
	e.encounter_id,
	b.name AS "facility"
FROM
	encounter AS e
	INNER JOIN person AS p on e.patient_id = p.person_id
	JOIN buildings AS b ON(b.id = e.building_id)
WHERE
	e.status = 'open'
