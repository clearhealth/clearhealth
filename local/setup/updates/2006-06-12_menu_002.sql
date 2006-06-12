INSERT INTO `menu` 
	( `menu_id` , `site_section` , `parent` , `dynamic_key` , `section` , `display_order` , `title` , `action` , `prefix` )
VALUES 
	(NULL , 'admin', '30', '', 'children', '100', 'Appointment Rules', 'AppointmentRuleset/list', 'main'),
	(NULL , 'default', '8', '', 'children', '300', 'Appointment Rules', 'AppointmentRuleset/list', 'main');
