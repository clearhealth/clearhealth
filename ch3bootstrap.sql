-- phpMyAdmin SQL Dump
-- version 3.0.1.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 31, 2009 at 03:19 PM
-- Server version: 5.0.45
-- PHP Version: 5.2.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `clearhealth`
--

-- --------------------------------------------------------

--
-- Table structure for table `account_note`
--

CREATE TABLE IF NOT EXISTS `account_note` (
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

-- --------------------------------------------------------

--
-- Table structure for table `aclModules`
--

CREATE TABLE IF NOT EXISTS `aclModules` (
  `aclModuleId` int(11) NOT NULL,
  `aclModuleName` varchar(32) NOT NULL,
  PRIMARY KEY  (`aclModuleId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `aclPrivileges`
--

CREATE TABLE IF NOT EXISTS `aclPrivileges` (
  `aclPrivilegeId` int(11) NOT NULL,
  `aclResourceId` int(11) NOT NULL,
  `aclPrivilegeName` varchar(32) NOT NULL,
  PRIMARY KEY  (`aclPrivilegeId`),
  UNIQUE KEY `aclResourceId_2` (`aclResourceId`,`aclPrivilegeName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `aclResources`
--

CREATE TABLE IF NOT EXISTS `aclResources` (
  `aclResourceId` int(11) NOT NULL,
  `aclModuleId` int(11) NOT NULL,
  `aclResourceName` varchar(32) NOT NULL,
  PRIMARY KEY  (`aclResourceId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `aclRolePrivileges`
--

CREATE TABLE IF NOT EXISTS `aclRolePrivileges` (
  `aclRoleId` int(11) NOT NULL,
  `aclPrivilegeId` int(11) NOT NULL,
  PRIMARY KEY  (`aclRoleId`,`aclPrivilegeId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `aclRoles`
--

CREATE TABLE IF NOT EXISTS `aclRoles` (
  `aclRoleId` int(11) NOT NULL,
  `aclRoleName` varchar(32) NOT NULL,
  PRIMARY KEY  (`aclRoleId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE IF NOT EXISTS `address` (
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

-- --------------------------------------------------------

--
-- Table structure for table `adodbseq`
--

CREATE TABLE IF NOT EXISTS `adodbseq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='STARTWITHDATA';

-- --------------------------------------------------------

--
-- Table structure for table `altnotice`
--

CREATE TABLE IF NOT EXISTS `altnotice` (
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

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE IF NOT EXISTS `appointment` (
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

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE IF NOT EXISTS `appointments` (
  `appointmentId` int(11) NOT NULL,
  `arrived` tinyint(1) NOT NULL,
  `title` varchar(255) NOT NULL default '',
  `reason` int(11) NOT NULL default '0',
  `walkin` tinyint(1) NOT NULL default '0',
  `createdDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastChangeId` int(11) NOT NULL default '0',
  `lastChangeDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `creatorId` int(11) NOT NULL default '0',
  `practiceId` int(11) NOT NULL default '0',
  `providerId` int(11) NOT NULL default '0',
  `patientId` int(11) NOT NULL default '0',
  `roomId` int(11) NOT NULL default '0',
  `appointmentCode` varchar(255) NOT NULL default '',
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  PRIMARY KEY  (`appointmentId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `appointment_breakdown`
--

CREATE TABLE IF NOT EXISTS `appointment_breakdown` (
  `appointment_breakdown_id` int(11) NOT NULL default '0',
  `appointment_id` int(11) NOT NULL default '0',
  `occurence_breakdown_id` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`appointment_breakdown_id`),
  KEY `occurence_breakdown_id` (`occurence_breakdown_id`,`person_id`),
  KEY `appointment_id` (`appointment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `appointment_rule`
--

CREATE TABLE IF NOT EXISTS `appointment_rule` (
  `appointment_rule_id` int(11) NOT NULL default '0',
  `appointment_ruleset_id` int(11) NOT NULL default '0',
  `type` varchar(20) NOT NULL default '',
  `label` varchar(255) NOT NULL default '',
  `data` longtext NOT NULL,
  PRIMARY KEY  (`appointment_rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `appointment_ruleset`
--

CREATE TABLE IF NOT EXISTS `appointment_ruleset` (
  `appointment_ruleset_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `error_message` text NOT NULL,
  `provider_id` int(11) NOT NULL default '0',
  `procedure_id` int(11) NOT NULL default '0',
  `room_id` int(11) NOT NULL default '0',
  `enabled` tinyint(1) NOT NULL default '0',
  `any` tinyint(4) NOT NULL,
  PRIMARY KEY  (`appointment_ruleset_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `appointment_template`
--

CREATE TABLE IF NOT EXISTS `appointment_template` (
  `appointment_template_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`appointment_template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `attachmentBlobs`
--

CREATE TABLE IF NOT EXISTS `attachmentBlobs` (
  `attachmentId` int(11) NOT NULL,
  `data` mediumblob NOT NULL,
  PRIMARY KEY  (`attachmentId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE IF NOT EXISTS `attachments` (
  `attachmentId` int(11) NOT NULL,
  `attachmentReferenceId` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `dateTime` datetime NOT NULL,
  `mimeType` varchar(255) NOT NULL,
  `md5sum` varchar(40) NOT NULL,
  PRIMARY KEY  (`attachmentId`),
  KEY `attachmentReferenceId` (`attachmentReferenceId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `audits`
--

CREATE TABLE IF NOT EXISTS `audits` (
  `auditId` int(11) NOT NULL,
  `objectClass` varchar(255) NOT NULL,
  `objectId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `message` text NOT NULL,
  `dateTime` datetime NOT NULL,
  PRIMARY KEY  (`auditId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `auditSequences`
--

CREATE TABLE IF NOT EXISTS `auditSequences` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `auditValues`
--

CREATE TABLE IF NOT EXISTS `auditValues` (
  `auditValueId` int(11) NOT NULL,
  `auditId` int(11) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`auditValueId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE IF NOT EXISTS `audit_log` (
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

-- --------------------------------------------------------

--
-- Table structure for table `audit_log_field`
--

CREATE TABLE IF NOT EXISTS `audit_log_field` (
  `audit_log_field_id` int(11) NOT NULL default '0',
  `audit_log_id` int(11) NOT NULL default '0',
  `field` varchar(255) NOT NULL default '',
  `old_value` text NOT NULL,
  `new_value` text NOT NULL,
  PRIMARY KEY  (`audit_log_field_id`),
  UNIQUE KEY `audit_log_id` (`audit_log_id`,`field`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `barcodeMacros`
--

CREATE TABLE IF NOT EXISTS `barcodeMacros` (
  `name` varchar(255) NOT NULL,
  `regex` varchar(255) NOT NULL,
  `macro` text NOT NULL,
  `active` tinyint(4) NOT NULL,
  `cache` tinyint(4) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `buildings`
--

CREATE TABLE IF NOT EXISTS `buildings` (
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

-- --------------------------------------------------------

--
-- Table structure for table `building_address`
--

CREATE TABLE IF NOT EXISTS `building_address` (
  `building_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  `address_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`building_id`,`address_id`),
  KEY `address_id` (`address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Links a building to a address specifying type. STARTEMPTY';

-- --------------------------------------------------------

--
-- Table structure for table `building_program_identifier`
--

CREATE TABLE IF NOT EXISTS `building_program_identifier` (
  `building_id` int(11) NOT NULL default '0',
  `program_id` int(11) NOT NULL default '0',
  `identifier` varchar(255) NOT NULL default '',
  `x12_sender_id` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`building_id`,`program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
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

-- --------------------------------------------------------

--
-- Table structure for table `category_to_document`
--

CREATE TABLE IF NOT EXISTS `category_to_document` (
  `category_id` int(11) NOT NULL default '0',
  `document_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`category_id`,`document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='STARTEMPTY';

-- --------------------------------------------------------

--
-- Table structure for table `clearhealth_claim`
--

CREATE TABLE IF NOT EXISTS `clearhealth_claim` (
  `claim_id` int(11) NOT NULL,
  `encounter_id` int(11) NOT NULL default '0',
  `identifier` varchar(255) NOT NULL default '',
  `total_billed` float(7,2) NOT NULL default '0.00',
  `total_paid` float(7,2) NOT NULL default '0.00',
  PRIMARY KEY  (`claim_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='STARTEMPTY';

-- --------------------------------------------------------

--
-- Table structure for table `clinicalNoteAnnotations`
--

CREATE TABLE IF NOT EXISTS `clinicalNoteAnnotations` (
  `clinicalNoteAnnotationId` int(11) NOT NULL,
  `clinicalNoteId` int(11) NOT NULL,
  `xAxis` int(11) NOT NULL,
  `yAxis` int(11) NOT NULL,
  `annotation` text NOT NULL,
  PRIMARY KEY  (`clinicalNoteAnnotationId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `clinicalNoteDefinitions`
--

CREATE TABLE IF NOT EXISTS `clinicalNoteDefinitions` (
  `clinicalNoteDefinitionId` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `clinicalNoteTemplateId` int(11) NOT NULL,
  `active` tinyint(4) default NULL,
  PRIMARY KEY  (`clinicalNoteDefinitionId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `clinicalNotes`
--

CREATE TABLE IF NOT EXISTS `clinicalNotes` (
  `clinicalNoteId` int(11) NOT NULL,
  `personId` int(11) NOT NULL,
  `visitId` int(11) NOT NULL,
  `clinicalNoteDefinitionId` int(11) NOT NULL,
  `dateTime` datetime NOT NULL,
  `authoringPersonId` int(11) NOT NULL,
  `consultationId` int(11) NOT NULL,
  `locationId` int(11) NOT NULL,
  `eSignatureId` int(11) NOT NULL,
  PRIMARY KEY  (`clinicalNoteId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `clinicalNoteTemplates`
--

CREATE TABLE IF NOT EXISTS `clinicalNoteTemplates` (
  `clinicalNoteTemplateId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `template` text NOT NULL,
  `guid` varchar(50) default NULL,
  PRIMARY KEY  (`clinicalNoteTemplateId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `codes`
--

CREATE TABLE IF NOT EXISTS `codes` (
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

-- --------------------------------------------------------

--
-- Table structure for table `code_category`
--

CREATE TABLE IF NOT EXISTS `code_category` (
  `code_category_id` int(11) NOT NULL default '0',
  `category_name` varchar(255) NOT NULL default '',
  `category_id` int(11) NOT NULL,
  PRIMARY KEY  (`code_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `code_to_category`
--

CREATE TABLE IF NOT EXISTS `code_to_category` (
  `code_category_id` int(11) NOT NULL default '0',
  `code_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`code_category_id`,`code_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `coding_data`
--

CREATE TABLE IF NOT EXISTS `coding_data` (
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

-- --------------------------------------------------------

--
-- Table structure for table `coding_data_dental`
--

CREATE TABLE IF NOT EXISTS `coding_data_dental` (
  `coding_data_id` int(11) NOT NULL default '0',
  `tooth` enum('N/A','All','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','All (Primary)','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T') NOT NULL default 'N/A',
  `toothside` enum('N/A','Front','Back','Top','Left','Right') NOT NULL default 'N/A',
  PRIMARY KEY  (`coding_data_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `coding_template`
--

CREATE TABLE IF NOT EXISTS `coding_template` (
  `coding_template_id` int(11) NOT NULL default '0',
  `practice_id` int(11) NOT NULL default '0',
  `reason_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL default '',
  `coding_parent_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`coding_template_id`),
  KEY `practice_id` (`practice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE IF NOT EXISTS `company` (
  `company_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `notes` text NOT NULL,
  `initials` varchar(10) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `is_historic` enum('no','yes') NOT NULL default 'no',
  PRIMARY KEY  (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Base Company record most of the data is linked in STARTEMPTY';

-- --------------------------------------------------------

--
-- Table structure for table `company_address`
--

CREATE TABLE IF NOT EXISTS `company_address` (
  `company_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  `address_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`,`address_id`),
  KEY `address_id` (`address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Links a company to a address specifying the type STARTEMPTY';

-- --------------------------------------------------------

--
-- Table structure for table `company_company`
--

CREATE TABLE IF NOT EXISTS `company_company` (
  `company_id` int(11) NOT NULL default '0',
  `related_company_id` int(11) NOT NULL default '0',
  `company_relation_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`,`related_company_id`),
  KEY `related_company_id` (`related_company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Relates a company to another company STARTEMPTY';

-- --------------------------------------------------------

--
-- Table structure for table `company_number`
--

CREATE TABLE IF NOT EXISTS `company_number` (
  `company_id` int(11) NOT NULL default '0',
  `number_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`,`number_id`),
  KEY `company_id` (`company_id`),
  KEY `number_id` (`number_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Links between company and phone_numbers STARTEMPTY';

-- --------------------------------------------------------

--
-- Table structure for table `company_type`
--

CREATE TABLE IF NOT EXISTS `company_type` (
  `company_id` int(11) NOT NULL default '0',
  `company_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`,`company_type`),
  KEY `company_id` (`company_id`),
  KEY `company_type` (`company_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Link to specify company type';

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `configId` varchar(32) NOT NULL,
  `value` text,
  PRIMARY KEY  (`configId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE IF NOT EXISTS `countries` (
  `countries_name` varchar(64) NOT NULL default '',
  `countries_iso_code_2` char(2) NOT NULL default '',
  `countries_iso_code_3` char(3) NOT NULL default '',
  PRIMARY KEY  (`countries_iso_code_3`),
  KEY `IDX_COUNTRIES_NAME` (`countries_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cronable`
--

CREATE TABLE IF NOT EXISTS `cronable` (
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

-- --------------------------------------------------------

--
-- Table structure for table `dashboardComponent`
--

CREATE TABLE IF NOT EXISTS `dashboardComponent` (
  `dashboardComponentId` varchar(50) NOT NULL COMMENT 'GUID',
  `name` varchar(50) NOT NULL,
  `systemName` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `type` varchar(20) NOT NULL,
  PRIMARY KEY  (`dashboardComponentId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `diagnosisCodesICD`
--

CREATE TABLE IF NOT EXISTS `diagnosisCodesICD` (
  `code` varchar(10) NOT NULL,
  `textShort` varchar(24) default NULL,
  `textLong` varchar(255) default NULL,
  PRIMARY KEY  (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `diagnosisCodesSNOMED`
--

CREATE TABLE IF NOT EXISTS `diagnosisCodesSNOMED` (
  `snomedId` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `lastchange` date NOT NULL,
  `subsetStatus` varchar(255) NOT NULL,
  `snomedStatus` varchar(255) NOT NULL,
  PRIMARY KEY  (`snomedId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `document`
--

CREATE TABLE IF NOT EXISTS `document` (
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

-- --------------------------------------------------------

--
-- Table structure for table `duplicate_queue`
--

CREATE TABLE IF NOT EXISTS `duplicate_queue` (
  `duplicate_queue_id` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `child_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`duplicate_queue_id`),
  UNIQUE KEY `child_id` (`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `eligibility_log`
--

CREATE TABLE IF NOT EXISTS `eligibility_log` (
  `eligibility_log_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL default '0',
  `log_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `message` longtext NOT NULL,
  PRIMARY KEY  (`eligibility_log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `encounter`
--

CREATE TABLE IF NOT EXISTS `encounter` (
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
  `payer_group_id` int(11) default NULL,
  `current_payer` int(11) default NULL,
  `room_id` int(11) default NULL,
  `practice_id` int(11) default NULL,
  PRIMARY KEY  (`encounter_id`),
  KEY `building_id` (`building_id`),
  KEY `treating_person_id` (`treating_person_id`),
  KEY `last_change_user_id` (`last_change_user_id`),
  KEY `patient_id` (`patient_id`),
  KEY `occurence_id` (`occurence_id`),
  KEY `date_of_treatment` (`date_of_treatment`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `encounter_date`
--

CREATE TABLE IF NOT EXISTS `encounter_date` (
  `encounter_date_id` int(11) NOT NULL,
  `encounter_id` int(11) NOT NULL default '0',
  `date_type` int(11) NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`encounter_date_id`),
  KEY `encounter_id` (`encounter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `encounter_person`
--

CREATE TABLE IF NOT EXISTS `encounter_person` (
  `encounter_person_id` int(11) NOT NULL,
  `encounter_id` int(11) NOT NULL default '0',
  `person_type` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`encounter_person_id`),
  KEY `encounter_id` (`encounter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `encounter_value`
--

CREATE TABLE IF NOT EXISTS `encounter_value` (
  `encounter_value_id` int(11) NOT NULL,
  `encounter_id` int(11) NOT NULL default '0',
  `value_type` int(11) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '0',
  PRIMARY KEY  (`encounter_value_id`),
  KEY `encounter_id` (`encounter_id`),
  KEY `value_type` (`value_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `enumerations`
--

CREATE TABLE IF NOT EXISTS `enumerations` (
  `enumerationId` int(11) NOT NULL,
  `guid` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `key` char(10) NOT NULL,
  `active` tinyint(4) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `parentId` int(11) NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `ormClass` varchar(255) default NULL,
  `ormId` int(11) default NULL,
  `ormEditMethod` varchar(64) default NULL,
  PRIMARY KEY  (`enumerationId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `enumerationsClosure`
--

CREATE TABLE IF NOT EXISTS `enumerationsClosure` (
  `ancestor` int(11) NOT NULL,
  `descendant` int(11) NOT NULL,
  `depth` int(11) NOT NULL,
  `weight` int(11) NOT NULL,
  PRIMARY KEY  (`ancestor`,`descendant`),
  KEY `descendant` (`descendant`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `enumeration_definition`
--

CREATE TABLE IF NOT EXISTS `enumeration_definition` (
  `enumeration_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `type` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`enumeration_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `enumeration_value`
--

CREATE TABLE IF NOT EXISTS `enumeration_value` (
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

-- --------------------------------------------------------

--
-- Table structure for table `enumeration_value_practice`
--

CREATE TABLE IF NOT EXISTS `enumeration_value_practice` (
  `enumeration_value_id` int(11) NOT NULL default '0',
  `practice_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`enumeration_value_id`,`practice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `eob_adjustment`
--

CREATE TABLE IF NOT EXISTS `eob_adjustment` (
  `eob_adjustment_id` int(11) NOT NULL default '0',
  `payment_id` int(11) NOT NULL default '0',
  `payment_claimline_id` int(11) NOT NULL default '0',
  `adjustment_type` int(11) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`eob_adjustment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `eSignatures`
--

CREATE TABLE IF NOT EXISTS `eSignatures` (
  `eSignatureId` int(11) NOT NULL,
  `eSignatureParentId` int(11) NOT NULL,
  `dateTime` datetime NOT NULL,
  `signedDateTime` datetime NOT NULL,
  `signingUserId` int(11) NOT NULL,
  `objectId` int(11) NOT NULL,
  `objectClass` varchar(255) NOT NULL,
  `summary` varchar(255) NOT NULL,
  `signature` varchar(255) NOT NULL,
  PRIMARY KEY  (`eSignatureId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE IF NOT EXISTS `event` (
  `event_id` int(11) NOT NULL,
  `start` datetime NOT NULL default '0000-00-00 00:00:00',
  `end` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`event_id`),
  KEY `start` (`start`),
  KEY `end` (`end`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `event_group`
--

CREATE TABLE IF NOT EXISTS `event_group` (
  `event_group_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL default '',
  `room_id` int(11) NOT NULL default '0',
  `schedule_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`event_group_id`),
  KEY `room_id` (`room_id`),
  KEY `schedule_id` (`schedule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `facility_codes`
--

CREATE TABLE IF NOT EXISTS `facility_codes` (
  `facility_code_id` int(11) NOT NULL,
  `code` varchar(5) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`facility_code_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Stores x12 facility_code code/human name combos';

-- --------------------------------------------------------

--
-- Table structure for table `fbaddress`
--

CREATE TABLE IF NOT EXISTS `fbaddress` (
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

-- --------------------------------------------------------

--
-- Table structure for table `fbclaim`
--

CREATE TABLE IF NOT EXISTS `fbclaim` (
  `claim_id` int(11) NOT NULL default '0',
  `claim_identifier` varchar(255) NOT NULL default '',
  `revision` int(11) NOT NULL default '0',
  `status` enum('new','pending','sent','archive','deleted') NOT NULL default 'new',
  `timestamp` timestamp NULL default '0000-00-00 00:00:00',
  `date_sent` datetime NOT NULL default '0000-00-00 00:00:00',
  `format` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`claim_id`),
  KEY `claim_identifier` (`claim_identifier`),
  KEY `status` (`status`),
  KEY `revision` (`revision`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fbclaimline`
--

CREATE TABLE IF NOT EXISTS `fbclaimline` (
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fbcompany`
--

CREATE TABLE IF NOT EXISTS `fbcompany` (
  `company_id` int(11) NOT NULL default '0',
  `claim_id` int(11) NOT NULL default '0',
  `index` tinyint(4) NOT NULL default '0',
  `identifier` varchar(25) NOT NULL default '',
  `identifier_type` varchar(10) NOT NULL default '',
  `type` varchar(50) NOT NULL default '',
  `name` varchar(100) NOT NULL default '',
  `phone_number` varchar(45) NOT NULL default '',
  `program_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`company_id`),
  KEY `claim_id` (`claim_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Base Company record most of the data is in linked tables';

-- --------------------------------------------------------

--
-- Table structure for table `fbdiagnoses`
--

CREATE TABLE IF NOT EXISTS `fbdiagnoses` (
  `id` int(11) NOT NULL default '0',
  `claimline_id` int(11) NOT NULL default '0',
  `diagnosis` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `claimline_id` (`claimline_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fblatest_revision`
--

CREATE TABLE IF NOT EXISTS `fblatest_revision` (
  `claim_identifier` varchar(255) NOT NULL default '',
  `revision` int(11) NOT NULL default '0',
  PRIMARY KEY  (`claim_identifier`),
  KEY `revision` (`revision`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fbperson`
--

CREATE TABLE IF NOT EXISTS `fbperson` (
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

-- --------------------------------------------------------

--
-- Table structure for table `fbpractice`
--

CREATE TABLE IF NOT EXISTS `fbpractice` (
  `practice_id` int(11) NOT NULL default '0',
  `claim_id` int(11) NOT NULL default '0',
  `billing_contact_person_id` int(11) NOT NULL default '0',
  `treating_location_company_company_id` int(11) NOT NULL default '0',
  `billing_location_company_id` int(11) NOT NULL default '0',
  `provider_person_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`practice_id`),
  KEY `claim_id` (`claim_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fbqueue`
--

CREATE TABLE IF NOT EXISTS `fbqueue` (
  `queue_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `max_items` int(11) NOT NULL default '0',
  `num_items` int(11) NOT NULL default '0',
  `ids` mediumtext NOT NULL,
  PRIMARY KEY  (`queue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fee_schedule`
--

CREATE TABLE IF NOT EXISTS `fee_schedule` (
  `fee_schedule_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `label` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `priority` int(11) NOT NULL default '2',
  PRIMARY KEY  (`fee_schedule_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fee_schedule_data`
--

CREATE TABLE IF NOT EXISTS `fee_schedule_data` (
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

-- --------------------------------------------------------

--
-- Table structure for table `fee_schedule_data_modifier`
--

CREATE TABLE IF NOT EXISTS `fee_schedule_data_modifier` (
  `fsd_modifier_id` int(11) NOT NULL default '0',
  `fee_schedule_id` int(11) NOT NULL default '0',
  `code_id` int(11) NOT NULL default '0',
  `modifier` int(11) NOT NULL default '0',
  `fee` float(7,2) NOT NULL default '0.00',
  PRIMARY KEY  (`fsd_modifier_id`),
  UNIQUE KEY `fee_schedule_id` (`fee_schedule_id`,`code_id`,`modifier`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fee_schedule_discount`
--

CREATE TABLE IF NOT EXISTS `fee_schedule_discount` (
  `fee_schedule_discount_id` int(11) NOT NULL default '0',
  `practice_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `insurance_program_id` int(11) NOT NULL default '0',
  `type` enum('default','program') NOT NULL default 'default',
  PRIMARY KEY  (`fee_schedule_discount_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fee_schedule_discount_by_code`
--

CREATE TABLE IF NOT EXISTS `fee_schedule_discount_by_code` (
  `fee_schedule_discount_by_code_id` int(11) NOT NULL default '0',
  `fee_schedule_discount_id` int(11) NOT NULL default '0',
  `fee_schedule_discount_level_id` int(11) NOT NULL default '0',
  `code_pattern` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`fee_schedule_discount_by_code_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fee_schedule_discount_income`
--

CREATE TABLE IF NOT EXISTS `fee_schedule_discount_income` (
  `fee_schedule_discount_income_id` int(11) NOT NULL default '0',
  `fee_schedule_discount_id` int(11) NOT NULL default '0',
  `fee_schedule_discount_level_id` int(11) NOT NULL default '0',
  `family_size` int(11) NOT NULL default '0',
  `income` float(9,2) NOT NULL default '0.00',
  PRIMARY KEY  (`fee_schedule_discount_income_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fee_schedule_discount_level`
--

CREATE TABLE IF NOT EXISTS `fee_schedule_discount_level` (
  `fee_schedule_discount_level_id` int(11) NOT NULL default '0',
  `fee_schedule_discount_id` int(11) NOT NULL default '0',
  `discount` float(5,2) NOT NULL default '0.00',
  `disp_order` int(11) NOT NULL default '0',
  `type` enum('percent','flat') NOT NULL default 'percent',
  PRIMARY KEY  (`fee_schedule_discount_level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fee_schedule_revision`
--

CREATE TABLE IF NOT EXISTS `fee_schedule_revision` (
  `revision_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `update_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`revision_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `filterStates`
--

CREATE TABLE IF NOT EXISTS `filterStates` (
  `filterStateId` int(11) NOT NULL,
  `tabName` varchar(50) NOT NULL,
  `providerId` int(11) NOT NULL,
  `roomId` int(11) NOT NULL,
  `dateFilter` date NOT NULL,
  PRIMARY KEY  (`filterStateId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `folders`
--

CREATE TABLE IF NOT EXISTS `folders` (
  `folder_id` int(10) unsigned NOT NULL,
  `label` varchar(50) NOT NULL default '',
  `create_date` datetime default '0000-00-00 00:00:00',
  `modify_date` datetime default '0000-00-00 00:00:00',
  `webdavname` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`folder_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `form`
--

CREATE TABLE IF NOT EXISTS `form` (
  `form_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `system_name` varchar(100) default NULL,
  PRIMARY KEY  (`form_id`),
  UNIQUE KEY `system_name` (`system_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Contains the EMR extending forms STARTWITHDATA';

-- --------------------------------------------------------

--
-- Table structure for table `formularyDefault`
--

CREATE TABLE IF NOT EXISTS `formularyDefault` (
  `fullNDC` varchar(20) NOT NULL,
  `directions` varchar(255) NOT NULL,
  `comments` varchar(255) NOT NULL,
  `price` float(10,2) NOT NULL,
  `schedule` CHAR( 10 ) NOT NULL,
  `labelId` INT NOT NULL,
  `externalUrl` VARCHAR( 255 ) NOT NULL,
  `qty` int(11) NOT NULL,
  `keywords` VARCHAR( 255 ) NOT NULL,
  PRIMARY KEY  (`fullNDC`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `form_data`
--

CREATE TABLE IF NOT EXISTS `form_data` (
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

-- --------------------------------------------------------

--
-- Table structure for table `form_rule`
--

CREATE TABLE IF NOT EXISTS `form_rule` (
  `form_rule_id` int(11) NOT NULL,
  `field_name` varchar(100) NOT NULL default '',
  `rule_name` varchar(30) NOT NULL default '',
  `operator` char(3) NOT NULL default '',
  `value_type` int(1) NOT NULL default '1',
  `value` varchar(30) NOT NULL default '',
  `message` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`form_rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `form_structure`
--

CREATE TABLE IF NOT EXISTS `form_structure` (
  `form_structure_id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL default '0',
  `field_name` varchar(100) NOT NULL default '',
  `field_type` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`form_structure_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gacl_acl`
--

CREATE TABLE IF NOT EXISTS `gacl_acl` (
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

-- --------------------------------------------------------

--
-- Table structure for table `gacl_acl_sections`
--

CREATE TABLE IF NOT EXISTS `gacl_acl_sections` (
  `id` int(11) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(230) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_value_acl_sections` (`value`),
  KEY `gacl_hidden_acl_sections` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gacl_acl_seq`
--

CREATE TABLE IF NOT EXISTS `gacl_acl_seq` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gacl_aco`
--

CREATE TABLE IF NOT EXISTS `gacl_aco` (
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

-- --------------------------------------------------------

--
-- Table structure for table `gacl_aco_map`
--

CREATE TABLE IF NOT EXISTS `gacl_aco_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gacl_aco_sections`
--

CREATE TABLE IF NOT EXISTS `gacl_aco_sections` (
  `id` int(11) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(230) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_value_aco_sections` (`value`),
  KEY `gacl_hidden_aco_sections` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gacl_aco_sections_seq`
--

CREATE TABLE IF NOT EXISTS `gacl_aco_sections_seq` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gacl_aco_seq`
--

CREATE TABLE IF NOT EXISTS `gacl_aco_seq` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gacl_aro`
--

CREATE TABLE IF NOT EXISTS `gacl_aro` (
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

-- --------------------------------------------------------

--
-- Table structure for table `gacl_aro_groups`
--

CREATE TABLE IF NOT EXISTS `gacl_aro_groups` (
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

-- --------------------------------------------------------

--
-- Table structure for table `gacl_aro_groups_id_seq`
--

CREATE TABLE IF NOT EXISTS `gacl_aro_groups_id_seq` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gacl_aro_groups_map`
--

CREATE TABLE IF NOT EXISTS `gacl_aro_groups_map` (
  `acl_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acl_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gacl_aro_map`
--

CREATE TABLE IF NOT EXISTS `gacl_aro_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gacl_aro_sections`
--

CREATE TABLE IF NOT EXISTS `gacl_aro_sections` (
  `id` int(11) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(230) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_value_aro_sections` (`value`),
  KEY `gacl_hidden_aro_sections` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gacl_aro_sections_seq`
--

CREATE TABLE IF NOT EXISTS `gacl_aro_sections_seq` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gacl_aro_seq`
--

CREATE TABLE IF NOT EXISTS `gacl_aro_seq` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gacl_axo`
--

CREATE TABLE IF NOT EXISTS `gacl_axo` (
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

-- --------------------------------------------------------

--
-- Table structure for table `gacl_axo_groups`
--

CREATE TABLE IF NOT EXISTS `gacl_axo_groups` (
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

-- --------------------------------------------------------

--
-- Table structure for table `gacl_axo_groups_id_seq`
--

CREATE TABLE IF NOT EXISTS `gacl_axo_groups_id_seq` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gacl_axo_groups_map`
--

CREATE TABLE IF NOT EXISTS `gacl_axo_groups_map` (
  `acl_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acl_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gacl_axo_map`
--

CREATE TABLE IF NOT EXISTS `gacl_axo_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gacl_axo_sections`
--

CREATE TABLE IF NOT EXISTS `gacl_axo_sections` (
  `id` int(11) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(230) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_value_axo_sections` (`value`),
  KEY `gacl_hidden_axo_sections` (`hidden`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gacl_axo_sections_seq`
--

CREATE TABLE IF NOT EXISTS `gacl_axo_sections_seq` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gacl_axo_seq`
--

CREATE TABLE IF NOT EXISTS `gacl_axo_seq` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gacl_groups_aro_map`
--

CREATE TABLE IF NOT EXISTS `gacl_groups_aro_map` (
  `group_id` int(11) NOT NULL default '0',
  `aro_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`aro_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gacl_groups_axo_map`
--

CREATE TABLE IF NOT EXISTS `gacl_groups_axo_map` (
  `group_id` int(11) NOT NULL default '0',
  `axo_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`axo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gacl_phpgacl`
--

CREATE TABLE IF NOT EXISTS `gacl_phpgacl` (
  `name` varchar(230) NOT NULL default '',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `genericData`
--

CREATE TABLE IF NOT EXISTS `genericData` (
  `genericDataId` int(11) NOT NULL,
  `objectId` int(11) NOT NULL,
  `objectClass` varchar(255) NOT NULL,
  `dateTime` datetime NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`genericDataId`),
  KEY `objectId` (`objectId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `generic_notes`
--

CREATE TABLE IF NOT EXISTS `generic_notes` (
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

-- --------------------------------------------------------

--
-- Table structure for table `graph_definition`
--

CREATE TABLE IF NOT EXISTS `graph_definition` (
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `group_occurence`
--

CREATE TABLE IF NOT EXISTS `group_occurence` (
  `group_occurence_id` int(11) NOT NULL default '0',
  `occurence_id` int(11) NOT NULL default '0',
  `patient_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_occurence_id`),
  UNIQUE KEY `occurence_id` (`occurence_id`,`patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hl7_message`
--

CREATE TABLE IF NOT EXISTS `hl7_message` (
  `hl7_message_id` int(11) NOT NULL default '0',
  `type` tinyint(4) NOT NULL,
  `control_id` varchar(50) NOT NULL default '',
  `message` longtext NOT NULL,
  `processed` tinyint(4) NOT NULL,
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`hl7_message_id`),
  KEY `control_id` (`control_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `identifier`
--

CREATE TABLE IF NOT EXISTS `identifier` (
  `identifier_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL default '0',
  `identifier` varchar(100) NOT NULL default '',
  `identifier_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`identifier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `import_map`
--

CREATE TABLE IF NOT EXISTS `import_map` (
  `old_id` int(11) NOT NULL default '0',
  `new_id` int(11) default NULL,
  `old_table_name` varchar(100) NOT NULL default '',
  `new_object_name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`old_id`,`old_table_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `insurance`
--

CREATE TABLE IF NOT EXISTS `insurance` (
  `company_id` int(11) NOT NULL default '0',
  `fee_schedule_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`),
  KEY `fee_schedule_id` (`fee_schedule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `insurance_payergroup`
--

CREATE TABLE IF NOT EXISTS `insurance_payergroup` (
  `payer_group_id` int(11) NOT NULL default '0',
  `insurance_program_id` int(11) NOT NULL default '0',
  `order` int(11) NOT NULL default '0',
  KEY `payer_group_id` (`payer_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `insurance_program`
--

CREATE TABLE IF NOT EXISTS `insurance_program` (
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

-- --------------------------------------------------------

--
-- Table structure for table `insured_relationship`
--

CREATE TABLE IF NOT EXISTS `insured_relationship` (
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

-- --------------------------------------------------------

--
-- Table structure for table `lab_note`
--

CREATE TABLE IF NOT EXISTS `lab_note` (
  `lab_note_id` int(11) NOT NULL default '0',
  `lab_test_id` int(11) NOT NULL default '0',
  `note` text NOT NULL,
  PRIMARY KEY  (`lab_note_id`),
  KEY `lab_test_id` (`lab_test_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lab_order`
--

CREATE TABLE IF NOT EXISTS `lab_order` (
  `lab_order_id` int(11) NOT NULL default '0',
  `patient_id` int(11) NOT NULL default '0',
  `type` char(6) NOT NULL,
  `status` char(2) NOT NULL default '',
  `ordering_provider` varchar(255) NOT NULL default '',
  `manual_service` tinyint(4) NOT NULL,
  `manual_order_date` date NOT NULL,
  `encounter_id` int(11) NOT NULL,
  `external_id` int(11) NOT NULL,
  PRIMARY KEY  (`lab_order_id`),
  KEY `external_id` (`external_id`),
  KEY `patient_id` (`patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lab_result`
--

CREATE TABLE IF NOT EXISTS `lab_result` (
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
  PRIMARY KEY  (`lab_result_id`),
  KEY `description` (`description`),
  KEY `lab_test_id` (`lab_test_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lab_test`
--

CREATE TABLE IF NOT EXISTS `lab_test` (
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
  PRIMARY KEY  (`lab_test_id`),
  KEY `lab_order_id` (`lab_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mainmenu`
--

CREATE TABLE IF NOT EXISTS `mainmenu` (
  `menuId` varchar(36) NOT NULL,
  `siteSection` varchar(50) NOT NULL default 'default',
  `parentId` varchar(36) NOT NULL default '0',
  `dynamicKey` varchar(50) NOT NULL,
  `section` enum('children','more','dynamic') NOT NULL default 'children',
  `displayOrder` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `action` varchar(255) NOT NULL default '',
  `prefix` varchar(100) NOT NULL default 'main',
  `type` varchar(20) NOT NULL,
  `active` tinyint(1) NOT NULL default '0',
  `typeValue` varchar(255) NOT NULL,
  PRIMARY KEY  (`menuId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `medications`
--

CREATE TABLE IF NOT EXISTS `medications` (
  `medicationId` int(11) NOT NULL,
  `hipaaNDC` varchar(255) NOT NULL,
  `personId` int(11) NOT NULL,
  `type` char(3) NOT NULL,
  `patientReported` tinyint(4) NOT NULL,
  `substitution` tinyint(4) NOT NULL,
  `dateBegan` datetime NOT NULL,
  `datePrescribed` datetime NOT NULL,
  `description` varchar(255) NOT NULL,
  `comment` varchar(255) NOT NULL,
  `directions` varchar(255) NOT NULL,
  `prescriberPersonId` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `dose` varchar(255) NOT NULL,
  `route` varchar(255) NOT NULL,
  `priority` char(10) NOT NULL,
  `schedule` varchar(255) NOT NULL,
  `prn` tinyint(4) NOT NULL,
  `transmit` char(10) NOT NULL,
  `dateTransmitted` datetime NOT NULL,
  `daysSupply` int(11) NOT NULL,
  `strength` varchar(255) NOT NULL,
  `unit` varchar(255) NOT NULL,
  `refills` int(11) NOT NULL,
  `rxnorm` varchar(255) NOT NULL,
  `eSignatureId` int(11) NOT NULL,
  KEY `medicationId` (`medicationId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `meds_bulk_quantity`
--

CREATE TABLE IF NOT EXISTS `meds_bulk_quantity` (
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

-- --------------------------------------------------------

--
-- Table structure for table `meds_case`
--

CREATE TABLE IF NOT EXISTS `meds_case` (
  `meds_case_id` int(11) NOT NULL default '0',
  `meds_inventory_item_id` int(11) NOT NULL default '0',
  `case_count` int(255) default NULL,
  PRIMARY KEY  (`meds_case_id`),
  UNIQUE KEY `meds_inventory_item_id` (`meds_inventory_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `meds_inventory_item`
--

CREATE TABLE IF NOT EXISTS `meds_inventory_item` (
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

-- --------------------------------------------------------

--
-- Table structure for table `meds_inventory_item_price`
--

CREATE TABLE IF NOT EXISTS `meds_inventory_item_price` (
  `meds_inventory_item_price_id` int(11) NOT NULL default '0',
  `meds_inventory_item_id` int(11) NOT NULL default '0',
  `company_id` int(11) NOT NULL default '0',
  `price` decimal(11,2) default NULL,
  `awp` decimal(11,2) default NULL,
  `aac` decimal(11,2) default NULL,
  `copay` decimal(11,2) default NULL,
  PRIMARY KEY  (`meds_inventory_item_price_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `meds_inventory_item_status`
--

CREATE TABLE IF NOT EXISTS `meds_inventory_item_status` (
  `meds_inventory_item_status_id` int(11) NOT NULL default '0',
  `meds_inventory_item_id` int(11) NOT NULL default '0',
  `on_hand` int(255) default NULL,
  `reorder_point` int(255) default NULL,
  PRIMARY KEY  (`meds_inventory_item_status_id`),
  UNIQUE KEY `meds_inventory_item_id` (`meds_inventory_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `meds_item_to_location`
--

CREATE TABLE IF NOT EXISTS `meds_item_to_location` (
  `meds_item_to_location_id` int(11) NOT NULL default '0',
  `meds_inventory_item_id` int(11) NOT NULL default '0',
  `building_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`meds_item_to_location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `meds_item_to_program`
--

CREATE TABLE IF NOT EXISTS `meds_item_to_program` (
  `meds_item_to_program_id` int(11) NOT NULL default '0',
  `meds_inventory_item_id` int(11) NOT NULL default '0',
  `insurance_program_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`meds_item_to_program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `meds_program`
--

CREATE TABLE IF NOT EXISTS `meds_program` (
  `meds_program_id` int(11) NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `meds_unit_of_use`
--

CREATE TABLE IF NOT EXISTS `meds_unit_of_use` (
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

-- --------------------------------------------------------

--
-- Table structure for table `meds_unit_of_use_warning`
--

CREATE TABLE IF NOT EXISTS `meds_unit_of_use_warning` (
  `meds_unit_of_use_warning_id` int(11) NOT NULL default '0',
  `meds_unit_of_use_id` int(11) NOT NULL default '0',
  `warning` int(11) default NULL,
  PRIMARY KEY  (`meds_unit_of_use_warning_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `meds_user_to_program`
--

CREATE TABLE IF NOT EXISTS `meds_user_to_program` (
  `user_id` int(11) NOT NULL default '0',
  `meds_program_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE IF NOT EXISTS `menu` (
  `menu_id` int(11) NOT NULL auto_increment,
  `site_section` varchar(50) NOT NULL default 'default',
  `parent` int(11) NOT NULL default '0',
  `dynamic_key` varchar(50) NOT NULL default '',
  `section` enum('children','more','dynamic') NOT NULL default 'children',
  `display_order` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `action` varchar(255) NOT NULL default '',
  `prefix` varchar(100) NOT NULL default 'main',
  PRIMARY KEY  (`menu_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=228988 ;

-- --------------------------------------------------------

--
-- Table structure for table `menu_form`
--

CREATE TABLE IF NOT EXISTS `menu_form` (
  `menu_form_id` int(11) NOT NULL default '0',
  `menu_id` int(11) NOT NULL default '0',
  `form_id` int(11) NOT NULL default '0',
  `title` varchar(50) NOT NULL default '',
  `custom_action` varchar(255) default NULL,
  PRIMARY KEY  (`menu_form_id`),
  KEY `menu_id` (`menu_id`),
  KEY `form_id` (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `menu_report`
--

CREATE TABLE IF NOT EXISTS `menu_report` (
  `menu_report_id` int(11) NOT NULL default '0',
  `menu_id` int(11) NOT NULL default '0',
  `report_template_id` int(11) NOT NULL default '0',
  `title` varchar(50) NOT NULL default '',
  `custom_action` varchar(255) default NULL,
  PRIMARY KEY  (`menu_report_id`),
  KEY `menu_id` (`menu_id`),
  KEY `report_template_id` (`report_template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `misc_charge`
--

CREATE TABLE IF NOT EXISTS `misc_charge` (
  `misc_charge_id` int(11) NOT NULL default '0',
  `encounter_id` int(11) NOT NULL default '0',
  `amount` float(7,2) NOT NULL default '0.00',
  `charge_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` varchar(50) NOT NULL default '',
  `note` text NOT NULL,
  PRIMARY KEY  (`misc_charge_id`),
  KEY `encounter_id` (`encounter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `name_history`
--

CREATE TABLE IF NOT EXISTS `name_history` (
  `name_history_id` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  `first_name` varchar(100) NOT NULL default '',
  `last_name` varchar(100) NOT NULL default '',
  `middle_name` varchar(50) NOT NULL default '',
  `update_date` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`name_history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `note`
--

CREATE TABLE IF NOT EXISTS `note` (
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

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE IF NOT EXISTS `notes` (
  `note_id` int(10) unsigned NOT NULL default '0',
  `revision_id` int(10) unsigned NOT NULL default '0',
  `user_id` int(10) unsigned NOT NULL default '0',
  `note` mediumtext NOT NULL,
  `create_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`note_id`),
  KEY `revision_id` (`revision_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nsdrDefinitionMethods`
--

CREATE TABLE IF NOT EXISTS `nsdrDefinitionMethods` (
  `uuid` char(36) NOT NULL,
  `methodName` char(50) default NULL,
  `method` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nsdrDefinitions`
--

CREATE TABLE IF NOT EXISTS `nsdrDefinitions` (
  `uuid` char(36) NOT NULL,
  `namespace` char(255) default NULL,
  `aliasFor` char(255) default NULL,
  `ORMClass` varchar(64) default NULL,
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `number`
--

CREATE TABLE IF NOT EXISTS `number` (
  `number_id` int(11) NOT NULL,
  `number_type` int(11) NOT NULL default '0',
  `notes` tinytext NOT NULL,
  `number` varchar(100) NOT NULL default '',
  `active` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`number_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='A phone number';

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `orderId` int(11) NOT NULL,
  `providerId` int(11) default NULL,
  `dateStart` datetime default NULL,
  `dateStop` datetime default NULL,
  `orderText` varchar(255) default NULL,
  `service` varchar(32) default NULL,
  `status` varchar(16) default NULL,
  `eSignatureId` int(11) NOT NULL default '0',
  PRIMARY KEY  (`orderId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ordo_registry`
--

CREATE TABLE IF NOT EXISTS `ordo_registry` (
  `ordo_id` int(11) NOT NULL default '0',
  `creator_id` int(11) NOT NULL default '0',
  `owner_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ordo_id`),
  KEY `creator_id` (`creator_id`,`owner_id`),
  KEY `owner_id` (`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ownership`
--

CREATE TABLE IF NOT EXISTS `ownership` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `participation_program`
--

CREATE TABLE IF NOT EXISTS `participation_program` (
  `participation_program_id` bigint(20) NOT NULL default '0',
  `adhoc` tinyint(4) NOT NULL,
  `class` varchar(255) NOT NULL default '',
  `type` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `form_id` int(11) NOT NULL,
  PRIMARY KEY  (`participation_program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `participation_program_basic`
--

CREATE TABLE IF NOT EXISTS `participation_program_basic` (
  `person_program_id` bigint(20) NOT NULL default '0',
  `federal_poverty_level` char(3) NOT NULL default '',
  `eligibility` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`person_program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `participation_program_clinic`
--

CREATE TABLE IF NOT EXISTS `participation_program_clinic` (
  `person_program_id` bigint(20) NOT NULL default '0',
  `eligibility` tinyint(4) NOT NULL default '0',
  `initial_date` date NOT NULL,
  `recent_date` date NOT NULL,
  PRIMARY KEY  (`person_program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE IF NOT EXISTS `patient` (
  `person_id` int(11) NOT NULL default '0',
  `is_default_provider_primary` int(11) NOT NULL default '0',
  `default_provider` int(11) NOT NULL default '0',
  `record_number` int(11) NOT NULL default '0',
  `employer_name` varchar(255) NOT NULL default '',
  `confidentiality` int(11) NOT NULL default '0',
  `specialNeedsNote` varchar(255) NOT NULL,
  `specialNeedsTranslator` tinyint(4) NOT NULL,
  `teamId` int(11) NOT NULL,
  PRIMARY KEY  (`person_id`),
  KEY `record_number` (`record_number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='An patient extends the person entity';

-- --------------------------------------------------------

--
-- Table structure for table `patientImmunizations`
--

CREATE TABLE IF NOT EXISTS `patientImmunizations` (
  `code` int(11) NOT NULL,
  `patientId` int(11) NOT NULL,
  `reportedNotAdministered` tinyint(4) NOT NULL,
  `patientReported` tinyint(4) NOT NULL,
  `series` varchar(255) NOT NULL,
  `reaction` varchar(255) NOT NULL,
  `repeatContraindicated` tinyint(4) NOT NULL,
  `immunization` varchar(255) NOT NULL,
  `comment` varchar(255) NOT NULL,
  PRIMARY KEY  (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `patient_chronic_code`
--

CREATE TABLE IF NOT EXISTS `patient_chronic_code` (
  `patient_id` int(11) NOT NULL default '0',
  `chronic_care_code` int(11) NOT NULL default '0',
  PRIMARY KEY  (`patient_id`,`chronic_care_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `patient_note`
--

CREATE TABLE IF NOT EXISTS `patient_note` (
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

-- --------------------------------------------------------

--
-- Table structure for table `patient_payment_plan`
--

CREATE TABLE IF NOT EXISTS `patient_payment_plan` (
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

-- --------------------------------------------------------

--
-- Table structure for table `patient_payment_plan_payment`
--

CREATE TABLE IF NOT EXISTS `patient_payment_plan_payment` (
  `patient_payment_plan_payment_id` int(11) NOT NULL default '0',
  `patient_payment_plan_id` int(11) NOT NULL default '0',
  `payment_date` date NOT NULL default '0000-00-00',
  `amount` float NOT NULL default '0',
  `paid_amount` float NOT NULL default '0',
  `paid` enum('Yes','No') NOT NULL default 'No',
  PRIMARY KEY  (`patient_payment_plan_payment_id`),
  KEY `patient_payment_plan_id` (`patient_payment_plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `patient_statistics`
--

CREATE TABLE IF NOT EXISTS `patient_statistics` (
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
  `education_level` int(11) NOT NULL default '0',
  `employment_status` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payer_group`
--

CREATE TABLE IF NOT EXISTS `payer_group` (
  `payer_group_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`payer_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE IF NOT EXISTS `payment` (
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

-- --------------------------------------------------------

--
-- Table structure for table `payment_claimline`
--

CREATE TABLE IF NOT EXISTS `payment_claimline` (
  `payment_claimline_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL default '0',
  `code_id` int(11) NOT NULL default '0',
  `coding_data_id` int(11) NOT NULL,
  `paid` float(7,2) NOT NULL default '0.00',
  `writeoff` float(7,2) NOT NULL default '0.00',
  `carry` float(7,2) NOT NULL default '0.00',
  PRIMARY KEY  (`payment_claimline_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `person`
--

CREATE TABLE IF NOT EXISTS `person` (
  `person_id` int(11) NOT NULL,
  `salutation` varchar(20) NOT NULL default '',
  `last_name` varchar(100) NOT NULL default '',
  `first_name` varchar(100) NOT NULL default '',
  `middle_name` varchar(50) NOT NULL default '',
  `suffix` varchar(12) NOT NULL,
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
  `active` tinyint(4) NOT NULL,
  `primary_practice_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`),
  KEY `primary_practice_id` (`primary_practice_id`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='A person in the system';

-- --------------------------------------------------------

--
-- Table structure for table `person_address`
--

CREATE TABLE IF NOT EXISTS `person_address` (
  `person_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  `address_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`,`address_id`),
  KEY `address_type` (`address_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Links a person to a address specifying the address type';

-- --------------------------------------------------------

--
-- Table structure for table `person_company`
--

CREATE TABLE IF NOT EXISTS `person_company` (
  `person_id` int(11) NOT NULL default '0',
  `company_id` int(11) NOT NULL default '0',
  `person_type` int(11) default NULL,
  PRIMARY KEY  (`person_id`,`company_id`),
  KEY `person_id` (`person_id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Links a person to a company and optionaly specifies the lin';

-- --------------------------------------------------------

--
-- Table structure for table `person_number`
--

CREATE TABLE IF NOT EXISTS `person_number` (
  `person_id` int(11) NOT NULL default '0',
  `number_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`,`number_id`),
  KEY `phone_id` (`number_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Links between people and phone_numbers';

-- --------------------------------------------------------

--
-- Table structure for table `person_participation_program`
--

CREATE TABLE IF NOT EXISTS `person_participation_program` (
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

-- --------------------------------------------------------

--
-- Table structure for table `person_person`
--

CREATE TABLE IF NOT EXISTS `person_person` (
  `person_person_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL default '0',
  `related_person_id` int(11) NOT NULL default '0',
  `relation_type` int(11) NOT NULL default '0',
  `guarantor` tinyint(1) NOT NULL default '0',
  `guarantor_priority` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_person_id`),
  UNIQUE KEY `person_id` (`person_id`,`related_person_id`,`relation_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `person_type`
--

CREATE TABLE IF NOT EXISTS `person_type` (
  `person_id` int(11) NOT NULL default '0',
  `person_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`,`person_type`),
  KEY `person_id` (`person_id`),
  KEY `person_type` (`person_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Link to specify person type';

-- --------------------------------------------------------

--
-- Table structure for table `practices`
--

CREATE TABLE IF NOT EXISTS `practices` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL default '',
  `website` varchar(255) NOT NULL default '',
  `identifier` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `practice_address`
--

CREATE TABLE IF NOT EXISTS `practice_address` (
  `practice_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  `address_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`practice_id`,`address_id`),
  KEY `address_id` (`address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Links a practice to a address specifying the address type';

-- --------------------------------------------------------

--
-- Table structure for table `practice_number`
--

CREATE TABLE IF NOT EXISTS `practice_number` (
  `practice_id` int(11) NOT NULL default '0',
  `number_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`practice_id`,`number_id`),
  KEY `person_id` (`practice_id`),
  KEY `phone_id` (`number_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Links between people and phone_numbers';

-- --------------------------------------------------------

--
-- Table structure for table `practice_setting`
--

CREATE TABLE IF NOT EXISTS `practice_setting` (
  `practice_setting_id` int(11) NOT NULL,
  `practice_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `value` text NOT NULL,
  `serialized` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`practice_setting_id`),
  UNIQUE KEY `practice_id` (`practice_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `preferences`
--

CREATE TABLE IF NOT EXISTS `preferences` (
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

-- --------------------------------------------------------

--
-- Table structure for table `problemListComments`
--

CREATE TABLE IF NOT EXISTS `problemListComments` (
  `problemListCommentId` int(11) NOT NULL,
  `problemListId` int(11) NOT NULL,
  `date` date default NULL,
  `comment` varchar(255) default NULL,
  `authorId` int(11) NOT NULL,
  PRIMARY KEY  (`problemListCommentId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `problemLists`
--

CREATE TABLE IF NOT EXISTS `problemLists` (
  `problemListId` int(11) NOT NULL,
  `code` varchar(10) default NULL,
  `codeTextShort` varchar(24) default NULL,
  `dateOfOnset` datetime default NULL,
  `service` varchar(255) default NULL,
  `personId` int(11) NOT NULL,
  `providerId` int(11) default NULL,
  `status` char(8) default NULL,
  `immediacy` char(9) default NULL,
  `lastUpdated` datetime default NULL,
  `flags` varchar(12) default NULL,
  `previousStatus` char(8) default NULL,
  PRIMARY KEY  (`problemListId`),
  KEY `personId` (`personId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `procedureCodeImmunizations`
--

CREATE TABLE IF NOT EXISTS `procedureCodeImmunizations` (
  `enumeration_value_id` int(11) NOT NULL default '0',
  `enumeration_id` int(11) NOT NULL default '0',
  `guid` char(36) NOT NULL,
  `systemName` char(24) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL default '',
  `sort` int(11) NOT NULL default '0',
  `extra1` varchar(255) NOT NULL default '',
  `extra2` varchar(255) NOT NULL default '',
  `status` int(1) NOT NULL default '1',
  `depth` tinyint(4) NOT NULL,
  `parent_id` int(11) NOT NULL,
  PRIMARY KEY  (`enumeration_value_id`),
  KEY `key` (`key`),
  KEY `enumeration_id` (`enumeration_id`),
  KEY `systemName` (`systemName`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `procedureCodesCPT`
--

CREATE TABLE IF NOT EXISTS `procedureCodesCPT` (
  `textLong` varchar(255) default NULL,
  `textShort` varchar(24) default NULL,
  `code` varchar(10) default NULL,
  KEY `code_text` (`textLong`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `procedureCodesImmunization`
--

CREATE TABLE IF NOT EXISTS `procedureCodesImmunization` (
  `textShort` varchar(255) NOT NULL,
  `code` varchar(11) NOT NULL,
  `textLong` varchar(255) NOT NULL,
  PRIMARY KEY  (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `provider`
--

CREATE TABLE IF NOT EXISTS `provider` (
  `person_id` int(11) NOT NULL default '0',
  `state_license_number` varchar(100) NOT NULL default '',
  `clia_number` varchar(100) NOT NULL default '',
  `dea_number` varchar(100) NOT NULL default '',
  `bill_as` int(11) NOT NULL default '0',
  `report_as` int(11) NOT NULL default '0',
  `routing_station` char(4) NOT NULL,
  `sureScriptsSPI` varchar(20) NOT NULL,
  `color` varchar(10) NOT NULL,
  PRIMARY KEY  (`person_id`),
  KEY `routing_station` (`routing_station`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `providerDashboardState`
--

CREATE TABLE IF NOT EXISTS `providerDashboardState` (
  `providerDashboardStateId` int(11) NOT NULL,
  `personId` int(11) NOT NULL,
  `facilityId` int(11) NOT NULL,
  `global` tinyint(1) NOT NULL COMMENT '1 global | 0 mine',
  `name` varchar(128) NOT NULL,
  `layout` char(2) NOT NULL COMMENT 'layout type for dashboardInnerLayout',
  `state` text NOT NULL,
  PRIMARY KEY  (`providerDashboardStateId`),
  KEY `personId` (`personId`,`facilityId`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `provider_to_insurance`
--

CREATE TABLE IF NOT EXISTS `provider_to_insurance` (
  `provider_to_insurance_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL default '0',
  `insurance_program_id` int(11) NOT NULL default '0',
  `provider_number` varchar(100) NOT NULL default '',
  `provider_number_type` int(11) NOT NULL default '0',
  `group_number` varchar(100) NOT NULL default '',
  `building_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`provider_to_insurance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pull_list`
--

CREATE TABLE IF NOT EXISTS `pull_list` (
  `appointment_id` int(11) NOT NULL default '0',
  `pull_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`appointment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `record_sequence`
--

CREATE TABLE IF NOT EXISTS `record_sequence` (
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1001 ;

-- --------------------------------------------------------

--
-- Table structure for table `recurrence`
--

CREATE TABLE IF NOT EXISTS `recurrence` (
  `recurrence_id` int(10) unsigned NOT NULL default '0',
  `start_date` date NOT NULL default '0000-00-00',
  `end_date` date NOT NULL default '0000-00-00',
  `start_time` time default NULL,
  `end_time` time default NULL,
  `recurrence_pattern_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`recurrence_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `recurrence_pattern`
--

CREATE TABLE IF NOT EXISTS `recurrence_pattern` (
  `recurrence_pattern_id` int(11) NOT NULL default '0',
  `pattern_type` enum('day','monthday','monthweek','yearmonthday','yearmonthweek','dayweek') NOT NULL default 'day',
  `number` int(11) default NULL,
  `weekday` enum('1','2','3','4','5','6','7') default NULL,
  `month` enum('01','02','03','04','05','06','07','08','09','10','11','12') default NULL,
  `monthday` tinyint(2) default NULL,
  `week_of_month` enum('First','Second','Third','Fourth','Last') default NULL,
  PRIMARY KEY  (`recurrence_pattern_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `refappointment`
--

CREATE TABLE IF NOT EXISTS `refappointment` (
  `refappointment_id` int(11) NOT NULL,
  `refrequest_id` int(11) NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `refpractice_id` int(11) NOT NULL default '0',
  `reflocation_id` int(11) NOT NULL default '0',
  `refprovider_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`refappointment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `refpatient_eligibility`
--

CREATE TABLE IF NOT EXISTS `refpatient_eligibility` (
  `refpatient_eligibility_id` int(11) NOT NULL,
  `eligibility` varchar(255) NOT NULL default '',
  `eligible_thru` date NOT NULL default '0000-00-00',
  `patient_id` int(11) NOT NULL default '0',
  `refprogram_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`refpatient_eligibility_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `refpractice`
--

CREATE TABLE IF NOT EXISTS `refpractice` (
  `refPractice_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `assign_by` enum('Practice','Provider') NOT NULL default 'Practice',
  `default_num_of_slots` int(11) NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`refPractice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `refPracticeLocation`
--

CREATE TABLE IF NOT EXISTS `refPracticeLocation` (
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

-- --------------------------------------------------------

--
-- Table structure for table `refpractice_specialty`
--

CREATE TABLE IF NOT EXISTS `refpractice_specialty` (
  `refpractice_specialty_id` int(11) NOT NULL,
  `specialty` int(11) NOT NULL default '0',
  `form` varchar(255) NOT NULL default '0',
  `refpractice_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`refpractice_specialty_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `refprogram`
--

CREATE TABLE IF NOT EXISTS `refprogram` (
  `refprogram_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL default '',
  `schema` int(11) NOT NULL default '0',
  PRIMARY KEY  (`refprogram_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `refprogram_member`
--

CREATE TABLE IF NOT EXISTS `refprogram_member` (
  `refprogram_member_id` int(11) NOT NULL,
  `refprogram_id` int(11) NOT NULL default '0',
  `external_id` int(11) NOT NULL default '0',
  `external_type` varchar(255) NOT NULL default '',
  `inactive` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`refprogram_member_id`),
  KEY `external_id` (`external_id`),
  KEY `refprogram_id` (`refprogram_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `refprogram_member_slot`
--

CREATE TABLE IF NOT EXISTS `refprogram_member_slot` (
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

-- --------------------------------------------------------

--
-- Table structure for table `refprovider`
--

CREATE TABLE IF NOT EXISTS `refprovider` (
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

-- --------------------------------------------------------

--
-- Table structure for table `refreferral_visit`
--

CREATE TABLE IF NOT EXISTS `refreferral_visit` (
  `refreferral_visit_id` int(11) NOT NULL,
  `refappointment_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`refreferral_visit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `refRequest`
--

CREATE TABLE IF NOT EXISTS `refRequest` (
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

-- --------------------------------------------------------

--
-- Table structure for table `refSpecialtyMap`
--

CREATE TABLE IF NOT EXISTS `refSpecialtyMap` (
  `refSpecialityMap_id` int(11) NOT NULL,
  `external_type` varchar(255) NOT NULL default '',
  `external_id` int(11) NOT NULL default '0',
  `enumeration_value_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`refSpecialityMap_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `refuser`
--

CREATE TABLE IF NOT EXISTS `refuser` (
  `refuser_id` int(11) NOT NULL,
  `external_user_id` int(11) NOT NULL default '0',
  `refusertype` int(11) NOT NULL default '0',
  `refprogram_id` int(11) NOT NULL default '0',
  `deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`refuser_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `relationship`
--

CREATE TABLE IF NOT EXISTS `relationship` (
  `relationship_id` int(11) NOT NULL,
  `parent_type` varchar(255) NOT NULL default '',
  `parent_id` int(11) NOT NULL default '0',
  `child_type` varchar(255) NOT NULL default '',
  `child_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`relationship_id`),
  KEY `parent_type` (`parent_type`,`parent_id`,`child_type`,`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE IF NOT EXISTS `reports` (
  `id` int(11) NOT NULL,
  `dbase` varchar(255) NOT NULL default '',
  `user` varchar(255) NOT NULL default '',
  `label` varchar(255) NOT NULL default '',
  `query` longtext NOT NULL,
  `description` mediumtext NOT NULL,
  `custom_id` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Report definitions TODO: change to Generic Seq';

-- --------------------------------------------------------

--
-- Table structure for table `report_snapshot`
--

CREATE TABLE IF NOT EXISTS `report_snapshot` (
  `report_snapshot_id` int(11) NOT NULL default '0',
  `report_id` int(11) NOT NULL default '0',
  `template_id` int(11) NOT NULL default '0',
  `snapshot_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `data` longtext NOT NULL,
  PRIMARY KEY  (`report_snapshot_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `report_templates`
--

CREATE TABLE IF NOT EXISTS `report_templates` (
  `report_template_id` int(11) NOT NULL default '0',
  `report_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `is_default` enum('yes','no') NOT NULL default 'yes',
  `sequence` int(11) NOT NULL default '100000',
  `custom_id` varchar(255) NOT NULL,
  PRIMARY KEY  (`report_template_id`),
  KEY `report_id` (`report_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Report templates';

-- --------------------------------------------------------

--
-- Table structure for table `revisions`
--

CREATE TABLE IF NOT EXISTS `revisions` (
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

-- --------------------------------------------------------

--
-- Table structure for table `revisions_db`
--

CREATE TABLE IF NOT EXISTS `revisions_db` (
  `revision_id` int(10) unsigned NOT NULL default '0',
  `filedata` blob NOT NULL,
  PRIMARY KEY  (`revision_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE IF NOT EXISTS `rooms` (
  `id` int(11) NOT NULL,
  `description` text NOT NULL,
  `number_seats` int(11) NOT NULL default '0',
  `building_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `color` varchar(10) NOT NULL default '',
  `routing_station` char(4) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `routing_station` (`routing_station`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `route_slip`
--

CREATE TABLE IF NOT EXISTS `route_slip` (
  `route_slip_id` int(11) NOT NULL default '0',
  `encounter_id` int(11) NOT NULL default '0',
  `report_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`route_slip_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `routing`
--

CREATE TABLE IF NOT EXISTS `routing` (
  `routingId` int(11) NOT NULL,
  `personId` int(11) NOT NULL,
  `stationId` char(4) NOT NULL,
  `fromStationId` char(4) NOT NULL,
  `timestamp` datetime NOT NULL,
  `checkInTimestamp` datetime NOT NULL,
  `appointmentId` int(11) NOT NULL,
  `providerId` int(11) NOT NULL,
  `roomId` int(11) NOT NULL,
  PRIMARY KEY  (`routingId`),
  KEY `station_id` (`stationId`,`fromStationId`,`timestamp`),
  KEY `appointment_id` (`appointmentId`),
  KEY `provider_id` (`providerId`,`roomId`),
  KEY `person_id` (`personId`),
  KEY `timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `routing_archive`
--

CREATE TABLE IF NOT EXISTS `routing_archive` (
  `routingId` int(11) NOT NULL,
  `personId` int(11) NOT NULL,
  `stationId` char(4) NOT NULL,
  `fromStationId` char(4) NOT NULL,
  `timestamp` datetime NOT NULL,
  `checkInTimestamp` datetime NOT NULL,
  `appointmentId` int(11) NOT NULL,
  `providerId` int(11) NOT NULL,
  `roomId` int(11) NOT NULL,
  PRIMARY KEY  (`routingId`),
  KEY `station_id` (`stationId`,`fromStationId`,`timestamp`),
  KEY `appointment_id` (`appointmentId`),
  KEY `provider_id` (`providerId`,`roomId`),
  KEY `person_id` (`personId`),
  KEY `timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE IF NOT EXISTS `schedule` (
  `schedule_id` int(10) unsigned NOT NULL,
  `title` varchar(150) default NULL,
  `description_long` text,
  `description_short` text,
  `schedule_code` varchar(255) default NULL,
  `provider_id` int(11) NOT NULL default '0',
  `room_id` int(11) NOT NULL,
  PRIMARY KEY  (`schedule_id`),
  KEY `provider_id` (`provider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `scheduleEvents`
--

CREATE TABLE IF NOT EXISTS `scheduleEvents` (
  `scheduleEventId` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL,
  `scheduleCode` char(4) NOT NULL,
  `providerId` int(11) NOT NULL,
  `roomId` int(11) NOT NULL,
  `scheduleId` int(11) NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  PRIMARY KEY  (`scheduleEventId`),
  KEY `start` (`start`,`end`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `schedule_event`
--

CREATE TABLE IF NOT EXISTS `schedule_event` (
  `event_id` int(11) NOT NULL default '0',
  `event_group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`event_id`),
  KEY `event_group_id` (`event_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `secondary_practice`
--

CREATE TABLE IF NOT EXISTS `secondary_practice` (
  `secondary_practice_id` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  `practice_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`secondary_practice_id`),
  KEY `person_id` (`person_id`,`practice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `self_mgmt_goals`
--

CREATE TABLE IF NOT EXISTS `self_mgmt_goals` (
  `self_mgmt_id` bigint(20) NOT NULL,
  `last_edit` timestamp NULL default NULL,
  `person_id` bigint(20) NOT NULL,
  `initiated` date NOT NULL,
  `completed` date NOT NULL,
  `type` tinyint(4) NOT NULL,
  PRIMARY KEY  (`self_mgmt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sequences`
--

CREATE TABLE IF NOT EXISTS `sequences` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sequences_daily`
--

CREATE TABLE IF NOT EXISTS `sequences_daily` (
  `counter` int(11) NOT NULL default '0',
  `updated_on` date NOT NULL default '0000-00-00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sequences_named`
--

CREATE TABLE IF NOT EXISTS `sequences_named` (
  `name` varchar(255) NOT NULL default '',
  `counter` int(11) NOT NULL default '0',
  PRIMARY KEY  (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `splash`
--

CREATE TABLE IF NOT EXISTS `splash` (
  `splash_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `message` longtext NOT NULL,
  PRIMARY KEY  (`splash_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `statement_history`
--

CREATE TABLE IF NOT EXISTS `statement_history` (
  `statement_history_id` int(11) NOT NULL default '0',
  `patient_id` int(11) NOT NULL default '0',
  `report_snapshot_id` int(11) NOT NULL default '0',
  `statement_number` int(11) NOT NULL default '0',
  `date_generated` datetime NOT NULL default '0000-00-00 00:00:00',
  `amount` float(7,2) NOT NULL default '0.00',
  `type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`statement_history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `statement_sequence`
--

CREATE TABLE IF NOT EXISTS `statement_sequence` (
  `id` int(11) NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE IF NOT EXISTS `states` (
  `zone_code` varchar(32) NOT NULL default '',
  `zone_name` varchar(32) NOT NULL default '',
  `country` char(3) default NULL,
  PRIMARY KEY  (`zone_code`,`zone_name`),
  KEY `country` (`country`),
  KEY `zone_code` (`zone_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `storables`
--

CREATE TABLE IF NOT EXISTS `storables` (
  `storable_id` int(10) unsigned NOT NULL default '0',
  `patient_id` int(11) NOT NULL,
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

-- --------------------------------------------------------

--
-- Table structure for table `storage_date`
--

CREATE TABLE IF NOT EXISTS `storage_date` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(50) NOT NULL default '',
  `value` date NOT NULL default '0000-00-00',
  `array_index` tinyint(4) NOT NULL,
  PRIMARY KEY  (`foreign_key`,`value_key`,`array_index`),
  KEY `foreign_key` (`foreign_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Generic way to store date values';

-- --------------------------------------------------------

--
-- Table structure for table `storage_int`
--

CREATE TABLE IF NOT EXISTS `storage_int` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(50) NOT NULL default '',
  `value` int(11) NOT NULL default '0',
  `array_index` tinyint(4) NOT NULL,
  PRIMARY KEY  (`foreign_key`,`value_key`,`array_index`),
  KEY `foreign_key` (`foreign_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Generic way to store integer values (also boolean)';

-- --------------------------------------------------------

--
-- Table structure for table `storage_string`
--

CREATE TABLE IF NOT EXISTS `storage_string` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(50) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  `array_index` tinyint(4) NOT NULL,
  PRIMARY KEY  (`foreign_key`,`value_key`,`array_index`),
  KEY `foreign_key` (`foreign_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Generic way to string values';

-- --------------------------------------------------------

--
-- Table structure for table `storage_text`
--

CREATE TABLE IF NOT EXISTS `storage_text` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(255) NOT NULL default '',
  `value` longtext NOT NULL,
  `array_index` tinyint(4) NOT NULL,
  PRIMARY KEY  (`foreign_key`,`value_key`,`array_index`),
  KEY `foreign_key` (`foreign_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Generic way to string values';

-- --------------------------------------------------------

--
-- Table structure for table `summary_columns`
--

CREATE TABLE IF NOT EXISTS `summary_columns` (
  `widget_form_id` bigint(20) default NULL,
  `type` varchar(100) default NULL,
  `name` varchar(100) default NULL,
  `summary_column_id` bigint(20) NOT NULL,
  `pretty_name` varchar(100) default NULL,
  `table_name` varchar(30) default NULL,
  UNIQUE KEY `idx_summary_columns` (`summary_column_id`,`widget_form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `superbill`
--

CREATE TABLE IF NOT EXISTS `superbill` (
  `superbill_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `practice_id` int(11) NOT NULL default '0',
  `status` int(11) NOT NULL default '0',
  PRIMARY KEY  (`superbill_id`),
  KEY `practice_id` (`practice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `superbill_data`
--

CREATE TABLE IF NOT EXISTS `superbill_data` (
  `superbill_data_id` int(11) NOT NULL default '0',
  `superbill_id` int(11) NOT NULL default '0',
  `code_id` int(11) NOT NULL default '0',
  `status` int(11) NOT NULL default '0',
  PRIMARY KEY  (`superbill_data_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `tag_id` int(10) unsigned NOT NULL,
  `tag` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`tag_id`),
  UNIQUE KEY `tag` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tags_storables`
--

CREATE TABLE IF NOT EXISTS `tags_storables` (
  `tag_id` int(10) unsigned NOT NULL default '0',
  `storable_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`tag_id`,`storable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE IF NOT EXISTS `teams` (
  `teamId` int(11) NOT NULL,
  `personId` int(11) NOT NULL,
  `cosignWithParent` tinyint(4) NOT NULL,
  `role` varchar(255) NOT NULL,
  PRIMARY KEY  (`teamId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `templatedText`
--

CREATE TABLE IF NOT EXISTS `templatedText` (
  `templateId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `template` longtext NOT NULL,
  PRIMARY KEY  (`templateId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tree`
--

CREATE TABLE IF NOT EXISTS `tree` (
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

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
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

-- --------------------------------------------------------

--
-- Table structure for table `userKeys`
--

CREATE TABLE IF NOT EXISTS `userKeys` (
  `userId` int(11) NOT NULL,
  `privateKey` text NOT NULL,
  `publicKey` text NOT NULL,
  `iv` char(30) NOT NULL,
  PRIMARY KEY  (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users_groups`
--

CREATE TABLE IF NOT EXISTS `users_groups` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  `foreign_id` int(11) NOT NULL default '0',
  `table` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_id` (`user_id`,`group_id`,`foreign_id`,`table`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `visit_queue`
--

CREATE TABLE IF NOT EXISTS `visit_queue` (
  `visit_queue_id` int(11) NOT NULL default '0',
  `visit_queue_template_id` int(11) NOT NULL default '0',
  `provider_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`visit_queue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `visit_queue_reason`
--

CREATE TABLE IF NOT EXISTS `visit_queue_reason` (
  `visit_queue_reason_id` int(11) NOT NULL,
  `ordernum` int(11) NOT NULL default '0',
  `appt_length` time NOT NULL default '01:00:00',
  `reason` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`visit_queue_reason_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `visit_queue_template`
--

CREATE TABLE IF NOT EXISTS `visit_queue_template` (
  `visit_queue_template_id` int(11) NOT NULL,
  `number_of_appointments` int(11) NOT NULL default '0',
  `visit_queue_reason_id` int(11) NOT NULL default '0',
  `visit_queue_rule_id` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`visit_queue_template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vitalSignGroups`
--

CREATE TABLE IF NOT EXISTS `vitalSignGroups` (
  `vitalSignGroupId` int(11) NOT NULL,
  `personId` int(11) NOT NULL,
  `dateTime` datetime NOT NULL,
  `enteringUserId` int(11) NOT NULL,
  `visitId` int(11) NOT NULL,
  `vitalSignTemplateId` int(11) NOT NULL,
  PRIMARY KEY  (`vitalSignGroupId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Table structure for table `vitalSignTemplates`
--

CREATE TABLE IF NOT EXISTS `vitalSignTemplates` (
  `vitalSignTemplateId` int(11) NOT NULL,
  `template` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `vitalSignValueQualifiers`
--

CREATE TABLE IF NOT EXISTS `vitalSignValueQualifiers` (
  `vitalSignValueQualifierId` int(11) NOT NULL,
  `vitalSignValueId` int(11) NOT NULL,
  `qualifier` int(11) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY  (`vitalSignValueQualifierId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `vitalSignValues`
--

CREATE TABLE IF NOT EXISTS `vitalSignValues` (
  `vitalSignValueId` int(11) NOT NULL,
  `vitalSignGroupId` int(11) NOT NULL,
  `unavailable` tinyint(4) NOT NULL,
  `refused` tinyint(4) NOT NULL,
  `vital` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `units` char(10) NOT NULL,
  PRIMARY KEY  (`vitalSignValueId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `widget_form`
--

CREATE TABLE IF NOT EXISTS `widget_form` (
  `widget_form_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `form_id` int(11) NOT NULL default '0',
  `type` int(11) NOT NULL default '0',
  `controller_name` varchar(100) NOT NULL,
  `show_on_medical_history` tinyint(1) NOT NULL,
  `report_id` int(11) NOT NULL,
  PRIMARY KEY  (`widget_form_id`),
  KEY `form_id` (`form_id`),
  KEY `report_id` (`report_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `x12imported_data`
--

CREATE TABLE IF NOT EXISTS `x12imported_data` (
  `x12imported_data_id` int(11) NOT NULL default '0',
  `data` longtext NOT NULL,
  `created_date` date NOT NULL default '0000-00-00',
  `filename` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`x12imported_data_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `x12transaction_data`
--

CREATE TABLE IF NOT EXISTS `x12transaction_data` (
  `transaction_data_id` int(11) NOT NULL default '0',
  `history_id` int(11) NOT NULL default '0',
  `raw` longtext NOT NULL,
  `transaction_status` varchar(255) NOT NULL default '',
  `payment_amount` float(7,2) NOT NULL default '0.00',
  `total_charge` float(7,2) NOT NULL default '0.00',
  `patient_responsibility` float(7,2) NOT NULL default '0.00',
  PRIMARY KEY  (`transaction_data_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `x12transaction_history`
--

CREATE TABLE IF NOT EXISTS `x12transaction_history` (
  `history_id` int(11) NOT NULL default '0',
  `source_id` int(11) NOT NULL default '0',
  `transaction_id` varchar(255) NOT NULL default '',
  `claim_id` varchar(255) NOT NULL default '',
  `applied_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `applied_by` int(11) NOT NULL default '0',
  `payment_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `zipcodes`
--

CREATE TABLE IF NOT EXISTS `zipcodes` (
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


--
-- ADDITIONAL TABLES
--
CREATE TABLE IF NOT EXISTS `healthStatusAlerts` (
  `healthStatusAlertId` int(11) NOT NULL,
  `message` text NOT NULL,
  `status` varchar(10) NOT NULL,
  `personId` int(11) NOT NULL,
  PRIMARY KEY  (`healthStatusAlertId`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `dataIntegrationActions` (
  `dataIntegrationActionId` int(11) NOT NULL,
  `guid` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `action` text NOT NULL,
  `handlerType` tinyint(4) NOT NULL,
  PRIMARY KEY  (`dataIntegrationActionId`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `dataIntegrationDestinations` (
  `dataIntegrationDestinationId` int(11) NOT NULL,
  `guid` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` char(4) NOT NULL,
  `connectInfo` text NOT NULL,
  `handlerType` tinyint(4) NOT NULL,
  PRIMARY KEY  (`dataIntegrationDestinationId`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `dataIntegrationDatasources` (
  `dataIntegrationDatasourceId` int(11) NOT NULL,
  `guid` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `datasource` text NOT NULL,
  `handlerType` tinyint(4) NOT NULL,
  PRIMARY KEY  (`dataIntegrationDatasourceId`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `dataIntegrationTemplates` (
  `dataIntegrationTemplateId` int(11) NOT NULL,
  `guid` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `template` text NOT NULL,
  `handlerType` tinyint(4) NOT NULL,
  PRIMARY KEY  (`dataIntegrationTemplateId`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `generalAlerts` (
  `generalAlertId` int(11) NOT NULL,
  `message` text NOT NULL,
  `urgency` int(11) NOT NULL,
  `status` varchar(12) NOT NULL,
  `dateTime` datetime NOT NULL,
  `teamId` char(10) NOT NULL,
  PRIMARY KEY  (`generalAlertId`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `handlers` (
  `handlerId` int(11) NOT NULL,
  `guid` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `direction` char(12) NOT NULL,
  `condition` varchar(255) NOT NULL,
  `conditionObject` text NOT NULL,
  `action` varchar(255) NOT NULL,
  `active` tinyint(4) NOT NULL,
  `review` tinyint(4) NOT NULL,
  `resolve` tinyint(4) NOT NULL,
  `handlerType` tinyint(4) NOT NULL,
  `dataIntegrationDatasourceId` int(11) NOT NULL,
  `dataIntegrationTemplateId` int(11) NOT NULL,
  `dataIntegrationDestinationId` int(11) NOT NULL,
  `dataIntegrationActionId` int(11) NOT NULL,
  PRIMARY KEY  (`handlerId`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `hl7Messages` (
  `hl7MessageId` int(11) NOT NULL,
  `message` text NOT NULL,
  `type` char(4) NOT NULL,
  PRIMARY KEY  (`hl7MessageId`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `processingErrors` (
  `processingErrorId` int(11) NOT NULL,
  `auditId` int(11) NOT NULL,
  `handlerId` int(11) NOT NULL,
  PRIMARY KEY  (`processingErrorId`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `healthStatusHandlerPatients` (
  `healthStatusHandlerId` int(11) NOT NULL,
  `personId` int(11) NOT NULL,
  PRIMARY KEY  (`healthStatusHandlerId`,`personId`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `teamMembers` (
  `teamMemberId` int(11) NOT NULL,
  `personId` int(11) NOT NULL,
  `cosignWithParent` tinyint(4) NOT NULL,
  `role` varchar(255) NOT NULL,
  PRIMARY KEY  (`teamMemberId`)
) ENGINE = InnoDB;


--
-- UPDATES
--
ALTER TABLE `audits` ADD `startProcessing` DATETIME NOT NULL ,
ADD `endProcessing` DATETIME NOT NULL ;

ALTER TABLE `nsdrDefinitions` ADD PRIMARY KEY(`uuid`);
ALTER TABLE `nsdrDefinitionMethods` ADD `nsdrDefinitionUuid` CHAR( 36 ) NOT NULL AFTER `uuid` ;
ALTER TABLE `nsdrDefinitionMethods` ADD PRIMARY KEY(`uuid`);

ALTER TABLE `healthStatusAlerts` ADD `healthStatusHandlerId` INT NOT NULL ,
ADD `dateDue` DATETIME NOT NULL ,
ADD `lastOccurence` DATETIME NOT NULL ,
ADD `priority` TINYINT NOT NULL ;

ALTER TABLE `address` ADD `person_id` INT NOT NULL ,
ADD `type` CHAR( 4 ) NOT NULL ,
ADD `active` TINYINT NOT NULL ;


ALTER TABLE `handlers` ADD `timeframe` VARCHAR( 36 ) NOT NULL AFTER `resolve` ;

ALTER TABLE `lab_order` ADD `person_id` INT NOT NULL ;

ALTER TABLE `medications` ADD `pharmacyId` INT NOT NULL ;

ALTER TABLE `patient` CHANGE `teamId` `teamId` CHAR( 10 ) NOT NULL ;


ALTER TABLE `audits` CHANGE `message` `message` VARCHAR( 255 ) NOT NULL ;

ALTER TABLE `patient` ADD `defaultPharmacyId` INT NOT NULL ,
ADD `signedHipaaDate` DATETIME NOT NULL ;

ALTER TABLE `number` ADD `person_id` INT NOT NULL ,
ADD `name` VARCHAR( 64 ) NOT NULL ,
ADD `type` INT NOT NULL ;

ALTER TABLE `reports` ADD `name` VARCHAR( 64 ) NOT NULL ,
ADD `systemName` VARCHAR( 255 ) NOT NULL ,
ADD `uuid` VARCHAR( 36 ) NOT NULL ;

ALTER TABLE `report_templates` ADD `uuid` VARCHAR( 36 ) NOT NULL ,
ADD `template` TEXT NOT NULL ;

ALTER TABLE `processingErrors` ADD UNIQUE (
  `auditId` ,
  `handlerId`
);

ALTER TABLE `generalAlerts` ADD `userId` INT NOT NULL ,
ADD `objectId` INT NOT NULL ,
ADD `objectClass` VARCHAR( 255 ) NOT NULL ,
CHANGE `urgency` `urgency` VARCHAR( 10 ) NOT NULL ;

CREATE TABLE IF NOT EXISTS `healthStatusHandlers` (
  `healthStatusHandlerId` int(11) NOT NULL,
  `guid` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `condition` varchar(255) NOT NULL,
  `handlerObject` text NOT NULL,
  `active` tinyint(4) NOT NULL,
  `timeframe` VARCHAR( 36 ) NOT NULL,
  `datasource` text NOT NULL,
  `template` text NOT NULL,
  PRIMARY KEY  (`healthStatusHandlerId`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `generalAlertHandlers` (
  `generalAlertHandlerId` int(11) NOT NULL,
  `guid` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `condition` varchar(255) NOT NULL,
  `handlerObject` text NOT NULL,
  `active` tinyint(4) NOT NULL,
  `datasource` text NOT NULL,
  `template` text NOT NULL,
  PRIMARY KEY  (`generalAlertHandlerId`)
) ENGINE = InnoDB;

ALTER TABLE `insurance_program` ADD `payer_identifier` VARCHAR( 255 ) NOT NULL ;

CREATE TABLE IF NOT EXISTS `diagnosisCodesAllergies` (
  `code` varchar(10) NOT NULL,
  `textShort` varchar(24) default NULL,
  `textLong` varchar(255) default NULL,
  PRIMARY KEY  (`code`)
) ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS `patientAllergies` (
  `patientAllergyId` int(11) NOT NULL,
  `causativeAgent` varchar(32) default NULL,
  `patientId` int(11) NOT NULL,
  `observerId` int(11) NOT NULL,
  `reactionType` varchar(32) NOT NULL,
  `observed` tinyint(1) NOT NULL,
  `severity` varchar(32) NOT NULL,
  `dateTimeCreated` datetime NOT NULL default '0000-00-00 00:00:00',
  `dateTimeReaction` datetime NOT NULL default '0000-00-00 00:00:00',
  `symptoms` varchar(255) NOT NULL,
  `comments` varchar(255) NOT NULL,
  `noKnownAllergies` tinyint(1) NOT NULL,
  `enteredInError` tinyint(1) default 0,
  PRIMARY KEY  (`patientAllergyId`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `appointmentTemplates` (
  `appointmentTemplateId` INT NOT NULL,
  `name` varchar(255) NOT NULL,
  `breakdown` text NOT NULL,
  PRIMARY KEY  (`appointmentTemplateId`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `patientProcedures` (
  `code` varchar(10) NOT NULL,
  `patientId` int(11) NOT NULL,
  `providerId` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `procedure` varchar(255) NOT NULL,
  `modifiers` varchar(255) NOT NULL,
  `comments` varchar(255) NOT NULL,
  PRIMARY KEY  (`code`,`patientId`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `patientDiagnosis` (
  `code` varchar(10) NOT NULL,
  `patientId` int(11) NOT NULL,
  `providerId` int(11) NOT NULL,
  `addToProblemList` tinyint(1) NOT NULL,
  `isPrimary` tinyint(1) NOT NULL,
  `diagnosis` varchar(255) NOT NULL,
  `comments` varchar(255) NOT NULL,
  PRIMARY KEY  (`code`,`patientId`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `patientVisitTypes` (
  `providerId` int(11) NOT NULL,
  `patientId` int(11) NOT NULL,
  `isPrimary` tinyint(1) NOT NULL,
  PRIMARY KEY  (`providerId`,`patientId`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `patientEducations` (
  `code` varchar(10) NOT NULL,
  `patientId` int(11) NOT NULL,
  `level` varchar(10) NOT NULL,
  `education` varchar(255) NOT NULL,
  `comments` varchar(255) NOT NULL,
  PRIMARY KEY  (`code`,`patientId`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `patientExams` (
  `code` varchar(10) NOT NULL,
  `patientId` int(11) NOT NULL,
  `result` varchar(10) NOT NULL,
  `exam` varchar(255) NOT NULL,
  `comments` varchar(255) NOT NULL,
  PRIMARY KEY  (`code`,`patientId`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `externalTeamMembers` (
  `externalTeamMemberId` int(11) NOT NULL,
  `personId` int(11) NOT NULL,
  `practice` varchar(100) NOT NULL,
  `provider` varchar(100) NOT NULL,
  `role` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `fax` varchar(15) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY  (`externalTeamMemberId`)
) ENGINE = InnoDB;
