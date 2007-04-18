-- 
-- Table structure for table `storage_date`
-- 

CREATE TABLE `storage_date` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(50) NOT NULL default '',
  `value` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`foreign_key`,`value_key`)
) ENGINE=MyISAM COMMENT='Generic way to store date values';

