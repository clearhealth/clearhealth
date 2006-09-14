ALTER TABLE `person` ADD `primary_practice_id` INT NOT NULL ;

ALTER TABLE `person` ADD INDEX ( `primary_practice_id` ) ;
