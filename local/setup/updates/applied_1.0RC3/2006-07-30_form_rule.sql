CREATE TABLE `form_rule` (
  `form_rule_id` int(11) NOT NULL auto_increment,
  `field_name` varchar(100) NOT NULL default '',
  `rule_name` varchar(30) NOT NULL default '',
  `operator` char(3) NOT NULL default '',
  `value` varchar(30) NOT NULL default '',
  `message` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`form_rule_id`)
) ENGINE=MyISAM;