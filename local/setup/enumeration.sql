-- phpMyAdmin SQL Dump
-- version 2.6.1-rc2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Mar 17, 2005 at 10:17 AM
-- Server version: 4.0.23
-- PHP Version: 4.3.10
-- 
-- Database: `clearhealth`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `enumeration`
-- 

DROP TABLE IF EXISTS `enumeration`;
CREATE TABLE `enumeration` (
  `name` varchar(100) NOT NULL default '',
  `title` varchar(100) NOT NULL default '',
  `description` tinytext NOT NULL,
  `gender` enum('Male','Female','Not Specified') NOT NULL default 'Male',
  `company_number_type` enum('Primary','Fax') NOT NULL default 'Primary',
  `quality_of_file` enum('Good','Bad') NOT NULL default 'Good',
  `disposition` enum('New','Waiting','Compete') NOT NULL default 'New',
  `state` enum('Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District of Columbia','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virginia','West Virginia','Wisconsin','Wyoming') NOT NULL default 'Alabama',
  `group_list` enum('All','Arizona','California') NOT NULL default 'All',
  `company_type` enum('Insurance') NOT NULL default 'Insurance',
  `assigning` enum('A - Assigned','B - Assigned Lab Services Only','C - Not Assigned','P - Assignment Refused') NOT NULL default 'A - Assigned',
  `relation_of_information_code` enum('A - On file','I - Informed Consent','M - Limited Ability','N - Not allowed','O - On file','Y - Has permission') NOT NULL default 'A - On file',
  `person_type` enum('Patient','Provider','Mid-level','Staff','Subscriber') NOT NULL default 'Patient',
  `provider_number_type` enum('State License') NOT NULL default 'State License',
  `subscriber_to_patient_relationship` enum('Self','Mother','Father') NOT NULL default 'Self',
  `payer_type` enum('medicare') NOT NULL default 'medicare',
  `person_to_person_relation_type` enum('Dependant','Spouse','Grand Parent','Other') NOT NULL default 'Dependant',
  `identifier_type` enum('SSN','EIN') NOT NULL default 'SSN',
  `number_type` enum('Home','Mobile','Work','Emergency') NOT NULL default 'Home',
  `address_type` enum('Home','Billing','Other') NOT NULL default 'Home',
  `code_modifier` enum('A0','A1','A2','B1','B2','C6') NOT NULL default 'A0',
  `encounter_reason` enum('Physical','Other') NOT NULL default 'Physical',
  `encounter_date_type` enum('Initial Visit Date','Update me please') NOT NULL default 'Initial Visit Date',
  `encounter_person_type` enum('blah') NOT NULL default 'blah',
  `payment_type` enum('visa','mastercard','amex','check','cash','remittance') NOT NULL default 'visa',
  `ethnicity` enum('Hispanic','Caucasian') NOT NULL default 'Hispanic',
  PRIMARY KEY  (`name`)
) TYPE=MyISAM COMMENT='Each enum stored as a new col, metadata in 1 row per enum';

-- 
-- Dumping data for table `enumeration`
-- 

INSERT INTO `enumeration` VALUES ('gender', 'Gender', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Dependant', 'SSN', 'Home', 'Home', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Hispanic');
INSERT INTO `enumeration` VALUES ('person_type', 'Person Type', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Dependant', 'SSN', 'Home', 'Home', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Hispanic');
INSERT INTO `enumeration` VALUES ('company_type', 'Company Type', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Dependant', 'SSN', 'Home', 'Home', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Hispanic');
INSERT INTO `enumeration` VALUES ('state', 'State', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Dependant', 'SSN', 'Home', 'Home', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Hispanic');
INSERT INTO `enumeration` VALUES ('number_type', 'Phone Number Type', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Dependant', 'SSN', 'Home', 'Home', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Hispanic');
INSERT INTO `enumeration` VALUES ('company_number_type', 'Company Number Type', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Dependant', 'SSN', 'Home', 'Home', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Hispanic');
INSERT INTO `enumeration` VALUES ('address_type', 'Address Type', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Dependant', 'SSN', 'Home', 'Home', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Hispanic');
INSERT INTO `enumeration` VALUES ('disposition', 'Disposition', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Dependant', 'SSN', 'Home', 'Home', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Hispanic');
INSERT INTO `enumeration` VALUES ('quality_of_file', 'Quality of File', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Dependant', 'SSN', 'Home', 'Home', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Hispanic');
INSERT INTO `enumeration` VALUES ('group_list', 'File Groups', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Dependant', 'SSN', 'Home', 'Home', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Hispanic');
INSERT INTO `enumeration` VALUES ('identifier_type', 'Identifier Type', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Dependant', 'SSN', 'Home', 'Home', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Hispanic');
INSERT INTO `enumeration` VALUES ('assigning', 'Assigning', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Dependant', 'SSN', 'Home', 'Home', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Hispanic');
INSERT INTO `enumeration` VALUES ('relation_of_information_code', '', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Dependant', 'SSN', 'Home', 'Home', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Hispanic');
INSERT INTO `enumeration` VALUES ('provider_number_type', 'Provider Number Type', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Dependant', 'SSN', 'Home', 'Home', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Hispanic');
INSERT INTO `enumeration` VALUES ('subscriber_to_patient', 'Subscriber to patient', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Dependant', 'SSN', 'Home', 'Home', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Hispanic');
INSERT INTO `enumeration` VALUES ('payer_type', 'Payer Type', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Dependant', 'SSN', 'Home', 'Home', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Hispanic');
INSERT INTO `enumeration` VALUES ('person_to_person_relation_type', 'Person to person relation type', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Dependant', 'SSN', 'Home', 'Home', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Hispanic');
INSERT INTO `enumeration` VALUES ('code_modifier', 'Code Modifier', 'Modifiers available for codes.', 'Male', 'Primary', 'Good', 'New', 'Alabama', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Dependant', 'SSN', 'Home', 'Home', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Hispanic');
INSERT INTO `enumeration` VALUES ('encounter_reason', 'Encounter Reason', 'Reasons for an encounter', 'Male', 'Primary', 'Good', 'New', 'Alabama', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Dependant', 'SSN', 'Home', 'Home', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Hispanic');
INSERT INTO `enumeration` VALUES ('encounter_date_type', 'Encounter Date Type', 'Types for extra dates attached to an encounter', 'Male', 'Primary', 'Good', 'New', 'Alabama', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Dependant', 'SSN', 'Home', 'Home', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Hispanic');
INSERT INTO `enumeration` VALUES ('encounter_person_type', 'Encounter Person Type', '', 'Male', 'Primary', 'Good', 'New', 'Alabama', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Dependant', 'SSN', 'Home', 'Home', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Hispanic');
INSERT INTO `enumeration` VALUES ('payment_type', 'Payment Type', 'Types of payments', 'Male', 'Primary', 'Good', 'New', 'Alabama', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Dependant', 'SSN', 'Home', 'Home', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Hispanic');
INSERT INTO `enumeration` VALUES ('ethnicity', 'Ethnicity', 'Ethnicity of a person, used in Patient Statistics', 'Male', 'Primary', 'Good', 'New', 'Alabama', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Dependant', 'SSN', 'Home', 'Home', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Hispanic');
