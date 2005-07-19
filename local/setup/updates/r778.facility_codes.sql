CREATE TABLE `facility_codes` (
	`code` VARCHAR( 5 ) NOT NULL ,
	`name` VARCHAR( 255 ) NOT NULL ,
	PRIMARY KEY ( `code` )
) COMMENT = 'Stores x("12", "facility"),
_code code/human name combos';


INSERT INTO `facility_codes` (`code`, `name`) VALUES
("11", "Office "),
("12", "Home "),
("21", "Inpatient Hospital "),
("22", "Outpatient Hospital "),
("23", "Emergency Room - Hospital "),
("24", "Ambulatory Surgical Center "),
("25", "Birthing Center "),
("26", "Military Treatment Facility "),
("31", "Skilled Nursing Facility "),
("32", "Nursing Facility "),
("33", "Custodial Care Facility "),
("34", "Hospice "),
("41", "Ambulance - Land "),
("42", "Ambulance - Air or Water "),
("51", "Inpatient Psychiatric Facility "),
("52", "Psychiatric Facility Partial Hospitalization "),
("53", "Community Mental Health Center "),
("54", "Intermediate Care Facility/Mentally Retarded "),
("55", "Residential Substance Abuse Treatment Facility "),
("56", "Psychiatric Residential Treatment Center "),
("50", "Federally Qualified Health Center "),
("60", "Mass Immunization Center "),
("61", "Comprehensive Inpatient Rehabilitation Facility "),
("62", "Comprehensive Outpatient Rehabilitation Facility "),
("65", "End Stage Renal Disease Treatment Facility "),
("71", "State or Local Public Health Clinic "),
("72", "Rural Health Clinic "),
("81", "Independent Laboratory "),
("99", "Other Unlisted Facility");

ALTER TABLE `buildings` ADD `facility_code_id` INT NOT NULL ;

