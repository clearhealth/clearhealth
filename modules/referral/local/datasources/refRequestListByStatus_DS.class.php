<?php
$loader->requireOnce('/includes/Datasource_sql.class.php');
$loader->requireOnce('/includes/EnumManager.class.php');
$loader->requireOnce('/includes/chlUtility.class.php');

class refRequestListByStatus_DS extends Datasource_sql
{
	/**#@+
	 * {@inheritdoc}
	 */
	var $_internalName = 'refRequestList_DS';
	var $_type = 'html';
	var $hideExportLink = true;
	/**#@-*/
	
	/**
	 * An array of refPatient ORDO's stored by their patient ID
	 *
	 * @var array
	 * @access private
	 */
	var $_patientCorral = array();
	
	
	/**
	 * Handle initialization of DS
	 */
	function refRequestListByStatus_DS($status_key) {
		//var_dump($_SESSION);
		//not clearhealth user id but person id
		$me = Me::getInstance();
		$chuser = $me->get_user();
		$userid = $me->get_person_id();
		$clinicid = $chuser->get("default_location_id");
		$clinicgroup = $_SESSION['defaultpractice'];
		$db =& Celini::dbInstance();
		$initprogs = array();
		$manprogs = array();
		/*for($res->MoveFirst();!$res->EOF;$res->MoveNext()) {
		//	if($res->fields['refusertype'] == 1) {
				$initprogs[] = $res->fields['refprogram_id'];
		//	} elseif($res->fields['refusertype'] == 2) {
				$manprogs[] = $res->fields['refprogram_id'];
		//	}
		}*/
		$person =& Celini::newORDO('Person', $me->get_person_id());
		
		$qRefStatus = $db->quote($status_key);
		$qExternalUserId = $db->quote($person->get('id'));
		$whereSql = "r.refStatus = {$qRefStatus}";

		//TODO:this is where referral manager/multiple practice permission limit to query goes
		//$whereSql .= ' AND c.clinic_id_string = ' . $db->quote($person->get('clinic_id_string'));
//		var_dump($manprogs);
//		$db->debug = true;
		$this->setup(Celini::dbInstance(), 
			array(
				'cols' => 'r.refRequest_id,
				           r.patient_id AS last_name,
				           r.patient_id AS first_name,
						   (r.patient_id ) AS record_number,
				           r.refRequest_id,
				           date_format(`r`.`date`, "%M %d, %Y") AS `formatted_date`,
						   r.refStatus,
				           r.reason,
						   pprog.name AS program_name,
				           r.refSpecialty',
				'from' => '
					refRequest AS r
					INNER JOIN participation_program pprog on r.refprogram_id=pprog.participation_program_id
					INNER JOIN refprogram AS prog on prog.refprogram_id = pprog.participation_program_id
					INNER JOIN person AS p ON(r.patient_id = p.person_id)
					',
				'where' => " 1 AND 
				$whereSql"
			),
			array(
				'last_name' => 'Last Name',
				'first_name' => 'First Name',
				'chl_id' => 'CHL ID',
				'formatted_date' => 'Referral Request',
				'refStatus' => 'Status',
				'reason' => 'Reason', 
				'program_name' => 'Program Name',
				'clinic_name' => 'Location',
				'refSpecialty' => 'Specialty'
				//'ref
				));
		
		$this->registerFilter('formatted_date', array($this, '_addLinkToList'));
		
		$enumCallback = array(&$this, '_enumValue');
		$this->registerFilter('refStatus', $enumCallback, 'refStatus');
		$this->registerFilter('reason', $enumCallback, 'refRejectionReason');
		$this->registerFilter('refSpecialty', $enumCallback, 'refSpecialty');
		$this->registerFilter('last_name', array(&$this, '_lastNameLookup'), 'last_name');
		$this->registerFilter('first_name', array(&$this, '_firstNameLookup'), 'first_name');
		
		$this->orderHints['formatted_date'] = '`date`';
		$this->addDefaultOrderRule('formatted_date', 'DESC');
	}
	
	function _addLinkToList($value, $rowValues) {
		$passAlong = '';
		if (isset($_GET['u'])) {
			$passAlong = "u=$_GET[u]";
		}
		
		$url = Celini::link('view/' . $rowValues['refRequest_id'],'referral', 'main').$passAlong;
//		return '<a href="javascript:void(0)" onclick="loadReferralView(\'' . $url . '\', \'' . $rowValues['refRequest_id'] . '\')">' . $value . '</a>';
		return '<a href="' . $url . '">' . $value . '</a>';
	}
	
	function _enumValue($value, $rowValues, $fieldName) {
		static $enumManager;
		if (!is_numeric($value)) {
			return $value;
		}
		
		$enumManager =& EnumManager::getInstance();
		return $enumManager->lookup($fieldName, $value);
	}
	
	/**#@+
	 * In here for abstraction - allows various ordos to be manipulated to determine which value
	 * to display
	 */
	function _lastNameLookup($value) {
		if (!isset($this->_patientCorral[$value])) {
			$this->_patientCorral[$value] = Celini::newORDO('Person', $value);
		}
		
		return $this->_patientCorral[$value]->get('last_name');
	}
	
	function _firstNameLookUp($value) { 
		if (!isset($this->_patientCorral[$value])) {
			$this->_patientCorral[$value] = Celini::newORDO('Person', $value);
		}
		
		return $this->_patientCorral[$value]->get('first_name');
	}
	/**#@-*/
}

