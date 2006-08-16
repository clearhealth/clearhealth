-- This clears out some of the tables before importing the old data --

TRUNCATE table category;
TRUNCATE table `user`;
TRUNCATE TABLE preferences;
TRUNCATE TABLE record_sequence;
TRUNCATE TABLE report_templates;
TRUNCATE TABLE reports;
TRUNCATE TABLE sequences;
TRUNCATE TABLE audit_log;
TRUNCATE TABLE audit_log_field;
TRUNCATE TABLE payment_claimline;

ALTER TABLE `hl7_message` CHANGE `id` `hl7_message_id` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `hl7_message` ADD `type` INT NOT NULL ,
ADD `processed` INT NOT NULL ;
