SELECT
 concat_ws(', ', per.last_name, per.first_name) AS Name,
 patstat.language AS Lang,
 per.person_id AS Record,
 num.number AS Phone,
 concat_ws(', ', relper.last_name, relper.first_name) AS Gaurantor,
 appt.reason AS Reason,
 concat(floor((unix_timestamp(e.end) - unix_timestamp(e.start)) / 60 / 60),
   ' hrs ', floor((unix_timestamp(e.end) - unix_timestamp(e.start)) / 60 % 60), ' mins') AS Duration,
 appt.title AS Note,
 concat_ws(', ', pro.last_name, pro.first_name) AS Provider,
 bal.total_balance AS Balance,
 lastpay.payment_date AS LastPayment,
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
 if (max(ifnull(encounter_id,0)),'Y','N') status
from
 patient p
 LEFT JOIN encounter e ON e.patient_id = p.person_id
group by
 p.person_id
) enc_patients ON enc_patients.patient_id = appt.patient_id