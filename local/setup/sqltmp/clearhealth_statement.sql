CREATE TABLE `statement_history` (
  `statement_history_id` int(11) NOT NULL default '0',
  `patient_id` INT NOT NULL,
  `report_statement_id` INT NOT NULL default '0',
  `statement_number` int(11) NOT NULL default '0',
  `date_generated` datetime NOT NULL default '0000-00-00 00:00:00',
  `amount` float(7,2) NOT NULL default '0.00',
  `type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`statement_history_id`)
)
