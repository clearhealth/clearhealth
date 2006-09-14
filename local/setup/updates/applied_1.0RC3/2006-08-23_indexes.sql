ALTER TABLE `encounter` ADD INDEX ( `patient_id` );
ALTER TABLE `misc_charge` ADD INDEX ( `encounter_id` );
ALTER TABLE `clearhealth_claim` ADD INDEX ( `encounter_id` ) ;
ALTER TABLE `clearhealth_claim` ADD INDEX ( `identifier` ) ;
