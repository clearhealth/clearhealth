<?php
/**
 * @package com.uversainc.clearhealth
 */
 
/**##@+
 * Required file
 */
$loader->requireOnce('includes/acl/AbstractAuth.abstract.php');
$loader->requireOnce('includes/UserProfile.class.php');
/**#@-*/

/**
 * Provides Clearhealth specific authorization
 *
 * @package com.uversainc.clearhealth
 */
class Auth extends AbstractAuth
{
	/**
	 * {@inheritdoc}
	 */
	function canI($what, $where = null) {
		return parent::canI($what, $where);
	}
	
	
	/**
	 * {@inheritdoc}
	 */
	function can($who, $what, $where = null) {
		$override = Auth::_patientPermission($who, $what, $where);
		return is_null($override) ? parent::can($who, $what, $where) : $override;
	}
	
	
	/**
	 * Checks to see if the user has access to access a given patient's data.
	 *
	 * This will only attempt to insert itself into certain controllers.  If a action is being
	 * performed on a controller where practice level permissions need to be applied, this will 
	 * attempt to apply them.
	 *
	 * If possible, you want to implement this checking directly into to code as it will run faster
	 * because the query can happen at the same time.  See {@link C_PatientFinder::SmartSearch()} 
	 * for an example.  This serves as a back-up to directly accessing a Patient, Encounter, etc.
	 *
	 * This method will return NULL if it does not apply to the current ACL request.  It will 
	 * return TRUE or FALSE if it should be applied.
	 *
	 * @param  string  $who
	 * @param  string  $what
	 * @param  string  $where
	 * @return boolean|null
	 * @access private
	 * @see    Auth::can()
	 *
	 * @todo Create an Auth::getInstance() so this information can be cached instead of having to
	 *       query the DB each time a request comes through.
	 */
	function _patientPermission($who, $what, $where) {
		// If the user is not logged in, or has override permissions do not perform a practice
		// specific permission check
		if (empty($who) || parent::can($who, 'override', $where)) {
			return;
		}
		
		// only perform practice specific permission checks on certain controllers
		static $controllers = array('encounter', 'patient', 'patientdashboard');
		if (!in_array(strtolower($where), $controllers)) {
			return;
		}
		
		// the "add" action does not to have a per-practice check performed
		if (($where == 'patient' || $where == 'encounter') && $what == 'add') {
			return;
		}
		
		$filteredGet =& Celini::filteredGet();
		switch ($where) {
			case 'encounter' :
				$encounter =& Celini::newORDO('Encounter', $filteredGet->get('id'));
				$patientId = $encounter->get('patient_id');
				break;
			
			default:
				$patientId = $filteredGet->get('id');
				if (empty($patientId)) {
					$patientId = $filteredGet->get(0);
				}
				break;
		}
		
		$user =& Celini::newORDO('User', $who, 'ByUsername');
		$userProfile =& new UserProfile($user->get('id'));
		$db = new clniDB();
		$qPatientId = $db->quote($patientId);
		
		$practiceList = implode(', ', $userProfile->getPracticeIdList());
		$sql = "
			SELECT
				*
			FROM
				person AS psn
				LEFT JOIN secondary_practice AS secondary ON(psn.person_id = secondary.person_id)
			WHERE
				psn.person_id = {$qPatientId} AND
				(
					psn.primary_practice_id IN({$practiceList}) OR 
					secondary.practice_id IN({$practiceList})
				)";
		$result = $db->execute($sql);
		
		return !$result->EOF;
	}
}

?>
