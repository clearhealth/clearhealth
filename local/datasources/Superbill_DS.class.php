<?php
$loader->requireOnce('includes/Datasource_sql.class.php');
class Superbill_DS extends Datasource_sql {

	var $_internalName = 'Superbill_DS';
	var $_type = 'html';

	function Superbill_DS($id = false,$type = false,$feeScheduleId = false) {
		$labels = array( 'code' => 'Code', 'code_text' => 'Code name');

		if ($id === false) {
			$session =& Celini::SessionInstance();
			$id = $session->get('Superbill:id');
		}

		$id = enforceType::int($id);

		$twhere = '';
		if ($type) {
			if ($type == 'procedure') {
				$tmp = array(3,4,5);
			}
			else {
				$tmp = array(2);
			}
			$twhere .= " and c.code_type in(".implode(',',$tmp).") ";
		}

		$cols = 'c.code_id, c.code, code, code_text';
		$from = 'codes c inner join superbill_data s using(code_id)';

		if ($feeScheduleId) {
			$f = enforceType::int($feeScheduleId);
			$cols .= ', fsd.data fee';
			$from .= " left join fee_schedule_data fsd on c.code_id = fsd.code_id and fsd.fee_schedule_id = $f";
			$labels['fee'] = 'Fee';
		}

		$this->setup(Celini::dbInstance(),
			array(
				'cols' => $cols,
				'from' => $from,
				'where' => "s.status = 1 and s.superbill_id = $id $twhere",
			),
			$labels);

		$this->addDefaultOrderRule('code_type','ASC');
		$this->addDefaultOrderRule('code','ASC',0);
	}
}
?>
