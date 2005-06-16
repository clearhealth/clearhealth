CREATE TABLE `patient_note` (
	`patient_note_id` int(11) NOT NULL default '0',
	`patient_id` int(11) NOT NULL default '0',
	`user_id` int(11) NOT NULL default '0',
	`priority` int(11) NOT NULL default '0',
	`note_date` datetime NOT NULL default '0000-00-00 00:00:00',
	`note` text NOT NULL,
	PRIMARY KEY  (`patient_note_id`)
) TYPE=MyISAM;
