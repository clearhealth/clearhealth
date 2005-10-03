CREATE TABLE `codes` (
  `code_id` int(11) NOT NULL auto_increment,
  `code_text` varchar(255) default NULL,
  `code_text_short` varchar(24) default NULL,
  `code` varchar(10) default NULL,
  `code_type` tinyint(2) default NULL,
  `modifier` varchar(5) default NULL,
  `units` tinyint(3) default NULL,
  `fee` decimal(7,2) default NULL,
  `superbill` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`code_id`)
) TYPE=MyISAM 
