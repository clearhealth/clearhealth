<?php
/**
 * Stub for generic user implementation
 *
 * @package com.uversainc.clearhealth
 * @author  Joshua Eichorn <jeichorn@mail.com>
 */

$loader->requireOnce('ordo/Base_User.class.php');

/**
 * Stub user class
 *
 * @package com.uversainc.clearhealth
 */
class User extends Base_User {
   
   var $default_location_id = "";

   function User() {
      parent::Base_User();
   }

   function users_factory($group="") {
                
		$users = array();

		if (!empty($group)) {
			//op-en-hcs way of doing it
			//  $sql .= " LEFT JOIN ".$this->_prefix."users_groups as ug on ug.user_id=u.user_id LEFT JOIN ".$this->_prefix."groups as g on g.id = ug.group_id where g.name =" . $this->_db->qstr($group);
			if ($group =="provider") {
				$sql = '
					SELECT
						distinct(u.user_id )
					FROM
						user AS u 
						INNER JOIN person_type AS pt USING(person_id)
						INNER JOIN enumeration_value AS ev ON(ev.key = pt.person_type)
						INNER JOIN enumeration_definition AS ed USING(enumeration_id)
					WHERE 
						ed.name = "person_type" AND
						ev.value = "Provider"';
			}
		}
		else {
			$u = new User(null,null);
			$sql = 'SELECT u.user_id FROM ' . $u->tableName() . ' AS u ';
		}
		
		$sql .= " order by u.username";

		$results = $this->_db->Execute($sql) or die ("Database Error: " . $this->_db->ErrorMsg());

		while ($results && !$results->EOF) {
		$tu = new User("","");
		$tu->set("user_id",$results->fields['user_id']);
		$tu->populate();
		$users[] = $tu;
		$results->MoveNext();
		}
		return $users;
		}

	/**
	* Get the practiceId that matches this users default location, if no location is specified use the first practice
	*
	* @todo replace call to practices_factory with a setup method that returns the default practice
	*/
	function get_DefaultPracticeId() {
		$room =& Celini::newORDO('Room',$this->get('default_location_id'));
		if ($room->isPopulated()) {
			$building =& Celini::newORDO('Building',$room->get('building_id'));
			return $building->get('practice_id');
		}
		else {
			ORDataObject::factory_include('Practice');
			$practices = Practice::practices_factory();
			if (isset($practices[0])) {
				return $practices[0]->get('id');
			}
		}
		return 0;
	}
}
?>
