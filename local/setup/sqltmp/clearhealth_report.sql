CREATE TABLE `report_templates` (
  `report_template_id` int(11) NOT NULL default '0',
  `report_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `is_default` enum('yes','no') NOT NULL default 'yes',
  PRIMARY KEY  (`report_template_id`),
  KEY `report_id` (`report_id`)
) TYPE=MyISAM COMMENT='Report templates';
INSERT INTO `report_templates` VALUES (201803,17857,'Default Template','yes'),(17077,17075,'Invoice View','no'),(17859,17857,'MCC Superbill Form','no');
