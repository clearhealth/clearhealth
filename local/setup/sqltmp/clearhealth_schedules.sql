CREATE TABLE `schedules` (
  `id` int(11) NOT NULL default '0',
  `schedule_code` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `description_long` text NOT NULL,
  `description_short` text NOT NULL,
  `practice_id` int(11) NOT NULL default '0',
  `user_id` int(11) default NULL,
  `room_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
INSERT INTO `schedules` VALUES (502530,'NS','No Shows','This is primarily for reporting purposes. When a patients appointment is set to no-show. They are assigned to this schedule. You must have two events groups names \"No Shows\" and \"Cancelations\"','Schedule No Shows are assigned to',0,0,0),(502531,'ADM','Admin Events','Anything added to the admin schedule will appear on every calendar. Use this for practice-wide meetings.','Admin Events appear on every schedule',0,0,0);
