---[patients,noPager]---
SELECT
  pat.person_id,
  (SUM(IFNULL(total_billed,0))+SUM(IFNULL(mc.amount,0))) - (SUM(IFNULL(total_paid,0)) 
  + SUM(IFNULL(writeoffs.writeoff,0))) AS total_balance
FROM
  encounter AS e 
  INNER JOIN person p ON(e.patient_id=p.person_id)
  INNER JOIN patient pat ON(pat.person_id=p.person_id)
  LEFT JOIN misc_charge mc on e.encounter_id = mc.encounter_id
  LEFT JOIN clearhealth_claim AS cc on e.encounter_id = cc.encounter_id
  LEFT JOIN (
    SELECT
            foreign_id,
            e.patient_id,
            SUM(ifnull(writeoff,0)) AS writeoff
          FROM
            payment p
            inner join clearhealth_claim cc on p.foreign_id = cc.claim_id
            inner join encounter e on cc.encounter_id = e.encounter_id
          WHERE
            p.encounter_id = 0
          GROUP BY
            foreign_id
  ) AS writeoffs ON(writeoffs.foreign_id = cc.claim_id AND writeoffs.patient_id=p.person_id )
WHERE
  IF('[last_name]' != '',p.last_name LIKE '[last_name]%',1)
  AND IF('[first_name]' != '',p.first_name LIKE '[first_name]%',1)
  AND IF('[practice]' != '',p.primary_practice_id='[practice:query:SELECT id,name FROM practices]',0)
  AND IF('[provider]' != '',e.treating_person_id='[provider:query:SELECT prov.person_id, CONCAT(per.first_name, " ", last_name) FROM provider AS prov JOIN person AS per USING(person_id)]',1)
GROUP BY p.person_id
HAVING total_balance != 0
