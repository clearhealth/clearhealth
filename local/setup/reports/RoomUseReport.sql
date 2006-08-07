SELECT
 date_format(e.start,'%m/%d/%y %H:%i') time,
 concat(floor((unix_timestamp(e.end) - unix_timestamp(e.start)) / 60 / 60),
   ' hrs ', floor((unix_timestamp(e.end) - unix_timestamp(e.start)) / 60 % 60), ' mins') AS Duration,
 room.name `Room`,
 concat_ws(', ', per.last_name, per.first_name) AS Patient,
 pat.record_number '#',
 lang_enum.value AS Lang,
 num.number AS Phone,
 ifnull(concat(relper.last_name,', ', relper.first_name),'Self') AS Guarantor,
 ifnull(reason_enum_pp.value,reason_enum_default.value) AS Reason,
 appt.title AS Note,
 concat_ws(', ', pro.last_name, pro.first_name) AS Provider,
 ifnull(bal.total_balance,'NA') AS Balance,
 ifnull(lastpay.payment_date,'NA') AS LastPayment,
 enc_patients.status new

FROM person AS per
 LEFT JOIN patient_statistics AS patstat ON per.person_id = patstat.person_id
 LEFT JOIN person_number AS pernum ON pernum.person_id = per.person_id
 LEFT JOIN number AS num ON pernum.number_id = num.number_id AND num.number_type=1 AND num.active=1
 LEFT JOIN person_person AS perper ON perper.person_id = per.person_id AND perper.guarantor=1 AND perper.guarantor_priority=1
 LEFT JOIN person AS relper ON perper.related_person_id = relper.person_id
 INNER JOIN appointment AS appt ON per.person_id = appt.patient_id
 LEFT JOIN event AS e ON appt.event_id = e.event_id
 LEFT JOIN person AS pro ON appt.provider_id = pro.person_id
 LEFT JOIN patient as pat on per.person_id = pat.person_id
 LEFT JOIN rooms as room on appt.room_id = room.id

LEFT JOIN (
   SELECT
     e.patient_id,
     (SUM(IFNULL(total_billed,0)) - (SUM(IFNULL(total_paid,0)) + SUM(IFNULL(writeoffs.writeoff,0)))) AS total_balance
   FROM encounter AS e
     INNER JOIN clearhealth_claim AS cc USING(encounter_id)
     LEFT JOIN (
       SELECT foreign_id, SUM(IFNULL(writeoff,0)) AS writeoff
       FROM payment
       WHERE encounter_id = 0
       GROUP BY foreign_id
     ) AS writeoffs ON (writeoffs.foreign_id = cc.claim_id)
GROUP BY e.patient_id
 ) AS bal on bal.patient_id = appt.patient_id


LEFT JOIN (
select
 patient_id, 
 max(payment_date ) payment_date
from 
(SELECT 
 e.patient_id, 
 max( p.payment_date ) payment_date
FROM payment AS p
  INNER JOIN clearhealth_claim AS cc ON p.foreign_id = cc.claim_id
  INNER JOIN insurance_program AS ip ON ip.insurance_program_id = p.payer_id AND ip.name = 'Self Pay'
  INNER JOIN company AS comp ON ip.company_id = comp.company_id AND comp.name = 'System'
  INNER JOIN encounter AS e ON e.encounter_id = cc.encounter_id
GROUP BY 
  e.patient_id
union
SELECT 
  e.patient_id, 
  max( p.payment_date ) payment_date
FROM payment AS p
  INNER JOIN encounter AS e ON p.encounter_id = p.encounter_id
GROUP BY 
  e.patient_id
) payment_dates
GROUP BY 
  patient_id
 ) AS lastpay on lastpay.patient_id = appt.patient_id

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
left join (
	select `key`, value from enumeration_value ev 
	inner join enumeration_definition ed using(enumeration_id) 
	where ed.name = 'language'
) lang_enum ON patstat.language = lang_enum.`key`



/* end from */
where
e.start >= '[start:date] 01:01:01' and e.end <= '[end:date] 23:59:59'
