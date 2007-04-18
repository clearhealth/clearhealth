-- 
-- Table structure for table `refpractice`
-- 

CREATE TABLE `refpractice` (
  `refPractice_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `assign_by` enum('Practice','Provider') NOT NULL default 'Practice',
  `default_num_of_slots` int(11) NOT NULL default '0',
  PRIMARY KEY  (`refPractice_id`)
) ENGINE=MyISAM;

