---[new_patient]---
 SELECT CONCAT(first_name," ",last_name) as Name, MIN(date_of_treatment) 
 as  'First_Visit', ev.value as Reason, if(walkin,'yes','no') as 'walk_in'
 FROM encounter
 INNER JOIN person as pers ON pers.person_id = patient_id
 INNER JOIN enumeration_definition as ed on ed.name = 'encounter_reason'
 INNER JOIN enumeration_value as ev on ev.enumeration_id = ed.enumeration_id
 INNER JOIN occurences on external_id = patient_id
 WHERE DATE_SUB(CURDATE(),INTERVAL ifnull('[x_days]',0) DAY) <=  
 date_of_treatment
 GROUP BY patient_id
 ORDER BY date_of_treatment, patient_id
 
 
 ---[walkin_count,hideFilter,noPager]---
 select count(a.patient_id)
 FROM
 (
 SELECT patient_id
 FROM encounter
 INNER JOIN occurences ON external_id = patient_id
 WHERE DATE_SUB( CURDATE( ) , INTERVAL ifnull('[x_days]',0)
 DAY ) <= date_of_treatment
 AND walkin = 1
 GROUP BY patient_id
 ) as a
 
 ---[not_walkin_count,hideFilter,noPager]---
 select count(a.patient_id)
 FROM
 (
 SELECT patient_id
 FROM encounter
 INNER JOIN occurences ON external_id = patient_id
 WHERE DATE_SUB( CURDATE( ) , INTERVAL ifnull('[x_days]',0)
 DAY ) <= date_of_treatment
 AND walkin = 0
 GROUP BY patient_id
 ) as a