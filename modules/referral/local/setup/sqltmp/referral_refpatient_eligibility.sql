-- 
-- Table structure for table `refpatient_eligibility`
-- 

CREATE TABLE `refpatient_eligibility` (
  `refpatient_eligibility_id` int(11) NOT NULL auto_increment,
  `eligibility` varchar(255) NOT NULL default '',
  `eligible_thru` date NOT NULL default '0000-00-00',
  `patient_id` int(11) NOT NULL default '0',
  `refprogram_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`refpatient_eligibility_id`)
) ENGINE=MyISAM AUTO_INCREMENT=394 ;

