<?php

$loader->requireOnce('includes/Datasource_sql.class.php');
$loader->requireOnce('includes/chlUtility.class.php');
/**
 * Serves as quick means of getting a full list of request objects
 */
class Refpatient_refRequestListKept_DS extends Datasource_sql
{
	/**#@+
	 * {@inheritdoc}
	 */
	var $_internalName = 'Refpatient_refRequestListKept_DS';
	var $_type = 'html';
	var $hideExportLink = true;
	/**#@-*/
	
	/**
	 * Handle initialization of DS
	 */
	function Refpatient_refRequestListKept_DS($patient_id,$forlist=false) {
		$userid =& $_SESSION['sLoggedInPCCUserID'];
		$clinicid =& $_SESSION['sLoggedInClinicID'];
		$clinicgroup =& $_SESSION['sLoggedInClinicGroupName'];
		$db =& Celini::dbInstance();
		$sql = "SELECT u.clinic_id_string FROM ".chlUtility::chlCareTable('users')." u WHERE u.user_id=".$db->quote($userid);
		$res = $db->execute($sql);
		$userclinicstring = $res->fields['clinic_id_string'];
		$sql = "SELECT refuser.* FROM refuser WHERE deleted=0 AND refuser.external_user_id=".$db->quote($userid);
		$res = $db->execute($sql);
		$initprogs = array();
		$manprogs = array();
		for($res->MoveFirst();!$res->EOF;$res->MoveNext()) {
			if($res->fields['refusertype'] == 1) {
				$initprogs[] = $res->fields['refprogram_id'];
			} else {
				$manprogs[] = $res->fields['refprogram_id'];
			}
		}
		
		$enforcer =& new EnforceType();
		$patient_id = $enforcer->int($patient_id);
		$this->setup(Celini::dbInstance(), 
			array(
				'cols' => '
					r.refRequest_id,
					date_format(r.`date`, "%M %d, %Y") AS `formatted_date`,
					r.reason,
					p.name AS practice_name,
					r.refSpecialty',
				'from' => '
					refRequest AS r
					INNER JOIN refappointment AS a ON(r.refRequest_id = a.refrequest_id)
					INNER JOIN refpractice AS p USING(refpractice_id)
					INNER JOIN enumeration_value AS ev ON(r.refStatus = ev.key)
					INNER JOIN enumeration_definition AS ed USING(enumeration_id)
					INNER JOIN '.chlUtility::chlCareTable('patients').' pat ON r.patient_id=pat.patient_id
					INNER JOIN refuser initrefuser ON r.initiator_id=initrefuser.external_user_id
					INNER JOIN '.chlUtility::chlCareTable('users').' inituser ON initrefuser.external_user_id=inituser.user_id
					',
				'where' => "
IF(r.initiator_id IN('".implode("','",$manprogs)."'),
	IF(pat.chl_authorization=0,
		IF(inituser.clinic_id_string=".$db->quote($userclinicstring).",
			1,
			IF(r.refprogram_id IN('".implode("','",$manprogs)."'),
				1,
				0
			)
		),
		1
	),
	IF(r.initiator_id IN('".implode("','",$initprogs)."'),
		IF(pat.chl_authorization != 0,
			1,
			IF(inituser.clinic_id_string=".$db->quote($userclinicstring).",
				1,
				0
			)
		),
		IF(pat.chl_authorization != 0,
			1,
			IF(inituser.clinic_id_string=".$db->quote($userclinicstring).",
				1,
				0
			)
		)
	)
)
AND
					r.patient_id = " . $db->quote($patient_id) . " AND
					ed.name = 'refStatus' AND
					ev.value = 'Appointment Kept'"
			),
			array(
				'formatted_date' => 'Referral Visit',
				'refSpecialty' => 'Specialty',
				'practice_name' => 'Referral Practice',
				'reason' => 'Reason', 
				
				//'ref
				));
		$this->registerFilter('formatted_date', array($this, '_addLinkToList'));
		
		$enumCallback = array(&$this, '_enumValue');
		$this->registerFilter('refStatus', $enumCallback, 'refStatus');
		$this->registerFilter('refSpecialty', $enumCallback, 'refSpecialty');
		
		$this->orderHints['formatted_date'] = 'r.date';
		$this->addDefaultOrderRule('formatted_date', 'DESC');
	}
	
	function _addLinkToList($value, $rowValues) {
		$passAlong = '';
		if (isset($_GET['u'])) {
			$passAlong = "u=$_GET[u]";
		}
		return '<a target="_top" href="'.Celini::link('view','refvisit', 'main') . 'refRequest_id=' . $rowValues['refRequest_id'] . '&' . $passAlong.'">' . $value . '</a>';
	}
	
	function _enumValue($value, $rowValues, $fieldName) {
		$em =& Celini::enumManagerInstance();
		return $em->lookup($fieldName, $value);
	}
}

