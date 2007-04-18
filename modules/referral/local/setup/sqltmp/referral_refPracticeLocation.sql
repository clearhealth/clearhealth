-- 
-- Table structure for table `refPracticeLocation`
-- 

CREATE TABLE `refPracticeLocation` (
  `refPracticeLocation_id` int(11) NOT NULL default '0',
  `refPractice_id` int(11) NOT NULL default '0',
  `address1` varchar(255) NOT NULL default '',
  `address2` varchar(255) NOT NULL default '',
  `city` varchar(255) NOT NULL default '',
  `state` varchar(255) NOT NULL default '',
  `zipcode` varchar(255) NOT NULL default '',
  `appointment_number` varchar(255) NOT NULL default '',
  `fax_number` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`refPracticeLocation_id`)
) ENGINE=MyISAM;

