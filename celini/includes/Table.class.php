<?php
/**
* A generic table renderer
*
* @author	Joshua Eichorn	<jeichorn@mail.com>
*/
class Table
{
	var $result;
	var $pager;
	var $template;
	var $row;

	var $assign = array();

	/**
	* Set the result set and the template to use
	*/
	function Table($result,$template)
	{
		$this->result = $result;
		$this->template = "default";
		if ($template !== "default" && file_exists($template)) {
			$this->template = realpath($template);
		}
		else {
			$this->template = Celini::getTemplatePath("table.html"); 
		}
	}

	/**
	* Return html for the Table
	*/
	function render()
	{
		// figure out column labels from array keys
		

		$c = new Controller();
		$c->template_dir = dirname(realpath($this->template));

		$table = array();
		$i = 0;
		if ($this->result) {
			while(!$this->result->EOF) {
				$table[$i] = $this->result->fields;
				if (isset($table[$i]['index'])) {
					$table[$i]['index'] = $i;
				}

				$i++;
				$this->result->moveNext();
			}
		}

		$c->assign("table",$table);
		$c->assign('get',$_GET);
		$c->assign('tools',$this);
		if (isset($_GET['report'])) {
			$c->assign('action',Celini::link('reports')."report=$_GET[report]");
		}

		$c->assign('today',date('Y-m-d'));

		foreach($this->assign as $key => $val) {
			$c->assign($key,$val);
		}

		if (isset($table[0])) {
			$labels = array_keys($table[0]);
			if ($this->pager) {
				$this->pager->span = count($labels);
			}
			$labels = array_map(array($this,'formatLabel'),$labels);
			$c->assign("columnLabels","<tr><th>".implode("</th><th>",$labels)."</th></tr>");
		}

		if (is_object($this->pager))
		{
			$c->assign("pager",$this->pager->render());
		}

		return $c->fetch($this->template);
	}

	/**
	* Format a column label
	*/
	function formatLabel($label)
	{
		return ucfirst($label);
	}

	var $_lookup;
	function lookup($type,$key,$alternate = false) {
		if (!isset($this->_lookup[$type])) {
			if (!class_exists($type)) {
				include_once APP_ROOT."/local/ordo/$type.class.php";
			}
			$this->_lookup[$type] = new $type();
		}

		return $this->_lookup[$type]->lookup($key,$alternate);
	}

	/**
	* Y/N
	*/
	function yN($val) {
		if ($val) {
			return "Y";
		}
		return "N";
	}

	/**
	* X
	*/
	function X($val) {
		if ($val) {
			return "X";
		}
		return "&nbsp;";
	}
}
?>
