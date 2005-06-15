---[census]---
select
 concat(p.last_name,', ',p.first_name) Patient,
 e.encounter_reason Reason,
 if (o.walkin,'Y','N') `Walk-in?`,
 date_format(o.start,'%a %m/%d/%Y') `Date`,
 concat_ws(' to ',date_format(o.start,'%H:%i'),date_format(o.end,'%H:%i')) `Time`
from
 occurences o
 inner join person p on o.external_id = p.person_id
 inner join encounter e on e.occurence_id = o.id
where
 if ('[after]',o.start > '[after:date]',1) and
 if ('[before]',o.end < '[before:date]',1) and
 if ('[facility]',o.location_id = '[facility:query:select r.id, concat(r.name,'->',b.name) name from rooms r inner join buildings b on r.building_id = b.id]',1) and
 if ('[provider]',o.user_id = '[provider:query:select u.user_id, concat(p.last_name,', ',p.first_name) name from user u inner join person p on u.person_id = p.person_id]',1)
order by
 `Date` DESC, `Time` DESC
/***
dsFilters-Reason|enumLookup&ds|encounter_reason
***/
---[total_encounters,hideFilter,noPager]---
select
 count(e.encounter_id) `Total Encounters`
from
 occurences o
 inner join person p on o.external_id = p.person_id
 inner join encounter e on e.occurence_id = o.id
where
 if ('[after]',o.start > '[after:date]',1) and
 if ('[before]',o.end < '[before:date]',1) and
 if ('[facility]',o.location_id = '[facility:query:select r.id, concat(r.name,'->',b.name) name from rooms r inner join buildings b on r.building_id = b.id]',1) and
 if ('[provider]',o.user_id = '[provider:query:select u.user_id, concat(p.last_name,', ',p.first_name) name from user u inner join person p on u.person_id = p.person_id]',1)
---[total_encounters_by_reason,hideFilter,noPager]---
select
 e.encounter_reason `Reason`,
 count(e.encounter_id) `Total`
from
 occurences o
 inner join person p on o.external_id = p.person_id
 inner join encounter e on e.occurence_id = o.id
where
 if ('[after]',o.start > '[after:date]',1) and
 if ('[before]',o.end < '[before:date]',1) and
 if ('[facility]',o.location_id = '[facility:query:select r.id, concat(r.name,'->',b.name) name from rooms r inner join buildings b on r.building_id = b.id]',1) and
 if ('[provider]',o.user_id = '[provider:query:select u.user_id, concat(p.last_name,', ',p.first_name) name from user u inner join person p on u.person_id = p.person_id]',1)
group by
 o.reason_code
/***
dsFilters-Reason|enumLookup&ds|encounter_reason
***/
---[total_encounters_by_walkin,hideFilter,noPager]---
select
 if (o.walkin,'Y','N') `Walk-in?`,
 count(e.encounter_id) `Total`
from
 occurences o
 inner join person p on o.external_id = p.person_id
 inner join encounter e on e.occurence_id = o.id
where
 if ('[after]',o.start > '[after:date]',1) and
 if ('[before]',o.end < '[before:date]',1) and
 if ('[facility]',o.location_id = '[facility:query:select r.id, concat(r.name,'->',b.name) name from rooms r inner join buildings b on r.building_id = b.id]',1) and
 if ('[provider]',o.user_id = '[provider:query:select u.user_id, concat(p.last_name,', ',p.first_name) name from user u inner join person p on u.person_id = p.person_id]',1)
group by
 o.walkin
