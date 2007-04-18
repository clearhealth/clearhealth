<?php
$loader->requireOnce('/includes/Datasource_sql.class.php');

class QueueHistory_DS extends Datasource_sql 
{
	var $_internalName = 'QueueHistory_DS';

	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';


	function QueueHistory_DS($queueId) {
		$qId = EnforceType::int($queueId);

		$em =& Celini::enumManagerInstance();
		$process = $em->lookupKey('audit_type','process');
		
		$format = TimestampObject::getFormat();
		
		$this->setup(Celini::dbInstance(),
			array(
				'cols'    => "date_format(log_date,'$format') process_date,
						u.username,
						message,
						audit_log_id
						",
				'from'    => "audit_log al 
						left join user u using(user_id)
						inner join fbqueue q on al.ordo_id = q.queue_id
						",
				'where'   => "al.ordo = 'fbqueue' and type = $process and q.queue_id = $qId"
			),
			array('process_date' => 'Date','username'=>'User','message'=>'Performed'));
		$this->orderHints['process_date'] = 'log_date';
		$this->addDefaultOrderRule('process_date','DESC');
	}
}

