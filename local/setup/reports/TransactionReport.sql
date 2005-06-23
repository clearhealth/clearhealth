/* Sql for a Transaction Report, tagged for using the standard reporting mechanism */
---[Transaction_List]---
select 
date_format(e.date_of_treatment,'%Y-%m-%d') `date`,
concat_ws(', ',p.last_name,p.first_name) patient,
pat.record_number,
pay.payment_type,
format(if(isnull(pay.amount),0,pay.amount),2) amount,
concat_ws(', ',pro.last_name,pro.first_name) provider
from encounter e
left join payment pay on pay.encounter_id = e.encounter_id
left join person p on e.patient_id = p.person_id
left join patient pat on p.person_id = pat.person_id
left join person pro on e.treating_person_id = pro.person_id
where if ('[user]',pay.user_id =
'[user:query:select user_id, concat_ws(', ',last_name,first_name) name from user u inner join person p using(person_id) order by last_name, first_name]',1)
 and e.date_of_treatment = '[date:date]'
---[Total_payment_amount,hideFilter]---
select 
sum(pay.amount) total
from payment pay
inner join encounter e on pay.encounter_id = e.encounter_id
inner join person p on e.patient_id = p.person_id
inner join patient pat on p.person_id = pat.person_id
inner join person pro on e.treating_person_id = pro.person_id
where if ('[user]',pay.user_id =
'[user]',1)
 and pay.payment_date = '[date:date]'
---[Total_payment_amount_by_type,hideFilter]---
select 
payment_type,
sum(pay.amount) total
from encounter e
inner join payment pay on pay.encounter_id = e.encounter_id
left join person p on e.patient_id = p.person_id
left join patient pat on p.person_id = pat.person_id
left join person pro on e.treating_person_id = pro.person_id
where if ('[user]',pay.user_id = '[user]',1) and e.date_of_treatment = '[date:date]'
group by payment_type
---[Total_encounters_by_provider,hideFilter]---
select 
concat_ws(', ',pro.last_name,pro.first_name) provider,
count(distinct e.encounter_id) total
from encounter e
left join payment pay on pay.encounter_id = e.encounter_id
left join person p on e.patient_id = p.person_id
left join patient pat on p.person_id = pat.person_id
left join person pro on e.treating_person_id = pro.person_id
where if ('[user]',pay.user_id = '[user]',1) and e.date_of_treatment = '[date:date]'
group by provider
---[Total_encounters,hideFilter]---
select 
count(distinct e.encounter_id) total
from encounter e
left join payment pay on pay.encounter_id = e.encounter_id
left join person p on e.patient_id = p.person_id
left join patient pat on p.person_id = pat.person_id
left join person pro on e.treating_person_id = pro.person_id
where if ('[user]',pay.user_id = '[user]',1) and e.date_of_treatment = '[date:date]'

