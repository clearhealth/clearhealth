-- 
-- Table structure for table `refprovider`
-- 

CREATE TABLE `refprovider` (
  `refprovider_id` int(11) NOT NULL auto_increment,
  `prefix` varchar(255) NOT NULL default '',
  `first_name` varchar(255) NOT NULL default '',
  `middle_name` varchar(255) NOT NULL default '',
  `last_name` varchar(255) NOT NULL default '',
  `direct_line` varchar(255) NOT NULL default '',
  `refpractice_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`refprovider_id`)
) ENGINE=MyISAM AUTO_INCREMENT=354 ;

