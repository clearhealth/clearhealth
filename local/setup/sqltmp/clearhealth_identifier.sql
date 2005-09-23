CREATE TABLE `identifier` (
  `identifier_id` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  `identifier` varchar(100) NOT NULL default '',
  `identifier_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`identifier_id`)
) TYPE=MyISAM;
