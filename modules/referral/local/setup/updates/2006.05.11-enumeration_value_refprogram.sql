
CREATE TABLE `enumeration_value_refprogram` (
  `enumeration_value_id` int(11) NOT NULL default '0',
  `refprogram_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`enumeration_value_id`,`refprogram_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
        
