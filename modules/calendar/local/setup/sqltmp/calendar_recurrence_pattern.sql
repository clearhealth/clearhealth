CREATE TABLE IF NOT EXISTS `recurrence_pattern` (
  `recurrence_pattern_id` int(11) NOT NULL default '0',
  `pattern_type` enum('day','monthday','monthweek','yearmonthday','yearmonthweek') NOT NULL default 'day',
  `number` int(11) default NULL,
  `weekday` enum('1','2','3','4','5','6','7') default NULL,
  `month` enum('01','02','03','04','05','06','07','08','09','10','11','12') default NULL,
  `monthday` tinyint(2) default NULL,
  `week_of_month` enum('First','Second','Third','Fourth','Last') default NULL,
  PRIMARY KEY  (`recurrence_pattern_id`)
) ENGINE=MyISAM;

