-- phpMyAdmin SQL Dump
-- version 2.6.1-rc2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jan 17, 2005 at 04:33 PM
-- Server version: 4.0.22
-- PHP Version: 4.3.9
-- 
-- Database: `freestand`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `address`
-- 

DROP TABLE IF EXISTS `address`;
CREATE TABLE `address` (
  `address_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `line1` varchar(255) NOT NULL default '',
  `line2` varchar(255) NOT NULL default '',
  `city` varchar(255) NOT NULL default '',
  `region` int(11) NOT NULL default '0',
  `county` int(11) NOT NULL default '0',
  `state` int(11) NOT NULL default '0',
  `postal_code` varchar(255) NOT NULL default '',
  `notes` text NOT NULL,
  PRIMARY KEY  (`address_id`)
) TYPE=InnoDB COMMENT='An address that can be for a company or a person';

-- --------------------------------------------------------

-- 
-- Table structure for table `company`
-- 

DROP TABLE IF EXISTS `company`;
CREATE TABLE `company` (
  `company_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `notes` text NOT NULL,
  `initials` varchar(10) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `is_historic` enum('no','yes') NOT NULL default 'no',
  PRIMARY KEY  (`company_id`)
) TYPE=InnoDB COMMENT='Base Company record most of the data is in linked tables';

-- --------------------------------------------------------

-- 
-- Table structure for table `company_address`
-- 

DROP TABLE IF EXISTS `company_address`;
CREATE TABLE `company_address` (
  `company_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  `address_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`,`address_id`),
  KEY `company_id` (`company_id`),
  KEY `address_id` (`address_id`)
) TYPE=InnoDB COMMENT='Links a company to a address specifying the address type';

-- --------------------------------------------------------

-- 
-- Table structure for table `company_company`
-- 

DROP TABLE IF EXISTS `company_company`;
CREATE TABLE `company_company` (
  `company_id` int(11) NOT NULL default '0',
  `related_company_id` int(11) NOT NULL default '0',
  `company_relation_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`,`related_company_id`),
  KEY `company_id` (`company_id`),
  KEY `related_company_id` (`related_company_id`)
) TYPE=InnoDB COMMENT='Relates a company to another company specify the type with a';

-- --------------------------------------------------------

-- 
-- Table structure for table `company_number`
-- 

DROP TABLE IF EXISTS `company_number`;
CREATE TABLE `company_number` (
  `company_id` int(11) NOT NULL default '0',
  `number_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`,`number_id`),
  KEY `company_id` (`company_id`),
  KEY `number_id` (`number_id`)
) TYPE=InnoDB COMMENT='Links between company and phone_numbers';

-- --------------------------------------------------------

-- 
-- Table structure for table `company_type`
-- 

DROP TABLE IF EXISTS `company_type`;
CREATE TABLE `company_type` (
  `company_id` int(11) NOT NULL default '0',
  `company_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`,`company_type`),
  KEY `company_id` (`company_id`),
  KEY `company_type` (`company_type`)
) TYPE=InnoDB COMMENT='Link to specify company type';

-- --------------------------------------------------------

-- 
-- Table structure for table `number`
-- 

DROP TABLE IF EXISTS `number`;
CREATE TABLE `number` (
  `number_id` int(11) NOT NULL default '0',
  `number_type` int(11) NOT NULL default '0',
  `notes` tinytext NOT NULL,
  `number` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`number_id`)
) TYPE=InnoDB COMMENT='A phone number';

-- --------------------------------------------------------

-- 
-- Table structure for table `person`
-- 

DROP TABLE IF EXISTS `person`;
CREATE TABLE `person` (
  `person_id` int(11) NOT NULL default '0',
  `salutation` varchar(20) NOT NULL default '',
  `last_name` varchar(100) NOT NULL default '',
  `first_name` varchar(100) NOT NULL default '',
  `middle_name` varchar(50) NOT NULL default '',
  `gender` int(11) NOT NULL default '0',
  `initials` varchar(10) NOT NULL default '',
  `date_of_birth` date NOT NULL default '0000-00-00',
  `summary` varchar(100) NOT NULL default '',
  `title` varchar(100) NOT NULL default '',
  `notes` text NOT NULL,
  `email` varchar(100) NOT NULL default '',
  `secondary_email` varchar(100) NOT NULL default '',
  `has_photo` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`person_id`)
) TYPE=InnoDB COMMENT='A person in the system';

-- --------------------------------------------------------

-- 
-- Table structure for table `person_address`
-- 

DROP TABLE IF EXISTS `person_address`;
CREATE TABLE `person_address` (
  `person_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  `address_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`,`address_id`),
  KEY `address_id` (`address_id`),
  KEY `person_id` (`person_id`)
) TYPE=InnoDB COMMENT='Links a person to a address specifying the address type';

-- --------------------------------------------------------

-- 
-- Table structure for table `person_number`
-- 

DROP TABLE IF EXISTS `person_number`;
CREATE TABLE `person_number` (
  `person_id` int(11) NOT NULL default '0',
  `number_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`,`number_id`),
  KEY `person_id` (`person_id`),
  KEY `phone_id` (`number_id`)
) TYPE=InnoDB COMMENT='Links between people and phone_numbers';

-- --------------------------------------------------------

-- 
-- Table structure for table `person_person`
-- 

DROP TABLE IF EXISTS `person_person`;
CREATE TABLE `person_person` (
  `person_id` int(11) NOT NULL default '0',
  `related_person_id` int(11) NOT NULL default '0',
  `relation_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`,`related_person_id`,`relation_type`),
  KEY `person_id` (`person_id`),
  KEY `related_person_id` (`related_person_id`)
) TYPE=InnoDB;

-- --------------------------------------------------------

-- 
-- Table structure for table `person_type`
-- 

DROP TABLE IF EXISTS `person_type`;
CREATE TABLE `person_type` (
  `person_id` int(11) NOT NULL default '0',
  `person_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`,`person_type`),
  KEY `person_id` (`person_id`),
  KEY `person_type` (`person_type`)
) TYPE=InnoDB COMMENT='Link to specify person type';

-- 
-- Constraints for dumped tables
-- 

-- 
-- Constraints for table `address`
-- 
ALTER TABLE `address`
  ADD CONSTRAINT `address_ibfk_13` FOREIGN KEY (`address_id`) REFERENCES `ownership` (`id`);

-- 
-- Constraints for table `company_number`
-- 
ALTER TABLE `company_number`
  ADD CONSTRAINT `company_number_ibfk_2` FOREIGN KEY (`number_id`) REFERENCES `number` (`number_id`),
  ADD CONSTRAINT `company_number_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `ownership` (`id`);

-- 
-- Constraints for table `person`
-- 
ALTER TABLE `person`
  ADD CONSTRAINT `person_ibfk_7` FOREIGN KEY (`person_id`) REFERENCES `ownership` (`id`);

-- 
-- Constraints for table `person_address`
-- 
ALTER TABLE `person_address`
  ADD CONSTRAINT `person_address_ibfk_3` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`),
  ADD CONSTRAINT `person_address_ibfk_4` FOREIGN KEY (`address_id`) REFERENCES `address` (`address_id`);

-- 
-- Constraints for table `person_number`
-- 
ALTER TABLE `person_number`
  ADD CONSTRAINT `person_number_ibfk_2` FOREIGN KEY (`number_id`) REFERENCES `number` (`number_id`),
  ADD CONSTRAINT `person_number_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`);

-- 
-- Constraints for table `person_person`
-- 
ALTER TABLE `person_person`
  ADD CONSTRAINT `person_person_ibfk_4` FOREIGN KEY (`related_person_id`) REFERENCES `person` (`person_id`),
  ADD CONSTRAINT `person_person_ibfk_3` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`);

-- 
-- Constraints for table `person_type`
-- 
ALTER TABLE `person_type`
  ADD CONSTRAINT `person_type_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`);
