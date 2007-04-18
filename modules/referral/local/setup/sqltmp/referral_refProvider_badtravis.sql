-- 
-- Table structure for table `refProvider`
-- 

CREATE TABLE `refProvider` (
  `refProvider_id` int(11) NOT NULL default '0',
  `refPractice_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `phone` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`refProvider_id`),
  KEY `refPractice_id` (`refPractice_id`)
) ENGINE=MyISAM;

