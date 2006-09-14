CREATE TABLE `schedule_event` (
`event_id` INT NOT NULL ,
`event_group_id` INT NOT NULL ,
PRIMARY KEY ( `event_id` ) ,
INDEX ( `event_group_id` )
);

ALTER TABLE `event_group` ADD `room_id` INT NOT NULL ;
ALTER TABLE `event_group` ADD INDEX ( `room_id` ) ;
ALTER TABLE `event_group` ADD `schedule_id` INT NOT NULL ;
ALTER TABLE `event_group` ADD INDEX ( `schedule_id` ) ;
ALTER TABLE `schedule` ADD `provider_id` INT NOT NULL ;
ALTER TABLE `schedule` ADD INDEX ( `provider_id` ) ;
ALTER TABLE `appointment` ADD `event_group_id` INT NOT NULL ;
ALTER TABLE `appointment` ADD INDEX ( `event_group_id` ) ;
