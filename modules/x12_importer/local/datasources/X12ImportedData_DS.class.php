<?php

$loader->requireOnce('includes/Datasource_sql.class.php');

class X12ImportedData_DS extends Datasource_sql
{
	var $_internalName = 'X12ImportedData';
	
	function X12ImportedData_DS() {
		$this->setup(
			Celini::dbInstance(),
			array(
				'cols' => '
					x12imported_data_id, 
					DATE_FORMAT(created_date, "' . DateObject::getFormat() . '") AS formatted_created_date,
					filename,
					0 claims
					',
				'from' => 'x12imported_data'
			),
			array(
				'formatted_created_date' => 'Created On',
				'filename' => 'Filename',
				'claims'=> '# Unprocessed Claims'
			)
		);

		$this->addDefaultOrderRule('claims','desc',3);
	}
}

?>
