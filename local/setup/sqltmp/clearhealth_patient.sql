CREATE TABLE `patient` (
  `person_id` int(11) NOT NULL default '0',
  `is_default_provider_primary` int(11) NOT NULL default '0',
  `default_provider` int(11) NOT NULL default '0',
  `record_number` int(11) NOT NULL default '0',
  `employer_name` varchar(255) NOT NULL default '' COMMENT '\0\0\0\0\0\0\0\0\0\0\0!\0\0ï¿½',
  PRIMARY KEY  (`person_id`),
  KEY `record_number` (`record_number`)
) TYPE=MyISAM COMMENT='An patient extends the person entity';
CREATE TABLE `patient_note` (
  `patient_note_id` int(11) NOT NULL default '0',
  `patient_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `priority` int(11) NOT NULL default '0',
  `note_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `note` text NOT NULL,
  `deprecated` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`patient_note_id`)
) TYPE=MyISAM;
CREATE TABLE `patient_statistics` (
  `person_id` int(11) NOT NULL default '0',
  `ethnicity` int(11) NOT NULL default '0',
  `race` int(11) NOT NULL default '0',
  `income` int(11) NOT NULL default '0',
  `language` int(11) NOT NULL default '0',
  `migrant_status` int(11) NOT NULL default '0',
  `registration_location` int(11) NOT NULL default '0',
  `sign_in_date` date NOT NULL default '0000-00-00',
  `monthly_income` int(11) NOT NULL default '0',
  `family_size` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`)
) TYPE=MyISAM;
CREATE TABLE `patient_chronic_code` (
`patient_id` int( 11 ) NOT NULL default '0',
`chronic_care_code` int( 11 ) NOT NULL default '0',
PRIMARY KEY ( `patient_id` , `chronic_care_code` )
);
