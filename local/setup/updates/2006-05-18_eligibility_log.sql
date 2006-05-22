CREATE TABLE `eligibility_log` (
  `eligibility_log_id` int(11) NOT NULL default '0',
  `patient_id` int(11) NOT NULL default '0',
  `log_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `message` longtext NOT NULL,
  PRIMARY KEY  (`eligibility_log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
