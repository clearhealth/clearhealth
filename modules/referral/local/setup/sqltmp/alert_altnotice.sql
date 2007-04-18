-- 
-- Table structure for table `altnotice`
-- 

CREATE TABLE `altnotice` (
  `altnotice_id` int(11) NOT NULL auto_increment,
  `creation_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `due_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `completed_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` varchar(255) NOT NULL default '',
  `diagnosis` text NOT NULL,
  `note` text NOT NULL,
  `deleted` tinyint(1) NOT NULL default '0',
  `chlfollow_up_reason` int(11) NOT NULL default '0',
  `clinic_id` int(11) NOT NULL default '0',
  `owner_type` varchar(255) NOT NULL default '',
  `owner_id` varchar(255) NOT NULL default '',
  `external_type` varchar(255) NOT NULL default '',
  `external_id` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`altnotice_id`),
  KEY `owner_type` (`owner_type`),
  KEY `owner_id` (`owner_id`),
  KEY `external_type` (`external_type`),
  KEY `external_id` (`external_id`)
) ENGINE=MyISAM;

