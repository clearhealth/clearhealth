<?php

require_once CELLINI_ROOT. "/controllers/Controller.class.php";

/**
*	Security class extends the gacl class for integration with logging/auditing
*
*	Mostly used for acl_check calls but also as a good check point for logging, auditing, transaction flow
*	Also performs lookups {@link Me}  object(s) in the session when using roles
*
*/

class Security {
	
	/**
	*	Call the parent constructor with the options array if provided. This contains the db information and runtime confiuration paramters.
	*	Extends PHPGacl which offer generic access control functionality. Extends it to provide logging points, auditing points,
	*	and utility functions.
	*/
	
	function Security($options = NULL) {
		//parent::gacl($options);	
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
			$me =& Me::getInstance();
		}
		//$return = $this->acl_check('actions', $action, 'users', $me->get_username(), $resource_section, $resource);
		//echo "actions:$action  users:" . $me->get_username() . "  $resource_section:$resource<br>";
		$return = false;

		if ($me->is_group_member("superadmin")) {
			$return  = true;
		}
		//special case determines who can edit users of certain groups, in this case the resource is the user object of the user to be edited
		else{
			if($resource_section == "users") {
				if ($resource->is_group_member("superadmin")) {
					$return = false;
				}
			}else{
				switch($action) {

				case "add":
					if($resource == "event" && ($me->is_group_member("supervisor") || $me->is_group_member("calendar user"))) {
						$return = true;
							break;
					}
						break;
				case "add_double":
					if($resource == "event" && ($me->is_group_member("supervisor"))) {
						$return = true;
							break;
					}
						break;
				case "edit":
					if($resource == "event" && $me->is_group_member("supervisor")) {
						$return  = true;
						break;
					}
					break;
				case "edit_owner":
					if ($me->is_group_member("calendar user") || $me->is_group_member("supervisor")) {
						$return  = true;
						break;
					}
					break;
				case "delete_owner":
					if ($me->is_group_member("calendar user") || $me->is_group_member("supervisor")) {
						$return  = true;
						break;
					}
					break;
				case "delete":
					if (($resource == "event" || $resource == "occurence" )&& $me->is_group_member("supervisor")) {
						$return = true;
						break;
					}
					break;
				case "usage":
					$return = true;
					break;
				default:
					$return = false;
				}
			}
		}

		if ($silent) {
			return $return;
		}elseif(!$return){
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

	
}

?>
