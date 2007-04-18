-- 
-- Table structure for table `refpractice_specialty`
-- 

CREATE TABLE `refpractice_specialty` (
  `refpractice_specialty_id` int(11) NOT NULL auto_increment,
  `specialty` int(11) NOT NULL default '0',
  `form` varchar(255) NOT NULL default '0',
  `refpractice_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`refpractice_specialty_id`)
) ENGINE=MyISAM AUTO_INCREMENT=43 ;

