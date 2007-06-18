<?php

require_once CELINI_ROOT . '/includes/Datasource_sql.class.php';

/**
 * Serves as quick means of getting a full list of request objects
 */
class Person_ReferralList_DS extends Datasource_sql
{
	/**#@+
	 * {@inheritdoc}
	 */
	var $_internalName = 'Person_ReferralList_DS';
	var $_type = 'html';
	var $hideExportLink = true;
	/**#@-*/
	
	
	
	/**
	 * Handle initialization of DS
	 */
	function Person_ReferralList_DS($patientId = 0,$encounterId = 0) {
		$patientId = (int)$patientId;
		$encounterId = (int)$encounterId;
		$where = " parprog.type = 'referral'";
		if ($patientId >0 ) {
			$where .= " and enc.patient_id = $patientId";
		}
		if ($encounterId >0 ) {
			$where .= " and enc.encounter_id = $encounterId";
		}
		$this->setup(Celini::dbInstance(), 
			array(
				'cols' => 'parprog.name, 
				rr.referral_service,
				rr.refrequest_id
				',
				'from' => '
				person_participation_program ppp 
				inner join participation_program parprog on parprog.participation_program_id = ppp.participation_program_id
				inner join refRequest rr on rr.refprogram_id = ppp.participation_program_id
				inner join encounter enc on enc.encounter_id = rr.visit_id
				left join form_data fd on fd.external_id = rr.refrequest_id
',
				'where' => $where,
				'groupby' => ' rr.refrequest_id'
			),
			array(
				'name' => 'Program',
				'referral_service' => 'Requested Service'
			)
		);
		$enumCallback = array(&$this, '_enumValue');
		$this->registerFilter('referral_service', $enumCallback, 'referral_service');
		
	}
	
	function _enumValue($value, $rowValues, $fieldName) {
		static $enumManager;
		$enumManager =& EnumManager::getInstance();
		$link = $enumManager->lookup($fieldName, $value);
		if (!strlen($link) >0) {
			$link = "_";
		}
		return '<a href="' . Celini::link('view','Referral',true,$rowValues['refrequest_id']) . '">' . $link . '</a>';

	}
}

