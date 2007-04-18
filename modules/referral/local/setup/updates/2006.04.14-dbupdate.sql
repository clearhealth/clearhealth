## mysqldiff 0.30
## 
## run on Fri Apr 14 08:40:41 2006
##
## --- file: 2006.04.14-staging_referral_2006_01_06.sql
## +++ file: 2006.04.14-saturnCelini.sql

ALTER TABLE `refPracticeLocation` ADD COLUMN `phone_number` varchar(255) NOT NULL default '';
ALTER TABLE `refpractice` CHANGE COLUMN `status` `status` int(1) NOT NULL default '0'; # was int(1) default NULL

CREATE TABLE IF NOT EXISTS `enumeration_value_by_clinic` (
  `enumeration_value_by_clinic_id` int(11) NOT NULL default '0',
  `enumeration_id` int(11) NOT NULL default '0',
  `key` int(11) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  `sort` int(11) NOT NULL default '0',
  `clinic_id` int(11) NOT NULL default '0',
  `status` int(1) NOT NULL default '0',
  PRIMARY KEY  (`enumeration_value_by_clinic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `enumeration_value_practice` (
  `enumeration_value_id` int(11) NOT NULL default '0',
  `practice_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`enumeration_value_id`,`practice_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `relationship` (
  `relationship_id` int(11) NOT NULL auto_increment,
  `parent_type` varchar(255) NOT NULL default '',
  `parent_id` int(11) NOT NULL default '0',
  `child_type` varchar(255) NOT NULL default '',
  `child_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`relationship_id`),
  KEY `parent_id` (`parent_id`),
  KEY `child_id` (`child_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


