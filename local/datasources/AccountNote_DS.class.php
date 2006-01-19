<?php
$loader->requireOnce('/includes/Datasource_sql.class.php');

class AccountNote_DS extends Datasource_sql 
{
	var $_internalName = 'AccountNote_DS';

	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';


	function AccountNote_DS($patientId) {
		$patientId = EnforceType::int($patientId);

		$manager =& Celini::enumManagerInstance();

		$format = DateObject::getFormat();
		
		$this->setup(Celini::dbInstance(),
			array(
				'cols'    => "an.account_note_id, 
						an.patient_id, 
						an.claim_id, 
						an.user_id, 
						u.username, 
						date_format(date_posted,'$format') date_posted,
						note,
						account_note_type.value note_type",
				'from'    => "account_note an inner join user u using(user_id) ".$manager->joinSql('account_note_type','note_type'),
				'orderby' => 'claim_id, date_posted',
				'where'   => "an.patient_id = {$patientId}"
			),
			false);
	}
}

