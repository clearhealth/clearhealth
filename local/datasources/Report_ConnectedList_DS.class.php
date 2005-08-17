<?php

class Report_ConnectedList_DS extends Datasource_sql
{
	/**
	 * Stores the case-sensative class name for this ds and should be considered
	 * read-only.
	 *
	 * This is being used so that the internal name matches the filesystem
	 * name.  Once BC for PHP 4 is no longer required, this can be dropped in
	 * favor of using get_class($ds) where ever this property is referenced.
	 *
	 * @var string
	 */
	var $_internalName = 'Report_ConnectedList_DS';
	
	
	function Report_ConnectedList_DS($menu_id) {
		settype($menu_id,'int');

		$this->setup(Cellini::dbInstance(),
			array(
				'cols' 	=> "title, report_id, description, rt.report_template_id",
				'from' 	=> "reports r inner join report_templates rt on r.id = rt.report_id 
				inner join menu_report mr using(report_template_id)",
				'where' => " mr.menu_id = $menu_id"
			),
			array('title' => 'Title','description' => 'Description')
		);
	}
}

