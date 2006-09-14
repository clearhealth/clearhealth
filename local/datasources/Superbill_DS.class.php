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

		$cols = 'c.code_id, c.code, code, code_text, code_type';
		$from = 'codes c inner join superbill_data s using(code_id)';
		$groupby = '';
		if ($feeScheduleId) {
			$f = enforceType::int($feeScheduleId);
			$cols .= ', fsd.data fee';
			$from .= " left join fee_schedule_data fsd on c.code_id = fsd.code_id and fsd.fee_schedule_id = $f";
			$labels['fee'] = 'Fee';
			$groupby = 'fsd.code_id';
		}

		$this->setup(Celini::dbInstance(),
			array(
				'cols' => $cols,
				'from' => $from,
				'where' => "s.status = 1 and s.superbill_id = $id $twhere",
				'groupby' => $groupby
			),
			$labels);

		$this->addDefaultOrderRule('code','ASC',0);
		if($feeScheduleId) {
			$this->addDefaultOrderRule('fsd.revision_id','DESC');
		}
		$this->registerFilter('code', array(&$this, '_actionAddCodeLink'));
	}
	
	function _actionAddCodeLink($value, $rowValues) {
		$codeText = trim($rowValues['code_text']);
		$realCode = "{$rowValues['code']}: {$codeText}";
		$codeId = trim($rowValues['code_id']);
		// diagnosis
		if ($rowValues['code_type'] == '2') {
			$hiddenInput = 'child_id';
			$visibleInput = 'child_code';
			$finalCall = 'addICDCode';
		}
		
		// procedure
		else {
			$hiddenInput = 'parent_id';
			$visibleInput = 'parent_code';
			$finalCall = 'addCPTCode';
		}
		
		$onclickJs = "\$('{$hiddenInput}').value='{$codeId}';\$('{$visibleInput}').value='{$realCode}'";
		return "<a href=\"javascript:void(0)\" onclick=\"{$onclickJs};{$finalCall}();\">{$value}</a>";
	}
}
?>
