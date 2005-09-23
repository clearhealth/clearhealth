CREATE TABLE `import_map` (
  `old_id` int(11) NOT NULL default '0',
  `new_id` int(11) default NULL,
  `old_table_name` varchar(100) NOT NULL default '',
  `new_object_name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`old_id`,`old_table_name`)
) TYPE=MyISAM;
