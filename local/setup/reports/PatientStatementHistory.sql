---[Patient,infoBox]---
select
 last_name, first_name, record_number `Record #`
from 
 patient
 inner join person using(person_id)
where
 patient.person_id = '<<[patient_id:C_patient]>>'
---[Statement_History]---
select
 statement_number `Statement #`,
 date_format(date_generated,'%m/%d/%Y') Date,
 amount
from
statement_history
where
 type = 1 and
 patient_id = '<<[patient_id:C_patient]>>'
order by Date

