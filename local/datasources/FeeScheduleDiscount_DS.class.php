<?php
$loader->requireOnce('/includes/Datasource_sql.class.php');

class FeeScheduleDiscount_DS extends Datasource_sql 
{
	var $_internalName = 'FeeScheduleDiscount_DS';

	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';
//EW-NOTE: I edited the first and second ar

	function FeeScheduleDiscount_DS() {
		$this->setup(Celini::dbInstance(),
			array(
				'cols'    => "p.name as practice,d.name, p.id practice_id, d.fee_schedule_discount_id edit",
				'from'    => "practices p
						left join fee_schedule_discount d on d.practice_id = p.id",
				'orderby' => 'practice'
			),
			array('practice' => 'Practice','name'=>'Fee Schedule Discount','edit'=>false));
	
		$this->registerFilter('edit',array(&$this,'editLink'));
	}

	function editLink($fsdId,$row) {
		if (empty($fsdId)) {
			return '<a href="'.Celini::link('add','FeeScheduleDiscount').'practiceId='.$row['practice_id'].'">Create Discount Table</a>';
		}
		return '<a href="'.Celini::link('edit','FeeScheduleDiscount',true,$fsdId).'">Edit Discount Table</a>';
	}
}

