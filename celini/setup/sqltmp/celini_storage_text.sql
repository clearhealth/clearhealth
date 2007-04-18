-- 
-- Table structure for table `storage_text`
-- 

CREATE TABLE `storage_text` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(255) NOT NULL default '',
  `value` longtext NOT NULL,
  PRIMARY KEY  (`foreign_key`,`value_key`)
) ENGINE=MyISAM COMMENT='Generic way to string values';

