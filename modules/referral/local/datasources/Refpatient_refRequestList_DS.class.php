<?php

require_once CELINI_ROOT . '/includes/Datasource_sql.class.php';
$GLOBALS['loader']->requireOnce('/includes/EnumManager.class.php');
/**
 * Serves as quick means of getting a full list of request objects
 */
class Refpatient_refRequestList_DS extends Datasource_sql
{
	/**#@+
	 * {@inheritdoc}
	 */
	var $_internalName = 'refRequestList_DS';
	var $_type = 'html';
	var $hideExportLink = true;
	/**#@-*/
	
	
	/**
	 * Handle initialization of DS
	 */
	function Refpatient_refRequestList_DS($patient_id) {
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
					r.refSpecialty,
					ev.value AS "referral_status"',
				'from' => '
					refRequest AS r
					LEFT JOIN refappointment AS a ON(r.refRequest_id = a.refrequest_id)
					LEFT JOIN refpractice AS p ON a.refpractice_id=p.refpractice_id
					INNER JOIN enumeration_value AS ev ON(r.refStatus = ev.key)
					INNER JOIN enumeration_definition AS ed USING(enumeration_id)
					INNER JOIN refuser initrefuser ON r.initiator_id=initrefuser.external_user_id AND initrefuser.refprogram_id=r.refprogram_id
					INNER JOIN '.chlUtility::chlCareTable('users').' inituser ON initrefuser.external_user_id=inituser.user_id
					LEFT JOIN refuser refinit ON refinit.external_user_id='.$userid.' AND refinit.refprogram_id=r.refprogram_id AND refinit.refusertype=1
					LEFT JOIN '.chlUtility::chlCareTable('users').' refinituser ON refinit.external_user_id=refinituser.user_id
					LEFT JOIN refuser refman ON refman.external_user_id='.$userid.' AND refman.refprogram_id=r.refprogram_id AND refman.refusertype=2
					LEFT JOIN '.chlUtility::chlCareTable('users').' refmanuser ON refman.external_user_id=refmanuser.user_id
					LEFT JOIN '.chlUtility::chlCareTable('users').' user ON user.user_id='.$userid.'
					',
				'where' => "
IF(refman.external_user_id > 0,
	1,
	IF(refinituser.user_id > 0,
		IF(inituser.clinic_id_string=refinituser.clinic_id_string,
			1,
			0
		),
		IF(inituser.clinic_id_string=user.clinic_id_string,
			1,
			0
		)
	)
)
AND
					r.patient_id = '" . $patient_id . "' AND
					ed.name = 'refStatus'"
			),
			array(
				'formatted_date' => 'Referral Request',
				'refSpecialty' => 'Specialty',
				'practice_name' => 'Referral Practice',
				'reason' => 'Reason',
				'referral_status' => 'Status', 
				
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
		return '<a target="_top" href="'.Celini::link('view/' . $rowValues['refRequest_id'],'Referral', 'main').$passAlong.'">' . $value . '</a>';
	}
	
	function _enumValue($value, $rowValues, $fieldName) {
		static $enumManager;
		$enumManager =& EnumManager::getInstance();
		return $enumManager->lookup($fieldName, $value);
	}
}

