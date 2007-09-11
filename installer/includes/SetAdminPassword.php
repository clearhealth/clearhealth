<?php

/*
 * SetAdminPassword Class
 *
 *	Sets the admin password for the installation using apprpriate sql	
 *
 *
 */
class SetAdminPassword extends BaseAction {
	
	var $server;
	
	var $username;
	
	var $password;
	
	var $port;
	
	var $version;

	var $admin_password;

	var $database_name;
	
	function BaseAction($params){
		$this->params = $params;
	}
	
	function prepareParameters(){
		$engine =& $GLOBALS['INSTALLER']['ENGINE'];
		if(isset($this->params['server_field'])){
			$server_field = $engine->getField($this->params['server_field']);
			$this->server = $server_field->value;
		}elseif(isset($this->params['server'])){
			$this->server = $this->params['server'];
		}else{
			$this->result_message = "Could not determine server name, please provide a server_field or server parameter!";
			return FALSE;
		}
		
		if(isset($this->params['username_field'])){
			$username_field = $engine->getField($this->params['username_field']);
			$this->username = $username_field->value;
		}elseif(isset($this->params['username'])){
			$this->username = $this->params['username'];
		}else{
			$this->result_message = "Could not determine datbase username, please provide a username_field or username parameter!";
			return FALSE;
		}


		if(isset($this->params['password_field'])){
			$password_field = $engine->getField($this->params['password_field']);
			$this->password = $password_field->value;
		}elseif(isset($this->params['password'])){
			$this->password = $this->params['password'];
		}else{
			$this->result_message = "Could not determine datbase password, please provide a password_field or password parameter!";
			return FALSE;
		}


		if(isset($this->params['port_field'])){
			$port_field = $engine->getField($this->params['port_field']);
			$this->port = $port_field->value;
		}elseif(isset($this->params['port'])){
			$this->port = $this->params['port'];
		}else{
			$this->result_message = "Could not determine database port, please provide a port_field or port parameter!";
			return FALSE;
		}

		if(isset($this->params['admin_password'])){
			$admin_password = $engine->getField($this->params['admin_password']);
			$this->admin_password = $admin_password->value;
		}elseif(isset($this->params['admin_password'])){
			$this->admin_password = $this->params['admin_password'];
		}else{
			$this->result_message = "No Admin password was set.";
			return FALSE;
		}

		if(isset($this->params['database_name'])){
			$database_name = $engine->getField($this->params['database_name']);
			$this->database_name = $database_name->value;
		}else{
			$this->result_message = "No Database  was set.";
			return FALSE;
		}

		
	}


	/*
	 * This function needs to be overriden in the implementing class
	 * and should return either TRUE or FALSE.
	 * 
	 */
	function perform(){
		if($this->prepareParameters() === FALSE){
			$this->result = INSTALLER_ACTION_FAIL;
			return $this->result;
		}
		@$db_con = mysql_connect($this->server.':'.$this->port, $this->username, $this->password);

		if(!$db_con){
			@$this->result_message = "Could not connect to mysql server ".$this->server.':'.$this->port." with username $this->username: ".mysql_error($db_con);
			$this->result = INSTALLER_ACTION_FAIL;
			return $this->result;
		}else{
			$db = $this->database_name;
			$select_result = mysql_select_db($db);
			if(!$select_result){
				$this->result_message = "Error selecting the '$db' database: ".mysql_error($db_con);
				$this->result = INSTALLER_ACTION_FAIL;
				return $this->result;	
			}else{
				$password = mysql_real_escape_string($this->admin_password);
				$query = "		UPDATE `user` 
							SET `password` = '$password' 
							WHERE 
								`user`.`user_id` = 1
							AND
								`password` = 'admin'
					";
				// To prevent abuse, this password setter requires that the
				// starting admin password be 'admin'			
				$query_result = mysql_query($query);			
		

				if($query_result){
					$this->result = INSTALLER_ACTION_SUCCESS;
					$this->result_message = "Set Admin Password";
					return $this->result;	
				}else{
					$this->result_message = 
						"Error setting admin password: ".mysql_error($db_con);
					$this->result = INSTALLER_ACTION_FAIL;
					return $this->result;	

				}



			}
		}
		
		return $this->result;
		


	}
	
	/*
	 * If the return value is TRUE then 
	 * the getHTML() and submitData() functions will be called.
	 */
	function isInteractive(){
		return false;
	}
	
	
	/*
	 * If the return value is FALSE then this action will be displayed 
	 * by itself and not be grouped with other actions.
	 */
	function allowGrouping(){
		return true;
	}

	function getResult(){
		return $this->result;	
	}
	
	function getResultMessage(){
		return $this->result_message;	
	}

	function success(){
		if ($this->result === INSTALLER_ACTION_SUCCESS)
			return true;
		return false;
	}
}
?>
