<?php
$loader->requireOnce('includes/Datasource_sql.class.php');

class PatientBalance_DS extends Datasource_sql
{
	
	var $primaryKey = 'person_id';

	var $_internalName = 'PatientBalance_DS';
	var $_type = 'html';

	function PatientBalance_DS($newfilters=array()) {
		$session =& Celini::sessionInstance();
		$oldfilters = $session->get('balancereport:filters');
		if(empty($oldfilters)) $oldfilters = array();
		$where = array();
		$inwhere = array();
		$encwhere = '';
		$db =& Celini::dbInstance();
		
		$fkeys = array('practice','provider','includeguarantees','balance','guarantorsonly');
		$filters = array();
		foreach($fkeys as $key) {
			if(isset($newfilters[$key])) {
				$filters[$key] = $newfilters[$key];
			} else {
				$filters[$key] = isset($oldfilters[$key]) ? $oldfilters[$key] : 0;
			}
		}
		$session->set('balancereport:filters',$filters);
		foreach($filters as $ftype=>$filter) {
			switch($ftype) {
				case 'practice':
					if($filter > 0) {
						$inwhere[] = 'b.practice_id='.$db->quote($filter);
					}
					break;
				case 'provider':
					if($filter > 0) {
						$inwhere[] = 'prov.person_id='.$db->quote($filter);
					}
					break;
				case 'includeguarantees':
					if($filter == 'true') {
						$encwhere = "OR CASE WHEN e.patient_id=pp.person_id AND pp.guarantor=1 AND pp.related_person_id=pat.person_id THEN 1 END ";
					}
					break;
				case 'balance':
					if($filter=='balance') {
						$where[] = 'balance > 0 ';
					} elseif($filter=='credit') {
						$where[] = 'balance < 0';
					}
					break;
				case 'guarantorsonly':
					if($filter == 'true') {
						$inwhere[] = "pat.person_id IN(SELECT DISTINCT related_person_id FROM person_person WHERE guarantor=1)";
					}
					break;
				default:
					break;
			}
		}
		if(count($where) > 0) {
			$where = implode(' AND ',$where);
		} else {
			$where = '';
		}
		if(count($inwhere) > 0) {
			$inwhere = ' WHERE '.implode(' AND ',$inwhere);
		} else {
			$inwhere = '';
		}
		$this->setup(
			Celini::dbInstance(),
			array(
				'cols' 	=> "*",
				'from' 	=> "
	(SELECT e.*,pat.person_id,
	CONCAT(p.last_name,', ',p.first_name) patient_name,
	(
		SUM(CASE WHEN e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN total_billed ELSE 0 END) - 
		(
			SUM(CASE WHEN e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN total_paid ELSE 0 END) +
			SUM(CASE WHEN e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ELSE 0 END)
		)
	) AS `0 - 30`,
	(
		SUM(CASE WHEN 
			e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND
			e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 31 DAY)
			THEN total_billed ELSE 0 END) - 
		(
			SUM(CASE WHEN
				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND
				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 31 DAY)
				THEN total_paid ELSE 0 END) +
			SUM(CASE WHEN 
				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND
				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 31 DAY)
				THEN IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ELSE 0 END)
		)
	) AS `31 - 60`,
	(
		SUM(CASE WHEN 
			e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 90 DAY) AND
			e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 61 DAY)
			THEN total_billed ELSE 0 END) - 
		(
			SUM(CASE WHEN
				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 90 DAY) AND
				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 61 DAY)
				THEN total_paid ELSE 0 END) +
			SUM(CASE WHEN 
				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 90 DAY) AND
				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 61 DAY)
				THEN IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ELSE 0 END)
		)
	) AS `61 - 90`,
	(
		SUM(CASE WHEN 
			e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 120 DAY) AND
			e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 91 DAY)
			THEN total_billed ELSE 0 END) - 
		(
			SUM(CASE WHEN
				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 120 DAY) AND
				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 91 DAY)
				THEN total_paid ELSE 0 END) +
			SUM(CASE WHEN 
				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 120 DAY) AND
				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 91 DAY)
				THEN IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ELSE 0 END)
		)
	) AS `91 - 120`,
	(
		SUM(CASE WHEN 
			e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 150 DAY) AND
			e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 121 DAY)
			THEN total_billed ELSE 0 END) - 
		(
			SUM(CASE WHEN
				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 150 DAY) AND
				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 121 DAY)
				THEN total_paid ELSE 0 END) +
			SUM(CASE WHEN 
				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 150 DAY) AND
				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 121 DAY)
				THEN IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ELSE 0 END)
		)
	) AS `121 - 150`,
	(
		SUM(CASE WHEN e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 151 DAY) THEN total_billed ELSE 0 END) - 
		(
			SUM(CASE WHEN e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 151 DAY) THEN total_paid ELSE 0 END) +
			SUM(CASE WHEN e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 151 DAY) THEN IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ELSE 0 END)
		)
	) AS `151+`,
	( SUM(total_billed) - ( SUM(total_paid) + SUM( IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ) ) 
	) AS `balance`,
	SUM(total_billed) total_billed,
	SUM(total_paid) total_paid,
	SUM(IF(writeoffs.writeoff IS NULL,0,writeoffs.writeoff)) total_writeoff
	FROM patient AS pat
	INNER JOIN person p ON pat.person_id=p.person_id
	INNER JOIN encounter AS e ON(pat.person_id = e.patient_id $encwhere)
	INNER JOIN clearhealth_claim AS cc USING(encounter_id)
	INNER JOIN buildings b ON e.building_id=b.id
	INNER JOIN person AS prov ON prov.person_id=e.treating_person_id
	LEFT JOIN (
		SELECT
			foreign_id,
			SUM(writeoff) AS writeoff
		FROM
			payment 
		WHERE
			encounter_id = 0
		GROUP BY
			foreign_id
	) AS writeoffs ON(writeoffs.foreign_id = cc.claim_id)
	INNER JOIN storage_int AS current_payer ON (current_payer.foreign_key = e.encounter_id AND current_payer.value_key = 'current_payer'),
	person_person pp
	$inwhere
	GROUP BY pat.person_id
	) AS DATA
					",
				'where' => $where
			),
			array(
				'patient_name' => 'Patient',
				'0 - 30' => '0 - 30', 
				'31 - 60' => '31 - 60',
				'61 - 90' => '61 - 90',
				'91 - 120' => '91 - 120',
				'121 - 150' => '121 - 150',
				'151+' => '151+',
				'balance' => 'Balance'
			)
			);
		$this->registerFilter('patient_name',array(&$this,'patientLink'));

	}

	function patientLink($name,$row) {
		$link = "<a href='".Celini::link('StatementReport','Patient',true,$row['person_id'])."'>{$row['patient_name']}</a>   <a href='".Celini::link('view','PatientDashboard',true,$row['person_id'])."'>rec</a>";
		return $link;
	}

}

?>