SELECT
 date_format(e.start,'%m/%d/%y %H:%i') time,
 room.name `Room`,
 per.date_of_birth as DOB,
 concat_ws(', ', per.last_name, per.first_name) AS Patient,
/* concat(' ',per.identifier) AS 'ssn',*/
 concat(' ',pat.record_number) '#',
 if(per.last_name is null, 'Meeting', ifnull(reason_enum_pp.value,reason_enum_default.value)) AS Reason,
 concat(' ',num.number) AS Phone,
 appt.title AS Note,
 concat_ws(', ', pro.last_name, pro.first_name) AS Provider,

 insur.name 'Insurance'

FROM appointment AS appt
 LEFT JOIN person AS per on per.person_id = appt.patient_id
 LEFT JOIN person_number AS pernum ON pernum.person_id = per.person_id
 LEFT JOIN number AS num ON pernum.number_id = num.number_id AND num.number_type=1 AND num.active=1
 LEFT JOIN person_person AS perper ON perper.person_id = per.person_id AND perper.guarantor=1 AND perper.guarantor_priority=1
 LEFT JOIN person AS relper ON perper.related_person_id = relper.person_id
 LEFT JOIN event AS e ON appt.event_id = e.event_id
 LEFT JOIN person AS pro ON appt.provider_id = pro.person_id
 LEFT JOIN patient as pat on per.person_id = pat.person_id
 LEFT JOIN rooms as room on appt.room_id = room.id

 LEFT JOIN 
  (SELECT
     ir.person_id,
     ip.name
   FROM
     insured_relationship ir
     LEFT JOIN insurance_program ip USING(insurance_program_id)
   WHERE ir.active=1
  ) insur ON(insur.person_id=per.person_id)
  
left join (
select
 p.person_id patient_id,
 if (max(ifnull(encounter_id,0)),'N','Y') status
from
 patient p
 LEFT JOIN encounter e ON e.patient_id = p.person_id
group by
 p.person_id
) enc_patients ON enc_patients.patient_id = appt.patient_id

left join (
	select evp.practice_id, `key`, value from enumeration_value ev 
	inner join enumeration_definition ed using(enumeration_id) 
	inner join enumeration_value_practice evp on ev.enumeration_value_id = evp.enumeration_value_id
	where ed.name = 'appointment_reasons'
) reason_enum_pp ON (reason_enum_pp.practice_id = appt.practice_id) and appt.reason = reason_enum_pp.`key`
left join (
	select `key`, value from enumeration_value ev 
	inner join enumeration_definition ed using(enumeration_id) 
	left join enumeration_value_practice evp on ev.enumeration_value_id = evp.enumeration_value_id
	where ed.name = 'appointment_reasons' and evp.practice_id is null
) reason_enum_default ON appt.reason = reason_enum_default.`key`

/* end from */
where
e.start >= '[start:date] 01:01:01' and e.end <= '[end:date] 23:59:59'
and 	if(
		LENGTH('[room]') > 0,
		room.id = '[room:query:select r.id, concat(b.name,'->',r.name) name from rooms r inner join buildings b on b.id = r.building_id order by b.name, r.name]',
		1
	)
and	if (
		LENGTH('[provider]') > 0,
			pro.person_id = '[provider:query:select p.person_id, concat(p.last_name,', ',p.first_name) from person p inner join provider pr using(person_id)]',
		1
	)
GROUP BY appt.appointment_id

