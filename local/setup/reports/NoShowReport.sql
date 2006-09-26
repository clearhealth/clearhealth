select 
 date_format(o.start,'%a %m/%d/%Y') `Date`,
 concat_ws(' to ',date_format(o.start,'%H:%i'),date_format(o.end,'%H:%i')) `Time`, 
 concat(
  floor((unix_timestamp(o.end) - unix_timestamp(o.start)) / 60 / 60),
  ' hours ',
  floor((unix_timestamp(o.end) - unix_timestamp(o.start)) / 60 % 60),
  ' minutes'
 )Duration, 
 concat(b.name, '->', r.name) Location, 
 concat(p.last_name,', ',p.first_name) Patient, 
 o.notes Title, 
 o.reason_code Reason, 
 concat(pro.last_name,', ',pro.first_name) Provider
from 
 schedules s 
 inner join events e on e.foreign_id = s.id
 inner join occurences o on e.id = o.event_id
 inner join rooms r on o.location_id = r.id
 inner join buildings b on b.id = r.building_id
 inner join person p on o.external_id = p.person_id
 inner join user u on o.user_id = u.user_id
 inner join person pro on u.person_id = pro.person_id
where 
 s.schedule_code = 'ns' and
 if ('[after]',o.start > '[after:date]',1) and
 if ('[before]',o.end < '[before:date]',1) and
 if ('[facility]',o.location_id = '[facility:rooms_building_practice_level]',1)

/***
dsFilters-Reason|enumLookup&ds|appointment_reasons
***/
