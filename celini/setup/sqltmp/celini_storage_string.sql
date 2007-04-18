-- 
-- Table structure for table `storage_string`
-- 

CREATE TABLE `storage_string` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(50) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`foreign_key`,`value_key`)
) ENGINE=MyISAM COMMENT='Generic way to string values';

