-- phpMyAdmin SQL Dump
-- version 2.8.0.3-Debian-1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jun 19, 2006 at 04:45 PM
-- Server version: 4.1.15
-- PHP Version: 5.1.4-Debian-0.1~breezy1
-- 
-- Database: `clearhealth`
-- 

-- --------------------------------------------------------

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='ACL Table';

-- 
-- Dumping data for table `gacl_acl`
-- 

INSERT INTO `gacl_acl` (`id`, `section_value`, `allow`, `enabled`, `return_value`, `note`, `updated_date`) VALUES (26, 'user', 1, 1, '', 'Give Superadmn and access to everything even when no resource is selected', 1150741690),
(24, 'user', 1, 1, '', 'Give Super Admin access to everything ', 1129066383),
(38, 'user', 1, 1, '', '', 1129066412),
(40, 'user', 1, 1, '', '', 1129066435),
(36, 'user', 1, 1, '', '', 1129066460),
(37, 'user', 1, 1, '', '', 1119041365),
(32, 'user', 1, 1, '', 'Give billing users basic access to those sections', 1129066489),
(33, 'user', 1, 1, '', 'Give all users of the system access to basic app sections', 1112057091),
(39, 'user', 1, 1, '', '', 1129066506);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_acl_sections`
-- 

INSERT INTO `gacl_acl_sections` (`id`, `value`, `order_value`, `name`, `hidden`) VALUES (1, 'system', 1, 'System', 0),
(2, 'user', 2, 'User', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_acl_seq`
-- 

DROP TABLE IF EXISTS `gacl_acl_seq`;
CREATE TABLE `gacl_acl_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_acl_seq`
-- 

INSERT INTO `gacl_acl_seq` (`id`) VALUES (44),
(44);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_aco`
-- 

INSERT INTO `gacl_aco` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (11, 'actions', 'view', 10, 'view', 0),
(12, 'actions', 'edit', 11, 'edit', 0),
(13, 'actions', 'add', 12, 'add', 0),
(14, 'actions', 'delete', 13, 'delete', 0),
(16, 'actions', 'usage', 9, 'usage', 0),
(17, 'actions', 'uploadFile', 14, 'Upload A file', 0),
(18, 'actions', 'delete_owner', 15, 'Delete Owner', 0),
(19, 'actions', 'edit_owner', 16, 'Edit Owner', 0),
(20, 'actions', 'double_book', 17, 'Double Book Apointment', 0),
(21, 'actions', 'override', 1, 'override', 0);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_aco_map`
-- 

INSERT INTO `gacl_aco_map` (`acl_id`, `section_value`, `value`) VALUES (24, 'actions', 'add'),
(24, 'actions', 'delete'),
(24, 'actions', 'delete_owner'),
(24, 'actions', 'edit'),
(24, 'actions', 'edit_owner'),
(24, 'actions', 'uploadFile'),
(24, 'actions', 'usage'),
(24, 'actions', 'view'),
(26, 'actions', 'add'),
(26, 'actions', 'delete'),
(26, 'actions', 'delete_owner'),
(26, 'actions', 'edit'),
(26, 'actions', 'edit_owner'),
(26, 'actions', 'override'),
(26, 'actions', 'uploadFile'),
(26, 'actions', 'usage'),
(26, 'actions', 'view'),
(32, 'actions', 'add'),
(32, 'actions', 'delete'),
(32, 'actions', 'edit'),
(32, 'actions', 'usage'),
(32, 'actions', 'view'),
(33, 'actions', 'usage'),
(33, 'actions', 'view'),
(36, 'actions', 'usage'),
(36, 'actions', 'view'),
(37, 'actions', 'add'),
(37, 'actions', 'delete_owner'),
(37, 'actions', 'edit'),
(37, 'actions', 'usage'),
(37, 'actions', 'view'),
(38, 'actions', 'add'),
(38, 'actions', 'delete'),
(38, 'actions', 'delete_owner'),
(38, 'actions', 'edit'),
(38, 'actions', 'usage'),
(38, 'actions', 'view'),
(39, 'actions', 'add'),
(39, 'actions', 'delete'),
(39, 'actions', 'double_book'),
(39, 'actions', 'edit'),
(39, 'actions', 'uploadFile'),
(39, 'actions', 'usage'),
(39, 'actions', 'view'),
(40, 'actions', 'add'),
(40, 'actions', 'edit'),
(40, 'actions', 'usage'),
(40, 'actions', 'view');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_aco_sections`
-- 

INSERT INTO `gacl_aco_sections` (`id`, `value`, `order_value`, `name`, `hidden`) VALUES (11, 'actions', 10, 'Actions', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aco_sections_seq`
-- 

DROP TABLE IF EXISTS `gacl_aco_sections_seq`;
CREATE TABLE `gacl_aco_sections_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_aco_sections_seq`
-- 

INSERT INTO `gacl_aco_sections_seq` (`id`) VALUES (11),
(11);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aco_seq`
-- 

DROP TABLE IF EXISTS `gacl_aco_seq`;
CREATE TABLE `gacl_aco_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_aco_seq`
-- 

INSERT INTO `gacl_aco_seq` (`id`) VALUES (21),
(21);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro`
-- 

DROP TABLE IF EXISTS `gacl_aro`;
CREATE TABLE `gacl_aro` (
  `id` int(11) NOT NULL default '0',
  `section_value` varchar(240) NOT NULL default '0',
  `value` varchar(240) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_section_value_value_aro` (`section_value`,`value`),
  KEY `gacl_hidden_aro` (`hidden`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_aro`
-- 

INSERT INTO `gacl_aro` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (15, 'users', 'admin', 10, 'admin', 0),
(40, 'users', 'jprovider', 600024, 'jprovider', 0),
(41, 'users', 'test', 603610, 'test', 0),
(42, 'users', 'jmp', 604308, 'jmp', 0),
(43, 'users', 'jperez', 606361, 'jperez', 0),
(44, 'users', 'mvilla', 608478, 'mvilla', 0),
(46, 'users', 'tallen', 615299, 'tallen', 1);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_aro_groups`
-- 

INSERT INTO `gacl_aro_groups` (`id`, `parent_id`, `lft`, `rgt`, `name`, `value`) VALUES (10, 0, 1, 44, 'Root', 'root'),
(12, 23, 13, 14, 'System Admin', 'admin'),
(19, 10, 2, 11, 'User Types', 'users'),
(20, 19, 3, 4, 'Provider', 'provider'),
(21, 19, 5, 6, 'Mid-level', 'mid-level'),
(22, 19, 7, 8, 'Staff', 'staff'),
(23, 10, 12, 39, 'Roles', 'roles'),
(24, 23, 15, 16, 'Supervisor', 'supervisor'),
(26, 23, 17, 18, 'Front Office', 'front_office'),
(31, 23, 37, 38, 'Staff', 'role_staff'),
(28, 23, 19, 32, 'Billing User', 'billing_user'),
(29, 23, 33, 34, 'Medical Assistant', 'medical_assistant');

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_groups_id_seq`
-- 

DROP TABLE IF EXISTS `gacl_aro_groups_id_seq`;
CREATE TABLE `gacl_aro_groups_id_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_aro_groups_id_seq`
-- 

INSERT INTO `gacl_aro_groups_id_seq` (`id`) VALUES (59),
(59);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_groups_map`
-- 

DROP TABLE IF EXISTS `gacl_aro_groups_map`;
CREATE TABLE `gacl_aro_groups_map` (
  `acl_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acl_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_aro_groups_map`
-- 

INSERT INTO `gacl_aro_groups_map` (`acl_id`, `group_id`) VALUES (24, 12),
(26, 12),
(32, 28),
(33, 20),
(33, 21),
(33, 22),
(36, 31),
(37, 31),
(38, 29),
(39, 24),
(40, 28);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_aro_sections`
-- 

INSERT INTO `gacl_aro_sections` (`id`, `value`, `order_value`, `name`, `hidden`) VALUES (10, 'users', 10, 'Users', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_sections_seq`
-- 

DROP TABLE IF EXISTS `gacl_aro_sections_seq`;
CREATE TABLE `gacl_aro_sections_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_aro_sections_seq`
-- 

INSERT INTO `gacl_aro_sections_seq` (`id`) VALUES (11),
(11);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_seq`
-- 

DROP TABLE IF EXISTS `gacl_aro_seq`;
CREATE TABLE `gacl_aro_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_aro_seq`
-- 

INSERT INTO `gacl_aro_seq` (`id`) VALUES (46),
(46);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_axo`
-- 

INSERT INTO `gacl_axo` (`id`, `section_value`, `value`, `order_value`, `name`, `hidden`) VALUES (0, 'resources', 'main', 10, 'Section - Main', 0),
(19, 'resources', 'preferences', 10, 'Section - Preferences', 0),
(17, 'resources', 'default', 10, 'Section - Default', 0),
(16, 'resources', 'access', 10, 'Section - Access', 0),
(44, 'resources', 'practice', 10, 'Section - Practice', 0),
(43, 'resources', 'personschedule', 10, 'Section - PersonSchedule', 0),
(42, 'resources', 'patientfinder', 10, 'Section - PatientFinder', 0),
(41, 'resources', 'patient', 10, 'Section - Patient', 0),
(40, 'resources', 'location', 10, 'Section - Location', 0),
(39, 'resources', 'feeschedule', 10, 'Section - FeeSchedule', 0),
(38, 'resources', 'calendar', 10, 'Section - Calendar', 0),
(37, 'resources', 'user', 10, 'Section - User', 0),
(36, 'resources', 'enumeration', 10, 'Section - Enumeration', 0),
(45, 'resources', 'report', 10, 'Section - Report', 0),
(46, 'resources', 'schedule', 10, 'Section - Schedule', 0),
(47, 'resources', 'form', 10, 'Section - Form', 0),
(48, 'resources', 'billing', 10, 'Section - Billing', 0),
(49, 'resources', 'admin', 10, 'Section - Admin', 0),
(50, 'resources', 'document', 10, 'Section - Document', 0),
(51, 'resources', 'documentcategory', 10, 'Section - DocumentCategory', 0),
(52, 'resources', 'insurance', 10, 'Section - Insurance', 0),
(53, 'resources', 'superbill', 10, 'Section - Superbill', 0),
(54, 'resources', 'event', 10, 'Section - Event', 0),
(55, 'resources', 'occurence', 10, 'Section - Occurence', 0),
(56, 'resources', 'building', 10, 'Building', 0),
(57, 'resources', 'room', 10, 'room', 0),
(58, 'resources', 'pdf', 10, 'Section - PDF', 0),
(59, 'resources', 'coding', 10, 'Section - Coding', 0),
(60, 'resources', 'docs', 10, 'Section - Docs', 0),
(61, 'resources', 'eob', 10, 'Section - Eob', 0),
(62, 'resources', 'claim', 10, 'Section - Claim', 0),
(63, 'resources', 'freebgateway', 10, 'Section - FreeBGateway', 0),
(64, 'resources', 'main_calendar', 1, 'Main Group Calendar', 0),
(65, 'resources', 'main_billing', 2, 'Main Group Billing', 0),
(66, 'resources', 'main_patient', 3, 'Main Group Patient', 0),
(67, 'resources', 'main_admin', 4, 'Main Group Admin', 0),
(68, 'resources', 'account', 10, 'Section - Account', 0),
(69, 'resources', 'appointment', 10, 'Section - Appointment', 0),
(70, 'resources', 'ajax', 10, 'Section - Ajax', 0),
(71, 'resources', 'images', 10, 'Section - Images', 0),
(72, 'resources', 'css', 10, 'Section - Css', 0),
(73, 'resources', 'myaccount', 10, 'Section - MyAccount', 0),
(74, 'resources', 'patientdashboard', 10, 'Section - PatientDashboard', 0),
(75, 'resources', 'summaryreport', 10, 'Section - SummaryReport', 0),
(76, 'resources', 'encounter', 10, 'Section - Encounter', 0),
(77, 'resources', 'test', 10, 'Section - Test', 0),
(78, 'resources', 'appointmenttemplate', 10, 'Section - AppointmentTemplate', 0),
(79, 'resources', 'occurencebreakdown', 10, 'Section - OccurenceBreakdown', 0),
(80, 'resources', 'feeschedulediscount', 10, 'Section - FeeScheduleDiscount', 0),
(81, 'resources', 'patientstatistics', 10, 'Section - PatientStatistics', 0),
(82, 'resources', 'queue', 10, 'Section - Queue', 0),
(83, 'resources', 'print', 10, 'Section - Print', 0),
(84, 'resources', 'cronable', 10, 'Section - Cronable', 0),
(85, 'resources', 'base_access', 10, 'Section - Base_Access', 0),
(86, 'resources', 'ie7', 10, 'Section - Ie7', 0),
(87, 'resources', 'crud', 10, 'Section - CRUD', 0),
(88, 'resources', 'minimal', 10, 'Section - Minimal', 0),
(89, 'resources', 'secondarypractice', 10, 'Section - SecondaryPractice', 0),
(90, 'resources', 'masteraccounthistory', 10, 'Section - MasterAccountHistory', 0),
(91, 'resources', 'widgetform', 10, 'Section - WidgetForm', 0),
(92, 'resources', 'patientpaymentplan', 10, 'Section - PatientPaymentPlan', 0),
(93, 'resources', 'appointmentruleset', 10, 'Section - AppointmentRuleset', 0),
(94, 'resources', 'codingtemplate', 10, 'Section - CodingTemplate', 0),
(95, 'resources', 'patientmerge', 10, 'Section - PatientMerge', 0),
(96, 'resources', 'claimhistory', 10, 'Section - ClaimHistory', 0),
(97, 'resources', 'auditlog', 10, 'Section - AuditLog', 0),
(98, 'resources', 'visitqueue', 10, 'Section - VisitQueue', 0),
(99, 'resources', 'medicaleligibility', 10, 'Section - MediCalEligibility', 0),
(100, 'resources', 'labs', 10, 'Section - Labs', 0),
(101, 'resources', 'labimporter', 10, 'Section - LabImporter', 0),
(102, 'resources', 'x12import', 10, 'Section - X12Import', 0),
(103, 'resources', 'x12apply', 10, 'Section - X12Apply', 0),
(104, 'resources', 'calendaroccurence', 10, 'Section - CalendarOccurence', 0),
(105, 'resources', 'calendarajaxevent', 10, 'Section - CalendarAJAXEvent', 0),
(106, 'resources', 'calendardisplay', 10, 'Section - CalendarDisplay', 0),
(107, 'resources', 'calendarevent', 10, 'Section - CalendarEvent', 0);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_axo_groups`
-- 

INSERT INTO `gacl_axo_groups` (`id`, `parent_id`, `lft`, `rgt`, `name`, `value`) VALUES (10, 0, 1, 4, 'Root', 'root'),
(11, 10, 2, 3, 'All Site Sections', 'sections');

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_groups_id_seq`
-- 

DROP TABLE IF EXISTS `gacl_axo_groups_id_seq`;
CREATE TABLE `gacl_axo_groups_id_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_axo_groups_id_seq`
-- 

INSERT INTO `gacl_axo_groups_id_seq` (`id`) VALUES (11);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_groups_map`
-- 

DROP TABLE IF EXISTS `gacl_axo_groups_map`;
CREATE TABLE `gacl_axo_groups_map` (
  `acl_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acl_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_axo_groups_map`
-- 

INSERT INTO `gacl_axo_groups_map` (`acl_id`, `group_id`) VALUES (24, 11);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_axo_map`
-- 

INSERT INTO `gacl_axo_map` (`acl_id`, `section_value`, `value`) VALUES (32, 'resources', 'billing'),
(32, 'resources', 'claim'),
(32, 'resources', 'coding'),
(32, 'resources', 'document'),
(32, 'resources', 'eob'),
(32, 'resources', 'main_billing'),
(32, 'resources', 'patient'),
(33, 'resources', 'access'),
(33, 'resources', 'default'),
(33, 'resources', 'docs'),
(33, 'resources', 'pdf'),
(33, 'resources', 'preferences'),
(36, 'resources', 'calendar'),
(36, 'resources', 'location'),
(36, 'resources', 'main_calendar'),
(36, 'resources', 'main_patient'),
(36, 'resources', 'patient'),
(36, 'resources', 'patientfinder'),
(37, 'resources', 'appointment'),
(37, 'resources', 'calendar'),
(37, 'resources', 'location'),
(37, 'resources', 'patient'),
(37, 'resources', 'patientfinder'),
(38, 'resources', 'appointment'),
(38, 'resources', 'calendar'),
(38, 'resources', 'location'),
(38, 'resources', 'main_calendar'),
(38, 'resources', 'patient'),
(38, 'resources', 'patientfinder'),
(39, 'resources', 'appointment'),
(39, 'resources', 'calendar'),
(39, 'resources', 'event'),
(39, 'resources', 'location'),
(39, 'resources', 'main_calendar'),
(39, 'resources', 'occurence'),
(39, 'resources', 'patient'),
(39, 'resources', 'patientfinder'),
(39, 'resources', 'schedule'),
(40, 'resources', 'admin'),
(40, 'resources', 'appointment'),
(40, 'resources', 'billing'),
(40, 'resources', 'calendar'),
(40, 'resources', 'claim'),
(40, 'resources', 'coding'),
(40, 'resources', 'eob'),
(40, 'resources', 'event'),
(40, 'resources', 'feeschedule'),
(40, 'resources', 'insurance'),
(40, 'resources', 'location'),
(40, 'resources', 'main_billing'),
(40, 'resources', 'main_calendar'),
(40, 'resources', 'main_patient'),
(40, 'resources', 'occurence'),
(40, 'resources', 'patient'),
(40, 'resources', 'patientfinder'),
(40, 'resources', 'personschedule'),
(40, 'resources', 'practice'),
(40, 'resources', 'schedule'),
(40, 'resources', 'superbill');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_axo_sections`
-- 

INSERT INTO `gacl_axo_sections` (`id`, `value`, `order_value`, `name`, `hidden`) VALUES (0, 'resources', 10, 'Resources', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_sections_seq`
-- 

DROP TABLE IF EXISTS `gacl_axo_sections_seq`;
CREATE TABLE `gacl_axo_sections_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_axo_sections_seq`
-- 

INSERT INTO `gacl_axo_sections_seq` (`id`) VALUES (30);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_seq`
-- 

DROP TABLE IF EXISTS `gacl_axo_seq`;
CREATE TABLE `gacl_axo_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_axo_seq`
-- 

INSERT INTO `gacl_axo_seq` (`id`) VALUES (107);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_groups_aro_map`
-- 

DROP TABLE IF EXISTS `gacl_groups_aro_map`;
CREATE TABLE `gacl_groups_aro_map` (
  `group_id` int(11) NOT NULL default '0',
  `aro_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`aro_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_groups_aro_map`
-- 

INSERT INTO `gacl_groups_aro_map` (`group_id`, `aro_id`) VALUES (12, 15),
(20, 40),
(20, 43),
(20, 44),
(20, 46),
(29, 44),
(31, 40),
(31, 41),
(31, 42),
(31, 43);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_groups_axo_map`
-- 

DROP TABLE IF EXISTS `gacl_groups_axo_map`;
CREATE TABLE `gacl_groups_axo_map` (
  `group_id` int(11) NOT NULL default '0',
  `axo_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`axo_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_groups_axo_map`
-- 

INSERT INTO `gacl_groups_axo_map` (`group_id`, `axo_id`) VALUES (11, 0),
(11, 16),
(11, 17),
(11, 18),
(11, 19),
(11, 36),
(11, 37),
(11, 38),
(11, 39),
(11, 40),
(11, 41),
(11, 42),
(11, 43),
(11, 44),
(11, 45),
(11, 46),
(11, 47),
(11, 48),
(11, 49),
(11, 50),
(11, 51),
(11, 52),
(11, 53),
(11, 54),
(11, 55),
(11, 56),
(11, 57),
(11, 58),
(11, 59),
(11, 60),
(11, 61),
(11, 62),
(11, 63),
(11, 64),
(11, 65),
(11, 66),
(11, 67),
(11, 68),
(11, 69),
(11, 70),
(11, 71),
(11, 72),
(11, 73),
(11, 74),
(11, 75),
(11, 76),
(11, 77),
(11, 78),
(11, 79),
(11, 80),
(11, 81),
(11, 82),
(11, 83),
(11, 84),
(11, 85),
(11, 86),
(11, 87),
(11, 88),
(11, 89),
(11, 90),
(11, 91),
(11, 92),
(11, 93),
(11, 94),
(11, 95),
(11, 96),
(11, 97),
(11, 98),
(11, 99),
(11, 100),
(11, 101),
(11, 102),
(11, 103),
(11, 104),
(11, 105),
(11, 106),
(11, 107);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_phpgacl`
-- 

DROP TABLE IF EXISTS `gacl_phpgacl`;
CREATE TABLE `gacl_phpgacl` (
  `name` varchar(230) NOT NULL default '',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_phpgacl`
-- 

INSERT INTO `gacl_phpgacl` (`name`, `value`) VALUES ('version', '3.3.3'),
('schema_version', '2.1');

