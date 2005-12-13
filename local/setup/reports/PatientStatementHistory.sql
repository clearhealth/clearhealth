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
 amount,
concat('<a href="report?report_id=',report_id,'&template_id=',template_id,'&fromSnapshot=',sh.report_snapshot_id,'">View</a>') View 
from
statement_history sh
 left join report_snapshot using(report_snapshot_id)
where
 type = 1 and
 patient_id = '<<[patient_id:C_patient]>>'
order by Date

