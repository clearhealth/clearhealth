CREATE TABLE `building_address` (
  `building_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  `address_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`building_id`,`address_id`),
  KEY `address_id` (`address_id`),
  KEY `building_id` (`building_id`)
) TYPE=MyISAM COMMENT='Links a building to a address specifying type. STARTEMPTY';
CREATE TABLE `building_program_identifier` (
  `building_id` int(11) NOT NULL default '0',
  `program_id` int(11) NOT NULL default '0',
  `identifier` varchar(255) NOT NULL default '',
  `x12_sender_id` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`building_id`,`program_id`)
) TYPE=MyISAM;
