<?php
/**
 * Class were extending
 */
$loader->requireOnce('/includes/Datasource_sql.class.php');
/**
 * Datasource for handling secondary practice info
 *
 * @package	com.uversainc.clearhealth
 */
class Person_SecondaryPractice_DS extends Datasource_sql {
	/**
	 * Stores the case-sensative class name for this ds and should be considered
	 * read-only.
	 *
	 * This is being used so that the internal name matches the filesystem
	 * name.  Once BC for PHP 4 is no longer required, this can be dropped in
	 * favor of using get_class($ds) where ever this property is referenced.
	 *
	 * @var string
	 */
	var $_internalName = 'Person_SecondaryPractice_DS';
	
	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';
	
	function Person_SecondaryPractice_DS($person_id) {
		$person_id = EnforceType::int($person_id);
		
		$this->setup(
			Celini::dbInstance(),
			array(
				'cols' => '
					sp.secondary_practice_id,
					prac.name AS practice',
				'from' => '
					secondary_practice AS sp
					INNER JOIN practices AS prac ON(prac.id = sp.practice_id)',
				'where' => "sp.person_id = {$person_id}"
				),
			array(
				'practice' => 'Practice',
				'action_delete' => ''
			)
		);
		
		$this->registerFilter('action_delete', array(&$this, '_addDeleteLink'));
	}
	
	function _addDeleteLink($value, $rowValues) {
		// todo implement
		$url = Celini::link('remove', 'SecondaryPractice', false, $rowValues['secondary_practice_id']) . 'process=true&redirect=true';
		return '<a href="' . $url . '">Delete</a>';
	}
}
?>
