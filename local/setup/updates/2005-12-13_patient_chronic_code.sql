CREATE TABLE `patient_chronic_code` (
`patient_id` int( 11 ) NOT NULL default '0',
`chronic_care_code` int( 11 ) NOT NULL default '0',
PRIMARY KEY ( `patient_id` , `chronic_care_code` )
) ENGINE = InnoDB DEFAULT CHARSET = latin1;

