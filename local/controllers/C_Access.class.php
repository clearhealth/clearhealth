<?php

require_once CELLINI_ROOT . "/controllers/C_Base_Access.class.php";
require_once APP_ROOT . "/local/controllers/C_Calendar.class.php";

require_once APP_ROOT . "/local/ordo/User.class.php";
require_once APP_ROOT . "/local/ordo/Practice.class.php";
require_once APP_ROOT . "/local/ordo/Room.class.php";

/**
 * Extends the base access controller added in application specific functionality
 *
 * @todo Move user editing out to a user controller
 */
class C_Access extends C_Base_Access {

	function login_session_setup(&$user) {
		$c = new C_Calendar();
	//	$c->set_filter_action("location/" . $user->get_default_location_id());
	}
	 
	function list_users_action() {


		$this->sec_obj->acl_qcheck("edit",$this->_me,"","user",$this,false);

		$this->assign("TOP_ACTION", Cellini::link('list_users','access'));
		$this->assign('EDIT_USER_ACTION', Cellini::link('edit_user'));
		$this->assign('ADD_USER_ACTION', Cellini::link('add_user'));

		$u =& ORDataObject::factory('User');
		$users = $u->users_factory();
		$this->assign("users",$users);
		return $this->fetch($GLOBALS['frame']['config']['template_dir'] ."/access/" . $this->template_mod . "_list_users.html");
	}
	
	function edit_user_action($id) {
		
		if (!is_numeric($id)) {
			echo "No suitable user id was provided, please check your query string.";	
		}
		

		$this->sec_obj->acl_qcheck("edit",$this->_me,"","user",$this,false);
		$u = new User(null,null);
		
		$p = new Practice();
		$pa = $p->practices_factory();
		$r = new Room();
		
		//false is because we do not want a blank inserted at the beginning of the array
		if(count($pa) > 0)
			$this->assign("rooms_practice_array",$r->rooms_practice_factory($pa[0]->get_id(),false));
		
		if (!isset($this->users[0]) || !is_object($this->users[0])) {
			if (empty($id) || $id == 0 ) {
			  $this->sec_obj->acl_qcheck("add",$this->_me,"certificate","new",$this,false);	
			}
			else {
			  $this->sec_obj->acl_qcheck("add",$this->_me,"certificate",$id,$this,false);
			}
			
			//manually create user object becuase we only have an id not a user and pass which the contructor requires
			
			$u->id = $id;
			$u->populate();		
			$this->assign("user",$u);
			//var_dump($u);
		}

		//$this->assign("TOP_ACTION", "index.php?" . "main&access&action=edit_user&user_id=$id");
		$this->assign("TOP_ACTION", Cellini::link('edit_user'). "&user_id=$id");
		$groups = $u->groups_factory();
		$this->assign("groups",$groups);
	
	
		return $this->fetch($GLOBALS['frame']['config']['template_dir'] ."/access/" . $this->template_mod . "_edit_user.html");	
	}
	
	function add_user_action() {
		
		$this->sec_obj->acl_qcheck("add",$this->_me,"","user",$this,false);
		
		$this->assign("TOP_ACTION", Cellini::link('add_user'));
		$u = new User(null,null);
		$groups = $u->groups_factory();
		$this->assign("groups",$groups);
		$p = new Practice();
                $pa = $p->practices_factory();
		$r = new Room();
		$this->assign("rooms_practice_array",$r->rooms_practice_factory($pa[0]->get_id(),false));	
		return $this->fetch($GLOBALS['frame']['config']['template_dir'] ."/access/" . $this->template_mod . "_add_user.html");	
	}
	
	function add_user_action_process() {
		
		if ($_POST['process'] != "true")
			return;
		
		$this->sec_obj->acl_qcheck("add",$this->_me,"","user","",false);
		  
		$db = $GLOBALS['config']['adodb']['db'];
		
		//$gapi = new gacl_api($GLOBALS['config']['gacl']);
		
		$messages = "";
		
		$name = addslashes($_POST['username']);
		$value = $name;
		$nickname = $_POST['nickname'];
		$color = $_POST['color'];
		$default_location_id = $_POST['default_location_id'];
		$password = $_POST['password'];
		$password2 = $_POST['password2'];
		$groups = $_POST['groups'];
		if (!is_array($groups)) {
			$groups = array();
		}
		if (!empty($password) && $password == $password2) {
		  
		  //$retval = $gapi->add_object("users", $name, $value , "1", 0, "aro");
			$retval = $db->GenID($GLOBALS['frame']['config']['db_prefix']."sequences");
			$sql = "INSERT INTO ".$GLOBALS['frame']['config']['db_prefix']."users (id, username, password, nickname, color, default_location_id) "
		  		  ." VALUES (" . $db->qstr($retval) . "," . $db->qstr($name) . "," . $db->qstr($password) . ", " . $db->qstr($nickname) . ",". $db->qstr($color). ",". $db->qstr($default_location_id) .")";
		  $db->Execute($sql);
		  if ($db->Affected_Rows() == 1) {
		  	$messages .= "Added user $name succesfully.<br>";	
		  }
		  else {
			$messages .= "User already exists, no action taken.<br>";	
			return;
		  }
		  
		  $user_id = $retval;
		  unset($retval);
		  
		  
		  $u = new User(null,null);
		  $pgroups = $u->groups_factory();
		  $pgroups = array_flip($pgroups);
		  if (in_array($pgroups['superadmin'],$groups)) {
		  	$retval = $this->sec_obj->acl_qcheck("add",$this->_me,"","superadmin","");
		  	if ($retval == false) {
		  		$messages .= "User added but you do not have permission to make user a superadmin.<br>";
		  		return;	
		  	}
		  }
		  
		  foreach ($groups as $group_id) {
		    if (is_numeric($group_id)) {
		  	  //$retval = $gapi->add_group_object($group_id, "users", $value,"ARO");
					$retval = $db->GenID($GLOBALS['frame']['config']['db_prefix']."sequences");
					$sql = "INSERT INTO ".$GLOBALS['frame']['config']['db_prefix']."users_groups values (" . $db->qstr($retval) . "," . $db->qstr($user_id) . "," . $db->qstr($group_id) . ",0,0)";
		  	  $db->Execute($sql);
		  	  if ($db->Affected_Rows() == 1) {
		  		//echo "Added to group: $group_id  <br>";	
		  	  }
		  	  else {
				  $messages .= "User could not be added to group, consult an administrator there may be errors in the security settings.<br>";	
		  	  }
		    }
		    else {
		  	  $messages .= "No valid group supplied, user is not in a group.<br>";
		    }
		  }
		}
		else {
		  $messages .= "Passwords empty or they do not match, could not add user try again.<br>";
		}
		$this->assign("messages", $messages);
		$this->_state = false;
		return $this->add_user_action();
		
	}
	
	function edit_user_action_process($id) {
		error_reporting(E_ALL^E_NOTICE);
		if ($_POST['process'] != "true")
			return;
		$db = $GLOBALS['config']['adodb']['db'];
		
		//$gapi = new gacl_api($GLOBALS['config']['gacl']);
		$messages = "";
		$name = addslashes($_POST['username']);
		$value = $name;
		$nickname = $_POST['nickname'];
		$color = $_POST['color'];
		$default_location_id = $_POST['default_location_id'];
		$password = $_POST['password'];
		$password2 = $_POST['password2'];
		$groups = $_POST['groups'];
		if (!is_array($groups)) {
			$groups = array();
		}
		
		if ($password == $password2) {
		  $password_sql = "";
		  if (!empty($password) && !empty($password2)) {
		  	$password_sql = ", password = " . $db->qstr($password);
		  }
		  //$retval = $gapi->add_object("users", $name, $value , "1", 0, "aro");
		  
			$sql = "UPDATE ".$GLOBALS['frame']['config']['db_prefix']."users set username =". $db->qstr($name) . ", nickname=" . $db->qstr($nickname). ", color=" . $db->qstr($color). ", default_location_id=" . $db->qstr($default_location_id) . $password_sql . " where id= " . $db->qstr($id);
			$db->Execute($sql);
		  if ($db->Affected_Rows() != -1) {
				$messages .= "Updated user $name succesfully.\n<br>";
		  }
		  else {
			$messages .= "Error updating user, database update did not affect any rows.";
			$this->assign("messages", $messages);	
			return;
		  }
		  
		  $user_id = $id;
		  unset($retval);
		  
		  $u = new User(null,null);
		  $pgroups = $u->groups_factory();
		  $pgroups = array_flip($pgroups);
		  if (in_array($pgroups['superadmin'],$groups)) {
		  	$retval = $this->sec_obj->acl_qcheck("add","","","superadmin","");
		  	if ($retval == false) {
		  		
		  		$messages .= "User updated but you do not have permission to make user a superadmin.<br>";
		  		return;	
		  	}
		  }
		  
			$sql = "DELETE FROM ".$GLOBALS['frame']['config']['db_prefix']."users_groups where user_id = " . $db->qstr($user_id);
		  $db->Execute($sql);
		  foreach ($groups as $group_id) {
		    if (is_numeric($group_id)) {
		  	  //$retval = $gapi->add_group_object($group_id, "users", $value,"ARO");
					$retval = $db->GenID($GLOBALS['frame']['config']['db_prefix']."sequences");
					$sql = "INSERT INTO ".$GLOBALS['frame']['config']['db_prefix']."users_groups values (" . $db->qstr($retval) . "," . $db->qstr($user_id) . "," . $db->qstr($group_id) . ",0,'')";
		  	  $db->Execute($sql);
		  	  if ($db->Affected_Rows() == 1) {
		  		//echo "Added to group: $group_id  <br>";	
		  	  }
					else {
					$messages .= "User could not be added to group, there may be errors in the security settings, consult and administrator.";
		  	  }
		    }
		    else {
		  	  $messages .= "No valid group supplied, user is not in a group.";
		    }
		  }
		}
		else {
		  $messages .= "Passwords empty or they do not match, could not update user try again.";
		}
		$this->messages->addMessage("",$messages);
		error_reporting(E_ALL);
	}
}
?>
