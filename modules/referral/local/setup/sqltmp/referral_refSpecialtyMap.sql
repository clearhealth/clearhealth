-- 
-- Table structure for table `refSpecialtyMap`
-- 

CREATE TABLE `refSpecialtyMap` (
  `refSpecialityMap_id` int(11) NOT NULL auto_increment,
  `external_type` varchar(255) NOT NULL default '',
  `external_id` int(11) NOT NULL default '0',
  `enumeration_value_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`refSpecialityMap_id`)
) ENGINE=MyISAM AUTO_INCREMENT=114 ;

