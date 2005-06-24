---[census]---
select
 concat(p.last_name,', ',p.first_name) Patient,
 e.encounter_reason Reason,
 if (o.walkin,'Y','N') `Walk-in?`,
 date_format(o.start,'%a %m/%d/%Y') `Date`,
 concat_ws(' to ',date_format(o.start,'%H:%i'),date_format(o.end,'%H:%i')) `Time`,
 concat_ws(', ',pro.last_name,pro.first_name) Provider,
 b.name Facility
from
 occurences o
 inner join person p on o.external_id = p.person_id
 inner join encounter e on e.occurence_id = o.id
 left join person pro on e.treating_person_id = pro.person_id
 left join buildings b on e.building_id = b.id
where
 if ('[after]',o.start > '[after:date]',1) and
 if ('[before]',o.end < '[before:date]',1) and
 if ('[facility]',e.building_id = '[facility:query:select b.id, b.name from buildings b order by b.name]',1) and
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
 if ('[facility]',e.building_id = '[facility]',1) and
 if ('[provider]',o.user_id = '[provider]',1)
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
 if ('[facility]',e.building_id = '[facility]',1) and
 if ('[provider]',o.user_id = '[provider]',1)
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
 if ('[facility]',e.building_id = '[facility]',1) and
 if ('[provider]',o.user_id = '[provider]',1)
group by
 o.walkin
---[total_encounters_by_facility,hideFilter,noPager]---
select
 b.name Facility,
 count(e.encounter_id) total
from
 occurences o
 inner join person p on o.external_id = p.person_id
 inner join encounter e on e.occurence_id = o.id
 inner join buildings b on e.building_id = b.id
where
 if ('[after]',o.start > '[after:date]',1) and
 if ('[before]',o.end < '[before:date]',1) and
 if ('[facility]',e.building_id = '[facility]',1) and
 if ('[provider]',o.user_id = '[provider]',1)
group by
 b.id
---[total_encounters_by_provider,hideFilter,noPager]---
select
 concat_ws(', ',pro.last_name,pro.first_name) Provider,
 count(e.encounter_id) total
from
 occurences o
 inner join person p on o.external_id = p.person_id
 inner join encounter e on e.occurence_id = o.id
 inner join person pro on e.treating_person_id = pro.person_id
where
 if ('[after]',o.start > '[after:date]',1) and
 if ('[before]',o.end < '[before:date]',1) and
 if ('[facility]',e.building_id = '[facility]',1) and
 if ('[provider]',o.user_id = '[provider]',1)
group by
 pro.person_id
