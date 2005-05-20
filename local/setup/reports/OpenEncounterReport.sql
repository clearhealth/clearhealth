select 
	date_format(date_of_treatment,'%Y-%m-%d') date_of_treatment, 
	date_format(timestamp,'%Y-%m-%d') last_change, 
	concat_ws(', ',p.last_name,p.first_name) patient,
	encounter_id 
from encounter 
inner join person p on patient_id = person_id 
where status = 'open'
