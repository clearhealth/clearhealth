<?php
$loader->requireOnce('includes/Datasource_sql.class.php');

/**
 * Displays a person's related person's addresses
 *
 * @package com.uversainc.clearhealth
 */
class Person_ClinicalSummary_DS extends Datasource_sql {
	/**
	 * {@inheritdoc}
	 */
	var $_internalName = 'Person_ClinicalSummary_DS';
	
	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';
	
	var $_personId = '';
	
	function Person_ClinicalSummary_DS($person_id) {
		$this->_personId = $person_id;
		
		$qPersonId = clniDB::quote($person_id);
		$this->setup(Celini::dbInstance(),
			array('union' =>	
			 array( 
			   array ( 'cols'=> "
					CASE WHEN ap.appointment_id IS NOT NULL and le.encounter_id IS NULL THEN ev.start
WHEN le.encounter_id IS NOT NULL THEN le.date_of_treatment
END AS 'date_of_service',
CASE WHEN ev.event_id IS NOT NULL and le.encounter_id IS NULL THEN 'app'
WHEN le.encounter_id IS NOT NULL THEN 'linked enc'
END AS 'contact_type', 
CASE WHEN ev.event_id IS NOT NULL and le.encounter_id IS NULL THEN ap.title
WHEN le.encounter_id IS NOT NULL THEN le.encounter_reason
END AS 'reason', 
CASE WHEN ev.event_id IS NOT NULL and le.encounter_id IS NULL THEN CONCAT(rmbd.name,'->',rm.name)
WHEN le.encounter_id IS NOT NULL THEN lebd.name
END AS 'location',
CASE WHEN le.encounter_id IS NULL THEN ap.title ELSE (select concat('Proc:',group_concat(DISTINCT c.code), ' Diag:',group_concat(DISTINCT c2.code))
from encounter e2 
left join coding_data cd on cd.foreign_id = e2.encounter_id   
inner join codes c on c.code_id = cd.code_id and c.code_type in (3,4)
left join coding_data cd2 on cd2.parent_id = cd.coding_data_id and cd2.coding_data_id 
left join codes c2 on c2.code_id = cd2.code_id
where e2.encounter_id = le.encounter_id
group by e2.encounter_id) END as 'description',
CASE WHEN ev.event_id IS NOT NULL and le.encounter_id IS NULL THEN concat(prov.first_name, ' ',prov.last_name)
WHEN le.encounter_id IS NOT NULL THEN concat(leprov.first_name, ' ',leprov.last_name)
END AS 'provider',
CASE WHEN ev.event_id IS NOT NULL and le.encounter_id IS NULL THEN ''
WHEN le.encounter_id IS NOT NULL THEN le.status
END AS 'status'
 
						",
				   'from'=> "
					person p
LEFT JOIN appointment ap ON ap.patient_id = p.person_id
LEFT JOIN event ev ON ap.event_id = ev.event_id
LEFT JOIN encounter le on le.occurence_id = ap.appointment_id
LEFT JOIN rooms rm on rm.id = ap.room_id
LEFT JOIN buildings rmbd on rm.building_id = rmbd.id
LEFT JOIN buildings lebd on le.building_id = lebd.id
LEFT JOIN person prov on prov.person_id = ap.provider_id
LEFT JOIN person leprov on leprov.person_id = le.treating_person_id
			",
				   'where'=> "p.person_id ={$qPersonId}"
				   ),//1st query
			   array ( 'cols'=> "
					e.date_of_treatment as 'date_of_service', 
'encounter' as 'contact_type', e.encounter_reason as 'reason',
bd.name as 'location',
(select concat('Proc:',group_concat(DISTINCT c.code), ' Diag:',group_concat(DISTINCT c2.code))
from encounter e2 
left join coding_data cd on cd.foreign_id = e2.encounter_id   
inner join codes c on c.code_id = cd.code_id and c.code_type in (3,4)
left join coding_data cd2 on cd2.parent_id = cd.coding_data_id and cd2.coding_data_id 
left join codes c2 on c2.code_id = cd2.code_id
where e2.encounter_id = e.encounter_id
group by e2.encounter_id) as 'description',
concat(prov.first_name, ' ',prov.last_name) as 'provider',
e.status
 
						",
				   'from'=> "
					person p
LEFT JOIN encounter e on e.occurence_id = 0 and e.patient_id = p.person_id
LEFT JOIN buildings bd on bd.id = e.building_id
LEFT JOIN person prov on prov.person_id = e.treating_person_id
			",
				   'where'=> "p.person_id ={$qPersonId}"
				   ),//2nd query
			   array ( 'cols'=> "
					CASE WHEN rr.refrequest_id IS NOT NULL and ra.refappointment_id IS NULL and rv.refreferral_visit_id IS NULL THEN
rr.date  
WHEN ra.refappointment_id IS NOT NULL OR rv.refreferral_visit_id IS NOT NULL THEN
ra.date  
END as 'date_of_service', 
CASE WHEN rr.refrequest_id IS NOT NULL and ra.refappointment_id IS NULL and rv.refreferral_visit_id IS NULL THEN
'ref request'  
WHEN ra.refappointment_id IS NOT NULL and  rv.refreferral_visit_id IS  NULL THEN
'ref app'  
WHEN rv.refreferral_visit_id IS  NOT NULL THEN
'ref visit'
END
'contact_type',
rr.reason,
CASE WHEN rr.refrequest_id IS NOT NULL and ra.refappointment_id IS NULL and rv.refreferral_visit_id IS NULL THEN
bd.name  
WHEN ra.refappointment_id IS NOT NULL OR rv.refreferral_visit_id IS NOT NULL THEN
rp.name
END as 'location',
'' as description,
concat('RI:',ri.first_name, ' ', ri.last_name) as 'provider',
rr.refStatus as 'status'
 
					
						",
				   'from'=> "
				person p
				LEFT JOIN refRequest rr on rr.patient_id = p.person_id
LEFT JOIN refappointment ra on ra.refrequest_id = rr.refrequest_id
LEFT JOIN refreferral_visit rv on rv.refappointment_id = ra.refappointment_id
LEFT JOIN buildings bd on rr.building_id = bd.id
LEFT JOIN refpractice rp on rp.refpractice_id = ra.refpractice_id
LEFT JOIN person ri on rr.initiator_id = p.person_id

			",
				   'where'=> "p.person_id ={$qPersonId}",
				   'orderby'=> 'contact_type'	
				   )//3rd query
				)//subarray
			    )//union
			,
			array(
				'contact_type' => 'Contact',
				'date_of_service' => 'Date Of Service',
				'reason' => 'Reason',
				'location' => 'Location',
				'description' => 'Description',
				'provider' => 'Provider',
				'status' => 'Status'
			)
		);
		//echo $this->preview();
		
		//var_dump($this->preview());
		$this->registerFilter('address_type', array(&$this, '_addressTypeLookup'));
		$this->registerFilter('line1', array(&$this, '_twoLineFormatting'));
	}
	
	
	function _twoLineFormatting($value, $row) {
		if (!isset($row['line2']) || empty($row['line2'])) {
			return $value;
		}
		
		return $value . ', ' . $row['line2'];
	}
	
	
	function _addressTypeLookup($value) {
		$em =& Celini::enumManagerInstance();
		return $em->lookup('address_type', $value);
	}
	
	
}
?>
