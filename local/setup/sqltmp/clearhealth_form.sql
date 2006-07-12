CREATE TABLE `form` (
  `form_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `system_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`form_id`)
) TYPE=MyISAM COMMENT='Contains the EMR extending forms STARTWITHDATA';
INSERT INTO `form` VALUES (800,'Test Data','Some random data'),(1710,'Patient Vitals','Patient Vital Statistics');
CREATE TABLE `form_data` (
  `form_data_id` int(11) NOT NULL default '0',
  `form_id` int(11) NOT NULL default '0',
  `external_id` int(11) NOT NULL default '0',
  `last_edit` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`form_data_id`)
) TYPE=MyISAM COMMENT='Links in the form data STARTWITHDATA';
