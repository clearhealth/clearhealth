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
		$patient_id = (int)$patient_id;
		$this->setup(Celini::dbInstance(), 
			array(
				'cols' => '
					r.refRequest_id,
					date_format(r.`date`, "%M %d, %Y") AS `formatted_date`,
					r.reason,
					p.name AS practice_name,
					r.refSpecialty,
					r.refStatus AS "referral_status"',
				'from' => '
					refRequest AS r
					LEFT JOIN refappointment AS a ON(r.refRequest_id = a.refrequest_id)
					LEFT JOIN refpractice AS p ON a.refpractice_id=p.refpractice_id
					',
				'where' => "r.patient_id = " . $patient_id . "
			),
			array(
				'formatted_date' => 'Referral Request',
				'refSpecialty' => 'Specialty',
				'practice_name' => 'Referral Practice',
				'reason' => 'Reason',
				'referral_status' => 'Status', 
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

