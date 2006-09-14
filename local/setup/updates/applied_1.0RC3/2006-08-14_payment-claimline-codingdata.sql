ALTER TABLE `payment_claimline` ADD `coding_data_id` INT NOT NULL AFTER `code_id` ;

ALTER TABLE `payment_claimline` ADD INDEX ( `coding_data_id` ) ;

ALTER TABLE `payment_claimline` ADD INDEX ( `payment_id` ) ;

ALTER TABLE `payment_claimline` ADD INDEX ( `code_id` ) ;
