<?php
$loader->requireOnce('includes/Datasource_sql.class.php');
class CodeCategory_DS extends Datasource_Sql {

	var $_internalName = 'CodeCategory_DS';
	var $_type = 'html';

	function CodeCategory_DS() {
		$labels = array('actions'=>false, 'category_id' => 'ID','category_name' => 'Name', 'num' => '# Codes');

		$this->setup(Celini::dbInstance(),
			array(
				'cols' => "category_name, category_id, count(ctc.code_id) num, c.code_category_id",
				'from' => 'code_category c left join code_to_category ctc on c.code_category_id = ctc.code_category_id',
				'groupby' => 'c.code_category_id'
			),
			$labels);

		$this->addDefaultOrderRule('category_name','ASC',2);
		$this->registerTemplate('category_name','<a href="'.Celini::link('edit','CodeCategory').
			'code_category_id={$code_category_id}">{$category_name}</a>');

		$this->registerTemplate('actions','<a href="#" onclick="deleteCodeCategory(this,{$code_category_id})" title="Delete Category">X</a>');
	}
}
?>
