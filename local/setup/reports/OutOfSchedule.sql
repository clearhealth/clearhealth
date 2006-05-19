-- jeichorn:
-- what i did was make a schedule derived table and an appontment one,
-- and then left join schedules to appointments using thire times and
-- provider_id
-- 
-- then its just a matter of grabbing all the appointments where
-- schedule is stuff null and you have the ones outside of the schedule
--

---[appts]---
SELECT
	{link:controller=CalendarDisplay&action=day&columnName=patient&id=a.start&field=date} Patient,
    a.start,
    a.end,
    a.provider,
    a.location
    
FROM (
	SELECT
        appointment.appointment_id AS id,
        appointment.title,
        appointment.provider_id,
        event.start AS start,
        event.end AS end,
        concat(pat.last_name,', ',pat.first_name,' #',patp.record_number) AS patient,
        concat(prov.last_name, ', ', prov.first_name) AS provider,
        concat(buildings.name, '->', rooms.name) AS location
	FROM event
	INNER JOIN appointment ON event.event_id = appointment.event_id
	INNER JOIN person pat ON appointment.patient_id = pat.person_id
	INNER JOIN patient patp ON appointment.patient_id = patp.person_id
	INNER JOIN person prov ON appointment.provider_id = prov.person_id
	INNER JOIN rooms ON appointment.room_id = rooms.id
	INNER JOIN buildings ON buildings.id = rooms.building_id
	WHERE
	   event.start > date_format(now(),'%Y-%m-%d 00:00:00')
) AS a
LEFT JOIN (
	SELECT
	   event.start AS start,
	   event.end AS end,
	   provider.person_id AS provider_id
	FROM event
	INNER JOIN relationship AS ep ON
		ep.parent_type = 'Provider' AND
		ep.child_type = 'ScheduleEvent' AND
		ep.child_id = event.event_id
	INNER JOIN provider ON ep.parent_id = provider.person_id
) AS s ON (a.start >= s.start and a.end <= s.end) and s.provider_id = a.provider_id
WHERE
	s.start is null AND
    IF ('[provider]' > 0,
        a.provider_id = '[provider:query:SELECT provider.person_id, CONCAT(person.first_name, ' ', person.last_name) AS name FROM provider JOIN person ON provider.person_id = person.person_id ORDER BY last_name, first_name]',
        1
    )
