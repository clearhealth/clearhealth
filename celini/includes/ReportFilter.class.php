<?php
$loader->requireOnce("includes/Datasource_sql.class.php");
	
/**
* Automatically handles creating forms for filtering reports
*
* @author	Joshua Eichorn	<jeichorn@mail.com>
*/
class ReportFilter
{
	var $variables = array();
	var $variable_type = array();
	var $replace = array();
	var $values = false;
	var $base_query = "";
	var $controller = false;
	var $get_vars = array();
	var $action = false;
	var $dsFilters = array();
	var $extraData = array();
	var $controller_values = array();
	var $again = array();

	/**
	* Pass in a query to extract variables from
	* @todo: <<[ syntax is a big hack and this code is the wrong place, fix at some point
	*/
	function ReportFilter($query)
	{
		$this->controller = new Controller();
		if(strstr($query,"<<[")) {
			preg_match_all('/<<\[(.+)\]>>/U',$query,$match);
			$vars = array_flip(array_flip($match[1]));
			foreach($vars as $var){
				list($variable,$controller)=explode(':',$var);
				$query=str_replace("<<[$var]>>",(string)$this->controller->get($variable,$controller),$query);
			}
		}
		$this->extractVars($query);
		$this->base_query = $query;
		$data = $this->extractExtraDataSection($this->base_query);
		$this->extractDsFilters($data);
	}

	/**
	* Extract variable names from a query
	*/
	function extractVars($query)
	{
		preg_match_all('/\[(.+)\]/U',$query,$match);
		$vars = array_flip(array_flip($match[1]));
		foreach($vars as $key =>$var) {
			if (strstr($var,':')) {
				$tmp = preg_split('/([-:\'"\\\'\\"])/',$var,-1,PREG_SPLIT_DELIM_CAPTURE);
				$pieces = array();
				$inQuote = false;
				$current = "";
				foreach($tmp as $token) {
					switch($token) {
						case '-':
						case ':':
							if ($inQuote) {
								$current .= $token;
							}
							else {
								$pieces[] = $current;
								$current = "";
							}
							break;
						case '\'':
						case '"':
							$current .= $token;
							if ($inQuote) {
								$inQuote = false;
							}
							else {
								$inQuote = true;
							}
							break;
						default:
							$current .= $token;
							break;
					}
				}
				if (!empty($current)) {
					$pieces[] = $current;
				}
				$tmp = $pieces;
				$config =& Celini::configInstance();
				if($config->get('app_name') == 'clearhealth') {
				$tmp = $this->setPracticeLevel($tmp);
				}
				$vars[$key] = array_shift($tmp);
				if ($tmp[0] === "GET") {
					$this->get_vars[$key] = $vars[$key];
					unset($vars[$key]);
				}
				else if ($tmp[0] === "CONTROLLER") {
					$this->controller_values[$vars[$key]] = Controller::get($vars[$key],$tmp[1]);
				}
				else {
					$this->variable_type[$key] = $tmp;
				}

			}
			else {
				$this->variable_type[$key] = array('string');
			}
			$this->replace[$key] = $var;
		}
		
		$this->variables = $vars;
	}
	
	
	/**
	* set practice level permission for providers/rooms/buildings
	**/
	function setPracticeLevel ($filters_array) {
		$userProfile =& Celini::getCurrentUserProfile();
		$pid = $userProfile->getCurrentPracticeId();
		
		if ($filters_array[1]== "provider_practice_level") {
			$filters_array[2] = 'SELECT prov.person_id, CONCAT(per.first_name, " ", last_name) FROM provider AS prov LEFT JOIN person AS per ON prov.person_id=per.person_id WHERE per.primary_practice_id ='.$pid.'';
			$filters_array[1] = "query";
		}
		if ($filters_array[1]== "rooms_building_practice_level" || $filters_array[1]== "room_practice_level") {
			$filters_array[2] = "select r.id, concat(r.name,'->',b.name) name from rooms r inner join buildings b on r.building_id = b.id WHERE b.practice_id =".$pid."";
			$filters_array[1] = "query";
		}
		if ($filters_array[1]== "facility_practice_level" ) {
			$filters_array[2] = "SELECT id, name FROM buildings WHERE practice_id =".$pid." ORDER BY name ";
			$filters_array[1] = "query";
		}			
		return $filters_array;	
	}
	

	/**
	* Pull data in from post
	*/
	function update() {
		if (isset($_REQUEST['rf'])) {
			$this->values = $_REQUEST['rf'];
			foreach ($this->values as $key => $value) {
				if (!is_array($value) &&  $d =& DateObject::create($value) !== false) {
					if ($d->toUSA() == $value || $d->toISO() == $value) {
						$this->values[$key] = $d->toISO();
					}
				}
			}
		}
	}

	/**
	 * Get the values to replace with
	 */
	function getValues() {
		foreach($this->replace as $key => $var) {
			if (!isset($this->variables[$key]) && isset($this->get_vars[$key])) {
				$varname = $this->get_vars[$key];
				if (isset($_GET[$varname])) {
					$this->values[$varname] = $_GET[$varname];
				}
			}
		}
		return $this->values;
	}

	/**
	* replace variable placeholders with values
	*/
	function replace($query) {
		$this->update();

		$replace = array();
		$values = array();
		foreach($this->replace as $key => $var) {
			$replace[] = "[$var]";
			if (!isset($this->variables[$key]) && isset($this->get_vars[$key])) {
				$varname = $this->get_vars[$key];
				if (isset($_GET[$varname])) {
					$this->values[$varname] = $_GET[$varname];
				}
			}
			else {
				$varname = $this->variables[$key];
			}
			if (isset($this->values[$varname])) {
				//handle array filters like multi-selects
				if (is_array($this->values[$varname])) {
					$im = "','";
					if (is_numeric($this->values[$varname][0])) {
					$values[] = implode(",",$this->values[$varname]);

}
else {
					$values[] = "'" . implode("','",$this->values[$varname]). "'";

}


				}
				else {
				$values[] = $this->values[$varname];
				}
			}
			else if (isset($this->controller_values[$varname])) {
				$values[] = $this->controller_values[$varname];
			}
			else {
				$values[] = "0";
			}
		}
		$ret = str_replace($replace,$values,$query);
		return $ret;
	}

	/**
	 * Get a datasource from the query that can be used with a grid
	 */
	function &getDatasource() {
		$ds =& new Datasource_sql();

		$this->values = $this->controller->get('report_filter_values');
		$this->update();
		$this->controller->set('report_filter_values',$this->values);

		if (preg_match('/(select.*?from[\s]+).*where(.*)/i',$this->base_query,$matches)) {
			$main_query = $matches[1].$matches[2];
		}
		else {
			$main_query = $this->base_query;
		}
		//echo $main_query; exit;
		if (stristr($main_query,'union')) {
			
			$sqls = explode('union',$this->base_query);
			foreach($sqls as $sql) {
				$q = $this->replace(trim($sql));
				$query['union'][] = $this->_explodeQuery($q,true);
				//var_dump($query);
			}
		}
		else {
			$sql = $this->replace($this->base_query);
			$query = $this->_explodeQuery($sql);
		}

		$ds->setup($GLOBALS['db'],$query,false);

		return $ds;
	}

	function _explodeQuery($sql,$debug = false) {
		$query = array( 'cols' => '', 'from' => '', 'where' => '', 'groupby' => '', 'orderby' => '');

		$read = array('cols'=>1);
		$i = 2;
		$rule = '/^select(.*?)';

		$check = $sql;
		$from = false;
		$tail = false;
		$cols = false;
		if (preg_match('/(select.*?)from[\s]+(.*)(where.*)/i',$sql,$matches)) {
			$check = $matches[1].$matches[3];
			$cols = $matches[1];
			$from = $matches[2];
			$tail = $matches[3];
		}
		else {

			if (stristr($check,'from')) {
				$rule .= 'from[\s]+(.*)';
				$read['from'] = $i++;
			}
			if (stristr($check,'where')) {
				$rule .= 'where(.*)';
				$read['where'] = $i++;
			}
			if (preg_match('/group[\s]+by/',$check)) {
				$rule .= "group[\s]+by(.*)";
				$read['groupby'] = $i++;
			}
			if (preg_match('/order[\s]+by/i',$check)) {
				$rule .= "order[\s]+by(.*)";
				$read['orderby'] = $i++;
			}
			if (stristr($check,'limit')) {
				$rule .= "limit(.*)";
				$read['limit'] = $i++;
			}
			$rule .= '$/i';
			preg_match($rule,$sql,$match);
			if ($debug) {
				//var_dump($rule,$sql,$match);
			}
			foreach($read as $key => $index) {
				$query[$key] = $match[$index];
			}
		}

		if ($from !== false) {
			$query['from'] = $from;
		}

		if ($cols !== false) {
			preg_match('/select(.+)/i',$cols,$match);
			$query['cols'] = $match[1];
		}
		if ($tail !== false) {
			$read = array();
			$i = 1;
			$rule = '/';
			if (stristr($tail,'where')) {
				$rule .= 'where(.*)';
				$read['where'] = $i++;
			}
			if (preg_match('/group[\s]+by/',$tail)) {
				$rule .= "group[\s]+by(.*)";
				$read['groupby'] = $i++;
			}
			if (preg_match('/order[\s]+by/i',$tail)) {
				$rule .= "order[\s]+by(.*)";
				$read['orderby'] = $i++;
			}
			if (stristr($tail,'limit')) {
				$rule .= "limit(.*)";
				$read['limit'] = $i++;
			}
			$rule .= '/i';
			preg_match($rule,$tail,$match);
			foreach($read as $key => $index) {
				$query[$key] = $match[$index];
			}
		}
		return $query;
	}

	/**
	* Strip off the where clause to garentee we get rows
	* Important! only use sql 92 style joins with this (inner join table etc)
	*
	* @todo	do we need to cover comments etc?
	*/
	function stripWhere($query) {
		if (preg_match('/(.+)where/i',$query,$match)) {
			return $match[1];
		}
		return $query;
	}

	function setAction($action) {
		$this->action = $action;
	}

	function setMinimumAction() {
		if (isset($_GET['report_id']) && isset($_GET['template_id]'])) {
			$this->setAction(Celini::link(true,true,true)."report_id=$_GET[report_id]&template_id=$_GET[template_id]");
		}
	}

	function extractDsFilters($dataSection) {
		$rows = explode("  ",trim($dataSection));
		foreach($rows as $row) {
			if (preg_match('/([a-zA-Z_]+)-(.+)/',$row,$match)) {
				switch($match[1]) {
					case "dsFilters":
						$mods = explode(',',$match[2]);
						foreach($mods as $mod) {
							$tmp = explode('|',$mod);
							$col = array_shift($tmp);
							$this->dsFilters[$col] = $tmp;
						}
						break;
						break;
				}
			}
			if (preg_match('/(.+)=(.+)/',$row,$match)) {
				$this->extraData[$match[1]] = $match[2];
			}
		}

	}

	function extractExtraDataSection($query) {
		preg_match('/\/\*\*\*(.+)\*\*\*\//',$query,$match);
		if (isset($match[1])) {
			return $match[1];
		}
	}

	/**
	* Return html for the filter
	*/
	function render() {
		if (count($this->variables) == 0) {
			return "";
		}

		$vars = array();
/* debugs
echo "<pre>".print_r($this->variables,true)."</pre>";
echo "<pre>".print_r($this->controller_values,true)."</pre>";
echo "<pre>".print_r($this->variable_type,true)."</pre>";
*/
		foreach($this->variables as $key => $var)
		{
                       if ($this->variable_type[$key][0] == 'datetime') {
				if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}\ [0-9]{2}:[0-9]{2}/',$this->values[$var])) {
					$varValue = $this->values[$var];
	                    // quick hack to remove double slash on blank date
					if ($varValue == '//') {
						$varValue = '';
					}
				}
				// Not a real date
				else {
					$varValue = $this->values[$var];
				}
			}
			elseif ($this->variable_type[$key][0] == 'date' ) {
				if ($d =& DateObject::create($this->values[$var]) !== false) {
					$varValue = $d->toUSA();
					// quick hack to remove double slash on a blank date
					if ($varValue == '//') {
						$varValue = '';
					}
				}
				// Not a real date
				else {
					$varValue = $this->values[$var];
				}
			} elseif(isset($this->controller_values[$var])){
				continue;
			} else {
				$varValue = $this->values[$var];
			}
			$vars[$var] = array('name'=>$var,'value'=>$varValue,'type'=>$this->variable_type[$key]);
		}


		
		$this->controller->assign("vars",$vars);
		$em =& EnumManager::getInstance();
		$this->controller->assign('em',$em);

		if (!$this->action) {
			$this->action = $_SERVER["REQUEST_URI"];
		}
		$this->controller->assign("TOP_ACTION",$this->action);

		if (isset($_GET['gridMode']) && $_GET['gridMode'] === 'pdf') {
			$this->controller->assign('READONLY',true);
		}
		return $this->controller->fetch(Celini::getTemplatePath("/report/reportfilter.html")); 
	}
}
?>
