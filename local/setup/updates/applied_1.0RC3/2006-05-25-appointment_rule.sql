
CREATE TABLE `appointment_rule` (
  `appointment_rule_id` int(11) NOT NULL default '0',
  `appointment_ruleset_id` int(11) NOT NULL default '0',
  `type` varchar(20) NOT NULL default '',
  `label` varchar(255) NOT NULL default '',
  `data` longtext NOT NULL,
  PRIMARY KEY  (`appointment_rule_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `appointment_ruleset` (
  `appointment_ruleset_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `error_message` text NOT NULL,
  `provider_id` int(11) NOT NULL default '0',
  `procedure_id` int(11) NOT NULL default '0',
  `room_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`appointment_ruleset_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
        

