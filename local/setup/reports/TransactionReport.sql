/* Sql for a Transaction Report, tagged for using the standard reporting mechanism */
---[Transaction_List]---
select 
date_format(e.date_of_treatment,'%Y-%m-%d') `payment_date`,
concat_ws(', ',p.last_name,p.first_name) patient,
pat.record_number,
pay.payment_type AS 'Payment Type',
format(if(isnull(pay.amount),0,pay.amount),2) amount,
concat_ws(', ',pro.last_name,pro.first_name) provider,
ev.value encounter_note, 
concat_ws(', ',per.last_name,per.first_name) user
from encounter e
left join payment pay on pay.encounter_id = e.encounter_id
left join person p on e.patient_id = p.person_id
left join patient pat on p.person_id = pat.person_id
left join person pro on e.treating_person_id = pro.person_id
left join encounter_value ev on e.encounter_id = ev.encounter_id  AND ev.value_type =1
left JOIN user u on e.created_by_user_id = u.user_id 
left JOIN person per on per.person_id = u.person_id
where 
(
	IF (
		'[user]',
		e.created_by_user_id ='[user:query:select user_id, concat_ws(', ',last_name,first_name) name from user u inner join person p using(person_id) order by last_name, first_name]',
		1
	) OR
	IF (
		'[user2]',
		e.created_by_user_id ='[user2:query:select user_id, concat_ws(', ',last_name,first_name) name from user u inner join person p using(person_id) order by last_name, first_name]',
		0
	) OR
	IF (
		'[user3]',
		e.created_by_user_id ='[user3:query:select user_id, concat_ws(', ',last_name,first_name) name from user u inner join person p using(person_id) order by last_name, first_name]',
		0
	)
)
AND pay.payment_date = '[date:date]' 
AND if('[facility]',e.building_id = '[facility:query:select id, name from buildings order by name]',1)
/***
dsFilters-Payment Type|enumLookup&ds|payment_type
***/
---[Total_payment_amount,hideFilter]---
select
sum(pay.amount) total
from encounter e
inner join payment pay on pay.encounter_id = e.encounter_id
inner join person p on e.patient_id = p.person_id
inner join patient pat on p.person_id = pat.person_id
inner join person pro on e.treating_person_id = pro.person_id

where if ('[user]',e.created_by_user_id = '[user]',1)
 and pay.payment_date = '[date:date]'
and if('[facility]',e.building_id = '[facility]',1)


---[Total_payment_amount_by_type,hideFilter]---
select 
pay_ev.value AS 'Payment Type',
sum(pay.amount) total
from encounter e
inner join payment pay on pay.encounter_id = e.encounter_id
left join person p on e.patient_id = p.person_id
left join patient pat on p.person_id = pat.person_id
left join person pro on e.treating_person_id = pro.person_id
JOIN enumeration_value AS pay_ev ON (pay.payment_type = pay_ev.key)
JOIN enumeration_definition AS pay_ed ON(pay_ev.enumeration_id = pay_ed.enumeration_id AND pay_ed.name = 'payment_type')
where 
if ('[user]',e.created_by_user_id = '[user]',1) and pay.payment_date = '[date:date]' and if('[facility]',e.building_id = '[facility]',1)

group by payment_type

---[Total_payment_amount_by_type2,hideFilter]---
select 
payment_type,
sum(pay.amount) total
from encounter e
inner join payment pay on pay.encounter_id = e.encounter_id
left join person p on e.patient_id = p.person_id
left join patient pat on p.person_id = pat.person_id
left join person pro on e.treating_person_id = pro.person_id
where 
if ('[user2]',e.created_by_user_id = '[user2]',0) and pay.payment_date = '[date:date]' and if('[facility]',e.building_id = '[facility]',1)

group by payment_type

---[Total_payment_amount_by_type3,hideFilter]---
select 
payment_type,
sum(pay.amount) total
from encounter e
inner join payment pay on pay.encounter_id = e.encounter_id
left join person p on e.patient_id = p.person_id
left join patient pat on p.person_id = pat.person_id
left join person pro on e.treating_person_id = pro.person_id
where 
if ('[user3]',e.created_by_user_id = '[user3]',0) and pay.payment_date = '[date:date]' and if('[facility]',e.building_id = '[facility]',1)
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
where 
(if ('[user]',e.created_by_user_id = '[user]',1) and pay.payment_date = '[date:date]' and if('[facility]',e.building_id = '[facility]',1)
or
if ('[user2]',e.created_by_user_id = '[user2]',1) and pay.payment_date = '[date:date]' and if('[facility]',e.building_id = '[facility]',0)
or
if ('[user3]',e.created_by_user_id = '[user3]',1) and pay.payment_date = '[date:date]' and if('[facility]',e.building_id = '[facility]',0))
group by provider
---[Total_encounters,hideFilter]---
select 
count(distinct e.encounter_id) total
from encounter e
left join payment pay on pay.encounter_id = e.encounter_id
left join person p on e.patient_id = p.person_id
left join patient pat on p.person_id = pat.person_id
left join person pro on e.treating_person_id = pro.person_id
where 
(if ('[user]',e.created_by_user_id = '[user]',1) and pay.payment_date = '[date:date]' and if('[facility]',e.building_id = '[facility]',1)
or
if ('[user2]',e.created_by_user_id = '[user2]',2) and pay.payment_date = '[date:date]' and if('[facility]',e.building_id = '[facility]',0)
or
if ('[user3]',e.created_by_user_id = '[user3]',3) and pay.payment_date = '[date:date]' and if('[facility]',e.building_id = '[facility]',0))
