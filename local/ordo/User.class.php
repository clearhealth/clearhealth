<?php
/**
 * Stub for generic user implementation
 *
 * @package	com.uversainc.freestand
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */

/**#@+
 * Required Libs
 */
require_once CELLINI_ROOT.'/ordo/Base_User.class.php';
/**#@-*/

/**
 * Stub user class
 *
 * @package	com.uversainc.freestand
 */
class User extends Base_User {

	function User() {
		parent::Base_User();
	}

	function users_factory($group="") {
                $users = array();
                $u = new User(null,null);
                $sql = "SELECT u.user_id from " . $u->_prefix . $u->_table . " as u ";                                                                                      
                if (!empty($group)) {
                	//op-en-hcs way of doing it
                //  $sql .= " LEFT JOIN ".$this->_prefix."users_groups as ug on ug.user_id=u.user_id LEFT JOIN ".$this->_prefix."groups as g on g.id = ug.group_id where g.name =" . $this->_db->qstr($group);
					if ($group =="provider") {
						$sql = "SELECT u.user_id FROM provider INNER JOIN user u USING ( person_id ) ";
					}
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

}
?>
