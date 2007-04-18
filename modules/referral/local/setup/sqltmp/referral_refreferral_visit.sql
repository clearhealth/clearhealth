-- 
-- Table structure for table `refreferral_visit`
-- 

CREATE TABLE `refreferral_visit` (
  `refreferral_visit_id` int(11) NOT NULL auto_increment,
  `refappointment_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`refreferral_visit_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 ;

