#!/usr/bin/php
<?php

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
	            //header('X-pmaPing: Pong');
	        } // end if
	    } // end for
	
	    // add any rest to the returned array
	    if (!empty($sql) && preg_match('@[^[:space:]]+@', $sql)) {
		//	echo "<br> bottomone <br>";echo $sql;
	        $ret[] = array('query' => $sql, 'empty' => $nothing);
	    }
	
	    return TRUE;
	}

$first = true;
foreach($argv as $file){
	if($first){
		$first = false;
		continue;
	}

	$sql_commands = array();
	if(!is_readable($file)){
		die("Could not read file sql $file.");
	}
	
	$file_contents = file($file);
	$file_contents = join('', $file_contents);
	if(!splitMySqlFile($sql_commands, $file_contents)){
		die("Error parsing file $file");
	}
	
	$output = fopen($file.'.cache', 'a+');
	fwrite($output, serialize($sql_commands));
	fclose($output);
	print("Created cache file $file.cache\n\n");
}

?>
