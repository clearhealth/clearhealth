-- 
-- Table structure for table `refprogram_member_slot`
-- 

CREATE TABLE `refprogram_member_slot` (
  `refprogram_member_slot_id` int(11) NOT NULL auto_increment,
  `month` int(11) NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  `slots` int(11) NOT NULL default '0',
  `external_type` enum('Practice','Provider') NOT NULL default 'Practice',
  `external_id` int(11) NOT NULL default '0',
  `refprogram_member_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`refprogram_member_slot_id`)
) ENGINE=MyISAM AUTO_INCREMENT=358 ;

