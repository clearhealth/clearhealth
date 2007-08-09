-- MySQL dump 10.10
--
-- Host: localhost    Database: clearhealth
-- ------------------------------------------------------
-- Server version	5.0.27-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `DailyNamed`
--

DROP TABLE IF EXISTS `DailyNamed`;
CREATE TABLE `DailyNamed` (
  `id` int(11) NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `account_note`
--

DROP TABLE IF EXISTS `account_note`;
CREATE TABLE `account_note` (
  `account_note_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL default '0',
  `claim_id` varchar(100) NOT NULL default '',
  `user_id` int(11) NOT NULL default '0',
  `date_posted` datetime NOT NULL default '0000-00-00 00:00:00',
  `note` text NOT NULL,
  `note_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`account_note_id`),
  KEY `patient_id` (`patient_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `address`
--

DROP TABLE IF EXISTS `address`;
CREATE TABLE `address` (
  `address_id` int(11) NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='An address that can be for a company or a person. STARTEMPTY';

--
-- Table structure for table `adodbseq`
--

DROP TABLE IF EXISTS `adodbseq`;
CREATE TABLE `adodbseq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='STARTWITHDATA';

--
-- Table structure for table `altnotice`
--

DROP TABLE IF EXISTS `altnotice`;
CREATE TABLE `altnotice` (
  `altnotice_id` int(11) NOT NULL,
  `creation_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `due_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `completed_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` varchar(255) NOT NULL default '',
  `diagnosis` text NOT NULL,
  `note` text NOT NULL,
  `deleted` tinyint(1) NOT NULL default '0',
  `chlfollow_up_reason` int(11) NOT NULL default '0',
  `clinic_id` int(11) NOT NULL default '0',
  `owner_type` varchar(255) NOT NULL default '',
  `owner_id` varchar(255) NOT NULL default '',
  `external_type` varchar(255) NOT NULL default '',
  `external_id` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`altnotice_id`),
  KEY `owner_type` (`owner_type`),
  KEY `owner_id` (`owner_id`),
  KEY `external_type` (`external_type`),
  KEY `external_id` (`external_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `appointment`
--

DROP TABLE IF EXISTS `appointment`;
CREATE TABLE `appointment` (
  `appointment_id` int(11) NOT NULL,
  `arrived` tinyint(1) NOT NULL,
  `title` varchar(255) NOT NULL default '',
  `reason` int(11) NOT NULL default '0',
  `walkin` tinyint(1) NOT NULL default '0',
  `group_appointment` tinyint(1) NOT NULL default '0',
  `has_secondary` tinyint(1) NOT NULL default '0',
  `created_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_change_id` int(11) NOT NULL default '0',
  `last_change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `creator_id` int(11) NOT NULL default '0',
  `event_id` int(11) NOT NULL default '0',
  `practice_id` int(11) NOT NULL default '0',
  `provider_id` int(11) NOT NULL default '0',
  `patient_id` int(11) NOT NULL default '0',
  `room_id` int(11) NOT NULL default '0',
  `appointment_code` varchar(255) NOT NULL default '',
  `event_group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`appointment_id`),
  KEY `event_group_id` (`event_group_id`),
  KEY `event_id` (`event_id`),
  KEY `provider_id` (`provider_id`),
  KEY `patient_id` (`patient_id`),
  KEY `room_id` (`room_id`),
  KEY `arrived` (`arrived`),
  KEY `appointment_code` (`appointment_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `appointment_breakdown`
--

DROP TABLE IF EXISTS `appointment_breakdown`;
CREATE TABLE `appointment_breakdown` (
  `appointment_breakdown_id` int(11) NOT NULL default '0',
  `appointment_id` int(11) NOT NULL default '0',
  `occurence_breakdown_id` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`appointment_breakdown_id`),
  KEY `occurence_breakdown_id` (`occurence_breakdown_id`,`person_id`),
  KEY `appointment_id` (`appointment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `appointment_link`
--

DROP TABLE IF EXISTS `appointment_link`;
CREATE TABLE `appointment_link` (
  `oldId` int(11) NOT NULL,
  `newId` int(11) NOT NULL,
  PRIMARY KEY  (`oldId`,`newId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `appointment_rule`
--

DROP TABLE IF EXISTS `appointment_rule`;
CREATE TABLE `appointment_rule` (
  `appointment_rule_id` int(11) NOT NULL default '0',
  `appointment_ruleset_id` int(11) NOT NULL default '0',
  `type` varchar(20) NOT NULL default '',
  `label` varchar(255) NOT NULL default '',
  `data` longtext NOT NULL,
  PRIMARY KEY  (`appointment_rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `appointment_ruleset`
--

DROP TABLE IF EXISTS `appointment_ruleset`;
CREATE TABLE `appointment_ruleset` (
  `appointment_ruleset_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `error_message` text NOT NULL,
  `provider_id` int(11) NOT NULL default '0',
  `procedure_id` int(11) NOT NULL default '0',
  `room_id` int(11) NOT NULL default '0',
  `enabled` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`appointment_ruleset_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `appointment_template`
--

DROP TABLE IF EXISTS `appointment_template`;
CREATE TABLE `appointment_template` (
  `appointment_template_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`appointment_template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `audit_log`
--

DROP TABLE IF EXISTS `audit_log`;
CREATE TABLE `audit_log` (
  `audit_log_id` int(11) NOT NULL default '0',
  `ordo` varchar(255) NOT NULL default '',
  `ordo_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `type` int(11) NOT NULL default '0',
  `message` text NOT NULL,
  `log_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`audit_log_id`),
  KEY `ordo` (`ordo`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `audit_log_field`
--

DROP TABLE IF EXISTS `audit_log_field`;
CREATE TABLE `audit_log_field` (
  `audit_log_field_id` int(11) NOT NULL default '0',
  `audit_log_id` int(11) NOT NULL default '0',
  `field` varchar(255) NOT NULL default '',
  `old_value` text NOT NULL,
  `new_value` text NOT NULL,
  PRIMARY KEY  (`audit_log_field_id`),
  UNIQUE KEY `audit_log_id` (`audit_log_id`,`field`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `building_address`
--

DROP TABLE IF EXISTS `building_address`;
CREATE TABLE `building_address` (
  `building_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  `address_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`building_id`,`address_id`),
  KEY `address_id` (`address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Links a building to a address specifying type. STARTEMPTY';

--
-- Table structure for table `building_link`
--

DROP TABLE IF EXISTS `building_link`;
CREATE TABLE `building_link` (
  `oldId` int(11) NOT NULL,
  `newId` int(11) NOT NULL,
  PRIMARY KEY  (`oldId`,`newId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `building_program_identifier`
--

DROP TABLE IF EXISTS `building_program_identifier`;
CREATE TABLE `building_program_identifier` (
  `building_id` int(11) NOT NULL default '0',
  `program_id` int(11) NOT NULL default '0',
  `identifier` varchar(255) NOT NULL default '',
  `x12_sender_id` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`building_id`,`program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `buildings`
--

DROP TABLE IF EXISTS `buildings`;
CREATE TABLE `buildings` (
  `id` int(11) NOT NULL,
  `description` text NOT NULL,
  `name` varchar(255) NOT NULL default '',
  `practice_id` int(11) NOT NULL default '0',
  `identifier` varchar(255) NOT NULL default '',
  `facility_code_id` int(11) NOT NULL default '0',
  `phone_number` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `practice_id` (`practice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='STARTEMPTY';

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  `parent` int(11) NOT NULL default '0',
  `lft` int(11) NOT NULL default '0',
  `rght` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `parent` (`parent`),
  KEY `lft` (`lft`,`rght`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='STARTWITHDATA';

--
-- Table structure for table `category_to_document`
--

DROP TABLE IF EXISTS `category_to_document`;
CREATE TABLE `category_to_document` (
  `category_id` int(11) NOT NULL default '0',
  `document_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`category_id`,`document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='STARTEMPTY';

--
-- Table structure for table `clearhealth_claim`
--

DROP TABLE IF EXISTS `clearhealth_claim`;
CREATE TABLE `clearhealth_claim` (
  `claim_id` int(11) NOT NULL,
  `encounter_id` int(11) NOT NULL default '0',
  `identifier` varchar(255) NOT NULL default '',
  `total_billed` float(7,2) NOT NULL default '0.00',
  `total_paid` float(7,2) NOT NULL default '0.00',
  PRIMARY KEY  (`claim_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='STARTEMPTY';

--
-- Table structure for table `code_category`
--

DROP TABLE IF EXISTS `code_category`;
CREATE TABLE `code_category` (
  `code_category_id` int(11) NOT NULL default '0',
  `category_name` varchar(255) NOT NULL default '',
  `category_id` int(11) NOT NULL,
  PRIMARY KEY  (`code_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `code_to_category`
--

DROP TABLE IF EXISTS `code_to_category`;
CREATE TABLE `code_to_category` (
  `code_category_id` int(11) NOT NULL default '0',
  `code_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`code_category_id`,`code_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `codes`
--

DROP TABLE IF EXISTS `codes`;
CREATE TABLE `codes` (
  `code_id` int(11) NOT NULL,
  `code_text` varchar(255) default NULL,
  `code_text_short` varchar(24) default NULL,
  `code` varchar(10) default NULL,
  `code_type` tinyint(2) default NULL,
  `modifier` varchar(5) default NULL,
  `units` tinyint(3) default NULL,
  `fee` decimal(7,2) default NULL,
  `superbill` tinyint(1) NOT NULL default '0',
  `rvu` float NOT NULL,
  PRIMARY KEY  (`code_id`),
  KEY `code_text` (`code_text`),
  KEY `code` (`code`),
  KEY `code_type` (`code_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `coding_data`
--

DROP TABLE IF EXISTS `coding_data`;
CREATE TABLE `coding_data` (
  `coding_data_id` int(11) NOT NULL,
  `foreign_id` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `code_id` int(11) NOT NULL default '0',
  `modifier` int(11) NOT NULL default '0',
  `units` float(5,2) NOT NULL default '1.00',
  `fee` float(11,2) NOT NULL default '0.00',
  `primary_code` tinyint(4) NOT NULL default '0',
  `code_order` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`coding_data_id`),
  KEY `foreign_id` (`foreign_id`),
  KEY `parent_id` (`parent_id`),
  KEY `code_id` (`code_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `coding_data_dental`
--

DROP TABLE IF EXISTS `coding_data_dental`;
CREATE TABLE `coding_data_dental` (
  `coding_data_id` int(11) NOT NULL default '0',
  `tooth` enum('N/A','All','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','All (Primary)','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T') NOT NULL default 'N/A',
  `toothside` enum('N/A','Front','Back','Top','Left','Right') NOT NULL default 'N/A',
  PRIMARY KEY  (`coding_data_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `coding_template`
--

DROP TABLE IF EXISTS `coding_template`;
CREATE TABLE `coding_template` (
  `coding_template_id` int(11) NOT NULL default '0',
  `practice_id` int(11) NOT NULL default '0',
  `reason_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL default '',
  `coding_parent_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`coding_template_id`),
  KEY `practice_id` (`practice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `company`
--

DROP TABLE IF EXISTS `company`;
CREATE TABLE `company` (
  `company_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `notes` text NOT NULL,
  `initials` varchar(10) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `is_historic` enum('no','yes') NOT NULL default 'no',
  PRIMARY KEY  (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Base Company record most of the data is linked in STARTEMPTY';

--
-- Table structure for table `company_address`
--

DROP TABLE IF EXISTS `company_address`;
CREATE TABLE `company_address` (
  `company_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  `address_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`,`address_id`),
  KEY `address_id` (`address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Links a company to a address specifying the type STARTEMPTY';

--
-- Table structure for table `company_company`
--

DROP TABLE IF EXISTS `company_company`;
CREATE TABLE `company_company` (
  `company_id` int(11) NOT NULL default '0',
  `related_company_id` int(11) NOT NULL default '0',
  `company_relation_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`,`related_company_id`),
  KEY `related_company_id` (`related_company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Relates a company to another company STARTEMPTY';

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Links between company and phone_numbers STARTEMPTY';

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Link to specify company type';

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
CREATE TABLE `countries` (
  `countries_name` varchar(64) NOT NULL default '',
  `countries_iso_code_2` char(2) NOT NULL default '',
  `countries_iso_code_3` char(3) NOT NULL default '',
  PRIMARY KEY  (`countries_iso_code_3`),
  KEY `IDX_COUNTRIES_NAME` (`countries_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `cronable`
--

DROP TABLE IF EXISTS `cronable`;
CREATE TABLE `cronable` (
  `cronable_id` int(11) NOT NULL default '0',
  `label` varchar(255) NOT NULL default '',
  `minute` varchar(8) NOT NULL default '0',
  `hour` varchar(8) NOT NULL default '0',
  `day_of_month` varchar(8) NOT NULL default '0',
  `month` varchar(8) NOT NULL default '0',
  `day_of_week` varchar(8) NOT NULL default '0',
  `year` varchar(8) NOT NULL default '0',
  `at_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `controller` varchar(255) NOT NULL default '',
  `action` varchar(255) NOT NULL default '',
  `arguments` text NOT NULL,
  `last_run` datetime default '0000-00-00 00:00:00',
  PRIMARY KEY  (`cronable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `document`
--

DROP TABLE IF EXISTS `document`;
CREATE TABLE `document` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL default '',
  `type` enum('file_url','blob','web_url') default NULL,
  `size` int(11) default NULL,
  `date` datetime default NULL,
  `url` varchar(255) default NULL,
  `mimetype` varchar(255) default NULL,
  `pages` int(11) default NULL,
  `owner` int(11) default NULL,
  `revision` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `foreign_id` int(11) default NULL,
  `group_id` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `revision` (`revision`),
  KEY `foreign_id` (`foreign_id`),
  KEY `owner` (`owner`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `duplicate_queue`
--

DROP TABLE IF EXISTS `duplicate_queue`;
CREATE TABLE `duplicate_queue` (
  `duplicate_queue_id` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `child_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`duplicate_queue_id`),
  UNIQUE KEY `child_id` (`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `eligibility_log`
--

DROP TABLE IF EXISTS `eligibility_log`;
CREATE TABLE `eligibility_log` (
  `eligibility_log_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL default '0',
  `log_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `message` longtext NOT NULL,
  PRIMARY KEY  (`eligibility_log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `encounter`
--

DROP TABLE IF EXISTS `encounter`;
CREATE TABLE `encounter` (
  `encounter_id` int(11) NOT NULL default '0',
  `encounter_reason` int(11) NOT NULL default '0',
  `patient_id` int(11) NOT NULL default '0',
  `building_id` int(11) NOT NULL default '0',
  `date_of_treatment` datetime NOT NULL default '0000-00-00 00:00:00',
  `treating_person_id` int(11) NOT NULL default '0',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `last_change_user_id` int(11) NOT NULL default '0',
  `status` enum('closed','open','billed') NOT NULL default 'open',
  `occurence_id` int(11) default NULL,
  `created_by_user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`encounter_id`),
  KEY `building_id` (`building_id`),
  KEY `treating_person_id` (`treating_person_id`),
  KEY `last_change_user_id` (`last_change_user_id`),
  KEY `patient_id` (`patient_id`),
  KEY `occurence_id` (`occurence_id`),
  KEY `date_of_treatment` (`date_of_treatment`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `encounter_date`
--

DROP TABLE IF EXISTS `encounter_date`;
CREATE TABLE `encounter_date` (
  `encounter_date_id` int(11) NOT NULL,
  `encounter_id` int(11) NOT NULL default '0',
  `date_type` int(11) NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`encounter_date_id`),
  KEY `encounter_id` (`encounter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `encounter_link`
--

DROP TABLE IF EXISTS `encounter_link`;
CREATE TABLE `encounter_link` (
  `oldId` int(11) NOT NULL,
  `newId` int(11) NOT NULL,
  PRIMARY KEY  (`oldId`,`newId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `encounter_person`
--

DROP TABLE IF EXISTS `encounter_person`;
CREATE TABLE `encounter_person` (
  `encounter_person_id` int(11) NOT NULL,
  `encounter_id` int(11) NOT NULL default '0',
  `person_type` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`encounter_person_id`),
  KEY `encounter_id` (`encounter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `encounter_value`
--

DROP TABLE IF EXISTS `encounter_value`;
CREATE TABLE `encounter_value` (
  `encounter_value_id` int(11) NOT NULL,
  `encounter_id` int(11) NOT NULL default '0',
  `value_type` int(11) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '0',
  PRIMARY KEY  (`encounter_value_id`),
  KEY `encounter_id` (`encounter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `enumeration_definition`
--

DROP TABLE IF EXISTS `enumeration_definition`;
CREATE TABLE `enumeration_definition` (
  `enumeration_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `type` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`enumeration_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `enumeration_value`
--

DROP TABLE IF EXISTS `enumeration_value`;
CREATE TABLE `enumeration_value` (
  `enumeration_value_id` int(11) NOT NULL default '0',
  `enumeration_id` int(11) NOT NULL default '0',
  `key` varchar(255) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  `sort` int(11) NOT NULL default '0',
  `extra1` varchar(255) NOT NULL default '',
  `extra2` varchar(255) NOT NULL default '',
  `status` int(1) NOT NULL default '1',
  `depth` tinyint(4) NOT NULL,
  `parent_id` int(11) NOT NULL,
  PRIMARY KEY  (`enumeration_value_id`),
  KEY `key` (`key`),
  KEY `enumeration_id` (`enumeration_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `enumeration_value_practice`
--

DROP TABLE IF EXISTS `enumeration_value_practice`;
CREATE TABLE `enumeration_value_practice` (
  `enumeration_value_id` int(11) NOT NULL default '0',
  `practice_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`enumeration_value_id`,`practice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `eob_adjustment`
--

DROP TABLE IF EXISTS `eob_adjustment`;
CREATE TABLE `eob_adjustment` (
  `eob_adjustment_id` int(11) NOT NULL default '0',
  `payment_id` int(11) NOT NULL default '0',
  `payment_claimline_id` int(11) NOT NULL default '0',
  `adjustment_type` int(11) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`eob_adjustment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
CREATE TABLE `event` (
  `event_id` int(11) NOT NULL,
  `start` datetime NOT NULL default '0000-00-00 00:00:00',
  `end` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`event_id`),
  KEY `start` (`start`),
  KEY `end` (`end`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `event_group`
--

DROP TABLE IF EXISTS `event_group`;
CREATE TABLE `event_group` (
  `event_group_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL default '',
  `room_id` int(11) NOT NULL default '0',
  `schedule_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`event_group_id`),
  KEY `room_id` (`room_id`),
  KEY `schedule_id` (`schedule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `facility_codes`
--

DROP TABLE IF EXISTS `facility_codes`;
CREATE TABLE `facility_codes` (
  `facility_code_id` int(11) NOT NULL,
  `code` varchar(5) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`facility_code_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Stores x12 facility_code code/human name combos';

--
-- Table structure for table `fbaddress`
--

DROP TABLE IF EXISTS `fbaddress`;
CREATE TABLE `fbaddress` (
  `address_id` int(11) NOT NULL default '0',
  `external_id` int(11) NOT NULL default '0',
  `type` enum('default') NOT NULL default 'default',
  `name` varchar(100) NOT NULL default '',
  `line1` varchar(255) NOT NULL default '',
  `line2` varchar(255) NOT NULL default '',
  `city` varchar(255) NOT NULL default '',
  `state` varchar(5) NOT NULL default '0',
  `zip` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='An address that can be for a company or a person';

--
-- Table structure for table `fbclaim`
--

DROP TABLE IF EXISTS `fbclaim`;
CREATE TABLE `fbclaim` (
  `claim_id` int(11) NOT NULL default '0',
  `claim_identifier` varchar(255) NOT NULL default '' COMMENT '\0\0\0\0\0\0\0\0\0\0\0!\0\0?',
  `revision` int(11) NOT NULL default '0',
  `status` enum('new','pending','sent','archive','deleted') NOT NULL default 'new',
  `timestamp` timestamp NULL default '0000-00-00 00:00:00',
  `date_sent` datetime NOT NULL default '0000-00-00 00:00:00',
  `format` varchar(255) NOT NULL default '' COMMENT '\0\0\0\0\0\0\0\0\0\0\0!\0\0?',
  PRIMARY KEY  (`claim_id`),
  KEY `claim_identifier` (`claim_identifier`),
  KEY `status` (`status`),
  KEY `revision` (`revision`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `fbclaimline`
--

DROP TABLE IF EXISTS `fbclaimline`;
CREATE TABLE `fbclaimline` (
  `claimline_id` int(11) NOT NULL default '0',
  `claim_id` int(11) NOT NULL default '0',
  `procedure` varchar(10) NOT NULL default '',
  `modifier` varchar(4) NOT NULL default '',
  `amount` float(11,2) NOT NULL default '0.00',
  `units` float(5,2) NOT NULL default '0.00',
  `comment` varchar(80) NOT NULL default '',
  `comment_type` varchar(10) NOT NULL default '',
  `date_of_treatment` datetime NOT NULL default '0000-00-00 00:00:00',
  `amount_paid` float(11,2) NOT NULL default '0.00',
  `index` int(11) NOT NULL default '0',
  PRIMARY KEY  (`claimline_id`),
  KEY `claim_id` (`claim_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `fbcompany`
--

DROP TABLE IF EXISTS `fbcompany`;
CREATE TABLE `fbcompany` (
  `company_id` int(11) NOT NULL default '0',
  `claim_id` int(11) NOT NULL default '0',
  `index` tinyint(4) NOT NULL default '0',
  `identifier` varchar(25) NOT NULL default '',
  `identifier_type` varchar(10) NOT NULL default '',
  `type` varchar(50) NOT NULL default '',
  `name` varchar(100) NOT NULL default '',
  `phone_number` varchar(45) NOT NULL default '',
  PRIMARY KEY  (`company_id`),
  KEY `claim_id` (`claim_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Base Company record most of the data is in linked tables';

--
-- Table structure for table `fbdiagnoses`
--

DROP TABLE IF EXISTS `fbdiagnoses`;
CREATE TABLE `fbdiagnoses` (
  `id` int(11) NOT NULL default '0',
  `claimline_id` int(11) NOT NULL default '0',
  `diagnosis` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `claimline_id` (`claimline_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `fblatest_revision`
--

DROP TABLE IF EXISTS `fblatest_revision`;
CREATE TABLE `fblatest_revision` (
  `claim_identifier` varchar(255) NOT NULL default '',
  `revision` int(11) NOT NULL default '0',
  PRIMARY KEY  (`claim_identifier`),
  KEY `revision` (`revision`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `fbperson`
--

DROP TABLE IF EXISTS `fbperson`;
CREATE TABLE `fbperson` (
  `person_id` int(11) NOT NULL default '0',
  `claim_id` int(11) NOT NULL default '0',
  `index` tinyint(4) NOT NULL default '0',
  `type` varchar(50) NOT NULL default '',
  `identifier` varchar(100) NOT NULL default '',
  `identifier_type` varchar(10) NOT NULL default '',
  `record_number` varchar(255) NOT NULL default '',
  `salutation` varchar(20) NOT NULL default '',
  `last_name` varchar(100) NOT NULL default '',
  `first_name` varchar(100) NOT NULL default '',
  `middle_name` varchar(50) NOT NULL default '',
  `gender` enum('M','F','O') default NULL,
  `date_of_birth` date NOT NULL default '0000-00-00',
  `phone_number` varchar(45) NOT NULL default '',
  `comment` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`person_id`),
  KEY `claim_id` (`claim_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='A person in the system';

--
-- Table structure for table `fbpractice`
--

DROP TABLE IF EXISTS `fbpractice`;
CREATE TABLE `fbpractice` (
  `practice_id` int(11) NOT NULL default '0',
  `claim_id` int(11) NOT NULL default '0',
  `billing_contact_person_id` int(11) NOT NULL default '0',
  `treating_location_company_company_id` int(11) NOT NULL default '0',
  `billing_location_company_id` int(11) NOT NULL default '0',
  `provider_person_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`practice_id`),
  KEY `claim_id` (`claim_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `fbqueue`
--

DROP TABLE IF EXISTS `fbqueue`;
CREATE TABLE `fbqueue` (
  `queue_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `max_items` int(11) NOT NULL default '0',
  `num_items` int(11) NOT NULL default '0',
  `ids` mediumtext NOT NULL,
  PRIMARY KEY  (`queue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `fee_schedule`
--

DROP TABLE IF EXISTS `fee_schedule`;
CREATE TABLE `fee_schedule` (
  `fee_schedule_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `label` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `priority` int(11) NOT NULL default '2',
  PRIMARY KEY  (`fee_schedule_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `fee_schedule_data`
--

DROP TABLE IF EXISTS `fee_schedule_data`;
CREATE TABLE `fee_schedule_data` (
  `code_id` int(11) NOT NULL default '0',
  `revision_id` int(11) NOT NULL default '0',
  `fee_schedule_id` int(11) NOT NULL default '0',
  `data` float(11,2) NOT NULL default '0.00',
  `formula` varchar(255) NOT NULL default '',
  `mapped_code` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`code_id`,`revision_id`,`fee_schedule_id`),
  KEY `fee_schedule_id` (`fee_schedule_id`),
  KEY `revision_id` (`revision_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `fee_schedule_data_modifier`
--

DROP TABLE IF EXISTS `fee_schedule_data_modifier`;
CREATE TABLE `fee_schedule_data_modifier` (
  `fsd_modifier_id` int(11) NOT NULL default '0',
  `fee_schedule_id` int(11) NOT NULL default '0',
  `code_id` int(11) NOT NULL default '0',
  `modifier` int(11) NOT NULL default '0',
  `fee` float(7,2) NOT NULL default '0.00',
  PRIMARY KEY  (`fsd_modifier_id`),
  UNIQUE KEY `fee_schedule_id` (`fee_schedule_id`,`code_id`,`modifier`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `fee_schedule_discount`
--

DROP TABLE IF EXISTS `fee_schedule_discount`;
CREATE TABLE `fee_schedule_discount` (
  `fee_schedule_discount_id` int(11) NOT NULL default '0',
  `practice_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `insurance_program_id` int(11) NOT NULL default '0',
  `type` enum('default','program') NOT NULL default 'default',
  PRIMARY KEY  (`fee_schedule_discount_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `fee_schedule_discount_by_code`
--

DROP TABLE IF EXISTS `fee_schedule_discount_by_code`;
CREATE TABLE `fee_schedule_discount_by_code` (
  `fee_schedule_discount_by_code_id` int(11) NOT NULL default '0',
  `fee_schedule_discount_id` int(11) NOT NULL default '0',
  `fee_schedule_discount_level_id` int(11) NOT NULL default '0',
  `code_pattern` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`fee_schedule_discount_by_code_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `fee_schedule_discount_income`
--

DROP TABLE IF EXISTS `fee_schedule_discount_income`;
CREATE TABLE `fee_schedule_discount_income` (
  `fee_schedule_discount_income_id` int(11) NOT NULL default '0',
  `fee_schedule_discount_id` int(11) NOT NULL default '0',
  `fee_schedule_discount_level_id` int(11) NOT NULL default '0',
  `family_size` int(11) NOT NULL default '0',
  `income` float(9,2) NOT NULL default '0.00',
  PRIMARY KEY  (`fee_schedule_discount_income_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `fee_schedule_discount_level`
--

DROP TABLE IF EXISTS `fee_schedule_discount_level`;
CREATE TABLE `fee_schedule_discount_level` (
  `fee_schedule_discount_level_id` int(11) NOT NULL default '0',
  `fee_schedule_discount_id` int(11) NOT NULL default '0',
  `discount` float(5,2) NOT NULL default '0.00',
  `disp_order` int(11) NOT NULL default '0',
  `type` enum('percent','flat') NOT NULL default 'percent',
  PRIMARY KEY  (`fee_schedule_discount_level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `fee_schedule_revision`
--

DROP TABLE IF EXISTS `fee_schedule_revision`;
CREATE TABLE `fee_schedule_revision` (
  `revision_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `update_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`revision_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `financial_link`
--

DROP TABLE IF EXISTS `financial_link`;
CREATE TABLE `financial_link` (
  `oldId` int(11) NOT NULL,
  `newPaymentId` int(11) NOT NULL,
  `newChargeId` int(11) NOT NULL,
  PRIMARY KEY  (`oldId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `folders`
--

DROP TABLE IF EXISTS `folders`;
CREATE TABLE `folders` (
  `folder_id` int(10) unsigned NOT NULL,
  `label` varchar(50) NOT NULL default '',
  `create_date` datetime default '0000-00-00 00:00:00',
  `modify_date` datetime default '0000-00-00 00:00:00',
  `webdavname` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`folder_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `form`
--

DROP TABLE IF EXISTS `form`;
CREATE TABLE `form` (
  `form_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `system_name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`form_id`),
  UNIQUE KEY `system_name` (`system_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Contains the EMR extending forms STARTWITHDATA';

--
-- Table structure for table `form_data`
--

DROP TABLE IF EXISTS `form_data`;
CREATE TABLE `form_data` (
  `form_data_id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL default '0',
  `external_id` int(11) NOT NULL default '0',
  `encounter_id` int(11) NOT NULL,
  `last_edit` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`form_data_id`),
  KEY `form_id` (`form_id`),
  KEY `external_id` (`external_id`),
  KEY `encounter_id` (`encounter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Links in the form data STARTWITHDATA';

--
-- Table structure for table `form_rule`
--

DROP TABLE IF EXISTS `form_rule`;
CREATE TABLE `form_rule` (
  `form_rule_id` int(11) NOT NULL,
  `field_name` varchar(100) NOT NULL default '',
  `rule_name` varchar(30) NOT NULL default '',
  `operator` char(3) NOT NULL default '',
  `value_type` int(1) NOT NULL default '1',
  `value` varchar(30) NOT NULL default '',
  `message` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`form_rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `form_structure`
--

DROP TABLE IF EXISTS `form_structure`;
CREATE TABLE `form_structure` (
  `form_structure_id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL default '0',
  `field_name` varchar(100) NOT NULL default '',
  `field_type` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`form_structure_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `gacl_acl`
--

DROP TABLE IF EXISTS `gacl_acl`;
CREATE TABLE `gacl_acl` (
  `id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default 'system',
  `allow` int(11) NOT NULL default '0',
  `enabled` int(11) NOT NULL default '0',
  `return_value` longtext,
  `note` longtext,
  `updated_date` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `gacl_enabled_acl` (`enabled`),
  KEY `gacl_section_value_acl` (`section_value`),
  KEY `gacl_updated_date_acl` (`updated_date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='ACL Table';

--
-- Table structure for table `gacl_acl_sections`
--

DROP TABLE IF EXISTS `gacl_acl_sections`;
CREATE TABLE `gacl_acl_sections` (
  `id` int(11) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(230) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_value_acl_sections` (`value`),
  KEY `gacl_hidden_acl_sections` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `gacl_acl_seq`
--

DROP TABLE IF EXISTS `gacl_acl_seq`;
CREATE TABLE `gacl_acl_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `gacl_aco`
--

DROP TABLE IF EXISTS `gacl_aco`;
CREATE TABLE `gacl_aco` (
  `id` int(11) NOT NULL default '0',
  `section_value` varchar(240) NOT NULL default '0',
  `value` varchar(240) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_section_value_value_aco` (`section_value`,`value`),
  KEY `gacl_hidden_aco` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `gacl_aco_map`
--

DROP TABLE IF EXISTS `gacl_aco_map`;
CREATE TABLE `gacl_aco_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `gacl_aco_sections`
--

DROP TABLE IF EXISTS `gacl_aco_sections`;
CREATE TABLE `gacl_aco_sections` (
  `id` int(11) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(230) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_value_aco_sections` (`value`),
  KEY `gacl_hidden_aco_sections` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `gacl_aco_sections_seq`
--

DROP TABLE IF EXISTS `gacl_aco_sections_seq`;
CREATE TABLE `gacl_aco_sections_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `gacl_aco_seq`
--

DROP TABLE IF EXISTS `gacl_aco_seq`;
CREATE TABLE `gacl_aco_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `gacl_aro`
--

DROP TABLE IF EXISTS `gacl_aro`;
CREATE TABLE `gacl_aro` (
  `id` int(11) NOT NULL,
  `section_value` varchar(240) NOT NULL default '0',
  `value` varchar(240) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_section_value_value_aro` (`section_value`,`value`),
  KEY `gacl_hidden_aro` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `gacl_aro_groups`
--

DROP TABLE IF EXISTS `gacl_aro_groups`;
CREATE TABLE `gacl_aro_groups` (
  `id` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `lft` int(11) NOT NULL default '0',
  `rgt` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`,`value`),
  UNIQUE KEY `gacl_value_aro_groups` (`value`),
  KEY `gacl_parent_id_aro_groups` (`parent_id`),
  KEY `gacl_lft_rgt_aro_groups` (`lft`,`rgt`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `gacl_aro_groups_id_seq`
--

DROP TABLE IF EXISTS `gacl_aro_groups_id_seq`;
CREATE TABLE `gacl_aro_groups_id_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `gacl_aro_groups_map`
--

DROP TABLE IF EXISTS `gacl_aro_groups_map`;
CREATE TABLE `gacl_aro_groups_map` (
  `acl_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acl_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `gacl_aro_map`
--

DROP TABLE IF EXISTS `gacl_aro_map`;
CREATE TABLE `gacl_aro_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `gacl_aro_sections`
--

DROP TABLE IF EXISTS `gacl_aro_sections`;
CREATE TABLE `gacl_aro_sections` (
  `id` int(11) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(230) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_value_aro_sections` (`value`),
  KEY `gacl_hidden_aro_sections` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `gacl_aro_sections_seq`
--

DROP TABLE IF EXISTS `gacl_aro_sections_seq`;
CREATE TABLE `gacl_aro_sections_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `gacl_aro_seq`
--

DROP TABLE IF EXISTS `gacl_aro_seq`;
CREATE TABLE `gacl_aro_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `gacl_axo`
--

DROP TABLE IF EXISTS `gacl_axo`;
CREATE TABLE `gacl_axo` (
  `id` int(11) NOT NULL default '0',
  `section_value` varchar(240) NOT NULL default '0',
  `value` varchar(240) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_section_value_value_axo` (`section_value`,`value`),
  KEY `gacl_hidden_axo` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `gacl_axo_groups`
--

DROP TABLE IF EXISTS `gacl_axo_groups`;
CREATE TABLE `gacl_axo_groups` (
  `id` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `lft` int(11) NOT NULL default '0',
  `rgt` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`,`value`),
  UNIQUE KEY `gacl_value_axo_groups` (`value`),
  KEY `gacl_parent_id_axo_groups` (`parent_id`),
  KEY `gacl_lft_rgt_axo_groups` (`lft`,`rgt`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `gacl_axo_groups_id_seq`
--

DROP TABLE IF EXISTS `gacl_axo_groups_id_seq`;
CREATE TABLE `gacl_axo_groups_id_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `gacl_axo_groups_map`
--

DROP TABLE IF EXISTS `gacl_axo_groups_map`;
CREATE TABLE `gacl_axo_groups_map` (
  `acl_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acl_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `gacl_axo_map`
--

DROP TABLE IF EXISTS `gacl_axo_map`;
CREATE TABLE `gacl_axo_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `gacl_axo_sections`
--

DROP TABLE IF EXISTS `gacl_axo_sections`;
CREATE TABLE `gacl_axo_sections` (
  `id` int(11) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(230) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_value_axo_sections` (`value`),
  KEY `gacl_hidden_axo_sections` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `gacl_axo_sections_seq`
--

DROP TABLE IF EXISTS `gacl_axo_sections_seq`;
CREATE TABLE `gacl_axo_sections_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `gacl_axo_seq`
--

DROP TABLE IF EXISTS `gacl_axo_seq`;
CREATE TABLE `gacl_axo_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `gacl_groups_aro_map`
--

DROP TABLE IF EXISTS `gacl_groups_aro_map`;
CREATE TABLE `gacl_groups_aro_map` (
  `group_id` int(11) NOT NULL default '0',
  `aro_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`aro_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `gacl_groups_axo_map`
--

DROP TABLE IF EXISTS `gacl_groups_axo_map`;
CREATE TABLE `gacl_groups_axo_map` (
  `group_id` int(11) NOT NULL default '0',
  `axo_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`axo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `gacl_phpgacl`
--

DROP TABLE IF EXISTS `gacl_phpgacl`;
CREATE TABLE `gacl_phpgacl` (
  `name` varchar(230) NOT NULL default '',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `generic_notes`
--

DROP TABLE IF EXISTS `generic_notes`;
CREATE TABLE `generic_notes` (
  `generic_note_id` bigint(20) NOT NULL,
  `parent_obj_id` bigint(20) NOT NULL,
  `created` date NOT NULL,
  `person_id` bigint(20) NOT NULL,
  `note` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
  `deprecated` tinyint(4) NOT NULL,
  PRIMARY KEY  (`generic_note_id`),
  KEY `parent_obj_id` (`parent_obj_id`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `group_occurence`
--

DROP TABLE IF EXISTS `group_occurence`;
CREATE TABLE `group_occurence` (
  `group_occurence_id` int(11) NOT NULL default '0',
  `occurence_id` int(11) NOT NULL default '0',
  `patient_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_occurence_id`),
  UNIQUE KEY `occurence_id` (`occurence_id`,`patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `hl7_message`
--

DROP TABLE IF EXISTS `hl7_message`;
CREATE TABLE `hl7_message` (
  `id` int(11) NOT NULL default '0',
  `control_id` varchar(50) NOT NULL default '',
  `message` longtext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `control_id` (`control_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `identifier`
--

DROP TABLE IF EXISTS `identifier`;
CREATE TABLE `identifier` (
  `identifier_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL default '0',
  `identifier` varchar(100) NOT NULL default '',
  `identifier_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`identifier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `import_map`
--

DROP TABLE IF EXISTS `import_map`;
CREATE TABLE `import_map` (
  `old_id` int(11) NOT NULL default '0',
  `new_id` int(11) default NULL,
  `old_table_name` varchar(100) NOT NULL default '',
  `new_object_name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`old_id`,`old_table_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `ins_link`
--

DROP TABLE IF EXISTS `ins_link`;
CREATE TABLE `ins_link` (
  `oldId` varchar(50) NOT NULL,
  `newId` int(11) NOT NULL,
  PRIMARY KEY  (`oldId`,`newId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `insurance`
--

DROP TABLE IF EXISTS `insurance`;
CREATE TABLE `insurance` (
  `company_id` int(11) NOT NULL default '0',
  `fee_schedule_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`),
  KEY `fee_schedule_id` (`fee_schedule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `insurance_payergroup`
--

DROP TABLE IF EXISTS `insurance_payergroup`;
CREATE TABLE `insurance_payergroup` (
  `payer_group_id` int(11) NOT NULL default '0',
  `insurance_program_id` int(11) NOT NULL default '0',
  `order` int(11) NOT NULL default '0',
  KEY `payer_group_id` (`payer_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `insurance_program`
--

DROP TABLE IF EXISTS `insurance_program`;
CREATE TABLE `insurance_program` (
  `insurance_program_id` int(11) NOT NULL,
  `payer_type` int(11) NOT NULL default '0',
  `company_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `fee_schedule_id` int(11) NOT NULL default '0',
  `x12_sender_id` varchar(255) NOT NULL default '',
  `x12_receiver_id` varchar(255) NOT NULL default '',
  `x12_version` varchar(255) NOT NULL default '',
  `address_id` int(11) NOT NULL default '0',
  `funds_source` int(11) NOT NULL default '0',
  `program_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`insurance_program_id`),
  KEY `fee_schedule_id` (`fee_schedule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `insured_relationship`
--

DROP TABLE IF EXISTS `insured_relationship`;
CREATE TABLE `insured_relationship` (
  `insured_relationship_id` int(11) NOT NULL,
  `insurance_program_id` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  `subscriber_id` int(11) NOT NULL default '0',
  `subscriber_to_patient_relationship` int(11) NOT NULL default '0',
  `copay` float(11,2) NOT NULL default '0.00',
  `assigning` int(11) NOT NULL default '0',
  `group_name` varchar(100) NOT NULL default '',
  `group_number` varchar(100) NOT NULL default '',
  `default_provider` int(11) NOT NULL default '0',
  `program_order` int(11) NOT NULL default '0',
  `effective_start` date NOT NULL default '0000-00-00',
  `effective_end` date NOT NULL default '0000-00-00',
  `active` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`insured_relationship_id`),
  KEY `person_id` (`person_id`),
  KEY `insurance_program_id` (`insurance_program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `lab_note`
--

DROP TABLE IF EXISTS `lab_note`;
CREATE TABLE `lab_note` (
  `lab_note_id` int(11) NOT NULL default '0',
  `lab_test_id` int(11) NOT NULL default '0',
  `note` text NOT NULL,
  PRIMARY KEY  (`lab_note_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `lab_order`
--

DROP TABLE IF EXISTS `lab_order`;
CREATE TABLE `lab_order` (
  `lab_order_id` int(11) NOT NULL default '0',
  `patient_id` int(11) NOT NULL default '0',
  `type` char(6) NOT NULL,
  `status` char(2) NOT NULL default '',
  `ordering_provider` varchar(255) NOT NULL default '',
  `manual_service` tinyint(4) NOT NULL,
  `manual_order_date` date NOT NULL,
  `external_id` int(11) NOT NULL,
  PRIMARY KEY  (`lab_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `lab_result`
--

DROP TABLE IF EXISTS `lab_result`;
CREATE TABLE `lab_result` (
  `lab_result_id` int(11) NOT NULL default '0',
  `lab_test_id` int(11) NOT NULL default '0',
  `identifier` varchar(255) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  `units` varchar(255) NOT NULL default '',
  `reference_range` varchar(255) NOT NULL default '',
  `abnormal_flag` char(8) NOT NULL,
  `result_status` char(1) NOT NULL default '',
  `observation_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `producer_id` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL,
  PRIMARY KEY  (`lab_result_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `lab_test`
--

DROP TABLE IF EXISTS `lab_test`;
CREATE TABLE `lab_test` (
  `lab_test_id` int(11) NOT NULL default '0',
  `lab_order_id` int(11) NOT NULL default '0',
  `order_num` varchar(255) NOT NULL default '',
  `filer_order_num` varchar(255) NOT NULL default '',
  `observation_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `specimen_received_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `report_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering_provider` varchar(255) NOT NULL default '',
  `service` varchar(255) NOT NULL default '',
  `component_code` varchar(255) NOT NULL default '',
  `status` char(1) NOT NULL default '',
  `clia_disclosure` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`lab_test_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `link`
--

DROP TABLE IF EXISTS `link`;
CREATE TABLE `link` (
  `oldId` varchar(255) NOT NULL,
  `kind` varchar(255) NOT NULL,
  `newId` bigint(20) NOT NULL,
  PRIMARY KEY  (`oldId`,`kind`),
  KEY `newId` (`newId`),
  KEY `oldId` (`oldId`),
  KEY `kind` (`kind`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `meds_bulk_quantity`
--

DROP TABLE IF EXISTS `meds_bulk_quantity`;
CREATE TABLE `meds_bulk_quantity` (
  `meds_bulk_quantity_id` int(11) NOT NULL default '0',
  `meds_inventory_item_id` int(11) NOT NULL default '0',
  `pill_count` int(255) default NULL,
  `strength` varchar(255) default NULL,
  `class` int(11) NOT NULL default '0',
  `class_type` int(11) NOT NULL default '0',
  `use_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`meds_bulk_quantity_id`),
  UNIQUE KEY `meds_inventory_item_id` (`meds_inventory_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `meds_case`
--

DROP TABLE IF EXISTS `meds_case`;
CREATE TABLE `meds_case` (
  `meds_case_id` int(11) NOT NULL default '0',
  `meds_inventory_item_id` int(11) NOT NULL default '0',
  `case_count` int(255) default NULL,
  PRIMARY KEY  (`meds_case_id`),
  UNIQUE KEY `meds_inventory_item_id` (`meds_inventory_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `meds_inventory_item`
--

DROP TABLE IF EXISTS `meds_inventory_item`;
CREATE TABLE `meds_inventory_item` (
  `meds_inventory_item_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `sku` varchar(255) NOT NULL default '',
  `short_description` varchar(255) default NULL,
  `long_description` text,
  `shipping_weight` varchar(255) default NULL,
  `unit_weight` varchar(255) default NULL,
  `min_order_qty` int(255) default NULL,
  `type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`meds_inventory_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `meds_inventory_item_price`
--

DROP TABLE IF EXISTS `meds_inventory_item_price`;
CREATE TABLE `meds_inventory_item_price` (
  `meds_inventory_item_price_id` int(11) NOT NULL default '0',
  `meds_inventory_item_id` int(11) NOT NULL default '0',
  `company_id` int(11) NOT NULL default '0',
  `price` decimal(11,2) default NULL,
  `awp` decimal(11,2) default NULL,
  `aac` decimal(11,2) default NULL,
  `copay` decimal(11,2) default NULL,
  PRIMARY KEY  (`meds_inventory_item_price_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `meds_inventory_item_status`
--

DROP TABLE IF EXISTS `meds_inventory_item_status`;
CREATE TABLE `meds_inventory_item_status` (
  `meds_inventory_item_status_id` int(11) NOT NULL default '0',
  `meds_inventory_item_id` int(11) NOT NULL default '0',
  `on_hand` int(255) default NULL,
  `reorder_point` int(255) default NULL,
  PRIMARY KEY  (`meds_inventory_item_status_id`),
  UNIQUE KEY `meds_inventory_item_id` (`meds_inventory_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `meds_item_to_location`
--

DROP TABLE IF EXISTS `meds_item_to_location`;
CREATE TABLE `meds_item_to_location` (
  `meds_item_to_location_id` int(11) NOT NULL default '0',
  `meds_inventory_item_id` int(11) NOT NULL default '0',
  `building_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`meds_item_to_location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `meds_item_to_program`
--

DROP TABLE IF EXISTS `meds_item_to_program`;
CREATE TABLE `meds_item_to_program` (
  `meds_item_to_program_id` int(11) NOT NULL default '0',
  `meds_inventory_item_id` int(11) NOT NULL default '0',
  `insurance_program_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`meds_item_to_program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `meds_program`
--

DROP TABLE IF EXISTS `meds_program`;
CREATE TABLE `meds_program` (
  `meds_program_id` int(11) NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `meds_unit_of_use`
--

DROP TABLE IF EXISTS `meds_unit_of_use`;
CREATE TABLE `meds_unit_of_use` (
  `meds_unit_of_use_id` int(11) NOT NULL default '0',
  `meds_inventory_item_id` int(11) NOT NULL default '0',
  `brand_name` varchar(255) default NULL,
  `drug_name` varchar(255) default NULL,
  `lot` varchar(255) default NULL,
  `expiration_date` datetime default NULL,
  `ndc` varchar(255) default NULL,
  `pill_count` int(255) default NULL,
  `strength` varchar(255) default NULL,
  `class` int(11) NOT NULL default '0',
  `class_type` int(11) NOT NULL default '0',
  `use_type` int(11) NOT NULL default '0',
  `instructions` text,
  PRIMARY KEY  (`meds_unit_of_use_id`),
  UNIQUE KEY `meds_inventory_item_id` (`meds_inventory_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `meds_unit_of_use_warning`
--

DROP TABLE IF EXISTS `meds_unit_of_use_warning`;
CREATE TABLE `meds_unit_of_use_warning` (
  `meds_unit_of_use_warning_id` int(11) NOT NULL default '0',
  `meds_unit_of_use_id` int(11) NOT NULL default '0',
  `warning` int(11) default NULL,
  PRIMARY KEY  (`meds_unit_of_use_warning_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `meds_user_to_program`
--

DROP TABLE IF EXISTS `meds_user_to_program`;
CREATE TABLE `meds_user_to_program` (
  `user_id` int(11) NOT NULL default '0',
  `meds_program_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `menu_id` int(11) NOT NULL,
  `site_section` varchar(50) NOT NULL default 'default',
  `parent` int(11) NOT NULL default '0',
  `dynamic_key` varchar(50) NOT NULL default '',
  `section` enum('children','more','dynamic') NOT NULL default 'children',
  `display_order` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `action` varchar(255) NOT NULL default '',
  `prefix` varchar(100) NOT NULL default 'main',
  PRIMARY KEY  (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `menu_form`
--

DROP TABLE IF EXISTS `menu_form`;
CREATE TABLE `menu_form` (
  `menu_form_id` int(11) NOT NULL default '0',
  `menu_id` int(11) NOT NULL default '0',
  `form_id` int(11) NOT NULL default '0',
  `title` varchar(50) NOT NULL default '',
  `custom_action` varchar(255) default NULL,
  PRIMARY KEY  (`menu_form_id`),
  KEY `menu_id` (`menu_id`),
  KEY `form_id` (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `menu_report`
--

DROP TABLE IF EXISTS `menu_report`;
CREATE TABLE `menu_report` (
  `menu_report_id` int(11) NOT NULL default '0',
  `menu_id` int(11) NOT NULL default '0',
  `report_template_id` int(11) NOT NULL default '0',
  `title` varchar(50) NOT NULL default '',
  `custom_action` varchar(255) default NULL,
  PRIMARY KEY  (`menu_report_id`),
  KEY `menu_id` (`menu_id`),
  KEY `report_template_id` (`report_template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `misc_charge`
--

DROP TABLE IF EXISTS `misc_charge`;
CREATE TABLE `misc_charge` (
  `misc_charge_id` int(11) NOT NULL default '0',
  `encounter_id` int(11) NOT NULL default '0',
  `amount` float(7,2) NOT NULL default '0.00',
  `charge_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` varchar(50) NOT NULL default '',
  `note` text NOT NULL,
  PRIMARY KEY  (`misc_charge_id`),
  KEY `encounter_id` (`encounter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `name_history`
--

DROP TABLE IF EXISTS `name_history`;
CREATE TABLE `name_history` (
  `name_history_id` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  `first_name` varchar(100) NOT NULL default '',
  `last_name` varchar(100) NOT NULL default '',
  `middle_name` varchar(50) NOT NULL default '',
  `update_date` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`name_history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `note`
--

DROP TABLE IF EXISTS `note`;
CREATE TABLE `note` (
  `id` int(11) NOT NULL,
  `foreign_id` int(11) NOT NULL default '0',
  `note` varchar(255) default NULL,
  `owner` int(11) default NULL,
  `date` datetime default NULL,
  `revision` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `foreign_id` (`owner`),
  KEY `foreign_id_2` (`foreign_id`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
CREATE TABLE `notes` (
  `note_id` int(10) unsigned NOT NULL default '0',
  `revision_id` int(10) unsigned NOT NULL default '0',
  `user_id` int(10) unsigned NOT NULL default '0',
  `note` mediumtext NOT NULL,
  `create_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`note_id`),
  KEY `revision_id` (`revision_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `number`
--

DROP TABLE IF EXISTS `number`;
CREATE TABLE `number` (
  `number_id` int(11) NOT NULL,
  `number_type` int(11) NOT NULL default '0',
  `notes` tinytext NOT NULL,
  `number` varchar(100) NOT NULL default '',
  `active` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`number_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='A phone number';

--
-- Table structure for table `occurence_breakdown`
--

DROP TABLE IF EXISTS `occurence_breakdown`;
CREATE TABLE `occurence_breakdown` (
  `occurence_breakdown_id` int(11) NOT NULL default '0',
  `occurence_id` int(11) NOT NULL default '0',
  `index` int(11) default '0',
  `offset` int(11) NOT NULL default '0',
  `length` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`occurence_breakdown_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `occurences`
--

DROP TABLE IF EXISTS `occurences`;
CREATE TABLE `occurences` (
  `id` int(11) NOT NULL default '0',
  `event_id` int(11) NOT NULL default '0',
  `start` datetime NOT NULL default '0000-00-00 00:00:00',
  `end` datetime NOT NULL default '0000-00-00 00:00:00',
  `notes` varchar(255) NOT NULL default '',
  `location_id` int(11) NOT NULL default '0',
  `user_id` int(11) default NULL,
  `last_change_id` int(11) default NULL,
  `external_id` int(11) default NULL,
  `reason_code` int(11) NOT NULL default '0',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `walkin` tinyint(4) NOT NULL default '0',
  `group_appointment` tinyint(4) NOT NULL default '0',
  `creator_id` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `ordo_registry`
--

DROP TABLE IF EXISTS `ordo_registry`;
CREATE TABLE `ordo_registry` (
  `ordo_id` int(11) NOT NULL default '0',
  `creator_id` int(11) NOT NULL default '0',
  `owner_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ordo_id`),
  KEY `creator_id` (`creator_id`,`owner_id`),
  KEY `owner_id` (`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `ownership`
--

DROP TABLE IF EXISTS `ownership`;
CREATE TABLE `ownership` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `participation_program`
--

DROP TABLE IF EXISTS `participation_program`;
CREATE TABLE `participation_program` (
  `participation_program_id` bigint(20) NOT NULL default '0',
  `adhoc` tinyint(4) NOT NULL,
  `class` varchar(255) NOT NULL default '',
  `type` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `form_id` int(11) NOT NULL,
  PRIMARY KEY  (`participation_program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `participation_program_basic`
--

DROP TABLE IF EXISTS `participation_program_basic`;
CREATE TABLE `participation_program_basic` (
  `person_program_id` bigint(20) NOT NULL default '0',
  `federal_poverty_level` char(3) NOT NULL default '',
  `eligibility` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`person_program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `participation_program_clinic`
--

DROP TABLE IF EXISTS `participation_program_clinic`;
CREATE TABLE `participation_program_clinic` (
  `person_program_id` bigint(20) NOT NULL default '0',
  `eligibility` tinyint(4) NOT NULL default '0',
  `initial_date` date NOT NULL,
  `recent_date` date NOT NULL,
  PRIMARY KEY  (`person_program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `patient`
--

DROP TABLE IF EXISTS `patient`;
CREATE TABLE `patient` (
  `person_id` int(11) NOT NULL default '0',
  `is_default_provider_primary` int(11) NOT NULL default '0',
  `default_provider` int(11) NOT NULL default '0',
  `record_number` int(11) NOT NULL default '0',
  `employer_name` varchar(255) NOT NULL default '' COMMENT '\0\0\0\0\0\0\0\0\0\0\0!\0\0Ã¯Â¿Â½',
  `confidentiality` int(11) NOT NULL default '0',
  `specialNeedsNote` varchar(255) NOT NULL,
  `specialNeedsTranslator` tinyint(4) NOT NULL,
  PRIMARY KEY  (`person_id`),
  KEY `record_number` (`record_number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='An patient extends the person entity';

--
-- Table structure for table `patient_chronic_code`
--

DROP TABLE IF EXISTS `patient_chronic_code`;
CREATE TABLE `patient_chronic_code` (
  `patient_id` int(11) NOT NULL default '0',
  `chronic_care_code` int(11) NOT NULL default '0',
  PRIMARY KEY  (`patient_id`,`chronic_care_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `patient_link`
--

DROP TABLE IF EXISTS `patient_link`;
CREATE TABLE `patient_link` (
  `oldId` int(11) NOT NULL,
  `newId` int(11) NOT NULL,
  PRIMARY KEY  (`oldId`,`newId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `patient_note`
--

DROP TABLE IF EXISTS `patient_note`;
CREATE TABLE `patient_note` (
  `patient_note_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `priority` int(11) NOT NULL default '0',
  `note_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `note` text NOT NULL,
  `deprecated` tinyint(1) NOT NULL default '0',
  `reason` tinyint(4) NOT NULL,
  PRIMARY KEY  (`patient_note_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `patient_payment_plan`
--

DROP TABLE IF EXISTS `patient_payment_plan`;
CREATE TABLE `patient_payment_plan` (
  `patient_payment_plan_id` int(11) NOT NULL default '0',
  `patient_id` int(11) NOT NULL default '0',
  `start_date` date NOT NULL default '0000-00-00',
  `intervalnum` int(11) NOT NULL default '0',
  `intervaltype` enum('DAY','WEEK','MONTH','YEAR') NOT NULL default 'DAY',
  `num_intervals` int(11) NOT NULL default '0',
  `balance` float NOT NULL default '0',
  PRIMARY KEY  (`patient_payment_plan_id`),
  KEY `patient_id` (`patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `patient_payment_plan_payment`
--

DROP TABLE IF EXISTS `patient_payment_plan_payment`;
CREATE TABLE `patient_payment_plan_payment` (
  `patient_payment_plan_payment_id` int(11) NOT NULL default '0',
  `patient_payment_plan_id` int(11) NOT NULL default '0',
  `payment_date` date NOT NULL default '0000-00-00',
  `amount` float NOT NULL default '0',
  `paid_amount` float NOT NULL default '0',
  `paid` enum('Yes','No') NOT NULL default 'No',
  PRIMARY KEY  (`patient_payment_plan_payment_id`),
  KEY `patient_payment_plan_id` (`patient_payment_plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `patient_statistics`
--

DROP TABLE IF EXISTS `patient_statistics`;
CREATE TABLE `patient_statistics` (
  `person_id` int(11) NOT NULL default '0',
  `ethnicity` int(11) NOT NULL default '0',
  `race` int(11) NOT NULL default '0',
  `income` int(11) NOT NULL default '0',
  `language` int(11) NOT NULL default '0',
  `migrant_status` int(11) NOT NULL default '0',
  `registration_location` int(11) NOT NULL default '0',
  `sign_in_date` date NOT NULL default '0000-00-00',
  `monthly_income` int(11) NOT NULL default '0',
  `family_size` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `payer_group`
--

DROP TABLE IF EXISTS `payer_group`;
CREATE TABLE `payer_group` (
  `payer_group_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`payer_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `foreign_id` int(11) NOT NULL default '0',
  `encounter_id` int(11) NOT NULL default '0',
  `payment_type` int(11) NOT NULL default '0',
  `ref_num` varchar(60) NOT NULL default '',
  `amount` float(11,2) NOT NULL default '0.00',
  `writeoff` float(11,2) NOT NULL default '0.00',
  `user_id` int(11) NOT NULL default '0',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `payer_id` int(11) NOT NULL default '0',
  `payment_date` date NOT NULL default '0000-00-00',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`payment_id`),
  KEY `foreign_id` (`foreign_id`),
  KEY `encounter_id` (`encounter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `payment_claimline`
--

DROP TABLE IF EXISTS `payment_claimline`;
CREATE TABLE `payment_claimline` (
  `payment_claimline_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL default '0',
  `code_id` int(11) NOT NULL default '0',
  `coding_data_id` int(11) NOT NULL,
  `paid` float(7,2) NOT NULL default '0.00',
  `writeoff` float(7,2) NOT NULL default '0.00',
  `carry` float(7,2) NOT NULL default '0.00',
  PRIMARY KEY  (`payment_claimline_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `pccconversion`
--

DROP TABLE IF EXISTS `pccconversion`;
CREATE TABLE `pccconversion` (
  `type` varchar(255) NOT NULL default '',
  `old` int(11) NOT NULL default '0',
  `new` int(11) NOT NULL default '0',
  KEY `type` (`type`,`old`,`new`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `person`
--

DROP TABLE IF EXISTS `person`;
CREATE TABLE `person` (
  `person_id` int(11) NOT NULL,
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
  `identifier` varchar(100) NOT NULL default '',
  `identifier_type` int(11) NOT NULL default '0',
  `marital_status` int(11) NOT NULL default '0',
  `inactive` int(1) NOT NULL default '0',
  `primary_practice_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`),
  KEY `primary_practice_id` (`primary_practice_id`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='A person in the system';

--
-- Table structure for table `person_address`
--

DROP TABLE IF EXISTS `person_address`;
CREATE TABLE `person_address` (
  `person_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  `address_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`,`address_id`),
  KEY `address_type` (`address_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Links a person to a address specifying the address type';

--
-- Table structure for table `person_company`
--

DROP TABLE IF EXISTS `person_company`;
CREATE TABLE `person_company` (
  `person_id` int(11) NOT NULL default '0',
  `company_id` int(11) NOT NULL default '0',
  `person_type` int(11) default NULL,
  PRIMARY KEY  (`person_id`,`company_id`),
  KEY `person_id` (`person_id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Links a person to a company and optionaly specifies the lin';

--
-- Table structure for table `person_link`
--

DROP TABLE IF EXISTS `person_link`;
CREATE TABLE `person_link` (
  `oldId` int(11) NOT NULL,
  `newId` int(11) NOT NULL,
  PRIMARY KEY  (`oldId`,`newId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `person_number`
--

DROP TABLE IF EXISTS `person_number`;
CREATE TABLE `person_number` (
  `person_id` int(11) NOT NULL default '0',
  `number_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`,`number_id`),
  KEY `phone_id` (`number_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Links between people and phone_numbers';

--
-- Table structure for table `person_participation_program`
--

DROP TABLE IF EXISTS `person_participation_program`;
CREATE TABLE `person_participation_program` (
  `person_program_id` bigint(20) NOT NULL default '0',
  `participation_program_id` bigint(20) NOT NULL default '0',
  `person_id` bigint(20) NOT NULL default '0',
  `start` date NOT NULL default '0000-00-00',
  `end` date NOT NULL default '0000-00-00',
  `expires` tinyint(4) NOT NULL default '0',
  `active` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`person_program_id`),
  UNIQUE KEY `person_id` (`person_id`,`participation_program_id`),
  KEY `participation_program_id` (`participation_program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `person_person`
--

DROP TABLE IF EXISTS `person_person`;
CREATE TABLE `person_person` (
  `person_person_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL default '0',
  `related_person_id` int(11) NOT NULL default '0',
  `relation_type` int(11) NOT NULL default '0',
  `guarantor` tinyint(1) NOT NULL default '0',
  `guarantor_priority` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_person_id`),
  UNIQUE KEY `person_id` (`person_id`,`related_person_id`,`relation_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Link to specify person type';

--
-- Table structure for table `practice_address`
--

DROP TABLE IF EXISTS `practice_address`;
CREATE TABLE `practice_address` (
  `practice_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  `address_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`practice_id`,`address_id`),
  KEY `address_id` (`address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Links a practice to a address specifying the address type';

--
-- Table structure for table `practice_link`
--

DROP TABLE IF EXISTS `practice_link`;
CREATE TABLE `practice_link` (
  `oldId` char(100) NOT NULL,
  `newId` int(11) NOT NULL,
  PRIMARY KEY  (`oldId`,`newId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `practice_number`
--

DROP TABLE IF EXISTS `practice_number`;
CREATE TABLE `practice_number` (
  `practice_id` int(11) NOT NULL default '0',
  `number_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`practice_id`,`number_id`),
  KEY `person_id` (`practice_id`),
  KEY `phone_id` (`number_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Links between people and phone_numbers';

--
-- Table structure for table `practice_setting`
--

DROP TABLE IF EXISTS `practice_setting`;
CREATE TABLE `practice_setting` (
  `practice_setting_id` int(11) NOT NULL,
  `practice_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `value` text NOT NULL,
  `serialized` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`practice_setting_id`),
  UNIQUE KEY `practice_id` (`practice_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `practices`
--

DROP TABLE IF EXISTS `practices`;
CREATE TABLE `practices` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL default '',
  `website` varchar(255) NOT NULL default '',
  `identifier` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `preferences`
--

DROP TABLE IF EXISTS `preferences`;
CREATE TABLE `preferences` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  `parent` int(11) NOT NULL default '0',
  `lft` int(11) NOT NULL default '0',
  `rght` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `parent` (`parent`),
  KEY `lft` (`lft`,`rght`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `provider`
--

DROP TABLE IF EXISTS `provider`;
CREATE TABLE `provider` (
  `person_id` int(11) NOT NULL default '0',
  `state_license_number` varchar(100) NOT NULL default '',
  `clia_number` varchar(100) NOT NULL default '',
  `dea_number` varchar(100) NOT NULL default '',
  `bill_as` int(11) NOT NULL default '0',
  `report_as` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `provider_to_insurance`
--

DROP TABLE IF EXISTS `provider_to_insurance`;
CREATE TABLE `provider_to_insurance` (
  `provider_to_insurance_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL default '0',
  `insurance_program_id` int(11) NOT NULL default '0',
  `provider_number` varchar(100) NOT NULL default '',
  `provider_number_type` int(11) NOT NULL default '0',
  `group_number` varchar(100) NOT NULL default '',
  `building_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`provider_to_insurance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `pull_list`
--

DROP TABLE IF EXISTS `pull_list`;
CREATE TABLE `pull_list` (
  `appointment_id` int(11) NOT NULL default '0',
  `pull_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`appointment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `record_sequence`
--

DROP TABLE IF EXISTS `record_sequence`;
CREATE TABLE `record_sequence` (
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `recurrence`
--

DROP TABLE IF EXISTS `recurrence`;
CREATE TABLE `recurrence` (
  `recurrence_id` int(10) unsigned NOT NULL default '0',
  `start_date` date NOT NULL default '0000-00-00',
  `end_date` date NOT NULL default '0000-00-00',
  `start_time` time default NULL,
  `end_time` time default NULL,
  `recurrence_pattern_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`recurrence_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `recurrence_pattern`
--

DROP TABLE IF EXISTS `recurrence_pattern`;
CREATE TABLE `recurrence_pattern` (
  `recurrence_pattern_id` int(11) NOT NULL default '0',
  `pattern_type` enum('day','monthday','monthweek','yearmonthday','yearmonthweek','dayweek') NOT NULL default 'day',
  `number` int(11) default NULL,
  `weekday` enum('1','2','3','4','5','6','7') default NULL,
  `month` enum('01','02','03','04','05','06','07','08','09','10','11','12') default NULL,
  `monthday` tinyint(2) default NULL,
  `week_of_month` enum('First','Second','Third','Fourth','Last') default NULL,
  PRIMARY KEY  (`recurrence_pattern_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `refPracticeLocation`
--

DROP TABLE IF EXISTS `refPracticeLocation`;
CREATE TABLE `refPracticeLocation` (
  `refPracticeLocation_id` int(11) NOT NULL default '0',
  `refPractice_id` int(11) NOT NULL default '0',
  `address1` varchar(255) NOT NULL default '',
  `address2` varchar(255) NOT NULL default '',
  `city` varchar(255) NOT NULL default '',
  `state` varchar(255) NOT NULL default '',
  `zipcode` varchar(255) NOT NULL default '',
  `appointment_number` varchar(255) NOT NULL default '',
  `fax_number` varchar(255) NOT NULL default '',
  `phone_number` varchar(255) NOT NULL,
  PRIMARY KEY  (`refPracticeLocation_id`),
  KEY `refPractice_id` (`refPractice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `refRequest`
--

DROP TABLE IF EXISTS `refRequest`;
CREATE TABLE `refRequest` (
  `refRequest_id` int(10) unsigned NOT NULL,
  `date` date NOT NULL default '0000-00-00',
  `eligibility` varchar(255) NOT NULL default '0',
  `eligible_thru` date NOT NULL default '0000-00-00',
  `refSpecialty` int(11) NOT NULL default '0',
  `refRequested_day` int(11) NOT NULL default '0',
  `refRequested_time` int(11) NOT NULL default '0',
  `refStatus` int(11) NOT NULL default '0',
  `reason` varchar(255) NOT NULL default '',
  `history` varchar(255) NOT NULL default '',
  `notes` longtext NOT NULL,
  `translator` int(11) NOT NULL default '0',
  `transportation` int(11) NOT NULL default '0',
  `occurence_id` int(11) NOT NULL default '0',
  `refappointment_id` int(11) NOT NULL default '0',
  `refprogram_id` int(11) NOT NULL default '0',
  `patient_id` int(11) NOT NULL default '0',
  `visit_id` int(11) NOT NULL default '0',
  `initiator_id` int(11) NOT NULL default '0',
  `referral_service` tinyint(4) NOT NULL,
  PRIMARY KEY  (`refRequest_id`),
  KEY `patient_id` (`patient_id`),
  KEY `visit_id` (`visit_id`),
  KEY `initiator_id` (`initiator_id`),
  KEY `refprogram_id` (`refprogram_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `refSpecialtyMap`
--

DROP TABLE IF EXISTS `refSpecialtyMap`;
CREATE TABLE `refSpecialtyMap` (
  `refSpecialityMap_id` int(11) NOT NULL,
  `external_type` varchar(255) NOT NULL default '',
  `external_id` int(11) NOT NULL default '0',
  `enumeration_value_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`refSpecialityMap_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `refappointment`
--

DROP TABLE IF EXISTS `refappointment`;
CREATE TABLE `refappointment` (
  `refappointment_id` int(11) NOT NULL,
  `refrequest_id` int(11) NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `refpractice_id` int(11) NOT NULL default '0',
  `reflocation_id` int(11) NOT NULL default '0',
  `refprovider_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`refappointment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `refpatient_eligibility`
--

DROP TABLE IF EXISTS `refpatient_eligibility`;
CREATE TABLE `refpatient_eligibility` (
  `refpatient_eligibility_id` int(11) NOT NULL,
  `eligibility` varchar(255) NOT NULL default '',
  `eligible_thru` date NOT NULL default '0000-00-00',
  `patient_id` int(11) NOT NULL default '0',
  `refprogram_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`refpatient_eligibility_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `refpractice`
--

DROP TABLE IF EXISTS `refpractice`;
CREATE TABLE `refpractice` (
  `refPractice_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `assign_by` enum('Practice','Provider') NOT NULL default 'Practice',
  `default_num_of_slots` int(11) NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`refPractice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `refpractice_specialty`
--

DROP TABLE IF EXISTS `refpractice_specialty`;
CREATE TABLE `refpractice_specialty` (
  `refpractice_specialty_id` int(11) NOT NULL,
  `specialty` int(11) NOT NULL default '0',
  `form` varchar(255) NOT NULL default '0',
  `refpractice_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`refpractice_specialty_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `refprogram`
--

DROP TABLE IF EXISTS `refprogram`;
CREATE TABLE `refprogram` (
  `refprogram_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL default '',
  `schema` int(11) NOT NULL default '0',
  PRIMARY KEY  (`refprogram_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `refprogram_member`
--

DROP TABLE IF EXISTS `refprogram_member`;
CREATE TABLE `refprogram_member` (
  `refprogram_member_id` int(11) NOT NULL,
  `refprogram_id` int(11) NOT NULL default '0',
  `external_id` int(11) NOT NULL default '0',
  `external_type` varchar(255) NOT NULL default '',
  `inactive` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`refprogram_member_id`),
  KEY `external_id` (`external_id`),
  KEY `refprogram_id` (`refprogram_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `refprogram_member_slot`
--

DROP TABLE IF EXISTS `refprogram_member_slot`;
CREATE TABLE `refprogram_member_slot` (
  `refprogram_member_slot_id` int(11) NOT NULL,
  `month` int(11) NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  `slots` int(11) NOT NULL default '0',
  `external_type` enum('Practice','Provider') NOT NULL default 'Practice',
  `external_id` int(11) NOT NULL default '0',
  `refprogram_member_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`refprogram_member_slot_id`),
  KEY `external_id` (`external_id`),
  KEY `refprogram_member_id` (`refprogram_member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `refprovider`
--

DROP TABLE IF EXISTS `refprovider`;
CREATE TABLE `refprovider` (
  `refprovider_id` int(11) NOT NULL,
  `prefix` varchar(255) NOT NULL default '',
  `first_name` varchar(255) NOT NULL default '',
  `middle_name` varchar(255) NOT NULL default '',
  `last_name` varchar(255) NOT NULL default '',
  `direct_line` varchar(255) NOT NULL default '',
  `refpractice_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`refprovider_id`),
  KEY `refpractice_id` (`refpractice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `refreferral_visit`
--

DROP TABLE IF EXISTS `refreferral_visit`;
CREATE TABLE `refreferral_visit` (
  `refreferral_visit_id` int(11) NOT NULL,
  `refappointment_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`refreferral_visit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `refuser`
--

DROP TABLE IF EXISTS `refuser`;
CREATE TABLE `refuser` (
  `refuser_id` int(11) NOT NULL,
  `external_user_id` int(11) NOT NULL default '0',
  `refusertype` int(11) NOT NULL default '0',
  `refprogram_id` int(11) NOT NULL default '0',
  `deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`refuser_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `relationship`
--

DROP TABLE IF EXISTS `relationship`;
CREATE TABLE `relationship` (
  `relationship_id` int(11) NOT NULL,
  `parent_type` varchar(255) NOT NULL default '',
  `parent_id` int(11) NOT NULL default '0',
  `child_type` varchar(255) NOT NULL default '',
  `child_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`relationship_id`),
  KEY `parent_type` (`parent_type`,`parent_id`,`child_type`,`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `report_snapshot`
--

DROP TABLE IF EXISTS `report_snapshot`;
CREATE TABLE `report_snapshot` (
  `report_snapshot_id` int(11) NOT NULL default '0',
  `report_id` int(11) NOT NULL default '0',
  `template_id` int(11) NOT NULL default '0',
  `snapshot_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `data` longtext NOT NULL,
  PRIMARY KEY  (`report_snapshot_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `report_templates`
--

DROP TABLE IF EXISTS `report_templates`;
CREATE TABLE `report_templates` (
  `report_template_id` int(11) NOT NULL default '0',
  `report_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `is_default` enum('yes','no') NOT NULL default 'yes',
  `sequence` int(11) NOT NULL default '100000',
  `custom_id` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`report_template_id`),
  KEY `report_id` (`report_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Report templates';

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `dbase` varchar(255) NOT NULL default '',
  `user` varchar(255) NOT NULL default '',
  `label` varchar(255) NOT NULL default '',
  `query` longtext NOT NULL,
  `description` mediumtext NOT NULL,
  `custom_id` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Report definitions TODO: change to Generic Seq';

--
-- Table structure for table `revisions`
--

DROP TABLE IF EXISTS `revisions`;
CREATE TABLE `revisions` (
  `revision_id` int(10) unsigned NOT NULL,
  `storable_id` int(10) unsigned NOT NULL default '0',
  `revision` int(10) unsigned NOT NULL default '0',
  `create_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `user_id` int(10) unsigned NOT NULL default '0',
  `filesize` int(11) default NULL,
  PRIMARY KEY  (`revision_id`),
  KEY `storable_id` (`storable_id`,`revision`),
  KEY `modify_date` (`create_date`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `revisions_db`
--

DROP TABLE IF EXISTS `revisions_db`;
CREATE TABLE `revisions_db` (
  `revision_id` int(10) unsigned NOT NULL default '0',
  `filedata` blob NOT NULL,
  PRIMARY KEY  (`revision_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `description` text NOT NULL,
  `number_seats` int(11) NOT NULL default '0',
  `building_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `color` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `route_slip`
--

DROP TABLE IF EXISTS `route_slip`;
CREATE TABLE `route_slip` (
  `route_slip_id` int(11) NOT NULL default '0',
  `encounter_id` int(11) NOT NULL default '0',
  `report_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`route_slip_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `schedule`
--

DROP TABLE IF EXISTS `schedule`;
CREATE TABLE `schedule` (
  `schedule_id` int(10) unsigned NOT NULL,
  `title` varchar(150) default NULL,
  `description_long` text,
  `description_short` text,
  `schedule_code` varchar(255) default NULL,
  `provider_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`schedule_id`),
  KEY `provider_id` (`provider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `schedule_event`
--

DROP TABLE IF EXISTS `schedule_event`;
CREATE TABLE `schedule_event` (
  `event_id` int(11) NOT NULL default '0',
  `event_group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`event_id`),
  KEY `event_group_id` (`event_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `secondary_practice`
--

DROP TABLE IF EXISTS `secondary_practice`;
CREATE TABLE `secondary_practice` (
  `secondary_practice_id` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  `practice_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`secondary_practice_id`),
  KEY `person_id` (`person_id`,`practice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `self_mgmt_goals`
--

DROP TABLE IF EXISTS `self_mgmt_goals`;
CREATE TABLE `self_mgmt_goals` (
  `self_mgmt_id` bigint(20) NOT NULL,
  `last_edit` timestamp NULL default NULL,
  `person_id` bigint(20) NOT NULL,
  `initiated` date NOT NULL,
  `completed` date NOT NULL,
  `type` tinyint(4) NOT NULL,
  PRIMARY KEY  (`self_mgmt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `sequences`
--

DROP TABLE IF EXISTS `sequences`;
CREATE TABLE `sequences` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `sequences_daily`
--

DROP TABLE IF EXISTS `sequences_daily`;
CREATE TABLE `sequences_daily` (
  `counter` int(11) NOT NULL default '0',
  `updated_on` date NOT NULL default '0000-00-00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `sequences_named`
--

DROP TABLE IF EXISTS `sequences_named`;
CREATE TABLE `sequences_named` (
  `name` varchar(255) NOT NULL default '',
  `counter` int(11) NOT NULL default '0',
  PRIMARY KEY  (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `splash`
--

DROP TABLE IF EXISTS `splash`;
CREATE TABLE `splash` (
  `splash_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `message` longtext NOT NULL,
  PRIMARY KEY  (`splash_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `statement_history`
--

DROP TABLE IF EXISTS `statement_history`;
CREATE TABLE `statement_history` (
  `statement_history_id` int(11) NOT NULL default '0',
  `patient_id` int(11) NOT NULL default '0',
  `report_snapshot_id` int(11) NOT NULL default '0',
  `statement_number` int(11) NOT NULL default '0',
  `date_generated` datetime NOT NULL default '0000-00-00 00:00:00',
  `amount` float(7,2) NOT NULL default '0.00',
  `type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`statement_history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `statement_sequence`
--

DROP TABLE IF EXISTS `statement_sequence`;
CREATE TABLE `statement_sequence` (
  `id` int(11) NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `states`
--

DROP TABLE IF EXISTS `states`;
CREATE TABLE `states` (
  `zone_code` varchar(32) NOT NULL default '',
  `zone_name` varchar(32) NOT NULL default '',
  `country` char(3) default NULL,
  PRIMARY KEY  (`zone_code`,`zone_name`),
  KEY `country` (`country`),
  KEY `zone_code` (`zone_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `storables`
--

DROP TABLE IF EXISTS `storables`;
CREATE TABLE `storables` (
  `storable_id` int(10) unsigned NOT NULL default '0',
  `type` tinyint(3) unsigned NOT NULL default '0',
  `mimetype` varchar(25) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `storage_type` char(2) default NULL,
  `create_date` datetime default '0000-00-00 00:00:00',
  `last_revision_id` int(11) default NULL,
  `webdavname` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`storable_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `storage_date`
--

DROP TABLE IF EXISTS `storage_date`;
CREATE TABLE `storage_date` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(50) NOT NULL default '',
  `value` date NOT NULL default '0000-00-00',
  `array_index` tinyint(4) NOT NULL,
  PRIMARY KEY  (`foreign_key`,`value_key`,`array_index`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Generic way to store date values';

--
-- Table structure for table `storage_int`
--

DROP TABLE IF EXISTS `storage_int`;
CREATE TABLE `storage_int` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(50) NOT NULL default '',
  `value` int(11) NOT NULL default '0',
  `array_index` tinyint(4) NOT NULL,
  PRIMARY KEY  (`foreign_key`,`value_key`,`array_index`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Generic way to store integer values (also boolean)';

--
-- Table structure for table `storage_string`
--

DROP TABLE IF EXISTS `storage_string`;
CREATE TABLE `storage_string` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(50) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  `array_index` tinyint(4) NOT NULL,
  PRIMARY KEY  (`foreign_key`,`value_key`,`array_index`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Generic way to string values';

--
-- Table structure for table `storage_text`
--

DROP TABLE IF EXISTS `storage_text`;
CREATE TABLE `storage_text` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(255) NOT NULL default '',
  `value` longtext NOT NULL,
  `array_index` tinyint(4) NOT NULL,
  PRIMARY KEY  (`foreign_key`,`value_key`,`array_index`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Generic way to string values';

--
-- Table structure for table `summary_columns`
--

DROP TABLE IF EXISTS `summary_columns`;
CREATE TABLE `summary_columns` (
  `widget_form_id` bigint(20) default NULL,
  `type` varchar(100) default NULL,
  `name` varchar(100) default NULL,
  `summary_column_id` bigint(20) NOT NULL,
  `pretty_name` varchar(100) default NULL,
  `table_name` varchar(30) default NULL,
  UNIQUE KEY `idx_summary_columns` (`summary_column_id`,`widget_form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `superbill`
--

DROP TABLE IF EXISTS `superbill`;
CREATE TABLE `superbill` (
  `superbill_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `practice_id` int(11) NOT NULL default '0',
  `status` int(11) NOT NULL default '0',
  PRIMARY KEY  (`superbill_id`),
  KEY `practice_id` (`practice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `superbill_data`
--

DROP TABLE IF EXISTS `superbill_data`;
CREATE TABLE `superbill_data` (
  `superbill_data_id` int(11) NOT NULL default '0',
  `superbill_id` int(11) NOT NULL default '0',
  `code_id` int(11) NOT NULL default '0',
  `status` int(11) NOT NULL default '0',
  PRIMARY KEY  (`superbill_data_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `tag_id` int(10) unsigned NOT NULL,
  `tag` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`tag_id`),
  UNIQUE KEY `tag` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `tags_storables`
--

DROP TABLE IF EXISTS `tags_storables`;
CREATE TABLE `tags_storables` (
  `tag_id` int(10) unsigned NOT NULL default '0',
  `storable_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`tag_id`,`storable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `tree`
--

DROP TABLE IF EXISTS `tree`;
CREATE TABLE `tree` (
  `tree_id` int(10) unsigned NOT NULL,
  `lft` int(10) unsigned NOT NULL default '0',
  `rght` int(10) unsigned NOT NULL default '0',
  `level` int(10) unsigned NOT NULL default '0',
  `node_type` varchar(15) NOT NULL default '',
  `node_id` int(255) unsigned NOT NULL default '0',
  UNIQUE KEY `storable_id` (`tree_id`),
  KEY `lft` (`lft`,`rght`,`level`),
  KEY `node_type` (`node_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `username` varchar(55) NOT NULL default '',
  `password` varchar(255) NOT NULL default '',
  `nickname` varchar(255) NOT NULL default '',
  `color` varchar(255) NOT NULL default '',
  `person_id` int(11) default NULL,
  `disabled` enum('yes','no') NOT NULL default 'yes',
  `default_location_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `username` (`username`),
  KEY `person_id` (`person_id`),
  KEY `default_location_id` (`default_location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Users in the System';

--
-- Table structure for table `users_groups`
--

DROP TABLE IF EXISTS `users_groups`;
CREATE TABLE `users_groups` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  `foreign_id` int(11) NOT NULL default '0',
  `table` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_id` (`user_id`,`group_id`,`foreign_id`,`table`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `visit_queue`
--

DROP TABLE IF EXISTS `visit_queue`;
CREATE TABLE `visit_queue` (
  `visit_queue_id` int(11) NOT NULL default '0',
  `visit_queue_template_id` int(11) NOT NULL default '0',
  `provider_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`visit_queue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `visit_queue_reason`
--

DROP TABLE IF EXISTS `visit_queue_reason`;
CREATE TABLE `visit_queue_reason` (
  `visit_queue_reason_id` int(11) NOT NULL,
  `ordernum` int(11) NOT NULL default '0',
  `appt_length` time NOT NULL default '01:00:00',
  `reason` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`visit_queue_reason_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `visit_queue_template`
--

DROP TABLE IF EXISTS `visit_queue_template`;
CREATE TABLE `visit_queue_template` (
  `visit_queue_template_id` int(11) NOT NULL,
  `number_of_appointments` int(11) NOT NULL default '0',
  `visit_queue_reason_id` int(11) NOT NULL default '0',
  `visit_queue_rule_id` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`visit_queue_template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `widget_form`
--

DROP TABLE IF EXISTS `widget_form`;
CREATE TABLE `widget_form` (
  `widget_form_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `form_id` int(11) NOT NULL default '0',
  `type` int(11) NOT NULL default '0',
  `controller_name` varchar(100) NOT NULL,
  `show_on_medical_history` tinyint(1) NOT NULL,
  PRIMARY KEY  (`widget_form_id`),
  KEY `form_id` (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `x12imported_data`
--

DROP TABLE IF EXISTS `x12imported_data`;
CREATE TABLE `x12imported_data` (
  `x12imported_data_id` int(11) NOT NULL default '0',
  `data` longtext NOT NULL,
  `created_date` date NOT NULL default '0000-00-00',
  `filename` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`x12imported_data_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `x12transaction_data`
--

DROP TABLE IF EXISTS `x12transaction_data`;
CREATE TABLE `x12transaction_data` (
  `transaction_data_id` int(11) NOT NULL default '0',
  `history_id` int(11) NOT NULL default '0',
  `raw` longtext NOT NULL,
  `transaction_status` varchar(255) NOT NULL default '',
  `payment_amount` float(7,2) NOT NULL default '0.00',
  `total_charge` float(7,2) NOT NULL default '0.00',
  `patient_responsibility` float(7,2) NOT NULL default '0.00',
  PRIMARY KEY  (`transaction_data_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `x12transaction_history`
--

DROP TABLE IF EXISTS `x12transaction_history`;
CREATE TABLE `x12transaction_history` (
  `history_id` int(11) NOT NULL default '0',
  `source_id` int(11) NOT NULL default '0',
  `transaction_id` varchar(255) NOT NULL default '',
  `claim_id` varchar(255) NOT NULL default '',
  `applied_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `applied_by` int(11) NOT NULL default '0',
  `payment_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `zipcodes`
--

DROP TABLE IF EXISTS `zipcodes`;
CREATE TABLE `zipcodes` (
  `zip` int(10) unsigned NOT NULL default '0',
  `city` varchar(45) NOT NULL default '',
  `state` char(2) NOT NULL default '',
  `lat` float NOT NULL default '0',
  `lon` float NOT NULL default '0',
  `tz_offset` tinyint(4) NOT NULL default '0',
  `dst` char(1) NOT NULL default '',
  `country` char(2) NOT NULL default '',
  PRIMARY KEY  (`zip`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2007-08-09  2:52:30
