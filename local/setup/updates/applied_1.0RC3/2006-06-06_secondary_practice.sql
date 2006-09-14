CREATE TABLE `secondary_practice` (
	`secondary_practice_id` INT NOT NULL ,
	`person_id` INT NOT NULL ,
	`practice_id` INT NOT NULL ,
	PRIMARY KEY ( `secondary_practice_id` ) ,
	INDEX ( `person_id` , `practice_id` )
) TYPE = MYISAM ;
