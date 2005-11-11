<?php

require_once CELINI_ROOT."/ordo/ORDataObject.class.php";
require_once CELINI_ROOT."/includes/Table.class.php";
require_once CELINI_ROOT."/includes/ReportFilter.class.php";

/**
* Compat functions
*/
require_once CELINI_ROOT.'/lib/PHP_Compat/Compat/Function/file_put_contents.php';


/**
*	This class is a data model object for representation of report information.
*/

class Report extends ORDataObject {

	var $id = 0;
	var $label = '';
	var $description = '';
	var $query = '';
	var $templates = false;
	var $newTemplates = false;
	var $deletedTemplates = false;

	/**
	*	Constructor expects a reference to and adodb compliant db object.
	* 	When using frame this is in $GLOBALS['frame']['adodb']['db']
	*	It takes the reference and passes it to the parent class which set
	*	the private _db variable to be used when executing queries such as 
	*	$this->_db->Execute()
	*/
	function Report($report_id=0,$db = null) {
		parent::ORDataObject($db);	
		$this->_table = "reports";
		$this->_sequence_name = "sequences";	
    	
		if (!empty($report_id))
		{
			$this->id = $report_id;
			if (is_numeric($this->id) && $this->id != 0)
			{
				$this->populate();
			}
		}
	}

	function setup($report_id  = 0) {
		if ($report_id > 0) {
			$this->set('id',$report_id);
			$this->populate();
		}
	}

	function getReportDs() {
		$ds =& new Datasource_sql();

		$labels = array('label' => 'Name', 'description' => 'description', 'view' => false);

		$ds->setup($this->_db,array(
				'cols'	=> 'label, description, id',
				'from' 	=> "$this->_table ",
				'orderby' => "label",
			),
			$labels
		);

		$ds->registerFilter('view',array($this,'templateViewFilter'));
		$ds->registerTemplate('label','<a href="'.Celini::link('edit').'report_id={$id}">{$label}</a>');
		return $ds;
	}

	var $_templates = false;
	function templateViewFilter($colVal,$row) {
		if ($this->_templates == false) {
			$res = $this->_execute("select * from report_templates");
			while($res && !$res->EOF) {
				$this->_templates[$res->fields['report_id']][$res->fields['report_template_id']] = $res->fields;
				$res->MoveNext();
			}
		}
		$ret = "<select onchange=\"window.location = '".Celini::link('report')."' + this.options[this.selectedIndex].value\"><option>View Report with Template</option>";
		if (isset($this->_templates[$row['id']])) {
			foreach($this->_templates[$row['id']] as $template) {
				$ret .= "<option value='report_id=$row[id]&report_template_id=$template[report_template_id]'>$template[name]</option>";
			}
		}
		$ret .= "</select>";
		return $ret;
	}
    
	/**
	* Load all the reports n the system
	*/
	function report_factory($limit="") {
    	
		$reports = array();
		$sql = "SELECT * from " . $this->_table.$limit;

		$res = $this->_Execute($sql);
    	
		$i = 0;
		$last_id = false;
		while ($res && !$res->EOF) {
			$reports[$i] = new Report(null);
			$reports[$i]->populate_array($res->fields);
			$res->MoveNext(); 
			$i++;
		}	
		return $reports;
	}

	/**
	* Get a report objct from a template_id
	*/
	function fromTemplateId($template_id) {
		settype($template_id,'int');
		$id = $this->_db->getOne("select report_id from report_templates where report_template_id = $template_id");

		$ret = new Report($id);
		return $ret;
	}

	/**
	* Store report to the db
	*
	* @todo: do a permission check here
	*/
	function persist() {
		if ($this->newTemplates !== false) {
			foreach($this->newTemplates as $new) {
				$new['is_default'] = "no";
				$new['report_id'] = $this->id;
				$this->templates[$this->_db->GenID("sequences")] = $new;
			}
		}

		if ($this->templates !== false) {
			foreach($this->templates as $id => $template) {
				$sql = "REPLACE INTO " . $this->_prefix . "report_templates SET ";
				foreach($template as $field => $val) {
					$sql .= " `" . $field . "` = '" . mysql_real_escape_string(strval($val)) ."',";
				}
				$sql .= " `report_template_id` = ".(int)$id;
				//var_dump($sql);
				$this->_db->execute($sql);
			}
		}

		if ($this->deletedTemplates !== false) {
			foreach($this->deletedTemplates as $deleted) {
				$sql = "delete from report_templates where report_template_id = ".(int)$deleted;
				$this->_db->execute($sql);
			}
		}
		parent::persist();
		$this->template = false;
	}


	/**
	* Generate a default Template
	*/
	function generateDefaultTemplate() {
		// find the default template for this report
		$templates = $this->get_templates();
		foreach($templates as $t) {
			if ($t['is_default'] === "yes") {
				$id = $t['report_template_id'];
				break;
			}
		}
		$file = APP_ROOT."/user/report_templates/$id.tpl.html";
		if (!file_exists($file)) {
			$template = file_get_contents(APP_ROOT."/user/report_templates/default.html");

			list($labels,$cols) = $this->getReportLabels();

			$template = str_replace(array("[labels]","[cols]"),array($labels,$cols),$template);

			file_put_contents($file,$template);
		}
		return $id;
	}

	/**
	* Get a id => name list of report
	*/
	function getReportList() {
		$res = $this->_execute("select id, label from $this->_prefix$this->_table order by label");
		$ret = array(" ");
		while(!$res->EOF) {
			$ret[$res->fields['id']] = $res->fields['label'];
			$res->moveNext();
		}
		return $ret;
	}

	/**
	* Get n id => name list for report_templates
	*/
	function getTemplateList($id = false,$jsformat=false) {
		if ($id) {
			$this->set_id($id);
		}
		$templates = $this->get_templates();
		$ret = array();
		foreach($templates as $template) {
			if ($jsformat) {
				$ret[] = $template['report_template_id']."|".$template['name'];
			}
			else {
				$ret[$template['report_template_id']] = $template['name'];
			}
		}
		return $ret;
	}

	/**
	* Run the query query to get report labels from the query
	*/
	function getReportLabels() {
		$res = $this->_execute(ReportFilter::stripWhere($this->get('query')));

		$labels = array();
		$cols = array();
		if (is_array($res->fields)) {

			$labels = array_keys($res->fields);
			foreach($labels as $col) {
				$cols[] = "{\$row.$col}";
			}
		}

		$labels = array_map(array('Table','formatLabel'),$labels);
		return array(
			"<tr><th>".implode("</th><th>",$labels)."</th></tr>",
			"<tr><td>".implode("</th><td>",$cols)."</td></tr>"
		);
	}
	
	    
    
	/**#@+
	*	Getter/Setter method used as part of object model for populate, persist, and form_poulate operations
	*/

	/**
	* Lazy load templates
	*/
	function get_templates() {
		if ($this->id === 0) {
			return array();
		}
		if ($this->templates === false) {
			$res = $this->_db->execute("select * from report_templates where report_id = $this->id order by report_template_id");
			$this->templates = array();
			while($res && !$res->EOF) {
				$this->templates[$res->fields['report_template_id']] = $res->fields;
				$res->moveNext();
			}
			if (count($this->templates) == 0) {
				// create a default template
				$new_id = $this->_db->GenID("sequences");
				$this->_db->execute("insert into report_templates values ($new_id,$this->id,'Default Template','Yes')");
				$this->templates = false;
				$this->get_templates();
			}
		}
		return $this->templates;
	}

	function get_id()
	{
		return $this->id;
	}
	function set_id($id)
	{
		return $this->id = $id;
	}

	function get_label()
	{
		return $this->label;
	}
	function set_label($label)
	{
		$this->label = $label;
	}

	function get_description()
	{
		return $this->description;
	}
	function set_description($desc)
	{
		$this->description = $desc;
	}

	function set_query($query)
	{
		$this->query = $query;
	}
	function get_exploded_query() {
		$ret = array();
		$this->query = preg_replace('/\s/'," ",$this->query);
		if (strstr($this->query,'---[')) {
			$this->query = str_replace(array(" ---[","]--- "),array("\n---[","]---\n"),$this->query);
			//var_dump($this->query);
			preg_match_all('/---\[([\w,]+)\]---\s+(.+)/',$this->query,$match);
			foreach($match[1] as $key => $val) {
				$ret[$val] = trim($match[2][$key]);
			}
		}
		return $ret;
	}


	function connectedReportList($menu_id) {
		settype($menu_id,'int');

		$ds =& new Datasource_sql();
		$ds->setup($this->_db,array(
				'cols' 	=> "title, report_id, description, rt.report_template_id",
				'from' 	=> "$this->_table r inner join report_templates rt on r.id = rt.report_id 
				inner join menu_report mr using(report_template_id)",
				'where' => " mr.menu_id = $menu_id"
			),
			array('title' => 'Title','description' => 'Description')
		);

		return $ds;
	}

} 
?>
