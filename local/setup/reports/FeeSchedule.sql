select
 c.code, fsd.data fee
from
 codes c
 inner join fee_schedule_data fsd on fsd.code_id = c.code_id
 inner join fee_schedule fs on fsd.fee_schedule_id = fs.fee_schedule_id
where
 fs.fee_schedule_id = '[fee_schedule:query:select fee_schedule_id, label from fee_schedule order by label]'

