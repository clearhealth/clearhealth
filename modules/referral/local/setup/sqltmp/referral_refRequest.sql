-- 
-- Table structure for table `refRequest`
-- 

CREATE TABLE `refRequest` (
  `refRequest_id` int(10) unsigned NOT NULL auto_increment,
  `date` date NOT NULL default '0000-00-00',
  `eligibility` varchar(255) NOT NULL default '0',
  `eligible_thru` date NOT NULL default '0000-00-00',
  `refSpecialty` int(11) NOT NULL default '0',
  `refRequested_day` int(11) NOT NULL default '0',
  `refRequested_time` int(11) NOT NULL default '0',
  `refStatus` int(11) NOT NULL default '0',
  `reason` varchar(255) NOT NULL default '',
  `history` varchar(255) NOT NULL default '',
  `notes` varchar(255) NOT NULL default '',
  `translator` int(11) NOT NULL default '0',
  `transportation` int(11) NOT NULL default '0',
  `occurence_id` int(11) NOT NULL default '0',
  `refappointment_id` int(11) NOT NULL default '0',
  `refprogram_id` int(11) NOT NULL default '0',
  `patient_id` int(11) NOT NULL default '0',
  `visit_id` int(11) NOT NULL default '0',
  `initiator_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`refRequest_id`),
  KEY `patient_id` (`patient_id`),
  KEY `visit_id` (`visit_id`),
  KEY `initiator_id` (`initiator_id`)
) ENGINE=MyISAM AUTO_INCREMENT=513744 ;

