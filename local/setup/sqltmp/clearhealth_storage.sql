CREATE TABLE `storage_date` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(50) NOT NULL default '',
  `value` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`foreign_key`,`value_key`)
) TYPE=MyISAM COMMENT='Generic way to store date values';
CREATE TABLE `storage_int` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(50) NOT NULL default '',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`foreign_key`,`value_key`)
) TYPE=MyISAM COMMENT='Generic way to store integer values (also boolean)';
CREATE TABLE `storage_string` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(50) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`foreign_key`,`value_key`)
) TYPE=MyISAM COMMENT='Generic way to string values';
CREATE TABLE `storage_text` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(255) NOT NULL default '',
  `value` longtext NOT NULL,
  PRIMARY KEY  (`foreign_key`,`value_key`)
) TYPE=MyISAM COMMENT='Generic way to string values';
