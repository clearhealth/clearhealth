---[total_encounters,hideFilter,noPager]---
SELECT
 COUNT(e.encounter_id) `Total Encounters`
FROM
 encounter e 
 LEFT JOIN occurences AS o ON(e.occurence_id = o.id)
WHERE
 IF ('[after]',e.date_of_treatment >= '[after:date]',1) AND
 IF ('[before]',e.date_of_treatment <= '[before:date]',1) AND
 IF ('[facility]',e.building_id = '[facility]',1) AND
 IF ('[provider]',o.user_id = '[provider]',1)
---[total_encounters_by_reason,hideFilter,noPager]---
SELECT
 e.encounter_reason `Reason`,
 count(e.encounter_id) `Total`
FROM
 encounter AS e
 LEFT JOIN occurences AS o ON(e.occurence_id = o.id)
WHERE
 IF ('[after]',e.date_of_treatment >= '[after:date]',1) AND
 IF ('[before]',e.date_of_treatment <= '[before:date]',1) AND
 IF ('[facility]',e.building_id = '[facility:query:select b.id, b.name from buildings b order by b.name]',1) AND
 IF ('[provider]',e.treating_person_id = '[provider]',1)
GROUP BY
 o.reason_code
/***
dsFilters-Reason|enumLookup&ds|encounter_reason
***/
---[total_encounters_by_walkin,hideFilter,noPager]---
SELECT
 IF (o.walkin,'Y','N') `Walk-in?`,
 count(e.encounter_id) `Total`
FROM
 encounter e
 INNER JOIN person AS p on e.patient_id = p.person_id
 LEFT JOIN occurences AS o on (e.occurence_id = o.id)
WHERE
 IF ('[after]',e.date_of_treatment >= '[after:date]',1) AND
 IF ('[before]',e.date_of_treatment <= '[before:date]',1) AND
 IF ('[facility]',e.building_id = '[facility]',1) AND
 IF ('[provider]',e.treating_person_id = '[provider]',1)
GROUP BY
 `Walk-in?`
---[total_encounters_by_facility,hideFilter,noPager]---
SELECT
 b.name Facility,
 count(e.encounter_id) total
FROM
 encounter AS e
 INNER JOIN buildings AS b ON(e.building_id = b.id)
WHERE
 IF ('[after]',e.date_of_treatment >= '[after:date]',1) AND
 IF ('[before]',e.date_of_treatment <= '[before:date]',1) AND
 IF ('[facility]',e.building_id = '[facility]',1) AND
 IF ('[provider]',e.treating_person_id = '[provider]',1)
GROUP BY
 b.id
---[total_encounters_by_provider,hideFilter,noPager]---
SELECT
 concat_ws(', ',pro.last_name,pro.first_name) Provider,
 count(e.encounter_id) total
FROM
 encounter e
 INNER JOIN person AS pro ON(e.treating_person_id = pro.person_id)
WHERE
 IF ('[after]',e.date_of_treatment >= '[after:date]',1) AND
 IF ('[before]',e.date_of_treatment <= '[before:date]',1) AND
 IF ('[facility]',e.building_id = '[facility]',1) AND
 IF ('[provider]',e.treating_person_id = '[provider]',1)
GROUP BY
 pro.person_id
