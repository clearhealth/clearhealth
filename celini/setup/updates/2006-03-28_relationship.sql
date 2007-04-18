#ALTER TABLE `relationship` DROP INDEX `parent_type` ;

ALTER TABLE `relationship` ADD INDEX ( `parent_id` ) ;
ALTER TABLE `relationship` ADD INDEX ( `child_id` ) ;
ALTER TABLE `relationship` ADD INDEX ( `parent_type` ); 
ALTER TABLE `relationship` ADD INDEX ( `child_type` ) ;

ALTER TABLE `relationship` ADD INDEX `parent_and_types` ( `parent_id` , `parent_type` , `child_type` ) ;
ALTER TABLE `relationship` ADD INDEX `child_and_types` ( `child_id` , `parent_type` , `child_type` ) ;
