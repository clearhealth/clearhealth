<?php
$loader->requireOnce('includes/Datasource_sql.class.php');

/**
 * Displays generic notes based on parent object and type
 *
 * @package com.uversainc.clearhealth
 */
class Person_GenericNotes_DS extends Datasource_sql {
	/**
	 * {@inheritdoc}
	 */
	var $_internalName = 'Person_GenericNotes_DS';
	
	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';
	
	var $_parentObjId = '';
	var $_noteType = '';
	
	function Person_GenericNotes_DS($parentObjId,$type = '') {
		$this->_parentObjId = (int)$parentObjId;
		$this->_noteType = $type;
		
		$qParentObjId = clniDB::quote($this->_parentObjId);
		$qTypeWhere = '';
		if (strlen($this->_noteType) > 0) {
			$qTypeWhere = " AND gn.type = " .clniDB::quote($this->_noteType);
		}
		$this->setup(Celini::dbInstance(),
			array(	'cols' 	=> "
							generic_note_id,
							note,
							type,
							p.person_id,
							concat(p.first_name, ' ', p.last_name) as created_by,
							created,
							deprecated",
						'from' 	=> "
							generic_notes gn left join person p on p.person_id = gn.person_id ",
						'where'	=> "gn.parent_obj_id = {$qParentObjId} {$qTypeWhere}",
						'orderby' => "deprecated ASC,created DESC"
			),
			array(
				'note' => 'Note',
				'created_by' => 'Created By',
				'created' => 'Created',
			)
		);
		
		//echo $this->preview();
	}
	
	
}
?>
