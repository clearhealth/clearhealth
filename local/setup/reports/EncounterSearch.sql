---[encounter_search]---
SELECT 
 {link:controller=encounter&action=edit&columnName=enc.encounter_id} AS 'ID',
 CONCAT(pat.last_name, ', ', pat.first_name) AS 'Patient',
 CONCAT(pro.last_name, ', ', pro.first_name) AS 'Treating Provider',
 enc.encounter_reason AS 'Reason',
 DATE_FORMAT(enc.date_of_treatment, '%m/%d/%Y') AS 'Date of Treatment',
 enc.status AS 'Status'
FROM 
 encounter AS enc
 INNER JOIN person AS pat ON (enc.patient_id = pat.person_id)
 INNER JOIN person AS pro ON (enc.treating_person_id = pro.person_id) 
 INNER JOIN coding_data AS cd ON (enc.encounter_id = cd.foreign_id AND cd.parent_id = 0) 
 INNER JOIN codes AS c ON (cd.code_id = c.code_id) 
WHERE 
 enc.status LIKE '[status:query:SELECT DISTINCT status, status FROM encounter]%' 
 AND enc.date_of_treatment BETWEEN IF('[start_date:date]' = '', CURRENT_DATE - 1000, '[start_date:date]') AND IF('[end_date:date]' = '', CURRENT_DATE + 1, '[end_date:date]') 
 AND c.code LIKE '[procedure/diagnoses_code]%'

/*** dsFilters-Reason|enumLookup&ds|encounter_reason ***/