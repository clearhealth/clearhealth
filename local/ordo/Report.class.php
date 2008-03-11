<?php

$loader->requireOnce('ordo/ORDataObject.class.php');
$loader->requireOnce('includes/Table.class.php');
$loader->requireOnce('includes/ReportFilter.class.php');



/**
*	This class is a data model object for representation of report information.
*/

class Report extends ORDataObject {

	var $id = 0;
	var $custom_id = '';
	var $label = '';
	var $description = '';
	var $query = '';
	var $templates = false;
	var $newTemplates = false;
	var $deletedTemplates = false;

	var $storage_metadata =  array('int' => array('show_sequence'=>0,'snapshot_style'=>0), 'date' => array(), 'string' => array('system_report'=>''), 'text' => array());
	var $_table = "reports";
	var $_internalName='Report';

	/**
	*	Constructor expects a reference to and adodb compliant db object.
	* 	When using frame this is in $GLOBALS['frame']['adodb']['db']
	*	It takes the reference and passes it to the parent class which set
	*	the private _db variable to be used when executing queries such as 
	*	$this->_db->Execute()
	*/
	function Report($report_id=0,$db = null) {
		parent::ORDataObject($db);	
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

	/**
	 * Returns an array of all of the methods that are accessible via the AJAX object
	 *
	 * @return array
	 */
	function ajaxMethods() {
		return array(
			'getTemplateList'
		);
	}
	
	function setup($report_id  = 0) {
		if ($report_id > 0) {
			$this->set('id',$report_id);
			$this->populate();
		}
	}
	function get_report_id() {
		return $this->id;
	}
	function set_report_id($report_id) {
		$this->id = (int)$report_id;
	}
	function setupBySystemName($name) {
		$name = $this->dbHelper->quote($name);
		$sql = "select foreign_key from storage_string where value_key = 'system_report' and value = $name";

		$res = $this->dbHelper->execute($sql);

		if (!$res->EOF) {
			$this->setup($res->fields['foreign_key']);
		}
	}

	function getReportDs() {
		$ds =& new Datasource_sql();

		$labels = array('label' => 'Name', 'type'=>'Type','description' => 'description', 'view' => false);

		$ds->setup($this->_db,array(
				'cols'	=> "label label, description, id, if(ss.value is null or ss.value = '','Default','System') type",
				'from' 	=> "$this->_table r left join storage_string ss on r.id = ss.foreign_key and value_key = 'system_report' ",
				'orderby' => "label",
				'where' => "label != ''"
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
			foreach($this->newTemplates as $key => $new) {
				$new['is_default'] = "no";
				$new['report_id'] = $this->id;
				
				$newId = $this->_db->GenID('sequences');
				$this->templates[$newId] = $new;
				
				$this->newTemplates[$key]['id'] = $newId;
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
         * Getter for the full form path
         */
        function get_file_path($templateId = '') {
		$curTemplateId = $this->get('id');
		if ((int)$templateId > 0) $curTemplateId = (int)$templateId;
                $forms_dir = realpath(Celini::config_get('user_reports_dir'));
                $filename = $forms_dir."/".$curTemplateId.".tpl.";
                if (file_exists($filename."pdf")) {
                        return  $filename."pdf";
                }
                return $filename."html";
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
		$ret = array();
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
	
	function getDefaultTemplateId() {
		$tl = $this->getTemplateList();
		$tlka = array_keys($tl);
		return $tlka[0];
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
	 * Deprecated method: use valueList('templates') instead
	 *
	 * @see valueList_templates()
	 * @return array
	 * @deprecated
	 */
	function get_templates() {
		return $this->valueList('templates');
	}
	
	/**
	* Lazy load templates
	*/
	function valueList_templates() {
		if ($this->get('id') === 0) {
			return array();
		}
		if ($this->templates === false) {
			$qId = $this->dbHelper->quote($this->get('id'));
			$res = $this->dbHelper->execute("select * from report_templates where report_id = {$qId} order by report_template_id");
			$this->templates = array();
			while($res && !$res->EOF) {
				$res->fields['pdf'] = false;
				if (substr($this->get_file_path($res->fields['report_template_id']),-3) === "pdf") {
					$res->fields['pdf'] = true;
				}
				$this->templates[$res->fields['report_template_id']] = $res->fields;
				$res->moveNext();
			}
			if (count($this->templates) == 0) {
				// create a default template
				$qId = $this->dbHelper->quote($this->get('id'));
				$new_id = $this->dbHelper->nextId("sequences");
				$this->dbHelper->execute("insert into report_templates values ($new_id,{$qId},'Default Template','Yes',10000,'')");
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

	function get_custom_id() {
		return $this->custom_id;
	}
	function set_custom_id($custom_id) {
		return $this->custom_id = $custom_id;
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

	function get_query() {
		if (empty($this->query)) {
			return "select 'No Query Found' error";
		}
		return $this->query;
	}

	function set_query($query)
	{
		$this->query = $query;
	}
	function get_exploded_query() {
		$ret = array();
		$this->query = preg_replace('/\s/'," ",$this->get('query'));
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

	function nextSequence($templateId) {

		$sql = "update report_templates set sequence = sequence + 1 where report_template_id = ".$this->dbHelper->quote($templateId);
		$this->dbHelper->execute($sql);
		$sql = "select sequence from report_templates where report_template_id = ".$this->dbHelper->quote($templateId);
		$res = $this->dbHelper->execute($sql);
		return $res->fields['sequence'];
	}
} 
?>
