-- 
-- Table structure for table `storage_int`
-- 

CREATE TABLE `storage_int` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(50) NOT NULL default '',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`foreign_key`,`value_key`)
) ENGINE=MyISAM COMMENT='Generic way to store integer values (also boolean)';

