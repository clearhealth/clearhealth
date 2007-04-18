
CREATE TABLE `fbqueue` (
`queue_id` INT NOT NULL ,
`name` VARCHAR( 255 ) NOT NULL ,
`max_items` INT NOT NULL ,
`num_items` INT NOT NULL ,
`ids` MEDIUMTEXT NOT NULL ,
PRIMARY KEY ( `queue_id` )
);

