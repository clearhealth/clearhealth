---[payer_program_drilldown,hideFilter]---
SELECT 
  CONCAT(per.last_name, ', ', per.first_name) AS 'Patient Name',
  per.identifier AS SSN,
  per.gender as Gender,
  per.date_of_birth AS Birthday,
  ip.insurance_program_id AS 'Group Number',
  ip.name AS 'Group Name'
FROM 
  company AS c
  LEFT JOIN insurance_program AS ip ON c.company_id = ip.company_id
  LEFT JOIN insured_relationship AS ir ON ip.insurance_program_id = ir.insurance_program_id
  LEFT JOIN person AS per ON ir.person_id = per.person_id
WHERE ip.insurance_program_id = [insurance_program_id]
ORDER BY per.last_name, per.first_name

/***
dsFilters-Gender|enumLookup&ds|gender
***/
