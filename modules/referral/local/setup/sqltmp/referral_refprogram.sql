-- 
-- Table structure for table `refprogram`
-- 

CREATE TABLE `refprogram` (
  `refprogram_id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `schema` int(11) NOT NULL default '0',
  PRIMARY KEY  (`refprogram_id`)
) ENGINE=MyISAM AUTO_INCREMENT=254 ;

