-- phpMyAdmin SQL Dump
-- version 2.6.1-rc2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Mar 09, 2005 at 01:56 PM
-- Server version: 4.0.23
-- PHP Version: 4.3.10

SET FOREIGN_KEY_CHECKS=0;

SET AUTOCOMMIT=0;
START TRANSACTION;

-- 
-- Database: `clearhealth`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `enumeration`
-- 

DROP TABLE IF EXISTS `enumeration`;
CREATE TABLE `enumeration` (
  `name` varchar(20) NOT NULL default '',
  `title` varchar(100) NOT NULL default '',
  `description` tinytext NOT NULL,
  `gender` enum('Male','Female','Not Specified') NOT NULL default 'Male',
  `company_number_type` enum('Primary','Fax') NOT NULL default 'Primary',
  `address_type` enum('Main','Billing','Shipping') NOT NULL default 'Main',
  `quality_of_file` enum('Good','Bad') NOT NULL default 'Good',
  `disposition` enum('New','Waiting','Compete') NOT NULL default 'New',
  `state` enum('Alaska','Arizona','California') NOT NULL default 'Alaska',
  `group_list` enum('All','Arizona','California') NOT NULL default 'All',
  `identifier_type` enum('SSN') NOT NULL default 'SSN',
  `company_type` enum('Insurance') NOT NULL default 'Insurance',
  `assigning` enum('A - Assigned','B - Assigned Lab Services Only','C - Not Assigned','P - Assignment Refused') NOT NULL default 'A - Assigned',
  `relation_of_information_code` enum('A - On file','I - Informed Consent','M - Limited Ability','N - Not allowed','O - On file','Y - Has permission') NOT NULL default 'A - On file',
  `person_type` enum('Patient','Provider','Mid-level','Staff','Subscriber') NOT NULL default 'Patient',
  `provider_number_type` enum('State License') NOT NULL default 'State License',
  `subscriber_to_patient_relationship` enum('Self','Mother','Father') NOT NULL default 'Self',
  `payer_type` enum('medicare') NOT NULL default 'medicare',
  `number_type` enum('Home','Mobile','Work','Emergency') NOT NULL default 'Home',
  PRIMARY KEY  (`name`)
) TYPE=MyISAM COMMENT='Each enum stored as a new col, metadata in 1 row per enum';

-- 
-- Dumping data for table `enumeration`
-- 

INSERT INTO `enumeration` VALUES ('gender', 'Gender', '', 'Male', 'Primary', 'Main', 'Good', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Home');
INSERT INTO `enumeration` VALUES ('person_type', 'Person Type', '', 'Male', 'Primary', 'Main', 'Good', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Home');
INSERT INTO `enumeration` VALUES ('company_type', 'Company Type', '', 'Male', 'Primary', 'Main', 'Good', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Home');
INSERT INTO `enumeration` VALUES ('state', 'State', '', 'Male', 'Primary', 'Main', 'Good', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Home');
INSERT INTO `enumeration` VALUES ('number_type', 'Phone Number Type', '', 'Male', 'Primary', 'Main', 'Good', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Home');
INSERT INTO `enumeration` VALUES ('company_number_type', 'Company Number Type', '', 'Male', 'Primary', 'Main', 'Good', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Home');
INSERT INTO `enumeration` VALUES ('address_type', 'Address Type', '', 'Male', 'Primary', 'Main', 'Good', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Home');
INSERT INTO `enumeration` VALUES ('disposition', 'Disposition', '', 'Male', 'Primary', 'Main', 'Good', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Home');
INSERT INTO `enumeration` VALUES ('quality_of_file', 'Quality of File', '', 'Male', 'Primary', 'Main', 'Good', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Home');
INSERT INTO `enumeration` VALUES ('group_list', 'File Groups', '', 'Male', 'Primary', 'Main', 'Good', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Home');
INSERT INTO `enumeration` VALUES ('identifier_type', 'Identifier Type', '', 'Male', 'Primary', 'Main', 'Good', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Home');
INSERT INTO `enumeration` VALUES ('assigning', '', '', 'Male', 'Primary', 'Main', 'Good', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Home');
INSERT INTO `enumeration` VALUES ('relation_of_informat', '', '', 'Male', 'Primary', 'Main', 'Good', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Home');
INSERT INTO `enumeration` VALUES ('provider_number_type', '', '', 'Male', 'Primary', 'Main', 'Good', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Home');
INSERT INTO `enumeration` VALUES ('subscriber_to_patien', '', '', 'Male', 'Primary', 'Main', 'Good', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Home');
INSERT INTO `enumeration` VALUES ('payer_type', 'Payer Type', '', 'Male', 'Primary', 'Main', 'Good', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare', 'Home');

SET FOREIGN_KEY_CHECKS=1;

COMMIT;
