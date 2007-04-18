
CREATE TABLE `event` (
  `event_id` int(11) NOT NULL default '0',
  `start` datetime NOT NULL default '0000-00-00 00:00:00',
  `end` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`event_id`)
) ENGINE=MyISAM;

CREATE TABLE `recurrence` (
  `recurrence_id` int(10) unsigned NOT NULL default '0',
  `start_date` date NOT NULL default '0000-00-00',
  `end_date` date NOT NULL default '0000-00-00',
  `start_time` time default NULL,
  `end_time` time default NULL,
  PRIMARY KEY  (`recurrence_id`)
) ENGINE=MyISAM;

CREATE TABLE `recurrence_pattern` (
  `recurrence_pattern_id` int(11) NOT NULL default '0',
  `pattern_type` enum('day','monthday','monthweek','yearmonthday','yearmonthweek') NOT NULL default 'day',
  `number` int(11) default NULL,
  `weekday` enum('1','2','3','4','5','6','7') default NULL,
  `month` enum('01','02','03','04','05','06','07','08','09','10','11','12') default NULL,
  `monthday` tinyint(2) default NULL,
  `week_of_month` enum('First','Second','Third','Fourth','Last') default NULL,
  PRIMARY KEY  (`recurrence_pattern_id`)
) ENGINE=MyISAM;

CREATE TABLE `schedule` (
  `schedule_id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(150) default NULL,
  `description_long` text,
  `description_short` text,
  `schedule_code` varchar(255),
  PRIMARY KEY  (`schedule_id`)
) ENGINE=MyISAM;


INSERT INTO `enumeration_definition` VALUES (600297, 'confidentiality_levels', 'Confidentiality Levels', 'Default');
INSERT INTO `enumeration_definition` VALUES (600305, 'subscriber_to_patient_relationship', 'Subscriber To Patient Relationship', 'Default');
INSERT INTO `enumeration_definition` VALUES (600331, 'days_of_week', 'Days of Week', 'Default');
INSERT INTO `enumeration_definition` VALUES (600339, 'weeks_of_month', 'Weeks of Month', 'Default');
INSERT INTO `enumeration_definition` VALUES (600345, 'months_of_year', 'Months of Year', 'Default');
INSERT INTO `enumeration_definition` VALUES (600358, 'recurrence_pattern_type', 'Recurrence Pattern Type', 'Default');


INSERT INTO `enumeration_value` VALUES (600332, 600331, '7', 'Sunday', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600333, 600331, '1', 'Monday', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600334, 600331, '2', 'Tuesday', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600335, 600331, '3', 'Wednesday', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600336, 600331, '4', 'Thursday', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600337, 600331, '5', 'Friday', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600338, 600331, '6', 'Saturday', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600340, 600339, 'First', 'First', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600341, 600339, 'Second', 'Second', 1, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600342, 600339, 'Third', 'Third', 2, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600343, 600339, 'Fourth', 'Fourth', 3, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600344, 600339, 'Last', 'Last', 4, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600346, 600345, '01', 'January', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600347, 600345, '02', 'February', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600348, 600345, '03', 'March', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600349, 600345, '04', 'April', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600350, 600345, '05', 'May', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600351, 600345, '06', 'June', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600352, 600345, '07', 'July', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600353, 600345, '08', 'August', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600354, 600345, '09', 'September', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600355, 600345, '10', 'October', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600356, 600345, '11', 'November', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600357, 600345, '12', 'December', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600359, 600358, 'day', 'By Day (Every 3 Days)', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600360, 600358, 'monthweek', 'By Weekday Per Month (Every Third Tuesday)', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600361, 600358, 'monthday', 'By Day of Month (Every Fifth)', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600362, 600358, 'yearmonthday', 'By Day of Month Per Year (Every December 3rd)', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600363, 600358, 'yearmonthweek', 'By Weekday Per Month Per Year (Every Third Tuesday of November)', 0, '', '', 1);
