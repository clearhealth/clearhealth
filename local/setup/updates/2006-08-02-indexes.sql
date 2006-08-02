ALTER TABLE `appointment` ADD INDEX ( `event_id` );
ALTER TABLE `event` ADD INDEX ( `start` );
ALTER TABLE `event` ADD INDEX ( `end` );
ALTER TABLE `appointment` ADD INDEX ( `patient_id` );
