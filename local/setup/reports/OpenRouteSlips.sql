Select
 date_format(e.date_of_treatment,'%m/%d/%Y') `Date of Treatment`,
 concat(per.last_name,', ',per.first_name,' #',record_number) Patient,
 concat(pro.last_name,', ',pro.first_name) Provider,
 route_slip_id `Route Slip #`,
 date_format(report_date,'%m/%d/%Y') `Route Slip Date`,
 count(cd.coding_data_id) `# Claim Lines`
from
 route_slip rs
 inner join encounter e using(encounter_id)
 inner join patient p on e.patient_id = p.person_id
 inner join person per using(person_id)
 inner join person pro on e.treating_person_id = pro.person_id
 left join  coding_data cd on e.encounter_id = cd.foreign_id
where
 e.status = 'open'
group by
 route_slip_id,
 e.encounter_id
order by report_date DESC
