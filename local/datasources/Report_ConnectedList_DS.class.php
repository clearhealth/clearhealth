<?php

class Report_ConnectedList_DS extends Datasource_sql
{
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

