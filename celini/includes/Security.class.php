<?php

$loader->requireOnce('includes/acl/Auth.class.php');
$loader->requireOnce('lib/phpgacl/gacl.class.php');
$loader->requireOnce('lib/phpgacl/gacl_api.class.php');

/**
*	Security class extends the gacl class for integration with logging/auditing
*
*	Mostly used for acl_check calls but also as a good check point for logging, auditing, transaction flow
*	Also performs lookups {@link Me}  object(s) in the session when using roles
*
*/
class Security extends gacl_api {
	
	/**
	*	Call the parent constructor with the options array if provided. This contains the db information and runtime confiuration paramters.
	*	Extends PHPGacl which offer generic access control functionality. Extends it to provide logging points, auditing points,
	*	and utility functions.
	*/
	
	function Security($options = NULL) {
		parent::gacl($options);	
	}
	
	/**
	*	More convenient function than acl_check to perform security checks
	*
	*	This includes the most common defaults used in frame for acl checks
	*	@param string Name of an action in ACO section called actions
	*	@param object Object implementing get_id to provide a value in ARO section call persons, almost always a {@link Me} object
	*	@param string Name of a resource in AXO section called resources
	*	@param object callback object implementing permissions_error, only called if permissions check fails
	*/
	
	function &acl_qcheck($action,$me,$resource_section = "resources",$resource,$caller = NULL, $silent = true) {
		
		if (!is_object($me)) {
			$me =& $_SESSION['frame']['me'];	
		}
		if (empty($resource_section) && $resource_section !== false) {
			$resource_section = "resources";
		}
		$return = Auth::canI($action, $resource);
		//echo "actions:$action  users:" . $me->get_username() . "  $resource_section:$resource<br>";
		if ($me->is_group_member("superadmin")) {
		 	$return  = true;
		}
		if ($silent || $return) {
			return $return;	
		}
		elseif(!$return) {
			/**
			*	Case where permission check fails and callback method exists to perform error handling
			*/
			if (is_callable(array($caller,"permissions_error"))) {
				$caller->permissions_error('actions', $action, 'users', $me->get_username(), $resource_section, $resource);
			}
			/**
			*	Default case where permission check fails but no callback of permissions_check is present on the callback object
			*	Uses the default HTML ouput of the Controller parent class, should be suitable even on PDA displays
			*	@see Controller::permissions_check 
			*/
			else {
				$c = new Controller();
				$c->permissions_error('action', $action, 'users', $me->get_username(), $resource_section, $resource);
			}
		}
		return false; 
	}

	function acl_check($actions,$what,$users,$who) {
		$me =& $_SESSION['frame']['me'];	
		if ($me->is_group_member("superadmin")) {
                        $return  = true;
                }

		return parent::acl_check($actions, $what, $users, $who);
	} 

	/**
	 * Ultra simple acl check
	 */
	function check($action,$resource) {
		return $this->acl_qcheck($action,false,"resources",$resource);
	}

	/**
	* Update groups for a user
	*/
	function updateUsersGroups($username,$newGroups)
	{
		$tmp = $this->get_object_groups($this->get_object_id('Users',$username,'ARO'));
		$current = array();
		if (is_array($tmp)) {
			$current = array_flip($tmp);
		}

		$all = $current;
		$new = array();
		foreach($newGroups as $id)
		{
			if (is_array($id)) {
				$id = $id['id'];
				$new[$id] = $id;
			}
			$all[$id] = 0;
		}
		if (count($new) == 0) {
			$new = array_flip($newGroups);
			}
		foreach($all as $id => $a)
		{
			if (isset($current[$id]) && !isset($new[$id])) {
				//drop
				$this->del_group_object($id,'users',$username,'ARO');
			}
			else if (!isset($current[$id]) && isset($new[$id])) {
				//add
				$ret = $this->add_group_object($id,'users',$username,'ARO');
			}
		}

	}

	/**
	* Get a list of groups for a given user
	*/
	function getUsersGroups($username)
	{
		$groups = array();
		$g_ids = $this->get_object_groups($this->get_object_id('Users',$username,'ARO'));
		if ($g_ids === false) {
			$groups = array();
		} 
		else {
			$this->groups = array();
			foreach($g_ids as $id)
			{
				$data = $this->get_group_data($id);
				$groups[$id] = array('id'=>$id,'name'=> $data[2],'title'=>$data[3]);
			}
		}
		return $groups;
	}

	/**
	* Get a list of all groups
	*/
	function getGroups()
	{
		return $this->format_groups($this->sort_groups());
	}
}
?>
