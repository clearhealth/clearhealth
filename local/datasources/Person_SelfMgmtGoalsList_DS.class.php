<?php
$loader->requireOnce('includes/Datasource_sql.class.php');

/**
 * Displays a person's self management goals
 *
 * @package com.clearhealth.base
 */
class Person_SelfMgmtGoalsList_DS extends Datasource_sql {
	/**
	 * {@inheritdoc}
	 */
	var $_internalName = 'Person_SelfMgmtGoalsList_DS';
	
	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';
	
	var $_personId = '';
	
	function Person_SelfMgmtGoalsList_DS($person_id) {
		$this->_personId = $person_id;
		
		$qPersonId = clniDB::quote($person_id);
		$this->setup(Celini::dbInstance(),
			array(	'cols' 	=> "
						self_mgmt_id,
						type,
						initiated,
						completed ",
				'from' 	=> "self_mgmt_goals smg",

				'where'	=> "smg.person_id = {$qPersonId}",
				'orderby'=> "completed ASC, initiated DESC")
			,
			array(
				'type' => 'Goal',
				'initiated' => 'Initiated',
				'completed' => 'Completed'
			)
		);
		
		//echo $this->preview();exit;
		$this->registerFilter('type', array(&$this, '_lookup'));
	}
	
	
	
	
	function _lookup($value) {
		$em =& Celini::enumManagerInstance();
		return $em->lookup('self_mgmt_goals', $value);
	}
	
	
}
?>
