<?php
$loader->requireOnce('includes/Datasource_sql.class.php');

/**
 * @todo Make slots reflect the number of available slots, not the total number
 *    for a given month.
 */
class refProgramMembersWithSlots_DS extends Datasource_sql
{
	/**#@+
	 * {@inheritdoc}
	 */
	var $_internalName = 'refPracticeList_DS';
	var $_type = 'html';
	var $hideExportLink = true;
	/**#@-*/
	
	
	function refProgramMembersWithSlots_DS(&$request) {
		//settype($refprogram_id, 'integer');
		$request->_inPersist = true;
		$this->setup(Celini::dbInstance(), 
			array(
				'cols' => '
					prac.refpractice_id,
					IF (prac.assign_by = "Provider", prov.refprovider_id, "") AS refprovider_id,
					IF (prac.assign_by = "Practice", prac.name, CONCAT(prac.name,"/ ",prov.first_name, " ", prov.last_name)) AS name,
					(IF (slot_1.slots IS NOT NULL, slot_1.slots, prac.default_num_of_slots) - IF (used_slots_1 IS NULL, 0, used_slots_1)) AS slot_1,
					(IF (slot_2.slots IS NOT NULL, slot_2.slots, prac.default_num_of_slots) - IF (used_slots_2 IS NULL, 0, used_slots_2)) AS slot_2,
					(IF (slot_3.slots IS NOT NULL, slot_3.slots, prac.default_num_of_slots) - IF (used_slots_3 IS NULL, 0, used_slots_3)) AS slot_3',
				'from' => '
					refpractice AS prac
					INNER JOIN refSpecialtyMap AS sm ON(prac.refpractice_id = sm.external_id AND sm.external_type = "refpractice")
					INNER JOIN enumeration_value AS sm_enum USING(enumeration_value_id)
					INNER JOIN enumeration_definition AS ed USING(enumeration_id)
					LEFT JOIN refprovider AS prov ON(prac.refpractice_id = prov.refpractice_id)
					JOIN refprogram_member AS prog ON(
						IF (prac.assign_by = "Practice", 
							prac.refpractice_id = prog.external_id && prog.external_type = "Practice",
							prov.refprovider_id = prog.external_id && prog.external_type = "Provider"
						)
					)
					LEFT JOIN refprogram_member_slot AS slot_1 ON(
						prog.refprogram_member_id = slot_1.refprogram_member_id AND
						(
							(slot_1.year = DATE_FORMAT(NOW(), "%Y") AND slot_1.month = DATE_FORMAT(NOW(), "%m")) OR
							(slot_1.year IS NULL AND slot_1.month IS NULL)
						)
					)
					LEFT JOIN refprogram_member_slot AS slot_2 ON(
						prog.refprogram_member_id = slot_2.refprogram_member_id AND
						(
							(slot_2.year = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 1 MONTH), "%Y") AND slot_2.month = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 1 MONTH), "%m")) OR
							(slot_2.year IS NULL AND slot_2.month IS NULL)
						)
					)
					LEFT JOIN refprogram_member_slot AS slot_3 ON(
						prog.refprogram_member_id = slot_3.refprogram_member_id AND
						(
							(slot_3.year = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 2 MONTH), "%Y") AND slot_3.month = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 2 MONTH), "%m")) OR
							(slot_3.year IS NULL AND slot_3.month IS NULL)
						)
					)
					LEFT JOIN(
						SELECT
							COUNT(*) AS used_slots_1 ,
							appt.refpractice_id,
							appt.refprovider_id
						FROM
							refRequest AS req
							JOIN refappointment AS appt USING(refappointment_id) 
						WHERE
							req.refStatus BETWEEN 3 AND 5 AND
							DATE_FORMAT(appt.date, "%Y-%c") = DATE_FORMAT(NOW(), "%Y-%c")
						GROUP BY
							appt.refpractice_id
					) AS used_slots_1_table ON (
						IF (prac.assign_by = "Practice", 
							used_slots_1_table.refpractice_id = prac.refpractice_id,
							used_slots_1_table.refprovider_id = prov.refprovider_id
						)
					)
					LEFT JOIN(
						SELECT
							COUNT(*) AS used_slots_2 ,
							appt.refpractice_id,
							appt.refprovider_id
						FROM
							refRequest AS req
							JOIN refappointment AS appt USING(refappointment_id) 
						WHERE
							req.refStatus BETWEEN 3 AND 5 AND
							DATE_FORMAT(appt.date, "%Y-%c") = DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 MONTH), "%Y-%c")
						GROUP BY
							appt.refpractice_id
					) AS used_slots_2_table ON (
						IF (prac.assign_by = "Practice", 
							used_slots_2_table.refpractice_id = prac.refpractice_id,
							used_slots_2_table.refprovider_id = prov.refprovider_id
						)
					)
					LEFT JOIN(
						SELECT
							COUNT(*) AS used_slots_3 ,
							appt.refpractice_id,
							appt.refprovider_id
						FROM
							refRequest AS req
							JOIN refappointment AS appt USING(refappointment_id) 
						WHERE
							req.refStatus BETWEEN 3 AND 5 AND
							DATE_FORMAT(appt.date, "%Y-%c") = DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 2 MONTH), "%Y-%c")
						GROUP BY
							appt.refpractice_id
					) AS used_slots_3_table ON (
						IF (prac.assign_by = "Practice", 
							used_slots_3_table.refpractice_id = prac.refpractice_id,
							used_slots_3_table.refprovider_id = prov.refprovider_id
						)
					)
					',
				'where' => '
					refprogram_id = ' . $request->get('refprogram_id') . ' AND
					prac.status = 0 AND
					sm_enum.value  = "' . $request->value('refSpecialty') . '" AND
					ed.name = "refSpecialty" AND
					prog.inactive = 0', 
				'groupby' => '
					prog.refprogram_member_id'
			),
			array(
				'name' => 'Practice/Specialist Name',
				'slot_1' => date('M - Y'),
				'slot_2' => date('M - Y', strtotime('1 month')),
				'slot_3' => date('M - Y', strtotime('2 month'))
			)
		);
		
		$this->registerFilter('name', array(&$this, '_addLink'));
		$request->_inPersist = false;
	}
	
	function _addLink($value, $row) {
		return '<a href="javascript:handleAppointmentPopupDisplay(\'' . $value . '\', \'' . $row['refpractice_id'] . '\', \'' . $row['refprovider_id'] . '\');">' . htmlspecialchars($value) . '</a>';
	}
}

