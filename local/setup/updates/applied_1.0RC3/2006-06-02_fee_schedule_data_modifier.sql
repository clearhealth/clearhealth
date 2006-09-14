
CREATE TABLE `fee_schedule_data_modifier` (
  `fsd_modifier_id` int(11) NOT NULL default '0',
  `fee_schedule_id` int(11) NOT NULL default '0',
  `code_id` int(11) NOT NULL default '0',
  `modifier` int(11) NOT NULL default '0',
  `fee` float(7,2) NOT NULL default '0.00',
  PRIMARY KEY  (`fsd_modifier_id`),
  UNIQUE KEY `fee_schedule_id` (`fee_schedule_id`,`code_id`,`modifier`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

