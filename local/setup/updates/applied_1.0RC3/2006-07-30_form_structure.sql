CREATE TABLE `form_structure` (
  `form_structure_id` int(11) NOT NULL auto_increment,
  `form_id` int(11) NOT NULL default '0',
  `field_name` varchar(100) NOT NULL default '',
  `field_type` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`form_structure_id`)
) ENGINE=MyISAM;