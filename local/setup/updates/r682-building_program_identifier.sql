CREATE TABLE `building_program_identifier` (
`building_id` INT NOT NULL ,
`program_id` INT NOT NULL ,
`identifier` VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( `building_id` , `program_id` )
);
