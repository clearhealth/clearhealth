-- 
-- Table structure for table `enumeration_value`
-- 

CREATE TABLE `enumeration_value` (
  `enumeration_value_id` int(11) NOT NULL default '0',
  `enumeration_id` int(11) NOT NULL default '0',
  `key` int(11) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  `sort` int(11) NOT NULL default '0',
  `extra1` varchar(255) NOT NULL default '',
  `extra2` varchar(255) NOT NULL default '',
  `status` int(1) NOT NULL default '1',
  PRIMARY KEY  (`enumeration_value_id`)
) TYPE=MyISAM;


-- 
-- Table structure for table `enumeration_definition`
-- 

CREATE TABLE `enumeration_definition` (
  `enumeration_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `type` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`enumeration_id`),
  UNIQUE KEY `name` (`name`)
) TYPE=MyISAM;

