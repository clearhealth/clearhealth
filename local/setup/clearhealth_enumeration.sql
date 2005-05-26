-- phpMyAdmin SQL Dump
-- version 2.6.1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: May 26, 2005 at 10:17 AM
-- Server version: 4.1.10
-- PHP Version: 4.3.10
-- 
-- Database: `clearhealth`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `enumeration`
-- 

CREATE TABLE `enumeration` (
  `name` varchar(100) NOT NULL default '',
  `title` varchar(100) NOT NULL default '',
  `description` tinytext NOT NULL,
  `relation_of_information_code` enum('A - On file','I - Informed Consent','M - Limited Ability','N - Not allowed','O - On file','Y - Has permission') NOT NULL default 'A - On file',
  `subscriber_to_patient_relationship` enum('Self','Parent','Spouse','Other') NOT NULL default 'Self' COMMENT '\0\0\0\0\0\0\0\0\0\0\0!\0\0ÃƒÂ¯Ã‚Â¿Ã‚Â½',
  `code_modifier` enum('A0','A1','A2','B1','B2','C6') NOT NULL default 'A0',
  `encounter_reason` enum('Physical','Other') NOT NULL default 'Physical',
  `payment_type` enum('visa','mastercard','amex','check','cash','remittance') NOT NULL default 'visa',
  `encounter_date_type` enum('Date of Death','Date Last Seen','Date of Onset','Date of Initial Visit','Date of Cant Work Start','Date of Cant Work Stop','Date of Hospitilization Start','Date of Hospitilization Stop') NOT NULL default 'Date of Death',
  `address_type` enum('Home','Billing','Other','Main','Secondary') NOT NULL default 'Home',
  `appointment_reasons` enum('Physical','FP','CDP','CHDP','F/U','Sick','Lab Only') NOT NULL default 'Physical',
  `assigning` enum('A - Assigned','B - Assigned Lab Services Only','C - Not Assigned','P - Assignment Refused') NOT NULL default 'A - Assigned',
  `company_number_type` enum('Primary','Fax') NOT NULL default 'Primary',
  `company_type` enum('Insurance') NOT NULL default 'Insurance',
  `disposition` enum('New','Waiting','Compete') NOT NULL default 'New',
  `encounter_person_type` enum('Attending Nurse','Referring Provider') NOT NULL default 'Attending Nurse',
  `ethnicity` enum('Hispanic','Caucasian') NOT NULL default 'Hispanic',
  `gender` enum('Male','Female','Unknown') NOT NULL default 'Male',
  `group_list` enum('All','Arizona','California') NOT NULL default 'All',
  `identifier_type` enum('SSN','EIN') NOT NULL default 'SSN',
  `income` enum('Unknown','Under 100% of Poverty','100-200% of Poverty','Above 200% of Poverty') NOT NULL default 'Unknown',
  `language` enum('English','Spanish','Chinese','Japanese','Korean','Portuguese','Russian','Sign Language','Vietnamese','Tagalog','Punjabi','Hindustani','Armenian','Arabic','Laotian','Hmong','Cambodian','Finnish','Other') NOT NULL default 'English',
  `marital_status` enum('Single','Married','Other') NOT NULL default 'Single',
  `migrant_status` enum('Migrant Worker') NOT NULL default 'Migrant Worker',
  `number_type` enum('Home','Mobile','Work','Emergency','Fax') NOT NULL default 'Home',
  `payer_type` enum('medicare','champus','medical','private','feca','medicaid','champusva','otherhcfa','litigation') NOT NULL default 'medicare',
  `person_to_person_relation_type` enum('Dependant','Spouse','Grand Parent','Other') NOT NULL default 'Dependant',
  `person_type` enum('Patient','Provider','Mid-level','Staff','Subscriber') NOT NULL default 'Patient',
  `provider_number_type` enum('State License') NOT NULL default 'State License',
  `provider_reporting_type` enum('MD','RNFP','RN','PA','MA') NOT NULL default 'MD',
  `quality_of_file` enum('Good','Bad') NOT NULL default 'Good',
  `race` enum('White/Hispanic','Black','Native American/Alaskan Native','Asian/Pacific Islander','Other/Unknown') NOT NULL default 'White/Hispanic',
  `state` enum('AL','AK','AZ','AR','CA','CO','CT','DE','DC','FL','GA','HI','ID','IL','IN','IA','KS','KY','LA','ME','MD','MA','MI','MN','MS','MO','MT','NE','NV','NH','NJ','NM','NY','NC','ND','OH','OK','OR','PA','RI','SC','SD','TN','TX','UT','VT','VA','WA','WV','WI','WY','PR') NOT NULL default 'AL',
  `subscriber_to_patient` enum('Spouse','Parent') NOT NULL default 'Spouse',
  PRIMARY KEY  (`name`)
) TYPE=MyISAM COMMENT='enums stored as new col, metadata 1 row perenumSTARTWITHDATA';

-- 
-- Dumping data for table `enumeration`
-- 

INSERT INTO `enumeration` VALUES ('gender', 'Gender', 'Gender for billing purposes', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('person_type', 'Person Type', 'Types of people in the system. Like "patient" and the different user types', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('company_type', 'Company Type', 'Types of companies like "insurance"', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('state', 'State', 'A list of the states in the US', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('number_type', 'Phone Number Type', 'Types of phone numbers, like "home"', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('company_number_type', 'Company Number Type', 'Company phone number types like "fax"', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('address_type', 'Address Type', 'Address Types the system should be aware of. Like "home" or "billing"', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('disposition', 'Disposition', 'Dispositions like "new" "waiting" or "complete"', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('quality_of_file', 'Quality of File', 'Definable quality of life measures.', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('group_list', 'File Groups', 'Arbitrary groups for files', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('identifier_type', 'Identifier Type', 'Identifiers for billing, must include SocSec and EIN', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('assigning', 'Assigning', 'Various levels of assignment', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('relation_of_information_code', '', '', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('provider_number_type', 'Provider Number Type', 'Numbers tracked for Providers, like the State License number', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('subscriber_to_patient', 'Subscriber to patient', 'List of patient relationships for billing purposes', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('payer_type', 'Payer Type', 'Different types of payers', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('person_to_person_relation_type', 'Person to person relation type', 'List of family relationships to track', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('code_modifier', 'Code Modifier', 'Modifiers available for codes.', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('encounter_reason', 'Encounter Reason', 'Reasons for an encounter', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('encounter_date_type', 'Encounter Date Type', 'Types for extra dates attached to an encounter', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('payment_type', 'Payment Type', 'Types of payments', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('ethnicity', 'Ethnicity', 'For use in tracking social data. Like "Hispanic"', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('marital_status', 'Marital Status', 'Maritial Status primarily for billing', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('language', 'Languages', 'List of languages spoken.', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('race', 'Race', 'Different classes of race. Like "black" or "white/hispanic"', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('income', 'Income', 'Income levels for social data. like "under 100% poverty"', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('migrant_status', 'Migrant Status', 'Migrant status for social data', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('appointment_reasons', 'Appointment Reason', 'Different classes of appointments, like "physical"', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('provider_reporting_type', 'Provider Reporting Type', 'Type of Provider, for reporting purposes', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
INSERT INTO `enumeration` VALUES ('encounter_person_type', 'Encounter Person Type', 'People that can be associated with an encounter, like Referring Provider', 'A - On file', 'Self', 'A0', 'Physical', 'visa', 'Date of Death', 'Home', 'Physical', 'A - Assigned', 'Primary', 'Insurance', 'New', 'Attending Nurse', 'Hispanic', 'Male', 'All', 'SSN', 'Unknown', 'English', 'Single', 'Migrant Worker', 'Home', 'medicare', 'Dependant', 'Patient', 'State License', 'MD', 'Good', 'White/Hispanic', 'AL', 'Spouse');
        
