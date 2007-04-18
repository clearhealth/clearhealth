-- 
-- Table structure for table `refprogram_member`
-- 

CREATE TABLE `refprogram_member` (
  `refprogram_member_id` int(11) NOT NULL auto_increment,
  `refprogram_id` int(11) NOT NULL default '0',
  `external_id` int(11) NOT NULL default '0',
  `external_type` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`refprogram_member_id`)
) ENGINE=MyISAM AUTO_INCREMENT=355 ;

