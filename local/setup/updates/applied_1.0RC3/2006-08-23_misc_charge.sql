CREATE TABLE `misc_charge` (
`misc_charge_id` INT NOT NULL ,
`encounter_id` INT NOT NULL ,
`amount` FLOAT( 7, 2 ) NOT NULL ,
`charge_date` DATETIME NOT NULL ,
`title` VARCHAR( 50 ) NOT NULL ,
`note` TEXT NOT NULL ,
PRIMARY KEY ( `misc_charge_id` )
) TYPE = MYISAM ;

