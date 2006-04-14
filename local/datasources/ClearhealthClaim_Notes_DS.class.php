<?php
$loader->requireOnce('/includes/Datasource_sql.class.php');

class ClearhealthClaim_Notes_DS extends Datasource_sql 
{
	var $_internalName = 'ClearhealthClaim_Notes_DS';

	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';


	function ClearhealthClaim_Notes_DS($claimId) {
		$claimId = EnforceType::int($claimId);
		$em =& Celini::enumManagerInstance();
		$format = DateObject::getFormat();
		
		$this->setup(Celini::dbInstance(),
			array(
				'cols'    => "
						an.account_note_id,  
						u.username, 
						date_format(date_posted,'$format') AS formatted_date_posted,
						note,
						account_note_type.value note_type",
				'from'    => "
					account_note AS an 
					INNER JOIN user AS u USING(user_id)
					" . $em->joinSql('account_note_type','note_type'),
				'orderby' => 'claim_id, date_posted',
				'where'   => "an.claim_id = {$claimId}"
			),
			array(
				'username' => 'Posted By',
				'formatted_date_posted' => 'Posted On',
				'note' => 'Note',
				'note_type' => 'Type'
			)	
		);
		
		$this->orderHints['date_posted'] = 'formatted_date_posted';
	}
}

?>
