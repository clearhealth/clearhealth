ALTER TABLE `clearhealth_claim` ADD INDEX ( `encounter_id` ) ;
ALTER TABLE `coding_data` ADD INDEX ( `parent_id` ) ;
ALTER TABLE `coding_data` ADD INDEX ( `foreign_id` ) ;
ALTER TABLE `payment_claimline` ADD INDEX ( `payment_id` ) ;
ALTER TABLE `payment` ADD INDEX ( `encounter_id` ) ;
ALTER TABLE `occurences` ADD INDEX ( `start` ) ;
ALTER TABLE `occurences` ADD INDEX ( `end` ) ;
