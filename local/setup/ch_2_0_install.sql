-- MySQL dump 10.10
--
-- Host: localhost    Database: ch9
-- ------------------------------------------------------
-- Server version	5.0.24a

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `DailyNamed`
--


/*!40000 ALTER TABLE `DailyNamed` DISABLE KEYS */;
LOCK TABLES `DailyNamed` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `DailyNamed` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `account_note`
--


/*!40000 ALTER TABLE `account_note` DISABLE KEYS */;
LOCK TABLES `account_note` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `account_note` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='An address that can be for a company or a person. STARTEMPTY';

--
-- Dumping data for table `address`
--


/*!40000 ALTER TABLE `address` DISABLE KEYS */;
LOCK TABLES `address` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `address` ENABLE KEYS */;

--
-- Table structure for table `adodbseq`
--

DROP TABLE IF EXISTS `adodbseq`;
CREATE TABLE `adodbseq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='STARTWITHDATA';

--
-- Dumping data for table `adodbseq`
--


/*!40000 ALTER TABLE `adodbseq` DISABLE KEYS */;
LOCK TABLES `adodbseq` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `adodbseq` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `altnotice`
--


/*!40000 ALTER TABLE `altnotice` DISABLE KEYS */;
LOCK TABLES `altnotice` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `altnotice` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `appointment`
--


/*!40000 ALTER TABLE `appointment` DISABLE KEYS */;
LOCK TABLES `appointment` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `appointment` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `appointment_breakdown`
--


/*!40000 ALTER TABLE `appointment_breakdown` DISABLE KEYS */;
LOCK TABLES `appointment_breakdown` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `appointment_breakdown` ENABLE KEYS */;

--
-- Table structure for table `appointment_link`
--

DROP TABLE IF EXISTS `appointment_link`;
CREATE TABLE `appointment_link` (
  `oldId` int(11) NOT NULL,
  `newId` int(11) NOT NULL,
  PRIMARY KEY  (`oldId`,`newId`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `appointment_link`
--


/*!40000 ALTER TABLE `appointment_link` DISABLE KEYS */;
LOCK TABLES `appointment_link` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `appointment_link` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `appointment_rule`
--


/*!40000 ALTER TABLE `appointment_rule` DISABLE KEYS */;
LOCK TABLES `appointment_rule` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `appointment_rule` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `appointment_ruleset`
--


/*!40000 ALTER TABLE `appointment_ruleset` DISABLE KEYS */;
LOCK TABLES `appointment_ruleset` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `appointment_ruleset` ENABLE KEYS */;

--
-- Table structure for table `appointment_template`
--

DROP TABLE IF EXISTS `appointment_template`;
CREATE TABLE `appointment_template` (
  `appointment_template_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`appointment_template_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `appointment_template`
--


/*!40000 ALTER TABLE `appointment_template` DISABLE KEYS */;
LOCK TABLES `appointment_template` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `appointment_template` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `audit_log`
--


/*!40000 ALTER TABLE `audit_log` DISABLE KEYS */;
LOCK TABLES `audit_log` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `audit_log` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `audit_log_field`
--


/*!40000 ALTER TABLE `audit_log_field` DISABLE KEYS */;
LOCK TABLES `audit_log_field` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `audit_log_field` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='Links a building to a address specifying type. STARTEMPTY';

--
-- Dumping data for table `building_address`
--


/*!40000 ALTER TABLE `building_address` DISABLE KEYS */;
LOCK TABLES `building_address` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `building_address` ENABLE KEYS */;

--
-- Table structure for table `building_link`
--

DROP TABLE IF EXISTS `building_link`;
CREATE TABLE `building_link` (
  `oldId` int(11) NOT NULL,
  `newId` int(11) NOT NULL,
  PRIMARY KEY  (`oldId`,`newId`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `building_link`
--


/*!40000 ALTER TABLE `building_link` DISABLE KEYS */;
LOCK TABLES `building_link` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `building_link` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `building_program_identifier`
--


/*!40000 ALTER TABLE `building_program_identifier` DISABLE KEYS */;
LOCK TABLES `building_program_identifier` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `building_program_identifier` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='STARTEMPTY';

--
-- Dumping data for table `buildings`
--


/*!40000 ALTER TABLE `buildings` DISABLE KEYS */;
LOCK TABLES `buildings` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `buildings` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='STARTWITHDATA';

--
-- Dumping data for table `category`
--


/*!40000 ALTER TABLE `category` DISABLE KEYS */;
LOCK TABLES `category` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `category` ENABLE KEYS */;

--
-- Table structure for table `category_to_document`
--

DROP TABLE IF EXISTS `category_to_document`;
CREATE TABLE `category_to_document` (
  `category_id` int(11) NOT NULL default '0',
  `document_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`category_id`,`document_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='STARTEMPTY';

--
-- Dumping data for table `category_to_document`
--


/*!40000 ALTER TABLE `category_to_document` DISABLE KEYS */;
LOCK TABLES `category_to_document` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `category_to_document` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='STARTEMPTY';

--
-- Dumping data for table `clearhealth_claim`
--


/*!40000 ALTER TABLE `clearhealth_claim` DISABLE KEYS */;
LOCK TABLES `clearhealth_claim` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `clearhealth_claim` ENABLE KEYS */;

--
-- Table structure for table `code_category`
--

DROP TABLE IF EXISTS `code_category`;
CREATE TABLE `code_category` (
  `code_category_id` int(11) NOT NULL default '0',
  `category_name` varchar(255) NOT NULL default '',
  `category_id` int(11) NOT NULL,
  PRIMARY KEY  (`code_category_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `code_category`
--


/*!40000 ALTER TABLE `code_category` DISABLE KEYS */;
LOCK TABLES `code_category` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `code_category` ENABLE KEYS */;

--
-- Table structure for table `code_to_category`
--

DROP TABLE IF EXISTS `code_to_category`;
CREATE TABLE `code_to_category` (
  `code_category_id` int(11) NOT NULL default '0',
  `code_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`code_category_id`,`code_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `code_to_category`
--


/*!40000 ALTER TABLE `code_to_category` DISABLE KEYS */;
LOCK TABLES `code_to_category` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `code_to_category` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `codes`
--


/*!40000 ALTER TABLE `codes` DISABLE KEYS */;
LOCK TABLES `codes` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `codes` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `coding_data`
--


/*!40000 ALTER TABLE `coding_data` DISABLE KEYS */;
LOCK TABLES `coding_data` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `coding_data` ENABLE KEYS */;

--
-- Table structure for table `coding_data_dental`
--

DROP TABLE IF EXISTS `coding_data_dental`;
CREATE TABLE `coding_data_dental` (
  `coding_data_id` int(11) NOT NULL default '0',
  `tooth` enum('N/A','All','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','All (Primary)','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T') NOT NULL default 'N/A',
  `toothside` enum('N/A','Front','Back','Top','Left','Right') NOT NULL default 'N/A',
  PRIMARY KEY  (`coding_data_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `coding_data_dental`
--


/*!40000 ALTER TABLE `coding_data_dental` DISABLE KEYS */;
LOCK TABLES `coding_data_dental` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `coding_data_dental` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `coding_template`
--


/*!40000 ALTER TABLE `coding_template` DISABLE KEYS */;
LOCK TABLES `coding_template` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `coding_template` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='Base Company record most of the data is linked in STARTEMPTY';

--
-- Dumping data for table `company`
--


/*!40000 ALTER TABLE `company` DISABLE KEYS */;
LOCK TABLES `company` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `company` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='Links a company to a address specifying the type STARTEMPTY';

--
-- Dumping data for table `company_address`
--


/*!40000 ALTER TABLE `company_address` DISABLE KEYS */;
LOCK TABLES `company_address` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `company_address` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='Relates a company to another company STARTEMPTY';

--
-- Dumping data for table `company_company`
--


/*!40000 ALTER TABLE `company_company` DISABLE KEYS */;
LOCK TABLES `company_company` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `company_company` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='Links between company and phone_numbers STARTEMPTY';

--
-- Dumping data for table `company_number`
--


/*!40000 ALTER TABLE `company_number` DISABLE KEYS */;
LOCK TABLES `company_number` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `company_number` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='Link to specify company type';

--
-- Dumping data for table `company_type`
--


/*!40000 ALTER TABLE `company_type` DISABLE KEYS */;
LOCK TABLES `company_type` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `company_type` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `countries`
--


/*!40000 ALTER TABLE `countries` DISABLE KEYS */;
LOCK TABLES `countries` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `countries` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cronable`
--


/*!40000 ALTER TABLE `cronable` DISABLE KEYS */;
LOCK TABLES `cronable` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `cronable` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `document`
--


/*!40000 ALTER TABLE `document` DISABLE KEYS */;
LOCK TABLES `document` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `document` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `duplicate_queue`
--


/*!40000 ALTER TABLE `duplicate_queue` DISABLE KEYS */;
LOCK TABLES `duplicate_queue` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `duplicate_queue` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `eligibility_log`
--


/*!40000 ALTER TABLE `eligibility_log` DISABLE KEYS */;
LOCK TABLES `eligibility_log` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `eligibility_log` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `encounter`
--


/*!40000 ALTER TABLE `encounter` DISABLE KEYS */;
LOCK TABLES `encounter` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `encounter` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `encounter_date`
--


/*!40000 ALTER TABLE `encounter_date` DISABLE KEYS */;
LOCK TABLES `encounter_date` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `encounter_date` ENABLE KEYS */;

--
-- Table structure for table `encounter_link`
--

DROP TABLE IF EXISTS `encounter_link`;
CREATE TABLE `encounter_link` (
  `oldId` int(11) NOT NULL,
  `newId` int(11) NOT NULL,
  PRIMARY KEY  (`oldId`,`newId`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `encounter_link`
--


/*!40000 ALTER TABLE `encounter_link` DISABLE KEYS */;
LOCK TABLES `encounter_link` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `encounter_link` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `encounter_person`
--


/*!40000 ALTER TABLE `encounter_person` DISABLE KEYS */;
LOCK TABLES `encounter_person` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `encounter_person` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `encounter_value`
--


/*!40000 ALTER TABLE `encounter_value` DISABLE KEYS */;
LOCK TABLES `encounter_value` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `encounter_value` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `enumeration_definition`
--


/*!40000 ALTER TABLE `enumeration_definition` DISABLE KEYS */;
LOCK TABLES `enumeration_definition` WRITE;
INSERT INTO `enumeration_definition` VALUES (1,'refEligibilitySchema','Referral: Eligibility Schema','PointToObject'),(2,'refRejectionReason','Referral Rejection Reason','default'),(3,'emergency_contact_relationship','Emergency Contact Relationship','Default'),(4,'refUserType','Referral: User Type','default'),(5,'federal_poverty_level','federal_poverty_level','FPL'),(6,'pp_clinic_eligibility','PP Clinic eligibility','Default'),(7,'provider_number_type','Provider Number Type','Default'),(8,'address_type','Address Type','Default'),(9,'assigning','Assigning','Default'),(10,'code_modifier','Code Modifier','Default'),(11,'company_number_type','Company Number Type','Default'),(12,'company_type','Company Type','Default'),(13,'disposition','Disposition','Default'),(14,'encounter_date_type','Encounter Date Type','Default'),(15,'encounter_person_type','Encounter Person Type','Default'),(16,'encounter_value_type','Encounter Value Type','Default'),(17,'ethnicity','Ethnicity','Default'),(18,'gender','Gender','Default'),(19,'system_reports','System Reports','Url'),(20,'group_list','File Groups','Default'),(21,'identifier_type','Identifier Type','Default'),(22,'income','Income','Default'),(23,'language_old','Languages','Default'),(24,'marital_status','Marital Status','Default'),(25,'migrant_status','Migrant Status','Default'),(26,'number_type','Phone Number Type','Default'),(27,'payer_type','Payer Type','Default'),(28,'payment_type','Payment Type','Default'),(29,'person_to_person_relation_type','Person to person relation type','Default'),(30,'person_type','Person Type','PersonType'),(31,'provider_reporting_type','Provider Reporting Type','Default'),(32,'quality_of_file','Quality of File','Default'),(33,'race','Race','Default'),(34,'relation_of_information_code','Relation Of Information Code','Default'),(35,'state','State','Default'),(36,'subscriber_to_patient','Subscriber to patient','Default'),(37,'chronic_care_codes','Chronic Care Codes','Default'),(38,'funds_source','Funds Source','Default'),(39,'refSpecialty','Specialists','Default'),(40,'refEligibility','Referal Eligibility','Default'),(41,'refRequested_time','Referal: Requested Time','Default'),(42,'days','Days of the Week','Default'),(43,'yesNo','Yes or No','Default'),(44,'refStatus','Referral: Status','Default'),(45,'audit_type','Audit Type','Default'),(46,'pcc_employment','PCC Employment','Default'),(47,'pcc_education_level','PCC Education Level','Default'),(48,'yesnounknown','YesNoUnknown','Default'),(49,'pcc_housing','PCC Housing','Default'),(50,'pcc_meds_allergies_form','PCC Meds allergies form','Default'),(51,'active_inactive','Active Inactive','Default'),(52,'subscriber_to_patient_relationship','Subscriber To Patient Relationship','Default'),(53,'pcc_illness_form','PCC Illness Form','Default'),(54,'days_of_week','Days of Week','Default'),(55,'weeks_of_month','Weeks of Month','Default'),(56,'months_of_year','Months of Year','Default'),(57,'recurrence_pattern_type','Recurrence Pattern Type','Default'),(58,'pcc_related_list','PCC Related List','Default'),(59,'confidentiality_levels','Confidentiality Levels','Default'),(60,'billing_mode','Billing Mode','Mappedvalue'),(61,'account_note_type','Account Note Type','Default'),(62,'eob_adjustment_type','Eob Adjustment Type','MappedValue'),(63,'value_type','Value Type','Default'),(64,'confidential_family_planning_codes','Confidential family planning codes','Default'),(65,'confidential_disease_codes','Confidential_disease_codes','Default'),(66,'confidential_family_planning_and_disease_codes','Confidential Family Planning and Disease Codes','ConfidentialFamilyPlanningAndDisease'),(67,'widget_type','Widget Type','Default'),(68,'shelter_type','Shelter Type','Default'),(69,'county','County','Default'),(70,'household_status','Household Status','Default'),(71,'preferred_language','Preferred Language','Default'),(72,'english_proficiency','English Proficiency','Default'),(73,'country_of_origin','Country of Origin','Default'),(74,'religion','Religion','Default'),(75,'employment_status','Employment Status','Default'),(76,'education_level','Education Level','Default'),(77,'us_veteran','US Veteran','Default'),(78,'problem_planned_care_quicklist','Problem Planned Care','Default'),(79,'insurance_type','Insurance type','Default'),(80,'medication_coverage','Medication Coverage','Default'),(81,'allergies','Allergies','Tree'),(82,'immunizations','Immunization Name','Default'),(83,'previous_illness','Previous Illness','Default'),(84,'family_illness','Family Illness','Default'),(85,'relative','Relative','Default'),(86,'transaction','Transaction','Default'),(87,'lab_manual_description_list','Lab Value/Test Name -- LOINC','Default'),(88,'lab_manual_company_list','Lab Provider','Default'),(89,'lab_manual_service_list','Lab Manual Service List','Default'),(90,'referral_service','Referral Services','Default'),(91,'self_mgmt_goals','Self Management Goals','Tree'),(92,'patient_note_reason','Patient Note Reason','Default'),(93,'reason_type','Reason Type','Default'),(94,'encounter_reason','Encounter Reason','Default'),(95,'appointment_reasons','Appointment Reason','AppointmentReason'),(96,'simpleToggle','Simple Toggle','Default'),(97,'reminder_summary_modes','Reminder Summary Modes','Default'),(98,'lab_manual_abnormal_list','Lab Manual Abnormal List','Default'),(99,'dm_group_list','Document Manager Group List','Default'),(100,'clinicadmin_permissions','Clinic Admin Permissions','Default'),(101,'report_time_period','report_time_period','Default');
UNLOCK TABLES;
/*!40000 ALTER TABLE `enumeration_definition` ENABLE KEYS */;

--
-- Table structure for table `enumeration_definition2`
--

DROP TABLE IF EXISTS `enumeration_definition2`;
CREATE TABLE `enumeration_definition2` (
  `enumeration_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `type` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`enumeration_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `enumeration_definition2`
--


/*!40000 ALTER TABLE `enumeration_definition2` DISABLE KEYS */;
LOCK TABLES `enumeration_definition2` WRITE;
INSERT INTO `enumeration_definition2` VALUES (55,'refEligibilitySchema','Referral: Eligibility Schema','PointToObject'),(255,'refRejectionReason','Referral Rejection Reason','default'),(363,'emergency_contact_relationship','Emergency Contact Relationship','Default'),(394,'refUserType','Referral: User Type','default'),(465,'federal_poverty_level','federal_poverty_level','FPL'),(27698,'pp_clinic_eligibility','PP Clinic eligibility','Default'),(300300,'provider_number_type','Provider Number Type','Default'),(300466,'address_type','Address Type','Default'),(300480,'assigning','Assigning','Default'),(300485,'code_modifier','Code Modifier','Default'),(300492,'company_number_type','Company Number Type','Default'),(300495,'company_type','Company Type','Default'),(300497,'disposition','Disposition','Default'),(300501,'encounter_date_type','Encounter Date Type','Default'),(300510,'encounter_person_type','Encounter Person Type','Default'),(300515,'encounter_value_type','Encounter Value Type','Default'),(300521,'ethnicity','Ethnicity','Default'),(300524,'gender','Gender','Default'),(300525,'system_reports','System Reports','Url'),(300528,'group_list','File Groups','Default'),(300532,'identifier_type','Identifier Type','Default'),(300535,'income','Income','Default'),(300540,'language_old','Languages','Default'),(300560,'marital_status','Marital Status','Default'),(300564,'migrant_status','Migrant Status','Default'),(300566,'number_type','Phone Number Type','Default'),(300572,'payer_type','Payer Type','Default'),(300582,'payment_type','Payment Type','Default'),(300589,'person_to_person_relation_type','Person to person relation type','Default'),(300594,'person_type','Person Type','PersonType'),(300602,'provider_reporting_type','Provider Reporting Type','Default'),(300608,'quality_of_file','Quality of File','Default'),(300611,'race','Race','Default'),(300617,'relation_of_information_code','Relation Of Information Code','Default'),(300624,'state','State','Default'),(300677,'subscriber_to_patient','Subscriber to patient','Default'),(300818,'chronic_care_codes','Chronic Care Codes','Default'),(300852,'funds_source','Funds Source','Default'),(513682,'refSpecialty','Specialists','Default'),(513700,'refEligibility','Referal Eligibility','Default'),(513706,'refRequested_time','Referal: Requested Time','Default'),(513718,'days','Days of the Week','Default'),(513726,'yesNo','Yes or No','Default'),(513734,'refStatus','Referral: Status','Default'),(600016,'audit_type','Audit Type','Default'),(600052,'pcc_employment','PCC Employment','Default'),(600078,'pcc_education_level','PCC Education Level','Default'),(600181,'yesnounknown','YesNoUnknown','Default'),(600228,'pcc_housing','PCC Housing','Default'),(600268,'pcc_meds_allergies_form','PCC Meds allergies form','Default'),(600287,'active_inactive','Active Inactive','Default'),(600305,'subscriber_to_patient_relationship','Subscriber To Patient Relationship','Default'),(600306,'pcc_illness_form','PCC Illness Form','Default'),(600331,'days_of_week','Days of Week','Default'),(600339,'weeks_of_month','Weeks of Month','Default'),(600345,'months_of_year','Months of Year','Default'),(600358,'recurrence_pattern_type','Recurrence Pattern Type','Default'),(600488,'pcc_related_list','PCC Related List','Default'),(601227,'confidentiality_levels','Confidentiality Levels','Default'),(602407,'billing_mode','Billing Mode','Mappedvalue'),(604881,'account_note_type','Account Note Type','Default'),(604904,'eob_adjustment_type','Eob Adjustment Type','MappedValue'),(605185,'value_type','Value Type','Default'),(607814,'confidential_family_planning_codes','Confidential family planning codes','Default'),(607816,'confidential_disease_codes','Confidential_disease_codes','Default'),(608378,'confidential_family_planning_and_disease_codes','Confidential Family Planning and Disease Codes','ConfidentialFamilyPlanningAndDisease'),(1080302,'widget_type','Widget Type','Default'),(5691329,'shelter_type','Shelter Type','Default'),(5691388,'county','County','Default'),(5691555,'household_status','Household Status','Default'),(5691596,'preferred_language','Preferred Language','Default'),(5691799,'english_proficiency','English Proficiency','Default'),(5691858,'country_of_origin','Country of Origin','Default'),(5694014,'religion','Religion','Default'),(5694145,'employment_status','Employment Status','Default'),(5694186,'education_level','Education Level','Default'),(5694326,'us_veteran','US Veteran','Default'),(5694358,'problem_planned_care_quicklist','Problem Planned Care','Default'),(5694534,'insurance_type','Insurance type','Default'),(5694593,'medication_coverage','Medication Coverage','Default'),(5694625,'allergies','Allergies','Tree'),(5695629,'immunizations','Immunization Name','Default'),(5695787,'previous_illness','Previous Illness','Default'),(5696008,'family_illness','Family Illness','Default'),(5696229,'relative','Relative','Default'),(5696405,'transaction','Transaction','Default'),(5696523,'custom_diagnoses_quicklist','Custom Diagnoses','PerPractice'),(5697374,'custom_diagnoses2','Custom Diagnoses2','Default'),(5698225,'edu/sns/custom_procedures_quicklist','Custom Procedures','PerPractice'),(5699022,'custom_procedures2','Custom Procedures2','Default'),(5699774,'lab_manual_description_list','Lab Value/Test Name -- LOINC','Default'),(5700085,'lab_manual_company_list','Lab Provider','Default'),(5700117,'lab_manual_service_list','Lab Manual Service List','Default'),(5700158,'chart_status_(new)','Chart Status (new)','Default'),(5700181,'referred_by_(new)','Referred By (new)','Default'),(5700189,'referral_service','Referral Services','Default'),(5700302,'self_mgmt_goals','Self Management Goals','Tree'),(5700613,'patient_note_reason','Patient Note Reason','Default'),(5701146,'reason_type','Reason Type','Default'),(5701249,'','','Default'),(5701254,'encounter_reason','Encounter Reason','Default'),(5701426,'appointment_reasons','Appointment Reason','AppointmentReason'),(27511737,'simpleToggle','Simple Toggle','Default'),(31100126,'reminder_summary_modes','Reminder Summary Modes','Default'),(31106553,'lab_manual_abnormal_list','Lab Manual Abnormal List','Default'),(31143582,'dm_group_list','Document Manager Group List','Default'),(31692906,'clinicadmin_permissions','Clinic Admin Permissions','Default'),(32271991,'report_time_period','report_time_period','Default');
UNLOCK TABLES;
/*!40000 ALTER TABLE `enumeration_definition2` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `enumeration_value`
--


/*!40000 ALTER TABLE `enumeration_value` DISABLE KEYS */;
LOCK TABLES `enumeration_value` WRITE;
INSERT INTO `enumeration_value` VALUES (12,39,'6','Allergy Specialist',0,'','',1,0,0),(13,39,'7','Audiology',1,'','',1,0,0),(26,39,'3','Dermatology',4,'','',1,0,0),(248,39,'4','Gastroenterology',6,'','',1,0,0),(249,39,'5','Chiropractic',3,'','',1,0,0),(325,42,'8','Any Day',8,'','',1,0,0),(380,39,'8','General Surgery',8,'','',1,0,0),(381,39,'9','Gynecology',9,'','',1,0,0),(382,39,'10','Hematology',10,'','',1,0,0),(383,39,'11','Immunology',11,'','',1,0,0),(384,39,'12','Infectious Disease',12,'','',1,0,0),(385,39,'13','Neurology',13,'','',1,0,0),(386,39,'14','Ophthalmology',14,'','',1,0,0),(387,39,'15','Orthopedic Surgery',15,'','',1,0,0),(388,39,'16','Orthopedic Shoe Tech',16,'','',1,0,0),(389,39,'17','Otolaryngology',17,'','',1,0,0),(390,39,'18','Pain Management',18,'','',1,0,0),(391,39,'19','Pathology',19,'','',1,0,0),(392,39,'20','Phys Med/Rehab',20,'','',1,0,0),(393,39,'21','Podiatry',21,'','',1,0,0),(394,39,'22','Pulmonology',22,'','',1,0,0),(395,39,'23','Radiation Oncology',23,'','',1,0,0),(396,39,'24','Rheumatology',24,'','',1,0,0),(397,39,'25','Speech Therapy',25,'','',1,0,0),(398,39,'26','Urology',26,'','',1,0,0),(399,39,'27','Vascular Surgery',27,'','',1,0,0),(3934,5,'1','100',1,'15.00','15.00',1,0,0),(3935,5,'2','110',2,'20.00','20.00',1,0,0),(3936,5,'3','120',3,'20.00','25.00',1,0,0),(3937,5,'4','130',4,'20.00','30.00',1,0,0),(3938,5,'5','140',5,'20.00','35.00',1,0,0),(3939,5,'6','150',6,'20.00','40.00',1,0,0),(3940,5,'7','160',7,'20.00','45.00',1,0,0),(3941,5,'8','170',8,'20.00','50.00',1,0,0),(3942,5,'9','175',9,'20.00','55.00',1,0,0),(3943,5,'10','180',10,'25.00','55.00',1,0,0),(3944,5,'11','190',11,'25.00','60.00',1,0,0),(3945,5,'12','200',12,'25.00','65.00',1,0,0),(3946,5,'13','210',13,'25.00','75.00',1,0,0),(3947,5,'14','220',14,'25.00','80.00',1,0,0),(3948,5,'15','230',15,'25.00','85.00',1,0,0),(3949,5,'16','240',16,'25.00','90.00',1,0,0),(3950,5,'17','250',17,'25.00','95.00',1,0,0),(12325,39,'28','Plastic Surgery',28,'','',1,0,0),(12326,39,'29','Neurological Surgery',29,'','',1,0,0),(12327,39,'30','Retinal Specialist',30,'','',1,0,0),(12328,39,'31','Chinese Medicine',31,'','',1,0,0),(13756,67,'3','Criticals Pallet (QuickList)',4,'','',1,0,0),(25941,39,'32','Oncology',32,'','',1,0,0),(25942,39,'33','Prothesis',33,'','',1,0,0),(33440,67,'10','Disabled',16,'','',1,0,0),(45687,67,'4','Criticals Pallet (Controller)',7,'','',1,0,0),(46756,67,'5','Encounter Tab',10,'','',1,0,0),(49188,67,'6','Other',13,'','',1,0,0),(300013,300012,'1','Hello',0,'','',1,0,0),(300014,300012,'2','World',0,'','',1,0,0),(300016,300015,'1','test',0,'','',1,0,0),(300017,300015,'2','second test',1,'','',1,0,0),(300039,300038,'1','Home',0,'','',1,0,0),(300040,300038,'2','Billing',0,'','',1,0,0),(300041,300038,'3','Other',0,'','',1,0,0),(300042,300038,'4','Main',0,'','',1,0,0),(300043,300038,'5','Secondary',0,'','',1,0,0),(300045,300044,'1','Physical',0,'','',1,0,0),(300046,300044,'2','FP',1,'','',1,0,0),(300047,300044,'3','CDP',2,'','',1,0,0),(300048,300044,'4','CHDP',3,'','',1,0,0),(300049,300044,'5','F/U',4,'','',1,0,0),(300050,300044,'6','Sick',5,'','',1,0,0),(300051,300044,'7','Lab Only',6,'','',1,0,0),(300053,300052,'1','A - Assigned',0,'','',1,0,0),(300054,300052,'2','B - Assigned Lab Services Only',0,'','',1,0,0),(300055,300052,'3','C - Not Assigned',0,'','',1,0,0),(300056,300052,'4','P - Assignment Refused',0,'','',1,0,0),(300058,300057,'1','A0',0,'','',1,0,0),(300059,300057,'2','A1',0,'','',1,0,0),(300060,300057,'3','A2',0,'','',1,0,0),(300061,300057,'4','B1',0,'','',1,0,0),(300062,300057,'5','B2',0,'','',1,0,0),(300063,300057,'6','C6',0,'','',1,0,0),(300065,300064,'1','Primary',0,'','',1,0,0),(300066,300064,'2','Fax',0,'','',1,0,0),(300068,300067,'1','Insurance',0,'','',1,0,0),(300070,300069,'1','New',0,'','',1,0,0),(300071,300069,'2','Waiting',0,'','',1,0,0),(300072,300069,'3','Compete',0,'','',1,0,0),(300074,300073,'1','date_of_death',0,'','',1,0,0),(300075,300073,'2','date_last_seen',0,'','',1,0,0),(300076,300073,'3','date_of_onset',0,'','',1,0,0),(300077,300073,'4','date_of_initial_treatment',0,'','',1,0,0),(300078,300073,'5','date_of_cant_work_start',0,'','',1,0,0),(300079,300073,'6','date_of_cant_work_end',0,'','',1,0,0),(300080,300073,'7','date_of_hospitalization_start',0,'','',1,0,0),(300081,300073,'8','date_of_hospitalization_end',0,'','',1,0,0),(300083,300082,'1','Referring Provider',0,'','',1,0,0),(300085,300084,'1','Physical',0,'','',1,0,0),(300086,300084,'2','Other',0,'','',1,0,0),(300088,300087,'1','medicaid_resubmission_code',0,'','',1,0,0),(300089,300087,'2','prior_authorization_number',0,'','',1,0,0),(300090,300087,'3','auto_accident_state',0,'','',1,0,0),(300091,300087,'4','original_reference_number',0,'','',1,0,0),(300092,300087,'5','hcfa_10d_comment',0,'','',1,0,0),(300094,300093,'1','Hispanic',0,'','',1,0,0),(300095,300093,'2','Caucasian',0,'','',1,0,0),(300097,300096,'1','Male',0,'','',1,0,0),(300098,300096,'2','Female',0,'','',1,0,0),(300099,300096,'3','Unknown',0,'','',1,0,0),(300101,300100,'1','All',0,'','',1,0,0),(300102,300100,'2','Arizona',0,'','',1,0,0),(300103,300100,'3','California',0,'','',1,0,0),(300105,300104,'1','SSN',0,'','',1,0,0),(300106,300104,'2','EIN',0,'','',1,0,0),(300108,300107,'1','Unknown',0,'','',1,0,0),(300109,300107,'2','Under 100% of Poverty',0,'','',1,0,0),(300110,300107,'3','100-200% of Poverty',0,'','',1,0,0),(300111,300107,'4','Above 200% of Poverty',0,'','',1,0,0),(300113,300112,'1','English',0,'','',1,0,0),(300114,300112,'2','Spanish',0,'','',1,0,0),(300115,300112,'3','Chinese',0,'','',1,0,0),(300116,300112,'4','Japanese',0,'','',1,0,0),(300117,300112,'5','Korean',0,'','',1,0,0),(300118,300112,'6','Portuguese',0,'','',1,0,0),(300119,300112,'7','Russian',0,'','',1,0,0),(300120,300112,'8','Sign Language',0,'','',1,0,0),(300121,300112,'9','Vietnamese',0,'','',1,0,0),(300122,300112,'10','Tagalog',0,'','',1,0,0),(300123,300112,'11','Punjabi',0,'','',1,0,0),(300124,300112,'12','Hindustani',0,'','',1,0,0),(300125,300112,'13','Armenian',0,'','',1,0,0),(300126,300112,'14','Arabic',0,'','',1,0,0),(300127,300112,'15','Laotian',0,'','',1,0,0),(300128,300112,'16','Hmong',0,'','',1,0,0),(300129,300112,'17','Cambodian',0,'','',1,0,0),(300130,300112,'18','Finnish',0,'','',1,0,0),(300131,300112,'19','Other',0,'','',1,0,0),(300133,300132,'1','Single',0,'','',1,0,0),(300134,300132,'2','Married',0,'','',1,0,0),(300135,300132,'3','Other',0,'','',1,0,0),(300137,300136,'1','Migrant Worker',0,'','',1,0,0),(300139,300138,'1','Home',0,'','',1,0,0),(300140,300138,'2','Mobile',0,'','',1,0,0),(300141,300138,'3','Work',0,'','',1,0,0),(300142,300138,'4','Emergency',0,'','',1,0,0),(300143,300138,'5','Fax',0,'','',1,0,0),(300145,300144,'1','medicare',0,'','',1,0,0),(300146,300144,'2','champus',0,'','',1,0,0),(300147,300144,'3','medical',0,'','',1,0,0),(300148,300144,'4','private',0,'','',1,0,0),(300149,300144,'5','feca',0,'','',1,0,0),(300150,300144,'6','medicaid',0,'','',1,0,0),(300151,300144,'7','champusva',0,'','',1,0,0),(300152,300144,'8','otherhcfa',0,'','',1,0,0),(300153,300144,'9','litigation',0,'','',1,0,0),(300155,300154,'1','visa',0,'','',1,0,0),(300156,300154,'2','mastercard',0,'','',1,0,0),(300157,300154,'3','amex',0,'','',1,0,0),(300158,300154,'4','check',0,'','',1,0,0),(300159,300154,'5','cash',0,'','',1,0,0),(300160,300154,'6','remittance',0,'','',1,0,0),(300162,300161,'1','Dependant',0,'','',1,0,0),(300163,300161,'2','Spouse',0,'','',1,0,0),(300164,300161,'3','Grand Parent',0,'','',1,0,0),(300165,300161,'4','Other',0,'','',1,0,0),(300167,300166,'1','Patient',0,'','',1,0,0),(300168,300166,'2','Provider',0,'','',1,0,0),(300169,300166,'3','Mid-level',0,'','',1,0,0),(300170,300166,'4','Staff',0,'','',1,0,0),(300171,300166,'5','Subscriber',0,'','',1,0,0),(300173,300172,'1','State License',0,'','',1,0,0),(300175,300174,'1','MD',0,'','',1,0,0),(300176,300174,'2','RNFP',0,'','',1,0,0),(300177,300174,'3','RN',0,'','',1,0,0),(300178,300174,'4','PA',0,'','',1,0,0),(300179,300174,'5','MA',0,'','',1,0,0),(300181,300180,'1','Good',0,'','',1,0,0),(300182,300180,'2','Bad',0,'','',1,0,0),(300184,300183,'1','White/Hispanic',0,'','',1,0,0),(300185,300183,'2','Black',0,'','',1,0,0),(300186,300183,'3','Native American/Alaskan Native',0,'','',1,0,0),(300187,300183,'4','Asian/Pacific Islander',0,'','',1,0,0),(300188,300183,'5','Other/Unknown',0,'','',1,0,0),(300190,300189,'1','A - On file',0,'','',1,0,0),(300191,300189,'2','I - Informed Consent',0,'','',1,0,0),(300192,300189,'3','M - Limited Ability',0,'','',1,0,0),(300193,300189,'4','N - Not allowed',0,'','',1,0,0),(300194,300189,'5','O - On file',0,'','',1,0,0),(300195,300189,'6','Y - Has permission',0,'','',1,0,0),(300197,300196,'1','AL',0,'','',1,0,0),(300198,300196,'2','AK',0,'','',1,0,0),(300199,300196,'3','AZ',0,'','',1,0,0),(300200,300196,'4','AR',0,'','',1,0,0),(300201,300196,'5','CA',0,'','',1,0,0),(300202,300196,'6','CO',0,'','',1,0,0),(300203,300196,'7','CT',0,'','',1,0,0),(300204,300196,'8','DE',0,'','',1,0,0),(300205,300196,'9','DC',0,'','',1,0,0),(300206,300196,'10','FL',0,'','',1,0,0),(300207,300196,'11','GA',0,'','',1,0,0),(300208,300196,'12','HI',0,'','',1,0,0),(300209,300196,'13','ID',0,'','',1,0,0),(300210,300196,'14','IL',0,'','',1,0,0),(300211,300196,'15','IN',0,'','',1,0,0),(300212,300196,'16','IA',0,'','',1,0,0),(300213,300196,'17','KS',0,'','',1,0,0),(300214,300196,'18','KY',0,'','',1,0,0),(300215,300196,'19','LA',0,'','',1,0,0),(300216,300196,'20','ME',0,'','',1,0,0),(300217,300196,'21','MD',0,'','',1,0,0),(300218,300196,'22','MA',0,'','',1,0,0),(300219,300196,'23','MI',0,'','',1,0,0),(300220,300196,'24','MN',0,'','',1,0,0),(300221,300196,'25','MS',0,'','',1,0,0),(300222,300196,'26','MO',0,'','',1,0,0),(300223,300196,'27','MT',0,'','',1,0,0),(300224,300196,'28','NE',0,'','',1,0,0),(300225,300196,'29','NV',0,'','',1,0,0),(300226,300196,'30','NH',0,'','',1,0,0),(300227,300196,'31','NJ',0,'','',1,0,0),(300228,300196,'32','NM',0,'','',1,0,0),(300229,300196,'33','NY',0,'','',1,0,0),(300230,300196,'34','NC',0,'','',1,0,0),(300232,300196,'36','OH',0,'','',1,0,0),(300233,300196,'37','OK',0,'','',1,0,0),(300234,300196,'38','OR',0,'','',1,0,0),(300235,300196,'39','PA',0,'','',1,0,0),(300236,300196,'40','RI',0,'','',1,0,0),(300237,300196,'41','SC',0,'','',1,0,0),(300238,300196,'42','SD',0,'','',1,0,0),(300239,300196,'43','TN',0,'','',1,0,0),(300240,300196,'44','TX',0,'','',1,0,0),(300241,300196,'45','UT',0,'','',1,0,0),(300242,300196,'46','VT',0,'','',1,0,0),(300243,300196,'47','VA',0,'','',1,0,0),(300244,300196,'48','WA',0,'','',1,0,0),(300245,300196,'49','WV',0,'','',1,0,0),(300246,300196,'50','WI',0,'','',1,0,0),(300247,300196,'51','WY',0,'','',1,0,0),(300248,300196,'52','PR',0,'','',1,0,0),(300250,300249,'1','Spouse',0,'','',1,0,0),(300251,300249,'2','Parent',0,'','',1,0,0),(300253,300252,'1','Home',0,'','',1,0,0),(300254,300252,'2','Billing',0,'','',1,0,0),(300255,300252,'3','Other',0,'','',1,0,0),(300259,300258,'1','Physical',0,'','',1,0,0),(300260,300258,'2','FP',0,'','',1,0,0),(300261,300258,'3','CDP',0,'','',1,0,0),(300262,300258,'4','CHDP',0,'','',1,0,0),(300263,300258,'5','F/U',0,'','',1,0,0),(300264,300258,'6','Sick',0,'','',1,0,0),(300265,300258,'7','Lab Only',0,'','',1,0,0),(300267,300266,'1','A - Assigned',0,'','',1,0,0),(300291,300287,'4','date_of_initial_treatment',0,'','',1,0,0),(300292,300287,'5','date_of_cant_work_start',0,'','',1,0,0),(300293,300287,'6','date_of_cant_work_end',0,'','',1,0,0),(300294,300287,'7','date_of_hospitalization_start',0,'','',1,0,0),(300295,300287,'8','date_of_hospitalization_end',0,'','',1,0,0),(300297,300296,'1','Referring Provider',0,'','',1,0,0),(300299,300298,'1','Physical',0,'','',1,0,0),(300300,300298,'2','Other',0,'','',1,0,0),(300302,300301,'1','medicaid_resubmission_code',0,'','',1,0,0),(300306,300301,'5','hcfa_10d_comment',0,'','',1,0,0),(300308,300307,'1','Hispanic',0,'','',1,0,0),(300309,300307,'2','Caucasian',0,'','',1,0,0),(300311,300310,'1','Male',0,'','',1,0,0),(300312,300310,'2','Female',0,'','',1,0,0),(300313,300310,'3','Unknown',0,'','',1,0,0),(300315,300314,'1','All',0,'','',1,0,0),(300316,300314,'2','Arizona',0,'','',1,0,0),(300317,300314,'3','California',0,'','',1,0,0),(300319,300318,'1','SSN',0,'','',1,0,0),(300320,300318,'2','EIN',0,'','',1,0,0),(300322,300321,'1','Unknown',0,'','',1,0,0),(300323,300321,'2','Under 100% of Poverty',0,'','',1,0,0),(300324,300321,'3','100-200% of Poverty',0,'','',1,0,0),(300325,300321,'4','Above 200% of Poverty',0,'','',1,0,0),(300327,300326,'1','English',0,'','',1,0,0),(300328,300326,'2','Spanish',0,'','',1,0,0),(300329,300326,'3','Chinese',0,'','',1,0,0),(300330,300326,'4','Japanese',0,'','',1,0,0),(300331,300326,'5','Korean',0,'','',1,0,0),(300332,300326,'6','Portuguese',0,'','',1,0,0),(300333,300326,'7','Russian',0,'','',1,0,0),(300334,300326,'8','Sign Language',0,'','',1,0,0),(300335,300326,'9','Vietnamese',0,'','',1,0,0),(300336,300326,'10','Tagalog',0,'','',1,0,0),(300337,300326,'11','Punjabi',0,'','',1,0,0),(300338,300326,'12','Hindustani',0,'','',1,0,0),(300339,300326,'13','Armenian',0,'','',1,0,0),(300340,300326,'14','Arabic',0,'','',1,0,0),(300341,300326,'15','Laotian',0,'','',1,0,0),(300342,300326,'16','Hmong',0,'','',1,0,0),(300343,300326,'17','Cambodian',0,'','',1,0,0),(300344,300326,'18','Finnish',0,'','',1,0,0),(300347,300346,'1','Single',0,'','',1,0,0),(300348,300346,'2','Married',0,'','',1,0,0),(300349,300346,'3','Other',0,'','',1,0,0),(300351,300350,'1','Migrant Worker',0,'','',1,0,0),(300353,300352,'1','Home',0,'','',1,0,0),(300354,300352,'2','Mobile',0,'','',1,0,0),(300355,300352,'3','Work',0,'','',1,0,0),(300356,300352,'4','Emergency',0,'','',1,0,0),(300357,300352,'5','Fax',0,'','',1,0,0),(300359,300358,'1','medicare',0,'','',1,0,0),(300360,300358,'2','champus',0,'','',1,0,0),(300361,300358,'3','medical',0,'','',1,0,0),(300362,300358,'4','private',0,'','',1,0,0),(300363,300358,'5','feca',0,'','',1,0,0),(300364,300358,'6','medicaid',0,'','',1,0,0),(300365,300358,'7','champusva',0,'','',1,0,0),(300366,300358,'8','otherhcfa',0,'','',1,0,0),(300367,300358,'9','litigation',0,'','',1,0,0),(300369,300368,'1','visa',0,'','',1,0,0),(300370,300368,'2','mastercard',0,'','',1,0,0),(300371,300368,'3','amex',0,'','',1,0,0),(300372,300368,'4','check',0,'','',1,0,0),(300373,300368,'5','cash',0,'','',1,0,0),(300374,300368,'6','remittance',0,'','',1,0,0),(300376,300375,'1','Dependant',0,'','',1,0,0),(300377,300375,'2','Spouse',0,'','',1,0,0),(300378,300375,'3','Grand Parent',0,'','',1,0,0),(300379,300375,'4','Other',0,'','',1,0,0),(300381,300380,'1','Patient',0,'','',1,0,0),(300382,300380,'2','Provider',0,'','',1,0,0),(300383,300380,'3','Mid-level',0,'','',1,0,0),(300384,300380,'4','Staff',0,'','',1,0,0),(300385,300380,'5','Subscriber',0,'','',1,0,0),(300387,300386,'1','State License',0,'','',1,0,0),(300389,300388,'1','MD',0,'','',1,0,0),(300390,300388,'2','RNFP',0,'','',1,0,0),(300391,300388,'3','RN',0,'','',1,0,0),(300392,300388,'4','PA',0,'','',1,0,0),(300393,300388,'5','MA',0,'','',1,0,0),(300395,300394,'1','Good',0,'','',1,0,0),(300396,300394,'2','Bad',0,'','',1,0,0),(300398,300397,'1','White/Hispanic',0,'','',1,0,0),(300399,300397,'2','Black',0,'','',1,0,0),(300400,300397,'3','Native American/Alaskan Native',0,'','',1,0,0),(300401,300397,'4','Asian/Pacific Islander',0,'','',1,0,0),(300402,300397,'5','Other/Unknown',0,'','',1,0,0),(300404,300403,'1','A - On file',0,'','',1,0,0),(300405,300403,'2','I - Informed Consent',0,'','',1,0,0),(300406,300403,'3','M - Limited Ability',0,'','',1,0,0),(300407,300403,'4','N - Not allowed',0,'','',1,0,0),(300408,300403,'5','O - On file',0,'','',1,0,0),(300409,300403,'6','Y - Has permission',0,'','',1,0,0),(300411,300410,'1','AL',0,'','',1,0,0),(300412,300410,'2','AK',0,'','',1,0,0),(300413,300410,'3','AZ',0,'','',1,0,0),(300414,300410,'4','AR',0,'','',1,0,0),(300415,300410,'5','CA',0,'','',1,0,0),(300416,300410,'6','CO',0,'','',1,0,0),(300417,300410,'7','CT',0,'','',1,0,0),(300418,300410,'8','DE',0,'','',1,0,0),(300419,300410,'9','DC',0,'','',1,0,0),(300420,300410,'10','FL',0,'','',1,0,0),(300421,300410,'11','GA',0,'','',1,0,0),(300422,300410,'12','HI',0,'','',1,0,0),(300423,300410,'13','ID',0,'','',1,0,0),(300424,300410,'14','IL',0,'','',1,0,0),(300425,300410,'15','IN',0,'','',1,0,0),(300426,300410,'16','IA',0,'','',1,0,0),(300427,300410,'17','KS',0,'','',1,0,0),(300428,300410,'18','KY',0,'','',1,0,0),(300429,300410,'19','LA',0,'','',1,0,0),(300430,300410,'20','ME',0,'','',1,0,0),(300431,300410,'21','MD',0,'','',1,0,0),(300432,300410,'22','MA',0,'','',1,0,0),(300433,300410,'23','MI',0,'','',1,0,0),(300434,300410,'24','MN',0,'','',1,0,0),(300435,300410,'25','MS',0,'','',1,0,0),(300436,300410,'26','MO',0,'','',1,0,0),(300437,300410,'27','MT',0,'','',1,0,0),(300438,300410,'28','NE',0,'','',1,0,0),(300439,300410,'29','NV',0,'','',1,0,0),(300440,300410,'30','NH',0,'','',1,0,0),(300441,300410,'31','NJ',0,'','',1,0,0),(300442,300410,'32','NM',0,'','',1,0,0),(300443,300410,'33','NY',0,'','',1,0,0),(300444,300410,'34','NC',0,'','',1,0,0),(300445,300410,'35','ND',0,'','',1,0,0),(300446,300410,'36','OH',0,'','',1,0,0),(300447,300410,'37','OK',0,'','',1,0,0),(300448,300410,'38','OR',0,'','',1,0,0),(300449,300410,'39','PA',0,'','',1,0,0),(300450,300410,'40','RI',0,'','',1,0,0),(300451,300410,'41','SC',0,'','',1,0,0),(300452,300410,'42','SD',0,'','',1,0,0),(300453,300410,'43','TN',0,'','',1,0,0),(300454,300410,'44','TX',0,'','',1,0,0),(300455,300410,'45','UT',0,'','',1,0,0),(300456,300410,'46','VT',0,'','',1,0,0),(300457,300410,'47','VA',0,'','',1,0,0),(300458,300410,'48','WA',0,'','',1,0,0),(300459,300410,'49','WV',0,'','',1,0,0),(300460,300410,'50','WI',0,'','',1,0,0),(300461,300410,'51','WY',0,'','',1,0,0),(300462,300410,'52','PR',0,'','',1,0,0),(300464,300463,'1','Spouse',0,'','',1,0,0),(300465,300463,'2','Parent',0,'','',1,0,0),(300467,8,'2','Home',0,'','',1,0,0),(300468,8,'1','Billing',1,'','',1,0,0),(300469,8,'3','Other',2,'','',1,0,0),(300470,8,'4','Main',3,'','',1,0,0),(300471,8,'5','Secondary',4,'','',1,0,0),(300481,9,'1','A - Assigned',0,'','',1,0,0),(300482,9,'2','B - Assigned Lab Services Only',0,'','',1,0,0),(300483,9,'3','C - Not Assigned',0,'','',1,0,0),(300484,9,'4','P - Assignment Refused',0,'','',1,0,0),(300486,10,'1','A0',0,'','',1,0,0),(300487,10,'2','A1',0,'','',1,0,0),(300488,10,'3','A2',0,'','',1,0,0),(300489,10,'4','B1',0,'','',1,0,0),(300490,10,'5','B2',0,'','',1,0,0),(300491,10,'6','C6',0,'','',1,0,0),(300493,11,'1','Primary',0,'','',1,0,0),(300494,11,'2','Fax',0,'','',1,0,0),(300496,12,'1','Insurance',0,'','',1,0,0),(300498,13,'1','New',0,'','',1,0,0),(300499,13,'2','Waiting',0,'','',1,0,0),(300500,13,'3','Compete',0,'','',1,0,0),(300502,14,'1','date_of_death',0,'','',1,0,0),(300503,14,'2','date_last_seen',0,'','',1,0,0),(300504,14,'3','date_of_onset',0,'','',1,0,0),(300505,14,'4','date_of_initial_treatment',0,'','',1,0,0),(300506,14,'5','date_of_cant_work_start',0,'','',1,0,0),(300507,14,'6','date_of_cant_work_end',0,'','',1,0,0),(300508,14,'7','date_of_hospitalization_start',0,'','',1,0,0),(300509,14,'8','date_of_hospitalization_end',0,'','',1,0,0),(300511,15,'1','Referring Provider',0,'','',1,0,0),(300516,16,'1','medicaid_resubmission_code',0,'','',1,0,0),(300517,16,'2','prior_authorization_number',0,'','',1,0,0),(300518,16,'3','auto_accident_state',0,'','',1,0,0),(300519,16,'4','original_reference_number',0,'','',1,0,0),(300520,16,'5','hcfa_10d_comment',0,'','',1,0,0),(300522,17,'1','Hispanic or Latino',1,'','',1,0,0),(300523,17,'2','Blank',2,'','',0,0,0),(300525,18,'1','Male',0,'','',1,0,0),(300526,19,'1','Patient Statement',0,'/Patient/statement','',1,0,0),(300527,18,'3','Unknown',2,'','',1,0,0),(300529,20,'1','All',0,'','',1,0,0),(300530,20,'2','Arizona',0,'','',1,0,0),(300531,20,'3','California',0,'','',1,0,0),(300533,21,'1','SSN',1,'','',1,0,0),(300534,21,'2','EIN',2,'','',1,0,0),(300536,22,'1','Unknown',0,'','',1,0,0),(300537,22,'2','Under 100% of Poverty',0,'','',1,0,0),(300538,22,'3','100-200% of Poverty',0,'','',1,0,0),(300539,22,'4','Above 200% of Poverty',0,'','',1,0,0),(300541,23,'1','English',1,'','',1,0,0),(300542,23,'2','Spanish',1,'','',1,0,0),(300543,23,'3','Chinese',2,'','',1,0,0),(300544,23,'4','Japanese',3,'','',1,0,0),(300545,23,'5','Korean',4,'','',1,0,0),(300546,23,'6','Portuguese',5,'','',1,0,0),(300547,23,'7','Russian',6,'','',1,0,0),(300548,23,'8','Sign Language',7,'','',1,0,0),(300549,23,'9','Vietnamese',8,'','',1,0,0),(300550,23,'10','Tagalog',9,'','',1,0,0),(300551,23,'11','Punjabi',10,'','',1,0,0),(300552,23,'12','Hindustani',11,'','',1,0,0),(300553,23,'13','Armenian',12,'','',1,0,0),(300554,23,'14','Arabic',13,'','',1,0,0),(300555,23,'15','Laotian',14,'','',1,0,0),(300556,23,'16','Hmong',15,'','',1,0,0),(300557,23,'17','Cambodian',16,'','',1,0,0),(300558,23,'18','Finnish',17,'','',1,0,0),(300559,23,'19','Other',18,'','',1,0,0),(300561,24,'1','Single',5,'','',1,0,0),(300562,24,'2','Married',2,'','',1,0,0),(300563,24,'3','Separated',4,'','',1,0,0),(300565,25,'1','Migrant Worker',0,'','',1,0,0),(300567,26,'1','Home',1,'','',1,0,0),(300568,26,'2','Mobile',1,'','',1,0,0),(300569,26,'3','Work',2,'','',1,0,0),(300570,26,'4','Emergency',3,'','',1,0,0),(300571,26,'5','Fax',4,'','',1,0,0),(300573,27,'1','medicare',0,'','',1,0,0),(300574,27,'2','champus',2,'','',1,0,0),(300575,27,'3','medical',3,'','',1,0,0),(300576,27,'4','private pay',4,'','',1,0,0),(300577,27,'5','feca',5,'','',1,0,0),(300578,27,'6','medicaid',6,'','',1,0,0),(300579,27,'7','champusva',7,'','',1,0,0),(300580,27,'8','otherhcfa',8,'','',1,0,0),(300581,27,'9','litigation',9,'','',1,0,0),(300583,28,'1','visa',6,'','',0,0,0),(300584,28,'2','mastercard',5,'','',0,0,0),(300585,28,'3','amex',7,'','',0,0,0),(300586,28,'4','check',8,'','',0,0,0),(300587,28,'5','cash',9,'','',0,0,0),(300588,28,'6','remittance',10,'','',0,0,0),(300590,29,'1','Dependant',1,'','',1,0,0),(300591,29,'2','Spouse',1,'','',1,0,0),(300592,29,'3','Grand Parent',2,'','',1,0,0),(300593,29,'4','Other',3,'','',1,0,0),(300595,30,'1','Patient',0,'0','',1,0,0),(300596,30,'2','Provider',1,'1','',1,0,0),(300597,30,'3','Mid-level',2,'0','',1,0,0),(300598,30,'4','Staff',3,'0','',1,0,0),(300599,30,'5','Subscriber',4,'0','',1,0,0),(300601,7,'1','State License',0,'','',1,0,0),(300603,31,'1','MD',0,'','',1,0,0),(300604,31,'2','RNFP',0,'','',1,0,0),(300605,31,'3','RN',0,'','',1,0,0),(300606,31,'4','PA',0,'','',1,0,0),(300607,31,'5','MA',0,'','',1,0,0),(300609,32,'1','Good',0,'','',1,0,0),(300610,32,'2','Bad',0,'','',1,0,0),(300612,33,'1','American Indian or Alaska Native',1,'','',1,0,0),(300613,33,'2','Asian',1,'','',1,0,0),(300614,33,'3','Black or African American',2,'','',1,0,0),(300615,33,'4','Native Hawaiian or other Pacific Islander',3,'','',1,0,0),(300616,33,'5','White',4,'','',1,0,0),(300618,34,'1','A - On file',0,'','',1,0,0),(300619,34,'2','I - Informed Consent',0,'','',1,0,0),(300620,34,'3','M - Limited Ability',0,'','',1,0,0),(300621,34,'4','N - Not allowed',0,'','',1,0,0),(300622,34,'5','O - On file',0,'','',1,0,0),(300623,34,'6','Y - Has permission',0,'','',1,0,0),(300625,35,'1','AL',1,'','',1,0,0),(300626,35,'2','AK',1,'','',1,0,0),(300627,35,'3','AZ',2,'','',1,0,0),(300628,35,'4','AR',3,'','',1,0,0),(300629,35,'5','CA',4,'','',1,0,0),(300630,35,'6','CO',5,'','',1,0,0),(300631,35,'7','CT',6,'','',1,0,0),(300632,35,'8','DE',7,'','',1,0,0),(300633,35,'9','DC',8,'','',1,0,0),(300634,35,'10','FL',9,'','',1,0,0),(300635,35,'11','GA',10,'','',1,0,0),(300636,35,'12','HI',11,'','',1,0,0),(300637,35,'13','ID',12,'','',1,0,0),(300638,35,'14','IL',13,'','',1,0,0),(300639,35,'15','IN',14,'','',1,0,0),(300640,35,'16','IA',15,'','',1,0,0),(300641,35,'17','KS',16,'','',1,0,0),(300642,35,'18','KY',17,'','',1,0,0),(300643,35,'19','LA',18,'','',1,0,0),(300644,35,'20','ME',19,'','',1,0,0),(300645,35,'21','MD',20,'','',1,0,0),(300646,35,'22','MA',21,'','',1,0,0),(300647,35,'23','MI',22,'','',1,0,0),(300648,35,'24','MN',23,'','',1,0,0),(300649,35,'25','MS',24,'','',1,0,0),(300650,19,'2','Family Patient Statement',1,'/Patient/familyStatement','',1,0,0),(300651,19,'3','Pull List',2,'/Appointment/pullList','',1,0,0),(300659,35,'35','ND',26,'','',1,0,0),(300660,35,'36','OH',27,'','',1,0,0),(300661,35,'37','OK',28,'','',1,0,0),(300662,35,'38','OR',29,'','',1,0,0),(300663,35,'39','PA',30,'','',1,0,0),(300664,35,'40','RI',31,'','',1,0,0),(300665,35,'41','SC',32,'','',1,0,0),(300666,35,'42','SD',33,'','',1,0,0),(300667,35,'43','TN',34,'','',1,0,0),(300668,35,'44','TX',35,'','',1,0,0),(300669,35,'45','UT',36,'','',1,0,0),(300670,35,'46','VT',37,'','',1,0,0),(300671,35,'47','VA',38,'','',1,0,0),(300672,35,'48','WA',39,'','',1,0,0),(300673,35,'49','WV',40,'','',1,0,0),(300674,35,'50','WI',41,'','',1,0,0),(300675,35,'51','WY',42,'','',1,0,0),(300676,35,'52','PR',43,'','',1,0,0),(300678,36,'1','Spouse',0,'','',1,0,0),(300679,36,'2','Parent',0,'','',1,0,0),(300747,19,'4','Route Slip',3,'/Encounter/routeSlip','',1,0,0),(300819,37,'1','Diabetes',0,'','',0,0,0),(300820,37,'2','Hypertension',2,'','',0,0,0),(300853,38,'1','Patient',0,'','',1,0,0),(300854,38,'2','Private Insurance',0,'','',1,0,0),(300855,38,'3','State Program',0,'','',1,0,0),(300856,38,'4','Federal Program',0,'','',1,0,0),(300932,37,'3','hrt',1,'','',0,0,0),(301504,8,'6','Employer',5,'','',1,0,0),(301505,18,'2','Female',0,'','',1,0,0),(301507,17,'3','Not Hispanic or Latino',1,'','',1,0,0),(301508,27,'10','private insurance',1,'','',1,0,0),(301522,25,'2','Seasonal Worker',0,'','',1,0,0),(301523,25,'3','No',0,'','',1,0,0),(301524,25,'4','other',0,'','',1,0,0),(301538,37,'4','Hypercholestrolemia',0,'','',0,0,0),(513683,39,'1','Endocrinology',5,'','',1,0,0),(513684,39,'2','Cardiology',2,'','',1,0,0),(513707,41,'1','8:00 AM - Noon',0,'','',1,0,0),(513708,41,'2','10:00 AM - 2:00 PM',1,'','',1,0,0),(513709,41,'3','Noon - 4:00 PM',2,'','',1,0,0),(513710,41,'4','2:00 PM - 6:00 PM',3,'','',1,0,0),(513711,41,'5','4:00 PM - 8:00 PM',4,'','',1,0,0),(513712,41,'6','Evening',5,'','',1,0,0),(513719,42,'1','Monday',1,'','',1,0,0),(513720,42,'2','Tuesday',1,'','',1,0,0),(513721,42,'3','Wednesday',2,'','',1,0,0),(513722,42,'4','Thursday',3,'','',1,0,0),(513723,42,'5','Friday',4,'','',1,0,0),(513724,42,'6','Saturday',5,'','',1,0,0),(513725,42,'7','Sunday',6,'','',1,0,0),(513735,44,'1','Requested',1,'','',1,0,0),(513736,44,'2','Requested / Eligibility Pending',0,'Requested / Elig. Pending','',1,0,0),(513737,44,'3','Appointment Pending',2,'Appt Pending','',1,0,0),(513738,44,'4','Appointment Confirmed',3,'Appt Confirmed','',1,0,0),(513739,44,'5','Appointment Kept',4,'Appt Kept','',1,0,0),(513740,44,'6','Appointment No-Show',5,'Appt No-Show','',1,0,0),(513741,44,'7','Returned',6,'','',1,0,0),(513742,44,'8','Canceled',7,'','',0,0,0),(600021,45,'1','insert',1,'','',1,0,0),(600028,45,'2','update',2,'','',1,0,0),(600035,45,'3','delete',3,'','',1,0,0),(600042,45,'4','process',4,'','',1,0,0),(600057,46,'1','Employed',1,'','',1,0,0),(600064,46,'2','Unemployed',2,'','',1,0,0),(600071,46,'3','Unknown',3,'','',1,0,0),(600083,47,'1','Unknown',1,'','',1,0,0),(600090,47,'2','None - Illiterate',2,'','',1,0,0),(600097,47,'3','Some Elementary',3,'','',1,0,0),(600102,27,'11','MPC',11,'','',1,0,0),(600104,47,'4','Some Middle School',4,'','',1,0,0),(600109,27,'12','PCMI',12,'','',1,0,0),(600111,47,'5','Some High School',5,'','',1,0,0),(600116,27,'13','DCHCA',13,'','',1,0,0),(600118,47,'6','High School Degree',6,'','',1,0,0),(600123,27,'14','MCCP',14,'','',1,0,0),(600125,47,'7','Vocational/Tech School',7,'','',1,0,0),(600130,27,'15','CFK',15,'','',1,0,0),(600132,47,'8','Some College',8,'','',1,0,0),(600137,27,'16','None',16,'','',1,0,0),(600139,47,'9','Associates Degree',9,'','',1,0,0),(600146,47,'10','Bachelors Degree',10,'','',1,0,0),(600153,47,'11','Post Grad College',11,'','',1,0,0),(600160,47,'12','Masters Degree',12,'','',1,0,0),(600167,47,'13','Advanced Degree',13,'','',1,0,0),(600174,47,'14','Other',14,'','',1,0,0),(600186,48,'1','Yes',1,'','',1,0,0),(600193,48,'2','No',2,'','',1,0,0),(600200,48,'3','Unknown',3,'','',1,0,0),(600207,48,'4','-- Not Entered --',0,'','',1,0,0),(600214,46,'4','-- Not Entered --',0,'','',1,0,0),(600221,47,'15','-- Not Entered --',0,'','',1,0,0),(600233,49,'5','-- Not Entered --',0,'','',1,0,0),(600240,49,'1','Home',1,'','',1,0,0),(600247,49,'2','Apt',2,'','',1,0,0),(600254,49,'3','Other',3,'','',1,0,0),(600261,49,'4','Unknown',4,'','',1,0,0),(600273,50,'1','meds',1,'','',1,0,0),(600280,50,'2','other',2,'','',1,0,0),(600292,51,'1','Active',1,'','',1,0,0),(600299,51,'2','Inactive',2,'','',1,0,0),(600311,53,'1','HIV/AIDS',1,'','',1,0,0),(600318,53,'2','Anemia',2,'','',1,0,0),(600325,53,'3','Arthritis',3,'','',1,0,0),(600332,53,'4','Asthma',4,'','',1,0,0),(600333,54,'1','Monday',1,'','',1,0,0),(600334,54,'2','Tuesday',1,'','',1,0,0),(600335,54,'3','Wednesday',2,'','',1,0,0),(600336,54,'4','Thursday',3,'','',1,0,0),(600337,54,'5','Friday',4,'','',1,0,0),(600338,54,'6','Saturday',5,'','',1,0,0),(600339,53,'5','Cancer',5,'','',1,0,0),(600340,55,'First','First',0,'','',1,0,0),(600341,55,'Second','Second',1,'','',1,0,0),(600342,55,'Third','Third',2,'','',1,0,0),(600343,55,'Fourth','Fourth',3,'','',1,0,0),(600344,55,'Last','Last',4,'','',1,0,0),(600346,53,'6','Diabetes',6,'','',1,0,0),(600347,56,'02','February',1,'','',1,0,0),(600348,56,'03','March',2,'','',1,0,0),(600349,56,'04','April',3,'','',1,0,0),(600350,56,'05','May',4,'','',1,0,0),(600351,56,'06','June',5,'','',1,0,0),(600352,56,'07','July',6,'','',1,0,0),(600353,53,'7','Emotional Prob.',7,'','',1,0,0),(600354,56,'09','September',8,'','',1,0,0),(600355,56,'10','October',9,'','',1,0,0),(600356,56,'11','November',10,'','',1,0,0),(600357,56,'12','December',11,'','',1,0,0),(600359,57,'day','By Day (Every 3 Days)',0,'','',1,0,0),(600360,53,'8','TB skin test?',8,'','',1,0,0),(600361,57,'monthday','By Day of Month (Every Fifth)',0,'','',1,0,0),(600362,57,'yearmonthday','By Day of Month Per Year (Every December 3rd)',0,'','',1,0,0),(600363,57,'yearmonthweek','By Weekday Per Month Per Year (Every Third Tuesday of November)',0,'','',1,0,0),(600367,53,'9','Gallbladder',9,'','',1,0,0),(600374,53,'10','Heart Problems',10,'','',1,0,0),(600381,53,'11','Hepatitis/Liver',11,'','',1,0,0),(600388,53,'12','High Blood Pressure',12,'','',1,0,0),(600395,53,'13','High Cholesterol',13,'','',1,0,0),(600402,53,'14','Kidney Problems',14,'','',1,0,0),(600409,53,'15','Lung Problems',15,'','',1,0,0),(600416,53,'16','Allergies',16,'','',1,0,0),(600423,53,'17','Menstral Problems',17,'','',1,0,0),(600430,53,'18','Rhenmatic Fever',18,'','',1,0,0),(600437,53,'19','Sexually transmitted disease',19,'','',1,0,0),(600444,53,'20','Stomach Problems',20,'','',1,0,0),(600451,53,'21','Stroke',21,'','',1,0,0),(600458,53,'22','Thyroid Problems',22,'','',1,0,0),(600465,53,'23','Tuberculosis',23,'','',1,0,0),(600493,58,'1','AUNT or UNCLE',1,'','',1,0,0),(600500,58,'2','CHILD IS A PARENT',2,'','',1,0,0),(600507,58,'3','CHILD(ADOPTIVE)',3,'','',1,0,0),(600514,58,'4','CHILD(BIOLOGICAL)',4,'','',1,0,0),(600521,58,'5','CHILD(FOSTER)',5,'','',1,0,0),(600528,58,'6','CHILD(NON-DISCRETIO',6,'','',1,0,0),(600535,58,'7','COUSIN',7,'','',1,0,0),(600542,58,'8','DAYCARE ASSISTANT',8,'','',1,0,0),(600549,58,'9','DAYCARE PROVIDER',9,'','',1,0,0),(600556,58,'10','FIRST COUSIN',10,'','',1,0,0),(600563,58,'11','GRANDCHILD or',11,'','',1,0,0),(600570,58,'12','GRANDPARENT(ADOPTI',12,'','',1,0,0),(600577,58,'13','GRANDPARENT(BIOLOG',13,'','',1,0,0),(600584,58,'14','GRANDPARENT(NON-DI',14,'','',1,0,0),(600591,58,'15','HALF SIBLING',15,'','',1,0,0),(600598,58,'16','LEGAL GUARDIAN',16,'','',1,0,0),(600605,58,'17','NIECE OR NEPHEW',17,'','',1,0,0),(600612,58,'18','NON PARENT SPOUSE',18,'','',1,0,0),(600619,58,'19','NON-RELATED ADULT',19,'','',1,0,0),(600626,58,'20','OTHER',20,'','',1,0,0),(600633,58,'21','RELATED OR',21,'','',1,0,0),(600640,58,'22','OTHER PARENT',22,'','',1,0,0),(600647,58,'23','OTHER RELATED',23,'','',1,0,0),(600654,58,'24','PARAMOUR',24,'','',1,0,0),(600661,58,'25','PARENT(ADOPTIVE)',25,'','',1,0,0),(600668,58,'26','PARENT(BIOLOGICAL)',26,'','',1,0,0),(600675,58,'27','PARENT(FOSTER)',27,'','',1,0,0),(600682,58,'28','PARENT(NON-DISCRETI',28,'','',1,0,0),(600689,58,'29','PARENT(STEP)',29,'','',1,0,0),(600696,58,'30','SELF',30,'','',1,0,0),(600703,58,'31','SIBLING(ADOPTIVE)',31,'','',1,0,0),(600710,58,'32','SIBLING(BIOLOGICAL)',32,'','',1,0,0),(600717,58,'33','SIBLING(FOSTER)',33,'','',1,0,0),(600724,58,'34','SIBLING(NON-DISCRETI',34,'','',1,0,0),(600731,58,'35','SPOUSE',35,'','',1,0,0),(600738,58,'36','STEP CHILD',36,'','',1,0,0),(600745,58,'37','STEP SIBLING',37,'','',1,0,0),(600752,58,'38','UNRELATED CHILD',38,'','',1,0,0),(600759,56,'01','January',0,'','',1,0,0),(600778,56,'08','August',7,'','',1,0,0),(601228,59,'1','1 - No Special Restrictions',1,'','',1,0,0),(601229,59,'2','2 - Basic Confidentiality',1,'','',1,0,0),(601230,59,'3','3 - Family Planning',2,'','',1,0,0),(601231,59,'4','4 - Disease Confidentiality',3,'','',1,0,0),(601232,59,'5','6 - Extreme Confidentiality',5,'','',1,0,0),(602408,60,'0','Production',0,'P','',1,0,0),(602409,60,'1','Testing',0,'T','',1,0,0),(604886,61,'1','X12',0,'','',1,0,0),(604895,61,'2','Batch',1,'','',1,0,0),(604909,62,'1','Deductible Amount',1,'1','',1,0,0),(604918,62,'2','Coinsurance Amount',2,'2','',1,0,0),(604927,62,'3','Co-payment Amount',3,'3','',1,0,0),(604936,62,'4','The procedure code is inconsistent with the modifier used or a required modifier is missing.',4,'4','',1,0,0),(604945,62,'5','The procedure code/bill type is inconsistent with the place of service.',5,'5','',1,0,0),(604954,62,'6','The procedure/revenue code is inconsistent with the patient\'s age.',6,'6','',1,0,0),(604963,62,'7','The procedure/revenue code is inconsistent with the patient\'s gender.',7,'7','',1,0,0),(604972,62,'8','The procedure code is inconsistent with the provider type/specialty (taxonomy).',8,'8','',1,0,0),(604981,62,'9','The diagnosis is inconsistent with the patient\'s age.',9,'9','',1,0,0),(604990,62,'10','The diagnosis is inconsistent with the patient\'s gender.',10,'10','',1,0,0),(604999,62,'11','The diagnosis is inconsistent with the procedure.',11,'11','',1,0,0),(605008,62,'12','The diagnosis is inconsistent with the provider type.',12,'12','',1,0,0),(605017,62,'13','The date of death precedes the date of service.',13,'13','',1,0,0),(605026,62,'14','The date of birth follows the date of service.',14,'14','',1,0,0),(605035,62,'15','Payment adjusted because the submitted authorization number is missin',15,'15','',1,0,0),(605044,62,'16','Claim/service lacks information which is needed for adjudication. Additional information is supplied using remittance advice remarks codes whenever appropriate',16,'16','',1,0,0),(605053,62,'17','Payment adjusted because requested information was not provided or was insufficient/incomplete. Additional information is supplied using the remittance advice remarks codes whenever appropriate.',17,'17','',1,0,0),(605062,62,'18','Duplicate claim/service.',18,'18','',1,0,0),(605071,62,'19','Claim denied because this is a work-related injury/illness and thus the liability of the Worker\'s Compensation Carrier.',19,'19','',1,0,0),(605080,62,'20','Claim denied because this injury/illness is covered by the liability carrier.',20,'20','',1,0,0),(605089,62,'21','Claim denied because this injury/illness is the liability of the no-fault carrier.',21,'21','',1,0,0),(605098,62,'22','Payment adjusted because this care may be covered by another payer per coordination of benefits.',22,'22','',1,0,0),(605107,62,'23','Payment adjusted due to the impact of prior payer(s) adjudication including payments and/or adjustments',23,'23','',1,0,0),(605116,62,'24','Payment for charges adjusted. Charges are covered under a capitation agreement/managed care plan.',24,'24','',1,0,0),(605125,62,'25','Payment denied. Your Stop loss deductible has not been met.',25,'25','',1,0,0),(605134,62,'26','Expenses incurred prior to coverage.',26,'26','',1,0,0),(605143,62,'27','Expenses incurred after coverage terminated.',27,'27','',1,0,0),(605152,62,'28','Coverage not in effect at the time the service was provided.',28,'28','',1,0,0),(605161,62,'29','The time limit for filing has expired.',29,'29','',1,0,0),(605170,62,'30','Payment adjusted because the patient has not met the required eligibilit',30,'30','',1,0,0),(605179,62,'31','Claim denied as patient cannot be identified as our insured.',31,'31','',1,0,0),(605190,63,'1','Simple Value',0,'','',1,0,0),(605199,63,'2','Form Field Name',1,'','',1,0,0),(605213,605208,'1','Homeless',1,'','',1,0,0),(605222,605208,'2','Transition Program',2,'','',1,0,0),(605231,605208,'3','House/Apt',3,'','',1,0,0),(605240,605208,'4','Unknown',4,'','',1,0,0),(605249,605208,'5','Blank',5,'','',1,0,0),(605263,605258,'1','District of Columbia',0,'','',1,0,0),(605272,605258,'2','Carroll',0,'','',1,0,0),(605281,605258,'3','Charles',0,'','',1,0,0),(605290,605258,'4','Howard',0,'','',1,0,0),(605299,605258,'5','La Grange',0,'','',1,0,0),(605308,605258,'6','Arlington',0,'','',1,0,0),(605317,605258,'7','Baltimore',0,'','',1,0,0),(605326,605258,'8','Calvert',0,'','',1,0,0),(605335,605258,'9','Culpepper',0,'','',1,0,0),(605344,605258,'10','Essex',0,'','',1,0,0),(605353,605258,'11','Fairfax',0,'','',1,0,0),(605362,605258,'12','Frederick',0,'','',1,0,0),(605371,605258,'13','Loudoun',0,'','',1,0,0),(605380,605258,'14','Manassas',0,'','',1,0,0),(605389,605258,'15','Prince William',0,'','',1,0,0),(605398,605258,'16','Prince Georges',0,'','',1,0,0),(605407,605258,'17','Somerset',0,'','',1,0,0),(605421,605416,'1','Not Head of Household',0,'','',1,0,0),(605430,605416,'2','Unknown',0,'','',1,0,0),(605439,605416,'3','Blank',0,'','',1,0,0),(605453,605448,'1','Spanish',0,'','',1,0,0),(605462,605448,'2','Amharic',0,'','',1,0,0),(605471,605448,'3','Arabic',0,'','',1,0,0),(605480,605448,'4','Armenian',0,'','',1,0,0),(605489,605448,'5','Bengali',0,'','',1,0,0),(605498,605448,'6','Chinese',0,'','',1,0,0),(605507,605448,'7','Farsi',0,'','',1,0,0),(605516,605448,'8','French',0,'','',1,0,0),(605525,605448,'9','German',0,'','',1,0,0),(605534,605448,'10','Hindi',0,'','',1,0,0),(605543,605448,'11','Indonesian',0,'','',1,0,0),(605552,605448,'12','Korean',0,'','',1,0,0),(605561,605448,'13','Mongolian',0,'','',1,0,0),(605570,605448,'14','Russian',0,'','',1,0,0),(605579,605448,'15','Tagalog',0,'','',1,0,0),(605588,605448,'16','Tigrigna',0,'','',1,0,0),(605597,605448,'17','Urdu',0,'','',1,0,0),(605606,605448,'18','Vietnamese',0,'','',1,0,0),(605615,605448,'19','Other',0,'','',1,0,0),(605624,605448,'20','Unknown',0,'','',1,0,0),(605633,605448,'21','Blank',0,'','',1,0,0),(605647,605642,'1','Somewhat proficient',0,'','',1,0,0),(605656,605642,'2','Limited',0,'','',1,0,0),(605665,605642,'3','Not proficient',0,'','',1,0,0),(605674,605642,'4','Unknown',0,'','',1,0,0),(605683,605642,'5','Blank',0,'','',1,0,0),(605697,605692,'1','Albania',0,'','',1,0,0),(605706,605692,'2','Algeria',0,'','',1,0,0),(605715,605692,'3','American Samoa',0,'','',1,0,0),(605724,605692,'4','Andorra',0,'','',1,0,0),(605733,605692,'5','Angola',0,'','',1,0,0),(605742,605692,'6','Anguilla',0,'','',1,0,0),(605751,605692,'7','Antigua and Barbuda',0,'','',1,0,0),(605760,605692,'8','Argentina',0,'','',1,0,0),(605769,605692,'9','Armenia',0,'','',1,0,0),(605778,605692,'10','Aruba',0,'','',1,0,0),(605787,605692,'11','Australia',0,'','',1,0,0),(605796,605692,'12','Austria',0,'','',1,0,0),(605805,605692,'13','Azerbajan',0,'','',1,0,0),(605814,605692,'14','Azores (Portugal)',0,'','',1,0,0),(605823,605692,'15','Bahamas',0,'','',1,0,0),(605832,605692,'16','Bahrain',0,'','',1,0,0),(605833,57,'dayweek','By Days of Week',6,'','',1,0,0),(605841,605692,'17','Bangladesh',0,'','',1,0,0),(605850,605692,'18','Barbados',0,'','',1,0,0),(605859,605692,'19','Belarus',0,'','',1,0,0),(605868,605692,'20','Belgium',0,'','',1,0,0),(605877,605692,'21','Belize',0,'','',1,0,0),(605886,605692,'22','Benin',0,'','',1,0,0),(605895,605692,'23','Bermuda',0,'','',1,0,0),(605904,605692,'24','Bolivia',0,'','',1,0,0),(605913,605692,'25','Bonaire (Netherlands Antilles)',0,'','',1,0,0),(605922,605692,'26','Bosnia',0,'','',1,0,0),(605931,605692,'27','Botswana',0,'','',1,0,0),(605940,605692,'28','Brazil',0,'','',1,0,0),(605949,605692,'29','British Virgin Islands',0,'','',1,0,0),(605958,605692,'30','Brunei',0,'','',1,0,0),(605967,605692,'31','Bulgaria',0,'','',1,0,0),(605976,605692,'32','Burkina Faso',0,'','',1,0,0),(605985,605692,'33','Burundi',0,'','',1,0,0),(605994,605692,'34','Cambodia',0,'','',1,0,0),(606003,605692,'35','Cameroom',0,'','',1,0,0),(606012,605692,'36','Canada',0,'','',1,0,0),(606021,605692,'37','Canary Islands',0,'','',1,0,0),(606030,605692,'38','Cape Verde',0,'','',1,0,0),(606039,605692,'39','Cayman Islands',0,'','',1,0,0),(606048,605692,'40','Central African Republic',0,'','',1,0,0),(606057,605692,'41','Chad',0,'','',1,0,0),(606066,605692,'42','Channel Islands',0,'','',1,0,0),(606075,605692,'43','Chile',0,'','',1,0,0),(606084,605692,'44','China',0,'','',1,0,0),(606093,605692,'45','Colombia',0,'','',1,0,0),(606102,605692,'46','Congo-Democratic Republic of',0,'','',1,0,0),(606111,605692,'47','Congo-Republic of',0,'','',1,0,0),(606120,605692,'48','Cook Islands',0,'','',1,0,0),(606129,605692,'49','Costa Rica',0,'','',1,0,0),(606138,605692,'50','Croatia',0,'','',1,0,0),(606147,605692,'51','Cuba',0,'','',1,0,0),(606156,605692,'52','Curacao (Netherlands Antilles)',0,'','',1,0,0),(606165,605692,'53','Cyprus',0,'','',1,0,0),(606174,605692,'54','Czech Republic',0,'','',1,0,0),(606183,605692,'55','Denmark',0,'','',1,0,0),(606192,605692,'56','Djibouti',0,'','',1,0,0),(606201,605692,'57','Dominica',0,'','',1,0,0),(606210,605692,'58','Dominican Republic',0,'','',1,0,0),(606219,605692,'59','Ecuador',0,'','',1,0,0),(606228,605692,'60','Eqypt',0,'','',1,0,0),(606237,605692,'61','El Salvador',0,'','',1,0,0),(606246,605692,'62','England',0,'','',1,0,0),(606255,605692,'63','Equatorial Guniea',0,'','',1,0,0),(606264,605692,'64','Eritrea',0,'','',1,0,0),(606273,605692,'65','Estonia',0,'','',1,0,0),(606282,605692,'66','Ethiopia',0,'','',1,0,0),(606291,605692,'67','Faroe Islands (Denmark)',0,'','',1,0,0),(606300,605692,'68','Fiji',0,'','',1,0,0),(606309,605692,'69','Finland',0,'','',1,0,0),(606318,605692,'70','France',0,'','',1,0,0),(606327,605692,'71','French Guiana',0,'','',1,0,0),(606336,605692,'72','French Polynesia',0,'','',1,0,0),(606345,605692,'73','Gabon',0,'','',1,0,0),(606354,605692,'74','Gambia',0,'','',1,0,0),(606363,605692,'75','Georgia',0,'','',1,0,0),(606372,605692,'76','Germany',0,'','',1,0,0),(606381,605692,'77','Ghana',0,'','',1,0,0),(606390,605692,'78','Gilbraltar',0,'','',1,0,0),(606399,605692,'79','Greece',0,'','',1,0,0),(606408,605692,'80','Greenland (Denmark)',0,'','',1,0,0),(606417,605692,'81','Grenada',0,'','',1,0,0),(606426,605692,'82','Guadeloupe',0,'','',1,0,0),(606435,605692,'83','Guam',0,'','',1,0,0),(606444,605692,'84','Guatemala',0,'','',1,0,0),(606453,605692,'85','Guinea',0,'','',1,0,0),(606462,605692,'86','Guinea-Bissau',0,'','',1,0,0),(606471,605692,'87','Guyana',0,'','',1,0,0),(606480,605692,'88','Haiti',0,'','',1,0,0),(606489,605692,'89','Holland (Netherlands)',0,'','',1,0,0),(606498,605692,'90','Honduras',0,'','',1,0,0),(606507,605692,'91','Hong Kong',0,'','',1,0,0),(606516,605692,'92','Hungary',0,'','',1,0,0),(606525,605692,'93','Iceland',0,'','',1,0,0),(606534,605692,'94','India',0,'','',1,0,0),(606543,605692,'95','Indonesia',0,'','',1,0,0),(606552,605692,'96','Iran',0,'','',1,0,0),(606561,605692,'97','Iraq',0,'','',1,0,0),(606570,605692,'98','Ireland -Republic of',0,'','',1,0,0),(606579,605692,'99','Israel',0,'','',1,0,0),(606588,605692,'100','Italy',0,'','',1,0,0),(606597,605692,'101','Ivory Coast',0,'','',1,0,0),(606606,605692,'102','Jamaica',0,'','',1,0,0),(606615,605692,'103','Japan',0,'','',1,0,0),(606624,605692,'104','Kazakhstan',0,'','',1,0,0),(606633,605692,'105','Kenya',0,'','',1,0,0),(606642,605692,'106','Kiribati',0,'','',1,0,0),(606651,605692,'107','Korea (South Korea)',0,'','',1,0,0),(606660,605692,'108','Korsrae (Federated States of Micronesia)',0,'','',1,0,0),(606669,605692,'109','Kuwait',0,'','',1,0,0),(606678,605692,'110','Kyrgyzstan',0,'','',1,0,0),(606687,605692,'111','Laos',0,'','',1,0,0),(606696,605692,'112','Latvia',0,'','',1,0,0),(606705,605692,'113','Lebanon',0,'','',1,0,0),(606714,605692,'114','Lesotho',0,'','',1,0,0),(606723,605692,'115','Liberia',0,'','',1,0,0),(606732,605692,'116','Liechtenstein',0,'','',1,0,0),(606741,605692,'117','Lithuania',0,'','',1,0,0),(606750,605692,'118','Macau',0,'','',1,0,0),(606759,605692,'119','Macedonia',0,'','',1,0,0),(606768,605692,'120','Madagascar',0,'','',1,0,0),(606777,605692,'121','Maderia (Portugal)',0,'','',1,0,0),(606786,605692,'122','Malawi',0,'','',1,0,0),(606795,605692,'123','Malaysia',0,'','',1,0,0),(606804,605692,'124','Maldives',0,'','',1,0,0),(606813,605692,'125','Mali',0,'','',1,0,0),(606822,605692,'126','Malta',0,'','',1,0,0),(606831,605692,'127','Marshall Islands',0,'','',1,0,0),(606840,605692,'128','Martinique',0,'','',1,0,0),(606849,605692,'129','Mauritius',0,'','',1,0,0),(606858,605692,'130','Mexico',0,'','',1,0,0),(606867,605692,'131','Micronesia - Federated States of',0,'','',1,0,0),(606876,605692,'132','Moldova',0,'','',1,0,0),(606885,605692,'133','Monaco',0,'','',1,0,0),(606894,605692,'134','Mongolia',0,'','',1,0,0),(606903,605692,'135','Montserrat',0,'','',1,0,0),(606912,605692,'136','Morocco',0,'','',1,0,0),(606921,605692,'137','Mozambique',0,'','',1,0,0),(606930,605692,'138','Nambia',0,'','',1,0,0),(606939,605692,'139','Nepal',0,'','',1,0,0),(606948,605692,'140','Netherlands (Holland)',0,'','',1,0,0),(606957,605692,'141','Netherlands Antilles',0,'','',1,0,0),(606966,605692,'142','New Caledonia',0,'','',1,0,0),(606975,605692,'143','New Zealand',0,'','',1,0,0),(606984,605692,'144','Nicaragua',0,'','',1,0,0),(606993,605692,'145','Niger',0,'','',1,0,0),(607002,605692,'146','Nigeria',0,'','',1,0,0),(607011,605692,'147','Norfolk Island',0,'','',1,0,0),(607020,605692,'148','Northern Ireland (UK)',0,'','',1,0,0),(607029,605692,'149','Northern Mariana Islands',0,'','',1,0,0),(607038,605692,'150','Norway',0,'','',1,0,0),(607047,605692,'151','Oman',0,'','',1,0,0),(607056,605692,'152','Pakistan',0,'','',1,0,0),(607065,605692,'153','Palau',0,'','',1,0,0),(607074,605692,'154','Panama',0,'','',1,0,0),(607083,605692,'155','Papua New Guinea',0,'','',1,0,0),(607092,605692,'156','Paraguay',0,'','',1,0,0),(607101,605692,'157','Peru',0,'','',1,0,0),(607110,605692,'158','Philippines',0,'','',1,0,0),(607119,605692,'159','Poland',0,'','',1,0,0),(607128,605692,'160','Ponape (Federated States of Micronesia)',0,'','',1,0,0),(607137,605692,'161','Portugal',0,'','',1,0,0),(607146,605692,'162','Qatar',0,'','',1,0,0),(607155,605692,'163','Reunion',0,'','',1,0,0),(607164,605692,'164','Romania',0,'','',1,0,0),(607173,605692,'165','Rota (Northern Mariana Islands)',0,'','',1,0,0),(607182,605692,'166','Russia',0,'','',1,0,0),(607191,605692,'167','Rwanda',0,'','',1,0,0),(607200,605692,'168','Saba (Netherlands Antilles)',0,'','',1,0,0),(607209,605692,'169','Saipan (Northern Mariana Islands)',0,'','',1,0,0),(607218,605692,'170','San Marino',0,'','',1,0,0),(607227,605692,'171','Saudia Arabia',0,'','',1,0,0),(607236,605692,'172','Scotland (United Kingdom)',0,'','',1,0,0),(607245,605692,'173','Senegal',0,'','',1,0,0),(607254,605692,'174','Seychelles',0,'','',1,0,0),(607263,605692,'175','Sierra Leone',0,'','',1,0,0),(607272,605692,'176','Singapore',0,'','',1,0,0),(607281,605692,'177','Slovakia',0,'','',1,0,0),(607290,605692,'178','Slovenia',0,'','',1,0,0),(607299,605692,'179','Solomon Islands',0,'','',1,0,0),(607308,605692,'180','Somalia',0,'','',1,0,0),(607317,605692,'181','South Africa',0,'','',1,0,0),(607326,605692,'182','Spain',0,'','',1,0,0),(607335,605692,'183','Sir Lanki',0,'','',1,0,0),(607344,605692,'184','St. Barthelemy (Guadeloupe)',0,'','',1,0,0),(607353,605692,'185','St. Christopher (St. Kitts and Nevis)',0,'','',1,0,0),(607362,605692,'186','St. Croix (U.S. Virgin Islands)',0,'','',1,0,0),(607371,605692,'187','St. Eustatius (Netherlands Antilles)',0,'','',1,0,0),(607380,605692,'188','St. John ((U.S. Virgin Islands)',0,'','',1,0,0),(607389,605692,'189','St. Kitts and Nevis',0,'','',1,0,0),(607398,605692,'190','St. Lucia',0,'','',1,0,0),(607407,605692,'191','St. Martin (Guadeloupe)',0,'','',1,0,0),(607416,605692,'192','St. Thomas (U.S. Virgin Islands)',0,'','',1,0,0),(607425,605692,'193','St. Vincent and the Grenadines',0,'','',1,0,0),(607434,605692,'194','Suriname',0,'','',1,0,0),(607443,605692,'195','Swaziland',0,'','',1,0,0),(607452,605692,'196','Sweden',0,'','',1,0,0),(607461,605692,'197','Switzerland',0,'','',1,0,0),(607470,605692,'198','Syria',0,'','',1,0,0),(607479,605692,'199','Tahiti (French Polynesia)',0,'','',1,0,0),(607488,605692,'200','Taiwan',0,'','',1,0,0),(607497,605692,'201','Tajikistan',0,'','',1,0,0),(607506,605692,'202','Tanzania',0,'','',1,0,0),(607515,605692,'203','Thailand',0,'','',1,0,0),(607524,605692,'204','Tinian (Northern Mariana Islands)',0,'','',1,0,0),(607533,605692,'205','Togo',0,'','',1,0,0),(607542,605692,'206','Tonga',0,'','',1,0,0),(607547,300038,'6','Employer',6,'','',1,0,0),(607551,605692,'207','Tortola (British Virgin Islands)',0,'','',1,0,0),(607560,605692,'208','Trinidad and Tobago',0,'','',1,0,0),(607569,605692,'209','Truk (Federated States of Micronesia)',0,'','',1,0,0),(607578,605692,'210','Tunisia',0,'','',1,0,0),(607587,605692,'211','Turkey',0,'','',1,0,0),(607596,605692,'212','Turkmenistan',0,'','',1,0,0),(607605,605692,'213','Turks and Caicos Islands',0,'','',1,0,0),(607614,605692,'214','Tuvalu',0,'','',1,0,0),(607623,605692,'215','U.S. Virgin Islands',0,'','',1,0,0),(607632,605692,'216','Uganda',0,'','',1,0,0),(607641,605692,'217','Ukraine',0,'','',1,0,0),(607650,605692,'218','Union Island (St. Vincent and the Grenadines)',0,'','',1,0,0),(607659,605692,'219','United Arab Emirates',0,'','',1,0,0),(607668,605692,'220','United Kingdom',0,'','',1,0,0),(607677,605692,'221','Unknown',0,'','',1,0,0),(607686,605692,'222','Uruguay',0,'','',1,0,0),(607695,605692,'223','USA',0,'','',1,0,0),(607704,605692,'224','Uzbekistan',0,'','',1,0,0),(607713,605692,'225','Vanuatu',0,'','',1,0,0),(607722,605692,'226','Venezuela',0,'','',1,0,0),(607731,605692,'227','Vietnam',0,'','',1,0,0),(607740,605692,'228','Virgin Gorda (British Virgin Islands)',0,'','',1,0,0),(607749,605692,'229','Wake Island',0,'','',1,0,0),(607758,605692,'230','Wales (United Kingdom)',0,'','',1,0,0),(607767,605692,'231','Wallis and Futuna Islands',0,'','',1,0,0),(607776,605692,'232','Western Samoa',0,'','',1,0,0),(607785,605692,'233','Yap (Federated States of Micronesia)',0,'','',1,0,0),(607794,605692,'234','Yemen',0,'','',1,0,0),(607803,605692,'235','Zaire (Democratic Republic of Congo)',0,'','',1,0,0),(607812,605692,'236','Zambia',0,'','',1,0,0),(607821,605692,'237','Zimbabwe',0,'','',1,0,0),(607830,605692,'238','Blank',0,'','',1,0,0),(607844,607839,'1','Muslim',0,'','',1,0,0),(607853,607839,'2','Jewish',0,'','',1,0,0),(607862,607839,'3','Buddhist',0,'','',1,0,0),(607871,607839,'4','Hindu',0,'','',1,0,0),(607880,607839,'5','Catholic',0,'','',1,0,0),(607889,607839,'6','Jehovah\'s Witness',0,'','',1,0,0),(607898,607839,'7','Mormon',0,'','',1,0,0),(607907,607839,'8','None',0,'','',1,0,0),(607916,607839,'9','Orthodox',0,'','',1,0,0),(607925,607839,'10','Protestant',0,'','',1,0,0),(607934,607839,'11','Other',0,'','',1,0,0),(607943,607839,'12','Unknown',0,'','',1,0,0),(607952,607839,'13','Blank',0,'','',1,0,0),(607966,607961,'1','Unemployed',0,'','',1,0,0),(607975,607961,'2','Unknown',0,'','',1,0,0),(607984,607961,'3','Blank',0,'','',1,0,0),(607998,607993,'1','None-illiterate',0,'','',1,0,0),(608007,607993,'2','Some Elementary Education',0,'','',1,0,0),(608016,607993,'3','Some Middle School',0,'','',1,0,0),(608025,607993,'4','Some High School',0,'','',1,0,0),(608034,607993,'5','High School Degree',0,'','',1,0,0),(608043,607993,'6','Vocational/Tech School',0,'','',1,0,0),(608052,607993,'7','Some College',0,'','',1,0,0),(608061,607993,'8','Associates Degree',0,'','',1,0,0),(608070,607993,'9','Bachelors Degree',0,'','',1,0,0),(608079,607993,'10','Post Grad College',0,'','',1,0,0),(608088,607993,'11','Masters Degree',0,'','',1,0,0),(608097,607993,'12','Advanced Degree',0,'','',1,0,0),(608106,607993,'13','Other',0,'','',1,0,0),(608115,607993,'14','Blank',0,'','',1,0,0),(608129,608124,'1','No Unknown',0,'','',1,0,0),(608138,608124,'2','Blank',0,'','',1,0,0),(608152,608147,'1','Anti-coagulation',0,'','',1,0,0),(608161,608147,'2','Cancer',0,'','',1,0,0),(608170,608147,'3','CHF (Congestive Heart Faliure)',0,'','',1,0,0),(608179,608147,'4','CVA (Cerbrovascular Accident, Stroke)',0,'','',1,0,0),(608188,608147,'5','Depression',0,'','',1,0,0),(608197,608147,'6','Diabetes',0,'','',1,0,0),(608206,608147,'7','Diabetes Type I',0,'','',1,0,0),(608215,608147,'8','Diabetes Type II',0,'','',1,0,0),(608224,608147,'9','Hyperlipidemia',0,'','',1,0,0),(608233,608147,'10','Hypertension',0,'','',1,0,0),(608242,608147,'11','Nephropathy',0,'','',1,0,0),(608251,608147,'12','Neuropathy',0,'','',1,0,0),(608260,608147,'13','NKF',0,'','',1,0,0),(608269,608147,'14','Obesity',0,'','',1,0,0),(608278,608147,'15','Post MI',0,'','',1,0,0),(608287,608147,'16','PVD (Peripheralvascular Disease)',0,'','',1,0,0),(608296,608147,'17','Renal Faliure',0,'','',1,0,0),(608305,608147,'18','Retinopathy',0,'','',1,0,0),(608319,608314,'1','PCMI',0,'','',1,0,0),(608328,608314,'2','DCHCA',0,'','',1,0,0),(608337,608314,'3','MCCP',0,'','',1,0,0),(608346,608314,'4','CFK',0,'','',1,0,0),(608355,608314,'5','None',0,'','',1,0,0),(608369,608364,'1','No',0,'','',1,0,0),(608377,59,'6','5 - Disease and Family Planning Confidentiality',4,'','',1,0,0),(608378,608364,'2','Unknown',0,'','',1,0,0),(608401,608387,'2','apples',0,'','',1,0,0),(608410,608387,'3','Shrimp',0,'','',1,0,0),(608419,608387,'4','cherries',0,'','',1,0,0),(608428,608387,'5','Coffee',0,'','',1,0,0),(608437,608387,'6','Crayfish Â ',0,'','',1,0,0),(608446,608387,'7','Egg',0,'','',1,0,0),(608455,608387,'8','fish',0,'','',1,0,0),(608464,608387,'9','Tomato',0,'','',1,0,0),(608473,608387,'10','Hot peppers Â ',0,'','',1,0,0),(608482,608387,'11','peaches Â ',0,'','',1,0,0),(608491,608387,'12','peanuts Â ',0,'','',1,0,0),(608500,608387,'13','Pork',0,'','',1,0,0),(608509,608387,'14','Seafood',0,'','',1,0,0),(608518,608387,'15','Watermelon',0,'','',1,0,0),(608527,608387,'16','Yeast',0,'','',1,0,0),(608536,608387,'17','1st tier: Meds',0,'','',1,0,0),(608554,608387,'19','pyridium',0,'','',1,0,0),(608563,608387,'20','ACE inhibitors Â ',0,'','',1,0,0),(608572,608387,'21','Acetaminofen Â ',0,'','',1,0,0),(608581,608387,'22','Adalop',0,'','',1,0,0),(608590,608387,'23','Advil',0,'','',1,0,0),(608599,608387,'24','Aleve',0,'','',1,0,0),(608608,608387,'25','Alka Seltzer Â ',0,'','',1,0,0),(608617,608387,'26','Antibotics',0,'','',1,0,0),(608626,608387,'27','Amitriptyline',0,'','',1,0,0),(608635,608387,'28','AMOXICILLIN',0,'','',1,0,0),(608644,608387,'29','Ampicilina Â ',0,'','',1,0,0),(608653,608387,'30','anaprox Â ',0,'','',1,0,0),(608662,608387,'31','anestesics',0,'','',1,0,0),(608671,608387,'32','Anesthesia Â ',0,'','',1,0,0),(608680,608387,'33','aspirin',0,'','',1,0,0),(608689,608387,'34','augmentin Â ',0,'','',1,0,0),(608698,608387,'35','B-12 Â ',0,'','',1,0,0),(608707,608387,'36','Bactim',0,'','',1,0,0),(608716,608387,'37','benedril',0,'','',1,0,0),(608725,608387,'38','ceflin',0,'','',1,0,0),(608734,608387,'39','Celebrex',0,'','',1,0,0),(608743,608387,'40','Celexa',0,'','',1,0,0),(608752,608387,'41','Cephalexin',0,'','',1,0,0),(608761,608387,'42','chloroquine Â ',0,'','',1,0,0),(608770,608387,'43','Cipro Â ',0,'','',1,0,0),(608779,608387,'44','Citamol Â ',0,'','',1,0,0),(608788,608387,'45','Claritin Â ',0,'','',1,0,0),(608797,608387,'46','CLORAMFENICOL Â ',0,'','',1,0,0),(608806,608387,'47','CLYNDOMYCIN Â ',0,'','',1,0,0),(608815,608387,'48','codeine',0,'','',1,0,0),(608824,608387,'49','compazine Â ',0,'','',1,0,0),(608833,608387,'50','CORTISON(rash, angroedema) Â ',0,'','',1,0,0),(608842,608387,'51','Dexacort Â ',0,'','',1,0,0),(608851,608387,'52','Dilantin Â ',0,'','',1,0,0),(608860,608387,'53','Elavil Â ',0,'','',1,0,0),(608869,608387,'54','ERITROMICINA Â ',0,'','',1,0,0),(608878,608387,'55','erythancin Â ',0,'','',1,0,0),(608887,608387,'56','erythromycin',0,'','',1,0,0),(608896,608387,'57','FLEXERIL Â ',0,'','',1,0,0),(608905,608387,'58','Furoxona? Â ',0,'','',1,0,0),(608914,608387,'59','General Anestisia Â ',0,'','',1,0,0),(608923,608387,'60','Ibuprofen',0,'','',1,0,0),(608932,608387,'61','Lantus',0,'','',1,0,0),(608941,608387,'62','Levaquin Â ',0,'','',1,0,0),(608950,608387,'63','Lisinopril Â ',0,'','',1,0,0),(608959,608387,'64','Local anasthetic(Canbocaine??) Â ',0,'','',1,0,0),(608968,608387,'65','Losartan',0,'','',1,0,0),(608977,608387,'66','Maxzide',0,'','',1,0,0),(608986,608387,'67','MENTHOLATION Â ',0,'','',1,0,0),(608995,608387,'68','morphine Â ',0,'','',1,0,0),(609004,608387,'69','motrin',0,'','',1,0,0),(609013,608387,'70','naprocin',0,'','',1,0,0),(609022,608387,'71','No Know Drug Allergies',0,'','',1,0,0),(609031,608387,'72','Penicillin',0,'','',1,0,0),(609040,608387,'73','Percocet Â ',0,'','',1,0,0),(609049,608387,'74','Prinivil Â ',0,'','',1,0,0),(609058,608387,'75','Quinine (Quinidine) Â ',0,'','',1,0,0),(609067,608387,'76','Relafen Â ',0,'','',1,0,0),(609076,608387,'77','rocephin',0,'','',1,0,0),(609085,608387,'78','Sulfa',0,'','',1,0,0),(609094,608387,'79','tegretol',0,'','',1,0,0),(609103,608387,'80','Tetracycline Â ',0,'','',1,0,0),(609112,608387,'81','Thorazine Â ',0,'','',1,0,0),(609121,608387,'82','Codeine',0,'','',1,0,0),(609130,608387,'83','tylenor/acetaminophen Â ',0,'','',1,0,0),(609139,608387,'84','UltramÂ ',0,'','',1,0,0),(609148,608387,'85','Vancomycin',0,'','',1,0,0),(609157,608387,'86','Vasotec Â ',0,'','',1,0,0),(609166,608387,'87','Vioxx',0,'','',1,0,0),(609175,608387,'88','xlonipin',0,'','',1,0,0),(609184,608387,'89','Zestril',0,'','',1,0,0),(609193,608387,'90','Zocor Â ',0,'','',1,0,0),(609202,608387,'91','1st tier: Chemicals',0,'','',1,0,0),(609220,608387,'93','iodine',0,'','',1,0,0),(609229,608387,'94','chemicals Â ',0,'','',1,0,0),(609238,608387,'95','Iodine Â ',0,'','',1,0,0),(609247,608387,'96','LATEX',0,'','',1,0,0),(609256,608387,'97','metals Â ',0,'','',1,0,0),(609265,608387,'98','Peroxide Â ',0,'','',1,0,0),(609274,608387,'99','Potassium Â ',0,'','',1,0,0),(609283,608387,'100','Sodium',0,'','',1,0,0),(609292,608387,'101','sodium pentathol',0,'','',1,0,0),(609301,608387,'102','1st tier: Environment',0,'','',1,0,0),(609319,608387,'104','DUST Â ',0,'','',1,0,0),(609328,608387,'105','Hay fever',0,'','',1,0,0),(609337,608387,'106','Pollen',0,'','',1,0,0),(609346,608387,'107','SEASONAL Â ',0,'','',1,0,0),(609355,608387,'108','sun',0,'','',1,0,0),(609364,608387,'109','1st tier : Other',0,'','',1,0,0),(609387,609382,'1','DTaP (Diphtheria, Tetanus, aPertussis) (90700)',0,'','',1,0,0),(609396,609382,'2','Flu Vaccine (90655-90658)',0,'','',1,0,0),(609405,609382,'3','Hepatitis B (90746)',0,'','',1,0,0),(609414,609382,'4','Hepatitis B - 1st (90746)',0,'','',1,0,0),(609423,609382,'5','Hepatitis B - 2nd (90746)',0,'','',1,0,0),(609432,609382,'6','Hepatitis B - 3rd (90746)',0,'','',1,0,0),(609441,609382,'7','Hib (Haem Influenza type b) (90645-90648)',0,'','',1,0,0),(609450,609382,'8','MMR (Measles, Mumps, Rubella) (90707)',0,'','',1,0,0),(609459,609382,'9','Pneumovax (90669, 90732)',0,'','',1,0,0),(609468,609382,'10','IPV (Polio) (90713)',0,'','',1,0,0),(609477,609382,'11','PPD (TB test) (86580)',0,'','',1,0,0),(609486,609382,'12','Td (Tetanus, Diphtheria) (90718)',0,'','',1,0,0),(609495,609382,'13','Tetanus toxoid (90703)',0,'','',1,0,0),(609504,609382,'14','Tuberculosis (BCG) (90585)',0,'','',1,0,0),(609513,609382,'15','Varicella (Chickenpox) (90716)',0,'','',1,0,0),(609522,609382,'16','Blank',0,'','',1,0,0),(609536,609531,'1','Anemia',0,'','',1,0,0),(609545,609531,'2','Arthritis',0,'','',1,0,0),(609554,609531,'3','Asthma',0,'','',1,0,0),(609563,609531,'4','Cancer',0,'','',1,0,0),(609572,609531,'5','Diabetes',0,'','',1,0,0),(609581,609531,'6','Emotional Prob',0,'','',1,0,0),(609590,609531,'7','TB skin test?',0,'','',1,0,0),(609599,609531,'8','Gallbladder',0,'','',1,0,0),(609608,609531,'9','Heart Problems',0,'','',1,0,0),(609617,609531,'10','Hepatitis/Liver',0,'','',1,0,0),(609626,609531,'11','High Blood Pressure',0,'','',1,0,0),(609635,609531,'12','High Cholesterol',0,'','',1,0,0),(609644,609531,'13','Kidney Problems',0,'','',1,0,0),(609653,609531,'14','Lung Problems',0,'','',1,0,0),(609662,609531,'15','Allergies',0,'','',1,0,0),(609671,609531,'16','Menstral Problems',0,'','',1,0,0),(609680,609531,'17','Rheumatic Fever',0,'','',1,0,0),(609689,609531,'18','Sexually transmitted disease',0,'','',1,0,0),(609698,609531,'19','Stomach Problems',0,'','',1,0,0),(609707,609531,'20','Stroke',0,'','',1,0,0),(609716,609531,'21','Thyroid Problems',0,'','',1,0,0),(609725,609531,'22','Tuberculosis',0,'','',1,0,0),(609734,609531,'23','Blank',0,'','',1,0,0),(609748,609743,'1','Anemia',0,'','',1,0,0),(609757,609743,'2','Arthritis',0,'','',1,0,0),(609766,609743,'3','Asthma',0,'','',1,0,0),(609775,609743,'4','Cancer',0,'','',1,0,0),(609784,609743,'5','Diabetes',0,'','',1,0,0),(609793,609743,'6','Emotional Prob',0,'','',1,0,0),(609802,609743,'7','TB skin test?',0,'','',1,0,0),(609811,609743,'8','Gallbladder',0,'','',1,0,0),(609820,609743,'9','Heart Problems',0,'','',1,0,0),(609829,609743,'10','Hepatitis/Liver',0,'','',1,0,0),(609838,609743,'11','High Blood Pressure',0,'','',1,0,0),(609847,609743,'12','High Cholesterol',0,'','',1,0,0),(609856,609743,'13','Kidney Problems',0,'','',1,0,0),(609865,609743,'14','Lung Problems',0,'','',1,0,0),(609874,609743,'15','Allergies',0,'','',1,0,0),(609883,609743,'16','Menstral Problems',0,'','',1,0,0),(609892,609743,'17','Rheumatic Fever',0,'','',1,0,0),(609901,609743,'18','Sexually transmitted disease',0,'','',1,0,0),(609910,609743,'19','Stomach Problems',0,'','',1,0,0),(609919,609743,'20','Stroke',0,'','',1,0,0),(609928,609743,'21','Thyroid Problems',0,'','',1,0,0),(609937,609743,'22','Tuberculosis',0,'','',1,0,0),(609946,609743,'23','Blank',0,'','',1,0,0),(609960,609955,'1','Child (adoptive)',0,'','',1,0,0),(609969,609955,'2','Child (biological)',0,'','',1,0,0),(609978,609955,'3','Cousin',0,'','',1,0,0),(609987,609955,'4','Grandchild',0,'','',1,0,0),(609996,609955,'5','Grandparent (adoptive)',0,'','',1,0,0),(610005,609955,'6','Grandparent (biological)',0,'','',1,0,0),(610014,609955,'7','Half Sibling',0,'','',1,0,0),(610023,609955,'8','Legal Guardian',0,'','',1,0,0),(610032,609955,'9','Niece or Nephew',0,'','',1,0,0),(610041,609955,'10','Other',0,'','',1,0,0),(610050,609955,'11','Parent (adoptive)',0,'','',1,0,0),(610059,609955,'12','Parent (biological)',0,'','',1,0,0),(610068,609955,'13','Parent (step)',0,'','',1,0,0),(610077,609955,'14','Sibling (adoptive)',0,'','',1,0,0),(610086,609955,'15','Sibling (biological)',0,'','',1,0,0),(610095,609955,'16','Spouse',0,'','',1,0,0),(610104,609955,'17','Step child',0,'','',1,0,0),(610113,609955,'18','Blank',0,'','',1,0,0),(610127,610122,'1','Lab Payment',0,'','',1,0,0),(610136,610122,'2','Medications Payment',0,'','',1,0,0),(610145,610122,'3','Correction Payment',0,'','',1,0,0),(610154,610122,'4','Other',0,'','',1,0,0),(610168,610163,'','',0,'','',1,0,0),(610176,610171,'1','Non-Provider',0,'','',1,0,0),(610185,610171,'2','Specialist',0,'','',1,0,0),(610194,610171,'3','Medical Phone',0,'','',1,0,0),(610203,610171,'4','Medication PU',0,'','',1,0,0),(610212,610171,'5','Education',0,'','',1,0,0),(610221,610171,'6','Eligibility',0,'','',1,0,0),(610230,610171,'7','AFC.109',0,'','',1,0,0),(610239,610171,'8','AFC.110',0,'','',1,0,0),(610248,610171,'9','AFC.111',0,'','',1,0,0),(610257,610171,'10','AFC.112',0,'','',1,0,0),(610271,610266,'1','DPCC001',0,'','',1,0,0),(610280,610266,'2','DPCC002',0,'','',1,0,0),(610289,610266,'3','DPCC003',0,'','',1,0,0),(610298,610266,'4','DPCC004',0,'','',1,0,0),(610307,610266,'5','DPCC005',0,'','',1,0,0),(610316,610266,'6','DPCC006',0,'','',1,0,0),(610325,610266,'7','DPCC007',0,'','',1,0,0),(610334,610266,'8','DPCC008',0,'','',1,0,0),(610343,610266,'9','DPCC009',0,'','',1,0,0),(610352,610266,'10','DPCC010',0,'','',1,0,0),(610361,610266,'11','DPCC011',0,'','',1,0,0),(610370,610266,'12','DPCC012',0,'','',1,0,0),(610379,610266,'13','DPCC013',0,'','',1,0,0),(610388,610266,'14','DPCC014',0,'','',1,0,0),(610397,610266,'15','DPCC015',0,'','',1,0,0),(610406,610266,'16','DPCC016',0,'','',1,0,0),(610415,610266,'17','DPCC017',0,'','',1,0,0),(610424,610266,'18','DPCC018',0,'','',1,0,0),(610433,610266,'19','DPCC019',0,'','',1,0,0),(610442,610266,'20','DPCC020',0,'','',1,0,0),(610451,610266,'21','for Mercy',0,'','',1,0,0),(610460,610266,'22','MH0.01',0,'','',1,0,0),(610469,610266,'23','MH0.02',0,'','',1,0,0),(610478,610266,'24','MH0.03',0,'','',1,0,0),(610487,610266,'25','MH0.04',0,'','',1,0,0),(610496,610266,'26','MH0.05',0,'','',1,0,0),(610505,610266,'27','MH0.06',0,'','',1,0,0),(610514,610266,'28','MH0.07',0,'','',1,0,0),(610523,610266,'29','MH0.08',0,'','',1,0,0),(610532,610266,'30','MH0.09',0,'','',1,0,0),(610541,610266,'31','MH0.10',0,'','',1,0,0),(610550,610266,'32','MH0.11',0,'','',1,0,0),(610559,610266,'33','MH0.12',0,'','',1,0,0),(610568,610266,'34','MH0.13',0,'','',1,0,0),(610577,610266,'35','MH0.14',0,'','',1,0,0),(610586,610266,'36','MH0.15',0,'','',1,0,0),(610595,610266,'37','MH0.16',0,'','',1,0,0),(610604,610266,'38','MH0.17',0,'','',1,0,0),(610613,610266,'39','MH0.18',0,'','',1,0,0),(610622,610266,'40','MH0.19',0,'','',1,0,0),(610631,610266,'41','MH0.20',0,'','',1,0,0),(610640,610266,'42','MH0.21',0,'','',1,0,0),(610649,610266,'43','MH0.22',0,'','',1,0,0),(610658,610266,'44','MH0.23',0,'','',1,0,0),(610667,610266,'45','MH0.24',0,'','',1,0,0),(610676,610266,'46','MH0.25',0,'','',1,0,0),(610685,610266,'47','MH0.26',0,'','',1,0,0),(610694,610266,'48','MH0.27',0,'','',1,0,0),(610703,610266,'49','MH0.28',0,'','',1,0,0),(610712,610266,'50','MH0.29',0,'','',1,0,0),(610721,610266,'51','MH0.30',0,'','',1,0,0),(610730,610266,'52','MH0.31',0,'','',1,0,0),(610739,610266,'53','MH0.32',0,'','',1,0,0),(610748,610266,'54','MH0.33',0,'','',1,0,0),(610757,610266,'55','MH0.34',0,'','',1,0,0),(610766,610266,'56','MH1.01',0,'','',1,0,0),(610775,610266,'57','MH1.02',0,'','',1,0,0),(610784,610266,'58','MH1.03',0,'','',1,0,0),(610793,610266,'59','MH1.04',0,'','',1,0,0),(610802,610266,'60','MH1.05',0,'','',1,0,0),(610811,610266,'61','for AFC',0,'','',1,0,0),(610820,610266,'62','AFC.100',0,'','',1,0,0),(610829,610266,'63','AFC.101',0,'','',1,0,0),(610838,610266,'64','AFC.102',0,'','',1,0,0),(610847,610266,'65','AFC.103',0,'','',1,0,0),(610856,610266,'66','AFC.104',0,'','',1,0,0),(610865,610266,'67','AFC.105',0,'','',1,0,0),(610874,610266,'68','AFC.106',0,'','',1,0,0),(610883,610266,'69','AFC.107',0,'','',1,0,0),(610892,610266,'70','AFC.108',0,'','',1,0,0),(610901,610266,'71','Education_ Indiv - Asthma',0,'','',1,0,0),(610910,610266,'72','Education_ Indiv - Diabetes',0,'','',1,0,0),(610919,610266,'73','Education_ Indiv - Diet',0,'','',1,0,0),(610928,610266,'74','Education_ Indiv - Hl',0,'','',1,0,0),(610937,610266,'75','AFC.113',0,'','',1,0,0),(610946,610266,'76','AFC.114',0,'','',1,0,0),(610955,610266,'77','AFC.115',0,'','',1,0,0),(610964,610266,'78','AFC.116',0,'','',1,0,0),(610973,610266,'79','AFC.117',0,'','',1,0,0),(610982,610266,'80','AFC.118',0,'','',1,0,0),(610991,610266,'81','AFC.119',0,'','',1,0,0),(611000,610266,'82','AFC.120',0,'','',1,0,0),(611009,610266,'83','AFC.121',0,'','',1,0,0),(611018,610266,'84','AFC.122',0,'','',1,0,0),(611027,610266,'85','AFC.123',0,'','',1,0,0),(611036,610266,'86','AFC.124',0,'','',1,0,0),(611045,610266,'87','AFC.125',0,'','',1,0,0),(611054,610266,'88','AFC.126',0,'','',1,0,0),(611063,610266,'89','AFC.127',0,'','',1,0,0),(611072,610266,'90','AFC.128',0,'','',1,0,0),(611081,610266,'91','AFC.129',0,'','',1,0,0),(611090,610266,'92','AFC.130',0,'','',1,0,0),(611099,610266,'93','AFC.131',0,'','',1,0,0),(611108,610266,'94','AFC.132',0,'','',1,0,0),(611117,610266,'95','AFC.133',0,'','',1,0,0),(1080307,67,'1','EMR Tab',1,'','',1,0,0),(1080314,67,'2','Criticals Pallet',1,'','',1,0,0),(1080315,67,'3','Criticals Pallet (QuickList)',5,'','',1,0,0),(1080316,67,'10','Disabled',17,'','',1,0,0),(1080317,67,'4','Criticals Pallet (Controller)',8,'','',1,0,0),(1080318,67,'5','Encounter Tab',11,'','',1,0,0),(1080319,67,'6','Other',14,'','',1,0,0),(1080320,67,'7','Other (QuickList)',19,'','',1,0,0),(2080307,67,'1','EMR Tab',2,'','',1,0,0),(2080314,67,'2','Criticals Pallet',3,'','',1,0,0),(2080315,67,'3','Criticals Pallet (QuickList)',6,'','',1,0,0),(2080316,67,'10','Disabled',18,'','',1,0,0),(2080317,67,'4','Criticals Pallet (Controller)',9,'','',1,0,0),(2080318,67,'5','Encounter Tab',12,'','',1,0,0),(2080319,67,'6','Other',15,'','',1,0,0),(2080320,67,'7','Other (QuickList)',20,'','',1,0,0),(4592324,605208,'6','Shelter',0,'','',1,0,0),(5684392,5684387,'1','Shelter',0,'','',1,0,0),(5684401,5684387,'2','Homeless',0,'','',1,0,0),(5684410,5684387,'3','Transition Program',0,'','',1,0,0),(5684419,5684387,'4','House/Apt',0,'','',1,0,0),(5684428,5684387,'5','Unknown',0,'','',1,0,0),(5684437,5684387,'6','Blank',0,'','',1,0,0),(5684451,5684446,'1','Montgomery',0,'','',1,0,0),(5684460,5684446,'2','District of Columbia',0,'','',1,0,0),(5684469,5684446,'3','Carroll',0,'','',1,0,0),(5684478,5684446,'4','Charles',0,'','',1,0,0),(5684487,5684446,'5','Howard',0,'','',1,0,0),(5684496,5684446,'6','La Grange',0,'','',1,0,0),(5684505,5684446,'7','Arlington',0,'','',1,0,0),(5684514,5684446,'8','Baltimore',0,'','',1,0,0),(5684523,5684446,'9','Calvert',0,'','',1,0,0),(5684532,5684446,'10','Culpepper',0,'','',1,0,0),(5684541,5684446,'11','Essex',0,'','',1,0,0),(5684550,5684446,'12','Fairfax',0,'','',1,0,0),(5684559,5684446,'13','Frederick',0,'','',1,0,0),(5684568,5684446,'14','Loudoun',0,'','',1,0,0),(5684577,5684446,'15','Manassas',0,'','',1,0,0),(5684586,5684446,'16','Prince William',0,'','',1,0,0),(5684595,5684446,'17','Prince Georges',0,'','',1,0,0),(5684604,5684446,'18','Somerset',0,'','',1,0,0),(5684618,5684613,'1','Head of Household',0,'','',1,0,0),(5684627,5684613,'2','Not Head of Household',0,'','',1,0,0),(5684636,5684613,'3','Unknown',0,'','',1,0,0),(5684645,5684613,'4','Blank',0,'','',1,0,0),(5684659,5684654,'1','English',0,'','',1,0,0),(5684668,5684654,'2','Spanish',0,'','',1,0,0),(5684677,5684654,'3','Amharic',0,'','',1,0,0),(5684686,5684654,'4','Arabic',0,'','',1,0,0),(5684695,5684654,'5','Armenian',0,'','',1,0,0),(5684704,5684654,'6','Bengali',0,'','',1,0,0),(5684713,5684654,'7','Chinese',0,'','',1,0,0),(5684722,5684654,'8','Farsi',0,'','',1,0,0),(5684731,5684654,'9','French',0,'','',1,0,0),(5684740,5684654,'10','German',0,'','',1,0,0),(5684749,5684654,'11','Hindi',0,'','',1,0,0),(5684758,5684654,'12','Indonesian',0,'','',1,0,0),(5684767,5684654,'13','Korean',0,'','',1,0,0),(5684776,5684654,'14','Mongolian',0,'','',1,0,0),(5684785,5684654,'15','Russian',0,'','',1,0,0),(5684794,5684654,'16','Tagalog',0,'','',1,0,0),(5684803,5684654,'17','Tigrigna',0,'','',1,0,0),(5684812,5684654,'18','Urdu',0,'','',1,0,0),(5684821,5684654,'19','Vietnamese',0,'','',1,0,0),(5684830,5684654,'20','Other',0,'','',1,0,0),(5684839,5684654,'21','Unknown',0,'','',1,0,0),(5684848,5684654,'22','Blank',0,'','',1,0,0),(5684862,5684857,'1','Proficient',0,'','',1,0,0),(5684871,5684857,'2','Somewhat proficient',0,'','',1,0,0),(5684880,5684857,'3','Limited',0,'','',1,0,0),(5684889,5684857,'4','Not proficient',0,'','',1,0,0),(5684898,5684857,'5','Unknown',0,'','',1,0,0),(5684907,5684857,'6','Blank',0,'','',1,0,0),(5684921,5684916,'1','Afghanistan',0,'','',1,0,0),(5684930,5684916,'2','Albania',0,'','',1,0,0),(5684939,5684916,'3','Algeria',0,'','',1,0,0),(5684948,5684916,'4','American Samoa',0,'','',1,0,0),(5684957,5684916,'5','Andorra',0,'','',1,0,0),(5684966,5684916,'6','Angola',0,'','',1,0,0),(5684975,5684916,'7','Anguilla',0,'','',1,0,0),(5684984,5684916,'8','Antigua and Barbuda',0,'','',1,0,0),(5684993,5684916,'9','Argentina',0,'','',1,0,0),(5685002,5684916,'10','Armenia',0,'','',1,0,0),(5685011,5684916,'11','Aruba',0,'','',1,0,0),(5685020,5684916,'12','Australia',0,'','',1,0,0),(5685029,5684916,'13','Austria',0,'','',1,0,0),(5685038,5684916,'14','Azerbajan',0,'','',1,0,0),(5685047,5684916,'15','Azores (Portugal)',0,'','',1,0,0),(5685056,5684916,'16','Bahamas',0,'','',1,0,0),(5685065,5684916,'17','Bahrain',0,'','',1,0,0),(5685074,5684916,'18','Bangladesh',0,'','',1,0,0),(5685083,5684916,'19','Barbados',0,'','',1,0,0),(5685092,5684916,'20','Belarus',0,'','',1,0,0),(5685101,5684916,'21','Belgium',0,'','',1,0,0),(5685110,5684916,'22','Belize',0,'','',1,0,0),(5685119,5684916,'23','Benin',0,'','',1,0,0),(5685128,5684916,'24','Bermuda',0,'','',1,0,0),(5685137,5684916,'25','Bolivia',0,'','',1,0,0),(5685146,5684916,'26','Bonaire (Netherlands Antilles)',0,'','',1,0,0),(5685155,5684916,'27','Bosnia',0,'','',1,0,0),(5685164,5684916,'28','Botswana',0,'','',1,0,0),(5685173,5684916,'29','Brazil',0,'','',1,0,0),(5685182,5684916,'30','British Virgin Islands',0,'','',1,0,0),(5685191,5684916,'31','Brunei',0,'','',1,0,0),(5685200,5684916,'32','Bulgaria',0,'','',1,0,0),(5685209,5684916,'33','Burkina Faso',0,'','',1,0,0),(5685218,5684916,'34','Burundi',0,'','',1,0,0),(5685227,5684916,'35','Cambodia',0,'','',1,0,0),(5685236,5684916,'36','Cameroom',0,'','',1,0,0),(5685245,5684916,'37','Canada',0,'','',1,0,0),(5685254,5684916,'38','Canary Islands',0,'','',1,0,0),(5685263,5684916,'39','Cape Verde',0,'','',1,0,0),(5685272,5684916,'40','Cayman Islands',0,'','',1,0,0),(5685281,5684916,'41','Central African Republic',0,'','',1,0,0),(5685290,5684916,'42','Chad',0,'','',1,0,0),(5685299,5684916,'43','Channel Islands',0,'','',1,0,0),(5685308,5684916,'44','Chile',0,'','',1,0,0),(5685317,5684916,'45','China',0,'','',1,0,0),(5685326,5684916,'46','Colombia',0,'','',1,0,0),(5685335,5684916,'47','Congo-Democratic Republic of',0,'','',1,0,0),(5685344,5684916,'48','Congo-Republic of',0,'','',1,0,0),(5685353,5684916,'49','Cook Islands',0,'','',1,0,0),(5685362,5684916,'50','Costa Rica',0,'','',1,0,0),(5685371,5684916,'51','Croatia',0,'','',1,0,0),(5685380,5684916,'52','Cuba',0,'','',1,0,0),(5685389,5684916,'53','Curacao (Netherlands Antilles)',0,'','',1,0,0),(5685398,5684916,'54','Cyprus',0,'','',1,0,0),(5685407,5684916,'55','Czech Republic',0,'','',1,0,0),(5685416,5684916,'56','Denmark',0,'','',1,0,0),(5685425,5684916,'57','Djibouti',0,'','',1,0,0),(5685434,5684916,'58','Dominica',0,'','',1,0,0),(5685443,5684916,'59','Dominican Republic',0,'','',1,0,0),(5685452,5684916,'60','Ecuador',0,'','',1,0,0),(5685461,5684916,'61','Eqypt',0,'','',1,0,0),(5685470,5684916,'62','El Salvador',0,'','',1,0,0),(5685479,5684916,'63','England',0,'','',1,0,0),(5685488,5684916,'64','Equatorial Guniea',0,'','',1,0,0),(5685497,5684916,'65','Eritrea',0,'','',1,0,0),(5685506,5684916,'66','Estonia',0,'','',1,0,0),(5685515,5684916,'67','Ethiopia',0,'','',1,0,0),(5685524,5684916,'68','Faroe Islands (Denmark)',0,'','',1,0,0),(5685533,5684916,'69','Fiji',0,'','',1,0,0),(5685542,5684916,'70','Finland',0,'','',1,0,0),(5685551,5684916,'71','France',0,'','',1,0,0),(5685560,5684916,'72','French Guiana',0,'','',1,0,0),(5685569,5684916,'73','French Polynesia',0,'','',1,0,0),(5685578,5684916,'74','Gabon',0,'','',1,0,0),(5685587,5684916,'75','Gambia',0,'','',1,0,0),(5685596,5684916,'76','Georgia',0,'','',1,0,0),(5685605,5684916,'77','Germany',0,'','',1,0,0),(5685614,5684916,'78','Ghana',0,'','',1,0,0),(5685623,5684916,'79','Gilbraltar',0,'','',1,0,0),(5685632,5684916,'80','Greece',0,'','',1,0,0),(5685641,5684916,'81','Greenland (Denmark)',0,'','',1,0,0),(5685650,5684916,'82','Grenada',0,'','',1,0,0),(5685659,5684916,'83','Guadeloupe',0,'','',1,0,0),(5685668,5684916,'84','Guam',0,'','',1,0,0),(5685677,5684916,'85','Guatemala',0,'','',1,0,0),(5685686,5684916,'86','Guinea',0,'','',1,0,0),(5685695,5684916,'87','Guinea-Bissau',0,'','',1,0,0),(5685704,5684916,'88','Guyana',0,'','',1,0,0),(5685713,5684916,'89','Haiti',0,'','',1,0,0),(5685722,5684916,'90','Holland (Netherlands)',0,'','',1,0,0),(5685731,5684916,'91','Honduras',0,'','',1,0,0),(5685740,5684916,'92','Hong Kong',0,'','',1,0,0),(5685749,5684916,'93','Hungary',0,'','',1,0,0),(5685758,5684916,'94','Iceland',0,'','',1,0,0),(5685767,5684916,'95','India',0,'','',1,0,0),(5685776,5684916,'96','Indonesia',0,'','',1,0,0),(5685785,5684916,'97','Iran',0,'','',1,0,0),(5685794,5684916,'98','Iraq',0,'','',1,0,0),(5685803,5684916,'99','Ireland -Republic of',0,'','',1,0,0),(5685812,5684916,'100','Israel',0,'','',1,0,0),(5685821,5684916,'101','Italy',0,'','',1,0,0),(5685830,5684916,'102','Ivory Coast',0,'','',1,0,0),(5685839,5684916,'103','Jamaica',0,'','',1,0,0),(5685848,5684916,'104','Japan',0,'','',1,0,0),(5685857,5684916,'105','Kazakhstan',0,'','',1,0,0),(5685866,5684916,'106','Kenya',0,'','',1,0,0),(5685875,5684916,'107','Kiribati',0,'','',1,0,0),(5685884,5684916,'108','Korea (South Korea)',0,'','',1,0,0),(5685893,5684916,'109','Korsrae (Federated States of Micronesia)',0,'','',1,0,0),(5685902,5684916,'110','Kuwait',0,'','',1,0,0),(5685911,5684916,'111','Kyrgyzstan',0,'','',1,0,0),(5685920,5684916,'112','Laos',0,'','',1,0,0),(5685929,5684916,'113','Latvia',0,'','',1,0,0),(5685938,5684916,'114','Lebanon',0,'','',1,0,0),(5685947,5684916,'115','Lesotho',0,'','',1,0,0),(5685956,5684916,'116','Liberia',0,'','',1,0,0),(5685965,5684916,'117','Liechtenstein',0,'','',1,0,0),(5685974,5684916,'118','Lithuania',0,'','',1,0,0),(5685983,5684916,'119','Macau',0,'','',1,0,0),(5685992,5684916,'120','Macedonia',0,'','',1,0,0),(5686001,5684916,'121','Madagascar',0,'','',1,0,0),(5686010,5684916,'122','Maderia (Portugal)',0,'','',1,0,0),(5686019,5684916,'123','Malawi',0,'','',1,0,0),(5686028,5684916,'124','Malaysia',0,'','',1,0,0),(5686037,5684916,'125','Maldives',0,'','',1,0,0),(5686046,5684916,'126','Mali',0,'','',1,0,0),(5686055,5684916,'127','Malta',0,'','',1,0,0),(5686064,5684916,'128','Marshall Islands',0,'','',1,0,0),(5686073,5684916,'129','Martinique',0,'','',1,0,0),(5686082,5684916,'130','Mauritius',0,'','',1,0,0),(5686091,5684916,'131','Mexico',0,'','',1,0,0),(5686100,5684916,'132','Micronesia - Federated States of',0,'','',1,0,0),(5686109,5684916,'133','Moldova',0,'','',1,0,0),(5686118,5684916,'134','Monaco',0,'','',1,0,0),(5686127,5684916,'135','Mongolia',0,'','',1,0,0),(5686136,5684916,'136','Montserrat',0,'','',1,0,0),(5686145,5684916,'137','Morocco',0,'','',1,0,0),(5686154,5684916,'138','Mozambique',0,'','',1,0,0),(5686163,5684916,'139','Nambia',0,'','',1,0,0),(5686172,5684916,'140','Nepal',0,'','',1,0,0),(5686181,5684916,'141','Netherlands (Holland)',0,'','',1,0,0),(5686190,5684916,'142','Netherlands Antilles',0,'','',1,0,0),(5686199,5684916,'143','New Caledonia',0,'','',1,0,0),(5686208,5684916,'144','New Zealand',0,'','',1,0,0),(5686217,5684916,'145','Nicaragua',0,'','',1,0,0),(5686226,5684916,'146','Niger',0,'','',1,0,0),(5686235,5684916,'147','Nigeria',0,'','',1,0,0),(5686244,5684916,'148','Norfolk Island',0,'','',1,0,0),(5686253,5684916,'149','Northern Ireland (UK)',0,'','',1,0,0),(5686262,5684916,'150','Northern Mariana Islands',0,'','',1,0,0),(5686271,5684916,'151','Norway',0,'','',1,0,0),(5686280,5684916,'152','Oman',0,'','',1,0,0),(5686289,5684916,'153','Pakistan',0,'','',1,0,0),(5686298,5684916,'154','Palau',0,'','',1,0,0),(5686307,5684916,'155','Panama',0,'','',1,0,0),(5686316,5684916,'156','Papua New Guinea',0,'','',1,0,0),(5686325,5684916,'157','Paraguay',0,'','',1,0,0),(5686334,5684916,'158','Peru',0,'','',1,0,0),(5686343,5684916,'159','Philippines',0,'','',1,0,0),(5686352,5684916,'160','Poland',0,'','',1,0,0),(5686361,5684916,'161','Ponape (Federated States of Micronesia)',0,'','',1,0,0),(5686370,5684916,'162','Portugal',0,'','',1,0,0),(5686379,5684916,'163','Qatar',0,'','',1,0,0),(5686388,5684916,'164','Reunion',0,'','',1,0,0),(5686397,5684916,'165','Romania',0,'','',1,0,0),(5686406,5684916,'166','Rota (Northern Mariana Islands)',0,'','',1,0,0),(5686415,5684916,'167','Russia',0,'','',1,0,0),(5686424,5684916,'168','Rwanda',0,'','',1,0,0),(5686433,5684916,'169','Saba (Netherlands Antilles)',0,'','',1,0,0),(5686442,5684916,'170','Saipan (Northern Mariana Islands)',0,'','',1,0,0),(5686451,5684916,'171','San Marino',0,'','',1,0,0),(5686460,5684916,'172','Saudia Arabia',0,'','',1,0,0),(5686469,5684916,'173','Scotland (United Kingdom)',0,'','',1,0,0),(5686478,5684916,'174','Senegal',0,'','',1,0,0),(5686487,5684916,'175','Seychelles',0,'','',1,0,0),(5686496,5684916,'176','Sierra Leone',0,'','',1,0,0),(5686505,5684916,'177','Singapore',0,'','',1,0,0),(5686514,5684916,'178','Slovakia',0,'','',1,0,0),(5686523,5684916,'179','Slovenia',0,'','',1,0,0),(5686532,5684916,'180','Solomon Islands',0,'','',1,0,0),(5686541,5684916,'181','Somalia',0,'','',1,0,0),(5686550,5684916,'182','South Africa',0,'','',1,0,0),(5686559,5684916,'183','Spain',0,'','',1,0,0),(5686568,5684916,'184','Sir Lanki',0,'','',1,0,0),(5686577,5684916,'185','St. Barthelemy (Guadeloupe)',0,'','',1,0,0),(5686586,5684916,'186','St. Christopher (St. Kitts and Nevis)',0,'','',1,0,0),(5686595,5684916,'187','St. Croix (U.S. Virgin Islands)',0,'','',1,0,0),(5686604,5684916,'188','St. Eustatius (Netherlands Antilles)',0,'','',1,0,0),(5686613,5684916,'189','St. John ((U.S. Virgin Islands)',0,'','',1,0,0),(5686622,5684916,'190','St. Kitts and Nevis',0,'','',1,0,0),(5686631,5684916,'191','St. Lucia',0,'','',1,0,0),(5686640,5684916,'192','St. Martin (Guadeloupe)',0,'','',1,0,0),(5686649,5684916,'193','St. Thomas (U.S. Virgin Islands)',0,'','',1,0,0),(5686658,5684916,'194','St. Vincent and the Grenadines',0,'','',1,0,0),(5686667,5684916,'195','Suriname',0,'','',1,0,0),(5686676,5684916,'196','Swaziland',0,'','',1,0,0),(5686685,5684916,'197','Sweden',0,'','',1,0,0),(5686694,5684916,'198','Switzerland',0,'','',1,0,0),(5686703,5684916,'199','Syria',0,'','',1,0,0),(5686712,5684916,'200','Tahiti (French Polynesia)',0,'','',1,0,0),(5686721,5684916,'201','Taiwan',0,'','',1,0,0),(5686730,5684916,'202','Tajikistan',0,'','',1,0,0),(5686739,5684916,'203','Tanzania',0,'','',1,0,0),(5686748,5684916,'204','Thailand',0,'','',1,0,0),(5686757,5684916,'205','Tinian (Northern Mariana Islands)',0,'','',1,0,0),(5686766,5684916,'206','Togo',0,'','',1,0,0),(5686775,5684916,'207','Tonga',0,'','',1,0,0),(5686784,5684916,'208','Tortola (British Virgin Islands)',0,'','',1,0,0),(5686793,5684916,'209','Trinidad and Tobago',0,'','',1,0,0),(5686802,5684916,'210','Truk (Federated States of Micronesia)',0,'','',1,0,0),(5686811,5684916,'211','Tunisia',0,'','',1,0,0),(5686820,5684916,'212','Turkey',0,'','',1,0,0),(5686829,5684916,'213','Turkmenistan',0,'','',1,0,0),(5686838,5684916,'214','Turks and Caicos Islands',0,'','',1,0,0),(5686847,5684916,'215','Tuvalu',0,'','',1,0,0),(5686856,5684916,'216','U.S. Virgin Islands',0,'','',1,0,0),(5686865,5684916,'217','Uganda',0,'','',1,0,0),(5686874,5684916,'218','Ukraine',0,'','',1,0,0),(5686883,5684916,'219','Union Island (St. Vincent and the Grenadines)',0,'','',1,0,0),(5686892,5684916,'220','United Arab Emirates',0,'','',1,0,0),(5686901,5684916,'221','United Kingdom',0,'','',1,0,0),(5686910,5684916,'222','Unknown',0,'','',1,0,0),(5686919,5684916,'223','Uruguay',0,'','',1,0,0),(5686928,5684916,'224','USA',0,'','',1,0,0),(5686937,5684916,'225','Uzbekistan',0,'','',1,0,0),(5686946,5684916,'226','Vanuatu',0,'','',1,0,0),(5686955,5684916,'227','Venezuela',0,'','',1,0,0),(5686964,5684916,'228','Vietnam',0,'','',1,0,0),(5686973,5684916,'229','Virgin Gorda (British Virgin Islands)',0,'','',1,0,0),(5686982,5684916,'230','Wake Island',0,'','',1,0,0),(5686991,5684916,'231','Wales (United Kingdom)',0,'','',1,0,0),(5687000,5684916,'232','Wallis and Futuna Islands',0,'','',1,0,0),(5687009,5684916,'233','Western Samoa',0,'','',1,0,0),(5687018,5684916,'234','Yap (Federated States of Micronesia)',0,'','',1,0,0),(5687027,5684916,'235','Yemen',0,'','',1,0,0),(5687036,5684916,'236','Zaire (Democratic Republic of Congo)',0,'','',1,0,0),(5687045,5684916,'237','Zambia',0,'','',1,0,0),(5687054,5684916,'238','Zimbabwe',0,'','',1,0,0),(5687063,5684916,'239','Blank',0,'','',1,0,0),(5687077,5687072,'1','Other Christian',0,'','',1,0,0),(5687086,5687072,'2','Muslim',0,'','',1,0,0),(5687095,5687072,'3','Jewish',0,'','',1,0,0),(5687104,5687072,'4','Buddhist',0,'','',1,0,0),(5687113,5687072,'5','Hindu',0,'','',1,0,0),(5687122,5687072,'6','Catholic',0,'','',1,0,0),(5687131,5687072,'7','Jehovah\"s Witness',0,'','',1,0,0),(5687140,5687072,'8','Mormon',0,'','',1,0,0),(5687149,5687072,'9','None',0,'','',1,0,0),(5687158,5687072,'10','Orthodox',0,'','',1,0,0),(5687167,5687072,'11','Protestant',0,'','',1,0,0),(5687176,5687072,'12','Other',0,'','',1,0,0),(5687185,5687072,'13','Unknown',0,'','',1,0,0),(5687194,5687072,'14','Blank',0,'','',1,0,0),(5687208,5687203,'1','Employed',0,'','',1,0,0),(5687217,5687203,'2','Unemployed',0,'','',1,0,0),(5687226,5687203,'3','Unknown',0,'','',1,0,0),(5687235,5687203,'4','Blank',0,'','',1,0,0),(5687249,5687244,'1','Unknown',0,'','',1,0,0),(5687258,5687244,'2','None-illiterate',0,'','',1,0,0),(5687267,5687244,'3','Some Elementary Education',0,'','',1,0,0),(5687276,5687244,'4','Some Middle School',0,'','',1,0,0),(5687285,5687244,'5','Some High School',0,'','',1,0,0),(5687294,5687244,'6','High School Degree',0,'','',1,0,0),(5687303,5687244,'7','Vocational/Tech School',0,'','',1,0,0),(5687312,5687244,'8','Some College',0,'','',1,0,0),(5687321,5687244,'9','Associates Degree',0,'','',1,0,0),(5687330,5687244,'10','Bachelors Degree',0,'','',1,0,0),(5687339,5687244,'11','Post Grad College',0,'','',1,0,0),(5687348,5687244,'12','Masters Degree',0,'','',1,0,0),(5687357,5687244,'13','Advanced Degree',0,'','',1,0,0),(5687366,5687244,'14','Other',0,'','',1,0,0),(5687375,5687244,'15','Blank',0,'','',1,0,0),(5687389,5687384,'1','Yes',0,'','',1,0,0),(5687398,5687384,'2','No Unknown',0,'','',1,0,0),(5687407,5687384,'3','Blank',0,'','',1,0,0),(5687421,5687416,'1','Asthma',0,'','',1,0,0),(5687430,5687416,'2','Anti-coagulation',0,'','',1,0,0),(5687439,5687416,'3','Cancer',0,'','',1,0,0),(5687448,5687416,'4','CHF (Congestive Heart Faliure)',0,'','',1,0,0),(5687457,5687416,'5','CVA (Cerbrovascular Accident, Stroke)',0,'','',1,0,0),(5687466,5687416,'6','Depression',0,'','',1,0,0),(5687475,5687416,'7','Diabetes',0,'','',1,0,0),(5687484,5687416,'8','Diabetes Type I',0,'','',1,0,0),(5687493,5687416,'9','Diabetes Type II',0,'','',1,0,0),(5687502,5687416,'10','Hyperlipidemia',0,'','',1,0,0),(5687511,5687416,'11','Hypertension',0,'','',1,0,0),(5687520,5687416,'12','Nephropathy',0,'','',1,0,0),(5687529,5687416,'13','Neuropathy',0,'','',1,0,0),(5687538,5687416,'14','NKF',0,'','',1,0,0),(5687547,5687416,'15','Obesity',0,'','',1,0,0),(5687556,5687416,'16','Post MI',0,'','',1,0,0),(5687565,5687416,'17','PVD (Peripheralvascular Disease)',0,'','',1,0,0),(5687574,5687416,'18','Renal Faliure',0,'','',1,0,0),(5687583,5687416,'19','Retinopathy',0,'','',1,0,0),(5687597,5687592,'1','MPC',0,'','',1,0,0),(5687606,5687592,'2','PCMI',0,'','',1,0,0),(5687615,5687592,'3','DCHCA',0,'','',1,0,0),(5687624,5687592,'4','MCCP',0,'','',1,0,0),(5687633,5687592,'5','CFK',0,'','',1,0,0),(5687642,5687592,'6','None',0,'','',1,0,0),(5687656,5687651,'1','Yes',0,'','',1,0,0),(5687665,5687651,'2','No',0,'','',1,0,0),(5687674,5687651,'3','Unknown',0,'','',1,0,0),(5687688,5687683,'1','1st tier: Food',0,'','',1,0,0),(5687706,5687683,'3','apples',0,'','',1,0,0),(5687715,5687683,'4','Shrimp',0,'','',1,0,0),(5687724,5687683,'5','cherries',0,'','',1,0,0),(5687733,5687683,'6','Coffee',0,'','',1,0,0),(5687742,5687683,'7','Crayfish Â ',0,'','',1,0,0),(5687751,5687683,'8','Egg',0,'','',1,0,0),(5687760,5687683,'9','fish',0,'','',1,0,0),(5687769,5687683,'10','Tomato',0,'','',1,0,0),(5687778,5687683,'11','Hot peppers Â ',0,'','',1,0,0),(5687787,5687683,'12','peaches Â ',0,'','',1,0,0),(5687796,5687683,'13','peanuts Â ',0,'','',1,0,0),(5687805,5687683,'14','Pork',0,'','',1,0,0),(5687814,5687683,'15','Seafood',0,'','',1,0,0),(5687823,5687683,'16','Watermelon',0,'','',1,0,0),(5687832,5687683,'17','Yeast',0,'','',1,0,0),(5687841,5687683,'18','1st tier: Meds',0,'','',1,0,0),(5687859,5687683,'20','pyridium',0,'','',1,0,0),(5687868,5687683,'21','ACE inhibitors Â ',0,'','',1,0,0),(5687877,5687683,'22','Acetaminofen Â ',0,'','',1,0,0),(5687886,5687683,'23','Adalop',0,'','',1,0,0),(5687895,5687683,'24','Advil',0,'','',1,0,0),(5687904,5687683,'25','Aleve',0,'','',1,0,0),(5687913,5687683,'26','Alka Seltzer Â ',0,'','',1,0,0),(5687922,5687683,'27','Antibotics',0,'','',1,0,0),(5687931,5687683,'28','Amitriptyline',0,'','',1,0,0),(5687940,5687683,'29','AMOXICILLIN',0,'','',1,0,0),(5687949,5687683,'30','Ampicilina Â ',0,'','',1,0,0),(5687958,5687683,'31','anaprox Â ',0,'','',1,0,0),(5687967,5687683,'32','anestesics',0,'','',1,0,0),(5687976,5687683,'33','Anesthesia Â ',0,'','',1,0,0),(5687985,5687683,'34','aspirin',0,'','',1,0,0),(5687994,5687683,'35','augmentin Â ',0,'','',1,0,0),(5688003,5687683,'36','B-12 Â ',0,'','',1,0,0),(5688012,5687683,'37','Bactim',0,'','',1,0,0),(5688021,5687683,'38','benedril',0,'','',1,0,0),(5688030,5687683,'39','ceflin',0,'','',1,0,0),(5688039,5687683,'40','Celebrex',0,'','',1,0,0),(5688048,5687683,'41','Celexa',0,'','',1,0,0),(5688057,5687683,'42','Cephalexin',0,'','',1,0,0),(5688066,5687683,'43','chloroquine Â ',0,'','',1,0,0),(5688075,5687683,'44','Cipro Â ',0,'','',1,0,0),(5688084,5687683,'45','Citamol Â ',0,'','',1,0,0),(5688093,5687683,'46','Claritin Â ',0,'','',1,0,0),(5688102,5687683,'47','CLORAMFENICOL Â ',0,'','',1,0,0),(5688111,5687683,'48','CLYNDOMYCIN Â ',0,'','',1,0,0),(5688120,5687683,'49','codeine',0,'','',1,0,0),(5688129,5687683,'50','compazine Â ',0,'','',1,0,0),(5688138,5687683,'51','CORTISON(rash, angroedema) Â ',0,'','',1,0,0),(5688147,5687683,'52','Dexacort Â ',0,'','',1,0,0),(5688156,5687683,'53','Dilantin Â ',0,'','',1,0,0),(5688165,5687683,'54','Elavil Â ',0,'','',1,0,0),(5688174,5687683,'55','ERITROMICINA Â ',0,'','',1,0,0),(5688183,5687683,'56','erythancin Â ',0,'','',1,0,0),(5688192,5687683,'57','erythromycin',0,'','',1,0,0),(5688201,5687683,'58','FLEXERIL Â ',0,'','',1,0,0),(5688210,5687683,'59','Furoxona? Â ',0,'','',1,0,0),(5688219,5687683,'60','General Anestisia Â ',0,'','',1,0,0),(5688228,5687683,'61','Ibuprofen',0,'','',1,0,0),(5688237,5687683,'62','Lantus',0,'','',1,0,0),(5688246,5687683,'63','Levaquin Â ',0,'','',1,0,0),(5688255,5687683,'64','Lisinopril Â ',0,'','',1,0,0),(5688264,5687683,'65','Local anasthetic(Canbocaine??) Â ',0,'','',1,0,0),(5688273,5687683,'66','Losartan',0,'','',1,0,0),(5688282,5687683,'67','Maxzide',0,'','',1,0,0),(5688291,5687683,'68','MENTHOLATION Â ',0,'','',1,0,0),(5688300,5687683,'69','morphine Â ',0,'','',1,0,0),(5688309,5687683,'70','motrin',0,'','',1,0,0),(5688318,5687683,'71','naprocin',0,'','',1,0,0),(5688327,5687683,'72','No Know Drug Allergies',0,'','',1,0,0),(5688336,5687683,'73','Penicillin',0,'','',1,0,0),(5688345,5687683,'74','Percocet Â ',0,'','',1,0,0),(5688354,5687683,'75','Prinivil Â ',0,'','',1,0,0),(5688363,5687683,'76','Quinine (Quinidine) Â ',0,'','',1,0,0),(5688372,5687683,'77','Relafen Â ',0,'','',1,0,0),(5688381,5687683,'78','rocephin',0,'','',1,0,0),(5688390,5687683,'79','Sulfa',0,'','',1,0,0),(5688399,5687683,'80','tegretol',0,'','',1,0,0),(5688408,5687683,'81','Tetracycline Â ',0,'','',1,0,0),(5688417,5687683,'82','Thorazine Â ',0,'','',1,0,0),(5688426,5687683,'83','Codeine',0,'','',1,0,0),(5688435,5687683,'84','tylenor/acetaminophen Â ',0,'','',1,0,0),(5688444,5687683,'85','UltramÂ ',0,'','',1,0,0),(5688453,5687683,'86','Vancomycin',0,'','',1,0,0),(5688462,5687683,'87','Vasotec Â ',0,'','',1,0,0),(5688471,5687683,'88','Vioxx',0,'','',1,0,0),(5688480,5687683,'89','xlonipin',0,'','',1,0,0),(5688489,5687683,'90','Zestril',0,'','',1,0,0),(5688498,5687683,'91','Zocor Â ',0,'','',1,0,0),(5688507,5687683,'92','1st tier: Chemicals',0,'','',1,0,0),(5688525,5687683,'94','iodine',0,'','',1,0,0),(5688534,5687683,'95','chemicals Â ',0,'','',1,0,0),(5688543,5687683,'96','Iodine Â ',0,'','',1,0,0),(5688552,5687683,'97','LATEX',0,'','',1,0,0),(5688561,5687683,'98','metals Â ',0,'','',1,0,0),(5688570,5687683,'99','Peroxide Â ',0,'','',1,0,0),(5688579,5687683,'100','Potassium Â ',0,'','',1,0,0),(5688588,5687683,'101','Sodium',0,'','',1,0,0),(5688597,5687683,'102','sodium pentathol',0,'','',1,0,0),(5688606,5687683,'103','1st tier: Environment',0,'','',1,0,0),(5688624,5687683,'105','DUST Â ',0,'','',1,0,0),(5688633,5687683,'106','Hay fever',0,'','',1,0,0),(5688642,5687683,'107','Pollen',0,'','',1,0,0),(5688651,5687683,'108','SEASONAL Â ',0,'','',1,0,0),(5688660,5687683,'109','sun',0,'','',1,0,0),(5688669,5687683,'110','1st tier : Other',0,'','',1,0,0),(5688692,5688687,'1','DT (Diphtheria, Tetanus) (90702)',0,'','',1,0,0),(5688701,5688687,'2','DTaP (Diphtheria, Tetanus, aPertussis) (90700)',0,'','',1,0,0),(5688710,5688687,'3','Flu Vaccine (90655-90658)',0,'','',1,0,0),(5688719,5688687,'4','Hepatitis B (90746)',0,'','',1,0,0),(5688728,5688687,'5','Hepatitis B - 1st (90746)',0,'','',1,0,0),(5688737,5688687,'6','Hepatitis B - 2nd (90746)',0,'','',1,0,0),(5688746,5688687,'7','Hepatitis B - 3rd (90746)',0,'','',1,0,0),(5688755,5688687,'8','Hib (Haem Influenza type b) (90645-90648)',0,'','',1,0,0),(5688764,5688687,'9','MMR (Measles, Mumps, Rubella) (90707)',0,'','',1,0,0),(5688773,5688687,'10','Pneumovax (90669, 90732)',0,'','',1,0,0),(5688782,5688687,'11','IPV (Polio) (90713)',0,'','',1,0,0),(5688791,5688687,'12','PPD (TB test) (86580)',0,'','',1,0,0),(5688800,5688687,'13','Td (Tetanus, Diphtheria) (90718)',0,'','',1,0,0),(5688809,5688687,'14','Tetanus toxoid (90703)',0,'','',1,0,0),(5688818,5688687,'15','Tuberculosis (BCG) (90585)',0,'','',1,0,0),(5688827,5688687,'16','Varicella (Chickenpox) (90716)',0,'','',1,0,0),(5688836,5688687,'17','Blank',0,'','',1,0,0),(5688850,5688845,'1','HIV/AIDS',0,'','',1,0,0),(5688859,5688845,'2','Anemia',0,'','',1,0,0),(5688868,5688845,'3','Arthritis',0,'','',1,0,0),(5688877,5688845,'4','Asthma',0,'','',1,0,0),(5688886,5688845,'5','Cancer',0,'','',1,0,0),(5688895,5688845,'6','Diabetes',0,'','',1,0,0),(5688904,5688845,'7','Emotional Prob',0,'','',1,0,0),(5688913,5688845,'8','TB skin test?',0,'','',1,0,0),(5688922,5688845,'9','Gallbladder',0,'','',1,0,0),(5688931,5688845,'10','Heart Problems',0,'','',1,0,0),(5688940,5688845,'11','Hepatitis/Liver',0,'','',1,0,0),(5688949,5688845,'12','High Blood Pressure',0,'','',1,0,0),(5688958,5688845,'13','High Cholesterol',0,'','',1,0,0),(5688967,5688845,'14','Kidney Problems',0,'','',1,0,0),(5688976,5688845,'15','Lung Problems',0,'','',1,0,0),(5688985,5688845,'16','Allergies',0,'','',1,0,0),(5688994,5688845,'17','Menstral Problems',0,'','',1,0,0),(5689003,5688845,'18','Rheumatic Fever',0,'','',1,0,0),(5689012,5688845,'19','Sexually transmitted disease',0,'','',1,0,0),(5689021,5688845,'20','Stomach Problems',0,'','',1,0,0),(5689030,5688845,'21','Stroke',0,'','',1,0,0),(5689039,5688845,'22','Thyroid Problems',0,'','',1,0,0),(5689048,5688845,'23','Tuberculosis',0,'','',1,0,0),(5689057,5688845,'24','Blank',0,'','',1,0,0),(5689071,5689066,'1','HIV/AIDS',0,'','',1,0,0),(5689080,5689066,'2','Anemia',0,'','',1,0,0),(5689089,5689066,'3','Arthritis',0,'','',1,0,0),(5689098,5689066,'4','Asthma',0,'','',1,0,0),(5689107,5689066,'5','Cancer',0,'','',1,0,0),(5689116,5689066,'6','Diabetes',0,'','',1,0,0),(5689125,5689066,'7','Emotional Prob',0,'','',1,0,0),(5689134,5689066,'8','TB skin test?',0,'','',1,0,0),(5689143,5689066,'9','Gallbladder',0,'','',1,0,0),(5689152,5689066,'10','Heart Problems',0,'','',1,0,0),(5689161,5689066,'11','Hepatitis/Liver',0,'','',1,0,0),(5689170,5689066,'12','High Blood Pressure',0,'','',1,0,0),(5689179,5689066,'13','High Cholesterol',0,'','',1,0,0),(5689188,5689066,'14','Kidney Problems',0,'','',1,0,0),(5689197,5689066,'15','Lung Problems',0,'','',1,0,0),(5689206,5689066,'16','Allergies',0,'','',1,0,0),(5689215,5689066,'17','Menstral Problems',0,'','',1,0,0),(5689224,5689066,'18','Rheumatic Fever',0,'','',1,0,0),(5689233,5689066,'19','Sexually transmitted disease',0,'','',1,0,0),(5689242,5689066,'20','Stomach Problems',0,'','',1,0,0),(5689251,5689066,'21','Stroke',0,'','',1,0,0),(5689260,5689066,'22','Thyroid Problems',0,'','',1,0,0),(5689269,5689066,'23','Tuberculosis',0,'','',1,0,0),(5689278,5689066,'24','Blank',0,'','',1,0,0),(5689292,5689287,'1','Aunt or Uncle',0,'','',1,0,0),(5689301,5689287,'2','Child (adoptive)',0,'','',1,0,0),(5689310,5689287,'3','Child (biological)',0,'','',1,0,0),(5689319,5689287,'4','Cousin',0,'','',1,0,0),(5689328,5689287,'5','Grandchild',0,'','',1,0,0),(5689337,5689287,'6','Grandparent (adoptive)',0,'','',1,0,0),(5689346,5689287,'7','Grandparent (biological)',0,'','',1,0,0),(5689355,5689287,'8','Half Sibling',0,'','',1,0,0),(5689364,5689287,'9','Legal Guardian',0,'','',1,0,0),(5689373,5689287,'10','Niece or Nephew',0,'','',1,0,0),(5689382,5689287,'11','Other',0,'','',1,0,0),(5689391,5689287,'12','Parent (adoptive)',0,'','',1,0,0),(5689400,5689287,'13','Parent (biological)',0,'','',1,0,0),(5689409,5689287,'14','Parent (step)',0,'','',1,0,0),(5689418,5689287,'15','Sibling (adoptive)',0,'','',1,0,0),(5689427,5689287,'16','Sibling (biological)',0,'','',1,0,0),(5689436,5689287,'17','Spouse',0,'','',1,0,0),(5689445,5689287,'18','Step child',0,'','',1,0,0),(5689454,5689287,'19','Blank',0,'','',1,0,0),(5689468,5689463,'1','Visit Payment',0,'','',1,0,0),(5689477,5689463,'2','Lab Payment',0,'','',1,0,0),(5689486,5689463,'3','Medications Payment',0,'','',1,0,0),(5689495,5689463,'4','Correction Payment',0,'','',1,0,0),(5689504,5689463,'5','Other',0,'','',1,0,0),(5689518,5689513,'1','Provider',0,'','',1,0,0),(5689527,5689513,'2','Non-Provider',0,'','',1,0,0),(5689536,5689513,'3','Specialist',0,'','',1,0,0),(5689545,5689513,'4','Medical Phone',0,'','',1,0,0),(5689554,5689513,'5','Medication PU',0,'','',1,0,0),(5689563,5689513,'6','Education',0,'','',1,0,0),(5689572,5689513,'7','Eligibility',0,'','',1,0,0),(5689586,5689581,'1','for SCC, Mobile Med, Proyecto Salud, PCWC, MCHP, Holy Cross, MCC Medical Clinic, PCC',0,'','',1,0,0),(5689595,5689581,'2','DPCC001',0,'','',1,0,0),(5689604,5689581,'3','DPCC002',0,'','',1,0,0),(5689613,5689581,'4','DPCC003',0,'','',1,0,0),(5689622,5689581,'5','DPCC004',0,'','',1,0,0),(5689631,5689581,'6','DPCC005',0,'','',1,0,0),(5689640,5689581,'7','DPCC006',0,'','',1,0,0),(5689649,5689581,'8','DPCC007',0,'','',1,0,0),(5689658,5689581,'9','DPCC008',0,'','',1,0,0),(5689667,5689581,'10','DPCC009',0,'','',1,0,0),(5689676,5689581,'11','DPCC010',0,'','',1,0,0),(5689685,5689581,'12','DPCC011',0,'','',1,0,0),(5689694,5689581,'13','DPCC012',0,'','',1,0,0),(5689703,5689581,'14','DPCC013',0,'','',1,0,0),(5689712,5689581,'15','DPCC014',0,'','',1,0,0),(5689721,5689581,'16','DPCC015',0,'','',1,0,0),(5689730,5689581,'17','DPCC016',0,'','',1,0,0),(5689739,5689581,'18','DPCC017',0,'','',1,0,0),(5689748,5689581,'19','DPCC018',0,'','',1,0,0),(5689757,5689581,'20','DPCC019',0,'','',1,0,0),(5689766,5689581,'21','DPCC020',0,'','',1,0,0),(5689775,5689581,'22','for Mercy',0,'','',1,0,0),(5689784,5689581,'23','MH0.01',0,'','',1,0,0),(5689793,5689581,'24','MH0.02',0,'','',1,0,0),(5689802,5689581,'25','MH0.03',0,'','',1,0,0),(5689811,5689581,'26','MH0.04',0,'','',1,0,0),(5689820,5689581,'27','MH0.05',0,'','',1,0,0),(5689829,5689581,'28','MH0.06',0,'','',1,0,0),(5689838,5689581,'29','MH0.07',0,'','',1,0,0),(5689847,5689581,'30','MH0.08',0,'','',1,0,0),(5689856,5689581,'31','MH0.09',0,'','',1,0,0),(5689865,5689581,'32','MH0.10',0,'','',1,0,0),(5689874,5689581,'33','MH0.11',0,'','',1,0,0),(5689883,5689581,'34','MH0.12',0,'','',1,0,0),(5689892,5689581,'35','MH0.13',0,'','',1,0,0),(5689901,5689581,'36','MH0.14',0,'','',1,0,0),(5689910,5689581,'37','MH0.15',0,'','',1,0,0),(5689919,5689581,'38','MH0.16',0,'','',1,0,0),(5689928,5689581,'39','MH0.17',0,'','',1,0,0),(5689937,5689581,'40','MH0.18',0,'','',1,0,0),(5689946,5689581,'41','MH0.19',0,'','',1,0,0),(5689955,5689581,'42','MH0.20',0,'','',1,0,0),(5689964,5689581,'43','MH0.21',0,'','',1,0,0),(5689973,5689581,'44','MH0.22',0,'','',1,0,0),(5689982,5689581,'45','MH0.23',0,'','',1,0,0),(5689991,5689581,'46','MH0.24',0,'','',1,0,0),(5690000,5689581,'47','MH0.25',0,'','',1,0,0),(5690009,5689581,'48','MH0.26',0,'','',1,0,0),(5690018,5689581,'49','MH0.27',0,'','',1,0,0),(5690027,5689581,'50','MH0.28',0,'','',1,0,0),(5690036,5689581,'51','MH0.29',0,'','',1,0,0),(5690045,5689581,'52','MH0.30',0,'','',1,0,0),(5690054,5689581,'53','MH0.31',0,'','',1,0,0),(5690063,5689581,'54','MH0.32',0,'','',1,0,0),(5690072,5689581,'55','MH0.33',0,'','',1,0,0),(5690081,5689581,'56','MH0.34',0,'','',1,0,0),(5690090,5689581,'57','MH1.01',0,'','',1,0,0),(5690099,5689581,'58','MH1.02',0,'','',1,0,0),(5690108,5689581,'59','MH1.03',0,'','',1,0,0),(5690117,5689581,'60','MH1.04',0,'','',1,0,0),(5690126,5689581,'61','MH1.05',0,'','',1,0,0),(5690135,5689581,'62','for AFC',0,'','',1,0,0),(5690144,5689581,'63','AFC.100',0,'','',1,0,0),(5690153,5689581,'64','AFC.101',0,'','',1,0,0),(5690162,5689581,'65','AFC.102',0,'','',1,0,0),(5690171,5689581,'66','AFC.103',0,'','',1,0,0),(5690180,5689581,'67','AFC.104',0,'','',1,0,0),(5690189,5689581,'68','AFC.105',0,'','',1,0,0),(5690198,5689581,'69','AFC.106',0,'','',1,0,0),(5690207,5689581,'70','AFC.107',0,'','',1,0,0),(5690216,5689581,'71','AFC.108',0,'','',1,0,0),(5690225,5689581,'72','Education_ Indiv - Asthma',0,'','',1,0,0),(5690234,5689581,'73','Education_ Indiv - Diabetes',0,'','',1,0,0),(5690243,5689581,'74','Education_ Indiv - Diet',0,'','',1,0,0),(5690252,5689581,'75','Education_ Indiv - Hl',0,'','',1,0,0),(5690261,5689581,'76','AFC.113',0,'','',1,0,0),(5690270,5689581,'77','AFC.114',0,'','',1,0,0),(5690279,5689581,'78','AFC.115',0,'','',1,0,0),(5690288,5689581,'79','AFC.116',0,'','',1,0,0),(5690297,5689581,'80','AFC.117',0,'','',1,0,0),(5690306,5689581,'81','AFC.118',0,'','',1,0,0),(5690315,5689581,'82','AFC.119',0,'','',1,0,0),(5690324,5689581,'83','AFC.120',0,'','',1,0,0),(5690333,5689581,'84','AFC.121',0,'','',1,0,0),(5690342,5689581,'85','AFC.122',0,'','',1,0,0),(5690351,5689581,'86','AFC.123',0,'','',1,0,0),(5690360,5689581,'87','AFC.124',0,'','',1,0,0),(5690369,5689581,'88','AFC.125',0,'','',1,0,0),(5690378,5689581,'89','AFC.126',0,'','',1,0,0),(5690387,5689581,'90','AFC.127',0,'','',1,0,0),(5690396,5689581,'91','AFC.128',0,'','',1,0,0),(5690405,5689581,'92','AFC.129',0,'','',1,0,0),(5690414,5689581,'93','AFC.130',0,'','',1,0,0),(5690423,5689581,'94','AFC.131',0,'','',1,0,0),(5690432,5689581,'95','AFC.132',0,'','',1,0,0),(5690441,5689581,'96','AFC.133',0,'','',1,0,0),(5690460,5690455,'1','for SCC, Mobile Med, Proyecto Salud, PCWC, MCHP, Holy Cross, MCC Medical Clinic, PCC',0,'','',1,0,0),(5690469,5690455,'2','DPCC001',0,'','',1,0,0),(5690478,5690455,'3','DPCC002',0,'','',1,0,0),(5690487,5690455,'4','DPCC003',0,'','',1,0,0),(5690496,5690455,'5','DPCC004',0,'','',1,0,0),(5690505,5690455,'6','DPCC005',0,'','',1,0,0),(5690514,5690455,'7','DPCC006',0,'','',1,0,0),(5690523,5690455,'8','DPCC007',0,'','',1,0,0),(5690532,5690455,'9','DPCC008',0,'','',1,0,0),(5690541,5690455,'10','DPCC009',0,'','',1,0,0),(5690550,5690455,'11','DPCC010',0,'','',1,0,0),(5690559,5690455,'12','DPCC011',0,'','',1,0,0),(5690568,5690455,'13','DPCC012',0,'','',1,0,0),(5690577,5690455,'14','DPCC013',0,'','',1,0,0),(5690586,5690455,'15','DPCC014',0,'','',1,0,0),(5690595,5690455,'16','DPCC015',0,'','',1,0,0),(5690604,5690455,'17','DPCC016',0,'','',1,0,0),(5690613,5690455,'18','DPCC017',0,'','',1,0,0),(5690622,5690455,'19','DPCC018',0,'','',1,0,0),(5690631,5690455,'20','DPCC019',0,'','',1,0,0),(5690640,5690455,'21','DPCC020',0,'','',1,0,0),(5690649,5690455,'22','for Mercy',0,'','',1,0,0),(5690658,5690455,'23','MH0.01',0,'','',1,0,0),(5690667,5690455,'24','MH0.02',0,'','',1,0,0),(5690676,5690455,'25','MH0.03',0,'','',1,0,0),(5690685,5690455,'26','MH0.04',0,'','',1,0,0),(5690694,5690455,'27','MH0.05',0,'','',1,0,0),(5690703,5690455,'28','MH0.06',0,'','',1,0,0),(5690712,5690455,'29','MH0.07',0,'','',1,0,0),(5690721,5690455,'30','MH0.08',0,'','',1,0,0),(5690730,5690455,'31','MH0.09',0,'','',1,0,0),(5690739,5690455,'32','MH0.10',0,'','',1,0,0),(5690748,5690455,'33','MH0.11',0,'','',1,0,0),(5690757,5690455,'34','MH0.12',0,'','',1,0,0),(5690766,5690455,'35','MH0.13',0,'','',1,0,0),(5690775,5690455,'36','MH0.14',0,'','',1,0,0),(5690784,5690455,'37','MH0.15',0,'','',1,0,0),(5690793,5690455,'38','MH0.16',0,'','',1,0,0),(5690802,5690455,'39','MH0.17',0,'','',1,0,0),(5690811,5690455,'40','MH0.18',0,'','',1,0,0),(5690820,5690455,'41','MH0.19',0,'','',1,0,0),(5690829,5690455,'42','MH0.20',0,'','',1,0,0),(5690838,5690455,'43','MH0.21',0,'','',1,0,0),(5690847,5690455,'44','MH0.22',0,'','',1,0,0),(5690856,5690455,'45','MH0.23',0,'','',1,0,0),(5690865,5690455,'46','MH0.24',0,'','',1,0,0),(5690874,5690455,'47','MH0.25',0,'','',1,0,0),(5690883,5690455,'48','MH0.26',0,'','',1,0,0),(5690892,5690455,'49','MH0.27',0,'','',1,0,0),(5690901,5690455,'50','MH0.28',0,'','',1,0,0),(5690910,5690455,'51','MH0.29',0,'','',1,0,0),(5690919,5690455,'52','MH0.30',0,'','',1,0,0),(5690928,5690455,'53','MH0.31',0,'','',1,0,0),(5690937,5690455,'54','MH0.32',0,'','',1,0,0),(5690946,5690455,'55','MH0.33',0,'','',1,0,0),(5690955,5690455,'56','MH0.34',0,'','',1,0,0),(5690964,5690455,'57','MH1.01',0,'','',1,0,0),(5690973,5690455,'58','MH1.02',0,'','',1,0,0),(5690982,5690455,'59','MH1.03',0,'','',1,0,0),(5690991,5690455,'60','MH1.04',0,'','',1,0,0),(5691000,5690455,'61','MH1.05',0,'','',1,0,0),(5691009,5690455,'62','for AFC',0,'','',1,0,0),(5691018,5690455,'63','AFC.100',0,'','',1,0,0),(5691027,5690455,'64','AFC.101',0,'','',1,0,0),(5691036,5690455,'65','AFC.102',0,'','',1,0,0),(5691045,5690455,'66','AFC.103',0,'','',1,0,0),(5691054,5690455,'67','AFC.104',0,'','',1,0,0),(5691063,5690455,'68','AFC.105',0,'','',1,0,0),(5691072,5690455,'69','AFC.106',0,'','',1,0,0),(5691081,5690455,'70','AFC.107',0,'','',1,0,0),(5691090,5690455,'71','AFC.108',0,'','',1,0,0),(5691099,5690455,'72','Education_ Indiv - Asthma',0,'','',1,0,0),(5691108,5690455,'73','Education_ Indiv - Diabetes',0,'','',1,0,0),(5691117,5690455,'74','Education_ Indiv - Diet',0,'','',1,0,0),(5691126,5690455,'75','Education_ Indiv - Hl',0,'','',1,0,0),(5691135,5690455,'76','AFC.113',0,'','',1,0,0),(5691144,5690455,'77','AFC.114',0,'','',1,0,0),(5691153,5690455,'78','AFC.115',0,'','',1,0,0),(5691162,5690455,'79','AFC.116',0,'','',1,0,0),(5691171,5690455,'80','AFC.117',0,'','',1,0,0),(5691180,5690455,'81','AFC.118',0,'','',1,0,0),(5691189,5690455,'82','AFC.119',0,'','',1,0,0),(5691198,5690455,'83','AFC.120',0,'','',1,0,0),(5691207,5690455,'84','AFC.121',0,'','',1,0,0),(5691216,5690455,'85','AFC.122',0,'','',1,0,0),(5691225,5690455,'86','AFC.123',0,'','',1,0,0),(5691234,5690455,'87','AFC.124',0,'','',1,0,0),(5691243,5690455,'88','AFC.125',0,'','',1,0,0),(5691252,5690455,'89','AFC.126',0,'','',1,0,0),(5691261,5690455,'90','AFC.127',0,'','',1,0,0),(5691270,5690455,'91','AFC.128',0,'','',1,0,0),(5691279,5690455,'92','AFC.129',0,'','',1,0,0),(5691288,5690455,'93','AFC.130',0,'','',1,0,0),(5691297,5690455,'94','AFC.131',0,'','',1,0,0),(5691306,5690455,'95','AFC.132',0,'','',1,0,0),(5691315,5690455,'96','AFC.133',0,'','',1,0,0),(5691334,68,'1','Shelter',1,'','',1,0,0),(5691343,68,'2','Homeless',1,'','',1,0,0),(5691352,68,'3','Transition Program',2,'','',1,0,0),(5691361,68,'4','Has Home',3,'','',1,0,0),(5691370,68,'5','Unknown',4,'','',1,0,0),(5691379,68,'6','Blank',5,'','',0,0,0),(5691393,69,'1','Montgomery',1,'','',1,0,0),(5691402,69,'2','District of Columbia',1,'','',1,0,0),(5691411,69,'3','Carroll',2,'','',1,0,0),(5691420,69,'4','Charles',3,'','',1,0,0),(5691429,69,'5','Howard',4,'','',1,0,0),(5691438,69,'6','La Grange',5,'','',1,0,0),(5691447,69,'7','Arlington',6,'','',1,0,0),(5691456,69,'8','Baltimore',7,'','',1,0,0),(5691465,69,'9','Calvert',8,'','',1,0,0),(5691474,69,'10','Culpepper',9,'','',1,0,0),(5691483,69,'11','Essex',10,'','',1,0,0),(5691492,69,'12','Fairfax',11,'','',1,0,0),(5691501,69,'13','Frederick',12,'','',1,0,0),(5691510,69,'14','Loudoun',13,'','',1,0,0),(5691519,69,'15','Manassas',14,'','',1,0,0),(5691528,69,'16','Prince William',15,'','',1,0,0),(5691537,69,'17','Prince Georges',16,'','',1,0,0),(5691546,69,'18','Somerset',17,'','',1,0,0),(5691560,70,'1','Head of Household',0,'','',1,0,0),(5691569,70,'2','Not Head of Household',0,'','',1,0,0),(5691578,70,'3','Unknown',0,'','',1,0,0),(5691587,70,'4','Blank',0,'','',1,0,0),(5691601,71,'1','English',1,'','',1,0,0),(5691610,71,'2','Spanish',1,'','',1,0,0),(5691619,71,'3','Amharic',2,'','',1,0,0),(5691628,71,'4','Arabic',3,'','',1,0,0),(5691637,71,'5','Armenian',4,'','',1,0,0),(5691646,71,'6','Bengali',5,'','',1,0,0),(5691655,71,'7','Chinese',6,'','',1,0,0),(5691664,71,'8','Farsi',7,'','',1,0,0),(5691673,71,'9','French',8,'','',1,0,0),(5691682,71,'10','German',9,'','',1,0,0),(5691691,71,'11','Hindi',10,'','',1,0,0),(5691700,71,'12','Indonesian',11,'','',1,0,0),(5691709,71,'13','Korean',12,'','',1,0,0),(5691718,71,'14','Mongolian',13,'','',1,0,0),(5691727,71,'15','Russian',14,'','',1,0,0),(5691736,71,'16','Tagalog',15,'','',1,0,0),(5691745,71,'17','Tigrigna',16,'','',1,0,0),(5691754,71,'18','Urdu',17,'','',1,0,0),(5691763,71,'19','Vietnamese',18,'','',1,0,0),(5691772,71,'20','Other',19,'','',1,0,0),(5691781,71,'21','Unknown',20,'','',1,0,0),(5691790,71,'22','Blank',21,'','',1,0,0),(5691804,72,'1','Proficient',1,'','',1,0,0),(5691813,72,'2','Somewhat Proficient',1,'','',1,0,0),(5691822,72,'3','Limited',2,'','',1,0,0),(5691831,72,'4','Not Proficient',3,'','',1,0,0),(5691840,72,'5','Unknown',4,'','',1,0,0),(5691849,72,'6','Blank',5,'','',1,0,0),(5691863,73,'1','Afghanistan',1,'','',1,0,0),(5691872,73,'2','Albania',1,'','',1,0,0),(5691881,73,'3','Algeria',2,'','',1,0,0),(5691890,73,'4','American Samoa',3,'','',1,0,0),(5691899,73,'5','Andorra',4,'','',1,0,0),(5691908,73,'6','Angola',5,'','',1,0,0),(5691917,73,'7','Anguilla',6,'','',1,0,0),(5691926,73,'8','Antigua and Barbuda',7,'','',1,0,0),(5691935,73,'9','Argentina',8,'','',1,0,0),(5691944,73,'10','Armenia',9,'','',1,0,0),(5691953,73,'11','Aruba',10,'','',1,0,0),(5691962,73,'12','Australia',11,'','',1,0,0),(5691971,73,'13','Austria',12,'','',1,0,0),(5691980,73,'14','Azerbajan',13,'','',1,0,0),(5691989,73,'15','Azores (Portugal)',14,'','',1,0,0),(5691998,73,'16','Bahamas',15,'','',1,0,0),(5692007,73,'17','Bahrain',16,'','',1,0,0),(5692016,73,'18','Bangladesh',17,'','',1,0,0),(5692025,73,'19','Barbados',18,'','',1,0,0),(5692034,73,'20','Belarus',19,'','',1,0,0),(5692043,73,'21','Belgium',20,'','',1,0,0),(5692052,73,'22','Belize',21,'','',1,0,0),(5692061,73,'23','Benin',22,'','',1,0,0),(5692070,73,'24','Bermuda',23,'','',1,0,0),(5692079,73,'25','Bolivia',24,'','',1,0,0),(5692088,73,'26','Bonaire (Netherlands Antilles)',25,'','',1,0,0),(5692097,73,'27','Bosnia',26,'','',1,0,0),(5692106,73,'28','Botswana',27,'','',1,0,0),(5692115,73,'29','Brazil',28,'','',1,0,0),(5692124,73,'30','British Virgin Islands',29,'','',1,0,0),(5692133,73,'31','Brunei',30,'','',1,0,0),(5692142,73,'32','Bulgaria',31,'','',1,0,0),(5692151,73,'33','Burkina Faso',32,'','',1,0,0),(5692160,73,'34','Burundi',33,'','',1,0,0),(5692169,73,'35','Cambodia',34,'','',1,0,0),(5692178,73,'36','Cameroom',35,'','',1,0,0),(5692187,73,'37','Canada',36,'','',1,0,0),(5692196,73,'38','Canary Islands',37,'','',1,0,0),(5692205,73,'39','Cape Verde',38,'','',1,0,0),(5692214,73,'40','Cayman Islands',39,'','',1,0,0),(5692223,73,'41','Central African Republic',40,'','',1,0,0),(5692232,73,'42','Chad',41,'','',1,0,0),(5692241,73,'43','Channel Islands',42,'','',1,0,0),(5692250,73,'44','Chile',43,'','',1,0,0),(5692259,73,'45','China',44,'','',1,0,0),(5692268,73,'46','Colombia',45,'','',1,0,0),(5692277,73,'47','Congo-Democratic Republic of',46,'','',1,0,0),(5692286,73,'48','Congo-Republic of',47,'','',1,0,0),(5692295,73,'49','Cook Islands',48,'','',1,0,0),(5692304,73,'50','Costa Rica',49,'','',1,0,0),(5692313,73,'51','Croatia',50,'','',1,0,0),(5692322,73,'52','Cuba',51,'','',1,0,0),(5692331,73,'53','Curacao (Netherlands Antilles)',52,'','',1,0,0),(5692340,73,'54','Cyprus',53,'','',1,0,0),(5692349,73,'55','Czech Republic',54,'','',1,0,0),(5692358,73,'56','Denmark',55,'','',1,0,0),(5692367,73,'57','Djibouti',56,'','',1,0,0),(5692376,73,'58','Dominica',57,'','',1,0,0),(5692385,73,'59','Dominican Republic',58,'','',1,0,0),(5692394,73,'60','Ecuador',59,'','',1,0,0),(5692403,73,'61','Eqypt',60,'','',1,0,0),(5692412,73,'62','El Salvador',61,'','',1,0,0),(5692421,73,'63','England',62,'','',1,0,0),(5692430,73,'64','Equatorial Guniea',63,'','',1,0,0),(5692439,73,'65','Eritrea',64,'','',1,0,0),(5692448,73,'66','Estonia',65,'','',1,0,0),(5692457,73,'67','Ethiopia',66,'','',1,0,0),(5692466,73,'68','Faroe Islands (Denmark)',67,'','',1,0,0),(5692475,73,'69','Fiji',68,'','',1,0,0),(5692484,73,'70','Finland',69,'','',1,0,0),(5692493,73,'71','France',70,'','',1,0,0),(5692502,73,'72','French Guiana',71,'','',1,0,0),(5692511,73,'73','French Polynesia',72,'','',1,0,0),(5692520,73,'74','Gabon',73,'','',1,0,0),(5692529,73,'75','Gambia',74,'','',1,0,0),(5692538,73,'76','Georgia',75,'','',1,0,0),(5692547,73,'77','Germany',76,'','',1,0,0),(5692556,73,'78','Ghana',77,'','',1,0,0),(5692565,73,'79','Gilbraltar',78,'','',1,0,0),(5692574,73,'80','Greece',79,'','',1,0,0),(5692583,73,'81','Greenland (Denmark)',80,'','',1,0,0),(5692592,73,'82','Grenada',81,'','',1,0,0),(5692601,73,'83','Guadeloupe',82,'','',1,0,0),(5692610,73,'84','Guam',83,'','',1,0,0),(5692619,73,'85','Guatemala',84,'','',1,0,0),(5692628,73,'86','Guinea',85,'','',1,0,0),(5692637,73,'87','Guinea-Bissau',86,'','',1,0,0),(5692646,73,'88','Guyana',87,'','',1,0,0),(5692655,73,'89','Haiti',88,'','',1,0,0),(5692664,73,'90','Holland (Netherlands)',89,'','',1,0,0),(5692673,73,'91','Honduras',90,'','',1,0,0),(5692682,73,'92','Hong Kong',91,'','',1,0,0),(5692691,73,'93','Hungary',92,'','',1,0,0),(5692700,73,'94','Iceland',93,'','',1,0,0),(5692709,73,'95','India',94,'','',1,0,0),(5692718,73,'96','Indonesia',95,'','',1,0,0),(5692727,73,'97','Iran',96,'','',1,0,0),(5692736,73,'98','Iraq',97,'','',1,0,0),(5692745,73,'99','Ireland -Republic of',98,'','',1,0,0),(5692754,73,'100','Israel',99,'','',1,0,0),(5692763,73,'101','Italy',100,'','',1,0,0),(5692772,73,'102','Ivory Coast',101,'','',1,0,0),(5692781,73,'103','Jamaica',102,'','',1,0,0),(5692790,73,'104','Japan',103,'','',1,0,0),(5692799,73,'105','Kazakhstan',104,'','',1,0,0),(5692808,73,'106','Kenya',105,'','',1,0,0),(5692817,73,'107','Kiribati',106,'','',1,0,0),(5692826,73,'108','Korea (South Korea)',107,'','',1,0,0),(5692835,73,'109','Korsrae (Federated States of Micronesia)',108,'','',1,0,0),(5692844,73,'110','Kuwait',109,'','',1,0,0),(5692853,73,'111','Kyrgyzstan',110,'','',1,0,0),(5692862,73,'112','Laos',111,'','',1,0,0),(5692871,73,'113','Latvia',112,'','',1,0,0),(5692880,73,'114','Lebanon',113,'','',1,0,0),(5692889,73,'115','Lesotho',114,'','',1,0,0),(5692898,73,'116','Liberia',115,'','',1,0,0),(5692907,73,'117','Liechtenstein',116,'','',1,0,0),(5692916,73,'118','Lithuania',117,'','',1,0,0),(5692925,73,'119','Macau',118,'','',1,0,0),(5692934,73,'120','Macedonia',119,'','',1,0,0),(5692943,73,'121','Madagascar',120,'','',1,0,0),(5692952,73,'122','Maderia (Portugal)',121,'','',1,0,0),(5692961,73,'123','Malawi',122,'','',1,0,0),(5692970,73,'124','Malaysia',123,'','',1,0,0),(5692979,73,'125','Maldives',124,'','',1,0,0),(5692988,73,'126','Mali',125,'','',1,0,0),(5692997,73,'127','Malta',126,'','',1,0,0),(5693006,73,'128','Marshall Islands',127,'','',1,0,0),(5693015,73,'129','Martinique',128,'','',1,0,0),(5693024,73,'130','Mauritius',129,'','',1,0,0),(5693033,73,'131','Mexico',130,'','',1,0,0),(5693042,73,'132','Micronesia - Federated States of',131,'','',1,0,0),(5693051,73,'133','Moldova',132,'','',1,0,0),(5693060,73,'134','Monaco',133,'','',1,0,0),(5693069,73,'135','Mongolia',134,'','',1,0,0),(5693078,73,'136','Montserrat',135,'','',1,0,0),(5693087,73,'137','Morocco',136,'','',1,0,0),(5693096,73,'138','Mozambique',137,'','',1,0,0),(5693105,73,'139','Nambia',138,'','',1,0,0),(5693114,73,'140','Nepal',139,'','',1,0,0),(5693123,73,'141','Netherlands (Holland)',140,'','',1,0,0),(5693132,73,'142','Netherlands Antilles',141,'','',1,0,0),(5693141,73,'143','New Caledonia',142,'','',1,0,0),(5693150,73,'144','New Zealand',143,'','',1,0,0),(5693159,73,'145','Nicaragua',144,'','',1,0,0),(5693168,73,'146','Niger',145,'','',1,0,0),(5693177,73,'147','Nigeria',146,'','',1,0,0),(5693186,73,'148','Norfolk Island',147,'','',1,0,0),(5693195,73,'149','Northern Ireland (UK)',148,'','',1,0,0),(5693204,73,'150','Northern Mariana Islands',149,'','',1,0,0),(5693213,73,'151','Norway',150,'','',1,0,0),(5693222,73,'152','Oman',151,'','',1,0,0),(5693231,73,'153','Pakistan',152,'','',1,0,0),(5693240,73,'154','Palau',153,'','',1,0,0),(5693249,73,'155','Panama',154,'','',1,0,0),(5693258,73,'156','Papua New Guinea',155,'','',1,0,0),(5693267,73,'157','Paraguay',156,'','',1,0,0),(5693276,73,'158','Peru',157,'','',1,0,0),(5693285,73,'159','Philippines',158,'','',1,0,0),(5693294,73,'160','Poland',159,'','',1,0,0),(5693303,73,'161','Ponape (Federated States of Micronesia)',160,'','',1,0,0),(5693312,73,'162','Portugal',161,'','',1,0,0),(5693321,73,'163','Qatar',162,'','',1,0,0),(5693330,73,'164','Reunion',163,'','',1,0,0),(5693339,73,'165','Romania',164,'','',1,0,0),(5693348,73,'166','Rota (Northern Mariana Islands)',165,'','',1,0,0),(5693357,73,'167','Russia',166,'','',1,0,0),(5693366,73,'168','Rwanda',167,'','',1,0,0),(5693375,73,'169','Saba (Netherlands Antilles)',168,'','',1,0,0),(5693384,73,'170','Saipan (Northern Mariana Islands)',169,'','',1,0,0),(5693393,73,'171','San Marino',170,'','',1,0,0),(5693402,73,'172','Saudia Arabia',171,'','',1,0,0),(5693411,73,'173','Scotland (United Kingdom)',172,'','',1,0,0),(5693420,73,'174','Senegal',173,'','',1,0,0),(5693429,73,'175','Seychelles',174,'','',1,0,0),(5693438,73,'176','Sierra Leone',175,'','',1,0,0),(5693447,73,'177','Singapore',176,'','',1,0,0),(5693456,73,'178','Slovakia',177,'','',1,0,0),(5693465,73,'179','Slovenia',178,'','',1,0,0),(5693474,73,'180','Solomon Islands',179,'','',1,0,0),(5693483,73,'181','Somalia',180,'','',1,0,0),(5693492,73,'182','South Africa',181,'','',1,0,0),(5693501,73,'183','Spain',182,'','',1,0,0),(5693510,73,'184','Sir Lanki',183,'','',1,0,0),(5693519,73,'185','St. Barthelemy (Guadeloupe)',184,'','',1,0,0),(5693528,73,'186','St. Christopher (St. Kitts and Nevis)',185,'','',1,0,0),(5693537,73,'187','St. Croix (U.S. Virgin Islands)',186,'','',1,0,0),(5693546,73,'188','St. Eustatius (Netherlands Antilles)',187,'','',1,0,0),(5693555,73,'189','St. John ((U.S. Virgin Islands)',188,'','',1,0,0),(5693564,73,'190','St. Kitts and Nevis',189,'','',1,0,0),(5693573,73,'191','St. Lucia',190,'','',1,0,0),(5693582,73,'192','St. Martin (Guadeloupe)',191,'','',1,0,0),(5693591,73,'193','St. Thomas (U.S. Virgin Islands)',192,'','',1,0,0),(5693600,73,'194','St. Vincent and the Grenadines',193,'','',1,0,0),(5693609,73,'195','Suriname',194,'','',1,0,0),(5693618,73,'196','Swaziland',195,'','',1,0,0),(5693627,73,'197','Sweden',196,'','',1,0,0),(5693636,73,'198','Switzerland',197,'','',1,0,0),(5693645,73,'199','Syria',198,'','',1,0,0),(5693654,73,'200','Tahiti (French Polynesia)',199,'','',1,0,0),(5693663,73,'201','Taiwan',200,'','',1,0,0),(5693672,73,'202','Tajikistan',201,'','',1,0,0),(5693681,73,'203','Tanzania',202,'','',1,0,0),(5693690,73,'204','Thailand',203,'','',1,0,0),(5693699,73,'205','Tinian (Northern Mariana Islands)',204,'','',1,0,0),(5693708,73,'206','Togo',205,'','',1,0,0),(5693717,73,'207','Tonga',206,'','',1,0,0),(5693726,73,'208','Tortola (British Virgin Islands)',207,'','',1,0,0),(5693735,73,'209','Trinidad and Tobago',208,'','',1,0,0),(5693744,73,'210','Truk (Federated States of Micronesia)',209,'','',1,0,0),(5693753,73,'211','Tunisia',210,'','',1,0,0),(5693762,73,'212','Turkey',211,'','',1,0,0),(5693771,73,'213','Turkmenistan',212,'','',1,0,0),(5693780,73,'214','Turks and Caicos Islands',213,'','',1,0,0),(5693789,73,'215','Tuvalu',214,'','',1,0,0),(5693798,73,'216','U.S. Virgin Islands',215,'','',1,0,0),(5693807,73,'217','Uganda',216,'','',1,0,0),(5693816,73,'218','Ukraine',217,'','',1,0,0),(5693825,73,'219','Union Island (St. Vincent and the Grenadines)',218,'','',1,0,0),(5693834,73,'220','United Arab Emirates',219,'','',1,0,0),(5693843,73,'221','United Kingdom',220,'','',1,0,0),(5693852,73,'222','Unknown',221,'','',1,0,0),(5693861,73,'223','Uruguay',222,'','',1,0,0),(5693870,73,'224','USA',223,'','',1,0,0),(5693879,73,'225','Uzbekistan',224,'','',1,0,0),(5693888,73,'226','Vanuatu',225,'','',1,0,0),(5693897,73,'227','Venezuela',226,'','',1,0,0),(5693906,73,'228','Vietnam',227,'','',1,0,0),(5693915,73,'229','Virgin Gorda (British Virgin Islands)',228,'','',1,0,0),(5693924,73,'230','Wake Island',229,'','',1,0,0),(5693933,73,'231','Wales (United Kingdom)',230,'','',1,0,0),(5693942,73,'232','Wallis and Futuna Islands',231,'','',1,0,0),(5693951,73,'233','Western Samoa',232,'','',1,0,0),(5693960,73,'234','Yap (Federated States of Micronesia)',233,'','',1,0,0),(5693969,73,'235','Yemen',234,'','',1,0,0),(5693978,73,'236','Zaire (Democratic Republic of Congo)',235,'','',1,0,0),(5693987,73,'237','Zambia',236,'','',1,0,0),(5693996,73,'238','Zimbabwe',237,'','',1,0,0),(5694005,73,'239','Blank',238,'','',1,0,0),(5694019,74,'1','Other Christian',0,'','',1,0,0),(5694028,74,'2','Muslim',0,'','',1,0,0),(5694037,74,'3','Jewish',0,'','',1,0,0),(5694046,74,'4','Buddhist',0,'','',1,0,0),(5694055,74,'5','Hindu',0,'','',1,0,0),(5694064,74,'6','Catholic',0,'','',1,0,0),(5694073,74,'7','Jehovah\'s Witness',0,'','',1,0,0),(5694082,74,'8','Mormon',0,'','',1,0,0),(5694091,74,'9','None',0,'','',1,0,0),(5694100,74,'10','Orthodox',0,'','',1,0,0),(5694109,74,'11','Protestant',0,'','',1,0,0),(5694118,74,'12','Other',0,'','',1,0,0),(5694127,74,'13','Unknown',0,'','',1,0,0),(5694136,74,'14','Blank',0,'','',1,0,0),(5694150,75,'1','Employed',0,'','',1,0,0),(5694159,75,'2','Unemployed',0,'','',1,0,0),(5694168,75,'3','Unknown',0,'','',1,0,0),(5694177,75,'4','Blank',0,'','',1,0,0),(5694191,76,'1','Unknown',0,'','',1,0,0),(5694200,76,'2','None-illiterate',0,'','',1,0,0),(5694209,76,'3','Some Elementary Education',0,'','',1,0,0),(5694218,76,'4','Some Middle School',0,'','',1,0,0),(5694227,76,'5','Some High School',0,'','',1,0,0),(5694236,76,'6','High School Degree',0,'','',1,0,0),(5694245,76,'7','Vocational/Tech School',0,'','',1,0,0),(5694254,76,'8','Some College',0,'','',1,0,0),(5694263,76,'9','Associates Degree',0,'','',1,0,0),(5694272,76,'10','Bachelors Degree',0,'','',1,0,0),(5694281,76,'11','Post Grad College',0,'','',1,0,0),(5694290,76,'12','Masters Degree',0,'','',1,0,0),(5694299,76,'13','Advanced Degree',0,'','',1,0,0),(5694308,76,'14','Other',0,'','',1,0,0),(5694317,76,'15','Blank',0,'','',1,0,0),(5694331,77,'1','Yes',1,'','',1,0,0),(5694340,77,'2','No',1,'','',1,0,0),(5694349,77,'3','Unknown',2,'','',1,0,0),(5694363,78,'1','Asthma',1,'','',1,0,0),(5694372,78,'2','Anti-Coagulation',1,'','',1,0,0),(5694381,78,'3','Cancer',2,'','',1,0,0),(5694390,78,'4','CHF (Congestive Heart Faliure)',3,'','',1,0,0),(5694399,78,'5','CVA (Cerbrovascular Accident, Stroke)',4,'','',1,0,0),(5694408,78,'6','Depression',5,'','',1,0,0),(5694417,78,'7','Diabetes',6,'','',1,0,0),(5694426,78,'8','Diabetes Type I',7,'','',1,0,0),(5694435,78,'9','Diabetes Type II',8,'','',1,0,0),(5694444,78,'10','Hyperlipidemia',9,'','',1,0,0),(5694453,78,'11','Hypertension',10,'','',1,0,0),(5694462,78,'12','Nephropathy',11,'','',1,0,0),(5694471,78,'13','Neuropathy',12,'','',1,0,0),(5694480,78,'14','NKF',13,'','',1,0,0),(5694489,78,'15','Obesity',14,'','',1,0,0),(5694498,78,'16','Post MI',15,'','',1,0,0),(5694507,78,'17','PVD (Peripheralvascular Disease)',16,'','',1,0,0),(5694516,78,'18','Renal Faliure',17,'','',1,0,0),(5694525,78,'19','Retinopathy',18,'','',1,0,0),(5694539,79,'1','MPC',0,'','',1,0,0),(5694548,79,'2','PCMI',0,'','',1,0,0),(5694557,79,'3','DCHCA',0,'','',1,0,0),(5694566,79,'4','MCCP',0,'','',1,0,0),(5694575,79,'5','CFK',0,'','',1,0,0),(5694584,79,'6','None',0,'','',1,0,0),(5694598,80,'1','Yes',0,'','',1,0,0),(5694607,80,'2','No',0,'','',1,0,0),(5694616,80,'3','Unknown',0,'','',1,0,0),(5694630,81,'1','Food',1,'','',1,0,0),(5694648,81,'3','apples',2,'','',1,1,5694630),(5694657,81,'4','Shrimp',3,'','',1,1,5694630),(5694666,81,'5','cherries',4,'','',1,1,5694630),(5694675,81,'6','Coffee',5,'','',1,1,5694630),(5694684,81,'7','Crayfish',6,'','',1,1,5694630),(5694693,81,'8','Egg',7,'','',1,1,5694630),(5694702,81,'9','fish',8,'','',1,1,5694630),(5694711,81,'10','Tomato',9,'','',1,1,5694630),(5694720,81,'11','Hot peppers',10,'','',1,1,5694630),(5694729,81,'12','peaches',11,'','',1,1,5694630),(5694738,81,'13','peanuts',12,'','',1,1,5694630),(5694747,81,'14','Pork',13,'','',1,1,5694630),(5694756,81,'15','Seafood',14,'','',1,1,5694630),(5694765,81,'16','Watermelon',15,'','',1,1,5694630),(5694774,81,'17','Yeast',16,'','',1,1,5694630),(5694783,81,'18','Meds',17,'','',1,0,0),(5694801,81,'20','pyridium',19,'','',1,1,5694783),(5694810,81,'21','ACE inhibitors',20,'','',1,1,5694783),(5694819,81,'22','Acetaminofen',21,'','',1,1,5694783),(5694828,81,'23','Adalop',22,'','',1,1,5694783),(5694837,81,'24','Advil',23,'','',1,1,5694783),(5694846,81,'25','Aleve',24,'','',1,1,5694783),(5694855,81,'26','Alka Seltzer',25,'','',1,1,5694783),(5694864,81,'27','Antibotics',26,'','',1,1,5694783),(5694873,81,'28','Amitriptyline',27,'','',1,1,5694783),(5694882,81,'29','Amoxicillin',28,'','',1,1,5694783),(5694891,81,'30','Ampicilina',29,'','',1,1,5694783),(5694900,81,'31','anaprox',30,'','',1,1,5694783),(5694909,81,'32','anestesics',31,'','',1,1,5694783),(5694918,81,'33','Anesthesia',32,'','',1,1,5694783),(5694927,81,'34','aspirin',33,'','',1,1,5694783),(5694936,81,'35','augmentin',34,'','',1,1,5694783),(5694945,81,'36','B-12',35,'','',1,1,5694783),(5694954,81,'37','Bactim',36,'','',1,1,5694783),(5694963,81,'38','benedril',37,'','',1,1,5694783),(5694972,81,'39','ceflin',38,'','',1,1,5694783),(5694981,81,'40','Celebrex',39,'','',1,1,5694783),(5694990,81,'41','Celexa',40,'','',1,1,5694783),(5694999,81,'42','Cephalexin',41,'','',1,1,5694783),(5695008,81,'43','chloroquine',42,'','',1,0,0),(5695017,81,'44','Cipro',43,'','',1,1,5695008),(5695026,81,'45','Citamol',44,'','',1,1,5695008),(5695035,81,'46','Claritin',45,'','',1,1,5695008),(5695044,81,'47','Cloramfenicol',46,'','',1,1,5695008),(5695053,81,'48','Clyndomycin',47,'','',1,1,5695008),(5695062,81,'49','codeine',48,'','',1,1,5695008),(5695071,81,'50','compazine',49,'','',1,1,5695008),(5695080,81,'51','Cortison (rash, angroedema)',50,'','',1,1,5695008),(5695089,81,'52','Dexacort',51,'','',1,1,5695008),(5695098,81,'53','Dilantin',52,'','',1,1,5695008),(5695107,81,'54','Elavil',53,'','',1,1,5695008),(5695116,81,'55','Eritromicina',54,'','',1,1,5695008),(5695125,81,'56','erythancin',55,'','',1,1,5695008),(5695134,81,'57','erythromycin',56,'','',1,1,5695008),(5695143,81,'58','Flexeril',57,'','',1,1,5695008),(5695152,81,'59','Furoxona',58,'','',1,1,5695008),(5695161,81,'60','General Anestisia',59,'','',1,1,5695008),(5695170,81,'61','Ibuprofen',60,'','',1,1,5695008),(5695179,81,'62','Lantus',61,'','',1,1,5695008),(5695188,81,'63','Levaquin',62,'','',1,1,5695008),(5695197,81,'64','Lisinopril',63,'','',1,1,5695008),(5695206,81,'65','Local anasthetic(Canbocaine??)',64,'','',1,1,5695008),(5695215,81,'66','Losartan',65,'','',1,1,5695008),(5695224,81,'67','Maxzide',66,'','',1,1,5695008),(5695233,81,'68','Mentholation',67,'','',1,1,5695008),(5695242,81,'69','morphine',68,'','',1,1,5695008),(5695251,81,'70','motrin',69,'','',1,1,5695008),(5695260,81,'71','naprocin',70,'','',1,1,5695008),(5695269,81,'72','No Know Drug Allergies',71,'','',1,1,5695008),(5695278,81,'73','Penicillin',72,'','',1,1,5695008),(5695287,81,'74','Percocet',73,'','',1,1,5695008),(5695296,81,'75','Prinivil',74,'','',1,1,5695008),(5695305,81,'76','Quinine (Quinidine)',75,'','',1,1,5695008),(5695314,81,'77','Relafen',76,'','',1,1,5695008),(5695323,81,'78','rocephin',77,'','',1,1,5695008),(5695332,81,'79','Sulfa',78,'','',1,1,5695008),(5695341,81,'80','tegretol',79,'','',1,1,5695008),(5695350,81,'81','Tetracycline',80,'','',1,1,5695008),(5695359,81,'82','Thorazine',81,'','',1,1,5695008),(5695368,81,'83','Codeine',82,'','',1,1,5695008),(5695377,81,'84','tylenor/acetaminophen',83,'','',1,1,5695008),(5695386,81,'85','Ultram',84,'','',1,1,5695008),(5695395,81,'86','Vancomycin',85,'','',1,1,5695008),(5695404,81,'87','Vasotec',86,'','',1,1,5695008),(5695413,81,'88','Vioxx',87,'','',1,1,5695008),(5695422,81,'89','xlonipin',88,'','',1,1,5695008),(5695431,81,'90','Zestril',89,'','',1,1,5695008),(5695440,81,'91','Zocor',90,'','',1,1,5695008),(5695449,81,'92','Chemicals',91,'','',1,0,0),(5695467,81,'94','iodine',93,'','',1,1,5695449),(5695476,81,'95','chemicals',94,'','',1,1,5695449),(5695485,81,'96','Iodine',95,'','',0,1,5695449),(5695494,81,'97','Latex',96,'','',1,1,5695449),(5695503,81,'98','metals',97,'','',1,1,5695449),(5695512,81,'99','Peroxide',98,'','',1,1,5695449),(5695521,81,'100','Potassium',99,'','',1,1,5695449),(5695530,81,'101','Sodium',100,'','',1,1,5695521),(5695539,81,'102','sodium pentathol',101,'','',1,1,5695521),(5695548,81,'103','Environment',102,'','',1,0,0),(5695566,81,'105','Dust',104,'','',1,1,5695548),(5695575,81,'106','Hay fever',105,'','',1,1,5695548),(5695584,81,'107','Pollen',106,'','',1,1,5695548),(5695593,81,'108','Seasonal',107,'','',1,1,5695548),(5695602,81,'109','sun',108,'','',1,1,5695548),(5695611,81,'110','Other',109,'','',1,0,0),(5695634,82,'1','DT (Diphtheria, Tetanus) (90702)',1,'','',1,0,0),(5695643,82,'2','DTaP (Diphtheria, Tetanus, aPertussis) (90700)',1,'','',1,0,0),(5695652,82,'3','Flu Vaccine (90655-90658)',2,'','',1,0,0),(5695661,82,'4','Hepatitis B (90746)',3,'','',1,0,0),(5695670,82,'5','Hepatitis B - 1st (90746)',4,'','',1,0,0),(5695679,82,'6','Hepatitis B - 2nd (90746)',5,'','',1,0,0),(5695688,82,'7','Hepatitis B - 3rd (90746)',6,'','',1,0,0),(5695697,82,'8','Hib (Haem Influenza type b) (90645-90648)',7,'','',1,0,0),(5695706,82,'9','MMR (Measles, Mumps, Rubella) (90707)',8,'','',1,0,0),(5695715,82,'10','Pneumovax (90669, 90732)',9,'','',1,0,0),(5695724,82,'11','IPV (Polio) (90713)',10,'','',1,0,0),(5695733,82,'12','PPD (TB test) (86580)',11,'','',1,0,0),(5695742,82,'13','Td (Tetanus, Diphtheria) (90718)',12,'','',1,0,0),(5695751,82,'14','Tetanus toxoid (90703)',13,'','',1,0,0),(5695760,82,'15','Tuberculosis (BCG) (90585)',14,'','',1,0,0),(5695769,82,'16','Varicella (Chickenpox) (90716)',15,'','',1,0,0),(5695778,82,'17','Blank',16,'','',1,0,0),(5695792,83,'1','HIV/AIDS',0,'','',1,0,0),(5695801,83,'2','Anemia',0,'','',1,0,0),(5695810,83,'3','Arthritis',0,'','',1,0,0),(5695819,83,'4','Asthma',0,'','',1,0,0),(5695828,83,'5','Cancer',0,'','',1,0,0),(5695837,83,'6','Diabetes',0,'','',1,0,0),(5695846,83,'7','Emotional Prob',0,'','',1,0,0),(5695855,83,'8','TB skin test?',0,'','',1,0,0),(5695864,83,'9','Gallbladder',0,'','',1,0,0),(5695873,83,'10','Heart Problems',0,'','',1,0,0),(5695882,83,'11','Hepatitis/Liver',0,'','',1,0,0),(5695891,83,'12','High Blood Pressure',0,'','',1,0,0),(5695900,83,'13','High Cholesterol',0,'','',1,0,0),(5695909,83,'14','Kidney Problems',0,'','',1,0,0),(5695918,83,'15','Lung Problems',0,'','',1,0,0),(5695927,83,'16','Allergies',0,'','',1,0,0),(5695936,83,'17','Menstral Problems',0,'','',1,0,0),(5695945,83,'18','Rheumatic Fever',0,'','',1,0,0),(5695954,83,'19','Sexually transmitted disease',0,'','',1,0,0),(5695963,83,'20','Stomach Problems',0,'','',1,0,0),(5695972,83,'21','Stroke',0,'','',1,0,0),(5695981,83,'22','Thyroid Problems',0,'','',1,0,0),(5695990,83,'23','Tuberculosis',0,'','',1,0,0),(5695999,83,'24','Blank',0,'','',1,0,0),(5696013,84,'1','HIV/AIDS',0,'','',1,0,0),(5696022,84,'2','Anemia',0,'','',1,0,0),(5696031,84,'3','Arthritis',0,'','',1,0,0),(5696040,84,'4','Asthma',0,'','',1,0,0),(5696049,84,'5','Cancer',0,'','',1,0,0),(5696058,84,'6','Diabetes',0,'','',1,0,0),(5696067,84,'7','Emotional Prob',0,'','',1,0,0),(5696076,84,'8','TB skin test?',0,'','',1,0,0),(5696085,84,'9','Gallbladder',0,'','',1,0,0),(5696094,84,'10','Heart Problems',0,'','',1,0,0),(5696103,84,'11','Hepatitis/Liver',0,'','',1,0,0),(5696112,84,'12','High Blood Pressure',0,'','',1,0,0),(5696121,84,'13','High Cholesterol',0,'','',1,0,0),(5696130,84,'14','Kidney Problems',0,'','',1,0,0),(5696139,84,'15','Lung Problems',0,'','',1,0,0),(5696148,84,'16','Allergies',0,'','',1,0,0),(5696157,84,'17','Menstral Problems',0,'','',1,0,0),(5696166,84,'18','Rheumatic Fever',0,'','',1,0,0),(5696175,84,'19','Sexually transmitted disease',0,'','',1,0,0),(5696184,84,'20','Stomach Problems',0,'','',1,0,0),(5696193,84,'21','Stroke',0,'','',1,0,0),(5696202,84,'22','Thyroid Problems',0,'','',1,0,0),(5696211,84,'23','Tuberculosis',0,'','',1,0,0),(5696220,84,'24','Blank',0,'','',1,0,0),(5696234,85,'1','Aunt or Uncle',0,'','',1,0,0),(5696243,85,'2','Child (adoptive)',0,'','',1,0,0),(5696252,85,'3','Child (biological)',0,'','',1,0,0),(5696261,85,'4','Cousin',0,'','',1,0,0),(5696270,85,'5','Grandchild',0,'','',1,0,0),(5696279,85,'6','Grandparent (adoptive)',0,'','',1,0,0),(5696288,85,'7','Grandparent (biological)',0,'','',1,0,0),(5696297,85,'8','Half Sibling',0,'','',1,0,0),(5696306,85,'9','Legal Guardian',0,'','',1,0,0),(5696315,85,'10','Niece or Nephew',0,'','',1,0,0),(5696324,85,'11','Other',0,'','',1,0,0),(5696333,85,'12','Parent (adoptive)',0,'','',1,0,0),(5696342,85,'13','Parent (biological)',0,'','',1,0,0),(5696351,85,'14','Parent (step)',0,'','',1,0,0),(5696360,85,'15','Sibling (adoptive)',0,'','',1,0,0),(5696369,85,'16','Sibling (biological)',0,'','',1,0,0),(5696378,85,'17','Spouse',0,'','',1,0,0),(5696387,85,'18','Step child',0,'','',1,0,0),(5696396,85,'19','Blank',0,'','',1,0,0),(5696410,86,'1','Visit Payment',0,'','',1,0,0),(5696419,86,'2','Lab Payment',0,'','',1,0,0),(5696428,86,'3','Medications Payment',0,'','',1,0,0),(5696437,86,'4','Correction Payment',0,'','',1,0,0),(5696446,86,'5','Other',0,'','',1,0,0),(5696528,5696523,'1','for SCC, Mobile Med, Proyecto Salud, PCWC, MCHP, Holy Cross, MCC Medical Clinic, PCC - for SCC, Mobile Med, Proyecto Salud, PCWC, MCHP, Holy Cross, MCC Medical Clinic, PCC',1,'','',0,0,0),(5696537,5696523,'2','DPCC001 - Endocrine/Thyroid Disease (240-246)',1,'','',1,0,0),(5696546,5696523,'3','DPCC002 - Endocrine/Diabetes (250)',2,'','',1,0,0),(5696555,5696523,'4','DPCC003 - Blood diseases--(280-289)',3,'','',1,0,0),(5696564,5696523,'5','DPCC004 - Mental Health/Substance Abuse(290-319)',4,'','',1,0,0),(5696573,5696523,'6','DPCC005 - Nervous System Diseases (320-359)',5,'','',1,0,0),(5696582,5696523,'7','DPCC006 - Eye Diseases (360-379)',6,'','',1,0,0),(5696591,5696523,'8','DPCC007 - Ear Disease (380-389)',7,'','',1,0,0),(5696600,5696523,'9','DPCC008 - Circulatory System Diseases (390-459)',8,'','',1,0,0),(5696609,5696523,'10','DPCC009 - Hypertension (401)',9,'','',1,0,0),(5696618,5696523,'11','DPCC010 - Cerebrovascular Diseases (430-438)',10,'','',1,0,0),(5696627,5696523,'12','DPCC011 - Respiratory Diseases  (460-519)',11,'','',1,0,0),(5696636,5696523,'13','DPCC012 - Digestive Diseases (520-579)',12,'','',1,0,0),(5696645,5696523,'14','DPCC013 - Urinary System Diseases (580-608)',13,'','',1,0,0),(5696654,5696523,'15','DPCC014 - Breast Diseases (610-611)',14,'','',1,0,0),(5696663,5696523,'16','DPCC015 - Gynecological Disorders (614-627)',15,'','',1,0,0),(5696672,5696523,'17','DPCC016 - Skin Disease (680-709)',16,'','',1,0,0),(5696681,5696523,'18','DPCC017 - Musculoskeletal/Connective Tissue (710-739)',17,'','',1,0,0),(5696690,5696523,'19','DPCC018 - Signs & Symptoms/Ill-defined (780-799)',18,'','',1,0,0),(5696699,5696523,'20','DPCC019 - Injuries & Poisoning (800-999)',19,'','',1,0,0),(5696708,5696523,'21','DPCC020 - Circulatory System Diseases (390-459)',20,'','',1,0,0),(5696717,5696523,'22','MH0.01 - Allergy',21,'','',0,0,0),(5696726,5696523,'23','MH0.02 - Hyperlipedemia',22,'','',0,0,0),(5696735,5696523,'24','MH0.03 - Hypertension ',23,'','',0,0,0),(5696744,5696523,'25','MH0.04 - Heart Failure',24,'','',0,0,0),(5696753,5696523,'26','MH0.05 - Other Cardiovascular ',25,'','',0,0,0),(5696762,5696523,'27','MH0.06 - Dermatologic',26,'','',0,0,0),(5696771,5696523,'28','MH0.07 - Diabetes',27,'','',0,0,0),(5696780,5696523,'29','MH0.08 - Diabetes-retinopathy',28,'','',0,0,0),(5696789,5696523,'30','MH0.09 - Diabetes-nephropathy',29,'','',0,0,0),(5696798,5696523,'31','MH0.10 - Diabetes-neuropathy',30,'','',0,0,0),(5696807,5696523,'32','MH0.11 - Diabetes-vascular Disease',31,'','',0,0,0),(5696816,5696523,'33','MH0.12 - Education',32,'','',0,0,0),(5696825,5696523,'34','MH0.13 - Education-diabetes',33,'','',0,0,0),(5696834,5696523,'35','MH0.14 - Education-diet',34,'','',0,0,0),(5696843,5696523,'36','MH0.15 - Education-other',35,'','',0,0,0),(5696852,5696523,'37','MH0.16 - Endocrine',36,'','',0,0,0),(5696861,5696523,'38','MH0.17 - ENT',37,'','',0,0,0),(5696870,5696523,'39','MH0.18 - Eye',38,'','',0,0,0),(5696879,5696523,'40','MH0.19 - Gastrointestinal',39,'','',0,0,0),(5696888,5696523,'41','MH0.20 - Gyn',40,'','',0,0,0),(5696897,5696523,'42','MH0.21 - Hematologic',41,'','',0,0,0),(5696906,5696523,'43','MH0.22 - Mental Health',42,'','',0,0,0),(5696915,5696523,'44','MH0.23 - Neurologic',43,'','',0,0,0),(5696924,5696523,'45','MH0.24 - Oncology',44,'','',0,0,0),(5696933,5696523,'46','MH0.25 - Orthopedic',45,'','',0,0,0),(5696942,5696523,'47','MH0.26 - Physical Exam/forms',46,'','',0,0,0),(5696951,5696523,'48','MH0.27 - Podiatry',47,'','',0,0,0),(5696960,5696523,'49','MH0.28 - Respiratory',48,'','',0,0,0),(5696969,5696523,'50','MH0.29 - Rheumatology',49,'','',0,0,0),(5696978,5696523,'51','MH0.30 - Urologic',50,'','',0,0,0),(5696987,5696523,'52','MH0.31 - Other',51,'','',0,0,0),(5696996,5696523,'53','MH0.32 - Unknown',52,'','',0,0,0),(5697005,5696523,'54','MH0.33 - Obesity',53,'','',0,0,0),(5697014,5696523,'55','MH0.34 - Metabolic Syndrome',54,'','',0,0,0),(5697023,5696523,'56','MH1.01 - Phmercy',55,'','',0,0,0),(5697032,5696523,'57','MH1.02 - Phmedbank',56,'','',0,0,0),(5697041,5696523,'58','MH1.03 - Phselfpay',57,'','',0,0,0),(5697050,5696523,'59','MH1.04 - Phmdcard',58,'','',0,0,0),(5697059,5696523,'60','MH1.05 - Phother',59,'','',0,0,0),(5697068,5696523,'61','AFC.100 - Cardiovascular - Hypertension',60,'','',0,0,0),(5697077,5696523,'62','AFC.101 - Cardiovascular - Other',61,'','',0,0,0),(5697086,5696523,'63','AFC.102 - Dermatologic',62,'','',0,0,0),(5697095,5696523,'64','AFC.103 - Education_ Group - Diabetes',63,'','',0,0,0),(5697104,5696523,'65','AFC.104 - Education_ Group - Diet',64,'','',0,0,0),(5697113,5696523,'66','AFC.105 - Education_ Group - Exercise',65,'','',0,0,0),(5697122,5696523,'67','AFC.106 - Education_ Group - Hl',66,'','',0,0,0),(5697131,5696523,'68','AFC.107 - Education_ Group - Other  ',67,'','',0,0,0),(5697140,5696523,'69','AFC.108 - Education_ Group - Self Breast Exam',68,'','',0,0,0),(5697149,5696523,'70','AFC.109 - Education_ Indiv - Asthma ',69,'','',0,0,0),(5697158,5696523,'71','AFC.110 - Education_ Indiv - Diabetes',70,'','',0,0,0),(5697167,5696523,'72','AFC.111 - Education_ Indiv - Diet',71,'','',0,0,0),(5697176,5696523,'73','AFC.112 - Education_ Indiv - Hl',72,'','',0,0,0),(5697185,5696523,'74','AFC.113 - Education_ Indiv - Medication',73,'','',0,0,0),(5697194,5696523,'75','AFC.114 - Education_ Indiv - Other',74,'','',0,0,0),(5697203,5696523,'76','AFC.115 - Education_ Indiv - Self Breast Exam',75,'','',0,0,0),(5697212,5696523,'77','AFC.116 - Endocrine - Diabetes',76,'','',0,0,0),(5697221,5696523,'78','AFC.117 - Endocrine - Other',77,'','',0,0,0),(5697230,5696523,'79','AFC.118 - ENT',78,'','',0,0,0),(5697239,5696523,'80','AFC.119 - Eye',79,'','',0,0,0),(5697248,5696523,'81','AFC.120 - Gastrointestinal',80,'','',0,0,0),(5697257,5696523,'82','AFC.121 - Gyn',81,'','',0,0,0),(5697266,5696523,'83','AFC.122 - Hematologic',82,'','',0,0,0),(5697275,5696523,'84','AFC.123 - Mental Health',83,'','',0,0,0),(5697284,5696523,'85','AFC.124 - Musculoskeletal',84,'','',0,0,0),(5697293,5696523,'86','AFC.125 - Nephrology',85,'','',0,0,0),(5697302,5696523,'87','AFC.126 - Neurologic',86,'','',0,0,0),(5697311,5696523,'88','AFC.127 - Oncology',87,'','',0,0,0),(5697320,5696523,'89','AFC.128 - Physical Therapy - Assessment',88,'','',0,0,0),(5697329,5696523,'90','AFC.129 - Physical Therapy - Follow-up',89,'','',0,0,0),(5697338,5696523,'91','AFC.130 - Podiatry',90,'','',0,0,0),(5697347,5696523,'92','AFC.131 - Respiratory - Asthma',91,'','',0,0,0),(5697356,5696523,'93','AFC.132 - Respiratory - Other',92,'','',0,0,0),(5697365,5696523,'94','AFC.133 - Urologic',93,'','',0,0,0),(5697379,5697374,'1','for SCC, Mobile Med, Proyecto Salud, PCWC, MCHP, Holy Cross, MCC Medical Clinic, PCC',0,'','',1,0,0),(5697388,5697374,'2','Endocrine/Thyroid Disease (240-246)',0,'','',1,0,0),(5697397,5697374,'3','Endocrine/Diabetes (250)',0,'','',1,0,0),(5697406,5697374,'4','Blood diseases--(280-289)',0,'','',1,0,0),(5697415,5697374,'5','Mental Health/Substance Abuse(290-319)',0,'','',1,0,0),(5697424,5697374,'6','Nervous System Diseases (320-359)',0,'','',1,0,0),(5697433,5697374,'7','Eye Diseases (360-379)',0,'','',1,0,0),(5697442,5697374,'8','Ear Disease (380-389)',0,'','',1,0,0),(5697451,5697374,'9','Circulatory System Diseases (390-459)',0,'','',1,0,0),(5697460,5697374,'10','Hypertension (401)',0,'','',1,0,0),(5697469,5697374,'11','Cerebrovascular Diseases (430-438)',0,'','',1,0,0),(5697478,5697374,'12','Respiratory Diseases  (460-519)',0,'','',1,0,0),(5697487,5697374,'13','Digestive Diseases (520-579)',0,'','',1,0,0),(5697496,5697374,'14','Urinary System Diseases (580-608)',0,'','',1,0,0),(5697505,5697374,'15','Breast Diseases (610-611)',0,'','',1,0,0),(5697514,5697374,'16','Gynecological Disorders (614-627)',0,'','',1,0,0),(5697523,5697374,'17','Skin Disease (680-709)',0,'','',1,0,0),(5697532,5697374,'18','Musculoskeletal/Connective Tissue (710-739)',0,'','',1,0,0),(5697541,5697374,'19','Signs & Symptoms/Ill-defined (780-799)',0,'','',1,0,0),(5697550,5697374,'20','Injuries & Poisoning (800-999)',0,'','',1,0,0),(5697559,5697374,'21','Circulatory System Diseases (390-459)',0,'','',1,0,0),(5697568,5697374,'22','Allergy',0,'','',1,0,0),(5697577,5697374,'23','Hyperlipedemia',0,'','',1,0,0),(5697586,5697374,'24','HypertensionÂ ',0,'','',1,0,0),(5697595,5697374,'25','Heart Failure',0,'','',1,0,0),(5697604,5697374,'26','Other CardiovascularÂ ',0,'','',1,0,0),(5697613,5697374,'27','Dermatologic',0,'','',1,0,0),(5697622,5697374,'28','Diabetes',0,'','',1,0,0),(5697631,5697374,'29','Diabetes-retinopathy',0,'','',1,0,0),(5697640,5697374,'30','Diabetes-nephropathy',0,'','',1,0,0),(5697649,5697374,'31','Diabetes-neuropathy',0,'','',1,0,0),(5697658,5697374,'32','Diabetes-vascular Disease',0,'','',1,0,0),(5697667,5697374,'33','Education',0,'','',1,0,0),(5697676,5697374,'34','Education-diabetes',0,'','',1,0,0),(5697685,5697374,'35','Education-diet',0,'','',1,0,0),(5697694,5697374,'36','Education-other',0,'','',1,0,0),(5697703,5697374,'37','Endocrine',0,'','',1,0,0),(5697712,5697374,'38','ENT',0,'','',1,0,0),(5697721,5697374,'39','Eye',0,'','',1,0,0),(5697730,5697374,'40','Gastrointestinal',0,'','',1,0,0),(5697739,5697374,'41','Gyn',0,'','',1,0,0),(5697748,5697374,'42','Hematologic',0,'','',1,0,0),(5697757,5697374,'43','Mental Health',0,'','',1,0,0),(5697766,5697374,'44','Neurologic',0,'','',1,0,0),(5697775,5697374,'45','Oncology',0,'','',1,0,0),(5697784,5697374,'46','Orthopedic',0,'','',1,0,0),(5697793,5697374,'47','Physical Exam/forms',0,'','',1,0,0),(5697802,5697374,'48','Podiatry',0,'','',1,0,0),(5697811,5697374,'49','Respiratory',0,'','',1,0,0),(5697820,5697374,'50','Rheumatology',0,'','',1,0,0),(5697829,5697374,'51','Urologic',0,'','',1,0,0),(5697838,5697374,'52','Other',0,'','',1,0,0),(5697847,5697374,'53','Unknown',0,'','',1,0,0),(5697856,5697374,'54','Obesity',0,'','',1,0,0),(5697865,5697374,'55','Metabolic Syndrome',0,'','',1,0,0),(5697874,5697374,'56','Phmercy',0,'','',1,0,0),(5697883,5697374,'57','Phmedbank',0,'','',1,0,0),(5697892,5697374,'58','Phselfpay',0,'','',1,0,0),(5697901,5697374,'59','Phmdcard',0,'','',1,0,0),(5697910,5697374,'60','Phother',0,'','',1,0,0),(5697919,5697374,'61','Cardiovascular - Hypertension',0,'','',1,0,0),(5697928,5697374,'62','Cardiovascular - Other',0,'','',1,0,0),(5697937,5697374,'63','Dermatologic',0,'','',1,0,0),(5697946,5697374,'64','Education_ Group - Diabetes',0,'','',1,0,0),(5697955,5697374,'65','Education_ Group - Diet',0,'','',1,0,0),(5697964,5697374,'66','Education_ Group - Exercise',0,'','',1,0,0),(5697973,5697374,'67','Education_ Group - Hl',0,'','',1,0,0),(5697982,5697374,'68','Education_ Group - OtherÂ Â ',0,'','',1,0,0),(5697991,5697374,'69','Education_ Group - Self Breast Exam',0,'','',1,0,0),(5698000,5697374,'70','AFC062',0,'','',1,0,0),(5698009,5697374,'71','AFC063',0,'','',1,0,0),(5698018,5697374,'72','AFC064',0,'','',1,0,0),(5698027,5697374,'73','AFC065',0,'','',1,0,0),(5698036,5697374,'74','Education_ Indiv - Medication',0,'','',1,0,0),(5698045,5697374,'75','Education_ Indiv - Other',0,'','',1,0,0),(5698054,5697374,'76','Education_ Indiv - Self Breast Exam',0,'','',1,0,0),(5698063,5697374,'77','Endocrine - Diabetes',0,'','',1,0,0),(5698072,5697374,'78','Endocrine - Other',0,'','',1,0,0),(5698081,5697374,'79','ENT',0,'','',1,0,0),(5698090,5697374,'80','Eye',0,'','',1,0,0),(5698099,5697374,'81','Gastrointestinal',0,'','',1,0,0),(5698108,5697374,'82','Gyn',0,'','',1,0,0),(5698117,5697374,'83','Hematologic',0,'','',1,0,0),(5698126,5697374,'84','Mental Health',0,'','',1,0,0),(5698135,5697374,'85','Musculoskeletal',0,'','',1,0,0),(5698144,5697374,'86','Nephrology',0,'','',1,0,0),(5698153,5697374,'87','Neurologic',0,'','',1,0,0),(5698162,5697374,'88','Oncology',0,'','',1,0,0),(5698171,5697374,'89','Physical Therapy - Assessment',0,'','',1,0,0),(5698180,5697374,'90','Physical Therapy - Follow-up',0,'','',1,0,0),(5698189,5697374,'91','Podiatry',0,'','',1,0,0),(5698198,5697374,'92','Respiratory - Asthma',0,'','',1,0,0),(5698207,5697374,'93','Respiratory - Other',0,'','',1,0,0),(5698216,5697374,'94','Urologic',0,'','',1,0,0),(5698230,5698225,'1','for SCC, Mobile Med, Proyecto Salud, PCWC, MCHP, Holy Cross, MCC Medical Clinic, Mercy, PCC - for SCC, Mobile Med, Proyecto Salud, PCWC, MCHP, Holy Cross, MCC Medical Clinic, Mercy, PCC',2,'','',0,0,0),(5698239,5698225,'2','PPCC001 - DSME Completed',1,'','',1,0,0),(5698248,5698225,'3','PPCC002 - DM Class 1',1,'','',1,0,0),(5698257,5698225,'4','PPCC003 - DM Class 2',3,'','',1,0,0),(5698266,5698225,'5','PPCC004 - DM Class 3',4,'','',1,0,0),(5698275,5698225,'6','PPCC005 - DM Class 4',5,'','',1,0,0),(5698284,5698225,'7','PPCC006 - Nutrition Education',6,'','',1,0,0),(5698293,5698225,'8','PPCC007 - Clinical Breast Exam',7,'','',1,0,0),(5698302,5698225,'9','PPCC008 - Diabetic Foot Check (LEAP)',8,'','',1,0,0),(5698311,5698225,'10','PPCC009 - Retinal Eye Exam',9,'','',0,0,0),(5698320,5698225,'11','PPCC010 - General Foot Exam',10,'','',1,0,0),(5698329,5698225,'12','AFC001 - EKG',11,'','',0,0,0),(5698338,5698225,'13','AFC002 - Echo',12,'','',0,0,0),(5698347,5698225,'14','AFC003 - Breast Biopsy',13,'','',0,0,0),(5698356,5698225,'15','AFC004 - Colposcopy',14,'','',0,0,0),(5698365,5698225,'16','AFC005 - Mammogram - Unspecified',15,'','',0,0,0),(5698374,5698225,'17','AFC006 - Mammogram - Initial',16,'','',0,0,0),(5698383,5698225,'18','AFC007 - Mammogram - Annual',17,'','',0,0,0),(5698392,5698225,'19','AFC008 - Mammogram - Followup',18,'','',0,0,0),(5698401,5698225,'20','AFC009 - Mammogram - Screening',19,'','',0,0,0),(5698410,5698225,'21','AFC010 - Mammogram - Daignosis',20,'','',0,0,0),(5698419,5698225,'22','AFC011 - Mammogram - Unilateral',21,'','',0,0,0),(5698428,5698225,'23','AFC012 - Mammogram - Bilateral',22,'','',0,0,0),(5698437,5698225,'24','AFC013 - Clinical Breast Ex-init/normal',23,'','',0,0,0),(5698446,5698225,'25','AFC014 - Clinical Breast Ex-init/abnormal',24,'','',0,0,0),(5698455,5698225,'26','AFC015 - Clinical Breast Ex-init/unknown',25,'','',0,0,0),(5698464,5698225,'27','AFC016 - Clinical Breast Ex-annual/normal',26,'','',0,0,0),(5698473,5698225,'28','AFC017 - Clinical Breast Ex-annual/abnorm',27,'','',0,0,0),(5698482,5698225,'29','AFC018 - Clinical Breast Ex-annual/unknow',28,'','',0,0,0),(5698491,5698225,'30','AFC019 - Clinical Breast Ex-followup/norm',29,'','',0,0,0),(5698500,5698225,'31','AFC020 - Clinical Breast Ex-followup/ab',30,'','',0,0,0),(5698509,5698225,'32','AFC021 - Clinical Breast Ex-followup/unk',31,'','',0,0,0),(5698518,5698225,'33','AFC022 - Pelvic Exam',32,'','',0,0,0),(5698527,5698225,'34','AFC023 - Colonoscopy',33,'','',0,0,0),(5698536,5698225,'35','AFC024 - Eye Exam',34,'','',0,0,0),(5698545,5698225,'36','AFC025 - Foot Exam',35,'','',0,0,0),(5698554,5698225,'37','AFC026 - Blood Pressure Check',36,'','',0,0,0),(5698563,5698225,'38','AFC027 - Ear Irrigation',37,'','',0,0,0),(5698572,5698225,'39','AFC028 - Fingerstick Glucose',38,'','',0,0,0),(5698581,5698225,'40','AFC029 - Injection, B12',39,'','',0,0,0),(5698590,5698225,'41','AFC030 - IInjection, Flu Vaccine',40,'','',0,0,0),(5698599,5698225,'42','AFC031 - Injection, Medication',41,'','',0,0,0),(5698608,5698225,'43','AFC032 - Nebulizer Treatment',42,'','',0,0,0),(5698617,5698225,'44','AFC033 - Nurse Consult',43,'','',0,0,0),(5698626,5698225,'45','AFC034 - Wound Care',44,'','',0,0,0),(5698635,5698225,'46','AFC035 - Breast Cyst Aspiration',45,'','',0,0,0),(5698644,5698225,'47','AFC036 - Endometrial Biopsy Obtained',46,'','',0,0,0),(5698653,5698225,'48','AFC037 - Pr--pap Smear',47,'','',0,0,0),(5698662,5698225,'49','AFC038 - Vaginal Biopsy Obtained',48,'','',0,0,0),(5698671,5698225,'50','AFC039 - Bronchoscopy',49,'','',0,0,0),(5698680,5698225,'51','AFC040 - Cervical Biopsy',50,'','',0,0,0),(5698689,5698225,'52','AFC041 - Chemo Therapy',51,'','',0,0,0),(5698698,5698225,'53','AFC042 - Ct Scan',52,'','',0,0,0),(5698707,5698225,'54','AFC043 - Endoscopy',53,'','',0,0,0),(5698716,5698225,'55','AFC044 - Fine Needle Aspiration',54,'','',0,0,0),(5698725,5698225,'56','AFC045 - Flex Sig',55,'','',0,0,0),(5698734,5698225,'57','AFC046 - Hearing Aid',56,'','',0,0,0),(5698743,5698225,'58','AFC047 - Hysterectomy',57,'','',0,0,0),(5698752,5698225,'59','AFC048 - Lithortripsy',58,'','',0,0,0),(5698761,5698225,'60','AFC049 - Mri',59,'','',0,0,0),(5698770,5698225,'61','AFC050 - Radiation Therapy',60,'','',0,0,0),(5698779,5698225,'62','AFC051 - Special Consult',61,'','',0,0,0),(5698788,5698225,'63','AFC052 - Surgery',62,'','',0,0,0),(5698797,5698225,'64','AFC053 - Case Manager Consult',63,'','',0,0,0),(5698806,5698225,'65','AFC054 - Pharmacy Consult',64,'','',0,0,0),(5698815,5698225,'66','AFC055 - Lab/radiology Referral',65,'','',0,0,0),(5698824,5698225,'67','AFC056 - Counseling-individual',66,'','',0,0,0),(5698833,5698225,'68','AFC057 - Counseling-group',67,'','',0,0,0),(5698842,5698225,'69','AFC058 - Medication Management',68,'','',0,0,0),(5698851,5698225,'70','AFC059 - Breast Consult',69,'','',0,0,0),(5698860,5698225,'71','AFC060 - Bone Scan',70,'','',0,0,0),(5698869,5698225,'72','AFC061 - Stress Test',71,'','',0,0,0),(5698878,5698225,'73','AFC062 - Holter Monitor',72,'','',0,0,0),(5698887,5698225,'74','AFC063 - Ultrasound',73,'','',0,0,0),(5698896,5698225,'75','AFC064 - Gyn Consult',74,'','',0,0,0),(5698905,5698225,'76','AFC065 - Skin Biopsy',75,'','',0,0,0),(5698914,5698225,'77','AFC066 - Diabetes Education Mod 1',76,'','',0,0,0),(5698923,5698225,'78','AFC067 - Diabetes Education Mod 2',77,'','',0,0,0),(5698932,5698225,'79','AFC068 - Diabetes Education Mod 3',78,'','',0,0,0),(5698941,5698225,'80','AFC069 - Diabetes Education Mod 4',79,'','',0,0,0),(5698950,5698225,'81','AFC070 - Diabetes Education Mod 5',80,'','',0,0,0),(5698959,5698225,'82','AFC071 - Diabetes Education Mod 6',81,'','',0,0,0),(5698968,5698225,'83','AFC072 - Diabetes Education Mod 7',82,'','',0,0,0),(5698977,5698225,'84','AFC073 - Diabetes Education Mod 8',83,'','',0,0,0),(5698986,5698225,'85','AFC074 - Diabetes Education Mod 9',84,'','',0,0,0),(5698995,5698225,'86','AFC075 - Diabetes Glucometer Instr',85,'','',0,0,0),(5699004,5698225,'87','AFC076 - Diet Education',86,'','',0,0,0),(5699013,5698225,'88','AFC077 - Dental Information',87,'','',0,0,0),(5699027,5699022,'1','for SCC, Mobile Med, Proyecto Salud, PCWC, MCHP, Holy Cross, MCC Medical Clinic, Mercy, PCC',1,'','',1,0,0),(5699036,5699022,'2','DSME Final Course',1,'','',1,0,0),(5699045,5699022,'3','DM Class 1',2,'','',1,0,0),(5699054,5699022,'4','DM Class 2',3,'','',1,0,0),(5699063,5699022,'5','DM Class 3',4,'','',1,0,0),(5699072,5699022,'6','DM Class 4',5,'','',1,0,0),(5699081,5699022,'7','Nutrition Education',6,'','',1,0,0),(5699090,5699022,'8','Clinical Breast Exam',7,'','',1,0,0),(5699099,5699022,'9','Diabetic Foot Check (LEAP)',8,'','',1,0,0),(5699108,5699022,'10','Retinal Eye Exam',9,'','',0,0,0),(5699117,5699022,'11','EKG',11,'','',1,0,0),(5699126,5699022,'12','Echo',12,'','',1,0,0),(5699135,5699022,'13','Breast Biopsy',13,'','',1,0,0),(5699144,5699022,'14','Colposcopy',14,'','',1,0,0),(5699153,5699022,'15','Mammogram - Unspecified',15,'','',1,0,0),(5699162,5699022,'16','Mammogram - Initial',16,'','',1,0,0),(5699171,5699022,'17','Mammogram - Annual',17,'','',1,0,0),(5699180,5699022,'18','Mammogram - Followup',18,'','',1,0,0),(5699189,5699022,'19','Mammogram - Screening',19,'','',1,0,0),(5699198,5699022,'20','Mammogram - Daignosis',20,'','',1,0,0),(5699207,5699022,'21','Mammogram - Unilateral',21,'','',1,0,0),(5699216,5699022,'22','Mammogram - Bilateral',22,'','',1,0,0),(5699225,5699022,'23','Clinical Breast Ex-init/normal',23,'','',1,0,0),(5699234,5699022,'24','Clinical Breast Ex-init/abnormal',24,'','',1,0,0),(5699243,5699022,'25','Clinical Breast Ex-init/unknown',25,'','',1,0,0),(5699252,5699022,'26','Clinical Breast Ex-annual/normal',26,'','',1,0,0),(5699261,5699022,'27','Clinical Breast Ex-annual/abnorm',27,'','',1,0,0),(5699270,5699022,'28','Clinical Breast Ex-annual/unknow',28,'','',1,0,0),(5699279,5699022,'29','Clinical Breast Ex-followup/norm',29,'','',1,0,0),(5699288,5699022,'30','Clinical Breast Ex-followup/ab',30,'','',1,0,0),(5699297,5699022,'31','Clinical Breast Ex-followup/unk',31,'','',1,0,0),(5699306,5699022,'32','Pelvic Exam',32,'','',1,0,0),(5699315,5699022,'33','Colonoscopy',33,'','',1,0,0),(5699324,5699022,'34','Eye Exam',34,'','',1,0,0),(5699333,5699022,'35','Foot Exam',35,'','',1,0,0),(5699342,5699022,'36','Blood Pressure Check',36,'','',1,0,0),(5699351,5699022,'37','Ear Irrigation',37,'','',1,0,0),(5699360,5699022,'38','Fingerstick Glucose',38,'','',1,0,0),(5699369,5699022,'39','Injection, B12',39,'','',1,0,0),(5699378,5699022,'40','Injection, Flu Vaccine',40,'','',1,0,0),(5699387,5699022,'41','Injection, Medication',41,'','',1,0,0),(5699396,5699022,'42','Nebulizer Treatment',42,'','',1,0,0),(5699405,5699022,'43','Nurse Consult',43,'','',1,0,0),(5699414,5699022,'44','Wound Care',44,'','',1,0,0),(5699423,5699022,'45','Breast Cyst Aspiration',45,'','',1,0,0),(5699432,5699022,'46','Endometrial Biopsy Obtained',46,'','',1,0,0),(5699441,5699022,'47','Pr--pap Smear',47,'','',1,0,0),(5699450,5699022,'48','Vaginal Biopsy Obtained',48,'','',1,0,0),(5699459,5699022,'49','Bronchoscopy',49,'','',1,0,0),(5699468,5699022,'50','Cervical Biopsy',50,'','',1,0,0),(5699477,5699022,'51','Chemo Therapy',51,'','',1,0,0),(5699486,5699022,'52','Ct Scan',52,'','',1,0,0),(5699495,5699022,'53','Endoscopy',53,'','',1,0,0),(5699504,5699022,'54','Fine Needle Aspiration',54,'','',1,0,0),(5699513,5699022,'55','Flex Sig',55,'','',1,0,0),(5699522,5699022,'56','Hearing Aid',56,'','',1,0,0),(5699531,5699022,'57','Hysterectomy',57,'','',1,0,0),(5699540,5699022,'58','Lithortripsy',58,'','',1,0,0),(5699549,5699022,'59','Mri',59,'','',1,0,0),(5699558,5699022,'60','Radiation Therapy',60,'','',1,0,0),(5699567,5699022,'61','Special Consult',61,'','',1,0,0),(5699576,5699022,'62','Surgery',62,'','',1,0,0),(5699585,5699022,'63','Case Manager Consult',63,'','',1,0,0),(5699594,5699022,'64','Pharmacy Consult',64,'','',1,0,0),(5699603,5699022,'65','Lab/radiology Referral',65,'','',1,0,0),(5699612,5699022,'66','Counseling-individual',66,'','',1,0,0),(5699621,5699022,'67','Counseling-group',67,'','',1,0,0),(5699630,5699022,'68','Medication Management',68,'','',1,0,0),(5699639,5699022,'69','Breast Consult',69,'','',1,0,0),(5699648,5699022,'70','Bone Scan',70,'','',1,0,0),(5699657,5699022,'71','Stress Test',71,'','',1,0,0),(5699666,5699022,'72','Diabetes Education Mod 1',76,'','',1,0,0),(5699675,5699022,'73','Diabetes Education Mod 2',77,'','',1,0,0),(5699684,5699022,'74','Diabetes Education Mod 3',78,'','',1,0,0),(5699693,5699022,'75','Diabetes Education Mod 4',79,'','',1,0,0),(5699702,5699022,'76','Diabetes Education Mod 5',80,'','',1,0,0),(5699711,5699022,'77','Diabetes Education Mod 6',81,'','',1,0,0),(5699720,5699022,'78','Diabetes Education Mod 7',82,'','',1,0,0),(5699729,5699022,'79','Diabetes Education Mod 8',83,'','',1,0,0),(5699738,5699022,'80','Diabetes Education Mod 9',84,'','',1,0,0),(5699747,5699022,'81','Diabetes Glucometer Instr',85,'','',1,0,0),(5699756,5699022,'82','Diet Education',86,'','',1,0,0),(5699765,5699022,'83','Dental Information',87,'','',1,0,0),(5699779,87,'1','Pap Smear (88141)',1,'','',1,0,0),(5699788,87,'2','Hemoglobin A1c (83037)',1,'','',1,0,0),(5699797,87,'3','LDL cholestrol (83721)',2,'','',1,0,0),(5699806,87,'4','PSA',3,'','',1,0,0),(5699815,87,'5','Mammogram (76092)',4,'','',1,0,0),(5699824,87,'6','Glucose fingerstick(82948)',5,'','',1,0,0),(5699833,87,'7','Hemoccult/FOBT (82270)',6,'','',1,0,0),(5699842,87,'8','Urine Dipstick (81002)',7,'','',1,0,0),(5699851,87,'9','EKG w/o interpretation (93005)',8,'','',1,0,0),(5699860,87,'10','ALT (84460)',9,'','',1,0,0),(5699869,87,'11','AST (84450)',10,'','',1,0,0),(5699878,87,'12','CBC w/diff',11,'','',1,0,0),(5699887,87,'13','Cholesterol (82465)',12,'','',1,0,0),(5699896,87,'14','Colonoscopy (44388)',13,'','',1,0,0),(5699905,87,'15','Creatinine (82565)',14,'','',1,0,0),(5699914,87,'16','Double Contrast Barrium Enema (74280)',15,'','',1,0,0),(5699923,87,'17','Flexible Sigmoidoscopy (45330)',16,'','',1,0,0),(5699932,87,'18','GC DNA probe',17,'','',1,0,0),(5699941,87,'19','HDL Cholestrol (83718)',18,'','',1,0,0),(5699950,87,'20','KOH',19,'','',1,0,0),(5699959,87,'21','Microal/Creat Ratio (82043/82570)',20,'','',1,0,0),(5699968,87,'22','MicroAlbumin Urine (82043)',21,'','',1,0,0),(5699977,87,'23','Pap Smear (88141)',22,'','',1,0,0),(5699986,87,'24','Potassium, serum (84132)',23,'','',1,0,0),(5699995,87,'25','PPD (86580)',24,'','',1,0,0),(5700004,87,'26','Pregnancy Urine (81025)',25,'','',1,0,0),(5700013,87,'27','PT/INR',26,'','',1,0,0),(5700022,87,'28','PTT (85730)',27,'','',1,0,0),(5700031,87,'29','Strep Throat Culture',28,'','',1,0,0),(5700040,87,'30','Strep, Rapid (36403)',29,'','',1,0,0),(5700049,87,'31','Triglyceride(84478)',30,'','',1,0,0),(5700058,87,'32','UA (81003)',31,'','',1,0,0),(5700067,87,'33','Urine Culture (87086)',32,'','',1,0,0),(5700076,87,'34','24HrUrineProtein (84156)',33,'','',1,0,0),(5700090,88,'1','Patient Reported',2,'','',1,0,0),(5700099,88,'2','Other',3,'','',1,0,0),(5700108,88,'3','Blank',4,'','',1,0,0),(5700122,89,'1','Blood Panel',0,'','',1,0,0),(5700131,89,'2','Lipid Panel',2,'','',1,0,0),(5700140,89,'3','Liver Panel',3,'','',1,0,0),(5700149,89,'4','Other',4,'','',1,0,0),(5700163,5700158,'1','Archived',0,'','',1,0,0),(5700172,5700158,'2','Blank',0,'','',1,0,0),(5700186,5700181,'','',0,'','',1,0,0),(5700194,90,'1','Colorectal Screening: Colonoscopy',1,'','',1,0,0),(5700203,90,'2','Colorectal Screening: Double Contrast Barium Enema',2,'','',1,0,0),(5700212,90,'3','Colorectal Screening: Flex Sig',3,'','',1,0,0),(5700221,90,'4','Dental Exam',4,'','',1,0,0),(5700230,90,'5','Diabetes Education',5,'','',1,0,0),(5700239,90,'6','Diabetic Foot Check (LEAP)',6,'','',1,0,0),(5700248,90,'7','EKG',7,'','',1,0,0),(5700257,90,'8','Immunizations',8,'','',1,0,0),(5700266,90,'9','Mammography:  Screening/Diagnostic',9,'','',1,0,0),(5700275,90,'10','Pap Smear',10,'','',1,0,0),(5700284,90,'11','Retinal Exam',11,'','',1,0,0),(5700293,90,'12','Other',12,'','',1,0,0),(5700307,91,'1','for SCC, Mobile Med, Proyecto Salud, PCWC, MCHP, Holy Cross, MCC Medical Clinic, Mercy, PCC',32,'','',0,0,0),(5700316,91,'2','Follow Medical Plan',1,'','',1,0,0),(5700325,91,'3','Check blood sugar',1,'','',1,1,5700316),(5700334,91,'4','Check feet',2,'','',1,1,5700316),(5700343,91,'5','Use inhaler',3,'','',1,1,5700316),(5700352,91,'6','Take medicine as prescribed',4,'','',1,1,5700316),(5700361,91,'7','Keep health care appointments',5,'','',1,1,5700316),(5700370,91,'8','Complete recommended medical referrals',6,'','',1,1,5700316),(5700379,91,'9','Other',7,'','',1,1,5700316),(5700388,91,'10','Complete preventive screening',8,'','',1,0,0),(5700406,91,'12','Mammography',9,'','',1,1,5700388),(5700415,91,'13','Colonoscopy',10,'','',1,1,5700388),(5700424,91,'14','Pap smear',11,'','',1,1,5700388),(5700433,91,'15','Recommended Vaccinations',12,'','',1,1,5700388),(5700442,91,'16','Other',13,'','',1,1,5700388),(5700451,91,'17','Be physically active',14,'','',1,0,0),(5700460,91,'18','General',15,'','',1,1,5700451),(5700469,91,'19','Healthy Eating',16,'','',1,0,0),(5700478,91,'20','General',17,'','',1,1,5700469),(5700487,91,'21','Stop Smoking',18,'','',1,0,0),(5700496,91,'22','Stop date',19,'','',1,1,5700487),(5700505,91,'23','Quitline',20,'','',1,1,5700487),(5700514,91,'24','Refer to classes',21,'','',1,1,5700487),(5700523,91,'25','NRT',22,'','',1,1,5700487),(5700532,91,'26','Other',23,'','',1,1,5700487),(5700541,91,'27','Alcohol Consumption',24,'','',1,0,0),(5700550,91,'28','General',25,'','',1,1,5700541),(5700559,91,'29','Manage Stress',26,'','',1,0,0),(5700568,91,'30','General',27,'','',1,1,5700559),(5700577,91,'31','Education-Community Resources',28,'','',1,0,0),(5700586,91,'32','General',29,'','',1,1,5700577),(5700595,91,'33','Other',30,'','',1,0,0),(5700604,91,'34','General',31,'','',1,1,5700595),(5700618,92,'1','for AFC',19,'','',0,0,0),(5700627,92,'2','Call Back',1,'','',1,0,0),(5700636,92,'3','Check Progress',1,'','',1,0,0),(5700645,92,'4','Fairfax Eye Check',2,'','',0,0,0),(5700654,92,'5','Financial Screening',3,'','',0,0,0),(5700663,92,'6','Lab Check',4,'','',0,0,0),(5700672,92,'7','Radiology Check',5,'','',0,0,0),(5700681,92,'8','Return to Clinic',6,'','',0,0,0),(5700690,92,'9','Specialty Check',7,'','',0,0,0),(5700699,92,'10','for SCC',18,'','',0,0,0),(5700708,92,'11','N/A',8,'','',0,0,0),(5700717,92,'12','Meds Pickup',9,'','',0,0,0),(5700726,92,'13','Check Progress',10,'','',0,0,0),(5700735,92,'14','Call Back',11,'','',0,0,0),(5700744,92,'15','Converted',12,'','',1,0,0),(5700753,92,'16','for Mobile Med, Proyecto Salud, MCC Medical Clinic,Holy Cross, PCWC, Mercy, MKHP, PCC, JSF (Herndon)',20,'','',0,0,0),(5700762,92,'17','Call Back',13,'','',0,0,0),(5700771,92,'18','Check Progress',14,'','',0,0,0),(5700780,92,'19','Repeat Test',15,'','',1,0,0),(5700789,92,'20','Other',16,'','',1,0,0),(5700798,92,'21','N/A',17,'','',1,0,0),(5700836,33,'6','Other',5,'','',1,0,0),(5700843,33,'7','Unknown',6,'','',1,0,0),(5700850,33,'8','Blank',7,'','',0,0,0),(5700865,77,'4','Blank',4,'','',1,0,0),(5701151,93,'','',0,'','',1,0,0),(5701259,94,'1','Provider',0,'','',1,0,0),(5701268,94,'2','Non-Provider',0,'','',1,0,0),(5701277,94,'3','Specialist',0,'','',1,0,0),(5701286,94,'4','Medical Phone',0,'','',1,0,0),(5701295,94,'5','Medication PU',0,'','',1,0,0),(5701304,94,'6','Education',0,'','',1,0,0),(5701313,94,'7','Eligibility',0,'','',1,0,0),(5701431,95,'1','Provider',1,'','',1,0,0),(5701440,95,'2','Non-Provider',1,'','',1,0,0),(5701449,95,'3','Specialist',2,'','',1,0,0),(5701458,95,'4','Medical Phone',3,'','',1,0,0),(5701467,95,'5','Medication PU',4,'','',1,0,0),(5701476,95,'6','Education',5,'','',1,0,0),(5701485,95,'7','Eligibility',6,'','',1,0,0),(6032362,81,'107','Other',107,'','',1,1,5695611),(6772808,24,'4','Accompanied',0,'','',1,0,0),(6772815,24,'5','Divorced',1,'','',1,0,0),(6772824,24,'6','Not Specified',3,'','',1,0,0),(6772836,24,'7','Unknown',7,'','',1,0,0),(6772843,24,'8','Widowed',8,'','',1,0,0),(6772863,21,'3','NPI',3,'','',1,0,0),(6772870,21,'4','UPIN',4,'','',1,0,0),(6772877,21,'5','Old MRN',5,'','',1,0,0),(6772897,29,'5','Parent',5,'','',1,0,0),(7589253,21,'6','DL',6,'','',1,0,0),(12629405,26,'6','Alt',6,'','',1,0,0),(12629451,78,'20','SCC DC Archived',19,'','',1,0,0),(12629458,78,'21','SCC-DC Archived',20,'','',1,0,0),(13273049,8,'7','Employer2',7,'','',1,0,0),(15308313,28,'7','Correction Payment',3,'','',1,0,0),(15308320,28,'8','Labs Payment',1,'','',1,0,0),(15308327,28,'9','Medication Payment',2,'','',1,0,0),(15308334,28,'10','Other',4,'','',1,0,0),(15308341,28,'11','Visit Payment',0,'','',1,0,0),(17838750,89,'5','.',1,'','',0,0,0),(22151941,15,'2','Registrar',2,'','',1,0,0),(27499013,5699022,'84','for AFC',10,'','',1,0,0),(27499142,5699022,'85','Holter Monitor',72,'','',1,0,0),(27499149,5699022,'86','Ultrasound',73,'','',1,0,0),(27499156,5699022,'87','Gyn Consult',74,'','',1,0,0),(27499163,5699022,'88','Skin Biopsy',75,'','',1,0,0),(27499348,40,'3','No Status',1,'','',1,0,0),(27499355,40,'1','Eligible',2,'','',1,0,0),(27499362,40,'2','In-Eligible',3,'','',1,0,0),(27499369,40,'4','Not Required',4,'','',0,0,0),(27499418,6,'1','RQ',1,'','',1,0,0),(27499425,6,'2','Q',2,'','',1,0,0),(27499432,6,'3','I',3,'','',1,0,0),(27511354,90,'13','.',0,'','',1,0,0),(27511744,96,'0','No',1,'','',1,0,0),(27511751,96,'1','Yes',2,'','',1,0,0),(31099297,5698225,'89','D0053 Patient Referred Out',88,'','',0,0,0),(31099304,5698225,'90','D0120 Periodic Exam',89,'','',0,0,0),(31099311,5698225,'91','D0130 Emergency Exam',90,'','',0,0,0),(31099318,5698225,'92','D140 Limited Exam',91,'','',0,0,0),(31099325,5698225,'93','D0150 Comp. Exam',92,'','',0,0,0),(31099332,5698225,'94','D0210 Fmx',93,'','',0,0,0),(31099339,5698225,'95','D0220 Single Pa',94,'','',0,0,0),(31099346,5698225,'96','D0230 Add. Pa',95,'','',0,0,0),(31099353,5698225,'97','D0272 Two Bw\'s',96,'','',0,0,0),(31099360,5698225,'98','D0274 Four Bw\'s',97,'','',0,0,0),(31099367,5698225,'99','D0330 Panx',98,'','',0,0,0),(31099374,5698225,'100','D1110 Adult Prophy',99,'','',0,0,0),(31099381,5698225,'101','D1205 Fluoride Tx',100,'','',0,0,0),(31099388,5698225,'102','D1310 Nutr Couns.',101,'','',0,0,0),(31099395,5698225,'103','D1320 Tobac. Couns.',102,'','',0,0,0),(31099402,5698225,'104','D1330 Ohi',103,'','',0,0,0),(31099409,5698225,'105','D2140 1 Surf. Amal.',104,'','',0,0,0),(31099416,5698225,'106','D2150 2 Surf. Amal.',105,'','',0,0,0),(31099423,5698225,'107','D2160 3 Surf. Amal.',106,'','',0,0,0),(31099430,5698225,'108','D2161 4 Surf. Amal.',107,'','',0,0,0),(31099437,5698225,'109','D2330 1 Surf. Resin Anterior',108,'','',0,0,0),(31099444,5698225,'110','D2331 2 Surf. Resin Anterior',109,'','',0,0,0),(31099451,5698225,'111','D2332 3 Surf. Resin Anterior',110,'','',0,0,0),(31099458,5698225,'112','D2335 4 Surf. Resin Anterior',111,'','',0,0,0),(31099465,5698225,'113','D2949 Sed. Filling',116,'','',0,0,0),(31099472,5698225,'114','D3110 Pulp Cap. Dir.',117,'','',0,0,0),(31099479,5698225,'115','D3120 Pulp Cap. Ind.',118,'','',0,0,0),(31099486,5698225,'116','D3220 Pulpotomy',120,'','',0,0,0),(31099493,5698225,'117','D3251 Pulpectomy',121,'','',0,0,0),(31099500,5698225,'118','D4341 Perio Scale (each Quad)',122,'','',0,0,0),(31099507,5698225,'119','D4342 Perio Scale (1-3 Teeth)',123,'','',0,0,0),(31099514,5698225,'120','D4345 Perio Scaling',124,'','',0,0,0),(31099521,5698225,'121','D4355 Full Mouth Debridement',125,'','',0,0,0),(31099528,5698225,'122','D2385 1 Surf. Resin Post',112,'','',0,0,0),(31099535,5698225,'123','D2386 2 Surf. Resin Post',113,'','',0,0,0),(31099542,5698225,'124','D2387 3 Surf. Resin Post',114,'','',0,0,0),(31099549,5698225,'125','D2388 4 Surf. Resin Post',115,'','',0,0,0),(31099556,5698225,'126','D3126 Temp. Filling',119,'','',0,0,0),(31099563,5698225,'127','D7112 Post-op visit',127,'','',0,0,0),(31099570,5698225,'128','D7110 Extraction Single Tooth',126,'','',0,0,0),(31099577,5698225,'129','D7120 Each Additional Tooth',128,'','',0,0,0),(31099584,5698225,'130','D7210 Surgical Extraction',129,'','',0,0,0),(31099591,5698225,'131','D7250 Resdiual Root Removal',130,'','',0,0,0),(31099598,5698225,'132','D7510 I. and D. -ascess',131,'','',0,0,0),(31099605,5698225,'133','D9110 Paliative, Tx',132,'','',0,0,0),(31099612,5698225,'134','D9630 Rx',133,'','',0,0,0),(31099619,5698225,'135','D9910 Desens.',134,'','',0,0,0),(31099626,5698225,'136','D9951 Occlusal Adjustment',135,'','',0,0,0),(31099633,5698225,'137','D9999 Unspec. Tx',136,'','',0,0,0),(31100131,97,'1','My Practice',1,'','',1,0,0),(31100138,97,'2','My Building Only',2,'','',1,0,0),(31100145,97,'3','Created By Me',3,'','',1,0,0),(31106558,98,'1','Normal',1,'','',1,0,0),(31106565,98,'2','Abnormal',2,'','',1,0,0),(31126536,98,'3','.',0,'','',1,0,0),(31178988,88,'4','Quest',1,'','',1,0,0),(31178995,88,'5','Labcorp',1,'','',1,0,0),(31184710,43,'1','Y',1,'','',1,0,0),(31184717,43,'2','N',0,'','',1,0,0),(31310117,35,'53','MO',25,'','',1,0,0),(31503356,5698225,'1','for SCC, Mobile Med, Proyecto Salud, PCWC, MCHP, Holy Cross, MCC Medical Clinic, Mercy, PCC - for SCC, Mobile Med, Proyecto Salud, PCWC, MCHP, Holy Cross, MCC Medical Clinic, Mercy, PCC',3,'','',0,0,0),(31503359,5698225,'2','PPCC001 - DSME Completed',1,'','',0,0,0),(31503362,5698225,'3','PPCC002 - DM Class 1',2,'','',0,0,0),(31503365,5698225,'4','PPCC003 - DM Class 2',4,'','',0,0,0),(31503368,5698225,'5','PPCC004 - DM Class 3',5,'','',0,0,0),(31503371,5698225,'6','PPCC005 - DM Class 4',6,'','',0,0,0),(31503374,5698225,'7','PPCC006 - Nutrition Education',7,'','',0,0,0),(31503377,5698225,'8','PPCC007 - Clinical Breast Exam',8,'','',0,0,0),(31503380,5698225,'9','PPCC008 - Diabetic Foot Check (LEAP)',9,'','',0,0,0),(31503383,5698225,'10','PPCC009 - Retinal Eye Exam',10,'','',0,0,0),(31503386,5698225,'11','PPCC010 - General Foot Exam',11,'','',0,0,0),(31503389,5698225,'12','AFC001 - EKG',0,'','',1,0,0),(31503392,5698225,'13','AFC002 - Echo',12,'','',1,0,0),(31503395,5698225,'14','AFC003 - Breast Biopsy',13,'','',1,0,0),(31503398,5698225,'15','AFC004 - Colposcopy',14,'','',1,0,0),(31503401,5698225,'16','AFC005 - Mammogram - Unspecified',15,'','',1,0,0),(31503404,5698225,'17','AFC006 - Mammogram - Initial',16,'','',1,0,0),(31503407,5698225,'18','AFC007 - Mammogram - Annual',17,'','',1,0,0),(31503410,5698225,'19','AFC008 - Mammogram - Followup',18,'','',1,0,0),(31503413,5698225,'20','AFC009 - Mammogram - Screening',19,'','',1,0,0),(31503416,5698225,'21','AFC010 - Mammogram - Daignosis',20,'','',1,0,0),(31503419,5698225,'22','AFC011 - Mammogram - Unilateral',21,'','',1,0,0),(31503422,5698225,'23','AFC012 - Mammogram - Bilateral',22,'','',1,0,0),(31503425,5698225,'24','AFC013 - Clinical Breast Ex-init/normal',23,'','',1,0,0),(31503428,5698225,'25','AFC014 - Clinical Breast Ex-init/abnormal',24,'','',1,0,0),(31503431,5698225,'26','AFC015 - Clinical Breast Ex-init/unknown',25,'','',1,0,0),(31503434,5698225,'27','AFC016 - Clinical Breast Ex-annual/normal',26,'','',1,0,0),(31503437,5698225,'28','AFC017 - Clinical Breast Ex-annual/abnorm',27,'','',1,0,0),(31503440,5698225,'29','AFC018 - Clinical Breast Ex-annual/unknow',28,'','',1,0,0),(31503443,5698225,'30','AFC019 - Clinical Breast Ex-followup/norm',29,'','',1,0,0),(31503446,5698225,'31','AFC020 - Clinical Breast Ex-followup/ab',30,'','',1,0,0),(31503449,5698225,'32','AFC021 - Clinical Breast Ex-followup/unk',31,'','',1,0,0),(31503452,5698225,'33','AFC022 - Pelvic Exam',32,'','',1,0,0),(31503455,5698225,'34','AFC023 - Colonoscopy',33,'','',1,0,0),(31503458,5698225,'35','AFC024 - Eye Exam',34,'','',1,0,0),(31503461,5698225,'36','AFC025 - Foot Exam',35,'','',1,0,0),(31503464,5698225,'37','AFC026 - Blood Pressure Check',36,'','',1,0,0),(31503467,5698225,'38','AFC027 - Ear Irrigation',37,'','',1,0,0),(31503470,5698225,'39','AFC028 - Fingerstick Glucose',38,'','',1,0,0),(31503473,5698225,'40','AFC029 - Injection, B12',39,'','',1,0,0),(31503476,5698225,'41','AFC030 - IInjection, Flu Vaccine',40,'','',1,0,0),(31503479,5698225,'42','AFC031 - Injection, Medication',41,'','',1,0,0),(31503482,5698225,'43','AFC032 - Nebulizer Treatment',42,'','',1,0,0),(31503485,5698225,'44','AFC033 - Nurse Consult',43,'','',1,0,0),(31503488,5698225,'45','AFC034 - Wound Care',44,'','',1,0,0),(31503491,5698225,'46','AFC035 - Breast Cyst Aspiration',45,'','',1,0,0),(31503494,5698225,'47','AFC036 - Endometrial Biopsy Obtained',46,'','',1,0,0),(31503497,5698225,'48','AFC037 - Pr--pap Smear',47,'','',1,0,0),(31503500,5698225,'49','AFC038 - Vaginal Biopsy Obtained',48,'','',1,0,0),(31503503,5698225,'50','AFC039 - Bronchoscopy',49,'','',1,0,0),(31503506,5698225,'51','AFC040 - Cervical Biopsy',50,'','',1,0,0),(31503509,5698225,'52','AFC041 - Chemo Therapy',51,'','',1,0,0),(31503512,5698225,'53','AFC042 - Ct Scan',52,'','',1,0,0),(31503515,5698225,'54','AFC043 - Endoscopy',53,'','',1,0,0),(31503518,5698225,'55','AFC044 - Fine Needle Aspiration',54,'','',1,0,0),(31503521,5698225,'56','AFC045 - Flex Sig',55,'','',1,0,0),(31503524,5698225,'57','AFC046 - Hearing Aid',56,'','',1,0,0),(31503527,5698225,'58','AFC047 - Hysterectomy',57,'','',1,0,0),(31503530,5698225,'59','AFC048 - Lithortripsy',58,'','',1,0,0),(31503533,5698225,'60','AFC049 - Mri',59,'','',1,0,0),(31503536,5698225,'61','AFC050 - Radiation Therapy',60,'','',1,0,0),(31503539,5698225,'62','AFC051 - Special Consult',61,'','',1,0,0),(31503542,5698225,'63','AFC052 - Surgery',62,'','',1,0,0),(31503545,5698225,'64','AFC053 - Case Manager Consult',63,'','',1,0,0),(31503548,5698225,'65','AFC054 - Pharmacy Consult',64,'','',1,0,0),(31503551,5698225,'66','AFC055 - Lab/radiology Referral',65,'','',1,0,0),(31503554,5698225,'67','AFC056 - Counseling-individual',66,'','',1,0,0),(31503557,5698225,'68','AFC057 - Counseling-group',67,'','',1,0,0),(31503560,5698225,'69','AFC058 - Medication Management',68,'','',1,0,0),(31503563,5698225,'70','AFC059 - Breast Consult',69,'','',1,0,0),(31503566,5698225,'71','AFC060 - Bone Scan',70,'','',1,0,0),(31503569,5698225,'72','AFC061 - Stress Test',71,'','',1,0,0),(31503572,5698225,'73','AFC062 - Holter Monitor',72,'','',1,0,0),(31503575,5698225,'74','AFC063 - Ultrasound',73,'','',1,0,0),(31503578,5698225,'75','AFC064 - Gyn Consult',74,'','',1,0,0),(31503581,5698225,'76','AFC065 - Skin Biopsy',75,'','',1,0,0),(31503584,5698225,'77','AFC066 - Diabetes Education Mod 1',76,'','',1,0,0),(31503587,5698225,'78','AFC067 - Diabetes Education Mod 2',77,'','',1,0,0),(31503590,5698225,'79','AFC068 - Diabetes Education Mod 3',78,'','',1,0,0),(31503593,5698225,'80','AFC069 - Diabetes Education Mod 4',79,'','',1,0,0),(31503596,5698225,'81','AFC070 - Diabetes Education Mod 5',80,'','',1,0,0),(31503599,5698225,'82','AFC071 - Diabetes Education Mod 6',81,'','',1,0,0),(31503602,5698225,'83','AFC072 - Diabetes Education Mod 7',82,'','',1,0,0),(31503605,5698225,'84','AFC073 - Diabetes Education Mod 8',83,'','',1,0,0),(31503608,5698225,'85','AFC074 - Diabetes Education Mod 9',84,'','',1,0,0),(31503611,5698225,'86','AFC075 - Diabetes Glucometer Instr',85,'','',1,0,0),(31503614,5698225,'87','AFC076 - Diet Education',86,'','',1,0,0),(31503617,5698225,'88','AFC077 - Dental Information',87,'','',1,0,0),(31503620,5698225,'89','D0053 Patient Referred Out',88,'','',0,0,0),(31503623,5698225,'90','D0120 Periodic Exam',89,'','',0,0,0),(31503626,5698225,'91','D0130 Emergency Exam',90,'','',0,0,0),(31503629,5698225,'92','D140 Limited Exam',91,'','',0,0,0),(31503632,5698225,'93','D0150 Comp. Exam',92,'','',0,0,0),(31503635,5698225,'94','D0210 Fmx',93,'','',0,0,0),(31503638,5698225,'95','D0220 Single Pa',94,'','',0,0,0),(31503641,5698225,'96','D0230 Add. Pa',95,'','',0,0,0),(31503644,5698225,'97','D0272 Two Bw\'s',96,'','',0,0,0),(31503647,5698225,'98','D0274 Four Bw\'s',97,'','',0,0,0),(31503650,5698225,'99','D0330 Panx',98,'','',0,0,0),(31503653,5698225,'100','D1110 Adult Prophy',99,'','',0,0,0),(31503656,5698225,'101','D1205 Fluoride Tx',100,'','',0,0,0),(31503659,5698225,'102','D1310 Nutr Couns.',101,'','',0,0,0),(31503662,5698225,'103','D1320 Tobac. Couns.',102,'','',0,0,0),(31503665,5698225,'104','D1330 Ohi',103,'','',0,0,0),(31503668,5698225,'105','D2140 1 Surf. Amal.',104,'','',0,0,0),(31503671,5698225,'106','D2150 2 Surf. Amal.',105,'','',0,0,0),(31503674,5698225,'107','D2160 3 Surf. Amal.',106,'','',0,0,0),(31503677,5698225,'108','D2161 4 Surf. Amal.',107,'','',0,0,0),(31503680,5698225,'109','D2330 1 Surf. Resin Anterior',108,'','',0,0,0),(31503683,5698225,'110','D2331 2 Surf. Resin Anterior',109,'','',0,0,0),(31503686,5698225,'111','D2332 3 Surf. Resin Anterior',110,'','',0,0,0),(31503689,5698225,'112','D2335 4 Surf. Resin Anterior',111,'','',0,0,0),(31503692,5698225,'113','D2949 Sed. Filling',116,'','',0,0,0),(31503695,5698225,'114','D3110 Pulp Cap. Dir.',117,'','',0,0,0),(31503698,5698225,'115','D3120 Pulp Cap. Ind.',118,'','',0,0,0),(31503701,5698225,'116','D3220 Pulpotomy',120,'','',0,0,0),(31503704,5698225,'117','D3251 Pulpectomy',121,'','',0,0,0),(31503707,5698225,'118','D4341 Perio Scale (each Quad)',122,'','',0,0,0),(31503710,5698225,'119','D4342 Perio Scale (1-3 Teeth)',123,'','',0,0,0),(31503713,5698225,'120','D4345 Perio Scaling',124,'','',0,0,0),(31503716,5698225,'121','D4355 Full Mouth Debridement',125,'','',0,0,0),(31503719,5698225,'122','D2385 1 Surf. Resin Post',112,'','',0,0,0),(31503722,5698225,'123','D2386 2 Surf. Resin Post',113,'','',0,0,0),(31503725,5698225,'124','D2387 3 Surf. Resin Post',114,'','',0,0,0),(31503728,5698225,'125','D2388 4 Surf. Resin Post',115,'','',0,0,0),(31503731,5698225,'126','D3126 Temp. Filling',119,'','',0,0,0),(31503734,5698225,'127','D7112 Post-op visit',127,'','',0,0,0),(31503737,5698225,'128','D7110 Extraction Single Tooth',126,'','',0,0,0),(31503740,5698225,'129','D7120 Each Additional Tooth',128,'','',0,0,0),(31503743,5698225,'130','D7210 Surgical Extraction',129,'','',0,0,0),(31503746,5698225,'131','D7250 Resdiual Root Removal',130,'','',0,0,0),(31503749,5698225,'132','D7510 I. and D. -ascess',131,'','',0,0,0),(31503752,5698225,'133','D9110 Paliative, Tx',132,'','',0,0,0),(31503755,5698225,'134','D9630 Rx',133,'','',0,0,0),(31503758,5698225,'135','D9910 Desens.',134,'','',0,0,0),(31503761,5698225,'136','D9951 Occlusal Adjustment',135,'','',0,0,0),(31503764,5698225,'137','D9999 Unspec. Tx',136,'','',0,0,0),(31503767,5698225,'1','for SCC, Mobile Med, Proyecto Salud, PCWC, MCHP, Holy Cross, MCC Medical Clinic, Mercy, PCC - for SCC, Mobile Med, Proyecto Salud, PCWC, MCHP, Holy Cross, MCC Medical Clinic, Mercy, PCC',2,'','',0,0,0),(31503770,5698225,'2','PPCC001 - DSME Completed',1,'','',1,0,0),(31503773,5698225,'3','PPCC002 - DM Class 1',1,'','',1,0,0),(31503776,5698225,'4','PPCC003 - DM Class 2',3,'','',1,0,0),(31503779,5698225,'5','PPCC004 - DM Class 3',4,'','',1,0,0),(31503782,5698225,'6','PPCC005 - DM Class 4',5,'','',1,0,0),(31503785,5698225,'7','PPCC006 - Nutrition Education',6,'','',1,0,0),(31503788,5698225,'8','PPCC007 - Clinical Breast Exam',7,'','',1,0,0),(31503791,5698225,'9','PPCC008 - Diabetic Foot Check (LEAP)',8,'','',1,0,0),(31503794,5698225,'10','PPCC009 - Retinal Eye Exam',9,'','',1,0,0),(31503797,5698225,'11','PPCC010 - General Foot Exam',10,'','',1,0,0),(31503800,5698225,'12','AFC001 - EKG',11,'','',0,0,0),(31503803,5698225,'13','AFC002 - Echo',12,'','',0,0,0),(31503806,5698225,'14','AFC003 - Breast Biopsy',13,'','',0,0,0),(31503809,5698225,'15','AFC004 - Colposcopy',14,'','',0,0,0),(31503812,5698225,'16','AFC005 - Mammogram - Unspecified',15,'','',0,0,0),(31503815,5698225,'17','AFC006 - Mammogram - Initial',16,'','',0,0,0),(31503818,5698225,'18','AFC007 - Mammogram - Annual',17,'','',0,0,0),(31503821,5698225,'19','AFC008 - Mammogram - Followup',18,'','',0,0,0),(31503824,5698225,'20','AFC009 - Mammogram - Screening',19,'','',0,0,0),(31503827,5698225,'21','AFC010 - Mammogram - Daignosis',20,'','',0,0,0),(31503830,5698225,'22','AFC011 - Mammogram - Unilateral',21,'','',0,0,0),(31503833,5698225,'23','AFC012 - Mammogram - Bilateral',22,'','',0,0,0),(31503836,5698225,'24','AFC013 - Clinical Breast Ex-init/normal',23,'','',0,0,0),(31503839,5698225,'25','AFC014 - Clinical Breast Ex-init/abnormal',24,'','',0,0,0),(31503842,5698225,'26','AFC015 - Clinical Breast Ex-init/unknown',25,'','',0,0,0),(31503845,5698225,'27','AFC016 - Clinical Breast Ex-annual/normal',26,'','',0,0,0),(31503848,5698225,'28','AFC017 - Clinical Breast Ex-annual/abnorm',27,'','',0,0,0),(31503851,5698225,'29','AFC018 - Clinical Breast Ex-annual/unknow',28,'','',0,0,0),(31503854,5698225,'30','AFC019 - Clinical Breast Ex-followup/norm',29,'','',0,0,0),(31503857,5698225,'31','AFC020 - Clinical Breast Ex-followup/ab',30,'','',0,0,0),(31503860,5698225,'32','AFC021 - Clinical Breast Ex-followup/unk',31,'','',0,0,0),(31503863,5698225,'33','AFC022 - Pelvic Exam',32,'','',0,0,0),(31503866,5698225,'34','AFC023 - Colonoscopy',33,'','',0,0,0),(31503869,5698225,'35','AFC024 - Eye Exam',34,'','',0,0,0),(31503872,5698225,'36','AFC025 - Foot Exam',35,'','',0,0,0),(31503875,5698225,'37','AFC026 - Blood Pressure Check',36,'','',0,0,0),(31503878,5698225,'38','AFC027 - Ear Irrigation',37,'','',0,0,0),(31503881,5698225,'39','AFC028 - Fingerstick Glucose',38,'','',0,0,0),(31503884,5698225,'40','AFC029 - Injection, B12',39,'','',0,0,0),(31503887,5698225,'41','AFC030 - IInjection, Flu Vaccine',40,'','',0,0,0),(31503890,5698225,'42','AFC031 - Injection, Medication',41,'','',0,0,0),(31503893,5698225,'43','AFC032 - Nebulizer Treatment',42,'','',0,0,0),(31503896,5698225,'44','AFC033 - Nurse Consult',43,'','',0,0,0),(31503899,5698225,'45','AFC034 - Wound Care',44,'','',0,0,0),(31503902,5698225,'46','AFC035 - Breast Cyst Aspiration',45,'','',0,0,0),(31503905,5698225,'47','AFC036 - Endometrial Biopsy Obtained',46,'','',0,0,0),(31503908,5698225,'48','AFC037 - Pr--pap Smear',47,'','',0,0,0),(31503911,5698225,'49','AFC038 - Vaginal Biopsy Obtained',48,'','',0,0,0),(31503914,5698225,'50','AFC039 - Bronchoscopy',49,'','',0,0,0),(31503917,5698225,'51','AFC040 - Cervical Biopsy',50,'','',0,0,0),(31503920,5698225,'52','AFC041 - Chemo Therapy',51,'','',0,0,0),(31503923,5698225,'53','AFC042 - Ct Scan',52,'','',0,0,0),(31503926,5698225,'54','AFC043 - Endoscopy',53,'','',0,0,0),(31503929,5698225,'55','AFC044 - Fine Needle Aspiration',54,'','',0,0,0),(31503932,5698225,'56','AFC045 - Flex Sig',55,'','',0,0,0),(31503935,5698225,'57','AFC046 - Hearing Aid',56,'','',0,0,0),(31503938,5698225,'58','AFC047 - Hysterectomy',57,'','',0,0,0),(31503941,5698225,'59','AFC048 - Lithortripsy',58,'','',0,0,0),(31503944,5698225,'60','AFC049 - Mri',59,'','',0,0,0),(31503947,5698225,'61','AFC050 - Radiation Therapy',60,'','',0,0,0),(31503950,5698225,'62','AFC051 - Special Consult',61,'','',0,0,0),(31503953,5698225,'63','AFC052 - Surgery',62,'','',0,0,0),(31503956,5698225,'64','AFC053 - Case Manager Consult',63,'','',0,0,0),(31503959,5698225,'65','AFC054 - Pharmacy Consult',64,'','',0,0,0),(31503962,5698225,'66','AFC055 - Lab/radiology Referral',65,'','',0,0,0),(31503965,5698225,'67','AFC056 - Counseling-individual',66,'','',0,0,0),(31503968,5698225,'68','AFC057 - Counseling-group',67,'','',0,0,0),(31503971,5698225,'69','AFC058 - Medication Management',68,'','',0,0,0),(31503974,5698225,'70','AFC059 - Breast Consult',69,'','',0,0,0),(31503977,5698225,'71','AFC060 - Bone Scan',70,'','',0,0,0),(31503980,5698225,'72','AFC061 - Stress Test',71,'','',0,0,0),(31503983,5698225,'73','AFC062 - Holter Monitor',72,'','',0,0,0),(31503986,5698225,'74','AFC063 - Ultrasound',73,'','',0,0,0),(31503989,5698225,'75','AFC064 - Gyn Consult',74,'','',0,0,0),(31503992,5698225,'76','AFC065 - Skin Biopsy',75,'','',0,0,0),(31503995,5698225,'77','AFC066 - Diabetes Education Mod 1',76,'','',0,0,0),(31503998,5698225,'78','AFC067 - Diabetes Education Mod 2',77,'','',0,0,0),(31504001,5698225,'79','AFC068 - Diabetes Education Mod 3',78,'','',0,0,0),(31504004,5698225,'80','AFC069 - Diabetes Education Mod 4',79,'','',0,0,0),(31504007,5698225,'81','AFC070 - Diabetes Education Mod 5',80,'','',0,0,0),(31504010,5698225,'82','AFC071 - Diabetes Education Mod 6',81,'','',0,0,0),(31504013,5698225,'83','AFC072 - Diabetes Education Mod 7',82,'','',0,0,0),(31504016,5698225,'84','AFC073 - Diabetes Education Mod 8',83,'','',0,0,0),(31504019,5698225,'85','AFC074 - Diabetes Education Mod 9',84,'','',0,0,0),(31504022,5698225,'86','AFC075 - Diabetes Glucometer Instr',85,'','',0,0,0),(31504025,5698225,'87','AFC076 - Diet Education',86,'','',0,0,0),(31504028,5698225,'88','AFC077 - Dental Information',87,'','',0,0,0),(31504031,5698225,'89','D0053 Patient Referred Out',88,'','',1,0,0),(31504034,5698225,'90','D0120 Periodic Exam',89,'','',1,0,0),(31504037,5698225,'91','D0130 Emergency Exam',90,'','',1,0,0),(31504040,5698225,'92','D140 Limited Exam',91,'','',1,0,0),(31504043,5698225,'93','D0150 Comp. Exam',92,'','',1,0,0),(31504046,5698225,'94','D0210 Fmx',93,'','',1,0,0),(31504049,5698225,'95','D0220 Single Pa',94,'','',1,0,0),(31504052,5698225,'96','D0230 Add. Pa',95,'','',1,0,0),(31504055,5698225,'97','D0272 Two Bw\'s',96,'','',1,0,0),(31504058,5698225,'98','D0274 Four Bw\'s',97,'','',1,0,0),(31504061,5698225,'99','D0330 Panx',98,'','',1,0,0),(31504064,5698225,'100','D1110 Adult Prophy',99,'','',1,0,0),(31504067,5698225,'101','D1205 Fluoride Tx',100,'','',1,0,0),(31504070,5698225,'102','D1310 Nutr Couns.',101,'','',1,0,0),(31504073,5698225,'103','D1320 Tobac. Couns.',102,'','',1,0,0),(31504076,5698225,'104','D1330 Ohi',103,'','',1,0,0),(31504079,5698225,'105','D2140 1 Surf. Amal.',104,'','',1,0,0),(31504082,5698225,'106','D2150 2 Surf. Amal.',105,'','',1,0,0),(31504085,5698225,'107','D2160 3 Surf. Amal.',106,'','',1,0,0),(31504088,5698225,'108','D2161 4 Surf. Amal.',107,'','',1,0,0),(31504091,5698225,'109','D2330 1 Surf. Resin Anterior',108,'','',1,0,0),(31504094,5698225,'110','D2331 2 Surf. Resin Anterior',109,'','',1,0,0),(31504097,5698225,'111','D2332 3 Surf. Resin Anterior',110,'','',1,0,0),(31504100,5698225,'112','D2335 4 Surf. Resin Anterior',111,'','',1,0,0),(31504103,5698225,'113','D2949 Sed. Filling',116,'','',1,0,0),(31504106,5698225,'114','D3110 Pulp Cap. Dir.',117,'','',1,0,0),(31504109,5698225,'115','D3120 Pulp Cap. Ind.',118,'','',1,0,0),(31504112,5698225,'116','D3220 Pulpotomy',120,'','',1,0,0),(31504115,5698225,'117','D3251 Pulpectomy',121,'','',1,0,0),(31504118,5698225,'118','D4341 Perio Scale (each Quad)',122,'','',1,0,0),(31504121,5698225,'119','D4342 Perio Scale (1-3 Teeth)',123,'','',1,0,0),(31504124,5698225,'120','D4345 Perio Scaling',124,'','',1,0,0),(31504127,5698225,'121','D4355 Full Mouth Debridement',125,'','',1,0,0),(31504130,5698225,'122','D2385 1 Surf. Resin Post',112,'','',1,0,0),(31504133,5698225,'123','D2386 2 Surf. Resin Post',113,'','',1,0,0),(31504136,5698225,'124','D2387 3 Surf. Resin Post',114,'','',1,0,0),(31504139,5698225,'125','D2388 4 Surf. Resin Post',115,'','',1,0,0),(31504142,5698225,'126','D3126 Temp. Filling',119,'','',1,0,0),(31504145,5698225,'127','D7112 Post-op visit',127,'','',1,0,0),(31504148,5698225,'128','D7110 Extraction Single Tooth',126,'','',1,0,0),(31504151,5698225,'129','D7120 Each Additional Tooth',128,'','',1,0,0),(31504154,5698225,'130','D7210 Surgical Extraction',129,'','',1,0,0),(31504157,5698225,'131','D7250 Resdiual Root Removal',130,'','',1,0,0),(31504160,5698225,'132','D7510 I. and D. -ascess',131,'','',1,0,0),(31504163,5698225,'133','D9110 Paliative, Tx',132,'','',1,0,0),(31504166,5698225,'134','D9630 Rx',133,'','',1,0,0),(31504169,5698225,'135','D9910 Desens.',134,'','',1,0,0),(31504172,5698225,'136','D9951 Occlusal Adjustment',135,'','',1,0,0),(31504175,5698225,'137','D9999 Unspec. Tx',136,'','',1,0,0),(31504463,5698225,'1','for SCC, Mobile Med, Proyecto Salud, PCWC, MCHP, Holy Cross, MCC Medical Clinic, Mercy, PCC - for SCC, Mobile Med, Proyecto Salud, PCWC, MCHP, Holy Cross, MCC Medical Clinic, Mercy, PCC',3,'','',0,0,0),(31504466,5698225,'2','PPCC001 - DSME Completed',1,'','',0,0,0),(31504469,5698225,'3','PPCC002 - DM Class 1',2,'','',0,0,0),(31504472,5698225,'4','PPCC003 - DM Class 2',4,'','',0,0,0),(31504475,5698225,'5','PPCC004 - DM Class 3',5,'','',0,0,0),(31504478,5698225,'6','PPCC005 - DM Class 4',6,'','',0,0,0),(31504481,5698225,'7','PPCC006 - Nutrition Education',7,'','',0,0,0),(31504484,5698225,'8','PPCC007 - Clinical Breast Exam',8,'','',0,0,0),(31504487,5698225,'9','PPCC008 - Diabetic Foot Check (LEAP)',9,'','',0,0,0),(31504490,5698225,'10','PPCC009 - Retinal Eye Exam',10,'','',0,0,0),(31504493,5698225,'11','PPCC010 - General Foot Exam',11,'','',0,0,0),(31504496,5698225,'12','AFC001 - EKG',12,'','',0,0,0),(31504499,5698225,'13','AFC002 - Echo',13,'','',0,0,0),(31504502,5698225,'14','AFC003 - Breast Biopsy',14,'','',0,0,0),(31504505,5698225,'15','AFC004 - Colposcopy',15,'','',0,0,0),(31504508,5698225,'16','AFC005 - Mammogram - Unspecified',16,'','',0,0,0),(31504511,5698225,'17','AFC006 - Mammogram - Initial',17,'','',0,0,0),(31504514,5698225,'18','AFC007 - Mammogram - Annual',18,'','',0,0,0),(31504517,5698225,'19','AFC008 - Mammogram - Followup',19,'','',0,0,0),(31504520,5698225,'20','AFC009 - Mammogram - Screening',20,'','',0,0,0),(31504523,5698225,'21','AFC010 - Mammogram - Daignosis',21,'','',0,0,0),(31504526,5698225,'22','AFC011 - Mammogram - Unilateral',22,'','',0,0,0),(31504529,5698225,'23','AFC012 - Mammogram - Bilateral',23,'','',0,0,0),(31504532,5698225,'24','AFC013 - Clinical Breast Ex-init/normal',24,'','',0,0,0),(31504535,5698225,'25','AFC014 - Clinical Breast Ex-init/abnormal',25,'','',0,0,0),(31504538,5698225,'26','AFC015 - Clinical Breast Ex-init/unknown',26,'','',0,0,0),(31504541,5698225,'27','AFC016 - Clinical Breast Ex-annual/normal',27,'','',0,0,0),(31504544,5698225,'28','AFC017 - Clinical Breast Ex-annual/abnorm',28,'','',0,0,0),(31504547,5698225,'29','AFC018 - Clinical Breast Ex-annual/unknow',29,'','',0,0,0),(31504550,5698225,'30','AFC019 - Clinical Breast Ex-followup/norm',30,'','',0,0,0),(31504553,5698225,'31','AFC020 - Clinical Breast Ex-followup/ab',31,'','',0,0,0),(31504556,5698225,'32','AFC021 - Clinical Breast Ex-followup/unk',32,'','',0,0,0),(31504559,5698225,'33','AFC022 - Pelvic Exam',33,'','',0,0,0),(31504562,5698225,'34','AFC023 - Colonoscopy',34,'','',0,0,0),(31504565,5698225,'35','AFC024 - Eye Exam',35,'','',0,0,0),(31504568,5698225,'36','AFC025 - Foot Exam',36,'','',0,0,0),(31504571,5698225,'37','AFC026 - Blood Pressure Check',37,'','',0,0,0),(31504574,5698225,'38','AFC027 - Ear Irrigation',38,'','',0,0,0),(31504577,5698225,'39','AFC028 - Fingerstick Glucose',39,'','',0,0,0),(31504580,5698225,'40','AFC029 - Injection, B12',40,'','',0,0,0),(31504583,5698225,'41','AFC030 - IInjection, Flu Vaccine',41,'','',0,0,0),(31504586,5698225,'42','AFC031 - Injection, Medication',42,'','',0,0,0),(31504589,5698225,'43','AFC032 - Nebulizer Treatment',43,'','',0,0,0),(31504592,5698225,'44','AFC033 - Nurse Consult',44,'','',0,0,0),(31504595,5698225,'45','AFC034 - Wound Care',45,'','',0,0,0),(31504598,5698225,'46','AFC035 - Breast Cyst Aspiration',46,'','',0,0,0),(31504601,5698225,'47','AFC036 - Endometrial Biopsy Obtained',47,'','',0,0,0),(31504604,5698225,'48','AFC037 - Pr--pap Smear',48,'','',0,0,0),(31504607,5698225,'49','AFC038 - Vaginal Biopsy Obtained',49,'','',0,0,0),(31504610,5698225,'50','AFC039 - Bronchoscopy',50,'','',0,0,0),(31504613,5698225,'51','AFC040 - Cervical Biopsy',51,'','',0,0,0),(31504616,5698225,'52','AFC041 - Chemo Therapy',52,'','',0,0,0),(31504619,5698225,'53','AFC042 - Ct Scan',53,'','',0,0,0),(31504622,5698225,'54','AFC043 - Endoscopy',54,'','',0,0,0),(31504625,5698225,'55','AFC044 - Fine Needle Aspiration',55,'','',0,0,0),(31504628,5698225,'56','AFC045 - Flex Sig',56,'','',0,0,0),(31504631,5698225,'57','AFC046 - Hearing Aid',57,'','',0,0,0),(31504634,5698225,'58','AFC047 - Hysterectomy',58,'','',0,0,0),(31504637,5698225,'59','AFC048 - Lithortripsy',59,'','',0,0,0),(31504640,5698225,'60','AFC049 - Mri',60,'','',0,0,0),(31504643,5698225,'61','AFC050 - Radiation Therapy',61,'','',0,0,0),(31504646,5698225,'62','AFC051 - Special Consult',62,'','',0,0,0),(31504649,5698225,'63','AFC052 - Surgery',63,'','',0,0,0),(31504652,5698225,'64','AFC053 - Case Manager Consult',64,'','',0,0,0),(31504655,5698225,'65','AFC054 - Pharmacy Consult',65,'','',0,0,0),(31504658,5698225,'66','AFC055 - Lab/radiology Referral',66,'','',0,0,0),(31504661,5698225,'67','AFC056 - Counseling-individual',67,'','',0,0,0),(31504664,5698225,'68','AFC057 - Counseling-group',68,'','',0,0,0),(31504667,5698225,'69','AFC058 - Medication Management',69,'','',0,0,0),(31504670,5698225,'70','AFC059 - Breast Consult',70,'','',0,0,0),(31504673,5698225,'71','AFC060 - Bone Scan',71,'','',0,0,0),(31504676,5698225,'72','AFC061 - Stress Test',72,'','',0,0,0),(31504679,5698225,'73','AFC062 - Holter Monitor',73,'','',0,0,0),(31504682,5698225,'74','AFC063 - Ultrasound',74,'','',0,0,0),(31504685,5698225,'75','AFC064 - Gyn Consult',75,'','',0,0,0),(31504688,5698225,'76','AFC065 - Skin Biopsy',76,'','',0,0,0),(31504691,5698225,'77','AFC066 - Diabetes Education Mod 1',77,'','',0,0,0),(31504694,5698225,'78','AFC067 - Diabetes Education Mod 2',78,'','',0,0,0),(31504697,5698225,'79','AFC068 - Diabetes Education Mod 3',79,'','',0,0,0),(31504700,5698225,'80','AFC069 - Diabetes Education Mod 4',80,'','',0,0,0),(31504703,5698225,'81','AFC070 - Diabetes Education Mod 5',81,'','',0,0,0),(31504706,5698225,'82','AFC071 - Diabetes Education Mod 6',82,'','',0,0,0),(31504709,5698225,'83','AFC072 - Diabetes Education Mod 7',83,'','',0,0,0),(31504712,5698225,'84','AFC073 - Diabetes Education Mod 8',84,'','',0,0,0),(31504715,5698225,'85','AFC074 - Diabetes Education Mod 9',85,'','',0,0,0),(31504718,5698225,'86','AFC075 - Diabetes Glucometer Instr',86,'','',0,0,0),(31504721,5698225,'87','AFC076 - Diet Education',87,'','',0,0,0),(31504724,5698225,'88','AFC077 - Dental Information',88,'','',0,0,0),(31504727,5698225,'89','D0053 Patient Referred Out',0,'','',1,0,0),(31504730,5698225,'90','D0120 Periodic Exam',89,'','',1,0,0),(31504733,5698225,'91','D0130 Emergency Exam',90,'','',1,0,0),(31504736,5698225,'92','D140 Limited Exam',91,'','',1,0,0),(31504739,5698225,'93','D0150 Comp. Exam',92,'','',1,0,0),(31504742,5698225,'94','D0210 Fmx',93,'','',1,0,0),(31504745,5698225,'95','D0220 Single Pa',94,'','',1,0,0),(31504748,5698225,'96','D0230 Add. Pa',95,'','',1,0,0),(31504751,5698225,'97','D0272 Two Bws',96,'','',1,0,0),(31504754,5698225,'98','D0274 Four Bws',97,'','',1,0,0),(31504757,5698225,'99','D0330 Panx',98,'','',1,0,0),(31504760,5698225,'100','D1110 Adult Prophy',99,'','',1,0,0),(31504763,5698225,'101','D1205 Fluoride Tx',100,'','',1,0,0),(31504766,5698225,'102','D1310 Nutr Couns.',101,'','',1,0,0),(31504769,5698225,'103','D1320 Tobac. Couns.',102,'','',1,0,0),(31504772,5698225,'104','D1330 Ohi',103,'','',1,0,0),(31504775,5698225,'105','D2140 1 Surf. Amal.',104,'','',1,0,0),(31504778,5698225,'106','D2150 2 Surf. Amal.',105,'','',1,0,0),(31504781,5698225,'107','D2160 3 Surf. Amal.',106,'','',1,0,0),(31504784,5698225,'108','D2161 4 Surf. Amal.',107,'','',1,0,0),(31504787,5698225,'109','D2330 1 Surf. Resin Anterior',108,'','',1,0,0),(31504790,5698225,'110','D2331 2 Surf. Resin Anterior',109,'','',1,0,0),(31504793,5698225,'111','D2332 3 Surf. Resin Anterior',110,'','',1,0,0),(31504796,5698225,'112','D2335 4 Surf. Resin Anterior',111,'','',1,0,0),(31504799,5698225,'113','D2949 Sed. Filling',116,'','',1,0,0),(31504802,5698225,'114','D3110 Pulp Cap. Dir.',117,'','',1,0,0),(31504805,5698225,'115','D3120 Pulp Cap. Ind.',118,'','',1,0,0),(31504808,5698225,'116','D3220 Pulpotomy',120,'','',1,0,0),(31504811,5698225,'117','D3251 Pulpectomy',121,'','',1,0,0),(31504814,5698225,'118','D4341 Perio Scale (each Quad)',122,'','',1,0,0),(31504817,5698225,'119','D4342 Perio Scale (1-3 Teeth)',123,'','',1,0,0),(31504820,5698225,'120','D4345 Perio Scaling',124,'','',1,0,0),(31504823,5698225,'121','D4355 Full Mouth Debridement',125,'','',1,0,0),(31504826,5698225,'122','D2385 1 Surf. Resin Post',112,'','',1,0,0),(31504829,5698225,'123','D2386 2 Surf. Resin Post',113,'','',1,0,0),(31504832,5698225,'124','D2387 3 Surf. Resin Post',114,'','',1,0,0),(31504835,5698225,'125','D2388 4 Surf. Resin Post',115,'','',1,0,0),(31504838,5698225,'126','D3126 Temp. Filling',119,'','',1,0,0),(31504841,5698225,'127','D7112 Post-op visit',127,'','',1,0,0),(31504844,5698225,'128','D7110 Extraction Single Tooth',126,'','',1,0,0),(31504847,5698225,'129','D7120 Each Additional Tooth',128,'','',1,0,0),(31504850,5698225,'130','D7210 Surgical Extraction',129,'','',1,0,0),(31504853,5698225,'131','D7250 Resdiual Root Removal',130,'','',1,0,0),(31504856,5698225,'132','D7510 I. and D. -ascess',131,'','',1,0,0),(31504859,5698225,'133','D9110 Paliative, Tx',132,'','',1,0,0),(31504862,5698225,'134','D9630 Rx',133,'','',1,0,0),(31504865,5698225,'135','D9910 Desens.',134,'','',1,0,0),(31504868,5698225,'136','D9951 Occlusal Adjustment',135,'','',1,0,0),(31504871,5698225,'137','D9999 Unspec. Tx',136,'','',1,0,0),(31505390,5696523,'1','for SCC, Mobile Med, Proyecto Salud, PCWC, MCHP, Holy Cross, MCC Medical Clinic, PCC - for SCC, Mobile Med, Proyecto Salud, PCWC, MCHP, Holy Cross, MCC Medical Clinic, PCC',1,'','',0,0,0),(31505393,5696523,'2','DPCC001 - Endocrine/Thyroid Disease (240-246)',1,'','',0,0,0),(31505396,5696523,'3','DPCC002 - Endocrine/Diabetes (250)',2,'','',0,0,0),(31505399,5696523,'4','DPCC003 - Blood diseases--(280-289)',3,'','',0,0,0),(31505402,5696523,'5','DPCC004 - Mental Health/Substance Abuse(290-319)',4,'','',0,0,0),(31505405,5696523,'6','DPCC005 - Nervous System Diseases (320-359)',5,'','',0,0,0),(31505408,5696523,'7','DPCC006 - Eye Diseases (360-379)',6,'','',0,0,0),(31505411,5696523,'8','DPCC007 - Ear Disease (380-389)',7,'','',0,0,0),(31505414,5696523,'9','DPCC008 - Circulatory System Diseases (390-459)',8,'','',0,0,0),(31505417,5696523,'10','DPCC009 - Hypertension (401)',9,'','',0,0,0),(31505420,5696523,'11','DPCC010 - Cerebrovascular Diseases (430-438)',10,'','',0,0,0),(31505423,5696523,'12','DPCC011 - Respiratory Diseases  (460-519)',11,'','',0,0,0),(31505426,5696523,'13','DPCC012 - Digestive Diseases (520-579)',12,'','',0,0,0),(31505429,5696523,'14','DPCC013 - Urinary System Diseases (580-608)',13,'','',0,0,0),(31505432,5696523,'15','DPCC014 - Breast Diseases (610-611)',14,'','',0,0,0),(31505435,5696523,'16','DPCC015 - Gynecological Disorders (614-627)',15,'','',0,0,0),(31505438,5696523,'17','DPCC016 - Skin Disease (680-709)',16,'','',0,0,0),(31505441,5696523,'18','DPCC017 - Musculoskeletal/Connective Tissue (710-739)',17,'','',0,0,0),(31505444,5696523,'19','DPCC018 - Signs & Symptoms/Ill-defined (780-799)',18,'','',0,0,0),(31505447,5696523,'20','DPCC019 - Injuries & Poisoning (800-999)',19,'','',0,0,0),(31505450,5696523,'21','DPCC020 - Circulatory System Diseases (390-459)',20,'','',0,0,0),(31505453,5696523,'22','MH0.01 - Allergy',21,'','',0,0,0),(31505456,5696523,'23','MH0.02 - Hyperlipedemia',22,'','',0,0,0),(31505459,5696523,'24','MH0.03 - Hypertension ',23,'','',0,0,0),(31505462,5696523,'25','MH0.04 - Heart Failure',24,'','',0,0,0),(31505465,5696523,'26','MH0.05 - Other Cardiovascular ',25,'','',0,0,0),(31505468,5696523,'27','MH0.06 - Dermatologic',26,'','',0,0,0),(31505471,5696523,'28','MH0.07 - Diabetes',27,'','',0,0,0),(31505474,5696523,'29','MH0.08 - Diabetes-retinopathy',28,'','',0,0,0),(31505477,5696523,'30','MH0.09 - Diabetes-nephropathy',29,'','',0,0,0),(31505480,5696523,'31','MH0.10 - Diabetes-neuropathy',30,'','',0,0,0),(31505483,5696523,'32','MH0.11 - Diabetes-vascular Disease',31,'','',0,0,0),(31505486,5696523,'33','MH0.12 - Education',32,'','',0,0,0),(31505489,5696523,'34','MH0.13 - Education-diabetes',33,'','',0,0,0),(31505492,5696523,'35','MH0.14 - Education-diet',34,'','',0,0,0),(31505495,5696523,'36','MH0.15 - Education-other',35,'','',0,0,0),(31505498,5696523,'37','MH0.16 - Endocrine',36,'','',0,0,0),(31505501,5696523,'38','MH0.17 - ENT',37,'','',0,0,0),(31505504,5696523,'39','MH0.18 - Eye',38,'','',0,0,0),(31505507,5696523,'40','MH0.19 - Gastrointestinal',39,'','',0,0,0),(31505510,5696523,'41','MH0.20 - Gyn',40,'','',0,0,0),(31505513,5696523,'42','MH0.21 - Hematologic',41,'','',0,0,0),(31505516,5696523,'43','MH0.22 - Mental Health',42,'','',0,0,0),(31505519,5696523,'44','MH0.23 - Neurologic',43,'','',0,0,0),(31505522,5696523,'45','MH0.24 - Oncology',44,'','',0,0,0),(31505525,5696523,'46','MH0.25 - Orthopedic',45,'','',0,0,0),(31505528,5696523,'47','MH0.26 - Physical Exam/forms',46,'','',0,0,0),(31505531,5696523,'48','MH0.27 - Podiatry',47,'','',0,0,0),(31505534,5696523,'49','MH0.28 - Respiratory',48,'','',0,0,0),(31505537,5696523,'50','MH0.29 - Rheumatology',49,'','',0,0,0),(31505540,5696523,'51','MH0.30 - Urologic',50,'','',0,0,0),(31505543,5696523,'52','MH0.31 - Other',51,'','',0,0,0),(31505546,5696523,'53','MH0.32 - Unknown',52,'','',0,0,0),(31505549,5696523,'54','MH0.33 - Obesity',53,'','',0,0,0),(31505552,5696523,'55','MH0.34 - Metabolic Syndrome',54,'','',0,0,0),(31505555,5696523,'56','MH1.01 - Phmercy',55,'','',0,0,0),(31505558,5696523,'57','MH1.02 - Phmedbank',56,'','',0,0,0),(31505561,5696523,'58','MH1.03 - Phselfpay',57,'','',0,0,0),(31505564,5696523,'59','MH1.04 - Phmdcard',58,'','',0,0,0),(31505567,5696523,'60','MH1.05 - Phother',59,'','',0,0,0),(31505570,5696523,'61','AFC.100 - Cardiovascular - Hypertension',60,'','',1,0,0),(31505573,5696523,'62','AFC.101 - Cardiovascular - Other',61,'','',1,0,0),(31505576,5696523,'63','AFC.102 - Dermatologic',62,'','',1,0,0),(31505579,5696523,'64','AFC.103 - Education_ Group - Diabetes',63,'','',1,0,0),(31505582,5696523,'65','AFC.104 - Education_ Group - Diet',64,'','',1,0,0),(31505585,5696523,'66','AFC.105 - Education_ Group - Exercise',65,'','',1,0,0),(31505588,5696523,'67','AFC.106 - Education_ Group - Hl',66,'','',1,0,0),(31505591,5696523,'68','AFC.107 - Education_ Group - Other  ',67,'','',1,0,0),(31505594,5696523,'69','AFC.108 - Education_ Group - Self Breast Exam',68,'','',1,0,0),(31505597,5696523,'70','AFC.109 - Education_ Indiv - Asthma ',69,'','',1,0,0),(31505600,5696523,'71','AFC.110 - Education_ Indiv - Diabetes',70,'','',1,0,0),(31505603,5696523,'72','AFC.111 - Education_ Indiv - Diet',71,'','',1,0,0),(31505606,5696523,'73','AFC.112 - Education_ Indiv - Hl',72,'','',1,0,0),(31505609,5696523,'74','AFC.113 - Education_ Indiv - Medication',73,'','',1,0,0),(31505612,5696523,'75','AFC.114 - Education_ Indiv - Other',74,'','',1,0,0),(31505615,5696523,'76','AFC.115 - Education_ Indiv - Self Breast Exam',75,'','',1,0,0),(31505618,5696523,'77','AFC.116 - Endocrine - Diabetes',76,'','',1,0,0),(31505621,5696523,'78','AFC.117 - Endocrine - Other',77,'','',1,0,0),(31505624,5696523,'79','AFC.118 - ENT',78,'','',1,0,0),(31505627,5696523,'80','AFC.119 - Eye',79,'','',1,0,0),(31505630,5696523,'81','AFC.120 - Gastrointestinal',80,'','',1,0,0),(31505633,5696523,'82','AFC.121 - Gyn',81,'','',1,0,0),(31505636,5696523,'83','AFC.122 - Hematologic',82,'','',1,0,0),(31505639,5696523,'84','AFC.123 - Mental Health',83,'','',1,0,0),(31505642,5696523,'85','AFC.124 - Musculoskeletal',84,'','',1,0,0),(31505645,5696523,'86','AFC.125 - Nephrology',85,'','',1,0,0),(31505648,5696523,'87','AFC.126 - Neurologic',86,'','',1,0,0),(31505651,5696523,'88','AFC.127 - Oncology',87,'','',1,0,0),(31505654,5696523,'89','AFC.128 - Physical Therapy - Assessment',88,'','',1,0,0),(31505657,5696523,'90','AFC.129 - Physical Therapy - Follow-up',89,'','',1,0,0),(31505660,5696523,'91','AFC.130 - Podiatry',90,'','',1,0,0),(31505663,5696523,'92','AFC.131 - Respiratory - Asthma',91,'','',1,0,0),(31505666,5696523,'93','AFC.132 - Respiratory - Other',92,'','',1,0,0),(31505669,5696523,'94','AFC.133 - Urologic',93,'','',1,0,0),(31505672,5696523,'1','for SCC, Mobile Med, Proyecto Salud, PCWC, MCHP, Holy Cross, MCC Medical Clinic, PCC - for SCC, Mobile Med, Proyecto Salud, PCWC, MCHP, Holy Cross, MCC Medical Clinic, PCC',1,'','',0,0,0),(31505675,5696523,'2','DPCC001 - Endocrine/Thyroid Disease (240-246)',1,'','',1,0,0),(31505678,5696523,'3','DPCC002 - Endocrine/Diabetes (250)',2,'','',1,0,0),(31505681,5696523,'4','DPCC003 - Blood diseases--(280-289)',3,'','',1,0,0),(31505684,5696523,'5','DPCC004 - Mental Health/Substance Abuse(290-319)',4,'','',1,0,0),(31505687,5696523,'6','DPCC005 - Nervous System Diseases (320-359)',5,'','',1,0,0),(31505690,5696523,'7','DPCC006 - Eye Diseases (360-379)',6,'','',1,0,0),(31505693,5696523,'8','DPCC007 - Ear Disease (380-389)',7,'','',1,0,0),(31505696,5696523,'9','DPCC008 - Circulatory System Diseases (390-459)',8,'','',1,0,0),(31505699,5696523,'10','DPCC009 - Hypertension (401)',9,'','',1,0,0),(31505702,5696523,'11','DPCC010 - Cerebrovascular Diseases (430-438)',10,'','',1,0,0),(31505705,5696523,'12','DPCC011 - Respiratory Diseases  (460-519)',11,'','',1,0,0),(31505708,5696523,'13','DPCC012 - Digestive Diseases (520-579)',12,'','',1,0,0),(31505711,5696523,'14','DPCC013 - Urinary System Diseases (580-608)',13,'','',1,0,0),(31505714,5696523,'15','DPCC014 - Breast Diseases (610-611)',14,'','',1,0,0),(31505717,5696523,'16','DPCC015 - Gynecological Disorders (614-627)',15,'','',1,0,0),(31505720,5696523,'17','DPCC016 - Skin Disease (680-709)',16,'','',1,0,0),(31505723,5696523,'18','DPCC017 - Musculoskeletal/Connective Tissue (710-739)',17,'','',1,0,0),(31505726,5696523,'19','DPCC018 - Signs & Symptoms/Ill-defined (780-799)',18,'','',1,0,0),(31505729,5696523,'20','DPCC019 - Injuries & Poisoning (800-999)',19,'','',1,0,0),(31505732,5696523,'21','DPCC020 - Circulatory System Diseases (390-459)',20,'','',1,0,0),(31505735,5696523,'22','MH0.01 - Allergy',21,'','',1,0,0),(31505738,5696523,'23','MH0.02 - Hyperlipedemia',22,'','',1,0,0),(31505741,5696523,'24','MH0.03 - Hypertension ',23,'','',1,0,0),(31505744,5696523,'25','MH0.04 - Heart Failure',24,'','',1,0,0),(31505747,5696523,'26','MH0.05 - Other Cardiovascular ',25,'','',1,0,0),(31505750,5696523,'27','MH0.06 - Dermatologic',26,'','',1,0,0),(31505753,5696523,'28','MH0.07 - Diabetes',27,'','',1,0,0),(31505756,5696523,'29','MH0.08 - Diabetes-retinopathy',28,'','',1,0,0),(31505759,5696523,'30','MH0.09 - Diabetes-nephropathy',29,'','',1,0,0),(31505762,5696523,'31','MH0.10 - Diabetes-neuropathy',30,'','',1,0,0),(31505765,5696523,'32','MH0.11 - Diabetes-vascular Disease',31,'','',1,0,0),(31505768,5696523,'33','MH0.12 - Education',32,'','',1,0,0),(31505771,5696523,'34','MH0.13 - Education-diabetes',33,'','',1,0,0),(31505774,5696523,'35','MH0.14 - Education-diet',34,'','',1,0,0),(31505777,5696523,'36','MH0.15 - Education-other',35,'','',1,0,0),(31505780,5696523,'37','MH0.16 - Endocrine',36,'','',1,0,0),(31505783,5696523,'38','MH0.17 - ENT',37,'','',1,0,0),(31505786,5696523,'39','MH0.18 - Eye',38,'','',1,0,0),(31505789,5696523,'40','MH0.19 - Gastrointestinal',39,'','',1,0,0),(31505792,5696523,'41','MH0.20 - Gyn',40,'','',1,0,0),(31505795,5696523,'42','MH0.21 - Hematologic',41,'','',1,0,0),(31505798,5696523,'43','MH0.22 - Mental Health',42,'','',1,0,0),(31505801,5696523,'44','MH0.23 - Neurologic',43,'','',1,0,0),(31505804,5696523,'45','MH0.24 - Oncology',44,'','',1,0,0),(31505807,5696523,'46','MH0.25 - Orthopedic',45,'','',1,0,0),(31505810,5696523,'47','MH0.26 - Physical Exam/forms',46,'','',1,0,0),(31505813,5696523,'48','MH0.27 - Podiatry',47,'','',1,0,0),(31505816,5696523,'49','MH0.28 - Respiratory',48,'','',1,0,0),(31505819,5696523,'50','MH0.29 - Rheumatology',49,'','',1,0,0),(31505822,5696523,'51','MH0.30 - Urologic',50,'','',1,0,0),(31505825,5696523,'52','MH0.31 - Other',51,'','',1,0,0),(31505828,5696523,'53','MH0.32 - Unknown',52,'','',1,0,0),(31505831,5696523,'54','MH0.33 - Obesity',53,'','',1,0,0),(31505834,5696523,'55','MH0.34 - Metabolic Syndrome',54,'','',1,0,0),(31505837,5696523,'56','MH1.01 - Phmercy',55,'','',1,0,0),(31505840,5696523,'57','MH1.02 - Phmedbank',56,'','',1,0,0),(31505843,5696523,'58','MH1.03 - Phselfpay',57,'','',1,0,0),(31505846,5696523,'59','MH1.04 - Phmdcard',58,'','',1,0,0),(31505849,5696523,'60','MH1.05 - Phother',59,'','',1,0,0),(31505852,5696523,'61','AFC.100 - Cardiovascular - Hypertension',60,'','',1,0,0),(31505855,5696523,'62','AFC.101 - Cardiovascular - Other',61,'','',1,0,0),(31505858,5696523,'63','AFC.102 - Dermatologic',62,'','',1,0,0),(31505861,5696523,'64','AFC.103 - Education_ Group - Diabetes',63,'','',1,0,0),(31505864,5696523,'65','AFC.104 - Education_ Group - Diet',64,'','',1,0,0),(31505867,5696523,'66','AFC.105 - Education_ Group - Exercise',65,'','',1,0,0),(31505870,5696523,'67','AFC.106 - Education_ Group - Hl',66,'','',1,0,0),(31505873,5696523,'68','AFC.107 - Education_ Group - Other  ',67,'','',1,0,0),(31505876,5696523,'69','AFC.108 - Education_ Group - Self Breast Exam',68,'','',1,0,0),(31505879,5696523,'70','AFC.109 - Education_ Indiv - Asthma ',69,'','',1,0,0),(31505882,5696523,'71','AFC.110 - Education_ Indiv - Diabetes',70,'','',1,0,0),(31505885,5696523,'72','AFC.111 - Education_ Indiv - Diet',71,'','',1,0,0),(31505888,5696523,'73','AFC.112 - Education_ Indiv - Hl',72,'','',1,0,0),(31505891,5696523,'74','AFC.113 - Education_ Indiv - Medication',73,'','',1,0,0),(31505894,5696523,'75','AFC.114 - Education_ Indiv - Other',74,'','',1,0,0),(31505897,5696523,'76','AFC.115 - Education_ Indiv - Self Breast Exam',75,'','',1,0,0),(31505900,5696523,'77','AFC.116 - Endocrine - Diabetes',76,'','',1,0,0),(31505903,5696523,'78','AFC.117 - Endocrine - Other',77,'','',1,0,0),(31505906,5696523,'79','AFC.118 - ENT',78,'','',1,0,0),(31505909,5696523,'80','AFC.119 - Eye',79,'','',1,0,0),(31505912,5696523,'81','AFC.120 - Gastrointestinal',80,'','',1,0,0),(31505915,5696523,'82','AFC.121 - Gyn',81,'','',1,0,0),(31505918,5696523,'83','AFC.122 - Hematologic',82,'','',1,0,0),(31505921,5696523,'84','AFC.123 - Mental Health',83,'','',1,0,0),(31505924,5696523,'85','AFC.124 - Musculoskeletal',84,'','',1,0,0),(31505927,5696523,'86','AFC.125 - Nephrology',85,'','',1,0,0),(31505930,5696523,'87','AFC.126 - Neurologic',86,'','',1,0,0),(31505933,5696523,'88','AFC.127 - Oncology',87,'','',1,0,0),(31505936,5696523,'89','AFC.128 - Physical Therapy - Assessment',88,'','',1,0,0),(31505939,5696523,'90','AFC.129 - Physical Therapy - Follow-up',89,'','',1,0,0),(31505942,5696523,'91','AFC.130 - Podiatry',90,'','',1,0,0),(31505945,5696523,'92','AFC.131 - Respiratory - Asthma',91,'','',1,0,0),(31505948,5696523,'93','AFC.132 - Respiratory - Other',92,'','',1,0,0),(31505951,5696523,'94','AFC.133 - Urologic',93,'','',1,0,0),(31506072,5696523,'1','for SCC, Mobile Med, Proyecto Salud, PCWC, MCHP, Holy Cross, MCC Medical Clinic, PCC - for SCC, Mobile Med, Proyecto Salud, PCWC, MCHP, Holy Cross, MCC Medical Clinic, PCC',1,'','',0,0,0),(31506075,5696523,'2','DPCC001 - Endocrine/Thyroid Disease (240-246)',1,'','',1,0,0),(31506078,5696523,'3','DPCC002 - Endocrine/Diabetes (250)',2,'','',1,0,0),(31506081,5696523,'4','DPCC003 - Blood diseases--(280-289)',3,'','',1,0,0),(31506084,5696523,'5','DPCC004 - Mental Health/Substance Abuse(290-319)',4,'','',1,0,0),(31506087,5696523,'6','DPCC005 - Nervous System Diseases (320-359)',5,'','',1,0,0),(31506090,5696523,'7','DPCC006 - Eye Diseases (360-379)',6,'','',1,0,0),(31506093,5696523,'8','DPCC007 - Ear Disease (380-389)',7,'','',1,0,0),(31506096,5696523,'9','DPCC008 - Circulatory System Diseases (390-459)',8,'','',1,0,0),(31506099,5696523,'10','DPCC009 - Hypertension (401)',9,'','',1,0,0),(31506102,5696523,'11','DPCC010 - Cerebrovascular Diseases (430-438)',10,'','',1,0,0),(31506105,5696523,'12','DPCC011 - Respiratory Diseases  (460-519)',11,'','',1,0,0),(31506108,5696523,'13','DPCC012 - Digestive Diseases (520-579)',12,'','',1,0,0),(31506111,5696523,'14','DPCC013 - Urinary System Diseases (580-608)',13,'','',1,0,0),(31506114,5696523,'15','DPCC014 - Breast Diseases (610-611)',14,'','',1,0,0),(31506117,5696523,'16','DPCC015 - Gynecological Disorders (614-627)',15,'','',1,0,0),(31506120,5696523,'17','DPCC016 - Skin Disease (680-709)',16,'','',1,0,0),(31506123,5696523,'18','DPCC017 - Musculoskeletal/Connective Tissue (710-739)',17,'','',1,0,0),(31506126,5696523,'19','DPCC018 - Signs & Symptoms/Ill-defined (780-799)',18,'','',1,0,0),(31506129,5696523,'20','DPCC019 - Injuries & Poisoning (800-999)',19,'','',1,0,0),(31506132,5696523,'21','DPCC020 - Circulatory System Diseases (390-459)',20,'','',1,0,0),(31506135,5696523,'22','MH0.01 - Allergy',21,'','',1,0,0),(31506138,5696523,'23','MH0.02 - Hyperlipedemia',22,'','',1,0,0),(31506141,5696523,'24','MH0.03 - Hypertension ',23,'','',1,0,0),(31506144,5696523,'25','MH0.04 - Heart Failure',24,'','',1,0,0),(31506147,5696523,'26','MH0.05 - Other Cardiovascular ',25,'','',1,0,0),(31506150,5696523,'27','MH0.06 - Dermatologic',26,'','',1,0,0),(31506153,5696523,'28','MH0.07 - Diabetes',27,'','',1,0,0),(31506156,5696523,'29','MH0.08 - Diabetes-retinopathy',28,'','',1,0,0),(31506159,5696523,'30','MH0.09 - Diabetes-nephropathy',29,'','',1,0,0),(31506162,5696523,'31','MH0.10 - Diabetes-neuropathy',30,'','',1,0,0),(31506165,5696523,'32','MH0.11 - Diabetes-vascular Disease',31,'','',1,0,0),(31506168,5696523,'33','MH0.12 - Education',32,'','',1,0,0),(31506171,5696523,'34','MH0.13 - Education-diabetes',33,'','',1,0,0),(31506174,5696523,'35','MH0.14 - Education-diet',34,'','',1,0,0),(31506177,5696523,'36','MH0.15 - Education-other',35,'','',1,0,0),(31506180,5696523,'37','MH0.16 - Endocrine',36,'','',1,0,0),(31506183,5696523,'38','MH0.17 - ENT',37,'','',1,0,0),(31506186,5696523,'39','MH0.18 - Eye',38,'','',1,0,0),(31506189,5696523,'40','MH0.19 - Gastrointestinal',39,'','',1,0,0),(31506192,5696523,'41','MH0.20 - Gyn',40,'','',1,0,0),(31506195,5696523,'42','MH0.21 - Hematologic',41,'','',1,0,0),(31506198,5696523,'43','MH0.22 - Mental Health',42,'','',1,0,0),(31506201,5696523,'44','MH0.23 - Neurologic',43,'','',1,0,0),(31506204,5696523,'45','MH0.24 - Oncology',44,'','',1,0,0),(31506207,5696523,'46','MH0.25 - Orthopedic',45,'','',1,0,0),(31506210,5696523,'47','MH0.26 - Physical Exam/forms',46,'','',1,0,0),(31506213,5696523,'48','MH0.27 - Podiatry',47,'','',1,0,0),(31506216,5696523,'49','MH0.28 - Respiratory',48,'','',1,0,0),(31506219,5696523,'50','MH0.29 - Rheumatology',49,'','',1,0,0),(31506222,5696523,'51','MH0.30 - Urologic',50,'','',1,0,0),(31506225,5696523,'52','MH0.31 - Other',51,'','',1,0,0),(31506228,5696523,'53','MH0.32 - Unknown',52,'','',1,0,0),(31506231,5696523,'54','MH0.33 - Obesity',53,'','',1,0,0),(31506234,5696523,'55','MH0.34 - Metabolic Syndrome',54,'','',1,0,0),(31506237,5696523,'56','MH1.01 - Phmercy',55,'','',1,0,0),(31506240,5696523,'57','MH1.02 - Phmedbank',56,'','',1,0,0),(31506243,5696523,'58','MH1.03 - Phselfpay',57,'','',1,0,0),(31506246,5696523,'59','MH1.04 - Phmdcard',58,'','',1,0,0),(31506249,5696523,'60','MH1.05 - Phother',59,'','',1,0,0),(31506252,5696523,'61','AFC.100 - Cardiovascular - Hypertension',60,'','',0,0,0),(31506255,5696523,'62','AFC.101 - Cardiovascular - Other',61,'','',0,0,0),(31506258,5696523,'63','AFC.102 - Dermatologic',62,'','',0,0,0),(31506261,5696523,'64','AFC.103 - Education_ Group - Diabetes',63,'','',0,0,0),(31506264,5696523,'65','AFC.104 - Education_ Group - Diet',64,'','',0,0,0),(31506267,5696523,'66','AFC.105 - Education_ Group - Exercise',65,'','',0,0,0),(31506270,5696523,'67','AFC.106 - Education_ Group - Hl',66,'','',0,0,0),(31506273,5696523,'68','AFC.107 - Education_ Group - Other  ',67,'','',0,0,0),(31506276,5696523,'69','AFC.108 - Education_ Group - Self Breast Exam',68,'','',0,0,0),(31506279,5696523,'70','AFC.109 - Education_ Indiv - Asthma ',69,'','',0,0,0),(31506282,5696523,'71','AFC.110 - Education_ Indiv - Diabetes',70,'','',0,0,0),(31506285,5696523,'72','AFC.111 - Education_ Indiv - Diet',71,'','',0,0,0),(31506288,5696523,'73','AFC.112 - Education_ Indiv - Hl',72,'','',0,0,0),(31506291,5696523,'74','AFC.113 - Education_ Indiv - Medication',73,'','',0,0,0),(31506294,5696523,'75','AFC.114 - Education_ Indiv - Other',74,'','',0,0,0),(31506297,5696523,'76','AFC.115 - Education_ Indiv - Self Breast Exam',75,'','',0,0,0),(31506300,5696523,'77','AFC.116 - Endocrine - Diabetes',76,'','',0,0,0),(31506303,5696523,'78','AFC.117 - Endocrine - Other',77,'','',0,0,0),(31506306,5696523,'79','AFC.118 - ENT',78,'','',0,0,0),(31506309,5696523,'80','AFC.119 - Eye',79,'','',0,0,0),(31506312,5696523,'81','AFC.120 - Gastrointestinal',80,'','',0,0,0),(31506315,5696523,'82','AFC.121 - Gyn',81,'','',0,0,0),(31506318,5696523,'83','AFC.122 - Hematologic',82,'','',0,0,0),(31506321,5696523,'84','AFC.123 - Mental Health',83,'','',0,0,0),(31506324,5696523,'85','AFC.124 - Musculoskeletal',84,'','',0,0,0),(31506327,5696523,'86','AFC.125 - Nephrology',85,'','',0,0,0),(31506330,5696523,'87','AFC.126 - Neurologic',86,'','',0,0,0),(31506333,5696523,'88','AFC.127 - Oncology',87,'','',0,0,0),(31506336,5696523,'89','AFC.128 - Physical Therapy - Assessment',88,'','',0,0,0),(31506339,5696523,'90','AFC.129 - Physical Therapy - Follow-up',89,'','',0,0,0),(31506342,5696523,'91','AFC.130 - Podiatry',90,'','',0,0,0),(31506345,5696523,'92','AFC.131 - Respiratory - Asthma',91,'','',0,0,0),(31506348,5696523,'93','AFC.132 - Respiratory - Other',92,'','',0,0,0),(31506351,5696523,'94','AFC.133 - Urologic',93,'','',0,0,0),(31564268,54,'7','Sunday',7,'','',1,0,0),(31692911,100,'1','Staff',1,'','',1,0,0),(31692918,100,'2','Clinic Admin',2,'','',1,0,0),(31692925,100,'3','Supervisor',3,'','',1,0,0),(31797755,67,'8','Other (Lab Summary)',22,'','',1,0,0),(31882005,100,'4','Registrar',4,'','',1,0,0),(32020360,88,'6','Suburban',6,'','',1,0,0),(32054168,21,'7','Other',0,'','',1,0,0),(32084660,90,'14','Plastic Surgery',14,'','',1,0,0),(32092567,69,'19','Anne Arundel',19,'','',1,0,0),(32092574,69,'20','Alexandria',20,'','',1,0,0),(32107326,87,'35','Thyroid Simulating Hormone (84443)',34,'','',1,0,0),(32107335,87,'36','EKG with Interpretation (93042)',35,'','',1,0,0),(32107345,87,'37','Lipid Panel (80061)',36,'','',1,0,0),(32107354,87,'38','Thyroid Function Panel  (84463/84439)',37,'','',1,0,0),(32107363,87,'39','Liver Function Test (82247/82248/84075/84450/84460)',38,'','',1,0,0),(32107377,87,'40','Echocardiogram (93307)',40,'','',1,0,0),(32272005,101,'1','',1,'','',1,0,0),(32272012,101,'2','Yesterday',2,'','',1,0,0),(32272019,101,'3','Today',3,'','',1,0,0),(32272026,101,'4','This Week',4,'','',1,0,0),(32272033,101,'5','Next Week',5,'','',1,0,0);
UNLOCK TABLES;
/*!40000 ALTER TABLE `enumeration_value` ENABLE KEYS */;

--
-- Table structure for table `enumeration_value_practice`
--

DROP TABLE IF EXISTS `enumeration_value_practice`;
CREATE TABLE `enumeration_value_practice` (
  `enumeration_value_id` int(11) NOT NULL default '0',
  `practice_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`enumeration_value_id`,`practice_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `enumeration_value_practice`
--


/*!40000 ALTER TABLE `enumeration_value_practice` DISABLE KEYS */;
LOCK TABLES `enumeration_value_practice` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `enumeration_value_practice` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `eob_adjustment`
--


/*!40000 ALTER TABLE `eob_adjustment` DISABLE KEYS */;
LOCK TABLES `eob_adjustment` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `eob_adjustment` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `event`
--


/*!40000 ALTER TABLE `event` DISABLE KEYS */;
LOCK TABLES `event` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `event` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `event_group`
--


/*!40000 ALTER TABLE `event_group` DISABLE KEYS */;
LOCK TABLES `event_group` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `event_group` ENABLE KEYS */;

--
-- Table structure for table `facility_codes`
--

DROP TABLE IF EXISTS `facility_codes`;
CREATE TABLE `facility_codes` (
  `facility_code_id` int(11) NOT NULL,
  `code` varchar(5) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`facility_code_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='Stores x12 facility_code code/human name combos';

--
-- Dumping data for table `facility_codes`
--


/*!40000 ALTER TABLE `facility_codes` DISABLE KEYS */;
LOCK TABLES `facility_codes` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `facility_codes` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='An address that can be for a company or a person';

--
-- Dumping data for table `fbaddress`
--


/*!40000 ALTER TABLE `fbaddress` DISABLE KEYS */;
LOCK TABLES `fbaddress` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `fbaddress` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fbclaim`
--


/*!40000 ALTER TABLE `fbclaim` DISABLE KEYS */;
LOCK TABLES `fbclaim` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `fbclaim` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fbclaimline`
--


/*!40000 ALTER TABLE `fbclaimline` DISABLE KEYS */;
LOCK TABLES `fbclaimline` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `fbclaimline` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='Base Company record most of the data is in linked tables';

--
-- Dumping data for table `fbcompany`
--


/*!40000 ALTER TABLE `fbcompany` DISABLE KEYS */;
LOCK TABLES `fbcompany` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `fbcompany` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fbdiagnoses`
--


/*!40000 ALTER TABLE `fbdiagnoses` DISABLE KEYS */;
LOCK TABLES `fbdiagnoses` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `fbdiagnoses` ENABLE KEYS */;

--
-- Table structure for table `fblatest_revision`
--

DROP TABLE IF EXISTS `fblatest_revision`;
CREATE TABLE `fblatest_revision` (
  `claim_identifier` varchar(255) NOT NULL default '',
  `revision` int(11) NOT NULL default '0',
  PRIMARY KEY  (`claim_identifier`),
  KEY `revision` (`revision`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fblatest_revision`
--


/*!40000 ALTER TABLE `fblatest_revision` DISABLE KEYS */;
LOCK TABLES `fblatest_revision` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `fblatest_revision` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='A person in the system';

--
-- Dumping data for table `fbperson`
--


/*!40000 ALTER TABLE `fbperson` DISABLE KEYS */;
LOCK TABLES `fbperson` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `fbperson` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fbpractice`
--


/*!40000 ALTER TABLE `fbpractice` DISABLE KEYS */;
LOCK TABLES `fbpractice` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `fbpractice` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fbqueue`
--


/*!40000 ALTER TABLE `fbqueue` DISABLE KEYS */;
LOCK TABLES `fbqueue` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `fbqueue` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fee_schedule`
--


/*!40000 ALTER TABLE `fee_schedule` DISABLE KEYS */;
LOCK TABLES `fee_schedule` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `fee_schedule` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fee_schedule_data`
--


/*!40000 ALTER TABLE `fee_schedule_data` DISABLE KEYS */;
LOCK TABLES `fee_schedule_data` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `fee_schedule_data` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fee_schedule_data_modifier`
--


/*!40000 ALTER TABLE `fee_schedule_data_modifier` DISABLE KEYS */;
LOCK TABLES `fee_schedule_data_modifier` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `fee_schedule_data_modifier` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fee_schedule_discount`
--


/*!40000 ALTER TABLE `fee_schedule_discount` DISABLE KEYS */;
LOCK TABLES `fee_schedule_discount` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `fee_schedule_discount` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fee_schedule_discount_by_code`
--


/*!40000 ALTER TABLE `fee_schedule_discount_by_code` DISABLE KEYS */;
LOCK TABLES `fee_schedule_discount_by_code` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `fee_schedule_discount_by_code` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fee_schedule_discount_income`
--


/*!40000 ALTER TABLE `fee_schedule_discount_income` DISABLE KEYS */;
LOCK TABLES `fee_schedule_discount_income` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `fee_schedule_discount_income` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fee_schedule_discount_level`
--


/*!40000 ALTER TABLE `fee_schedule_discount_level` DISABLE KEYS */;
LOCK TABLES `fee_schedule_discount_level` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `fee_schedule_discount_level` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fee_schedule_revision`
--


/*!40000 ALTER TABLE `fee_schedule_revision` DISABLE KEYS */;
LOCK TABLES `fee_schedule_revision` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `fee_schedule_revision` ENABLE KEYS */;

--
-- Table structure for table `financial_link`
--

DROP TABLE IF EXISTS `financial_link`;
CREATE TABLE `financial_link` (
  `oldId` int(11) NOT NULL,
  `newPaymentId` int(11) NOT NULL,
  `newChargeId` int(11) NOT NULL,
  PRIMARY KEY  (`oldId`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `financial_link`
--


/*!40000 ALTER TABLE `financial_link` DISABLE KEYS */;
LOCK TABLES `financial_link` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `financial_link` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `folders`
--


/*!40000 ALTER TABLE `folders` DISABLE KEYS */;
LOCK TABLES `folders` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `folders` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='Contains the EMR extending forms STARTWITHDATA';

--
-- Dumping data for table `form`
--


/*!40000 ALTER TABLE `form` DISABLE KEYS */;
LOCK TABLES `form` WRITE;

INSERT INTO `form` (`form_id`, `name`, `description`, `system_name`) VALUES (1, 'Physical Exam', 'Basic physical health assessment.', 'physical_exam'),
(2, 'Subjective', 'Basic free text subjective assessment', 'subjective'),
(3, 'Objective', 'Basic vitals and objective data collection', 'objective'),
(4, 'Assessment', 'Basic free text assessment ', 'assessment'),
(5, 'Plan', 'Combined display of encounter linked labs, referrals, and free text plan note', 'plan'),
(6, 'Immunizations', 'Basic immunization record', 'immunizations'),
(7, 'Social History', 'Simple history of drug/alcohol/smoking use', 'social_history'),
(8, 'Family History Of Disease', 'Simple record of family history', 'family_history_of_disease'),
(9, 'Previous Illness', 'Simple record of prior conditions and treatments', 'previous_illness'),
(10, 'Allergies', 'Allergies', 'allergies'),
(11, 'Simple Medications', 'Simple medications record', 'simple_meds'),
(12, 'Chronic Care', 'Quicklist system form for Chronic Care list', 'problem_planned_care'),
(13, 'Risk Factors', 'Quicklist system form for risk factors', 'risk_factors'),
(14, 'Self Management Goals', 'System form for selfmgmt criticals display', 'self_management_goals'),
(15, 'Referral Completion Form', 'Sample referral completion with labs and coding', 'referral_completion_form'),
(16, 'Adhoc Referral Completion', 'Sample adhoc referral completion with labs and coding', 'adhoc_referral_completion');


UNLOCK TABLES;
/*!40000 ALTER TABLE `form` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='Links in the form data STARTWITHDATA';

--
-- Dumping data for table `form_data`
--


/*!40000 ALTER TABLE `form_data` DISABLE KEYS */;
LOCK TABLES `form_data` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `form_data` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `form_rule`
--


/*!40000 ALTER TABLE `form_rule` DISABLE KEYS */;
LOCK TABLES `form_rule` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `form_rule` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `form_structure`
--


/*!40000 ALTER TABLE `form_structure` DISABLE KEYS */;
LOCK TABLES `form_structure` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `form_structure` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='ACL Table';

--
-- Dumping data for table `gacl_acl`
--


/*!40000 ALTER TABLE `gacl_acl` DISABLE KEYS */;
LOCK TABLES `gacl_acl` WRITE;
INSERT INTO `gacl_acl` VALUES (2,'system',1,1,'','',1187152896);
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_acl` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_acl_sections`
--


/*!40000 ALTER TABLE `gacl_acl_sections` DISABLE KEYS */;
LOCK TABLES `gacl_acl_sections` WRITE;
INSERT INTO `gacl_acl_sections` VALUES (1,'system',0,'System',0);
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_acl_sections` ENABLE KEYS */;

--
-- Table structure for table `gacl_acl_seq`
--

DROP TABLE IF EXISTS `gacl_acl_seq`;
CREATE TABLE `gacl_acl_seq` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_acl_seq`
--


/*!40000 ALTER TABLE `gacl_acl_seq` DISABLE KEYS */;
LOCK TABLES `gacl_acl_seq` WRITE;
INSERT INTO `gacl_acl_seq` VALUES (2);
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_acl_seq` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_aco`
--


/*!40000 ALTER TABLE `gacl_aco` DISABLE KEYS */;
LOCK TABLES `gacl_aco` WRITE;
INSERT INTO `gacl_aco` VALUES (1,'actions','view',1,'view',0),(2,'actions','edit',2,'edit',0),(3,'actions','add',3,'add',0),(4,'actions','delete',4,'delete',0),(5,'actions','usage',5,'usage',0),(6,'actions','uploadFile',6,'Upload A file',0),(7,'actions','delete_owner',7,'Delete Owner',0),(8,'actions','edit_owner',8,'Edit Owner',0),(9,'actions','double_book',9,'Double Book Appointment',0),(10,'actions','override',10,'override',0),(11,'actions','list',11,'list',0);
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_aco` ENABLE KEYS */;

--
-- Table structure for table `gacl_aco_map`
--

DROP TABLE IF EXISTS `gacl_aco_map`;
CREATE TABLE `gacl_aco_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_aco_map`
--


/*!40000 ALTER TABLE `gacl_aco_map` DISABLE KEYS */;
LOCK TABLES `gacl_aco_map` WRITE;
INSERT INTO `gacl_aco_map` VALUES (2,'actions','add'),(2,'actions','delete'),(2,'actions','delete_owner'),(2,'actions','double_book'),(2,'actions','edit'),(2,'actions','edit_owner'),(2,'actions','list'),(2,'actions','override'),(2,'actions','uploadFile'),(2,'actions','usage'),(2,'actions','view');
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_aco_map` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_aco_sections`
--


/*!40000 ALTER TABLE `gacl_aco_sections` DISABLE KEYS */;
LOCK TABLES `gacl_aco_sections` WRITE;
INSERT INTO `gacl_aco_sections` VALUES (1,'actions',0,'Actions',0);
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_aco_sections` ENABLE KEYS */;

--
-- Table structure for table `gacl_aco_sections_seq`
--

DROP TABLE IF EXISTS `gacl_aco_sections_seq`;
CREATE TABLE `gacl_aco_sections_seq` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_aco_sections_seq`
--


/*!40000 ALTER TABLE `gacl_aco_sections_seq` DISABLE KEYS */;
LOCK TABLES `gacl_aco_sections_seq` WRITE;
INSERT INTO `gacl_aco_sections_seq` VALUES (1);
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_aco_sections_seq` ENABLE KEYS */;

--
-- Table structure for table `gacl_aco_seq`
--

DROP TABLE IF EXISTS `gacl_aco_seq`;
CREATE TABLE `gacl_aco_seq` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_aco_seq`
--


/*!40000 ALTER TABLE `gacl_aco_seq` DISABLE KEYS */;
LOCK TABLES `gacl_aco_seq` WRITE;
INSERT INTO `gacl_aco_seq` VALUES (11);
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_aco_seq` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_aro`
--


/*!40000 ALTER TABLE `gacl_aro` DISABLE KEYS */;
LOCK TABLES `gacl_aro` WRITE;
INSERT INTO `gacl_aro` VALUES (1,'users','admin',1,'admin',0);
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_aro` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_aro_groups`
--


/*!40000 ALTER TABLE `gacl_aro_groups` DISABLE KEYS */;
LOCK TABLES `gacl_aro_groups` WRITE;
INSERT INTO `gacl_aro_groups` VALUES (2,0,1,12,'Roles','roles'),(3,2,2,3,'System Admin','superadmin'),(6,2,4,5,'Provider','role_provider'),(7,2,6,7,'Front Office','front_office'),(8,2,8,9,'Billing User','billing_user'),(9,2,10,11,'Staff','staff');
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_aro_groups` ENABLE KEYS */;

--
-- Table structure for table `gacl_aro_groups_id_seq`
--

DROP TABLE IF EXISTS `gacl_aro_groups_id_seq`;
CREATE TABLE `gacl_aro_groups_id_seq` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_aro_groups_id_seq`
--


/*!40000 ALTER TABLE `gacl_aro_groups_id_seq` DISABLE KEYS */;
LOCK TABLES `gacl_aro_groups_id_seq` WRITE;
INSERT INTO `gacl_aro_groups_id_seq` VALUES (10);
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_aro_groups_id_seq` ENABLE KEYS */;

--
-- Table structure for table `gacl_aro_groups_map`
--

DROP TABLE IF EXISTS `gacl_aro_groups_map`;
CREATE TABLE `gacl_aro_groups_map` (
  `acl_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acl_id`,`group_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_aro_groups_map`
--


/*!40000 ALTER TABLE `gacl_aro_groups_map` DISABLE KEYS */;
LOCK TABLES `gacl_aro_groups_map` WRITE;
INSERT INTO `gacl_aro_groups_map` VALUES (2,3);
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_aro_groups_map` ENABLE KEYS */;

--
-- Table structure for table `gacl_aro_map`
--

DROP TABLE IF EXISTS `gacl_aro_map`;
CREATE TABLE `gacl_aro_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_aro_map`
--


/*!40000 ALTER TABLE `gacl_aro_map` DISABLE KEYS */;
LOCK TABLES `gacl_aro_map` WRITE;
INSERT INTO `gacl_aro_map` VALUES (2,'users','admin');
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_aro_map` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_aro_sections`
--


/*!40000 ALTER TABLE `gacl_aro_sections` DISABLE KEYS */;
LOCK TABLES `gacl_aro_sections` WRITE;
INSERT INTO `gacl_aro_sections` VALUES (1,'users',1,'Users',0);
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_aro_sections` ENABLE KEYS */;

--
-- Table structure for table `gacl_aro_sections_seq`
--

DROP TABLE IF EXISTS `gacl_aro_sections_seq`;
CREATE TABLE `gacl_aro_sections_seq` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_aro_sections_seq`
--


/*!40000 ALTER TABLE `gacl_aro_sections_seq` DISABLE KEYS */;
LOCK TABLES `gacl_aro_sections_seq` WRITE;
INSERT INTO `gacl_aro_sections_seq` VALUES (1);
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_aro_sections_seq` ENABLE KEYS */;

--
-- Table structure for table `gacl_aro_seq`
--

DROP TABLE IF EXISTS `gacl_aro_seq`;
CREATE TABLE `gacl_aro_seq` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_aro_seq`
--


/*!40000 ALTER TABLE `gacl_aro_seq` DISABLE KEYS */;
LOCK TABLES `gacl_aro_seq` WRITE;
INSERT INTO `gacl_aro_seq` VALUES (1);
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_aro_seq` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_axo`
--


/*!40000 ALTER TABLE `gacl_axo` DISABLE KEYS */;
LOCK TABLES `gacl_axo` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_axo` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_axo_groups`
--


/*!40000 ALTER TABLE `gacl_axo_groups` DISABLE KEYS */;
LOCK TABLES `gacl_axo_groups` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_axo_groups` ENABLE KEYS */;

--
-- Table structure for table `gacl_axo_groups_id_seq`
--

DROP TABLE IF EXISTS `gacl_axo_groups_id_seq`;
CREATE TABLE `gacl_axo_groups_id_seq` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_axo_groups_id_seq`
--


/*!40000 ALTER TABLE `gacl_axo_groups_id_seq` DISABLE KEYS */;
LOCK TABLES `gacl_axo_groups_id_seq` WRITE;
INSERT INTO `gacl_axo_groups_id_seq` VALUES (1);
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_axo_groups_id_seq` ENABLE KEYS */;

--
-- Table structure for table `gacl_axo_groups_map`
--

DROP TABLE IF EXISTS `gacl_axo_groups_map`;
CREATE TABLE `gacl_axo_groups_map` (
  `acl_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acl_id`,`group_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_axo_groups_map`
--


/*!40000 ALTER TABLE `gacl_axo_groups_map` DISABLE KEYS */;
LOCK TABLES `gacl_axo_groups_map` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_axo_groups_map` ENABLE KEYS */;

--
-- Table structure for table `gacl_axo_map`
--

DROP TABLE IF EXISTS `gacl_axo_map`;
CREATE TABLE `gacl_axo_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_axo_map`
--


/*!40000 ALTER TABLE `gacl_axo_map` DISABLE KEYS */;
LOCK TABLES `gacl_axo_map` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_axo_map` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_axo_sections`
--


/*!40000 ALTER TABLE `gacl_axo_sections` DISABLE KEYS */;
LOCK TABLES `gacl_axo_sections` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_axo_sections` ENABLE KEYS */;

--
-- Table structure for table `gacl_axo_sections_seq`
--

DROP TABLE IF EXISTS `gacl_axo_sections_seq`;
CREATE TABLE `gacl_axo_sections_seq` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_axo_sections_seq`
--


/*!40000 ALTER TABLE `gacl_axo_sections_seq` DISABLE KEYS */;
LOCK TABLES `gacl_axo_sections_seq` WRITE;
INSERT INTO `gacl_axo_sections_seq` VALUES (1);
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_axo_sections_seq` ENABLE KEYS */;

--
-- Table structure for table `gacl_axo_seq`
--

DROP TABLE IF EXISTS `gacl_axo_seq`;
CREATE TABLE `gacl_axo_seq` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_axo_seq`
--


/*!40000 ALTER TABLE `gacl_axo_seq` DISABLE KEYS */;
LOCK TABLES `gacl_axo_seq` WRITE;
INSERT INTO `gacl_axo_seq` VALUES (1);
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_axo_seq` ENABLE KEYS */;

--
-- Table structure for table `gacl_groups_aro_map`
--

DROP TABLE IF EXISTS `gacl_groups_aro_map`;
CREATE TABLE `gacl_groups_aro_map` (
  `group_id` int(11) NOT NULL default '0',
  `aro_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`aro_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_groups_aro_map`
--


/*!40000 ALTER TABLE `gacl_groups_aro_map` DISABLE KEYS */;
LOCK TABLES `gacl_groups_aro_map` WRITE;
INSERT INTO `gacl_groups_aro_map` VALUES (3,1);
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_groups_aro_map` ENABLE KEYS */;

--
-- Table structure for table `gacl_groups_axo_map`
--

DROP TABLE IF EXISTS `gacl_groups_axo_map`;
CREATE TABLE `gacl_groups_axo_map` (
  `group_id` int(11) NOT NULL default '0',
  `axo_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`axo_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_groups_axo_map`
--


/*!40000 ALTER TABLE `gacl_groups_axo_map` DISABLE KEYS */;
LOCK TABLES `gacl_groups_axo_map` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_groups_axo_map` ENABLE KEYS */;

--
-- Table structure for table `gacl_phpgacl`
--

DROP TABLE IF EXISTS `gacl_phpgacl`;
CREATE TABLE `gacl_phpgacl` (
  `name` varchar(230) NOT NULL default '',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`name`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_phpgacl`
--


/*!40000 ALTER TABLE `gacl_phpgacl` DISABLE KEYS */;
LOCK TABLES `gacl_phpgacl` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_phpgacl` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `generic_notes`
--


/*!40000 ALTER TABLE `generic_notes` DISABLE KEYS */;
LOCK TABLES `generic_notes` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `generic_notes` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `group_occurence`
--


/*!40000 ALTER TABLE `group_occurence` DISABLE KEYS */;
LOCK TABLES `group_occurence` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `group_occurence` ENABLE KEYS */;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `groups`
--


/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
LOCK TABLES `groups` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;

--
-- Table structure for table `hl7_message`
--

DROP TABLE IF EXISTS `hl7_message`;
CREATE TABLE `hl7_message` (
  `hl7_message_id` int(11) NOT NULL default '0',
  `type` tinyint(4) NOT NULL,
  `control_id` varchar(50) NOT NULL default '',
  `message` longtext NOT NULL,
  `processed` tinyint(4) NOT NULL,
  PRIMARY KEY  (`hl7_message_id`),
  KEY `control_id` (`control_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;


--
-- Dumping data for table `hl7_message`
--


/*!40000 ALTER TABLE `hl7_message` DISABLE KEYS */;
LOCK TABLES `hl7_message` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `hl7_message` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `identifier`
--


/*!40000 ALTER TABLE `identifier` DISABLE KEYS */;
LOCK TABLES `identifier` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `identifier` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `import_map`
--


/*!40000 ALTER TABLE `import_map` DISABLE KEYS */;
LOCK TABLES `import_map` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `import_map` ENABLE KEYS */;

--
-- Table structure for table `ins_link`
--

DROP TABLE IF EXISTS `ins_link`;
CREATE TABLE `ins_link` (
  `oldId` varchar(50) NOT NULL,
  `newId` int(11) NOT NULL,
  PRIMARY KEY  (`oldId`,`newId`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ins_link`
--


/*!40000 ALTER TABLE `ins_link` DISABLE KEYS */;
LOCK TABLES `ins_link` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `ins_link` ENABLE KEYS */;

--
-- Table structure for table `insurance`
--

DROP TABLE IF EXISTS `insurance`;
CREATE TABLE `insurance` (
  `company_id` int(11) NOT NULL default '0',
  `fee_schedule_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`),
  KEY `fee_schedule_id` (`fee_schedule_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `insurance`
--


/*!40000 ALTER TABLE `insurance` DISABLE KEYS */;
LOCK TABLES `insurance` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `insurance` ENABLE KEYS */;

--
-- Table structure for table `insurance_payergroup`
--

DROP TABLE IF EXISTS `insurance_payergroup`;
CREATE TABLE `insurance_payergroup` (
  `payer_group_id` int(11) NOT NULL default '0',
  `insurance_program_id` int(11) NOT NULL default '0',
  `order` int(11) NOT NULL default '0',
  KEY `payer_group_id` (`payer_group_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `insurance_payergroup`
--


/*!40000 ALTER TABLE `insurance_payergroup` DISABLE KEYS */;
LOCK TABLES `insurance_payergroup` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `insurance_payergroup` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `insurance_program`
--


/*!40000 ALTER TABLE `insurance_program` DISABLE KEYS */;
LOCK TABLES `insurance_program` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `insurance_program` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `insured_relationship`
--


/*!40000 ALTER TABLE `insured_relationship` DISABLE KEYS */;
LOCK TABLES `insured_relationship` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `insured_relationship` ENABLE KEYS */;

--
-- Table structure for table `lab_note`
--

DROP TABLE IF EXISTS `lab_note`;
CREATE TABLE `lab_note` (
  `lab_note_id` int(11) NOT NULL default '0',
  `lab_test_id` int(11) NOT NULL default '0',
  `note` text NOT NULL,
  PRIMARY KEY  (`lab_note_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lab_note`
--


/*!40000 ALTER TABLE `lab_note` DISABLE KEYS */;
LOCK TABLES `lab_note` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `lab_note` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lab_order`
--


/*!40000 ALTER TABLE `lab_order` DISABLE KEYS */;
LOCK TABLES `lab_order` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `lab_order` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lab_result`
--


/*!40000 ALTER TABLE `lab_result` DISABLE KEYS */;
LOCK TABLES `lab_result` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `lab_result` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lab_test`
--


/*!40000 ALTER TABLE `lab_test` DISABLE KEYS */;
LOCK TABLES `lab_test` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `lab_test` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `link`
--


/*!40000 ALTER TABLE `link` DISABLE KEYS */;
LOCK TABLES `link` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `link` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `meds_bulk_quantity`
--


/*!40000 ALTER TABLE `meds_bulk_quantity` DISABLE KEYS */;
LOCK TABLES `meds_bulk_quantity` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `meds_bulk_quantity` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `meds_case`
--


/*!40000 ALTER TABLE `meds_case` DISABLE KEYS */;
LOCK TABLES `meds_case` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `meds_case` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `meds_inventory_item`
--


/*!40000 ALTER TABLE `meds_inventory_item` DISABLE KEYS */;
LOCK TABLES `meds_inventory_item` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `meds_inventory_item` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `meds_inventory_item_price`
--


/*!40000 ALTER TABLE `meds_inventory_item_price` DISABLE KEYS */;
LOCK TABLES `meds_inventory_item_price` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `meds_inventory_item_price` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `meds_inventory_item_status`
--


/*!40000 ALTER TABLE `meds_inventory_item_status` DISABLE KEYS */;
LOCK TABLES `meds_inventory_item_status` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `meds_inventory_item_status` ENABLE KEYS */;

--
-- Table structure for table `meds_item_to_location`
--

DROP TABLE IF EXISTS `meds_item_to_location`;
CREATE TABLE `meds_item_to_location` (
  `meds_item_to_location_id` int(11) NOT NULL default '0',
  `meds_inventory_item_id` int(11) NOT NULL default '0',
  `building_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`meds_item_to_location_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `meds_item_to_location`
--


/*!40000 ALTER TABLE `meds_item_to_location` DISABLE KEYS */;
LOCK TABLES `meds_item_to_location` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `meds_item_to_location` ENABLE KEYS */;

--
-- Table structure for table `meds_item_to_program`
--

DROP TABLE IF EXISTS `meds_item_to_program`;
CREATE TABLE `meds_item_to_program` (
  `meds_item_to_program_id` int(11) NOT NULL default '0',
  `meds_inventory_item_id` int(11) NOT NULL default '0',
  `insurance_program_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`meds_item_to_program_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `meds_item_to_program`
--


/*!40000 ALTER TABLE `meds_item_to_program` DISABLE KEYS */;
LOCK TABLES `meds_item_to_program` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `meds_item_to_program` ENABLE KEYS */;

--
-- Table structure for table `meds_program`
--

DROP TABLE IF EXISTS `meds_program`;
CREATE TABLE `meds_program` (
  `meds_program_id` int(11) NOT NULL default '0'
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `meds_program`
--


/*!40000 ALTER TABLE `meds_program` DISABLE KEYS */;
LOCK TABLES `meds_program` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `meds_program` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `meds_unit_of_use`
--


/*!40000 ALTER TABLE `meds_unit_of_use` DISABLE KEYS */;
LOCK TABLES `meds_unit_of_use` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `meds_unit_of_use` ENABLE KEYS */;

--
-- Table structure for table `meds_unit_of_use_warning`
--

DROP TABLE IF EXISTS `meds_unit_of_use_warning`;
CREATE TABLE `meds_unit_of_use_warning` (
  `meds_unit_of_use_warning_id` int(11) NOT NULL default '0',
  `meds_unit_of_use_id` int(11) NOT NULL default '0',
  `warning` int(11) default NULL,
  PRIMARY KEY  (`meds_unit_of_use_warning_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `meds_unit_of_use_warning`
--


/*!40000 ALTER TABLE `meds_unit_of_use_warning` DISABLE KEYS */;
LOCK TABLES `meds_unit_of_use_warning` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `meds_unit_of_use_warning` ENABLE KEYS */;

--
-- Table structure for table `meds_user_to_program`
--

DROP TABLE IF EXISTS `meds_user_to_program`;
CREATE TABLE `meds_user_to_program` (
  `user_id` int(11) NOT NULL default '0',
  `meds_program_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `meds_user_to_program`
--


/*!40000 ALTER TABLE `meds_user_to_program` DISABLE KEYS */;
LOCK TABLES `meds_user_to_program` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `meds_user_to_program` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `menu`
--


/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
LOCK TABLES `menu` WRITE;
INSERT INTO `menu` VALUES (1,'a',1,'','children',0,'','','main'),(2,'default',1,'','children',10,'Actions','','main'),(3,'default',2,'','children',100,'Add Appointment','javascript:showAddAppointment()','RAW'),(4,'default',2,'','children',200,'Search','Appointment/Search','main'),(5,'default',2,'','children',300,'Filters','javascript:showCalendarFilters()','RAW'),(6,'default',2,'','children',400,'Day','CalendarDisplay/Day','main'),(7,'default',1,'','children',20,'Reports','','main/CalendarDisplay'),(8,'default',1,'','children',30,'Admin','','main'),(9,'default',8,'','children',100,'Schedules','Schedule/list','main'),(10,'default',8,'','children',200,'Templates','AppointmentTemplate/list','main'),(11,'patient',1,'','children',10,'Actions','','main'),(12,'patient',11,'','children',100,'Add Patient','Patient/Add','main'),(13,'patient',11,'','children',200,'Search','PatientFinder/Find','main'),(14,'patient',11,'','children',300,'Dashboard','PatientDashboard/View','main'),(15,'patient',11,'','children',400,'Add Encounter','Encounter/Add','main'),(16,'patient',11,'','children',500,'Documents','Document/List','main'),(17,'patient',1,'','children',20,'Reports','','main/Patient'),(18,'patient',1,'','children',120,'Admin','','main'),(19,'patient',18,'','children',100,'Merge Queue','PatientMerge/List','main'),(20,'billing',1,'','children',10,'Actions','',''),(21,'billing',20,'','children',100,'Claims','Claim/List','main'),(22,'billing',20,'','children',200,'Master Account History','MasterAccountHistory/View','main'),(23,'billing',1,'','children',20,'Reports','','main/Billing'),(24,'billing',1,'','children',30,'Admin','','main'),(25,'billing',24,'','children',100,'Payers','Insurance/List','main'),(26,'billing',24,'','children',200,'Fee Schedules','FeeSchedule/List','main'),(27,'billing',24,'','children',300,'Discount Tables','FeeScheduleDiscount/List','main'),(28,'billing',24,'','children',400,'Superbills','Superbill/List','main'),(29,'billing',24,'','children',500,'Import 835','X12Import/upload','main'),(30,'admin',1,'','children',10,'Calendar','','main'),(31,'admin',30,'','children',100,'Schedules','Schedule/list','main'),(32,'admin',30,'','children',200,'Templates','AppointmentTemplate/list','main'),(33,'admin',1,'','children',20,'Patient','','main'),(34,'admin',33,'','children',100,'Labs','Labs/List','main'),(35,'admin',33,'','children',200,'EMR Plugins','WidgetForm/List','main'),(36,'admin',33,'','children',300,'Document Categories','DocumentCategory/List','main'),(37,'admin',1,'','children',30,'Billing','','main'),(38,'admin',37,'','children',100,'Payers','Insurance/List','main'),(39,'admin',37,'','children',200,'Fee Schedules','FeeSchedule/List','main'),(40,'admin',37,'','children',300,'Discount Tables','FeeScheduleDiscount/List','main'),(41,'admin',37,'','children',400,'Superbills','Superbill/List','main'),(42,'admin',37,'','children',500,'Input 835','X12Import/upload','main'),(43,'admin',1,'','children',40,'Setup','','main'),(44,'admin',43,'','children',100,'Facilities','Location/List','main'),(45,'admin',43,'','children',200,'Users','User/List','main'),(46,'admin',43,'','children',300,'Enumerations','Enumeration/List','main'),(47,'admin',43,'','children',400,'ACL Editor','Admin/Acl','main'),(48,'admin',43,'','children',500,'Timed Events','Cronable/List','main'),(49,'admin',1,'','children',50,'Reports/Forms','','main'),(50,'admin',49,'','children',100,'Reports','Report/List','main'),(51,'admin',49,'','children',200,'Forms','Form/List','main'),(52,'admin',49,'','children',300,'Connect Reports','Report/Connect','main'),(53,'admin',49,'','children',400,'Connect Forms','Form/Connect','main'),(54,'all',1,'','children',5000,'Practice','','main'),(55,'all',1,'','children',400,'My Account','','main'),(56,'all',55,'','children',100,'Change Password','MyAccount/Password','main'),(57,'patient',1,'','children',-1,'Encounter Forms','','main/Encounter'),(58,'patient',1,'','children',-1,'Dashboard Forms','','main/Patient'),(59,'patient',1,'','children',-1,'Dashboard Reports','','main/Patient'),(61,'admin',43,'','children',0,'Building','Building/add','main'),(62,'admin',43,'','children',0,'Room','Room/add','main'),(63,'all',55,'','children',1000,'Logout','Access/logout','main'),(64,'admin',37,'','children',500,'Claim Template','CodingTemplate/list','main'),(65,'admin',30,'','children',100,'Appointment Rules','AppointmentRuleset/list','main'),(66,'default',8,'','children',300,'Appointment Rules','AppointmentRuleset/list','main'),(67,'patient',11,'','children',1000,'View Audit Log','AuditLog/list','main'),(68,'default',8,'','children',1000,'Visit Queue Templates','VisitQueue/ListTemplates','main'),(69,'admin',30,'','children',200,'Visit Queue Templates','VisitQueue/ListTemplates','main'),(70,'billing',24,'','children',600,'Claim Template','CodingTemplate/List','main'),(71,'billing',20,'','children',0,'Process Queues','Queue/process','main'),(72,'billing',20,'','children',0,'Process Queues','Queue/process','main'),(73,'admin',37,'','children',900,'Code Categories','CodeCategory/list','main'),(74,'billing',24,'','children',900,'Code Categories','CodeCategory/list','main'),(75,'admin',37,'','children',900,'Code Categories','CodeCategory/list','main'),(76,'billing',24,'','children',900,'Code Categories','CodeCategory/list','main'),(77,'patient',11,'','children',0,'Edit Patient','Patient/Edit','main'),(78,'default',1,'','children',0,'Reports','CalendarDisplay/report','main'),(79,'patient',1,'','children',0,'Reports','Patient/report','main'),(80,'billing',24,'','children',110,'Payer Groups','PayerGroup/List','main'),(81,'admin',37,'','children',110,'Payer Groups','PayerGroup/List','main'),(82,'default',2,'','children',105,'Add Meeting','javascript:showAddMeeting()','RAW'),(83,'patient',1,'','children',100,'Patient Data','','main'),(84,'patient',83,'','children',100,'Clinical Summary','ClinicalSummary/View','main'),(85,'patient',83,'','children',110,'Problem- Planned Care','','main'),(86,'patient',83,'','children',120,'Encounters','','main'),(87,'patient',83,'','children',130,'Labs','Labs/list','main'),(88,'patient',83,'','children',140,'Medications','','main'),(89,'patient',83,'','children',150,'Referrals','','main'),(90,'patient',83,'','children',160,'Reminders','','main'),(91,'patient',83,'','children',170,'Stored Documents','','main'),(92,'patient',83,'','children',180,'Demographics','Patient/edit','main'),(93,'patient',83,'patient_id','children',190,'Medical History','MedicalHistory/View','main'),(94,'default',2,'','children',500,'Today\'s Calendar','','main'),(95,'default',2,'','children',600,'Print Calendar','','main'),(96,'patient',11,'','children',450,'Search for Encounter','','main'),(97,'admin',49,'','children',600,'Manage Lists','','main'),(98,'admin',49,'','children',700,'Splash Screen Content','','main'),(99,'patient',18,'','children',800,'Manage Referrals','','main'),(100,'admin',18,'','children',900,'Manage Medications','','main'),(101,'patient',1,'','children',110,'Referral','','main'),(102,'patient',101,'','children',10,'Appointments Confirmed','','main'),(103,'patient',101,'','children',20,'Appointments Cancelled','','main'),(104,'patient',101,'','children',40,'Appointments yet to be Confirmed','','main'),(105,'patient',101,'','children',50,'Summary by Specialist/Practice','','main'),(106,'patient',101,'','children',60,'Summary by Specialty','','main'),(107,'patient',101,'','children',30,'Appointments Needed','','main'),(108,'patient',101,'','children',30,'Add/List Referral','Referral/add','main'),(109,'patient',101,'','children',1,'Summary','Refreporting/list','main'),(110,'admin',33,'','children',400,'Participation Programs','ParticipationProgram/list','main');
UNLOCK TABLES;
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `menu_form`
--


/*!40000 ALTER TABLE `menu_form` DISABLE KEYS */;
LOCK TABLES `menu_form` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `menu_form` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `menu_report`
--


/*!40000 ALTER TABLE `menu_report` DISABLE KEYS */;
LOCK TABLES `menu_report` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `menu_report` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `misc_charge`
--


/*!40000 ALTER TABLE `misc_charge` DISABLE KEYS */;
LOCK TABLES `misc_charge` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `misc_charge` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `name_history`
--


/*!40000 ALTER TABLE `name_history` DISABLE KEYS */;
LOCK TABLES `name_history` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `name_history` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `note`
--


/*!40000 ALTER TABLE `note` DISABLE KEYS */;
LOCK TABLES `note` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `note` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notes`
--


/*!40000 ALTER TABLE `notes` DISABLE KEYS */;
LOCK TABLES `notes` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `notes` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='A phone number';

--
-- Dumping data for table `number`
--


/*!40000 ALTER TABLE `number` DISABLE KEYS */;
LOCK TABLES `number` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `number` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `occurence_breakdown`
--


/*!40000 ALTER TABLE `occurence_breakdown` DISABLE KEYS */;
LOCK TABLES `occurence_breakdown` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `occurence_breakdown` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `occurences`
--


/*!40000 ALTER TABLE `occurences` DISABLE KEYS */;
LOCK TABLES `occurences` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `occurences` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ordo_registry`
--


/*!40000 ALTER TABLE `ordo_registry` DISABLE KEYS */;
LOCK TABLES `ordo_registry` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `ordo_registry` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ownership`
--


/*!40000 ALTER TABLE `ownership` DISABLE KEYS */;
LOCK TABLES `ownership` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `ownership` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `participation_program`
--


/*!40000 ALTER TABLE `participation_program` DISABLE KEYS */;
LOCK TABLES `participation_program` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `participation_program` ENABLE KEYS */;

--
-- Table structure for table `participation_program_basic`
--

DROP TABLE IF EXISTS `participation_program_basic`;
CREATE TABLE `participation_program_basic` (
  `person_program_id` bigint(20) NOT NULL default '0',
  `federal_poverty_level` char(3) NOT NULL default '',
  `eligibility` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`person_program_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `participation_program_basic`
--


/*!40000 ALTER TABLE `participation_program_basic` DISABLE KEYS */;
LOCK TABLES `participation_program_basic` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `participation_program_basic` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `participation_program_clinic`
--


/*!40000 ALTER TABLE `participation_program_clinic` DISABLE KEYS */;
LOCK TABLES `participation_program_clinic` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `participation_program_clinic` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='An patient extends the person entity';

--
-- Dumping data for table `patient`
--


/*!40000 ALTER TABLE `patient` DISABLE KEYS */;
LOCK TABLES `patient` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `patient` ENABLE KEYS */;

--
-- Table structure for table `patient_chronic_code`
--

DROP TABLE IF EXISTS `patient_chronic_code`;
CREATE TABLE `patient_chronic_code` (
  `patient_id` int(11) NOT NULL default '0',
  `chronic_care_code` int(11) NOT NULL default '0',
  PRIMARY KEY  (`patient_id`,`chronic_care_code`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `patient_chronic_code`
--


/*!40000 ALTER TABLE `patient_chronic_code` DISABLE KEYS */;
LOCK TABLES `patient_chronic_code` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `patient_chronic_code` ENABLE KEYS */;

--
-- Table structure for table `patient_link`
--

DROP TABLE IF EXISTS `patient_link`;
CREATE TABLE `patient_link` (
  `oldId` int(11) NOT NULL,
  `newId` int(11) NOT NULL,
  PRIMARY KEY  (`oldId`,`newId`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `patient_link`
--


/*!40000 ALTER TABLE `patient_link` DISABLE KEYS */;
LOCK TABLES `patient_link` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `patient_link` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `patient_note`
--


/*!40000 ALTER TABLE `patient_note` DISABLE KEYS */;
LOCK TABLES `patient_note` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `patient_note` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `patient_payment_plan`
--


/*!40000 ALTER TABLE `patient_payment_plan` DISABLE KEYS */;
LOCK TABLES `patient_payment_plan` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `patient_payment_plan` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `patient_payment_plan_payment`
--


/*!40000 ALTER TABLE `patient_payment_plan_payment` DISABLE KEYS */;
LOCK TABLES `patient_payment_plan_payment` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `patient_payment_plan_payment` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `patient_statistics`
--


/*!40000 ALTER TABLE `patient_statistics` DISABLE KEYS */;
LOCK TABLES `patient_statistics` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `patient_statistics` ENABLE KEYS */;

--
-- Table structure for table `payer_group`
--

DROP TABLE IF EXISTS `payer_group`;
CREATE TABLE `payer_group` (
  `payer_group_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`payer_group_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `payer_group`
--


/*!40000 ALTER TABLE `payer_group` DISABLE KEYS */;
LOCK TABLES `payer_group` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `payer_group` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `payment`
--


/*!40000 ALTER TABLE `payment` DISABLE KEYS */;
LOCK TABLES `payment` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `payment` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `payment_claimline`
--


/*!40000 ALTER TABLE `payment_claimline` DISABLE KEYS */;
LOCK TABLES `payment_claimline` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `payment_claimline` ENABLE KEYS */;

--
-- Table structure for table `pccconversion`
--

DROP TABLE IF EXISTS `pccconversion`;
CREATE TABLE `pccconversion` (
  `type` varchar(255) NOT NULL default '',
  `old` int(11) NOT NULL default '0',
  `new` int(11) NOT NULL default '0',
  KEY `type` (`type`,`old`,`new`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pccconversion`
--


/*!40000 ALTER TABLE `pccconversion` DISABLE KEYS */;
LOCK TABLES `pccconversion` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `pccconversion` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='A person in the system';

--
-- Dumping data for table `person`
--


/*!40000 ALTER TABLE `person` DISABLE KEYS */;
LOCK TABLES `person` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `person` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='Links a person to a address specifying the address type';

--
-- Dumping data for table `person_address`
--


/*!40000 ALTER TABLE `person_address` DISABLE KEYS */;
LOCK TABLES `person_address` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `person_address` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='Links a person to a company and optionaly specifies the lin';

--
-- Dumping data for table `person_company`
--


/*!40000 ALTER TABLE `person_company` DISABLE KEYS */;
LOCK TABLES `person_company` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `person_company` ENABLE KEYS */;

--
-- Table structure for table `person_link`
--

DROP TABLE IF EXISTS `person_link`;
CREATE TABLE `person_link` (
  `oldId` int(11) NOT NULL,
  `newId` int(11) NOT NULL,
  PRIMARY KEY  (`oldId`,`newId`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `person_link`
--


/*!40000 ALTER TABLE `person_link` DISABLE KEYS */;
LOCK TABLES `person_link` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `person_link` ENABLE KEYS */;

--
-- Table structure for table `person_number`
--

DROP TABLE IF EXISTS `person_number`;
CREATE TABLE `person_number` (
  `person_id` int(11) NOT NULL default '0',
  `number_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`,`number_id`),
  KEY `phone_id` (`number_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='Links between people and phone_numbers';

--
-- Dumping data for table `person_number`
--


/*!40000 ALTER TABLE `person_number` DISABLE KEYS */;
LOCK TABLES `person_number` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `person_number` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `person_participation_program`
--


/*!40000 ALTER TABLE `person_participation_program` DISABLE KEYS */;
LOCK TABLES `person_participation_program` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `person_participation_program` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `person_person`
--


/*!40000 ALTER TABLE `person_person` DISABLE KEYS */;
LOCK TABLES `person_person` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `person_person` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='Link to specify person type';

--
-- Dumping data for table `person_type`
--


/*!40000 ALTER TABLE `person_type` DISABLE KEYS */;
LOCK TABLES `person_type` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `person_type` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='Links a practice to a address specifying the address type';

--
-- Dumping data for table `practice_address`
--


/*!40000 ALTER TABLE `practice_address` DISABLE KEYS */;
LOCK TABLES `practice_address` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `practice_address` ENABLE KEYS */;

--
-- Table structure for table `practice_link`
--

DROP TABLE IF EXISTS `practice_link`;
CREATE TABLE `practice_link` (
  `oldId` char(100) NOT NULL,
  `newId` int(11) NOT NULL,
  PRIMARY KEY  (`oldId`,`newId`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `practice_link`
--


/*!40000 ALTER TABLE `practice_link` DISABLE KEYS */;
LOCK TABLES `practice_link` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `practice_link` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='Links between people and phone_numbers';

--
-- Dumping data for table `practice_number`
--


/*!40000 ALTER TABLE `practice_number` DISABLE KEYS */;
LOCK TABLES `practice_number` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `practice_number` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `practice_setting`
--


/*!40000 ALTER TABLE `practice_setting` DISABLE KEYS */;
LOCK TABLES `practice_setting` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `practice_setting` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `practices`
--


/*!40000 ALTER TABLE `practices` DISABLE KEYS */;
LOCK TABLES `practices` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `practices` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `preferences`
--


/*!40000 ALTER TABLE `preferences` DISABLE KEYS */;
LOCK TABLES `preferences` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `preferences` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `provider`
--


/*!40000 ALTER TABLE `provider` DISABLE KEYS */;
LOCK TABLES `provider` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `provider` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `provider_to_insurance`
--


/*!40000 ALTER TABLE `provider_to_insurance` DISABLE KEYS */;
LOCK TABLES `provider_to_insurance` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `provider_to_insurance` ENABLE KEYS */;

--
-- Table structure for table `pull_list`
--

DROP TABLE IF EXISTS `pull_list`;
CREATE TABLE `pull_list` (
  `appointment_id` int(11) NOT NULL default '0',
  `pull_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`appointment_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pull_list`
--


/*!40000 ALTER TABLE `pull_list` DISABLE KEYS */;
LOCK TABLES `pull_list` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `pull_list` ENABLE KEYS */;

--
-- Table structure for table `record_sequence`
--

DROP TABLE IF EXISTS `record_sequence`;
CREATE TABLE `record_sequence` (
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `record_sequence`
--


/*!40000 ALTER TABLE `record_sequence` DISABLE KEYS */;
LOCK TABLES `record_sequence` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `record_sequence` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `recurrence`
--


/*!40000 ALTER TABLE `recurrence` DISABLE KEYS */;
LOCK TABLES `recurrence` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `recurrence` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `recurrence_pattern`
--


/*!40000 ALTER TABLE `recurrence_pattern` DISABLE KEYS */;
LOCK TABLES `recurrence_pattern` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `recurrence_pattern` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `refPracticeLocation`
--


/*!40000 ALTER TABLE `refPracticeLocation` DISABLE KEYS */;
LOCK TABLES `refPracticeLocation` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `refPracticeLocation` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `refRequest`
--


/*!40000 ALTER TABLE `refRequest` DISABLE KEYS */;
LOCK TABLES `refRequest` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `refRequest` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `refSpecialtyMap`
--


/*!40000 ALTER TABLE `refSpecialtyMap` DISABLE KEYS */;
LOCK TABLES `refSpecialtyMap` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `refSpecialtyMap` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `refappointment`
--


/*!40000 ALTER TABLE `refappointment` DISABLE KEYS */;
LOCK TABLES `refappointment` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `refappointment` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `refpatient_eligibility`
--


/*!40000 ALTER TABLE `refpatient_eligibility` DISABLE KEYS */;
LOCK TABLES `refpatient_eligibility` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `refpatient_eligibility` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `refpractice`
--


/*!40000 ALTER TABLE `refpractice` DISABLE KEYS */;
LOCK TABLES `refpractice` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `refpractice` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `refpractice_specialty`
--


/*!40000 ALTER TABLE `refpractice_specialty` DISABLE KEYS */;
LOCK TABLES `refpractice_specialty` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `refpractice_specialty` ENABLE KEYS */;

--
-- Table structure for table `refprogram`
--

DROP TABLE IF EXISTS `refprogram`;
CREATE TABLE `refprogram` (
  `refprogram_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL default '',
  `schema` int(11) NOT NULL default '0',
  PRIMARY KEY  (`refprogram_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `refprogram`
--


/*!40000 ALTER TABLE `refprogram` DISABLE KEYS */;
LOCK TABLES `refprogram` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `refprogram` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `refprogram_member`
--


/*!40000 ALTER TABLE `refprogram_member` DISABLE KEYS */;
LOCK TABLES `refprogram_member` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `refprogram_member` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `refprogram_member_slot`
--


/*!40000 ALTER TABLE `refprogram_member_slot` DISABLE KEYS */;
LOCK TABLES `refprogram_member_slot` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `refprogram_member_slot` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `refprovider`
--


/*!40000 ALTER TABLE `refprovider` DISABLE KEYS */;
LOCK TABLES `refprovider` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `refprovider` ENABLE KEYS */;

--
-- Table structure for table `refreferral_visit`
--

DROP TABLE IF EXISTS `refreferral_visit`;
CREATE TABLE `refreferral_visit` (
  `refreferral_visit_id` int(11) NOT NULL,
  `refappointment_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`refreferral_visit_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `refreferral_visit`
--


/*!40000 ALTER TABLE `refreferral_visit` DISABLE KEYS */;
LOCK TABLES `refreferral_visit` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `refreferral_visit` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `refuser`
--


/*!40000 ALTER TABLE `refuser` DISABLE KEYS */;
LOCK TABLES `refuser` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `refuser` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `relationship`
--


/*!40000 ALTER TABLE `relationship` DISABLE KEYS */;
LOCK TABLES `relationship` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `relationship` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `report_snapshot`
--


/*!40000 ALTER TABLE `report_snapshot` DISABLE KEYS */;
LOCK TABLES `report_snapshot` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `report_snapshot` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='Report templates';

--
-- Dumping data for table `report_templates`
--


/*!40000 ALTER TABLE `report_templates` DISABLE KEYS */;
LOCK TABLES `report_templates` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `report_templates` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='Report definitions TODO: change to Generic Seq';

--
-- Dumping data for table `reports`
--


/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
LOCK TABLES `reports` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `revisions`
--


/*!40000 ALTER TABLE `revisions` DISABLE KEYS */;
LOCK TABLES `revisions` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `revisions` ENABLE KEYS */;

--
-- Table structure for table `revisions_db`
--

DROP TABLE IF EXISTS `revisions_db`;
CREATE TABLE `revisions_db` (
  `revision_id` int(10) unsigned NOT NULL default '0',
  `filedata` blob NOT NULL,
  PRIMARY KEY  (`revision_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `revisions_db`
--


/*!40000 ALTER TABLE `revisions_db` DISABLE KEYS */;
LOCK TABLES `revisions_db` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `revisions_db` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rooms`
--


/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
LOCK TABLES `rooms` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;

--
-- Table structure for table `route_slip`
--

DROP TABLE IF EXISTS `route_slip`;
CREATE TABLE `route_slip` (
  `route_slip_id` int(11) NOT NULL default '0',
  `encounter_id` int(11) NOT NULL default '0',
  `report_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`route_slip_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `route_slip`
--


/*!40000 ALTER TABLE `route_slip` DISABLE KEYS */;
LOCK TABLES `route_slip` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `route_slip` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `schedule`
--


/*!40000 ALTER TABLE `schedule` DISABLE KEYS */;
LOCK TABLES `schedule` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `schedule` ENABLE KEYS */;

--
-- Table structure for table `schedule_event`
--

DROP TABLE IF EXISTS `schedule_event`;
CREATE TABLE `schedule_event` (
  `event_id` int(11) NOT NULL default '0',
  `event_group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`event_id`),
  KEY `event_group_id` (`event_group_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `schedule_event`
--


/*!40000 ALTER TABLE `schedule_event` DISABLE KEYS */;
LOCK TABLES `schedule_event` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `schedule_event` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `secondary_practice`
--


/*!40000 ALTER TABLE `secondary_practice` DISABLE KEYS */;
LOCK TABLES `secondary_practice` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `secondary_practice` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `self_mgmt_goals`
--


/*!40000 ALTER TABLE `self_mgmt_goals` DISABLE KEYS */;
LOCK TABLES `self_mgmt_goals` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `self_mgmt_goals` ENABLE KEYS */;

--
-- Table structure for table `sequences`
--

DROP TABLE IF EXISTS `sequences`;
CREATE TABLE `sequences` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sequences`
--


/*!40000 ALTER TABLE `sequences` DISABLE KEYS */;
LOCK TABLES `sequences` WRITE;
INSERT INTO `sequences` VALUES (1000000);
UNLOCK TABLES;
/*!40000 ALTER TABLE `sequences` ENABLE KEYS */;

--
-- Table structure for table `sequences_daily`
--

DROP TABLE IF EXISTS `sequences_daily`;
CREATE TABLE `sequences_daily` (
  `counter` int(11) NOT NULL default '0',
  `updated_on` date NOT NULL default '0000-00-00'
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sequences_daily`
--


/*!40000 ALTER TABLE `sequences_daily` DISABLE KEYS */;
LOCK TABLES `sequences_daily` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `sequences_daily` ENABLE KEYS */;

--
-- Table structure for table `sequences_named`
--

DROP TABLE IF EXISTS `sequences_named`;
CREATE TABLE `sequences_named` (
  `name` varchar(255) NOT NULL default '',
  `counter` int(11) NOT NULL default '0',
  PRIMARY KEY  (`name`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sequences_named`
--


/*!40000 ALTER TABLE `sequences_named` DISABLE KEYS */;
LOCK TABLES `sequences_named` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `sequences_named` ENABLE KEYS */;

--
-- Table structure for table `splash`
--

DROP TABLE IF EXISTS `splash`;
CREATE TABLE `splash` (
  `splash_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `message` longtext NOT NULL,
  PRIMARY KEY  (`splash_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `splash`
--


/*!40000 ALTER TABLE `splash` DISABLE KEYS */;
LOCK TABLES `splash` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `splash` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `statement_history`
--


/*!40000 ALTER TABLE `statement_history` DISABLE KEYS */;
LOCK TABLES `statement_history` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `statement_history` ENABLE KEYS */;

--
-- Table structure for table `statement_sequence`
--

DROP TABLE IF EXISTS `statement_sequence`;
CREATE TABLE `statement_sequence` (
  `id` int(11) NOT NULL default '0'
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `statement_sequence`
--


/*!40000 ALTER TABLE `statement_sequence` DISABLE KEYS */;
LOCK TABLES `statement_sequence` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `statement_sequence` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `states`
--


/*!40000 ALTER TABLE `states` DISABLE KEYS */;
LOCK TABLES `states` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `states` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `storables`
--


/*!40000 ALTER TABLE `storables` DISABLE KEYS */;
LOCK TABLES `storables` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `storables` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='Generic way to store date values';

--
-- Dumping data for table `storage_date`
--


/*!40000 ALTER TABLE `storage_date` DISABLE KEYS */;
LOCK TABLES `storage_date` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `storage_date` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='Generic way to store integer values (also boolean)';

--
-- Dumping data for table `storage_int`
--


/*!40000 ALTER TABLE `storage_int` DISABLE KEYS */;
LOCK TABLES `storage_int` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `storage_int` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='Generic way to string values';

--
-- Dumping data for table `storage_string`
--


/*!40000 ALTER TABLE `storage_string` DISABLE KEYS */;
LOCK TABLES `storage_string` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `storage_string` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='Generic way to string values';

--
-- Dumping data for table `storage_text`
--


/*!40000 ALTER TABLE `storage_text` DISABLE KEYS */;
LOCK TABLES `storage_text` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `storage_text` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `summary_columns`
--


/*!40000 ALTER TABLE `summary_columns` DISABLE KEYS */;
LOCK TABLES `summary_columns` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `summary_columns` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `superbill`
--


/*!40000 ALTER TABLE `superbill` DISABLE KEYS */;
LOCK TABLES `superbill` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `superbill` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `superbill_data`
--


/*!40000 ALTER TABLE `superbill_data` DISABLE KEYS */;
LOCK TABLES `superbill_data` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `superbill_data` ENABLE KEYS */;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `tag_id` int(10) unsigned NOT NULL,
  `tag` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`tag_id`),
  UNIQUE KEY `tag` (`tag`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tags`
--


/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
LOCK TABLES `tags` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;

--
-- Table structure for table `tags_storables`
--

DROP TABLE IF EXISTS `tags_storables`;
CREATE TABLE `tags_storables` (
  `tag_id` int(10) unsigned NOT NULL default '0',
  `storable_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`tag_id`,`storable_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tags_storables`
--


/*!40000 ALTER TABLE `tags_storables` DISABLE KEYS */;
LOCK TABLES `tags_storables` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `tags_storables` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tree`
--


/*!40000 ALTER TABLE `tree` DISABLE KEYS */;
LOCK TABLES `tree` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `tree` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1 COMMENT='Users in the System';

--
-- Dumping data for table `user`
--


/*!40000 ALTER TABLE `user` DISABLE KEYS */;
LOCK TABLES `user` WRITE;
INSERT INTO `user` VALUES (1,'admin','admin','adm','',NULL,'no',0);
UNLOCK TABLES;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_groups`
--


/*!40000 ALTER TABLE `users_groups` DISABLE KEYS */;
LOCK TABLES `users_groups` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `users_groups` ENABLE KEYS */;

--
-- Table structure for table `visit_queue`
--

DROP TABLE IF EXISTS `visit_queue`;
CREATE TABLE `visit_queue` (
  `visit_queue_id` int(11) NOT NULL default '0',
  `visit_queue_template_id` int(11) NOT NULL default '0',
  `provider_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`visit_queue_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `visit_queue`
--


/*!40000 ALTER TABLE `visit_queue` DISABLE KEYS */;
LOCK TABLES `visit_queue` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `visit_queue` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `visit_queue_reason`
--


/*!40000 ALTER TABLE `visit_queue_reason` DISABLE KEYS */;
LOCK TABLES `visit_queue_reason` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `visit_queue_reason` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `visit_queue_template`
--


/*!40000 ALTER TABLE `visit_queue_template` DISABLE KEYS */;
LOCK TABLES `visit_queue_template` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `visit_queue_template` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `widget_form`
--


/*!40000 ALTER TABLE `widget_form` DISABLE KEYS */;
LOCK TABLES `widget_form` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `widget_form` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `x12imported_data`
--


/*!40000 ALTER TABLE `x12imported_data` DISABLE KEYS */;
LOCK TABLES `x12imported_data` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `x12imported_data` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `x12transaction_data`
--


/*!40000 ALTER TABLE `x12transaction_data` DISABLE KEYS */;
LOCK TABLES `x12transaction_data` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `x12transaction_data` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `x12transaction_history`
--


/*!40000 ALTER TABLE `x12transaction_history` DISABLE KEYS */;
LOCK TABLES `x12transaction_history` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `x12transaction_history` ENABLE KEYS */;

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
) ENGINE=INNODB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `zipcodes`
--


/*!40000 ALTER TABLE `zipcodes` DISABLE KEYS */;
LOCK TABLES `zipcodes` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `zipcodes` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

