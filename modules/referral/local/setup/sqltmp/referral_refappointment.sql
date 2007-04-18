-- 
-- Table structure for table `refappointment`
-- 

CREATE TABLE `refappointment` (
  `refappointment_id` int(11) NOT NULL auto_increment,
  `refrequest_id` int(11) NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `refpractice_id` int(11) NOT NULL default '0',
  `reflocation_id` int(11) NOT NULL default '0',
  `refprovider_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`refappointment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=393 ;

