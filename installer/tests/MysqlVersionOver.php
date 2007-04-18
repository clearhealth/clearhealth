<?php
/*
 * MysqlVersionOver
 *
 * Tests that the running Mysql Version is >= the supplied parameter
 */
 
class MysqlVersionOver extends BaseTest{
	var $server;
	
	var $username;
	
	var $password;
	
	var $port;
	
	var $version;
	
	function MysqlVersionOver($params){
		parent::BaseTest($params);	

		if(!is_array($this->params) || count($this->params) <= 0){
			ErrorStack::addError("Invalid parameters, you need to provide the field names and version", ERRORSTACK_ERROR, 'MysqlVersionOver');
			$this->result = INSTALLER_TEST_FAIL;
			return $this->result;
		}
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
			$this->result_message = "Could not determine datbase port, please provide a port_field or port parameter!";
			return FALSE;
		}
		
		if(isset($this->params['version'])){
			$this->version = $this->params['version'];
		}else{
			$this->result_message = "Could not find required version number, please provide a version parameter!";
			return FALSE;			
		}
	}
	
	function perform(){
		if($this->prepareParameters() === FALSE){
			$this->result = INSTALLER_TEST_FAIL;
			return $this->result;
		}

		@$db_con = mysql_connect($this->server.':'.$this->port, $this->username, $this->password);
		if(!$db_con){
			@$this->result_message = "Could not connect to mysql server ".$this->server.':'.$this->port." with username $this->username: ".mysql_error($db_con);
			$this->result = INSTALLER_TEST_FAIL;
			return $this->result;
		}else{
			$mysql_version = mysql_get_server_info($db_con);
			if(!$mysql_version){
				$this->result_message = "Error getting server version information: ".mysql_error($db_con);
				$this->result = INSTALLER_TEST_FAIL;
				return $this->result;	
			}else{
				if(version_compare($mysql_version, $this->version, '>=')){
					$this->result = INSTALLER_TEST_SUCCESS;
					$this->result_message = "You are running mysql version $mysql_version which is >= $this->version";
					return $this->result;	
				}
			}
		}
		
		return $this->result;
	}
}
?>
