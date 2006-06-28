<?php
$loader->requireOnce('includes/Datasource_sql.class.php');
class Superbill_DS extends Datasource_sql {

	var $_internalName = 'Superbill_DS';
	var $_type = 'html';

	function Superbill_DS($id = false,$type = false) {
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

		$this->setup(Celini::dbInstance(),
			array(
				'cols' => 'c.code_id, c.code, code, code_text',
				'from' => 'codes c inner join superbill_data s using(code_id)',
				'where' => "s.status = 1 and s.superbill_id = $id $twhere",
				'orderby' => 'code_type,code'
			),
			$labels);
	}
}
?>
