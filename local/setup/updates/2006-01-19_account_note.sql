
CREATE TABLE `account_note` (
  `account_note_id` int(11) NOT NULL default '0',
  `patient_id` INT NOT NULL AFTER `account_note_id`,
  `claim_id` varchar(100) NOT NULL default '',
  `user_id` int(11) NOT NULL default '0',
  `date_posted` datetime NOT NULL default '0000-00-00 00:00:00',
  `note` text NOT NULL,
  `note_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`account_note_id`)
);

