<?php
$loader->requireOnce('/includes/Datasource_sql.class.php');

class Coding_List_DS extends Datasource_sql 
{
	var $_internalName = 'Coding_List_DS';

	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';


	function Coding_List_DS($externalId = false,$code_type = '1,2,3,4',$distinct = false) {
		$externalId = (int)$externalId;
		$code_type = preg_replace('/[^0-9,]*/','',$code_type);
		$labels = array(	
			'code'=>'Code',
			'code_text'=>'Desc'
		);
		$where = " cd.foreign_id = $externalId ";
		if ($code_type != '') {
		  $where .= " and c.code_type in (" . $code_type . ")";
		}
		$select = ' * ';
		if ($distinct) {
			$select = " DISTINCT c.code ";
		}
		
		$this->setup(Celini::dbInstance(),
			array(
				'cols'    =>  $select,
				'from'    => "coding_data cd 
						inner join codes c on c.code_id = cd.code_id  
						",
				'where'	  => $where,
			),
			$labels);
	}
}

