ALTER TABLE `fee_schedule_discount_level` ADD `type` ENUM( 'percent', 'flat' ) NOT NULL AFTER `discount` 
