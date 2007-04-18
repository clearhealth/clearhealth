<?php
$loader->requireOnce('includes/Datasource_sql.class.php');
$loader->requireOnce('includes/chlUtility.class.php');

/**
 * Provides a list of users tied to a given program
 *
 * @todo Make this configurable so it can be used outside CHLCare
 */
class Refprogram_refUserList_DS extends Datasource_sql
{
	/**#@+
	 * {@inheritdoc}
	 */
	var $_internalName = 'Refprogram_refUserList_DS';
	var $_type = 'html';
	var $hideExportLink = true;
	/**#@-*/
	
	var $isAdmin = false;
	
	function Refprogram_refUserList_DS($refprogram_id) {
		$refprogram_id = EnforceType::int($refprogram_id);
		
		$this->setup(Celini::dbInstance(), 
			array(
				'cols' => 'ru.refuser_id,
				           CONCAT(p.first_name, " ", p.middle_name, " ", p.last_name) AS provider_name,
						   u.username,
						   c.name AS clinic_name,
						   ru.refusertype,
						   "Delete" as action_delete
						   ',
				'from' => 
					'refuser AS ru
					JOIN user AS u ON(ru.external_user_id = u.user_id)
					INNER JOIN person p on p.person_id = u.person_id
					INNER JOIN practices AS c on c.id=p.primary_practice_id
					',
				'where' => 'ru.refprogram_id = ' . $refprogram_id . ' AND ru.deleted = 0',
				// Addresses an issue where multiple clinic's can have the same "clinic_id_string"
				// field.  A known issue inside CHLCare.
				'groupby' => 'ru.refuser_id'
			),
			array(
				'provider_name' => 'User\'s Name',
				'username' => 'Username',
				'clinic_name' => 'Clinic Location',
				'refusertype' => 'Role',
				'action_delete' => ''
			)
		);
		
		//$this->filter['action_delete']['universal'] = array(&$this, '_actionDeleteLink');
		$this->registerFilter('refusertype', array(&$this, '_enumLookup'));
		$this->registerFilter('action_delete', array(&$this, '_actionDeleteLink'));
	}
	
	function _enumLookup($value) {
		$em =& Celini::enumManagerInstance();
		return $em->lookup('refUserType', $value);
	}
	
	function _actionDeleteLink($value, $rowValues) {
		// note that value is already filtered when it reaches this point
		if (!$this->isAdmin && $rowValues['refusertype'] == 'Referral Manager') {
			return $value;
		}
		$url = Celini::link('removeuser', 'refprogram', 'main') . 'refuser_id=' . (int)$rowValues['refuser_id'] . '&process=true';
		$confirmJs = "if(!confirm('Are you sure you want to remove {$rowValues[username]}?')) { return false; }";
		return '<a href="' . $url .'" onclick="' . $confirmJs . '">' . $value . '</a>';
	}
}

