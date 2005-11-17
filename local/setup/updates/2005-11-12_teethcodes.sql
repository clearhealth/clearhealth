CREATE TABLE `coding_data_dental` (
	`coding_data_id` INT( 11 ) NOT NULL ,
	`tooth` ENUM( 'N/A', 'All', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', 'All (Primary)', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T' ) DEFAULT 'N/A' NOT NULL ,
	`toothside` ENUM( 'N/A', 'Front', 'Back', 'Top', 'Left', 'Right' ) DEFAULT 'N/A' NOT NULL,
	PRIMARY KEY (`coding_data_id`)
) TYPE = MYISAM ;