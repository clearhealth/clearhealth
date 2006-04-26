CREATE TABLE `appointment` (
  `appointment_id` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `reason` int(11) NOT NULL default '0',
  `walkin` tinyint(1) NOT NULL default '0',
  `group_appointment` tinyint(1) NOT NULL default '0',
  `has_secondary` tinyint(1) NOT NULL default '0',
  `created_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_change_id` int(11) NOT NULL default '0',
  `last_change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `creator_id` int(11) NOT NULL default '0',
  `event_id` int(11) NOT NULL default '0',
  `practice_id` int(11) NOT NULL default '0',
  `provider_id` int(11) NOT NULL default '0',
  `patient_id` int(11) NOT NULL default '0',
  `room_id` int(11) NOT NULL default '0',
  'appointment_code' varchar(255) NOT NULL default ''
  PRIMARY KEY  (`appointment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

