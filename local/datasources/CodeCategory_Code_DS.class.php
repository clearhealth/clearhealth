<?php
$loader->requireOnce('includes/Datasource_sql.class.php');
class CodeCategory_Code_DS extends Datasource_Sql {

	var $_internalName = 'CodeCategory_Code_DS';
	var $_type = 'html';

	function CodeCategory_Code_DS($category_id) {
		$labels = array( 'code' => 'Code', 'code_text' => 'Description');

		$id = EnforceType::int($category_id);

		$this->setup(Celini::dbInstance(),
			array(
				'cols' => "code, code_text",
				'from' => 'codes c inner join code_to_category ctc on c.code_id = ctc.code_id',
				'where' => "ctc.code_category_id = $id"
			),
			$labels);
	}
}
?>
