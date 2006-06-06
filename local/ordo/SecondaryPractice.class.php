<?php

class SecondaryPractice extends ORDataObject
{
	var $secondary_practice_id = '';
	var $person_id = '';
	var $practice_id = '';
	
	var $_foreignKeyList = array(
		'practice_id' => 'Practice'
	);
	var $_table = 'secondary_practice';
	var $_key = 'secondary_practice_id';
}

?>
