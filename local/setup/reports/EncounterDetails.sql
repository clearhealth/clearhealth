---[patient,noPager]---
SELECT
	patient.person_id,
	patient.last_name,
	patient.first_name,
	patient.middle_name,
	patient.date_of_birth,
	patient.identifier
FROM encounter
INNER JOIN person AS patient ON patient.person_id = encounter.patient_id
WHERE encounter.encounter_id = '<<[encounter_id:C_Encounter]>>'

---[details,noPager,hideFilter]---
SELECT
	bldg.name AS facility,
	CONCAT(doc.first_name, ' ', doc.last_name) AS treating_provider,
	encounter.date_of_treatment,
	enum_val.value AS encounter_reason,
	CONCAT(event.start, ' ', bldg.name, '->', room.name) AS appointment
FROM encounter
INNER JOIN person AS doc ON doc.person_id = encounter.treating_person_id
INNER JOIN buildings AS bldg ON bldg.id = encounter.building_id
INNER JOIN enumeration_definition AS enum_def ON enum_def.name = 'encounter_reason'
INNER JOIN enumeration_value AS enum_val ON 
	 enum_val.enumeration_id = enum_def.enumeration_id AND
	 enum_val.key = encounter.encounter_reason
LEFT JOIN appointment AS appt ON appt.appointment_id = encounter.occurence_id
LEFT JOIN rooms AS room ON room.id = appt.room_id
LEFT JOIN event ON event.event_id = appt.event_id
WHERE encounter.encounter_id = '<<[encounter_id:C_Encounter]>>'

---[encounter_people,noPager,hideFilter]---
SELECT 
	CONCAT(person.first_name, ' ', person.last_name) AS person,
	enum_val.value AS type
FROM encounter_person
INNER JOIN person ON person.person_id = encounter_person.person_id
INNER JOIN enumeration_definition AS enum_def ON enum_def.name = 'encounter_person_type'
INNER JOIN enumeration_value AS enum_val ON 
	 enum_val.enumeration_id = enum_def.enumeration_id AND
	 enum_val.key = encounter_person.person_type
WHERE encounter_person.encounter_id = '<<[encounter_id:C_Encounter]>>'

---[encounter_dates,noPager,hideFilter]---
SELECT 
	encounter_date.date,
	enum_val.value AS type
FROM encounter_date
INNER JOIN enumeration_definition AS enum_def ON enum_def.name = 'encounter_date_type'
INNER JOIN enumeration_value AS enum_val ON 
	 enum_val.enumeration_id = enum_def.enumeration_id AND
	 enum_val.key = encounter_date.date_type
WHERE encounter_date.encounter_id = '<<[encounter_id:C_Encounter]>>'
ORDER BY date ASC

---[other_encounter_info,noPager,hideFilter]---
SELECT
	encounter_value.value AS info,
	enum_val.value AS type
FROM encounter_value
INNER JOIN enumeration_definition AS enum_def ON enum_def.name = 'encounter_value_type'
INNER JOIN enumeration_value AS enum_val ON 
	 enum_val.enumeration_id = enum_def.enumeration_id AND
	 enum_val.key = encounter_value.value_type
WHERE encounter_value.encounter_id = '<<[encounter_id:C_Encounter]>>'
ORDER BY encounter_value.encounter_value_id ASC

---[copay,noPager,hideFilter]---
SELECT
	enum_val.value AS type,
	payment.payment_date,
	payment.amount,
	payment.title
FROM payment
INNER JOIN enumeration_definition AS enum_def ON enum_def.name = 'payment_type'
INNER JOIN enumeration_value AS enum_val ON 
	 enum_val.enumeration_id = enum_def.enumeration_id AND
	 enum_val.key = payment.payment_type
WHERE payment.encounter_id = '<<[encounter_id:C_Encounter]>>'

---[forms,noPager,hideFilter]---
SELECT *
FROM form_data
INNER JOIN form ON form.form_id = form_data.form_id
WHERE form_data.external_id = '<<[encounter_id:C_Encounter]>>'