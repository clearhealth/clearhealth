<?php
$loader->requireOnce('includes/Datasource_sql.class.php');

/**
 * Displays a person's related person's addresses
 *
 * @package com.clear-health.clearhealth
 */
class Splash_DS extends Datasource_sql {
	/**
	 * {@inheritdoc}
	 */
	var $_internalName = 'Splash_DS';
	
	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';
	
	
	function Splash_DS() {
		
		$this->setup(Celini::dbInstance(),
			array(	'cols' 	=> "
							*
							",
						'from' 	=> "
							splash sp",
						'where'	=> ""
			),
			array(
				'name' => 'Name',
				'splash_id' => 'ID'
			)
		);
		$this->registerTemplate('splash_id','<a href="' .Celini::link('edit') . 'splash_id={$splash_id}">{$splash_id}</a>');
		
	}
}
?>
