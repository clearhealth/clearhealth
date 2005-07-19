SELECT 
	date_format(date_of_treatment,'%Y-%m-%d') date_of_treatment, 
	date_format(timestamp,'%Y-%m-%d') last_change, 
	concat_ws(', ',p.last_name,p.first_name) patient,
	e.encounter_id,
	b.name AS "Facility"
FROM
	encounter AS e
	INNER JOIN person AS p on e.patient_id = p.person_id
	JOIN buildings AS b ON(b.id = e.building_id)
WHERE
	e.status = 'open'
