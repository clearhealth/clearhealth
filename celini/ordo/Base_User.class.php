<?php
/**
 * Object Relational Persistence Mapping Class for table: user
 *
 * @package	com.uversainc.celini
 * @author	Joshua Eichorn <jeichorn@mail.com>
 */


/**
 * ID of the default user
 */
define('DEFAULT_USER_ID',0);

/**
 * Object Relational Persistence Mapping Class for table: user
 *
 * @package	com.uversainc.celini
 */
class Base_User extends ORDataObject {

	/**#@+
	 * Fields of table: user mapped to class members
	 */
	var $id		= 0;
	var $username	= "";
	var $password	= "";
	var $nickname	= "";
	var $color	= "";
	var $person_id	= 0;
	var $groups 	= array();
	var $disabled	= "";
	/**#@-*/

	/**
	 * Primary Key
	 */
	var $_pKey = "user_id";

	/**
	 * Portion of sql that starts the where clause of the login query
	 */
	var $_extraLoginTest = " disabled = 'no' and ";
	
	
	/** 
	 * {@inheritdoc}
	 */
	var $_table = 'user';

	/**
	 * Setup some basic attributes
	 * Shouldn't be called directly by the user, user the factory method on ORDataObject
	 */
	function Base_User($db = null) {
		parent::ORDataObject($db);	
		$this->_sequence_name = 'sequences';
		$this->_key = 'user_id';	
	}

	/**
	 * Called by factory with passed in parameters, you can specify the primary_key of User with this
	 */
	function setup($username = "", $password = "") {

		// id 0 is reserved for the default user, a named user should never have an id of 0
		$this->id = 0;

		if (!empty($username) && !empty($password)) {
			// check if this username exists and uses pop3 auth
			if ($this->usesRemoteAuth($username))
			{
				if ($this->remoteAuth($username,$password))
				{
					$this->id = $this->get_id_from_userpass($username,'!remote!');
				}
			}
			else if ($this->remoteAuth($username,$password))
			{
				$this->bootstrapRemoteUser($username);
			}
			else
			{
				$this->id = $this->get_id_from_userpass($username,$password);
			}
			if (is_numeric($this->id) && $this->id != 0) {
				$this->populate();
			}	
		}
	}

	function setupById($id) {
		$id = EnforceType::int($id);
		$this->set('id',$id);
		$this->populate();
	}
	
	
	/**
	 * Setup a {@link User} ordo based on a given username
	 *
	 * Replaces {@link Base_User::fromUsername()}
	 *
	 * @param  string  $username
	 * @access protected
	 */
	function setupByUsername($username) {
		$qUsername = $this->dbHelper->quote($username);
		$tableName = $this->tableName();
		$sql = "SELECT * FROM {$tableName} WHERE username = {$qUsername}";
		$this->helper->populateFromQuery($this, $sql);
	}


	/**
	* Authenticate against a pop3 server
	*
	* @param	$username	string
	* @param	$password	string
	* @return	boolean
	*/
	function remoteAuth($username,$password)
	{
		if (!isset($GLOBALS['config']['pop3connect']) || $GLOBALS['config']['pop3connect'] === false) {
			return false;
		}
		@$ret = imap_open($GLOBALS['config']['pop3connect'],$username,$password);
		if ($ret === false)
		{
			return false;
		}
		imap_close($ret);
		return true;
	}

	/**
	* Check if a username is setup to store its password on the remote authentication server
	* A password of "!remote!"
	*
	* @param	$username	string
	* @return	boolean
	*/
	function usesRemoteAuth($username)
	{
		$sql = "select $this->_pKey from $this->_prefix$this->_table where username = ".$this->_db->qstr($username)." and password = '!remote!'";
		$res = $this->_Execute($sql);
		if ($res && !$res->EOF)
		{
			return true;
		}
		return false;
	}

	/**
	* Create a new users with basic permissions and a remote password
	*
	* @param	$username	string
	* @return	int	user_id
	*/
	function bootstrapRemoteUser($username)
	{
		$this->set('username',$username);
		$this->set('password','!remote!');
		$this->persist();
		$this->set_id($this->get_id_from_userpass($username,'!remote!'));
	}
	
    
	/**
	* Pull data for this record from the database
	*/
	function populate() {
		parent::populate($this->_pKey);

		$this->groups = $GLOBALS['security']->getUsersGroups($this->username);
	}

	/**
	* Store data to the database
	*/
	function persist() {
		parent::persist($this->_pKey);

		// create a matching user in gacl
		$gacl_id = $GLOBALS['security']->get_object_id('Users',$this->get('username'),'ARO');
		if ($gacl_id === false) {
			$gacl_id = $GLOBALS['security']->add_object('users',$this->get('username'),$this->get('username'),$this->get('id'),1,'ARO');
		}
		$GLOBALS['security']->updateUsersGroups($this->get('username'),$this->groups);
	}

	/**
	* Static method to get a User instance from an id
	*/
	function &fromId($id) {
		settype($id,'int');
		$u =& ORDataObject::factory('User');
		$u->set('id',$id);
		$u->populate();
		return $u;
	}

	/**
	* Static method to get a User instance from an person_id
	*/
	function &fromPersonId($person_id) {
		settype($person_id,'int');
		if($person_id == 0) {
			$u =& Celini::newORDO('User');
			return $u;
		}
		$u =& ORDataObject::factory('User');

		$res = $u->_execute("select $u->_pKey from $u->_prefix$u->_table where person_id = $person_id");
		if ($res->fields) {
			$id = $res->fields[$u->_pKey];
		}
		else {
			return $u;
		}
		$u->set('id',$id);
		$u->populate();
		return $u;
	}

	/**
	* Static method to get a User instance from a username
	*
	* This method should not be used; instead use {@link User::setupByUsername()} as it is just an
	* alias to that method.
	*
	* @deprecated
	*/
	function &fromUsername($username) {
		$user =& Celini::newORDO('User', $username, 'ByUsername');
		return $user;
	}

	/**
	*	User has a this specialty function because it sometimes needs to populate having only a username and password
	*	rather than an id. This case is usually login.
	*	
	*/
	function get_id_from_userpass ($username, $password) {
		$sql = "SELECT u.$this->_pKey from $this->_prefix$this->_table as u where $this->_extraLoginTest username = " . $this->_quote($username) . " and password = " .  
			$this->_quote($password);
		//echo "sql: $sql<br>";
		$results = $this->_execute($sql);
		if (!$results->EOF) {
			return $results->fields[$this->_pKey];	
		}
		return NULL;	
	} 
    
	function users_factory($limit = "") {
		// were using an api to get the groups so this is a slow factory
		$res = $this->_execute("select $this->_pKey from $this->_prefix$this->_table".$limit);
			
		$users = array();
		$i = 0;
		while ($res && !$res->EOF) {
			$users[$i] = User::fromId($res->fields[$this->_pKey]);
			$res->MoveNext(); 
			$i++;
		}	
		return $users;
	}

	function is_group_member($test_group = "usage") {
		foreach($this->groups as $group) {
			if (isset($group['name']) && $group['name'] == $test_group) {
				return true;	
			}	
		}
		return false;
	}

	/**#@+
	 * Getters and Setters for Table: user
	 */

	
	/**
	 * Getter for Primary Key: id
	 */
	function get_user_id() {
		return $this->id;
	}

	/**
	 * Compat getter for user_id
	 */
	function get_id() {
		return $this->id;
	}

	/**
	 * person_id
	 */
	function get_person_id() {
		if (isset($this->person_id)) {
			return $this->person_id;
		}
	}

	/**
	 * person_id
	 */
	function set_person_id($id) {
		$this->person_id = $id;
	}

	/**
	 * Setter for Primary Key: id
	 */
	function set_user_id($id)  {
		$this->id = $id;
	}

	/**
	 * Setter for password, don't set when password is empty
	 */
	function set_password($password) {
		if (!empty($password)) {
			$this->password = $password;
		}
	}

	/**
	 * Get a name to disply for the user
	 */
	function get_displayname() {
		if ($this->get('person_id')) {
			$person =& ORDAtaObject::factory('Person',$this->get('person_id'));
			return $person->get('first_name').' '.$person->get('last_name');
		}
		return $this->nickname;
	}

	/**#@-*/

	/**
	 * Disable a users login
	 */
	function disable() {
		$this->set('disabled','yes');
		$this->persist();
	}

	/**
	 * Enable a users login
	 */
	function enable() {
		$this->set('disabled','no');
		$this->persist();
	}

	function getDisplayGroups($parent = false) {
		$ret = $GLOBALS['security']->sort_groups();
		if ($parent !== false) {
			$id = $GLOBALS['security']->get_group_id($parent);
			if ($id) {
				$ret = $ret[$id];
			}
		}
		else {
			$ret = $GLOBALS['security']->format_groups($ret);
		}
		return $ret;
	}

	function get_selected_group_ids() {
		return array_keys($this->get('groups'));
	}
	
	function &getChild_Person() {
		$person =& Celini::newORDO('Person', $this->get('person_id'));
		return $person;
	}
}
?>
