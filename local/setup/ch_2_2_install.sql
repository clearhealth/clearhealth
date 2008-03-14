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

INSERT INTO `buildings` (`id`, `description`, `name`, `practice_id`, `identifier`, `facility_code_id`, `phone_number`) VALUES (900079, '', 'Primary Care', 900001, '', 0, '');

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
INSERT INTO `category` VALUES (1, 'Clearhealth', 'Clearhealth', 0, 1, 1);
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
  `modifier` varchar(255) NOT NULL default '0',
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
INSERT INTO `enumeration_definition` (`enumeration_id`, `name`, `title`, `type`) VALUES (1, 'refEligibilitySchema', 'Referral: Eligibility Schema', 'PointToObject'),
(2, 'refRejectionReason', 'Referral Rejection Reason', 'default'),
(3, 'emergency_contact_relationship', 'Emergency Contact Relationship', 'Default'),
(4, 'refUserType', 'Referral: User Type', 'default'),
(5, 'federal_poverty_level', 'federal_poverty_level', 'FPL'),
(6, 'pp_clinic_eligibility', 'PP Clinic eligibility', 'Default'),
(7, 'provider_number_type', 'Provider Number Type', 'Default'),
(8, 'address_type', 'Address Type', 'Default'),
(9, 'assigning', 'Assigning', 'Default'),
(10, 'code_modifier', 'Code Modifier', 'Default'),
(11, 'company_number_type', 'Company Number Type', 'Default'),
(12, 'company_type', 'Company Type', 'Default'),
(13, 'disposition', 'Disposition', 'Default'),
(14, 'encounter_date_type', 'Encounter Date Type', 'Default'),
(15, 'encounter_person_type', 'Encounter Person Type', 'Default'),
(16, 'encounter_value_type', 'Encounter Value Type', 'Default'),
(17, 'ethnicity', 'Ethnicity', 'Default'),
(18, 'gender', 'Gender', 'Default'),
(19, 'system_reports', 'System Reports', 'Url'),
(20, 'group_list', 'File Groups', 'Default'),
(21, 'identifier_type', 'Identifier Type', 'Default'),
(22, 'income', 'Income', 'Default'),
(23, 'language', 'Languages', 'Default'),
(24, 'marital_status', 'Marital Status', 'Default'),
(25, 'migrant_status', 'Migrant Status', 'Default'),
(26, 'number_type', 'Phone Number Type', 'Default'),
(27, 'payer_type', 'Payer Type', 'Default'),
(28, 'payment_type', 'Payment Type', 'Default'),
(29, 'person_to_person_relation_type', 'Person to person relation type', 'Default'),
(30, 'person_type', 'Person Type', 'PersonType'),
(31, 'provider_reporting_type', 'Provider Reporting Type', 'Default'),
(32, 'quality_of_file', 'Quality of File', 'Default'),
(33, 'race', 'Race', 'Default'),
(34, 'relation_of_information_code', 'Relation Of Information Code', 'Default'),
(35, 'state', 'State', 'Default'),
(36, 'subscriber_to_patient', 'Subscriber to patient', 'Default'),
(37, 'chronic_care_codes', 'Chronic Care Codes', 'Default'),
(38, 'funds_source', 'Funds Source', 'Default'),
(39, 'refSpecialty', 'Specialists', 'Default'),
(40, 'refEligibility', 'Referal Eligibility', 'Default'),
(41, 'refRequested_time', 'Referal: Requested Time', 'Default'),
(42, 'days', 'Days of the Week', 'Default'),
(43, 'yesNo', 'Yes or No', 'Default'),
(44, 'refStatus', 'Referral: Status', 'Default'),
(45, 'audit_type', 'Audit Type', 'Default'),
(48, 'yesnounknown', 'YesNoUnknown', 'Default'),
(51, 'active_inactive', 'Active Inactive', 'Default'),
(52, 'subscriber_to_patient_relationship', 'Subscriber To Patient Relationship', 'Default'),
(54, 'days_of_week', 'Days of Week', 'Default'),
(55, 'weeks_of_month', 'Weeks of Month', 'Default'),
(56, 'months_of_year', 'Months of Year', 'Default'),
(57, 'recurrence_pattern_type', 'Recurrence Pattern Type', 'Default'),
(59, 'confidentiality_levels', 'Confidentiality Levels', 'Default'),
(60, 'billing_mode', 'Billing Mode', 'MappedValue'),
(61, 'account_note_type', 'Account Note Type', 'Default'),
(62, 'eob_adjustment_type', 'Eob Adjustment Type', 'MappedValue'),
(63, 'value_type', 'Value Type', 'Default'),
(64, 'confidential_family_planning_codes', 'Confidential family planning codes', 'Default'),
(65, 'confidential_disease_codes', 'Confidential_disease_codes', 'Default'),
(66, 'confidential_family_planning_and_disease_codes', 'Confidential Family Planning and Disease Codes', 'ConfidentialFamilyPlanningAndDisease'),
(67, 'widget_type', 'Widget Type', 'Default'),
(68, 'shelter_type', 'Shelter Type', 'Default'),
(69, 'county', 'County', 'Default'),
(70, 'household_status', 'Household Status', 'Default'),
(71, 'preferred_language', 'Preferred Language', 'Default'),
(72, 'english_proficiency', 'English Proficiency', 'Default'),
(73, 'country_of_origin', 'Country of Origin', 'Default'),
(74, 'religion', 'Religion', 'Default'),
(75, 'employment_status', 'Employment Status', 'Default'),
(76, 'education_level', 'Education Level', 'Default'),
(77, 'us_veteran', 'US Veteran', 'Default'),
(78, 'chronic_care_quicklist', 'Chronic care', 'Default'),
(79, 'insurance_type', 'Insurance type', 'Default'),
(80, 'medication_coverage', 'Medication Coverage', 'Default'),
(81, 'allergies', 'Allergies', 'Tree'),
(82, 'immunizations', 'Immunization Name', 'Default'),
(83, 'previous_illness', 'Previous Illness', 'Default'),
(84, 'family_illness', 'Family Illness', 'Default'),
(85, 'relative', 'Relative', 'Default'),
(86, 'transaction', 'Transaction', 'Default'),
(87, 'lab_manual_description_list', 'Lab Test List', 'Default'),
(88, 'lab_manual_company_list', 'Lab Provider', 'Default'),
(89, 'lab_manual_service_list', 'Lab Manual Service List', 'Default'),
(90, 'referral_service', 'Referral Services', 'Default'),
(91, 'self_mgmt_goals', 'Self Management Goals', 'Tree'),
(92, 'patient_note_reason', 'Patient Note Reason', 'Default'),
(93, 'reason_type', 'Reason Type', 'Default'),
(94, 'encounter_reason', 'Encounter Reason', 'Default'),
(95, 'appointment_reasons', 'Appointment Reason', 'AppointmentReason'),
(96, 'simpleToggle', 'Simple Toggle', 'Default'),
(97, 'reminder_summary_modes', 'Reminder Summary Modes', 'Default'),
(98, 'lab_manual_abnormal_list', 'Lab Manual Abnormal List', 'Default'),
(99, 'dm_group_list', 'Document Manager Group List', 'Default'),
(100, 'clinicadmin_permissions', 'Clinic Admin Permissions', 'Default'),
(101, 'report_time_period', 'Report Time Period', 'Default'),
(102, 'risk_factors_quicklist', 'Risk Factors Quicklist', 'Default');

UNLOCK TABLES;
/*!40000 ALTER TABLE `enumeration_definition` ENABLE KEYS */;

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
INSERT INTO `enumeration_value` (`enumeration_value_id`, `enumeration_id`, `key`, `value`, `sort`, `extra1`, `extra2`, `status`, `depth`, `parent_id`) VALUES (12, 39, '6', 'Allergy Specialist', 0, '', '', 1, 0, 0),
(13, 39, '7', 'Audiology', 1, '', '', 1, 0, 0),
(26, 39, '3', 'Dermatology', 4, '', '', 1, 0, 0),
(248, 39, '4', 'Gastroenterology', 6, '', '', 1, 0, 0),
(249, 39, '5', 'Chiropractic', 3, '', '', 1, 0, 0),
(325, 42, '8', 'Any Day', 8, '', '', 1, 0, 0),
(380, 39, '8', 'General Surgery', 8, '', '', 1, 0, 0),
(381, 39, '9', 'Gynecology', 9, '', '', 1, 0, 0),
(382, 39, '10', 'Hematology', 10, '', '', 1, 0, 0),
(383, 39, '11', 'Immunology', 11, '', '', 1, 0, 0),
(384, 39, '12', 'Infectious Disease', 12, '', '', 1, 0, 0),
(385, 39, '13', 'Neurology', 13, '', '', 1, 0, 0),
(386, 39, '14', 'Ophthalmology', 14, '', '', 1, 0, 0),
(387, 39, '15', 'Orthopedic Surgery', 15, '', '', 1, 0, 0),
(388, 39, '16', 'Orthopedic Shoe Tech', 16, '', '', 1, 0, 0),
(389, 39, '17', 'Otolaryngology', 17, '', '', 1, 0, 0),
(390, 39, '18', 'Pain Management', 18, '', '', 1, 0, 0),
(391, 39, '19', 'Pathology', 19, '', '', 1, 0, 0),
(392, 39, '20', 'Phys Med/Rehab', 20, '', '', 1, 0, 0),
(393, 39, '21', 'Podiatry', 21, '', '', 1, 0, 0),
(394, 39, '22', 'Pulmonology', 22, '', '', 1, 0, 0),
(395, 39, '23', 'Radiation Oncology', 23, '', '', 1, 0, 0),
(396, 39, '24', 'Rheumatology', 24, '', '', 1, 0, 0),
(397, 39, '25', 'Speech Therapy', 25, '', '', 1, 0, 0),
(398, 39, '26', 'Urology', 26, '', '', 1, 0, 0),
(399, 39, '27', 'Vascular Surgery', 27, '', '', 1, 0, 0),
(3934, 5, '1', '100', 1, '15.00', '15.00', 1, 0, 0),
(3935, 5, '2', '110', 2, '20.00', '20.00', 1, 0, 0),
(3936, 5, '3', '120', 3, '20.00', '25.00', 1, 0, 0),
(3937, 5, '4', '130', 4, '20.00', '30.00', 1, 0, 0),
(3938, 5, '5', '140', 5, '20.00', '35.00', 1, 0, 0),
(3939, 5, '6', '150', 6, '20.00', '40.00', 1, 0, 0),
(3940, 5, '7', '160', 7, '20.00', '45.00', 1, 0, 0),
(3941, 5, '8', '170', 8, '20.00', '50.00', 1, 0, 0),
(3942, 5, '9', '175', 9, '20.00', '55.00', 1, 0, 0),
(3943, 5, '10', '180', 10, '25.00', '55.00', 1, 0, 0),
(3944, 5, '11', '190', 11, '25.00', '60.00', 1, 0, 0),
(3945, 5, '12', '200', 12, '25.00', '65.00', 1, 0, 0),
(3946, 5, '13', '210', 13, '25.00', '75.00', 1, 0, 0),
(3947, 5, '14', '220', 14, '25.00', '80.00', 1, 0, 0),
(3948, 5, '15', '230', 15, '25.00', '85.00', 1, 0, 0),
(3949, 5, '16', '240', 16, '25.00', '90.00', 1, 0, 0),
(3950, 5, '17', '250', 17, '25.00', '95.00', 1, 0, 0),
(12325, 39, '28', 'Plastic Surgery', 28, '', '', 1, 0, 0),
(12326, 39, '29', 'Neurological Surgery', 29, '', '', 1, 0, 0),
(12327, 39, '30', 'Retinal Specialist', 30, '', '', 1, 0, 0),
(12328, 39, '31', 'Chinese Medicine', 31, '', '', 1, 0, 0),
(13756, 67, '3', 'Criticals Pallet (QuickList)', 4, '', '', 1, 0, 0),
(25941, 39, '32', 'Oncology', 32, '', '', 1, 0, 0),
(25942, 39, '33', 'Prothesis', 33, '', '', 1, 0, 0),
(33440, 67, '10', 'Disabled', 16, '', '', 1, 0, 0),
(45687, 67, '4', 'Criticals Pallet (Controller)', 7, '', '', 1, 0, 0),
(46756, 67, '5', 'Encounter Tab', 10, '', '', 1, 0, 0),
(49188, 67, '6', 'Other', 13, '', '', 1, 0, 0),
(300467, 8, '2', 'Home', 0, '', '', 1, 0, 0),
(300468, 8, '1', 'Billing', 1, '', '', 1, 0, 0),
(300469, 8, '3', 'Other', 2, '', '', 1, 0, 0),
(300470, 8, '4', 'Main', 3, '', '', 1, 0, 0),
(300471, 8, '5', 'Secondary', 4, '', '', 1, 0, 0),
(300481, 9, '1', 'A - Assigned', 0, '', '', 1, 0, 0),
(300482, 9, '2', 'B - Assigned Lab Services Only', 0, '', '', 1, 0, 0),
(300483, 9, '3', 'C - Not Assigned', 0, '', '', 1, 0, 0),
(300484, 9, '4', 'P - Assignment Refused', 0, '', '', 1, 0, 0),
(300486, 10, '1', 'A0', 0, '', '', 1, 0, 0),
(300487, 10, '2', 'A1', 0, '', '', 1, 0, 0),
(300488, 10, '3', 'A2', 0, '', '', 1, 0, 0),
(300489, 10, '4', 'B1', 0, '', '', 1, 0, 0),
(300490, 10, '5', 'B2', 0, '', '', 1, 0, 0),
(300491, 10, '6', 'C6', 0, '', '', 1, 0, 0),
(300493, 11, '1', 'Primary', 0, '', '', 1, 0, 0),
(300494, 11, '2', 'Fax', 0, '', '', 1, 0, 0),
(300496, 12, '1', 'Insurance', 0, '', '', 1, 0, 0),
(300498, 13, '1', 'New', 0, '', '', 1, 0, 0),
(300499, 13, '2', 'Waiting', 0, '', '', 1, 0, 0),
(300500, 13, '3', 'Compete', 0, '', '', 1, 0, 0),
(300502, 14, '1', 'date_of_death', 0, '', '', 1, 0, 0),
(300503, 14, '2', 'date_last_seen', 0, '', '', 1, 0, 0),
(300504, 14, '3', 'date_of_onset', 0, '', '', 1, 0, 0),
(300505, 14, '4', 'date_of_initial_treatment', 0, '', '', 1, 0, 0),
(300506, 14, '5', 'date_of_cant_work_start', 0, '', '', 1, 0, 0),
(300507, 14, '6', 'date_of_cant_work_end', 0, '', '', 1, 0, 0),
(300508, 14, '7', 'date_of_hospitalization_start', 0, '', '', 1, 0, 0),
(300509, 14, '8', 'date_of_hospitalization_end', 0, '', '', 1, 0, 0),
(300511, 15, '1', 'Referring Provider', 0, '', '', 1, 0, 0),
(300516, 16, '1', 'medicaid_resubmission_code', 0, '', '', 1, 0, 0),
(300517, 16, '2', 'prior_authorization_number', 0, '', '', 1, 0, 0),
(300518, 16, '3', 'auto_accident_state', 0, '', '', 1, 0, 0),
(300519, 16, '4', 'original_reference_number', 0, '', '', 1, 0, 0),
(300520, 16, '5', 'hcfa_10d_comment', 0, '', '', 1, 0, 0),
(300522, 17, '1', 'Hispanic or Latino', 1, '', '', 1, 0, 0),
(300523, 17, '2', 'Blank', 2, '', '', 0, 0, 0),
(300525, 18, '1', 'Male', 0, '', '', 1, 0, 0),
(300526, 19, '1', 'Patient Statement', 0, '/Patient/statement', '', 1, 0, 0),
(300527, 18, '3', 'Unknown', 2, '', '', 1, 0, 0),
(300529, 20, '1', 'All', 0, '', '', 1, 0, 0),
(300530, 20, '2', 'Arizona', 0, '', '', 1, 0, 0),
(300531, 20, '3', 'California', 0, '', '', 1, 0, 0),
(300533, 21, '1', 'SSN', 1, '', '', 1, 0, 0),
(300534, 21, '2', 'EIN', 2, '', '', 1, 0, 0),
(300536, 22, '1', 'Unknown', 0, '', '', 1, 0, 0),
(300537, 22, '2', 'Under 100% of Poverty', 0, '', '', 1, 0, 0),
(300538, 22, '3', '100-200% of Poverty', 0, '', '', 1, 0, 0),
(300539, 22, '4', 'Above 200% of Poverty', 0, '', '', 1, 0, 0),
(300541, 23, '1', 'English', 1, '', '', 1, 0, 0),
(300542, 23, '2', 'Spanish', 1, '', '', 1, 0, 0),
(300543, 23, '3', 'Chinese', 2, '', '', 1, 0, 0),
(300544, 23, '4', 'Japanese', 3, '', '', 1, 0, 0),
(300545, 23, '5', 'Korean', 4, '', '', 1, 0, 0),
(300546, 23, '6', 'Portuguese', 5, '', '', 1, 0, 0),
(300547, 23, '7', 'Russian', 6, '', '', 1, 0, 0),
(300548, 23, '8', 'Sign Language', 7, '', '', 1, 0, 0),
(300549, 23, '9', 'Vietnamese', 8, '', '', 1, 0, 0),
(300550, 23, '10', 'Tagalog', 9, '', '', 1, 0, 0),
(300551, 23, '11', 'Punjabi', 10, '', '', 1, 0, 0),
(300552, 23, '12', 'Hindustani', 11, '', '', 1, 0, 0),
(300553, 23, '13', 'Armenian', 12, '', '', 1, 0, 0),
(300554, 23, '14', 'Arabic', 13, '', '', 1, 0, 0),
(300555, 23, '15', 'Laotian', 14, '', '', 1, 0, 0),
(300556, 23, '16', 'Hmong', 15, '', '', 1, 0, 0),
(300557, 23, '17', 'Cambodian', 16, '', '', 1, 0, 0),
(300558, 23, '18', 'Finnish', 17, '', '', 1, 0, 0),
(300559, 23, '19', 'Other', 18, '', '', 1, 0, 0),
(300561, 24, '1', 'Single', 5, '', '', 1, 0, 0),
(300562, 24, '2', 'Married', 2, '', '', 1, 0, 0),
(300563, 24, '3', 'Separated', 4, '', '', 1, 0, 0),
(300565, 25, '1', 'Migrant Worker', 0, '', '', 1, 0, 0),
(300567, 26, '1', 'Home', 1, '', '', 1, 0, 0),
(300568, 26, '2', 'Mobile', 1, '', '', 1, 0, 0),
(300569, 26, '3', 'Work', 2, '', '', 1, 0, 0),
(300570, 26, '4', 'Emergency', 3, '', '', 1, 0, 0),
(300571, 26, '5', 'Fax', 4, '', '', 1, 0, 0),
(300573, 27, '1', 'medicare', 0, '', '', 1, 0, 0),
(300574, 27, '2', 'champus', 2, '', '', 1, 0, 0),
(300575, 27, '3', 'medical', 3, '', '', 1, 0, 0),
(300576, 27, '4', 'private pay', 4, '', '', 1, 0, 0),
(300577, 27, '5', 'feca', 5, '', '', 1, 0, 0),
(300578, 27, '6', 'medicaid', 6, '', '', 1, 0, 0),
(300579, 27, '7', 'champusva', 7, '', '', 1, 0, 0),
(300580, 27, '8', 'otherhcfa', 8, '', '', 1, 0, 0),
(300581, 27, '9', 'litigation', 9, '', '', 1, 0, 0),
(300583, 28, '1', 'visa', 6, '', '', 0, 0, 0),
(300584, 28, '2', 'mastercard', 5, '', '', 0, 0, 0),
(300585, 28, '3', 'amex', 7, '', '', 0, 0, 0),
(300586, 28, '4', 'check', 8, '', '', 0, 0, 0),
(300587, 28, '5', 'cash', 9, '', '', 0, 0, 0),
(300588, 28, '6', 'remittance', 10, '', '', 0, 0, 0),
(300590, 29, '1', 'Dependant', 1, '', '', 1, 0, 0),
(300591, 29, '2', 'Spouse', 1, '', '', 1, 0, 0),
(300592, 29, '3', 'Grand Parent', 2, '', '', 1, 0, 0),
(300593, 29, '4', 'Other', 3, '', '', 1, 0, 0),
(300595, 30, '1', 'Patient', 0, '0', '', 1, 0, 0),
(300596, 30, '2', 'Provider', 1, '1', '', 1, 0, 0),
(300597, 30, '3', 'Mid-level', 2, '0', '', 1, 0, 0),
(300598, 30, '4', 'Staff', 3, '0', '', 1, 0, 0),
(300599, 30, '5', 'Subscriber', 4, '0', '', 1, 0, 0),
(300601, 7, '1', 'State License', 0, '', '', 1, 0, 0),
(300603, 31, '1', 'MD', 0, '', '', 1, 0, 0),
(300604, 31, '2', 'RNFP', 0, '', '', 1, 0, 0),
(300605, 31, '3', 'RN', 0, '', '', 1, 0, 0),
(300606, 31, '4', 'PA', 0, '', '', 1, 0, 0),
(300607, 31, '5', 'MA', 0, '', '', 1, 0, 0),
(300609, 32, '1', 'Good', 0, '', '', 1, 0, 0),
(300610, 32, '2', 'Bad', 0, '', '', 1, 0, 0),
(300612, 33, '1', 'American Indian or Alaska Native', 1, '', '', 1, 0, 0),
(300613, 33, '2', 'Asian', 1, '', '', 1, 0, 0),
(300614, 33, '3', 'Black or African American', 2, '', '', 1, 0, 0),
(300615, 33, '4', 'Native Hawaiian or other Pacific Islander', 3, '', '', 1, 0, 0),
(300616, 33, '5', 'White', 4, '', '', 1, 0, 0),
(300618, 34, '1', 'A - On file', 0, '', '', 1, 0, 0),
(300619, 34, '2', 'I - Informed Consent', 0, '', '', 1, 0, 0),
(300620, 34, '3', 'M - Limited Ability', 0, '', '', 1, 0, 0),
(300621, 34, '4', 'N - Not allowed', 0, '', '', 1, 0, 0),
(300622, 34, '5', 'O - On file', 0, '', '', 1, 0, 0),
(300623, 34, '6', 'Y - Has permission', 0, '', '', 1, 0, 0),
(300625, 35, '1', 'AL', 1, '', '', 1, 0, 0),
(300626, 35, '2', 'AK', 1, '', '', 1, 0, 0),
(300627, 35, '3', 'AZ', 2, '', '', 1, 0, 0),
(300628, 35, '4', 'AR', 3, '', '', 1, 0, 0),
(300629, 35, '5', 'CA', 4, '', '', 1, 0, 0),
(300630, 35, '6', 'CO', 5, '', '', 1, 0, 0),
(300631, 35, '7', 'CT', 6, '', '', 1, 0, 0),
(300632, 35, '8', 'DE', 7, '', '', 1, 0, 0),
(300633, 35, '9', 'DC', 8, '', '', 1, 0, 0),
(300634, 35, '10', 'FL', 9, '', '', 1, 0, 0),
(300635, 35, '11', 'GA', 10, '', '', 1, 0, 0),
(300636, 35, '12', 'HI', 11, '', '', 1, 0, 0),
(300637, 35, '13', 'ID', 12, '', '', 1, 0, 0),
(300638, 35, '14', 'IL', 13, '', '', 1, 0, 0),
(300639, 35, '15', 'IN', 14, '', '', 1, 0, 0),
(300640, 35, '16', 'IA', 15, '', '', 1, 0, 0),
(300641, 35, '17', 'KS', 16, '', '', 1, 0, 0),
(300642, 35, '18', 'KY', 17, '', '', 1, 0, 0),
(300643, 35, '19', 'LA', 18, '', '', 1, 0, 0),
(300644, 35, '20', 'ME', 19, '', '', 1, 0, 0),
(300645, 35, '21', 'MD', 20, '', '', 1, 0, 0),
(300646, 35, '22', 'MA', 21, '', '', 1, 0, 0),
(300647, 35, '23', 'MI', 22, '', '', 1, 0, 0),
(300648, 35, '24', 'MN', 23, '', '', 1, 0, 0),
(300649, 35, '25', 'MS', 24, '', '', 1, 0, 0),
(300650, 19, '2', 'Family Patient Statement', 1, '/Patient/familyStatement', '', 1, 0, 0),
(300651, 19, '3', 'Pull List', 2, '/Appointment/pullList', '', 1, 0, 0),
(300659, 35, '35', 'ND', 26, '', '', 1, 0, 0),
(300660, 35, '36', 'OH', 27, '', '', 1, 0, 0),
(300661, 35, '37', 'OK', 28, '', '', 1, 0, 0),
(300662, 35, '38', 'OR', 29, '', '', 1, 0, 0),
(300663, 35, '39', 'PA', 30, '', '', 1, 0, 0),
(300664, 35, '40', 'RI', 31, '', '', 1, 0, 0),
(300665, 35, '41', 'SC', 32, '', '', 1, 0, 0),
(300666, 35, '42', 'SD', 33, '', '', 1, 0, 0),
(300667, 35, '43', 'TN', 34, '', '', 1, 0, 0),
(300668, 35, '44', 'TX', 35, '', '', 1, 0, 0),
(300669, 35, '45', 'UT', 36, '', '', 1, 0, 0),
(300670, 35, '46', 'VT', 37, '', '', 1, 0, 0),
(300671, 35, '47', 'VA', 38, '', '', 1, 0, 0),
(300672, 35, '48', 'WA', 39, '', '', 1, 0, 0),
(300673, 35, '49', 'WV', 40, '', '', 1, 0, 0),
(300674, 35, '50', 'WI', 41, '', '', 1, 0, 0),
(300675, 35, '51', 'WY', 42, '', '', 1, 0, 0),
(300676, 35, '52', 'PR', 43, '', '', 1, 0, 0),
(300678, 36, '1', 'Spouse', 0, '', '', 1, 0, 0),
(300679, 36, '2', 'Parent', 0, '', '', 1, 0, 0),
(300747, 19, '4', 'Route Slip', 3, '/Encounter/routeSlip', '', 1, 0, 0),
(300819, 37, '1', 'Diabetes', 0, '', '', 0, 0, 0),
(300820, 37, '2', 'Hypertension', 2, '', '', 0, 0, 0),
(300853, 38, '1', 'Patient', 0, '', '', 1, 0, 0),
(300854, 38, '2', 'Private Insurance', 0, '', '', 1, 0, 0),
(300855, 38, '3', 'State Program', 0, '', '', 1, 0, 0),
(300856, 38, '4', 'Federal Program', 0, '', '', 1, 0, 0),
(300932, 37, '3', 'hrt', 1, '', '', 0, 0, 0),
(301504, 8, '6', 'Employer', 5, '', '', 1, 0, 0),
(301505, 18, '2', 'Female', 0, '', '', 1, 0, 0),
(301507, 17, '3', 'Not Hispanic or Latino', 1, '', '', 1, 0, 0),
(301508, 27, '10', 'private insurance', 1, '', '', 1, 0, 0),
(301522, 25, '2', 'Seasonal Worker', 0, '', '', 1, 0, 0),
(301523, 25, '3', 'No', 0, '', '', 1, 0, 0),
(301524, 25, '4', 'other', 0, '', '', 1, 0, 0),
(301538, 37, '4', 'Hypercholestrolemia', 0, '', '', 0, 0, 0),
(513683, 39, '1', 'Endocrinology', 5, '', '', 1, 0, 0),
(513684, 39, '2', 'Cardiology', 2, '', '', 1, 0, 0),
(513707, 41, '1', '8:00 AM - Noon', 0, '', '', 1, 0, 0),
(513708, 41, '2', '10:00 AM - 2:00 PM', 1, '', '', 1, 0, 0),
(513709, 41, '3', 'Noon - 4:00 PM', 2, '', '', 1, 0, 0),
(513710, 41, '4', '2:00 PM - 6:00 PM', 3, '', '', 1, 0, 0),
(513711, 41, '5', '4:00 PM - 8:00 PM', 4, '', '', 1, 0, 0),
(513712, 41, '6', 'Evening', 5, '', '', 1, 0, 0),
(513719, 42, '1', 'Monday', 1, '', '', 1, 0, 0),
(513720, 42, '2', 'Tuesday', 1, '', '', 1, 0, 0),
(513721, 42, '3', 'Wednesday', 2, '', '', 1, 0, 0),
(513722, 42, '4', 'Thursday', 3, '', '', 1, 0, 0),
(513723, 42, '5', 'Friday', 4, '', '', 1, 0, 0),
(513724, 42, '6', 'Saturday', 5, '', '', 1, 0, 0),
(513725, 42, '7', 'Sunday', 6, '', '', 1, 0, 0),
(513735, 44, '1', 'Requested', 1, '', '', 1, 0, 0),
(513736, 44, '2', 'Requested / Eligibility Pending', 0, 'Requested / Elig. Pending', '', 1, 0, 0),
(513737, 44, '3', 'Appointment Pending', 2, 'Appt Pending', '', 1, 0, 0),
(513738, 44, '4', 'Appointment Confirmed', 3, 'Appt Confirmed', '', 1, 0, 0),
(513739, 44, '5', 'Appointment Kept', 4, 'Appt Kept', '', 1, 0, 0),
(513740, 44, '6', 'Appointment No-Show', 5, 'Appt No-Show', '', 1, 0, 0),
(513741, 44, '7', 'Returned', 6, '', '', 1, 0, 0),
(513742, 44, '8', 'Canceled', 7, '', '', 0, 0, 0),
(600021, 45, '1', 'insert', 1, '', '', 1, 0, 0),
(600028, 45, '2', 'update', 2, '', '', 1, 0, 0),
(600035, 45, '3', 'delete', 3, '', '', 1, 0, 0),
(600042, 45, '4', 'process', 4, '', '', 1, 0, 0),
(600102, 27, '11', 'MPC', 11, '', '', 1, 0, 0),
(600109, 27, '12', 'PCMI', 12, '', '', 1, 0, 0),
(600116, 27, '13', 'DCHCA', 13, '', '', 1, 0, 0),
(600123, 27, '14', 'MCCP', 14, '', '', 1, 0, 0),
(600130, 27, '15', 'CFK', 15, '', '', 1, 0, 0),
(600137, 27, '16', 'None', 16, '', '', 1, 0, 0),
(600186, 48, '1', 'Yes', 1, '', '', 1, 0, 0),
(600193, 48, '2', 'No', 2, '', '', 1, 0, 0),
(600200, 48, '3', 'Unknown', 3, '', '', 1, 0, 0),
(600207, 48, '4', '-- Not Entered --', 0, '', '', 1, 0, 0),
(600292, 51, '1', 'Active', 1, '', '', 1, 0, 0),
(600299, 51, '2', 'Inactive', 2, '', '', 1, 0, 0),
(600333, 54, '1', 'Monday', 1, '', '', 1, 0, 0),
(600334, 54, '2', 'Tuesday', 1, '', '', 1, 0, 0),
(600335, 54, '3', 'Wednesday', 2, '', '', 1, 0, 0),
(600336, 54, '4', 'Thursday', 3, '', '', 1, 0, 0),
(600337, 54, '5', 'Friday', 4, '', '', 1, 0, 0),
(600338, 54, '6', 'Saturday', 5, '', '', 1, 0, 0),
(600340, 55, 'First', 'First', 0, '', '', 1, 0, 0),
(600341, 55, 'Second', 'Second', 1, '', '', 1, 0, 0),
(600342, 55, 'Third', 'Third', 2, '', '', 1, 0, 0),
(600343, 55, 'Fourth', 'Fourth', 3, '', '', 1, 0, 0),
(600344, 55, 'Last', 'Last', 4, '', '', 1, 0, 0),
(600347, 56, '02', 'February', 1, '', '', 1, 0, 0),
(600348, 56, '03', 'March', 2, '', '', 1, 0, 0),
(600349, 56, '04', 'April', 3, '', '', 1, 0, 0),
(600350, 56, '05', 'May', 4, '', '', 1, 0, 0),
(600351, 56, '06', 'June', 5, '', '', 1, 0, 0),
(600352, 56, '07', 'July', 6, '', '', 1, 0, 0),
(600354, 56, '09', 'September', 8, '', '', 1, 0, 0),
(600355, 56, '10', 'October', 9, '', '', 1, 0, 0),
(600356, 56, '11', 'November', 10, '', '', 1, 0, 0),
(600357, 56, '12', 'December', 11, '', '', 1, 0, 0),
(600359, 57, 'day', 'By Day (Every 3 Days)', 0, '', '', 1, 0, 0),
(600361, 57, 'monthday', 'By Day of Month (Every Fifth)', 0, '', '', 1, 0, 0),
(600362, 57, 'yearmonthday', 'By Day of Month Per Year (Every December 3rd)', 0, '', '', 1, 0, 0),
(600363, 57, 'yearmonthweek', 'By Weekday Per Month Per Year (Every Third Tuesday of November)', 0, '', '', 1, 0, 0),
(600759, 56, '01', 'January', 0, '', '', 1, 0, 0),
(600778, 56, '08', 'August', 7, '', '', 1, 0, 0),
(601228, 59, '1', '1 - No Special Restrictions', 1, '', '', 1, 0, 0),
(601229, 59, '2', '2 - Basic Confidentiality', 1, '', '', 1, 0, 0),
(601230, 59, '3', '3 - Family Planning', 2, '', '', 1, 0, 0),
(601231, 59, '4', '4 - Disease Confidentiality', 3, '', '', 1, 0, 0),
(601232, 59, '5', '6 - Extreme Confidentiality', 5, '', '', 1, 0, 0),
(602408, 60, '0', 'Production', 0, 'P', '', 1, 0, 0),
(602409, 60, '1', 'Testing', 0, 'T', '', 1, 0, 0),
(604886, 61, '1', 'X12', 0, '', '', 1, 0, 0),
(604895, 61, '2', 'Batch', 1, '', '', 1, 0, 0),
(604909, 62, '1', 'Deductible Amount', 1, '1', '', 1, 0, 0),
(604918, 62, '2', 'Coinsurance Amount', 2, '2', '', 1, 0, 0),
(604927, 62, '3', 'Co-payment Amount', 3, '3', '', 1, 0, 0),
(604936, 62, '4', 'The procedure code is inconsistent with the modifier used or a required modifier is missing.', 4, '4', '', 1, 0, 0),
(604945, 62, '5', 'The procedure code/bill type is inconsistent with the place of service.', 5, '5', '', 1, 0, 0),
(604954, 62, '6', 'The procedure/revenue code is inconsistent with the patient''s age.', 6, '6', '', 1, 0, 0),
(604963, 62, '7', 'The procedure/revenue code is inconsistent with the patient''s gender.', 7, '7', '', 1, 0, 0),
(604972, 62, '8', 'The procedure code is inconsistent with the provider type/specialty (taxonomy).', 8, '8', '', 1, 0, 0),
(604981, 62, '9', 'The diagnosis is inconsistent with the patient''s age.', 9, '9', '', 1, 0, 0),
(604990, 62, '10', 'The diagnosis is inconsistent with the patient''s gender.', 10, '10', '', 1, 0, 0),
(604999, 62, '11', 'The diagnosis is inconsistent with the procedure.', 11, '11', '', 1, 0, 0),
(605008, 62, '12', 'The diagnosis is inconsistent with the provider type.', 12, '12', '', 1, 0, 0),
(605017, 62, '13', 'The date of death precedes the date of service.', 13, '13', '', 1, 0, 0),
(605026, 62, '14', 'The date of birth follows the date of service.', 14, '14', '', 1, 0, 0),
(605035, 62, '15', 'Payment adjusted because the submitted authorization number is missin', 15, '15', '', 1, 0, 0),
(605044, 62, '16', 'Claim/service lacks information which is needed for adjudication. Additional information is supplied using remittance advice remarks codes whenever appropriate', 16, '16', '', 1, 0, 0),
(605053, 62, '17', 'Payment adjusted because requested information was not provided or was insufficient/incomplete. Additional information is supplied using the remittance advice remarks codes whenever appropriate.', 17, '17', '', 1, 0, 0),
(605062, 62, '18', 'Duplicate claim/service.', 18, '18', '', 1, 0, 0),
(605071, 62, '19', 'Claim denied because this is a work-related injury/illness and thus the liability of the Worker''s Compensation Carrier.', 19, '19', '', 1, 0, 0),
(605080, 62, '20', 'Claim denied because this injury/illness is covered by the liability carrier.', 20, '20', '', 1, 0, 0),
(605089, 62, '21', 'Claim denied because this injury/illness is the liability of the no-fault carrier.', 21, '21', '', 1, 0, 0),
(605098, 62, '22', 'Payment adjusted because this care may be covered by another payer per coordination of benefits.', 22, '22', '', 1, 0, 0),
(605107, 62, '23', 'Payment adjusted due to the impact of prior payer(s) adjudication including payments and/or adjustments', 23, '23', '', 1, 0, 0),
(605116, 62, '24', 'Payment for charges adjusted. Charges are covered under a capitation agreement/managed care plan.', 24, '24', '', 1, 0, 0),
(605125, 62, '25', 'Payment denied. Your Stop loss deductible has not been met.', 25, '25', '', 1, 0, 0),
(605134, 62, '26', 'Expenses incurred prior to coverage.', 26, '26', '', 1, 0, 0),
(605143, 62, '27', 'Expenses incurred after coverage terminated.', 27, '27', '', 1, 0, 0),
(605152, 62, '28', 'Coverage not in effect at the time the service was provided.', 28, '28', '', 1, 0, 0),
(605161, 62, '29', 'The time limit for filing has expired.', 29, '29', '', 1, 0, 0),
(605170, 62, '30', 'Payment adjusted because the patient has not met the required eligibilit', 30, '30', '', 1, 0, 0),
(605179, 62, '31', 'Claim denied as patient cannot be identified as our insured.', 31, '31', '', 1, 0, 0),
(605190, 63, '1', 'Simple Value', 0, '', '', 1, 0, 0),
(605199, 63, '2', 'Form Field Name', 1, '', '', 1, 0, 0),
(605833, 57, 'dayweek', 'By Days of Week', 6, '', '', 1, 0, 0),
(608377, 59, '6', '5 - Disease and Family Planning Confidentiality', 4, '', '', 1, 0, 0),
(1080307, 67, '1', 'EMR Tab', 1, '', '', 1, 0, 0),
(1080314, 67, '2', 'Criticals Pallet', 1, '', '', 1, 0, 0),
(1080320, 67, '7', 'Other (QuickList)', 19, '', '', 1, 0, 0),
(5691334, 68, '1', 'Shelter', 1, '', '', 1, 0, 0),
(5691343, 68, '2', 'Homeless', 1, '', '', 1, 0, 0),
(5691352, 68, '3', 'Transition Program', 2, '', '', 1, 0, 0),
(5691361, 68, '4', 'Has Home', 3, '', '', 1, 0, 0),
(5691370, 68, '5', 'Unknown', 4, '', '', 1, 0, 0),
(5691379, 68, '6', 'Blank', 5, '', '', 0, 0, 0),
(5691393, 69, '1', 'Montgomery', 1, '', '', 1, 0, 0),
(5691402, 69, '2', 'District of Columbia', 1, '', '', 1, 0, 0),
(5691411, 69, '3', 'Carroll', 2, '', '', 1, 0, 0),
(5691420, 69, '4', 'Charles', 3, '', '', 1, 0, 0),
(5691429, 69, '5', 'Howard', 4, '', '', 1, 0, 0),
(5691438, 69, '6', 'La Grange', 5, '', '', 1, 0, 0),
(5691447, 69, '7', 'Arlington', 6, '', '', 1, 0, 0),
(5691456, 69, '8', 'Baltimore', 7, '', '', 1, 0, 0),
(5691465, 69, '9', 'Calvert', 8, '', '', 1, 0, 0),
(5691474, 69, '10', 'Culpepper', 9, '', '', 1, 0, 0),
(5691483, 69, '11', 'Essex', 10, '', '', 1, 0, 0),
(5691492, 69, '12', 'Fairfax', 11, '', '', 1, 0, 0),
(5691501, 69, '13', 'Frederick', 12, '', '', 1, 0, 0),
(5691510, 69, '14', 'Loudoun', 13, '', '', 1, 0, 0),
(5691519, 69, '15', 'Manassas', 14, '', '', 1, 0, 0),
(5691528, 69, '16', 'Prince William', 15, '', '', 1, 0, 0),
(5691537, 69, '17', 'Prince Georges', 16, '', '', 1, 0, 0),
(5691546, 69, '18', 'Somerset', 17, '', '', 1, 0, 0),
(5691560, 70, '1', 'Head of Household', 0, '', '', 1, 0, 0),
(5691569, 70, '2', 'Not Head of Household', 0, '', '', 1, 0, 0),
(5691578, 70, '3', 'Unknown', 0, '', '', 1, 0, 0),
(5691587, 70, '4', 'Blank', 0, '', '', 1, 0, 0),
(5691601, 71, '1', 'English', 1, '', '', 1, 0, 0),
(5691610, 71, '2', 'Spanish', 1, '', '', 1, 0, 0),
(5691619, 71, '3', 'Amharic', 2, '', '', 1, 0, 0),
(5691628, 71, '4', 'Arabic', 3, '', '', 1, 0, 0),
(5691637, 71, '5', 'Armenian', 4, '', '', 1, 0, 0),
(5691646, 71, '6', 'Bengali', 5, '', '', 1, 0, 0),
(5691655, 71, '7', 'Chinese', 6, '', '', 1, 0, 0),
(5691664, 71, '8', 'Farsi', 7, '', '', 1, 0, 0),
(5691673, 71, '9', 'French', 8, '', '', 1, 0, 0),
(5691682, 71, '10', 'German', 9, '', '', 1, 0, 0),
(5691691, 71, '11', 'Hindi', 10, '', '', 1, 0, 0),
(5691700, 71, '12', 'Indonesian', 11, '', '', 1, 0, 0),
(5691709, 71, '13', 'Korean', 12, '', '', 1, 0, 0),
(5691718, 71, '14', 'Mongolian', 13, '', '', 1, 0, 0),
(5691727, 71, '15', 'Russian', 14, '', '', 1, 0, 0),
(5691736, 71, '16', 'Tagalog', 15, '', '', 1, 0, 0),
(5691745, 71, '17', 'Tigrigna', 16, '', '', 1, 0, 0),
(5691754, 71, '18', 'Urdu', 17, '', '', 1, 0, 0),
(5691763, 71, '19', 'Vietnamese', 18, '', '', 1, 0, 0),
(5691772, 71, '20', 'Other', 19, '', '', 1, 0, 0),
(5691781, 71, '21', 'Unknown', 20, '', '', 1, 0, 0),
(5691790, 71, '22', 'Blank', 21, '', '', 1, 0, 0),
(5691804, 72, '1', 'Proficient', 1, '', '', 1, 0, 0),
(5691813, 72, '2', 'Somewhat Proficient', 1, '', '', 1, 0, 0),
(5691822, 72, '3', 'Limited', 2, '', '', 1, 0, 0),
(5691831, 72, '4', 'Not Proficient', 3, '', '', 1, 0, 0),
(5691840, 72, '5', 'Unknown', 4, '', '', 1, 0, 0),
(5691849, 72, '6', 'Blank', 5, '', '', 1, 0, 0),
(5691863, 73, '1', 'Afghanistan', 1, '', '', 1, 0, 0),
(5691872, 73, '2', 'Albania', 1, '', '', 1, 0, 0),
(5691881, 73, '3', 'Algeria', 2, '', '', 1, 0, 0),
(5691890, 73, '4', 'American Samoa', 3, '', '', 1, 0, 0),
(5691899, 73, '5', 'Andorra', 4, '', '', 1, 0, 0),
(5691908, 73, '6', 'Angola', 5, '', '', 1, 0, 0),
(5691917, 73, '7', 'Anguilla', 6, '', '', 1, 0, 0),
(5691926, 73, '8', 'Antigua and Barbuda', 7, '', '', 1, 0, 0),
(5691935, 73, '9', 'Argentina', 8, '', '', 1, 0, 0),
(5691944, 73, '10', 'Armenia', 9, '', '', 1, 0, 0),
(5691953, 73, '11', 'Aruba', 10, '', '', 1, 0, 0),
(5691962, 73, '12', 'Australia', 11, '', '', 1, 0, 0),
(5691971, 73, '13', 'Austria', 12, '', '', 1, 0, 0),
(5691980, 73, '14', 'Azerbajan', 13, '', '', 1, 0, 0),
(5691989, 73, '15', 'Azores (Portugal)', 14, '', '', 1, 0, 0),
(5691998, 73, '16', 'Bahamas', 15, '', '', 1, 0, 0),
(5692007, 73, '17', 'Bahrain', 16, '', '', 1, 0, 0),
(5692016, 73, '18', 'Bangladesh', 17, '', '', 1, 0, 0),
(5692025, 73, '19', 'Barbados', 18, '', '', 1, 0, 0),
(5692034, 73, '20', 'Belarus', 19, '', '', 1, 0, 0),
(5692043, 73, '21', 'Belgium', 20, '', '', 1, 0, 0),
(5692052, 73, '22', 'Belize', 21, '', '', 1, 0, 0),
(5692061, 73, '23', 'Benin', 22, '', '', 1, 0, 0),
(5692070, 73, '24', 'Bermuda', 23, '', '', 1, 0, 0),
(5692079, 73, '25', 'Bolivia', 24, '', '', 1, 0, 0),
(5692088, 73, '26', 'Bonaire (Netherlands Antilles)', 25, '', '', 1, 0, 0),
(5692097, 73, '27', 'Bosnia', 26, '', '', 1, 0, 0),
(5692106, 73, '28', 'Botswana', 27, '', '', 1, 0, 0),
(5692115, 73, '29', 'Brazil', 28, '', '', 1, 0, 0),
(5692124, 73, '30', 'British Virgin Islands', 29, '', '', 1, 0, 0),
(5692133, 73, '31', 'Brunei', 30, '', '', 1, 0, 0),
(5692142, 73, '32', 'Bulgaria', 31, '', '', 1, 0, 0),
(5692151, 73, '33', 'Burkina Faso', 32, '', '', 1, 0, 0),
(5692160, 73, '34', 'Burundi', 33, '', '', 1, 0, 0),
(5692169, 73, '35', 'Cambodia', 34, '', '', 1, 0, 0),
(5692178, 73, '36', 'Cameroom', 35, '', '', 1, 0, 0),
(5692187, 73, '37', 'Canada', 36, '', '', 1, 0, 0),
(5692196, 73, '38', 'Canary Islands', 37, '', '', 1, 0, 0),
(5692205, 73, '39', 'Cape Verde', 38, '', '', 1, 0, 0),
(5692214, 73, '40', 'Cayman Islands', 39, '', '', 1, 0, 0),
(5692223, 73, '41', 'Central African Republic', 40, '', '', 1, 0, 0),
(5692232, 73, '42', 'Chad', 41, '', '', 1, 0, 0),
(5692241, 73, '43', 'Channel Islands', 42, '', '', 1, 0, 0),
(5692250, 73, '44', 'Chile', 43, '', '', 1, 0, 0),
(5692259, 73, '45', 'China', 44, '', '', 1, 0, 0),
(5692268, 73, '46', 'Colombia', 45, '', '', 1, 0, 0),
(5692277, 73, '47', 'Congo-Democratic Republic of', 46, '', '', 1, 0, 0),
(5692286, 73, '48', 'Congo-Republic of', 47, '', '', 1, 0, 0),
(5692295, 73, '49', 'Cook Islands', 48, '', '', 1, 0, 0),
(5692304, 73, '50', 'Costa Rica', 49, '', '', 1, 0, 0),
(5692313, 73, '51', 'Croatia', 50, '', '', 1, 0, 0),
(5692322, 73, '52', 'Cuba', 51, '', '', 1, 0, 0),
(5692331, 73, '53', 'Curacao (Netherlands Antilles)', 52, '', '', 1, 0, 0),
(5692340, 73, '54', 'Cyprus', 53, '', '', 1, 0, 0),
(5692349, 73, '55', 'Czech Republic', 54, '', '', 1, 0, 0),
(5692358, 73, '56', 'Denmark', 55, '', '', 1, 0, 0),
(5692367, 73, '57', 'Djibouti', 56, '', '', 1, 0, 0),
(5692376, 73, '58', 'Dominica', 57, '', '', 1, 0, 0),
(5692385, 73, '59', 'Dominican Republic', 58, '', '', 1, 0, 0),
(5692394, 73, '60', 'Ecuador', 59, '', '', 1, 0, 0),
(5692403, 73, '61', 'Eqypt', 60, '', '', 1, 0, 0),
(5692412, 73, '62', 'El Salvador', 61, '', '', 1, 0, 0),
(5692421, 73, '63', 'England', 62, '', '', 1, 0, 0),
(5692430, 73, '64', 'Equatorial Guniea', 63, '', '', 1, 0, 0),
(5692439, 73, '65', 'Eritrea', 64, '', '', 1, 0, 0),
(5692448, 73, '66', 'Estonia', 65, '', '', 1, 0, 0),
(5692457, 73, '67', 'Ethiopia', 66, '', '', 1, 0, 0),
(5692466, 73, '68', 'Faroe Islands (Denmark)', 67, '', '', 1, 0, 0),
(5692475, 73, '69', 'Fiji', 68, '', '', 1, 0, 0),
(5692484, 73, '70', 'Finland', 69, '', '', 1, 0, 0),
(5692493, 73, '71', 'France', 70, '', '', 1, 0, 0),
(5692502, 73, '72', 'French Guiana', 71, '', '', 1, 0, 0),
(5692511, 73, '73', 'French Polynesia', 72, '', '', 1, 0, 0),
(5692520, 73, '74', 'Gabon', 73, '', '', 1, 0, 0),
(5692529, 73, '75', 'Gambia', 74, '', '', 1, 0, 0),
(5692538, 73, '76', 'Georgia', 75, '', '', 1, 0, 0),
(5692547, 73, '77', 'Germany', 76, '', '', 1, 0, 0),
(5692556, 73, '78', 'Ghana', 77, '', '', 1, 0, 0),
(5692565, 73, '79', 'Gilbraltar', 78, '', '', 1, 0, 0),
(5692574, 73, '80', 'Greece', 79, '', '', 1, 0, 0),
(5692583, 73, '81', 'Greenland (Denmark)', 80, '', '', 1, 0, 0),
(5692592, 73, '82', 'Grenada', 81, '', '', 1, 0, 0),
(5692601, 73, '83', 'Guadeloupe', 82, '', '', 1, 0, 0),
(5692610, 73, '84', 'Guam', 83, '', '', 1, 0, 0),
(5692619, 73, '85', 'Guatemala', 84, '', '', 1, 0, 0),
(5692628, 73, '86', 'Guinea', 85, '', '', 1, 0, 0),
(5692637, 73, '87', 'Guinea-Bissau', 86, '', '', 1, 0, 0),
(5692646, 73, '88', 'Guyana', 87, '', '', 1, 0, 0),
(5692655, 73, '89', 'Haiti', 88, '', '', 1, 0, 0),
(5692664, 73, '90', 'Holland (Netherlands)', 89, '', '', 1, 0, 0),
(5692673, 73, '91', 'Honduras', 90, '', '', 1, 0, 0),
(5692682, 73, '92', 'Hong Kong', 91, '', '', 1, 0, 0),
(5692691, 73, '93', 'Hungary', 92, '', '', 1, 0, 0),
(5692700, 73, '94', 'Iceland', 93, '', '', 1, 0, 0),
(5692709, 73, '95', 'India', 94, '', '', 1, 0, 0),
(5692718, 73, '96', 'Indonesia', 95, '', '', 1, 0, 0),
(5692727, 73, '97', 'Iran', 96, '', '', 1, 0, 0),
(5692736, 73, '98', 'Iraq', 97, '', '', 1, 0, 0),
(5692745, 73, '99', 'Ireland -Republic of', 98, '', '', 1, 0, 0),
(5692754, 73, '100', 'Israel', 99, '', '', 1, 0, 0),
(5692763, 73, '101', 'Italy', 100, '', '', 1, 0, 0),
(5692772, 73, '102', 'Ivory Coast', 101, '', '', 1, 0, 0),
(5692781, 73, '103', 'Jamaica', 102, '', '', 1, 0, 0),
(5692790, 73, '104', 'Japan', 103, '', '', 1, 0, 0),
(5692799, 73, '105', 'Kazakhstan', 104, '', '', 1, 0, 0),
(5692808, 73, '106', 'Kenya', 105, '', '', 1, 0, 0),
(5692817, 73, '107', 'Kiribati', 106, '', '', 1, 0, 0),
(5692826, 73, '108', 'Korea (South Korea)', 107, '', '', 1, 0, 0),
(5692835, 73, '109', 'Korsrae (Federated States of Micronesia)', 108, '', '', 1, 0, 0),
(5692844, 73, '110', 'Kuwait', 109, '', '', 1, 0, 0),
(5692853, 73, '111', 'Kyrgyzstan', 110, '', '', 1, 0, 0),
(5692862, 73, '112', 'Laos', 111, '', '', 1, 0, 0),
(5692871, 73, '113', 'Latvia', 112, '', '', 1, 0, 0),
(5692880, 73, '114', 'Lebanon', 113, '', '', 1, 0, 0),
(5692889, 73, '115', 'Lesotho', 114, '', '', 1, 0, 0),
(5692898, 73, '116', 'Liberia', 115, '', '', 1, 0, 0),
(5692907, 73, '117', 'Liechtenstein', 116, '', '', 1, 0, 0),
(5692916, 73, '118', 'Lithuania', 117, '', '', 1, 0, 0),
(5692925, 73, '119', 'Macau', 118, '', '', 1, 0, 0),
(5692934, 73, '120', 'Macedonia', 119, '', '', 1, 0, 0),
(5692943, 73, '121', 'Madagascar', 120, '', '', 1, 0, 0),
(5692952, 73, '122', 'Maderia (Portugal)', 121, '', '', 1, 0, 0),
(5692961, 73, '123', 'Malawi', 122, '', '', 1, 0, 0),
(5692970, 73, '124', 'Malaysia', 123, '', '', 1, 0, 0),
(5692979, 73, '125', 'Maldives', 124, '', '', 1, 0, 0),
(5692988, 73, '126', 'Mali', 125, '', '', 1, 0, 0),
(5692997, 73, '127', 'Malta', 126, '', '', 1, 0, 0),
(5693006, 73, '128', 'Marshall Islands', 127, '', '', 1, 0, 0),
(5693015, 73, '129', 'Martinique', 128, '', '', 1, 0, 0),
(5693024, 73, '130', 'Mauritius', 129, '', '', 1, 0, 0),
(5693033, 73, '131', 'Mexico', 130, '', '', 1, 0, 0),
(5693042, 73, '132', 'Micronesia - Federated States of', 131, '', '', 1, 0, 0),
(5693051, 73, '133', 'Moldova', 132, '', '', 1, 0, 0),
(5693060, 73, '134', 'Monaco', 133, '', '', 1, 0, 0),
(5693069, 73, '135', 'Mongolia', 134, '', '', 1, 0, 0),
(5693078, 73, '136', 'Montserrat', 135, '', '', 1, 0, 0),
(5693087, 73, '137', 'Morocco', 136, '', '', 1, 0, 0),
(5693096, 73, '138', 'Mozambique', 137, '', '', 1, 0, 0),
(5693105, 73, '139', 'Nambia', 138, '', '', 1, 0, 0),
(5693114, 73, '140', 'Nepal', 139, '', '', 1, 0, 0),
(5693123, 73, '141', 'Netherlands (Holland)', 140, '', '', 1, 0, 0),
(5693132, 73, '142', 'Netherlands Antilles', 141, '', '', 1, 0, 0),
(5693141, 73, '143', 'New Caledonia', 142, '', '', 1, 0, 0),
(5693150, 73, '144', 'New Zealand', 143, '', '', 1, 0, 0),
(5693159, 73, '145', 'Nicaragua', 144, '', '', 1, 0, 0),
(5693168, 73, '146', 'Niger', 145, '', '', 1, 0, 0),
(5693177, 73, '147', 'Nigeria', 146, '', '', 1, 0, 0),
(5693186, 73, '148', 'Norfolk Island', 147, '', '', 1, 0, 0),
(5693195, 73, '149', 'Northern Ireland (UK)', 148, '', '', 1, 0, 0),
(5693204, 73, '150', 'Northern Mariana Islands', 149, '', '', 1, 0, 0),
(5693213, 73, '151', 'Norway', 150, '', '', 1, 0, 0),
(5693222, 73, '152', 'Oman', 151, '', '', 1, 0, 0),
(5693231, 73, '153', 'Pakistan', 152, '', '', 1, 0, 0),
(5693240, 73, '154', 'Palau', 153, '', '', 1, 0, 0),
(5693249, 73, '155', 'Panama', 154, '', '', 1, 0, 0),
(5693258, 73, '156', 'Papua New Guinea', 155, '', '', 1, 0, 0),
(5693267, 73, '157', 'Paraguay', 156, '', '', 1, 0, 0),
(5693276, 73, '158', 'Peru', 157, '', '', 1, 0, 0),
(5693285, 73, '159', 'Philippines', 158, '', '', 1, 0, 0),
(5693294, 73, '160', 'Poland', 159, '', '', 1, 0, 0),
(5693303, 73, '161', 'Ponape (Federated States of Micronesia)', 160, '', '', 1, 0, 0),
(5693312, 73, '162', 'Portugal', 161, '', '', 1, 0, 0),
(5693321, 73, '163', 'Qatar', 162, '', '', 1, 0, 0),
(5693330, 73, '164', 'Reunion', 163, '', '', 1, 0, 0),
(5693339, 73, '165', 'Romania', 164, '', '', 1, 0, 0),
(5693348, 73, '166', 'Rota (Northern Mariana Islands)', 165, '', '', 1, 0, 0),
(5693357, 73, '167', 'Russia', 166, '', '', 1, 0, 0),
(5693366, 73, '168', 'Rwanda', 167, '', '', 1, 0, 0),
(5693375, 73, '169', 'Saba (Netherlands Antilles)', 168, '', '', 1, 0, 0),
(5693384, 73, '170', 'Saipan (Northern Mariana Islands)', 169, '', '', 1, 0, 0),
(5693393, 73, '171', 'San Marino', 170, '', '', 1, 0, 0),
(5693402, 73, '172', 'Saudia Arabia', 171, '', '', 1, 0, 0),
(5693411, 73, '173', 'Scotland (United Kingdom)', 172, '', '', 1, 0, 0),
(5693420, 73, '174', 'Senegal', 173, '', '', 1, 0, 0),
(5693429, 73, '175', 'Seychelles', 174, '', '', 1, 0, 0),
(5693438, 73, '176', 'Sierra Leone', 175, '', '', 1, 0, 0),
(5693447, 73, '177', 'Singapore', 176, '', '', 1, 0, 0),
(5693456, 73, '178', 'Slovakia', 177, '', '', 1, 0, 0),
(5693465, 73, '179', 'Slovenia', 178, '', '', 1, 0, 0),
(5693474, 73, '180', 'Solomon Islands', 179, '', '', 1, 0, 0),
(5693483, 73, '181', 'Somalia', 180, '', '', 1, 0, 0),
(5693492, 73, '182', 'South Africa', 181, '', '', 1, 0, 0),
(5693501, 73, '183', 'Spain', 182, '', '', 1, 0, 0),
(5693510, 73, '184', 'Sir Lanki', 183, '', '', 1, 0, 0),
(5693519, 73, '185', 'St. Barthelemy (Guadeloupe)', 184, '', '', 1, 0, 0),
(5693528, 73, '186', 'St. Christopher (St. Kitts and Nevis)', 185, '', '', 1, 0, 0),
(5693537, 73, '187', 'St. Croix (U.S. Virgin Islands)', 186, '', '', 1, 0, 0),
(5693546, 73, '188', 'St. Eustatius (Netherlands Antilles)', 187, '', '', 1, 0, 0),
(5693555, 73, '189', 'St. John ((U.S. Virgin Islands)', 188, '', '', 1, 0, 0),
(5693564, 73, '190', 'St. Kitts and Nevis', 189, '', '', 1, 0, 0),
(5693573, 73, '191', 'St. Lucia', 190, '', '', 1, 0, 0),
(5693582, 73, '192', 'St. Martin (Guadeloupe)', 191, '', '', 1, 0, 0),
(5693591, 73, '193', 'St. Thomas (U.S. Virgin Islands)', 192, '', '', 1, 0, 0),
(5693600, 73, '194', 'St. Vincent and the Grenadines', 193, '', '', 1, 0, 0),
(5693609, 73, '195', 'Suriname', 194, '', '', 1, 0, 0),
(5693618, 73, '196', 'Swaziland', 195, '', '', 1, 0, 0),
(5693627, 73, '197', 'Sweden', 196, '', '', 1, 0, 0),
(5693636, 73, '198', 'Switzerland', 197, '', '', 1, 0, 0),
(5693645, 73, '199', 'Syria', 198, '', '', 1, 0, 0),
(5693654, 73, '200', 'Tahiti (French Polynesia)', 199, '', '', 1, 0, 0),
(5693663, 73, '201', 'Taiwan', 200, '', '', 1, 0, 0),
(5693672, 73, '202', 'Tajikistan', 201, '', '', 1, 0, 0),
(5693681, 73, '203', 'Tanzania', 202, '', '', 1, 0, 0),
(5693690, 73, '204', 'Thailand', 203, '', '', 1, 0, 0),
(5693699, 73, '205', 'Tinian (Northern Mariana Islands)', 204, '', '', 1, 0, 0),
(5693708, 73, '206', 'Togo', 205, '', '', 1, 0, 0),
(5693717, 73, '207', 'Tonga', 206, '', '', 1, 0, 0),
(5693726, 73, '208', 'Tortola (British Virgin Islands)', 207, '', '', 1, 0, 0),
(5693735, 73, '209', 'Trinidad and Tobago', 208, '', '', 1, 0, 0),
(5693744, 73, '210', 'Truk (Federated States of Micronesia)', 209, '', '', 1, 0, 0),
(5693753, 73, '211', 'Tunisia', 210, '', '', 1, 0, 0),
(5693762, 73, '212', 'Turkey', 211, '', '', 1, 0, 0),
(5693771, 73, '213', 'Turkmenistan', 212, '', '', 1, 0, 0),
(5693780, 73, '214', 'Turks and Caicos Islands', 213, '', '', 1, 0, 0),
(5693789, 73, '215', 'Tuvalu', 214, '', '', 1, 0, 0),
(5693798, 73, '216', 'U.S. Virgin Islands', 215, '', '', 1, 0, 0),
(5693807, 73, '217', 'Uganda', 216, '', '', 1, 0, 0),
(5693816, 73, '218', 'Ukraine', 217, '', '', 1, 0, 0),
(5693825, 73, '219', 'Union Island (St. Vincent and the Grenadines)', 218, '', '', 1, 0, 0),
(5693834, 73, '220', 'United Arab Emirates', 219, '', '', 1, 0, 0),
(5693843, 73, '221', 'United Kingdom', 220, '', '', 1, 0, 0),
(5693852, 73, '222', 'Unknown', 221, '', '', 1, 0, 0),
(5693861, 73, '223', 'Uruguay', 222, '', '', 1, 0, 0),
(5693870, 73, '224', 'USA', 223, '', '', 1, 0, 0),
(5693879, 73, '225', 'Uzbekistan', 224, '', '', 1, 0, 0),
(5693888, 73, '226', 'Vanuatu', 225, '', '', 1, 0, 0),
(5693897, 73, '227', 'Venezuela', 226, '', '', 1, 0, 0),
(5693906, 73, '228', 'Vietnam', 227, '', '', 1, 0, 0),
(5693915, 73, '229', 'Virgin Gorda (British Virgin Islands)', 228, '', '', 1, 0, 0),
(5693924, 73, '230', 'Wake Island', 229, '', '', 1, 0, 0),
(5693933, 73, '231', 'Wales (United Kingdom)', 230, '', '', 1, 0, 0),
(5693942, 73, '232', 'Wallis and Futuna Islands', 231, '', '', 1, 0, 0),
(5693951, 73, '233', 'Western Samoa', 232, '', '', 1, 0, 0),
(5693960, 73, '234', 'Yap (Federated States of Micronesia)', 233, '', '', 1, 0, 0),
(5693969, 73, '235', 'Yemen', 234, '', '', 1, 0, 0),
(5693978, 73, '236', 'Zaire (Democratic Republic of Congo)', 235, '', '', 1, 0, 0),
(5693987, 73, '237', 'Zambia', 236, '', '', 1, 0, 0),
(5693996, 73, '238', 'Zimbabwe', 237, '', '', 1, 0, 0),
(5694005, 73, '239', 'Blank', 238, '', '', 1, 0, 0),
(5694019, 74, '1', 'Other Christian', 0, '', '', 1, 0, 0),
(5694028, 74, '2', 'Muslim', 0, '', '', 1, 0, 0),
(5694037, 74, '3', 'Jewish', 0, '', '', 1, 0, 0),
(5694046, 74, '4', 'Buddhist', 0, '', '', 1, 0, 0),
(5694055, 74, '5', 'Hindu', 0, '', '', 1, 0, 0),
(5694064, 74, '6', 'Catholic', 0, '', '', 1, 0, 0),
(5694073, 74, '7', 'Jehovah''s Witness', 0, '', '', 1, 0, 0),
(5694082, 74, '8', 'Mormon', 0, '', '', 1, 0, 0),
(5694091, 74, '9', 'None', 0, '', '', 1, 0, 0),
(5694100, 74, '10', 'Orthodox', 0, '', '', 1, 0, 0),
(5694109, 74, '11', 'Protestant', 0, '', '', 1, 0, 0),
(5694118, 74, '12', 'Other', 0, '', '', 1, 0, 0),
(5694127, 74, '13', 'Unknown', 0, '', '', 1, 0, 0),
(5694136, 74, '14', 'Blank', 0, '', '', 1, 0, 0),
(5694150, 75, '1', 'Employed', 0, '', '', 1, 0, 0),
(5694159, 75, '2', 'Unemployed', 0, '', '', 1, 0, 0),
(5694168, 75, '3', 'Unknown', 0, '', '', 1, 0, 0),
(5694177, 75, '4', 'Blank', 0, '', '', 1, 0, 0),
(5694191, 76, '1', 'Unknown', 0, '', '', 1, 0, 0),
(5694200, 76, '2', 'None-illiterate', 0, '', '', 1, 0, 0),
(5694209, 76, '3', 'Some Elementary Education', 0, '', '', 1, 0, 0),
(5694218, 76, '4', 'Some Middle School', 0, '', '', 1, 0, 0),
(5694227, 76, '5', 'Some High School', 0, '', '', 1, 0, 0),
(5694236, 76, '6', 'High School Degree', 0, '', '', 1, 0, 0),
(5694245, 76, '7', 'Vocational/Tech School', 0, '', '', 1, 0, 0),
(5694254, 76, '8', 'Some College', 0, '', '', 1, 0, 0),
(5694263, 76, '9', 'Associates Degree', 0, '', '', 1, 0, 0),
(5694272, 76, '10', 'Bachelors Degree', 0, '', '', 1, 0, 0),
(5694281, 76, '11', 'Post Grad College', 0, '', '', 1, 0, 0),
(5694290, 76, '12', 'Masters Degree', 0, '', '', 1, 0, 0),
(5694299, 76, '13', 'Advanced Degree', 0, '', '', 1, 0, 0),
(5694308, 76, '14', 'Other', 0, '', '', 1, 0, 0),
(5694317, 76, '15', 'Blank', 0, '', '', 1, 0, 0),
(5694331, 77, '1', 'Yes', 1, '', '', 1, 0, 0),
(5694340, 77, '2', 'No', 1, '', '', 1, 0, 0),
(5694349, 77, '3', 'Unknown', 2, '', '', 1, 0, 0),
(5694363, 78, '1', 'Asthma', 1, '', '', 1, 0, 0),
(5694372, 78, '2', 'Anti-Coagulation', 1, '', '', 1, 0, 0),
(5694381, 78, '3', 'Cancer', 2, '', '', 1, 0, 0),
(5694390, 78, '4', 'CHF (Congestive Heart Faliure)', 3, '', '', 1, 0, 0),
(5694399, 78, '5', 'CVA (Cerbrovascular Accident, Stroke)', 4, '', '', 1, 0, 0),
(5694408, 78, '6', 'Depression', 5, '', '', 1, 0, 0),
(5694417, 78, '7', 'Diabetes', 6, '', '', 1, 0, 0),
(5694426, 78, '8', 'Diabetes Type I', 7, '', '', 1, 0, 0),
(5694435, 78, '9', 'Diabetes Type II', 8, '', '', 1, 0, 0),
(5694444, 78, '10', 'Hyperlipidemia', 9, '', '', 1, 0, 0),
(5694453, 78, '11', 'Hypertension', 10, '', '', 1, 0, 0),
(5694462, 78, '12', 'Nephropathy', 11, '', '', 1, 0, 0),
(5694471, 78, '13', 'Neuropathy', 12, '', '', 1, 0, 0),
(5694480, 78, '14', 'NKF', 13, '', '', 1, 0, 0),
(5694489, 78, '15', 'Obesity', 14, '', '', 1, 0, 0),
(5694498, 78, '16', 'Post MI', 15, '', '', 1, 0, 0),
(5694507, 78, '17', 'PVD (Peripheralvascular Disease)', 16, '', '', 1, 0, 0),
(5694516, 78, '18', 'Renal Faliure', 17, '', '', 1, 0, 0),
(5694525, 78, '19', 'Retinopathy', 18, '', '', 1, 0, 0),
(5694539, 79, '1', 'MPC', 0, '', '', 1, 0, 0),
(5694548, 79, '2', 'PCMI', 0, '', '', 1, 0, 0),
(5694557, 79, '3', 'DCHCA', 0, '', '', 1, 0, 0),
(5694566, 79, '4', 'MCCP', 0, '', '', 1, 0, 0),
(5694575, 79, '5', 'CFK', 0, '', '', 1, 0, 0),
(5694584, 79, '6', 'None', 0, '', '', 1, 0, 0),
(5694598, 80, '1', 'Yes', 0, '', '', 1, 0, 0),
(5694607, 80, '2', 'No', 0, '', '', 1, 0, 0),
(5694616, 80, '3', 'Unknown', 0, '', '', 1, 0, 0),
(5694630, 81, '1', 'Food', 1, '', '', 1, 0, 0),
(5694648, 81, '3', 'apples', 2, '', '', 1, 1, 5694630),
(5694657, 81, '4', 'Shrimp', 3, '', '', 1, 1, 5694630),
(5694666, 81, '5', 'cherries', 4, '', '', 1, 1, 5694630),
(5694675, 81, '6', 'Coffee', 5, '', '', 1, 1, 5694630),
(5694684, 81, '7', 'Crayfish', 6, '', '', 1, 1, 5694630),
(5694693, 81, '8', 'Egg', 7, '', '', 1, 1, 5694630),
(5694702, 81, '9', 'fish', 8, '', '', 1, 1, 5694630),
(5694711, 81, '10', 'Tomato', 9, '', '', 1, 1, 5694630),
(5694720, 81, '11', 'Hot peppers', 10, '', '', 1, 1, 5694630),
(5694729, 81, '12', 'peaches', 11, '', '', 1, 1, 5694630),
(5694738, 81, '13', 'peanuts', 12, '', '', 1, 1, 5694630),
(5694747, 81, '14', 'Pork', 13, '', '', 1, 1, 5694630),
(5694756, 81, '15', 'Seafood', 14, '', '', 1, 1, 5694630),
(5694765, 81, '16', 'Watermelon', 15, '', '', 1, 1, 5694630),
(5694774, 81, '17', 'Yeast', 16, '', '', 1, 1, 5694630),
(5694783, 81, '18', 'Meds', 17, '', '', 1, 0, 0),
(5694801, 81, '20', 'pyridium', 19, '', '', 1, 1, 5694783),
(5694810, 81, '21', 'ACE inhibitors', 20, '', '', 1, 1, 5694783),
(5694819, 81, '22', 'Acetaminofen', 21, '', '', 1, 1, 5694783),
(5694828, 81, '23', 'Adalop', 22, '', '', 1, 1, 5694783),
(5694837, 81, '24', 'Advil', 23, '', '', 1, 1, 5694783),
(5694846, 81, '25', 'Aleve', 24, '', '', 1, 1, 5694783),
(5694855, 81, '26', 'Alka Seltzer', 25, '', '', 1, 1, 5694783),
(5694864, 81, '27', 'Antibotics', 26, '', '', 1, 1, 5694783),
(5694873, 81, '28', 'Amitriptyline', 27, '', '', 1, 1, 5694783),
(5694882, 81, '29', 'Amoxicillin', 28, '', '', 1, 1, 5694783),
(5694891, 81, '30', 'Ampicilina', 29, '', '', 1, 1, 5694783),
(5694900, 81, '31', 'anaprox', 30, '', '', 1, 1, 5694783),
(5694909, 81, '32', 'anestesics', 31, '', '', 1, 1, 5694783),
(5694918, 81, '33', 'Anesthesia', 32, '', '', 1, 1, 5694783),
(5694927, 81, '34', 'aspirin', 33, '', '', 1, 1, 5694783),
(5694936, 81, '35', 'augmentin', 34, '', '', 1, 1, 5694783),
(5694945, 81, '36', 'B-12', 35, '', '', 1, 1, 5694783),
(5694954, 81, '37', 'Bactim', 36, '', '', 1, 1, 5694783),
(5694963, 81, '38', 'benedril', 37, '', '', 1, 1, 5694783),
(5694972, 81, '39', 'ceflin', 38, '', '', 1, 1, 5694783),
(5694981, 81, '40', 'Celebrex', 39, '', '', 1, 1, 5694783),
(5694990, 81, '41', 'Celexa', 40, '', '', 1, 1, 5694783),
(5694999, 81, '42', 'Cephalexin', 41, '', '', 1, 1, 5694783),
(5695008, 81, '43', 'chloroquine', 42, '', '', 1, 0, 0),
(5695017, 81, '44', 'Cipro', 43, '', '', 1, 1, 5695008),
(5695026, 81, '45', 'Citamol', 44, '', '', 1, 1, 5695008),
(5695035, 81, '46', 'Claritin', 45, '', '', 1, 1, 5695008),
(5695044, 81, '47', 'Cloramfenicol', 46, '', '', 1, 1, 5695008),
(5695053, 81, '48', 'Clyndomycin', 47, '', '', 1, 1, 5695008),
(5695062, 81, '49', 'codeine', 48, '', '', 1, 1, 5695008),
(5695071, 81, '50', 'compazine', 49, '', '', 1, 1, 5695008),
(5695080, 81, '51', 'Cortison (rash, angroedema)', 50, '', '', 1, 1, 5695008),
(5695089, 81, '52', 'Dexacort', 51, '', '', 1, 1, 5695008),
(5695098, 81, '53', 'Dilantin', 52, '', '', 1, 1, 5695008),
(5695107, 81, '54', 'Elavil', 53, '', '', 1, 1, 5695008),
(5695116, 81, '55', 'Eritromicina', 54, '', '', 1, 1, 5695008),
(5695125, 81, '56', 'erythancin', 55, '', '', 1, 1, 5695008),
(5695134, 81, '57', 'erythromycin', 56, '', '', 1, 1, 5695008),
(5695143, 81, '58', 'Flexeril', 57, '', '', 1, 1, 5695008),
(5695152, 81, '59', 'Furoxona', 58, '', '', 1, 1, 5695008),
(5695161, 81, '60', 'General Anestisia', 59, '', '', 1, 1, 5695008),
(5695170, 81, '61', 'Ibuprofen', 60, '', '', 1, 1, 5695008),
(5695179, 81, '62', 'Lantus', 61, '', '', 1, 1, 5695008),
(5695188, 81, '63', 'Levaquin', 62, '', '', 1, 1, 5695008),
(5695197, 81, '64', 'Lisinopril', 63, '', '', 1, 1, 5695008),
(5695206, 81, '65', 'Local anasthetic(Canbocaine??)', 64, '', '', 1, 1, 5695008),
(5695215, 81, '66', 'Losartan', 65, '', '', 1, 1, 5695008),
(5695224, 81, '67', 'Maxzide', 66, '', '', 1, 1, 5695008),
(5695233, 81, '68', 'Mentholation', 67, '', '', 1, 1, 5695008),
(5695242, 81, '69', 'morphine', 68, '', '', 1, 1, 5695008),
(5695251, 81, '70', 'motrin', 69, '', '', 1, 1, 5695008),
(5695260, 81, '71', 'naprocin', 70, '', '', 1, 1, 5695008),
(5695269, 81, '72', 'No Know Drug Allergies', 71, '', '', 1, 1, 5695008),
(5695278, 81, '73', 'Penicillin', 72, '', '', 1, 1, 5695008),
(5695287, 81, '74', 'Percocet', 73, '', '', 1, 1, 5695008),
(5695296, 81, '75', 'Prinivil', 74, '', '', 1, 1, 5695008),
(5695305, 81, '76', 'Quinine (Quinidine)', 75, '', '', 1, 1, 5695008),
(5695314, 81, '77', 'Relafen', 76, '', '', 1, 1, 5695008),
(5695323, 81, '78', 'rocephin', 77, '', '', 1, 1, 5695008),
(5695332, 81, '79', 'Sulfa', 78, '', '', 1, 1, 5695008),
(5695341, 81, '80', 'tegretol', 79, '', '', 1, 1, 5695008),
(5695350, 81, '81', 'Tetracycline', 80, '', '', 1, 1, 5695008),
(5695359, 81, '82', 'Thorazine', 81, '', '', 1, 1, 5695008),
(5695368, 81, '83', 'Codeine', 82, '', '', 1, 1, 5695008),
(5695377, 81, '84', 'tylenor/acetaminophen', 83, '', '', 1, 1, 5695008),
(5695386, 81, '85', 'Ultram', 84, '', '', 1, 1, 5695008),
(5695395, 81, '86', 'Vancomycin', 85, '', '', 1, 1, 5695008),
(5695404, 81, '87', 'Vasotec', 86, '', '', 1, 1, 5695008),
(5695413, 81, '88', 'Vioxx', 87, '', '', 1, 1, 5695008),
(5695422, 81, '89', 'xlonipin', 88, '', '', 1, 1, 5695008),
(5695431, 81, '90', 'Zestril', 89, '', '', 1, 1, 5695008),
(5695440, 81, '91', 'Zocor', 90, '', '', 1, 1, 5695008),
(5695449, 81, '92', 'Chemicals', 91, '', '', 1, 0, 0),
(5695467, 81, '94', 'iodine', 93, '', '', 1, 1, 5695449),
(5695476, 81, '95', 'chemicals', 94, '', '', 1, 1, 5695449),
(5695485, 81, '96', 'Iodine', 95, '', '', 0, 1, 5695449),
(5695494, 81, '97', 'Latex', 96, '', '', 1, 1, 5695449),
(5695503, 81, '98', 'metals', 97, '', '', 1, 1, 5695449),
(5695512, 81, '99', 'Peroxide', 98, '', '', 1, 1, 5695449),
(5695521, 81, '100', 'Potassium', 99, '', '', 1, 1, 5695449),
(5695530, 81, '101', 'Sodium', 100, '', '', 1, 1, 5695521),
(5695539, 81, '102', 'sodium pentathol', 101, '', '', 1, 1, 5695521),
(5695548, 81, '103', 'Environment', 102, '', '', 1, 0, 0),
(5695566, 81, '105', 'Dust', 104, '', '', 1, 1, 5695548),
(5695575, 81, '106', 'Hay fever', 105, '', '', 1, 1, 5695548),
(5695584, 81, '107', 'Pollen', 106, '', '', 1, 1, 5695548),
(5695593, 81, '108', 'Seasonal', 107, '', '', 1, 1, 5695548),
(5695602, 81, '109', 'sun', 108, '', '', 1, 1, 5695548),
(5695611, 81, '110', 'Other', 109, '', '', 1, 0, 0),
(5695634, 82, '1', 'DT (Diphtheria, Tetanus) (90702)', 1, '', '', 1, 0, 0),
(5695643, 82, '2', 'DTaP (Diphtheria, Tetanus, aPertussis) (90700)', 1, '', '', 1, 0, 0),
(5695652, 82, '3', 'Flu Vaccine (90655-90658)', 2, '', '', 1, 0, 0),
(5695661, 82, '4', 'Hepatitis B (90746)', 3, '', '', 1, 0, 0),
(5695670, 82, '5', 'Hepatitis B - 1st (90746)', 4, '', '', 1, 0, 0),
(5695679, 82, '6', 'Hepatitis B - 2nd (90746)', 5, '', '', 1, 0, 0),
(5695688, 82, '7', 'Hepatitis B - 3rd (90746)', 6, '', '', 1, 0, 0),
(5695697, 82, '8', 'Hib (Haem Influenza type b) (90645-90648)', 7, '', '', 1, 0, 0),
(5695706, 82, '9', 'MMR (Measles, Mumps, Rubella) (90707)', 8, '', '', 1, 0, 0),
(5695715, 82, '10', 'Pneumovax (90669, 90732)', 9, '', '', 1, 0, 0),
(5695724, 82, '11', 'IPV (Polio) (90713)', 10, '', '', 1, 0, 0),
(5695733, 82, '12', 'PPD (TB test) (86580)', 11, '', '', 1, 0, 0),
(5695742, 82, '13', 'Td (Tetanus, Diphtheria) (90718)', 12, '', '', 1, 0, 0),
(5695751, 82, '14', 'Tetanus toxoid (90703)', 13, '', '', 1, 0, 0),
(5695760, 82, '15', 'Tuberculosis (BCG) (90585)', 14, '', '', 1, 0, 0),
(5695769, 82, '16', 'Varicella (Chickenpox) (90716)', 15, '', '', 1, 0, 0),
(5695778, 82, '17', 'Blank', 16, '', '', 1, 0, 0),
(5695792, 83, '1', 'HIV/AIDS', 0, '', '', 1, 0, 0),
(5695801, 83, '2', 'Anemia', 0, '', '', 1, 0, 0),
(5695810, 83, '3', 'Arthritis', 0, '', '', 1, 0, 0),
(5695819, 83, '4', 'Asthma', 0, '', '', 1, 0, 0),
(5695828, 83, '5', 'Cancer', 0, '', '', 1, 0, 0),
(5695837, 83, '6', 'Diabetes', 0, '', '', 1, 0, 0),
(5695846, 83, '7', 'Emotional Prob', 0, '', '', 1, 0, 0),
(5695855, 83, '8', 'TB skin test?', 0, '', '', 1, 0, 0),
(5695864, 83, '9', 'Gallbladder', 0, '', '', 1, 0, 0),
(5695873, 83, '10', 'Heart Problems', 0, '', '', 1, 0, 0),
(5695882, 83, '11', 'Hepatitis/Liver', 0, '', '', 1, 0, 0),
(5695891, 83, '12', 'High Blood Pressure', 0, '', '', 1, 0, 0),
(5695900, 83, '13', 'High Cholesterol', 0, '', '', 1, 0, 0),
(5695909, 83, '14', 'Kidney Problems', 0, '', '', 1, 0, 0),
(5695918, 83, '15', 'Lung Problems', 0, '', '', 1, 0, 0),
(5695927, 83, '16', 'Allergies', 0, '', '', 1, 0, 0),
(5695936, 83, '17', 'Menstral Problems', 0, '', '', 1, 0, 0),
(5695945, 83, '18', 'Rheumatic Fever', 0, '', '', 1, 0, 0),
(5695954, 83, '19', 'Sexually transmitted disease', 0, '', '', 1, 0, 0),
(5695963, 83, '20', 'Stomach Problems', 0, '', '', 1, 0, 0),
(5695972, 83, '21', 'Stroke', 0, '', '', 1, 0, 0),
(5695981, 83, '22', 'Thyroid Problems', 0, '', '', 1, 0, 0),
(5695990, 83, '23', 'Tuberculosis', 0, '', '', 1, 0, 0),
(5695999, 83, '24', 'Blank', 0, '', '', 1, 0, 0),
(5696013, 84, '1', 'HIV/AIDS', 0, '', '', 1, 0, 0),
(5696022, 84, '2', 'Anemia', 0, '', '', 1, 0, 0),
(5696031, 84, '3', 'Arthritis', 0, '', '', 1, 0, 0),
(5696040, 84, '4', 'Asthma', 0, '', '', 1, 0, 0),
(5696049, 84, '5', 'Cancer', 0, '', '', 1, 0, 0),
(5696058, 84, '6', 'Diabetes', 0, '', '', 1, 0, 0),
(5696067, 84, '7', 'Emotional Prob', 0, '', '', 1, 0, 0),
(5696076, 84, '8', 'TB skin test?', 0, '', '', 1, 0, 0),
(5696085, 84, '9', 'Gallbladder', 0, '', '', 1, 0, 0),
(5696094, 84, '10', 'Heart Problems', 0, '', '', 1, 0, 0),
(5696103, 84, '11', 'Hepatitis/Liver', 0, '', '', 1, 0, 0),
(5696112, 84, '12', 'High Blood Pressure', 0, '', '', 1, 0, 0),
(5696121, 84, '13', 'High Cholesterol', 0, '', '', 1, 0, 0),
(5696130, 84, '14', 'Kidney Problems', 0, '', '', 1, 0, 0),
(5696139, 84, '15', 'Lung Problems', 0, '', '', 1, 0, 0),
(5696148, 84, '16', 'Allergies', 0, '', '', 1, 0, 0),
(5696157, 84, '17', 'Menstral Problems', 0, '', '', 1, 0, 0),
(5696166, 84, '18', 'Rheumatic Fever', 0, '', '', 1, 0, 0),
(5696175, 84, '19', 'Sexually transmitted disease', 0, '', '', 1, 0, 0),
(5696184, 84, '20', 'Stomach Problems', 0, '', '', 1, 0, 0),
(5696193, 84, '21', 'Stroke', 0, '', '', 1, 0, 0),
(5696202, 84, '22', 'Thyroid Problems', 0, '', '', 1, 0, 0);
INSERT INTO `enumeration_value` (`enumeration_value_id`, `enumeration_id`, `key`, `value`, `sort`, `extra1`, `extra2`, `status`, `depth`, `parent_id`) VALUES (5696211, 84, '23', 'Tuberculosis', 0, '', '', 1, 0, 0),
(5696220, 84, '24', 'Blank', 0, '', '', 1, 0, 0),
(5696234, 85, '1', 'Aunt or Uncle', 0, '', '', 1, 0, 0),
(5696243, 85, '2', 'Child (adoptive)', 0, '', '', 1, 0, 0),
(5696252, 85, '3', 'Child (biological)', 0, '', '', 1, 0, 0),
(5696261, 85, '4', 'Cousin', 0, '', '', 1, 0, 0),
(5696270, 85, '5', 'Grandchild', 0, '', '', 1, 0, 0),
(5696279, 85, '6', 'Grandparent (adoptive)', 0, '', '', 1, 0, 0),
(5696288, 85, '7', 'Grandparent (biological)', 0, '', '', 1, 0, 0),
(5696297, 85, '8', 'Half Sibling', 0, '', '', 1, 0, 0),
(5696306, 85, '9', 'Legal Guardian', 0, '', '', 1, 0, 0),
(5696315, 85, '10', 'Niece or Nephew', 0, '', '', 1, 0, 0),
(5696324, 85, '11', 'Other', 0, '', '', 1, 0, 0),
(5696333, 85, '12', 'Parent (adoptive)', 0, '', '', 1, 0, 0),
(5696342, 85, '13', 'Parent (biological)', 0, '', '', 1, 0, 0),
(5696351, 85, '14', 'Parent (step)', 0, '', '', 1, 0, 0),
(5696360, 85, '15', 'Sibling (adoptive)', 0, '', '', 1, 0, 0),
(5696369, 85, '16', 'Sibling (biological)', 0, '', '', 1, 0, 0),
(5696378, 85, '17', 'Spouse', 0, '', '', 1, 0, 0),
(5696387, 85, '18', 'Step child', 0, '', '', 1, 0, 0),
(5696396, 85, '19', 'Blank', 0, '', '', 1, 0, 0),
(5696410, 86, '1', 'Visit Payment', 0, '', '', 1, 0, 0),
(5696419, 86, '2', 'Lab Payment', 0, '', '', 1, 0, 0),
(5696428, 86, '3', 'Medications Payment', 0, '', '', 1, 0, 0),
(5696437, 86, '4', 'Correction Payment', 0, '', '', 1, 0, 0),
(5696446, 86, '5', 'Other', 0, '', '', 1, 0, 0),
(5699779, 87, '1', 'Pap Smear (88141)', 1, '', '', 1, 0, 0),
(5699788, 87, '2', 'Hemoglobin A1c (83037)', 1, '', '', 1, 0, 0),
(5699797, 87, '3', 'LDL cholestrol (83721)', 2, '', '', 1, 0, 0),
(5699806, 87, '4', 'PSA', 3, '', '', 1, 0, 0),
(5699815, 87, '5', 'Mammogram (76092)', 4, '', '', 1, 0, 0),
(5699824, 87, '6', 'Glucose fingerstick(82948)', 5, '', '', 1, 0, 0),
(5699833, 87, '7', 'Hemoccult/FOBT (82270)', 6, '', '', 1, 0, 0),
(5699842, 87, '8', 'Urine Dipstick (81002)', 7, '', '', 1, 0, 0),
(5699851, 87, '9', 'EKG w/o interpretation (93005)', 8, '', '', 1, 0, 0),
(5699860, 87, '10', 'ALT (84460)', 9, '', '', 1, 0, 0),
(5699869, 87, '11', 'AST (84450)', 10, '', '', 1, 0, 0),
(5699878, 87, '12', 'CBC w/diff', 11, '', '', 1, 0, 0),
(5699887, 87, '13', 'Cholesterol (82465)', 12, '', '', 1, 0, 0),
(5699896, 87, '14', 'Colonoscopy (44388)', 13, '', '', 1, 0, 0),
(5699905, 87, '15', 'Creatinine (82565)', 14, '', '', 1, 0, 0),
(5699914, 87, '16', 'Double Contrast Barrium Enema (74280)', 15, '', '', 1, 0, 0),
(5699923, 87, '17', 'Flexible Sigmoidoscopy (45330)', 16, '', '', 1, 0, 0),
(5699932, 87, '18', 'GC DNA probe', 17, '', '', 1, 0, 0),
(5699941, 87, '19', 'HDL Cholestrol (83718)', 18, '', '', 1, 0, 0),
(5699950, 87, '20', 'KOH', 19, '', '', 1, 0, 0),
(5699959, 87, '21', 'Microal/Creat Ratio (82043/82570)', 20, '', '', 1, 0, 0),
(5699968, 87, '22', 'MicroAlbumin Urine (82043)', 21, '', '', 1, 0, 0),
(5699977, 87, '23', 'Pap Smear (88141)', 22, '', '', 1, 0, 0),
(5699986, 87, '24', 'Potassium, serum (84132)', 23, '', '', 1, 0, 0),
(5699995, 87, '25', 'PPD (86580)', 24, '', '', 1, 0, 0),
(5700004, 87, '26', 'Pregnancy Urine (81025)', 25, '', '', 1, 0, 0),
(5700013, 87, '27', 'PT/INR', 26, '', '', 1, 0, 0),
(5700022, 87, '28', 'PTT (85730)', 27, '', '', 1, 0, 0),
(5700031, 87, '29', 'Strep Throat Culture', 28, '', '', 1, 0, 0),
(5700040, 87, '30', 'Strep, Rapid (36403)', 29, '', '', 1, 0, 0),
(5700049, 87, '31', 'Triglyceride(84478)', 30, '', '', 1, 0, 0),
(5700058, 87, '32', 'UA (81003)', 31, '', '', 1, 0, 0),
(5700067, 87, '33', 'Urine Culture (87086)', 32, '', '', 1, 0, 0),
(5700076, 87, '34', '24HrUrineProtein (84156)', 33, '', '', 1, 0, 0),
(5700090, 88, '1', 'Patient Reported', 2, '', '', 1, 0, 0),
(5700099, 88, '2', 'Other', 3, '', '', 1, 0, 0),
(5700108, 88, '3', 'Blank', 4, '', '', 1, 0, 0),
(5700122, 89, '1', 'Blood Panel', 0, '', '', 1, 0, 0),
(5700131, 89, '2', 'Lipid Panel', 2, '', '', 1, 0, 0),
(5700140, 89, '3', 'Liver Panel', 3, '', '', 1, 0, 0),
(5700149, 89, '4', 'Other', 4, '', '', 1, 0, 0),
(5700194, 90, '1', 'Colorectal Screening: Colonoscopy', 1, '', '', 1, 0, 0),
(5700203, 90, '2', 'Colorectal Screening: Double Contrast Barium Enema', 2, '', '', 1, 0, 0),
(5700212, 90, '3', 'Colorectal Screening: Flex Sig', 3, '', '', 1, 0, 0),
(5700221, 90, '4', 'Dental Exam', 4, '', '', 1, 0, 0),
(5700230, 90, '5', 'Diabetes Education', 5, '', '', 1, 0, 0),
(5700239, 90, '6', 'Diabetic Foot Check (LEAP)', 6, '', '', 1, 0, 0),
(5700248, 90, '7', 'EKG', 7, '', '', 1, 0, 0),
(5700257, 90, '8', 'Immunizations', 8, '', '', 1, 0, 0),
(5700266, 90, '9', 'Mammography:  Screening/Diagnostic', 9, '', '', 1, 0, 0),
(5700275, 90, '10', 'Pap Smear', 10, '', '', 1, 0, 0),
(5700284, 90, '11', 'Retinal Exam', 11, '', '', 1, 0, 0),
(5700293, 90, '12', 'Other', 12, '', '', 1, 0, 0),
(5700307, 91, '1', 'for SCC, Mobile Med, Proyecto Salud, PCWC, MCHP, Holy Cross, MCC Medical Clinic, Mercy, PCC', 32, '', '', 0, 0, 0),
(5700316, 91, '2', 'Follow Medical Plan', 1, '', '', 1, 0, 0),
(5700325, 91, '3', 'Check blood sugar', 1, '', '', 1, 1, 5700316),
(5700334, 91, '4', 'Check feet', 2, '', '', 1, 1, 5700316),
(5700343, 91, '5', 'Use inhaler', 3, '', '', 1, 1, 5700316),
(5700352, 91, '6', 'Take medicine as prescribed', 4, '', '', 1, 1, 5700316),
(5700361, 91, '7', 'Keep health care appointments', 5, '', '', 1, 1, 5700316),
(5700370, 91, '8', 'Complete recommended medical referrals', 6, '', '', 1, 1, 5700316),
(5700379, 91, '9', 'Other', 7, '', '', 1, 1, 5700316),
(5700388, 91, '10', 'Complete preventive screening', 8, '', '', 1, 0, 0),
(5700406, 91, '12', 'Mammography', 9, '', '', 1, 1, 5700388),
(5700415, 91, '13', 'Colonoscopy', 10, '', '', 1, 1, 5700388),
(5700424, 91, '14', 'Pap smear', 11, '', '', 1, 1, 5700388),
(5700433, 91, '15', 'Recommended Vaccinations', 12, '', '', 1, 1, 5700388),
(5700442, 91, '16', 'Other', 13, '', '', 1, 1, 5700388),
(5700451, 91, '17', 'Be physically active', 14, '', '', 1, 0, 0),
(5700460, 91, '18', 'General', 15, '', '', 1, 1, 5700451),
(5700469, 91, '19', 'Healthy Eating', 16, '', '', 1, 0, 0),
(5700478, 91, '20', 'General', 17, '', '', 1, 1, 5700469),
(5700487, 91, '21', 'Stop Smoking', 18, '', '', 1, 0, 0),
(5700496, 91, '22', 'Stop date', 19, '', '', 1, 1, 5700487),
(5700505, 91, '23', 'Quitline', 20, '', '', 1, 1, 5700487),
(5700514, 91, '24', 'Refer to classes', 21, '', '', 1, 1, 5700487),
(5700523, 91, '25', 'NRT', 22, '', '', 1, 1, 5700487),
(5700532, 91, '26', 'Other', 23, '', '', 1, 1, 5700487),
(5700541, 91, '27', 'Alcohol Consumption', 24, '', '', 1, 0, 0),
(5700550, 91, '28', 'General', 25, '', '', 1, 1, 5700541),
(5700559, 91, '29', 'Manage Stress', 26, '', '', 1, 0, 0),
(5700568, 91, '30', 'General', 27, '', '', 1, 1, 5700559),
(5700577, 91, '31', 'Education-Community Resources', 28, '', '', 1, 0, 0),
(5700586, 91, '32', 'General', 29, '', '', 1, 1, 5700577),
(5700595, 91, '33', 'Other', 30, '', '', 1, 0, 0),
(5700604, 91, '34', 'General', 31, '', '', 1, 1, 5700595),
(5700618, 92, '1', 'for AFC', 19, '', '', 0, 0, 0),
(5700627, 92, '2', 'Call Back', 1, '', '', 1, 0, 0),
(5700636, 92, '3', 'Check Progress', 1, '', '', 1, 0, 0),
(5700645, 92, '4', 'Fairfax Eye Check', 2, '', '', 0, 0, 0),
(5700654, 92, '5', 'Financial Screening', 3, '', '', 0, 0, 0),
(5700663, 92, '6', 'Lab Check', 4, '', '', 0, 0, 0),
(5700672, 92, '7', 'Radiology Check', 5, '', '', 0, 0, 0),
(5700681, 92, '8', 'Return to Clinic', 6, '', '', 0, 0, 0),
(5700690, 92, '9', 'Specialty Check', 7, '', '', 0, 0, 0),
(5700699, 92, '10', 'for SCC', 18, '', '', 0, 0, 0),
(5700708, 92, '11', 'N/A', 8, '', '', 0, 0, 0),
(5700717, 92, '12', 'Meds Pickup', 9, '', '', 0, 0, 0),
(5700726, 92, '13', 'Check Progress', 10, '', '', 0, 0, 0),
(5700735, 92, '14', 'Call Back', 11, '', '', 0, 0, 0),
(5700744, 92, '15', 'Converted', 12, '', '', 1, 0, 0),
(5700753, 92, '16', 'for Mobile Med, Proyecto Salud, MCC Medical Clinic,Holy Cross, PCWC, Mercy, MKHP, PCC, JSF (Herndon)', 20, '', '', 0, 0, 0),
(5700762, 92, '17', 'Call Back', 13, '', '', 0, 0, 0),
(5700771, 92, '18', 'Check Progress', 14, '', '', 0, 0, 0),
(5700780, 92, '19', 'Repeat Test', 15, '', '', 1, 0, 0),
(5700789, 92, '20', 'Other', 16, '', '', 1, 0, 0),
(5700798, 92, '21', 'N/A', 17, '', '', 1, 0, 0),
(5700836, 33, '6', 'Other', 5, '', '', 1, 0, 0),
(5700843, 33, '7', 'Unknown', 6, '', '', 1, 0, 0),
(5700850, 33, '8', 'Blank', 7, '', '', 0, 0, 0),
(5700865, 77, '4', 'Blank', 4, '', '', 1, 0, 0),
(5701151, 93, '', '', 0, '', '', 1, 0, 0),
(5701259, 94, '1', 'Provider', 0, '', '', 1, 0, 0),
(5701268, 94, '2', 'Non-Provider', 0, '', '', 1, 0, 0),
(5701277, 94, '3', 'Specialist', 0, '', '', 1, 0, 0),
(5701286, 94, '4', 'Medical Phone', 0, '', '', 1, 0, 0),
(5701295, 94, '5', 'Medication PU', 0, '', '', 1, 0, 0),
(5701304, 94, '6', 'Education', 0, '', '', 1, 0, 0),
(5701313, 94, '7', 'Eligibility', 0, '', '', 1, 0, 0),
(5701431, 95, '1', 'Provider', 1, '', '', 1, 0, 0),
(5701440, 95, '2', 'Non-Provider', 1, '', '', 1, 0, 0),
(5701449, 95, '3', 'Specialist', 2, '', '', 1, 0, 0),
(5701458, 95, '4', 'Medical Phone', 3, '', '', 1, 0, 0),
(5701467, 95, '5', 'Medication PU', 4, '', '', 1, 0, 0),
(5701476, 95, '6', 'Education', 5, '', '', 1, 0, 0),
(5701485, 95, '7', 'Eligibility', 6, '', '', 1, 0, 0),
(6032362, 81, '107', 'Other', 107, '', '', 1, 1, 5695611),
(6772808, 24, '4', 'Accompanied', 0, '', '', 1, 0, 0),
(6772815, 24, '5', 'Divorced', 1, '', '', 1, 0, 0),
(6772824, 24, '6', 'Not Specified', 3, '', '', 1, 0, 0),
(6772836, 24, '7', 'Unknown', 7, '', '', 1, 0, 0),
(6772843, 24, '8', 'Widowed', 8, '', '', 1, 0, 0),
(6772863, 21, '3', 'NPI', 3, '', '', 1, 0, 0),
(6772870, 21, '4', 'UPIN', 4, '', '', 1, 0, 0),
(6772877, 21, '5', 'Old MRN', 5, '', '', 1, 0, 0),
(6772897, 29, '5', 'Parent', 5, '', '', 1, 0, 0),
(7589253, 21, '6', 'DL', 6, '', '', 1, 0, 0),
(12629405, 26, '6', 'Alt', 6, '', '', 1, 0, 0),
(12629451, 78, '20', 'SCC DC Archived', 19, '', '', 1, 0, 0),
(12629458, 78, '21', 'SCC-DC Archived', 20, '', '', 1, 0, 0),
(13273049, 8, '7', 'Employer2', 7, '', '', 1, 0, 0),
(15308313, 28, '7', 'Correction Payment', 3, '', '', 1, 0, 0),
(15308320, 28, '8', 'Labs Payment', 1, '', '', 1, 0, 0),
(15308327, 28, '9', 'Medication Payment', 2, '', '', 1, 0, 0),
(15308334, 28, '10', 'Other', 4, '', '', 1, 0, 0),
(15308341, 28, '11', 'Visit Payment', 0, '', '', 1, 0, 0),
(17838750, 89, '5', '.', 1, '', '', 0, 0, 0),
(22151941, 15, '2', 'Registrar', 2, '', '', 1, 0, 0),
(27499348, 40, '3', 'No Status', 1, '', '', 1, 0, 0),
(27499355, 40, '1', 'Eligible', 2, '', '', 1, 0, 0),
(27499362, 40, '2', 'In-Eligible', 3, '', '', 1, 0, 0),
(27499369, 40, '4', 'Not Required', 4, '', '', 0, 0, 0),
(27499418, 6, '1', 'RQ', 1, '', '', 1, 0, 0),
(27499425, 6, '2', 'Q', 2, '', '', 1, 0, 0),
(27499432, 6, '3', 'I', 3, '', '', 1, 0, 0),
(27511354, 90, '13', '.', 0, '', '', 1, 0, 0),
(27511744, 96, '0', 'No', 1, '', '', 1, 0, 0),
(27511751, 96, '1', 'Yes', 2, '', '', 1, 0, 0),
(31100131, 97, '1', 'My Practice', 1, '', '', 1, 0, 0),
(31100138, 97, '2', 'My Building Only', 2, '', '', 1, 0, 0),
(31100145, 97, '3', 'Created By Me', 3, '', '', 1, 0, 0),
(31106558, 98, '1', 'Normal', 1, '', '', 1, 0, 0),
(31106565, 98, '2', 'Abnormal', 2, '', '', 1, 0, 0),
(31126536, 98, '3', '.', 0, '', '', 1, 0, 0),
(31178988, 88, '4', 'Quest', 1, '', '', 1, 0, 0),
(31178995, 88, '5', 'Labcorp', 1, '', '', 1, 0, 0),
(31184710, 43, '1', 'Y', 1, '', '', 1, 0, 0),
(31184717, 43, '2', 'N', 0, '', '', 1, 0, 0),
(31310117, 35, '53', 'MO', 25, '', '', 1, 0, 0),
(1000274, 102, '10', 'Unsafe Sex', 10, '', '', 1, 0, 0),
(1000267, 102, '9', 'Heredity', 9, '', '', 1, 0, 0),
(1000260, 102, '8', 'Occupation', 8, '', '', 1, 0, 0),
(1000253, 102, '7', 'Tobacco', 7, '', '', 1, 0, 0),
(31564268, 54, '7', 'Sunday', 7, '', '', 1, 0, 0),
(31692911, 100, '1', 'Staff', 1, '', '', 1, 0, 0),
(31692918, 100, '2', 'Clinic Admin', 2, '', '', 1, 0, 0),
(31692925, 100, '3', 'Supervisor', 3, '', '', 1, 0, 0),
(31797755, 67, '8', 'Other (Lab Summary)', 22, '', '', 1, 0, 0),
(31882005, 100, '4', 'Registrar', 4, '', '', 1, 0, 0),
(32020360, 88, '6', 'Suburban', 6, '', '', 1, 0, 0),
(32054168, 21, '7', 'Other', 0, '', '', 1, 0, 0),
(32084660, 90, '14', 'Plastic Surgery', 14, '', '', 1, 0, 0),
(32092567, 69, '19', 'Anne Arundel', 19, '', '', 1, 0, 0),
(32092574, 69, '20', 'Alexandria', 20, '', '', 1, 0, 0),
(32107326, 87, '35', 'Thyroid Simulating Hormone (84443)', 34, '', '', 1, 0, 0),
(32107335, 87, '36', 'EKG with Interpretation (93042)', 35, '', '', 1, 0, 0),
(32107345, 87, '37', 'Lipid Panel (80061)', 36, '', '', 1, 0, 0),
(32107354, 87, '38', 'Thyroid Function Panel  (84463/84439)', 37, '', '', 1, 0, 0),
(32107363, 87, '39', 'Liver Function Test (82247/82248/84075/84450/84460)', 38, '', '', 1, 0, 0),
(32107377, 87, '40', 'Echocardiogram (93307)', 39, '', '', 1, 0, 0),
(32272005, 101, '1', '', 1, '', '', 1, 0, 0),
(32272012, 101, '2', 'Yesterday', 2, '', '', 1, 0, 0),
(32272019, 101, '3', 'Today', 3, '', '', 1, 0, 0),
(32272026, 101, '4', 'This Week', 4, '', '', 1, 0, 0),
(32272033, 101, '5', 'Next Week', 5, '', '', 1, 0, 0),
(1000246, 102, '6', 'Phys. Inactivity', 6, '', '', 1, 0, 0),
(1000239, 102, '5', 'BMI', 5, '', '', 1, 0, 0),
(1000232, 102, '4', 'Nutrition', 4, '', '', 1, 0, 0),
(1000225, 102, '3', 'Cholesterol', 3, '', '', 1, 0, 0),
(1000218, 102, '2', 'Blood Pressure', 2, '', '', 1, 0, 0),
(1000211, 102, '1', 'Alcohol', 1, '', '', 1, 0, 0);


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
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (1, '11', 'Office');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (2, '12', 'Home');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (3, '21', 'Inpatient Hospital');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (4, '22', 'Outpatient Hospital');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (5, '23', 'Emergency Room - Hospital');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (6, '24', 'Ambulatory Surgical Center');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (7, '25', 'Birthing Center');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (8, '26', 'Military Treatment Facility');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (9, '31', 'Skilled Nursing Facility');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (10, '32', 'Nursing Facility');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (11, '33', 'Custodial Care Facility');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (12, '34', 'Hospice');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (13, '41', 'Ambulance - Land');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (14, '42', 'Ambulance - Air or Water');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (15, '51', 'Inpatient Psychiatric Facility');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (16, '52', 'Psychiatric Facility Partial Hospitalization');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (17, '53', 'Community Mental Health Center');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (18, '54', 'Intermediate Care Facility/Mentally Retarded');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (19, '55', 'Residential Substance Abuse Treatment Facility');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (20, '56', 'Psychiatric Residential Treatment Center');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (21, '50', 'Federally Qualified Health Center');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (22, '60', 'Mass Immunization Center');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (23, '61', 'Comprehensive Inpatient Rehabilitation Facility');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (24, '62', 'Comprehensive Outpatient Rehabilitation Facility');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (25, '65', 'End Stage Renal Disease Treatment Facility');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (26, '71', 'State or Local Public Health Clinic');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (27, '72', 'Rural Health Clinic');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (28, '81', 'Independent Laboratory');
INSERT INTO `facility_codes` (`facility_code_id`, `code`, `name`) VALUES (29, '99', 'Other Unlisted Facility');

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
  `modifier` varchar(255) NOT NULL default '',
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

INSERT INTO `gacl_acl` (`id`, `section_value`, `allow`, `enabled`, `return_value`, `note`, `updated_date`) VALUES (3, 'system', 1, 1, '', '', 1189815214);

-- --------------------------------------------------------

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

INSERT INTO `gacl_acl_sections` (`id`, `value`, `order_value`, `name`, `hidden`) VALUES (1, 'system', 0, 'System', 0);

-- --------------------------------------------------------

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

INSERT INTO `gacl_acl_seq` (`id`) VALUES (3);

-- --------------------------------------------------------

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

INSERT INTO `gacl_aco` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (1, 'actions', 'view', 1, 'view', 0);
INSERT INTO `gacl_aco` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (2, 'actions', 'edit', 2, 'edit', 0);
INSERT INTO `gacl_aco` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (3, 'actions', 'add', 3, 'add', 0);
INSERT INTO `gacl_aco` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (4, 'actions', 'delete', 4, 'delete', 0);
INSERT INTO `gacl_aco` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (5, 'actions', 'usage', 5, 'usage', 0);
INSERT INTO `gacl_aco` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (6, 'actions', 'uploadFile', 6, 'Upload A file', 0);
INSERT INTO `gacl_aco` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (7, 'actions', 'delete_owner', 7, 'Delete Owner', 0);
INSERT INTO `gacl_aco` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (8, 'actions', 'edit_owner', 8, 'Edit Owner', 0);
INSERT INTO `gacl_aco` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (9, 'actions', 'double_book', 9, 'Double Book Appointment', 0);
INSERT INTO `gacl_aco` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (10, 'actions', 'override', 10, 'override', 0);
INSERT INTO `gacl_aco` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (11, 'actions', 'list', 11, 'list', 0);

-- --------------------------------------------------------

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

INSERT INTO `gacl_aco_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'actions', 'add');
INSERT INTO `gacl_aco_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'actions', 'delete');
INSERT INTO `gacl_aco_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'actions', 'delete_owner');
INSERT INTO `gacl_aco_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'actions', 'double_book');
INSERT INTO `gacl_aco_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'actions', 'edit');
INSERT INTO `gacl_aco_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'actions', 'edit_owner');
INSERT INTO `gacl_aco_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'actions', 'list');
INSERT INTO `gacl_aco_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'actions', 'override');
INSERT INTO `gacl_aco_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'actions', 'uploadFile');
INSERT INTO `gacl_aco_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'actions', 'usage');
INSERT INTO `gacl_aco_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'actions', 'view');

-- --------------------------------------------------------

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

INSERT INTO `gacl_aco_sections` (`id`, `value`, `order_value`, `name`, `hidden`) VALUES (1, 'actions', 0, 'Actions', 0);

-- --------------------------------------------------------

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

INSERT INTO `gacl_aco_sections_seq` (`id`) VALUES (1);

-- --------------------------------------------------------

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

INSERT INTO `gacl_aco_seq` (`id`) VALUES (11);

-- --------------------------------------------------------

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

INSERT INTO `gacl_aro` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (1, 'users', 'admin', 1, 'admin', 0);

-- --------------------------------------------------------

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

INSERT INTO `gacl_aro_groups` (`id`, `parent_id`, `lft`, `rgt`, `name`, `value`) VALUES (2, 0, 1, 12, 'Roles', 'roles');
INSERT INTO `gacl_aro_groups` (`id`, `parent_id`, `lft`, `rgt`, `name`, `value`) VALUES (3, 2, 2, 3, 'System Admin', 'superadmin');
INSERT INTO `gacl_aro_groups` (`id`, `parent_id`, `lft`, `rgt`, `name`, `value`) VALUES (6, 2, 4, 5, 'Provider', 'role_provider');
INSERT INTO `gacl_aro_groups` (`id`, `parent_id`, `lft`, `rgt`, `name`, `value`) VALUES (7, 2, 6, 7, 'Front Office', 'front_office');
INSERT INTO `gacl_aro_groups` (`id`, `parent_id`, `lft`, `rgt`, `name`, `value`) VALUES (8, 2, 8, 9, 'Billing User', 'billing_user');
INSERT INTO `gacl_aro_groups` (`id`, `parent_id`, `lft`, `rgt`, `name`, `value`) VALUES (9, 2, 10, 11, 'Staff', 'staff');

-- --------------------------------------------------------

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

INSERT INTO `gacl_aro_groups_id_seq` (`id`) VALUES (10);

-- --------------------------------------------------------

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

INSERT INTO `gacl_aro_groups_map` (`acl_id`, `group_id`) VALUES (3, 6);
INSERT INTO `gacl_aro_groups_map` (`acl_id`, `group_id`) VALUES (3, 7);
INSERT INTO `gacl_aro_groups_map` (`acl_id`, `group_id`) VALUES (3, 8);
INSERT INTO `gacl_aro_groups_map` (`acl_id`, `group_id`) VALUES (3, 9);

-- --------------------------------------------------------

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


-- --------------------------------------------------------

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

INSERT INTO `gacl_aro_sections` (`id`, `value`, `order_value`, `name`, `hidden`) VALUES (1, 'users', 1, 'Users', 0);

-- --------------------------------------------------------

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

INSERT INTO `gacl_aro_sections_seq` (`id`) VALUES (1);

-- --------------------------------------------------------

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

INSERT INTO `gacl_aro_seq` (`id`) VALUES (1);

-- --------------------------------------------------------

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

INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (2, 'resources', 'ie7', 10, 'Section - Ie7', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (3, 'resources', 'images', 10, 'Section - Images', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (4, 'resources', 'enumeration', 10, 'Section - Enumeration', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (5, 'resources', 'ajax', 10, 'Section - Ajax', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (6, 'resources', 'main', 10, 'Section - Main', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (7, 'resources', 'crud', 10, 'Section - CRUD', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (8, 'resources', 'pdf', 10, 'Section - PDF', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (9, 'resources', 'base_access', 10, 'Section - Base_Access', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (10, 'resources', 'default', 10, 'Section - Default', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (11, 'resources', 'print', 10, 'Section - Print', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (12, 'resources', 'css', 10, 'Section - Css', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (13, 'resources', 'minimal', 10, 'Section - Minimal', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (14, 'resources', 'cronable', 10, 'Section - Cronable', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (15, 'resources', 'user', 10, 'Section - User', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (16, 'resources', 'access', 10, 'Section - Access', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (17, 'resources', 'insurance', 10, 'Section - Insurance', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (18, 'resources', 'form', 10, 'Section - Form', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (19, 'resources', 'account', 10, 'Section - Account', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (20, 'resources', 'patientpaymentplan', 10, 'Section - PatientPaymentPlan', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (21, 'resources', 'payergroup', 10, 'Section - PayerGroup', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (22, 'resources', 'room', 10, 'Section - Room', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (23, 'resources', 'appointmenttemplate', 10, 'Section - AppointmentTemplate', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (24, 'resources', 'building', 10, 'Section - Building', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (25, 'resources', 'self_mgmt_goals', 10, 'Section - Self_Mgmt_Goals', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (26, 'resources', 'appointment', 10, 'Section - Appointment', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (27, 'resources', 'billing', 10, 'Section - Billing', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (28, 'resources', 'quicklist', 10, 'Section - QuickList', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (29, 'resources', 'medicalhistory', 10, 'Section - MedicalHistory', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (30, 'resources', 'thumbnail', 10, 'Section - Thumbnail', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (31, 'resources', 'selfmgmtgoals', 10, 'Section - SelfMgmtGoals', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (32, 'resources', 'participationprogram', 10, 'Section - ParticipationProgram', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (33, 'resources', 'coding', 10, 'Section - Coding', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (34, 'resources', 'docs', 10, 'Section - Docs', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (35, 'resources', 'patientmerge', 10, 'Section - PatientMerge', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (36, 'resources', 'masteraccounthistory', 10, 'Section - MasterAccountHistory', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (37, 'resources', 'eob', 10, 'Section - Eob', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (38, 'resources', 'report', 10, 'Section - Report', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (39, 'resources', 'preferences', 10, 'Section - Preferences', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (40, 'resources', 'myaccount', 10, 'Section - MyAccount', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (41, 'resources', 'patient', 10, 'Section - Patient', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (42, 'resources', 'codingtemplate', 10, 'Section - CodingTemplate', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (43, 'resources', 'patientstatistics', 10, 'Section - PatientStatistics', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (44, 'resources', 'appointmentruleset', 10, 'Section - AppointmentRuleset', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (45, 'resources', 'occurencebreakdown', 10, 'Section - OccurenceBreakdown', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (46, 'resources', 'clinicalsummary', 10, 'Section - ClinicalSummary', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (47, 'resources', 'formrule', 10, 'Section - FormRule', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (48, 'resources', 'superbill', 10, 'Section - Superbill', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (49, 'resources', 'duplicatefinder', 10, 'Section - DuplicateFinder', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (50, 'resources', 'patientfinder', 10, 'Section - PatientFinder', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (51, 'resources', 'admin', 10, 'Section - Admin', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (52, 'resources', 'feeschedulediscount', 10, 'Section - FeeScheduleDiscount', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (53, 'resources', 'patientdashboard', 10, 'Section - PatientDashboard', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (54, 'resources', 'secondarypractice', 10, 'Section - SecondaryPractice', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (55, 'resources', 'claimhistory', 10, 'Section - ClaimHistory', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (56, 'resources', 'personperson', 10, 'Section - PersonPerson', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (57, 'resources', 'codecategory', 10, 'Section - CodeCategory', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (58, 'resources', 'splash', 10, 'Section - Splash', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (59, 'resources', 'location', 10, 'Section - Location', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (60, 'resources', 'summaryreport', 10, 'Section - SummaryReport', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (61, 'resources', 'widgetform', 10, 'Section - WidgetForm', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (62, 'resources', 'visitqueue', 10, 'Section - VisitQueue', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (63, 'resources', 'medicaleligibility', 10, 'Section - MediCalEligibility', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (64, 'resources', 'document', 10, 'Section - Document', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (65, 'resources', 'criticalview', 10, 'Section - CriticalView', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (66, 'resources', 'schedule', 10, 'Section - Schedule', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (67, 'resources', 'practice', 10, 'Section - Practice', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (68, 'resources', 'feeschedule', 10, 'Section - FeeSchedule', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (69, 'resources', 'documentcategory', 10, 'Section - DocumentCategory', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (70, 'resources', 'tabstate', 10, 'Section - TabState', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (71, 'resources', 'auditlog', 10, 'Section - AuditLog', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (72, 'resources', 'encounter', 10, 'Section - Encounter', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (73, 'resources', 'test', 10, 'Section - Test', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (74, 'resources', 'claim', 10, 'Section - Claim', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (75, 'resources', 'queue', 10, 'Section - Queue', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (76, 'resources', 'freebgateway', 10, 'Section - FreeBGateway', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (77, 'resources', 'labimporter', 10, 'Section - LabImporter', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (78, 'resources', 'labs', 10, 'Section - Labs', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (79, 'resources', 'x12import', 10, 'Section - X12Import', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (80, 'resources', 'x12apply', 10, 'Section - X12Apply', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (81, 'resources', 'calendardisplay', 10, 'Section - CalendarDisplay', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (82, 'resources', 'calendarevent', 10, 'Section - CalendarEvent', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (83, 'resources', 'calendaroccurence', 10, 'Section - CalendarOccurence', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (84, 'resources', 'calendarajaxevent', 10, 'Section - CalendarAJAXEvent', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (85, 'resources', 'chllabtests', 10, 'Section - Chllabtests', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (86, 'resources', 'altnotice', 10, 'Section - Altnotice', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (87, 'resources', 'refpractice', 10, 'Section - Refpractice', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (88, 'resources', 'refappointment', 10, 'Section - Refappointment', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (89, 'resources', 'altnoticelist', 10, 'Section - Altnoticelist', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (90, 'resources', 'referralattachment', 10, 'Section - ReferralAttachment', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (91, 'resources', 'referral', 10, 'Section - Referral', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (92, 'resources', 'refvisit', 10, 'Section - Refvisit', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (93, 'resources', 'refprogram', 10, 'Section - Refprogram', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (94, 'resources', 'refpatienteligibility', 10, 'Section - Refpatienteligibility', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (95, 'resources', 'refpatient', 10, 'Section - Refpatient', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (96, 'resources', 'refreporting', 10, 'Section - Refreporting', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (97, 'resources', 'docsmartstorable', 10, 'Section - DocSmartStorable', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (98, 'resources', 'docsmartfolder', 10, 'Section - DocSmartFolder', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (99, 'resources', 'docsmart', 10, 'Section - DocSmart', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (100, 'resources', 'main_calendar', 1, 'Main Group Calendar', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (101, 'resources', 'main_billing', 2, 'Main Group Billing', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (102, 'resources', 'main_patient', 3, 'Main Group Patient', 0);
INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (103, 'resources', 'main_admin', 4, 'Main Group Admin', 0);

-- --------------------------------------------------------

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


-- --------------------------------------------------------

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

INSERT INTO `gacl_axo_groups_id_seq` (`id`) VALUES (1);

-- --------------------------------------------------------

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


-- --------------------------------------------------------

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

INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'access');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'account');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'ajax');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'appointment');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'auditlog');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'base_access');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'calendarajaxevent');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'calendardisplay');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'calendarevent');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'calendaroccurence');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'chllabtests');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'claim');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'claimhistory');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'clinicalsummary');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'codecategory');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'coding');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'codingtemplate');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'criticalview');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'crud');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'css');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'default');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'docs');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'docsmart');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'docsmartfolder');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'docsmartstorable');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'document');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'documentcategory');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'duplicatefinder');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'encounter');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'eob');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'feeschedule');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'feeschedulediscount');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'form');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'formrule');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'freebgateway');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'ie7');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'images');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'insurance');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'labimporter');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'labs');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'location');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'main');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'main_billing');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'main_calendar');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'main_patient');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'masteraccounthistory');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'medicaleligibility');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'medicalhistory');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'minimal');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'myaccount');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'occurencebreakdown');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'participationprogram');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'patient');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'patientdashboard');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'patientfinder');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'patientmerge');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'patientpaymentplan');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'patientstatistics');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'payergroup');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'pdf');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'personperson');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'practice');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'preferences');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'print');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'queue');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'quicklist');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'refappointment');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'referral');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'referralattachment');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'refpatient');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'refpatienteligibility');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'refpractice');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'refprogram');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'refreporting');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'refvisit');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'report');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'room');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'schedule');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'secondarypractice');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'selfmgmtgoals');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'self_mgmt_goals');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'splash');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'summaryreport');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'superbill');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'tabstate');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'test');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'thumbnail');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'visitqueue');
INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (3, 'resources', 'widgetform');

-- --------------------------------------------------------

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

INSERT INTO `gacl_axo_sections` (`id`, `value`, `order_value`, `name`, `hidden`) VALUES (2, 'resources', 10, 'Resources', 0);

-- --------------------------------------------------------

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

INSERT INTO `gacl_axo_sections_seq` (`id`) VALUES (2);

-- --------------------------------------------------------

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

INSERT INTO `gacl_axo_seq` (`id`) VALUES (104);

-- --------------------------------------------------------

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

INSERT INTO `gacl_groups_aro_map` (`group_id`, `aro_id`) VALUES (3, 1);

-- --------------------------------------------------------

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


-- --------------------------------------------------------

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
  `extra` varchar(255) NOT NULL,
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

INSERT INTO `practice_setting` (`practice_setting_id`, `practice_id`, `name`, `value`, `serialized`) VALUES (900071, 900001, 'CalendarIncrement', '900', 0);
INSERT INTO `practice_setting` (`practice_setting_id`, `practice_id`, `name`, `value`, `serialized`) VALUES (900075, 900001, 'FacilityType', '0', 0);


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

INSERT INTO `practices` (`id`, `name`, `website`, `identifier`) VALUES (900001, 'Practice Name Here', '', '');

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
INSERT INTO `record_sequence` VALUES (1000);
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
INSERT INTO `reports` (`id`, `dbase`, `user`, `label`, `query`, `description`, `custom_id`) VALUES (200000, '', '', 'Arrivals', "select  concat('<a onclick=\"setTimeout('window.location.href=window.location.href',2000);\" target=\"_NEW\" href=\"/index.php/main/Encounter/Edit/0?appointment_id=',ap.appointment_id,'&patient_id=',p.person_id,'\">',p.last_name, ', ',p.first_name,'</a>') as Patient,  concat(prov.first_name, ' ', prov.last_name) as Provider, concat_ws('->',prac.name,b.name, r.name) as 'Location'  from appointment ap  inner join person p on p.person_id = ap.patient_id  inner join person prov on prov.person_id = ap.provider_id  left join person_address pa on pa.person_id  = prov.person_id and pa.address_type=7 left join address a on a.address_id = pa.address_id inner join rooms r on r.id = ap.room_id  inner join buildings b on b.id = r.building_id  inner join practices prac on prac.id = b.practice_id  inner join event ev on ev.event_id = ap.event_id left join encounter enc on enc.occurence_id = ap.appointment_id where enc.occurence_id IS NULL and ap.arrived = 1 and prac.id = '[practice:query:select prac.id, prac.name as practice from practices prac order by prac.name]' and  if (prac.name = 'Oncology Specialties, PC - CCI Drive' and length('[department]') > 0,a.name = '[department:query:select name, name as name2 from address where name='vitals1' or name='vitals2' group by name]', 1) and ev.start >= date_format(now(),'%Y-%m-%d 00:00:00') and ev.end <= date_format(now(),'%Y-%m-%d 23:59:59') and ap.appointment_code != \"CAN\"", 'Provides a list of patients who have been marked as \'arrived\' but do not yet have an encounter', 'arrivals');
INSERT INTO `reports` (`id`, `dbase`, `user`, `label`, `query`, `description`, `custom_id`) VALUES (200001, '', '', 'Room Use', "SELECT  date_format(e.start,'%m/%d/%y %H:%i') time,  concat(floor((unix_timestamp(e.end) - unix_timestamp(e.start)) / 60 / 60),    ' hrs ', floor((unix_timestamp(e.end) - unix_timestamp(e.start)) / 60 % 60), ' mins') AS Duration,  room.name `Room`,  concat_ws(', ', per.last_name, per.first_name) AS Patient,  pat.record_number '#',  lang_enum.value AS Lang,  num.number AS Phone,  ifnull(concat(relper.last_name,', ', relper.first_name),'Self') AS Guarantor,  ifnull(reason_enum_pp.value,reason_enum_default.value) AS Reason,  appt.title AS Note,  concat_ws(', ', pro.last_name, pro.first_name) AS Provider,  ifnull(bal.total_balance,'NA') AS Balance,  ifnull(lastpay.payment_date,'NA') AS LastPayment,  enc_patients.status new  FROM person AS per  LEFT JOIN patient_statistics AS patstat ON per.person_id = patstat.person_id  LEFT JOIN person_number AS pernum ON pernum.person_id = per.person_id  LEFT JOIN number AS num ON pernum.number_id = num.number_id AND num.number_type=1 AND num.active=1  LEFT JOIN person_person AS perper ON perper.person_id = per.person_id AND perper.guarantor=1 AND perper.guarantor_priority=1  LEFT JOIN person AS relper ON perper.related_person_id = relper.person_id  INNER JOIN appointment AS appt ON per.person_id = appt.patient_id  LEFT JOIN event AS e ON appt.event_id = e.event_id  LEFT JOIN person AS pro ON appt.provider_id = pro.person_id  LEFT JOIN patient as pat on per.person_id = pat.person_id  LEFT JOIN rooms as room on appt.room_id = room.id  LEFT JOIN (    SELECT      e.patient_id,      (SUM(IFNULL(total_billed,0)) - (SUM(IFNULL(total_paid,0)) + SUM(IFNULL(writeoffs.writeoff,0)))) AS total_balance    FROM encounter AS e      INNER JOIN clearhealth_claim AS cc USING(encounter_id)      LEFT JOIN (        SELECT foreign_id, SUM(IFNULL(writeoff,0)) AS writeoff        FROM payment        WHERE encounter_id = 0        GROUP BY foreign_id      ) AS writeoffs ON (writeoffs.foreign_id = cc.claim_id) GROUP BY e.patient_id  ) AS bal on bal.patient_id = appt.patient_id   LEFT JOIN ( select  patient_id,   max(payment_date ) payment_date from  (SELECT   e.patient_id,   max( p.payment_date ) payment_date FROM payment AS p   INNER JOIN clearhealth_claim AS cc ON p.foreign_id = cc.claim_id   INNER JOIN insurance_program AS ip ON ip.insurance_program_id = p.payer_id AND ip.name = 'Self Pay'   INNER JOIN company AS comp ON ip.company_id = comp.company_id AND comp.name = 'System'   INNER JOIN encounter AS e ON e.encounter_id = cc.encounter_id GROUP BY    e.patient_id union SELECT    e.patient_id,    max( p.payment_date ) payment_date FROM payment AS p   INNER JOIN encounter AS e ON e.encounter_id = p.encounter_id GROUP BY    e.patient_id ) payment_dates GROUP BY    patient_id  ) AS lastpay on lastpay.patient_id = appt.patient_id  left join ( select  p.person_id patient_id,  if (max(ifnull(encounter_id,0)),'N','Y') status from  patient p  LEFT JOIN encounter e ON e.patient_id = p.person_id group by  p.person_id ) enc_patients ON enc_patients.patient_id = appt.patient_id  left join ( 	select evp.practice_id, `key`, value from enumeration_value ev  	inner join enumeration_definition ed using(enumeration_id)  	inner join enumeration_value_practice evp on ev.enumeration_value_id = evp.enumeration_value_id 	where ed.name = 'appointment_reasons' ) reason_enum_pp ON (reason_enum_pp.practice_id = appt.practice_id) and appt.reason = reason_enum_pp.`key` left join ( 	select `key`, value from enumeration_value ev  	inner join enumeration_definition ed using(enumeration_id)  	left join enumeration_value_practice evp on ev.enumeration_value_id = evp.enumeration_value_id 	where ed.name = 'appointment_reasons' and evp.practice_id is null ) reason_enum_default ON appt.reason = reason_enum_default.`key` left join ( 	select `key`, value from enumeration_value ev  	inner join enumeration_definition ed using(enumeration_id)  	where ed.name = 'language' ) lang_enum ON patstat.language = lang_enum.`key`    /* end from */ where e.start >= '[start:date] 01:01:01' and e.end <= '[end:date] 23:59:59' and 	if( 		LENGTH('[room]') > 0, 		room.id = '[room:query:select r.id, concat(b.name,'->',r.name) name from rooms r inner join buildings b on b.id = r.building_id order by b.name, r.name]', 		1 	) and	if ( 		LENGTH('[provider]') > 0, 			pro.person_id = '[provider:query:select p.person_id, concat(p.last_name,', ',p.first_name) from person p inner join provider pr using(person_id)]', 		1 	) ", 'Show appointments by provider and location', 'room_use'); 
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
INSERT INTO `rooms` (`id`, `description`, `number_seats`, `building_id`, `name`, `color`) VALUES (900101, '', 100, 900079, 'TX', 'FFF8DC');

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

INSERT INTO `summary_columns` (`widget_form_id`, `type`, `name`, `summary_column_id`, `pretty_name`, `table_name`) VALUES (1000087, NULL, 'allergy_name', 1, 'allergy', 'storage_string'),
(1000094, NULL, 'chronic_care', 1, 'Condition', 'storage_string'),
(1000101, NULL, 'medication_name', 1, 'Med', 'storage_string'),
(1000101, NULL, 'medication_dose', 2, 'Dose', 'storage_string'),
(1000118, NULL, 'risk', 1, 'Risk', 'storage_string');
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
INSERT INTO `widget_form` (`widget_form_id`, `name`, `form_id`, `type`, `controller_name`, `show_on_medical_history`) VALUES (1000003, 'Self Management Goals', 14, 4, 'SelfMgmtGoals', 1),
(1000017, 'Physical Exams', 1, 5, '', 1),
(1000024, 'Subjective', 2, 5, '', 1),
(1000031, 'Objective', 3, 5, '', 1),
(1000038, 'Assessment', 4, 5, '', 1),
(1000045, 'Plan', 5, 5, '', 1),
(1000052, 'Immunizations', 6, 6, '', 1),
(1000066, 'Social History', 7, 6, '', 1),
(1000073, 'Family History of Disease', 8, 6, '', 1),
(1000080, 'Previous Illness', 9, 6, '', 1),
(1000087, 'Allergies', 10, 2, '', 1),
(1000094, 'Chronic Care', 12, 3, 'QuickList', 1),
(1000101, 'Medications', 11, 2, '', 1),
(1000118, 'Risks', 13, 3, '', 1);
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

/***
SQL Changes from 2.1 to 2.2
***/


ALTER TABLE `appointment_ruleset` ADD COLUMN `any` tinyint(4) NOT NULL;
ALTER TABLE `coding_data` CHANGE COLUMN `modifier` `modifier` int(11) NOT NULL default '0';
ALTER TABLE `fbclaimline` CHANGE COLUMN `modifier` `modifier` varchar(4) NOT NULL default '';
ALTER TABLE `gacl_acl_seq` DROP PRIMARY KEY;
ALTER TABLE `gacl_aco_sections_seq` DROP PRIMARY KEY;
ALTER TABLE `gacl_aco_seq` DROP PRIMARY KEY;
ALTER TABLE `gacl_aro_groups_id_seq` DROP PRIMARY KEY;
ALTER TABLE `gacl_aro_sections_seq` DROP PRIMARY KEY;
ALTER TABLE `gacl_aro_seq` DROP PRIMARY KEY;
ALTER TABLE `gacl_axo_groups_id_seq` DROP PRIMARY KEY;
ALTER TABLE `gacl_axo_sections_seq` DROP PRIMARY KEY;
ALTER TABLE `gacl_axo_seq` DROP PRIMARY KEY;
ALTER TABLE `hl7_message` DROP COLUMN `hl7_message_id`;
ALTER TABLE `hl7_message` DROP COLUMN `type`;
ALTER TABLE `hl7_message` DROP COLUMN `processed`;
ALTER TABLE `hl7_message` ADD COLUMN `id` int(11) NOT NULL default '0';
ALTER TABLE `hl7_message` DROP PRIMARY KEY;
ALTER TABLE `hl7_message` ADD PRIMARY KEY (`id`);
ALTER TABLE `hl7_message` DROP INDEX ;
ALTER TABLE `lab_note` ADD INDEX `lab_test_id` (`lab_test_id`);
ALTER TABLE `lab_order` ADD INDEX `external_id` (`external_id`);
ALTER TABLE `lab_order` ADD INDEX `patient_id` (`patient_id`);
ALTER TABLE `lab_result` DROP COLUMN `extra`;
ALTER TABLE `lab_result` ADD INDEX `description` (`description`);
ALTER TABLE `lab_result` ADD INDEX `lab_test_id` (`lab_test_id`);
ALTER TABLE `lab_test` ADD INDEX `lab_order_id` (`lab_order_id`);
ALTER TABLE `storage_int` ADD `array_index` TINYINT NOT NULL ;
ALTER TABLE `storage_int` DROP PRIMARY KEY ,
ALTER TABLE `storage_date` ADD `array_index` TINYINT NOT NULL ;
ALTER TABLE `storage_date` DROP PRIMARY KEY ,
ALTER TABLE `storage_string` ADD `array_index` TINYINT NOT NULL ;
ALTER TABLE `storage_string` DROP PRIMARY KEY ,
ALTER TABLE `storage_text` ADD `array_index` TINYINT NOT NULL ;
ALTER TABLE `storage_text` DROP PRIMARY KEY ,
ALTER TABLE `storage_date` ADD INDEX `value_key` (`value_key`);
ALTER TABLE `storage_date` ADD INDEX `foreign_key` (`foreign_key`);
ALTER TABLE `storage_int` ADD INDEX `value_key` (`value_key`);
ALTER TABLE `storage_int` ADD INDEX `foreign_key` (`foreign_key`);
ALTER TABLE `storage_string` ADD INDEX `value_key` (`value_key`);
ALTER TABLE `storage_text` ADD INDEX `value_key` (`value_key`);
ADD PRIMARY KEY ( `foreign_key` , `value_key` , `array_index` );
ALTER TABLE `widget_form` ADD COLUMN `report_id` int(11) NOT NULL;
ALTER TABLE `widget_form` ADD INDEX `report_id` (`report_id`);
ALTER TABLE `encounter_value` ADD INDEX ( `value_type` );
CREATE TABLE `graph_definition` (
  `graph_definition_id` int(11) NOT NULL,
  `external_id` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `font_size` int(11) NOT NULL,
  `font_type` varchar(255) NOT NULL,
  `font_file` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `title_size` int(11) NOT NULL,
  `canvas` varchar(15) NOT NULL,
  `plot_area` varchar(255) NOT NULL,
  `graph_type` varchar(255) NOT NULL,
  `querylinks` varchar(255) NOT NULL,
  PRIMARY KEY  (`graph_definition_id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;




