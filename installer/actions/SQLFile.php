<?php
/*
 * SQLFile Class
 *
 * This is an abstract class that needs to have at
 * least the perform method overridden
 */
class SQLFile extends BaseAction {
	var $server;
	
	var $username;
	
	var $password;
	
	var $port;
	
	var $db_name;
	
	var $mysql_path;
	
	var $file_list;
	
	var $loop;
	
	var $query_count = 0;
	
	function SQLFile($params){
		parent::BaseAction($params);
		
		$this->interactive = true;
		$this->grouping = false;
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
		}

		if($this->prepareParameters() === FALSE){
			$this->result = INSTALLER_ACTION_FAIL;
			return $this->result;
		}
		$sql_commands = array();
		foreach($this->file_list as $file){
			if(!is_readable($file)){
				$this->result = INSTALLER_ACTION_FAIL;
				$this->result_message = "Could not read file sql $file.";
				$this->loop = 2;
				return $this->result;
			}
			
			$file_contents = file($file);
			$file_contents = join('', $file_contents);
			if(!$this->splitMySqlFile($sql_commands[], $file_contents)){
				$this->result = INSTALLER_ACTION_FAIL;
				$this->result_message = "Error parsing file $file";
				$this->loop = 2;
				return $this->result;
			}
		}

		foreach($this->cache_file_list as $file){
			if(!is_readable($file)){
				$this->result = INSTALLER_ACTION_FAIL;
				$this->result_message = "Could not read file sql cache $file.";
				$this->loop = 2;
				return $this->result;
			}
			
			$file_contents = file_get_contents($file);
			//$sql_commands[] = unserialize($file_contents);
		}
	
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
			$comm = $this->mysql_path."/mysql -u" . $this->username .
		         " -f --password='" . $this->password .
		         "' " . $this->db_name . " < " .
			"$file"; 
			
			//if true NOT a *nix system, i.e. windows, mysql must be in system path
                        if(strpos(strtolower($_SERVER['SERVER_SOFTWARE']),'unix') === false && strpos(strtolower($_SERVER['SERVER_SOFTWARE']),'linux') === false) {
                        // windows has different command structure needs
                        $comm = "mysql -u" . $this->username .
                         " -f -p" . $this->password .
                         " " . $this->db_name . " < " .
                        "$file";
                        }

			exec($comm,$output,$return);
			if ($return > 0) {
			$this->result_message = "Error running SQL query: <BR>\n".$comm." Condition: $return <BR />\n";
			$this->result = INSTALLER_ACTION_FAIL;
			$this->loop = 2;
			return $this->result;
			}
			$this->query_count = 1;
			/*
			foreach($sql_commands as $sql_command_set){
			if (!is_null($sql_command_set)) {
				foreach($sql_command_set as $sql_statement){
					if(empty($sql_statement['query'])) continue;
					
					if(mysql_query($sql_statement['query'], $db_con) === FALSE){
						$this->result_message = "Error running SQL query: <BR>\n".$sql_statement['query']."<BR>\n".mysql_error($db_con);
						$this->result = INSTALLER_ACTION_FAIL;
						$this->loop = 2;
						return $this->result;
					}
					$this->query_count++;
				}
			}
			}*/
			$this->result = INSTALLER_ACTION_SUCCESS;
			$this->result_message = "Loaded ".(count($this->file_list)+count($this->cache_file_list))." sql files with {$this->query_count} queries";
			$this->loop = 3;
		}
		
		return $this->result;
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

		// Get cache file list
		$this->cache_file_list = array();
		if(isset($this->params['cache_files']) && (!is_array($this->params['cache_files']) || count($this->params['cache_files']) == 0)){
			$this->result_message = "When using the cache_files parameter it must contain an array of the db cache files to load";
			return FALSE;
		}elseif(isset($this->params['cache_files'])){
			$this->cache_file_list = $this->params['cache_files'];			
		}		

		// Get file list
		$this->file_list = array();
		if(isset($this->params['files']) && (!is_array($this->params['files']) || count($this->params['files']) == 0)){
			$this->result_message = "When using the files parameter it must contain an array of the files to load";
			return FALSE;
		}elseif(isset($this->params['files'])){
			$this->file_list = $this->params['files'];			
		}
		
		if(count($this->cache_file_list) == 0 && count($this->file_list) == 0){
			$this->result_message = "You must specify either the files or cache_files parameter";
			return FALSE;		
		}		
	}

	/**
	* Function from phpMyAdmin (http://phpwizard.net/projects/phpMyAdmin/)
	*
 	* Removes comment and splits large sql files into individual queries
 	*
	* Last revision: September 23, 2001 - gandon
 	*
 	* @param   array    the splitted sql commands
 	* @param   string   the sql commands
 	* @return  boolean  always true
 	* @access  public
 	*/
	function splitMySqlFile(&$ret, $sql){
	    // do not trim, see bug #1030644
	    //$sql          = trim($sql);
	    $sql          = rtrim($sql, "\n\r");
	    $sql_len      = strlen($sql);
	    $char         = '';
	    $string_start = '';
	    $in_string    = FALSE;
	    $nothing      = TRUE;
	    $time0        = time();
	
	    for ($i = 0; $i < $sql_len; ++$i) {
	        $char = $sql[$i];
	
		//echo "parsing character $i<br>";
	
	        // We are in a string, check for not escaped end of strings except for
	        // backquotes that can't be escaped
	        if ($in_string) {
	            for (;;) {
	                $i         = strpos($sql, $string_start, $i);
	                // No end of string found -> add the current substring to the
	                // returned array
	                if (!$i) {
	        	//	echo "<br> instring <br>"; echo $sql;
		           $ret[] = array('query' => $sql, 'empty' => $nothing);
	                    return TRUE;
	                }
	                // Backquotes or no backslashes before quotes: it's indeed the
	                // end of the string -> exit the loop
	                else if ($string_start == '`' || $sql[$i-1] != '\\') {
	                    $string_start      = '';
	                    $in_string         = FALSE;
	                    break;
	                }
	                // one or more Backslashes before the presumed end of string...
	                else {
	                    // ... first checks for escaped backslashes
	                    $j                     = 2;
	                    $escaped_backslash     = FALSE;
	                    while ($i-$j > 0 && $sql[$i-$j] == '\\') {
	                        $escaped_backslash = !$escaped_backslash;
	                        $j++;
	                    }
	                    // ... if escaped backslashes: it's really the end of the
	                    // string -> exit the loop
	                    if ($escaped_backslash) {
	                        $string_start  = '';
	                        $in_string     = FALSE;
	                        break;
	                    }
	                    // ... else loop
	                    else {
	                        $i++;
	                    }
	                } // end if...elseif...else
	            } // end for
	        } // end if (in string)
	
	        // lets skip comments (/*, -- and #)
	        else if (($char == '-' && $sql_len > $i + 2 && $sql[$i + 1] == '-' && $sql[$i + 2] <= ' ') 
			|| $char == '#' 
			|| ($char == '/' && $sql_len > $i + 1 && $sql[$i + 1] == '*')) {
	            $i = strpos($sql, $char == '/' ? '*/' : "\n", $i);
	            // didn't we hit end of string?
	            if ($i === FALSE) {
	                break;
	            }
	            if ($char == '/') $i++;
	        }
	
	        // We are not in a string, first check for delimiter...
	        else if ($char == ';') {
	            // if delimiter found, add the parsed part to the returned array
			$parsedsql = substr($sql, 0, $i);
			//	echo "<br> midone <br>";echo $parsedsql;
	            $ret[]      = array('query' => $parsedsql, 'empty' => $nothing);
	            $nothing    = TRUE;
	            $sql        = ltrim(substr($sql, min($i + 1, $sql_len)));
	            $sql_len    = strlen($sql);
	            if ($sql_len) {
	                $i      = -1;
	            } else {
	                /// The submited statement(s) end(s) here
	                return TRUE;
	            }
	        } // end else if (is delimiter)
	
	        // ... then check for start of a string,...
	        else if (($char == '"') || ($char == '\'') || ($char == '`')) {
	            $in_string    = TRUE;
	            $nothing      = FALSE;
	            $string_start = $char;
	        } // end else if (is start of string)
	
	        elseif ($nothing) {
	            $nothing = FALSE;
	        }
	
	        //loic1: send a fake header each 30 sec. to bypass browser timeout
	        $time1     = time();
	        if ($time1 >= $time0 + 30) {
	            $time0 = $time1;
	            header('X-pmaPing: Pong');
	        } // end if
	    } // end for
	
	    // add any rest to the returned array
	    if (!empty($sql) && preg_match('@[^[:space:]]+@', $sql)) {
		//	echo "<br> bottomone <br>";echo $sql;
	        $ret[] = array('query' => $sql, 'empty' => $nothing);
	    }
	
	    return TRUE;
	}

	function getHTML($smarty){
		$smarty->assign("loop",$this->loop);
		$smarty->assign_by_ref('ACTION', $this);
		if ($this->loop < 2) {
			$es =& $GLOBALS['INSTALLER']['SMARTY'];
			$es->assign('HEADER_EXTRAS','<META HTTP-EQUIV=Refresh CONTENT="2; URL=index.php?save_action=true">');
		}
		return $smarty->fetch(Installer::getTemplatePath('action_sql_file.tpl'));
	}

	function dataSubmitted(){
		return false;
	}


	
}
?>
