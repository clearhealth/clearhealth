CREATE TABLE `buildings` (
  `id` int(11) NOT NULL default '0',
  `description` text NOT NULL,
  `name` varchar(255) NOT NULL default '',
  `practice_id` int(11) NOT NULL default '0',
  `identifier` varchar(255) NOT NULL default '',
  `facility_code_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='STARTEMPTY';
