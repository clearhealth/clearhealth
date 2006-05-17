<?php
$loader->requireOnce('/includes/Datasource_sql.class.php');

class DuplicateQueue_DS extends Datasource_sql 
{
	var $_internalName = 'DuplicateQueue_DS';

	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';


	function DuplicateQueue_DS() {
		$format = DateObject::getFormat();
		
		$this->setup(Celini::dbInstance(),
			array(
				'cols'    => "
					concat(child.last_name,', ',child.first_name,' #',childp.record_number) child,
					concat(parent.last_name,', ',parent.first_name,' #',parentp.record_number) parent,
					duplicate_queue_id,
					child_id,
					parent_id
					",
				'from'    => "
					duplicate_queue dq
					inner join person child on dq.child_id = child.person_id
					inner join patient childp on child.person_id = childp.person_id
					inner join person parent on dq.parent_id = parent.person_id
					inner join patient parentp on parent.person_id = parentp.person_id
				"
			),
			array('child'=>'Patient to be merged','parent'=>'Merge target','action'=>false));
	
		$this->registerTemplate('child','<a href="'.Celini::link('view','PatientDashboard').'patient_id={$child_id}">{$child}</a>');
		$this->registerTemplate('parent','<a href="'.Celini::link('view','PatientDashboard').'patient_id={$parent_id}">{$parent}</a>');
		$this->registerTemplate('action','<a href="'.Celini::link('merge').'dq_id={$duplicate_queue_id}">Merge</a>');
	}
}

