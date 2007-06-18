<?php

$loader->requireOnce('/includes/Datasource_sql.class.php');

class Person_RelatedList_DS extends Datasource_sql
{
	/**
	 * A cache of the relation_type enum
	 *
	 * @var array
	 * @access private
	 * @see _loadRelationTypeList()
	 */
	var $_relationshipTypes = null;
	
	
	/**
	 * Handle instantiation
	 *
	 * @param int
	 */
	function Person_RelatedList_DS($person_id) {
		settype($person_id,'int');

		$labels = array(
			'left_name'     => 'Person',
			'relation_type' => 'Relation Of',
			'right_name'    => 'Relation',
			'date_of_birth'	=> 'DOB',
			'guarantor'	=> 'Guarantor?',
			'action_delete' => ''
		);
		$this->setup(Celini::dbInstance(),
			array(
				'union' => 
				array(
					array(
					'cols' 	=> "
						t.person_person_id, 
						CONCAT_WS(' ',p.first_name, p.last_name) left_name,
						relation_type,
						CONCAT_WS(' ',r.first_name, r.last_name) right_name,
						r.date_of_birth,
						r.person_id right_id,
						p.person_id left_id,
						if(guarantor=1,concat('Yes (R of P) #',guarantor_priority+1),'No') guarantor",
					'from' 	=> "
						person_person AS t
						INNER JOIN person AS p ON (p.person_id = t.person_id)
						INNER JOIN person AS r ON (r.person_id = t.related_person_id)",
					'where'	=> "t.person_id = $person_id",
					),
					array(
					'cols' 	=> "
						t.person_person_id, 
						CONCAT_WS(' ',r.first_name, r.last_name) right_name,
						relation_type,
						CONCAT_WS(' ',p.first_name, p.last_name) left_name,
						p.person_id right_id,
						r.person_id left_id,
						r.date_of_birth,
						if(guarantor=1,concat('Yes (P of R) #',guarantor_priority+1),'No') guarantor",
					'from' 	=> "
						person_person AS t
						INNER JOIN person AS r ON (r.person_id = t.person_id) 
						INNER JOIN person AS p ON (p.person_id = t.related_person_id)",
					'where'	=> "t.related_person_id = $person_id",
					)
				)
			),
			$labels);

		$this->registerFilter('relation_type',array(&$this,'_humanReadableRelationshipType'));
		$this->registerFilter('action_delete', array(&$this, '_addDeleteAction'));
		$this->registerTemplate('right_name','<a class="dashedLink" title="View dashboard for {$right_name}" href="'.
			Celini::link('view','PatientDashboard').'id={$right_id}">{$right_name}</a>');
		$this->registerTemplate('left_name','<a class="dashedLink" title="View dashboard for {$left_name}" href="'.
			Celini::link('view','PatientDashboard').'id={$left_id}">{$left_name}</a>');

	}
	
	
	/**
	 * Changes the relation_type enum into a human readable field.
	 *
	 * @return string
	 * @access protected
	 */
	function _humanReadableRelationshipType($type) {
		$this->_loadRelationshipTypeList();
		return isset($this->_relationshipTypes[$type]) ?
			$this->_relationshipTypes[$type] :
			$type;
	}
	
	/**
	 * Loads the relation_type enum into memory
	 *
	 * @access protected
	 */
	function _loadRelationshipTypeList() {
		if (!is_null($this->_relationshipTypes)) {
			return;
		}
		
		$enum = ORDataObject::factory('Enumeration');
		$this->_relationshipTypes = $enum->get_enum_list('person_to_person_relation_type');
	}
	
	/**
	 * Adds a link to delete a relationship
	 */
	 function _addDeleteAction($value, $rowValues) {
		 $url = Celini::link('delete', 'PersonPerson') . 'id=' . $rowValues['person_person_id'] . '&embedded=true&process=true';
		 $confirmJs = "if(!confirm('Are you sure you want to remove the relationship from {$rowValues['left_name']} to {$rowValues['right_name']}?')) { return false; }";  
		 return '<a href="' . $url . '" onclick="' . $confirmJs . '">Delete</a>';
	 }
}

