<?php
/*
 * SQLOptions Class
 *
 * This is an abstract class that needs to have at
 * least the perform method overridden
 */
class SQLOptions extends SQLFile {
	
	var $params_prepared = false;
	var $file = "";	

	function SQLOptions($params){
		parent::BaseAction($params);
		
		$this->interactive = true;
		$this->grouping = true;
	}
	
	/*
	 * This function needs to be overriden in the implementing class
	 * and should return either TRUE or FALSE.
	 * 
	 * @var $params array Array of parameters needed for the specific implementation
	 */
	function perform(){
		if (empty($this->loop)) {
			$this->loop = 1;
		}elseif ($this->loop == 5) {
			$this->result = INSTALLER_ACTION_SUCCESS;
			$this->result_message = "Completed optional database file installation.";
			return $this->result;
		}

		if($this->prepareParameters() === FALSE){
			$this->result = INSTALLER_ACTION_FAIL;
			return $this->result;
		}
		$sql_commands = array();
		$file = $this->file;
		if(!empty($file) && !is_readable($file)){
			$this->result = INSTALLER_ACTION_FAIL;
			$this->result_message = "Could not read file sql $file.";
			$this->loop = 2;
			return $this->result;
		}else if ($this->loop < 3) {
			$this->loop = 3;
			$this->result = INSTALLER_ACTION_WARNING;
			$this->result_message = "The database file you selected is being installed, this may take several minutes especially for larger files.";
			return $this->result;
		}


		/*$file_contents = file($file);
		$file_contents = join('', $file_contents);
		if(!$this->splitMySqlFile($sql_commands[], $file_contents)){
			$this->result = INSTALLER_ACTION_FAIL;
			$this->result_message = "Error parsing file $file";
			$this->loop = 2;
			return $this->result;
		}*/
		
		$db_con = mysql_connect($this->server.':'.$this->port, $this->username, $this->password);
		if(!$db_con){
			$this->result_message = "Could not connect to mysql server ".$this->server.':'.$this->port." with username $this->username: ".mysql_error($db_con);
			$this->result = INSTALLER_ACTION_FAIL;
			return $this->result;
		}else{
			if(mysql_select_db($this->db_name, $db_con) == FALSE){
				if(mysql_query("CREATE DATABASE $this->db_name", $db_con) == FALSE){
					$this->result_message = "Could not create database: $this->db_name";
					$this->result = INSTALLER_ACTION_FAIL;
					return $this->result;
				}
				if(mysql_select_db($this->db_name, $db_con) == FALSE){
					$this->result_message = "Could not select database: $this->db_name";
					$this->result = INSTALLER_ACTION_FAIL;
					return $this->result;
				}
			}
			$query_count = 1;
			/*foreach($sql_commands as $sql_command_set){
			if (!is_null($sql_command_set)) {
				foreach($sql_command_set as $sql_statement){
					if(mysql_query($sql_statement['query'], $db_con) === FALSE){
						$this->result_message = "Error running SQL query: <BR>\n".$sql_statement['query']."<BR>\n".mysql_error($db_con);
						$this->result = INSTALLER_ACTION_FAIL;
						$this->loop = 2;
						return $this->result;
					}
					$query_count++;
				}
			}
			}*/
			$this->result = INSTALLER_ACTION_WARNING;
			$this->result_message = "Loaded database file with $query_count queries";
			$comm = "(".$this->mysql_path."/mysql -u" . $this->username .
                         " --password='" . $this->password .
                         "' " . $this->db_name . " < " .
                        "$file) 3>&1 1>&2 2>&3";
                        exec($comm,$output,$return);
                        if ($return > 0) {
                        $this->result_message = "Error running SQL query: <BR>\n".$comm. " " . print_r($output,true) . " Condition: $return <BR />\n";
                        $this->result = INSTALLER_ACTION_FAIL;
                        $this->loop = 2;
                        return $this->result;
                        }
                        $this->query_count = 1;
			$k = array_search($this->file,$this->file_list);
			if (isset($this->file_list[$k]))
				unset($this->file_list[$k]);
			if(count($this->file_list) == 0){
				$this->result = INSTALLER_ACTION_SUCCESS;
			}
			$this->file = "";
		}
		$this->loop = 1;	
		return $this->result;
	}
		
	function prepareParameters(){
		if ($this->params_prepared) {
			return true;
		}
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
		
		if(isset($this->params['db_field'])){
			$database_field = $engine->getField($this->params['db_field']);
			$this->db_name = $database_field->value;
		}elseif(isset($this->params['db_name'])){
			$this->db_name = $this->params['db_name'];
		}else{
			$this->result_message = "Could not determine database name, please provide a db_field or db_name parameter!";
			return FALSE;
		}
		if(isset($this->params['mysql_path'])){
                        $this->mysql_path = $engine->getField($this->params['mysql_path'])->value;
                }else{
                        $this->result_message = "No path to mysql supplied, please provide a path parameter!";
                        return FALSE;
                }

		// Get file list
		if(!isset($this->params['files']) || !is_array($this->params['files']) || count($this->params['files']) == 0){
			$this->result_message = "You must provide a files parameter that is an array of the files to load";
			return FALSE;
		}else{
			$this->file_list = array();
			foreach($this->params['files'] as $file){
				if(is_dir($file)) {
					$d = dir($file);
					while (false !== ($entry = $d->read())) {
						if (preg_match("/^.*\.sql$/",$entry)) {
							$pretty_name = ucwords(str_replace("_"," ",preg_replace("/^(.*)\.sql/","\$1",$entry)));
							$this->file_list[$pretty_name] = $d->path . "/" . $entry;
						}
					}
					$d->close();
				}else if (file_exists($file)) {
					$pretty_name = ucwords(str_replace("_"," ",preg_replace("/^.*\/(.*)\.sql$/","\$1",$file)));
					$this->file_list[$pretty_name] = $file;
				}
			}
		}	
		$this->params_prepared = true;
	}

	function getHTML($smarty){
		if($this->prepareParameters() === FALSE){
			$this->result = INSTALLER_ACTION_FAIL;
			return $this->result;
		}
		$smarty->assign("files",$this->file_list);	
		$smarty->assign("loop",$this->loop);
		$smarty->assign_by_ref('ACTION', $this);
		if ($this->loop == 3) {
			$es =& $GLOBALS['INSTALLER']['SMARTY'];
			$es->assign('HEADER_EXTRAS','<META HTTP-EQUIV=Refresh CONTENT="2; URL=index.php?save_action=true">');
		}
		return $smarty->fetch(Installer::getTemplatePath('action_sql_options.tpl'));
	}

	function dataSubmitted(){
		if (isset($_POST['install_sql_done'])) {
			$this->loop = 5;
			return $this->result;
		}else if (isset($_POST['install_sql'])) {
			if (isset($_POST['optfile'])) {
				if (array_search($_POST['optfile'], $this->file_list)) {
					$this->file = $_POST['optfile'];
					$this->result = INSTALLER_ACTION_FAIL;
					$this->result_message = "Installing File.";
					return $this->result;

				}
			}
		}
		return false;
	}
}
?>
