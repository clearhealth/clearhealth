---[practice]---
select 
 p.name,
 a.line1,
 a.line2,
 a.city,
 a.state,
 a.postal_code
from practices p 
inner join buildings b on p.id = b.practice_id
inner join encounter e on b.id = e.building_id
left join practice_address pa on p.id = pa.practice_id
left join address a using(address_id)
where address_type = 4 and e.encounter_id = '[encounter_id:CONTROLLER:C_Patient]'
---[treating_facility]---
select 
 b.name,
 a.line1,
 a.line2,
 a.city,
 a.state,
 a.postal_code
from buildings b
inner join encounter e on b.id = e.building_id
left join building_address ba on b.id = ba.building_id
left join address a using(address_id)
where e.encounter_id = '[encounter_id:CONTROLLER:C_Patient]'
---[treating_provider]---
select 
 per.salutation,
 per.last_name,
 per.first_name,
 p.state_license_number,
 a.line1,
 a.line2,
 a.city,
 a.state,
 a.postal_code,
 n.number

from provider p
inner join person per using(person_id)
inner join encounter e on p.person_id = e.treating_person_id
left join person_address pa on p.person_id = pa.person_id
left join address a on a.address_id = pa.person_id and address_type = 1
left join person_number pn on p.person_id = pn.person_id
left join number n on n.number_id = pn.number_id and n.number_type = 1
where
 e.encounter_id = '[encounter_id:CONTROLLER:C_Patient]'
---[patient]---
select * from person p
inner join patient pat using(person_id)
inner join encounter e on p.person_id = e.patient_id
left join person_address pa on p.person_id = pa.person_id
left join address a on a.address_id = pa.address_id and address_type =1  
left join person_number pn on p.person_id = pn.person_id
left join number n on n.number_id = pn.number_id and n.number_type = 1
where e.encounter_id = '[encounter_id:CONTROLLER:C_Patient]'
---[code_list]--- 
select cpt.code_text `Procedure`, cpt.code Code, 
concat_ws(', '
,max(case code_order when 1 then c.code else null end) 
,max(case code_order when 2 then c.code else null end)
,max(case code_order when 3 then c.code else null end)
,max(case code_order when 4 then c.code else null end) 
) Diagnosis, cd.modifier, cd.units, cd.fee
from coding_data cd
inner join codes c using(code_id)
inner join codes cpt on cd.parent_id = cpt.code_id
inner join encounter e on cd.foreign_id = e.encounter_id
where e.encounter_id = '[encounter_id:CONTROLLER:C_Patient]'
group by cd.parent_id
union
select 'Total','','',null,sum(units),sum(fee)
from coding_data cd
where foreign_id = '[encounter_id:CONTROLLER:C_Patient]' and primary_code = 1
---[payment_history]---
select 
date_format(payment_date, '%m/%d/%Y'), amount, payment_type
from payment
where encounter_id = '[encounter_id:CONTROLLER:C_Patient]'
---[encounter]---
select * from encounter e where e.encounter_id = '[encounter_id:CONTROLLER:C_Patient]'
