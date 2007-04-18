CREATE TABLE IF NOT EXISTS `schedule` (
  `schedule_id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(150) default NULL,
  `description_long` text,
  `description_short` text,
  `schedule_code` varchar(255),
  PRIMARY KEY  (`schedule_id`)
) ENGINE=MyISAM;

