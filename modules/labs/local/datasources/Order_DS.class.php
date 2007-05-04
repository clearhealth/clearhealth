<?php
$loader->requireOnce('/includes/Datasource_sql.class.php');

class Order_DS extends Datasource_sql 
{
	var $_internalName = 'Lab_DS';

	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';


	function Order_DS($patientId = false) {
		$labels = array(	
			'last_name'=>'Last Name',
			'first_name'=>'First Name',
			'record_number'=>'#',
			'type'=>'Type',
			'status'=>'Status',
			'ordering_provider'=>'Ordering Provider',
			'report_time'=>'Date',
			'num_tests'=>'# Tests',
			'lab_order_id'=>false
		);
		$where = '';
		//$where = " (l.status = 'P' OR l.status = '') ";
		if ($patientId) {
			$patientId = EnforceType::int($patientId);
			$where = " l.patient_id = $patientId";
			unset($labels['last_name']);
			unset($labels['first_name']);
			unset($labels['record_number']);
		}

		$format = DateObject::getFormat();
		
		$this->setup(Celini::dbInstance(),
			array(
				'cols'    => "p.last_name,
						p.first_name,
						pt.record_number,
						l.type,
						l.status,
						l.ordering_provider,
						date_format(min(t.report_time),'$format') report_time,
						count(t.lab_test_id) num_tests,
						l.lab_order_id
						",
				'from'    => "lab_order l 
						inner join person p on p.person_id = l.patient_id 
						inner join patient pt using(person_id)
						left join lab_test t on l.lab_order_id = t.lab_order_id
						",
				'where'	  => $where,
				'groupby' => 'l.lab_order_id'
			),
			$labels);
		$this->orderHints['report_time'] = 't.report_time';
		$this->addDefaultOrderRule('report_time','ASC',false);
		$this->registerTemplate('lab_order_id','<a href="'.Celini::link('edit','Labs').'id={$lab_order_id}">Edit</a>');
	}
}

