<?php
$loader->requireOnce('includes/Datasource_sql.class.php');
class SuperbillList_DS extends Datasource_Sql {

	var $_internalName = 'SuperbillList_DS';
	var $_type = 'html';

	function SuperbillList_DS() {
		$labels = array( 'name' => 'Name', 'practice' => 'Practice', 'status' => 'Status');

		$this->setup(Celini::dbInstance(),
			array(
				'cols' => "s.name, ifnull(p.name,'All') practice, case s.status WHEN 0 THEN 'disabled' ELSE 'enabled' END status, s.superbill_id",
				'from' => 'superbill s left join practices p on p.id = s.practice_id',
			),
			$labels);
	}
}
?>
