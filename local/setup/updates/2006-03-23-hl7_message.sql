CREATE TABLE `hl7_message` (
  `id` int(11) NOT NULL default '0',
  `control_id` varchar(50) NOT NULL default '',
  `message` longtext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `control_id` (`control_id`)
) ENGINE=MyISAM DEFAULT ;
