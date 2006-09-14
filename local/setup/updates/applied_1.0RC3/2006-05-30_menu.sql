INSERT INTO `menu` ( `menu_id` , `site_section` , `parent` , `dynamic_key` , `section` , `display_order` , `title` , `action` , `prefix` )
VALUES (
'', 'admin', '110', '', 'children', '700', 'Appointment Rules', 'AppointmentRuleset/list', 'main'
);
INSERT INTO `menu` ( `menu_id` , `site_section` , `parent` , `dynamic_key` , `section` , `display_order` , `title` , `action` , `prefix` )
VALUES (
'', 'admin', '110', '', 'children', '0', 'Add Appointment Rules', 'AppointmentRuleset/add', 'main'
), (
'', 'admin', '110', '', 'children', '0', 'Edit Appointment Rules', 'AppointmentRuleset/edit', 'main'
);

