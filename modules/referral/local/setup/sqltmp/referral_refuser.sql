-- 
-- Table structure for table `refuser`
-- 

CREATE TABLE `refuser` (
  `refuser_id` int(11) NOT NULL auto_increment,
  `external_user_id` int(11) NOT NULL default '0',
  `refusertype` int(11) NOT NULL default '0',
  `refprogram_id` int(11) NOT NULL default '0',
  `deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`refuser_id`)
) ENGINE=MyISAM AUTO_INCREMENT=404 ;

