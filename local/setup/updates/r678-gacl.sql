-- phpMyAdmin SQL Dump
-- version 2.6.1-rc2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jun 17, 2005 at 01:58 PM
-- Server version: 4.0.23
-- PHP Version: 4.3.11

SET FOREIGN_KEY_CHECKS=0;

SET AUTOCOMMIT=0;
START TRANSACTION;

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
) TYPE=MyISAM COMMENT='ACL Table';

-- 
-- Dumping data for table `gacl_acl`
-- 

INSERT INTO `gacl_acl` VALUES (26, 'user', 1, 1, '', 'Give Superadmn and Supervisors access to everything', 1119041473);
INSERT INTO `gacl_acl` VALUES (24, 'user', 1, 1, '', 'Give Super Admin access to everything even when no resource is selected', 1119041463);
INSERT INTO `gacl_acl` VALUES (38, 'user', 1, 1, '', '', 1119041415);
INSERT INTO `gacl_acl` VALUES (40, 'user', 1, 1, '', '', 1119041810);
INSERT INTO `gacl_acl` VALUES (29, 'user', 0, 1, '', 'Deny Supervisors access to some system wide configuration sections', 1112057023);
INSERT INTO `gacl_acl` VALUES (36, 'user', 1, 1, '', '', 1119041256);
INSERT INTO `gacl_acl` VALUES (37, 'user', 1, 1, '', '', 1119041365);
INSERT INTO `gacl_acl` VALUES (32, 'user', 1, 1, '', 'Give billing users basic access to those sections', 1112160920);
INSERT INTO `gacl_acl` VALUES (33, 'user', 1, 1, '', 'Give all users of the system access to basic app sections', 1112057091);
INSERT INTO `gacl_acl` VALUES (39, 'user', 1, 1, '', '', 1119041735);

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
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_acl_sections`
-- 

INSERT INTO `gacl_acl_sections` VALUES (1, 'system', 1, 'System', 0);
INSERT INTO `gacl_acl_sections` VALUES (2, 'user', 2, 'User', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_acl_seq`
-- 

DROP TABLE IF EXISTS `gacl_acl_seq`;
CREATE TABLE `gacl_acl_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_acl_seq`
-- 

INSERT INTO `gacl_acl_seq` VALUES (40);
INSERT INTO `gacl_acl_seq` VALUES (40);

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
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_aco`
-- 

INSERT INTO `gacl_aco` VALUES (11, 'actions', 'view', 10, 'view', 0);
INSERT INTO `gacl_aco` VALUES (12, 'actions', 'edit', 11, 'edit', 0);
INSERT INTO `gacl_aco` VALUES (13, 'actions', 'add', 12, 'add', 0);
INSERT INTO `gacl_aco` VALUES (14, 'actions', 'delete', 13, 'delete', 0);
INSERT INTO `gacl_aco` VALUES (16, 'actions', 'usage', 9, 'usage', 0);
INSERT INTO `gacl_aco` VALUES (17, 'actions', 'uploadFile', 14, 'Upload A file', 0);
INSERT INTO `gacl_aco` VALUES (18, 'actions', 'delete_owner', 15, 'Delete Owner', 0);
INSERT INTO `gacl_aco` VALUES (19, 'actions', 'edit_owner', 16, 'Edit Owner', 0);
INSERT INTO `gacl_aco` VALUES (20, 'actions', 'double_book', 17, 'Double Book Apointment', 0);

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
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_aco_map`
-- 

INSERT INTO `gacl_aco_map` VALUES (24, 'actions', 'add');
INSERT INTO `gacl_aco_map` VALUES (24, 'actions', 'delete');
INSERT INTO `gacl_aco_map` VALUES (24, 'actions', 'delete_owner');
INSERT INTO `gacl_aco_map` VALUES (24, 'actions', 'edit');
INSERT INTO `gacl_aco_map` VALUES (24, 'actions', 'edit_owner');
INSERT INTO `gacl_aco_map` VALUES (24, 'actions', 'uploadFile');
INSERT INTO `gacl_aco_map` VALUES (24, 'actions', 'usage');
INSERT INTO `gacl_aco_map` VALUES (24, 'actions', 'view');
INSERT INTO `gacl_aco_map` VALUES (26, 'actions', 'add');
INSERT INTO `gacl_aco_map` VALUES (26, 'actions', 'delete');
INSERT INTO `gacl_aco_map` VALUES (26, 'actions', 'delete_owner');
INSERT INTO `gacl_aco_map` VALUES (26, 'actions', 'edit');
INSERT INTO `gacl_aco_map` VALUES (26, 'actions', 'edit_owner');
INSERT INTO `gacl_aco_map` VALUES (26, 'actions', 'uploadFile');
INSERT INTO `gacl_aco_map` VALUES (26, 'actions', 'usage');
INSERT INTO `gacl_aco_map` VALUES (26, 'actions', 'view');
INSERT INTO `gacl_aco_map` VALUES (29, 'actions', 'add');
INSERT INTO `gacl_aco_map` VALUES (29, 'actions', 'edit');
INSERT INTO `gacl_aco_map` VALUES (32, 'actions', 'add');
INSERT INTO `gacl_aco_map` VALUES (32, 'actions', 'delete');
INSERT INTO `gacl_aco_map` VALUES (32, 'actions', 'edit');
INSERT INTO `gacl_aco_map` VALUES (32, 'actions', 'usage');
INSERT INTO `gacl_aco_map` VALUES (32, 'actions', 'view');
INSERT INTO `gacl_aco_map` VALUES (33, 'actions', 'usage');
INSERT INTO `gacl_aco_map` VALUES (33, 'actions', 'view');
INSERT INTO `gacl_aco_map` VALUES (36, 'actions', 'usage');
INSERT INTO `gacl_aco_map` VALUES (36, 'actions', 'view');
INSERT INTO `gacl_aco_map` VALUES (37, 'actions', 'add');
INSERT INTO `gacl_aco_map` VALUES (37, 'actions', 'delete_owner');
INSERT INTO `gacl_aco_map` VALUES (37, 'actions', 'edit');
INSERT INTO `gacl_aco_map` VALUES (37, 'actions', 'usage');
INSERT INTO `gacl_aco_map` VALUES (37, 'actions', 'view');
INSERT INTO `gacl_aco_map` VALUES (38, 'actions', 'add');
INSERT INTO `gacl_aco_map` VALUES (38, 'actions', 'delete');
INSERT INTO `gacl_aco_map` VALUES (38, 'actions', 'delete_owner');
INSERT INTO `gacl_aco_map` VALUES (38, 'actions', 'edit');
INSERT INTO `gacl_aco_map` VALUES (38, 'actions', 'usage');
INSERT INTO `gacl_aco_map` VALUES (38, 'actions', 'view');
INSERT INTO `gacl_aco_map` VALUES (39, 'actions', 'add');
INSERT INTO `gacl_aco_map` VALUES (39, 'actions', 'delete');
INSERT INTO `gacl_aco_map` VALUES (39, 'actions', 'double_book');
INSERT INTO `gacl_aco_map` VALUES (39, 'actions', 'edit');
INSERT INTO `gacl_aco_map` VALUES (39, 'actions', 'uploadFile');
INSERT INTO `gacl_aco_map` VALUES (39, 'actions', 'usage');
INSERT INTO `gacl_aco_map` VALUES (39, 'actions', 'view');
INSERT INTO `gacl_aco_map` VALUES (40, 'actions', 'add');
INSERT INTO `gacl_aco_map` VALUES (40, 'actions', 'edit');
INSERT INTO `gacl_aco_map` VALUES (40, 'actions', 'usage');
INSERT INTO `gacl_aco_map` VALUES (40, 'actions', 'view');

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
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_aco_sections`
-- 

INSERT INTO `gacl_aco_sections` VALUES (11, 'actions', 10, 'Actions', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aco_sections_seq`
-- 

DROP TABLE IF EXISTS `gacl_aco_sections_seq`;
CREATE TABLE `gacl_aco_sections_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_aco_sections_seq`
-- 

INSERT INTO `gacl_aco_sections_seq` VALUES (11);
INSERT INTO `gacl_aco_sections_seq` VALUES (11);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aco_seq`
-- 

DROP TABLE IF EXISTS `gacl_aco_seq`;
CREATE TABLE `gacl_aco_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_aco_seq`
-- 

INSERT INTO `gacl_aco_seq` VALUES (20);
INSERT INTO `gacl_aco_seq` VALUES (20);

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
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_aro`
-- 

INSERT INTO `gacl_aro` VALUES (15, 'users', 'admin', 10, 'Admin', 0);
INSERT INTO `gacl_aro` VALUES (26, 'users', 'jconrad', 1111, 'jconrad', 1);
INSERT INTO `gacl_aro` VALUES (27, 'users', 'rdoc', 17045, 'rdoc', 1);
INSERT INTO `gacl_aro` VALUES (28, 'users', 'aagona', 17484, 'aagona', 1);
INSERT INTO `gacl_aro` VALUES (29, 'users', 'aaugustin', 17591, 'aaugustin', 1);
INSERT INTO `gacl_aro` VALUES (30, 'users', 'bbaritone', 17601, 'bbaritone', 1);
INSERT INTO `gacl_aro` VALUES (31, 'users', 'cchaplin', 17609, 'cchaplin', 1);
INSERT INTO `gacl_aro` VALUES (32, 'users', 'ddunkin', 17617, 'ddunkin', 1);
INSERT INTO `gacl_aro` VALUES (33, 'users', 'eeverstone', 17626, 'eeverstone', 1);
INSERT INTO `gacl_aro` VALUES (34, 'users', 'fflintstone', 17635, 'fflintstone', 1);
INSERT INTO `gacl_aro` VALUES (35, 'users', 'mminton', 1121, 'mminton', 1);

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
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_aro_groups`
-- 

INSERT INTO `gacl_aro_groups` VALUES (10, 0, 1, 26, 'Root', 'root');
INSERT INTO `gacl_aro_groups` VALUES (12, 23, 11, 12, 'System Admin', 'admin');
INSERT INTO `gacl_aro_groups` VALUES (19, 10, 2, 9, 'User Types', 'users');
INSERT INTO `gacl_aro_groups` VALUES (20, 19, 3, 4, 'Provider', 'provider');
INSERT INTO `gacl_aro_groups` VALUES (21, 19, 5, 6, 'Mid-level', 'mid-level');
INSERT INTO `gacl_aro_groups` VALUES (22, 19, 7, 8, 'Staff', 'staff');
INSERT INTO `gacl_aro_groups` VALUES (23, 10, 10, 25, 'Roles', 'roles');
INSERT INTO `gacl_aro_groups` VALUES (24, 23, 13, 14, 'Supervisor', 'supervisor');
INSERT INTO `gacl_aro_groups` VALUES (26, 23, 15, 16, 'Front Office', 'front_office');
INSERT INTO `gacl_aro_groups` VALUES (31, 23, 23, 24, 'Staff', 'role_staff');
INSERT INTO `gacl_aro_groups` VALUES (28, 23, 17, 18, 'Biller', 'billing_user');
INSERT INTO `gacl_aro_groups` VALUES (29, 23, 19, 20, 'Medical Assistant', 'medical_assistant');

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_groups_id_seq`
-- 

DROP TABLE IF EXISTS `gacl_aro_groups_id_seq`;
CREATE TABLE `gacl_aro_groups_id_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_aro_groups_id_seq`
-- 

INSERT INTO `gacl_aro_groups_id_seq` VALUES (31);
INSERT INTO `gacl_aro_groups_id_seq` VALUES (31);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_groups_map`
-- 

DROP TABLE IF EXISTS `gacl_aro_groups_map`;
CREATE TABLE `gacl_aro_groups_map` (
  `acl_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acl_id`,`group_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_aro_groups_map`
-- 

INSERT INTO `gacl_aro_groups_map` VALUES (24, 12);
INSERT INTO `gacl_aro_groups_map` VALUES (26, 24);
INSERT INTO `gacl_aro_groups_map` VALUES (29, 24);
INSERT INTO `gacl_aro_groups_map` VALUES (32, 28);
INSERT INTO `gacl_aro_groups_map` VALUES (33, 20);
INSERT INTO `gacl_aro_groups_map` VALUES (33, 21);
INSERT INTO `gacl_aro_groups_map` VALUES (33, 22);
INSERT INTO `gacl_aro_groups_map` VALUES (36, 31);
INSERT INTO `gacl_aro_groups_map` VALUES (37, 31);
INSERT INTO `gacl_aro_groups_map` VALUES (38, 29);
INSERT INTO `gacl_aro_groups_map` VALUES (39, 24);
INSERT INTO `gacl_aro_groups_map` VALUES (40, 28);

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
) TYPE=MyISAM;

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
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_aro_sections`
-- 

INSERT INTO `gacl_aro_sections` VALUES (10, 'users', 10, 'Users', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_sections_seq`
-- 

DROP TABLE IF EXISTS `gacl_aro_sections_seq`;
CREATE TABLE `gacl_aro_sections_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_aro_sections_seq`
-- 

INSERT INTO `gacl_aro_sections_seq` VALUES (11);
INSERT INTO `gacl_aro_sections_seq` VALUES (11);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_seq`
-- 

DROP TABLE IF EXISTS `gacl_aro_seq`;
CREATE TABLE `gacl_aro_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_aro_seq`
-- 

INSERT INTO `gacl_aro_seq` VALUES (35);
INSERT INTO `gacl_aro_seq` VALUES (35);

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
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_axo`
-- 

INSERT INTO `gacl_axo` VALUES (0, 'resources', 'main', 10, 'Section - Main', 0);
INSERT INTO `gacl_axo` VALUES (19, 'resources', 'preferences', 10, 'Section - Preferences', 0);
INSERT INTO `gacl_axo` VALUES (17, 'resources', 'default', 10, 'Section - Default', 0);
INSERT INTO `gacl_axo` VALUES (16, 'resources', 'access', 10, 'Section - Access', 0);
INSERT INTO `gacl_axo` VALUES (44, 'resources', 'practice', 10, 'Section - Practice', 0);
INSERT INTO `gacl_axo` VALUES (43, 'resources', 'personschedule', 10, 'Section - PersonSchedule', 0);
INSERT INTO `gacl_axo` VALUES (42, 'resources', 'patientfinder', 10, 'Section - PatientFinder', 0);
INSERT INTO `gacl_axo` VALUES (41, 'resources', 'patient', 10, 'Section - Patient', 0);
INSERT INTO `gacl_axo` VALUES (40, 'resources', 'location', 10, 'Section - Location', 0);
INSERT INTO `gacl_axo` VALUES (39, 'resources', 'feeschedule', 10, 'Section - FeeSchedule', 0);
INSERT INTO `gacl_axo` VALUES (38, 'resources', 'calendar', 10, 'Section - Calendar', 0);
INSERT INTO `gacl_axo` VALUES (37, 'resources', 'user', 10, 'Section - User', 0);
INSERT INTO `gacl_axo` VALUES (36, 'resources', 'enumeration', 10, 'Section - Enumeration', 0);
INSERT INTO `gacl_axo` VALUES (45, 'resources', 'report', 10, 'Section - Report', 0);
INSERT INTO `gacl_axo` VALUES (46, 'resources', 'schedule', 10, 'Section - Schedule', 0);
INSERT INTO `gacl_axo` VALUES (47, 'resources', 'form', 10, 'Section - Form', 0);
INSERT INTO `gacl_axo` VALUES (48, 'resources', 'billing', 10, 'Section - Billing', 0);
INSERT INTO `gacl_axo` VALUES (49, 'resources', 'admin', 10, 'Section - Admin', 0);
INSERT INTO `gacl_axo` VALUES (50, 'resources', 'document', 10, 'Section - Document', 0);
INSERT INTO `gacl_axo` VALUES (51, 'resources', 'documentcategory', 10, 'Section - DocumentCategory', 0);
INSERT INTO `gacl_axo` VALUES (52, 'resources', 'insurance', 10, 'Section - Insurance', 0);
INSERT INTO `gacl_axo` VALUES (53, 'resources', 'superbill', 10, 'Section - Superbill', 0);
INSERT INTO `gacl_axo` VALUES (54, 'resources', 'event', 10, 'Section - Event', 0);
INSERT INTO `gacl_axo` VALUES (55, 'resources', 'occurence', 10, 'Section - Occurence', 0);
INSERT INTO `gacl_axo` VALUES (56, 'resources', 'building', 10, 'Building', 0);
INSERT INTO `gacl_axo` VALUES (57, 'resources', 'room', 10, 'room', 0);
INSERT INTO `gacl_axo` VALUES (58, 'resources', 'pdf', 10, 'Section - PDF', 0);
INSERT INTO `gacl_axo` VALUES (59, 'resources', 'coding', 10, 'Section - Coding', 0);
INSERT INTO `gacl_axo` VALUES (60, 'resources', 'docs', 10, 'Section - Docs', 0);
INSERT INTO `gacl_axo` VALUES (61, 'resources', 'eob', 10, 'Section - Eob', 0);
INSERT INTO `gacl_axo` VALUES (62, 'resources', 'claim', 10, 'Section - Claim', 0);
INSERT INTO `gacl_axo` VALUES (63, 'resources', 'freebgateway', 10, 'Section - FreeBGateway', 0);
INSERT INTO `gacl_axo` VALUES (64, 'resources', 'main_calendar', 1, 'Main Group Calendar', 0);
INSERT INTO `gacl_axo` VALUES (65, 'resources', 'main_billing', 2, 'Main Group Billing', 0);
INSERT INTO `gacl_axo` VALUES (66, 'resources', 'main_patient', 3, 'Main Group Patient', 0);
INSERT INTO `gacl_axo` VALUES (67, 'resources', 'main_admin', 4, 'Main Group Admin', 0);
INSERT INTO `gacl_axo` VALUES (68, 'resources', 'account', 10, 'Section - Account', 0);
INSERT INTO `gacl_axo` VALUES (69, 'resources', 'appointment', 10, 'Section - Appointment', 0);

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
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_axo_groups`
-- 

INSERT INTO `gacl_axo_groups` VALUES (10, 0, 1, 4, 'Root', 'root');
INSERT INTO `gacl_axo_groups` VALUES (11, 10, 2, 3, 'All Site Sections', 'sections');

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_groups_id_seq`
-- 

DROP TABLE IF EXISTS `gacl_axo_groups_id_seq`;
CREATE TABLE `gacl_axo_groups_id_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_axo_groups_id_seq`
-- 

INSERT INTO `gacl_axo_groups_id_seq` VALUES (11);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_groups_map`
-- 

DROP TABLE IF EXISTS `gacl_axo_groups_map`;
CREATE TABLE `gacl_axo_groups_map` (
  `acl_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acl_id`,`group_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_axo_groups_map`
-- 

INSERT INTO `gacl_axo_groups_map` VALUES (24, 11);

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
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_axo_map`
-- 

INSERT INTO `gacl_axo_map` VALUES (29, 'resources', 'documentcategory');
INSERT INTO `gacl_axo_map` VALUES (29, 'resources', 'enumeration');
INSERT INTO `gacl_axo_map` VALUES (29, 'resources', 'feeschedule');
INSERT INTO `gacl_axo_map` VALUES (29, 'resources', 'form');
INSERT INTO `gacl_axo_map` VALUES (29, 'resources', 'report');
INSERT INTO `gacl_axo_map` VALUES (29, 'resources', 'superbill');
INSERT INTO `gacl_axo_map` VALUES (29, 'resources', 'user');
INSERT INTO `gacl_axo_map` VALUES (32, 'resources', 'billing');
INSERT INTO `gacl_axo_map` VALUES (32, 'resources', 'claim');
INSERT INTO `gacl_axo_map` VALUES (32, 'resources', 'coding');
INSERT INTO `gacl_axo_map` VALUES (32, 'resources', 'document');
INSERT INTO `gacl_axo_map` VALUES (32, 'resources', 'eob');
INSERT INTO `gacl_axo_map` VALUES (32, 'resources', 'main_billing');
INSERT INTO `gacl_axo_map` VALUES (32, 'resources', 'patient');
INSERT INTO `gacl_axo_map` VALUES (33, 'resources', 'access');
INSERT INTO `gacl_axo_map` VALUES (33, 'resources', 'default');
INSERT INTO `gacl_axo_map` VALUES (33, 'resources', 'docs');
INSERT INTO `gacl_axo_map` VALUES (33, 'resources', 'pdf');
INSERT INTO `gacl_axo_map` VALUES (33, 'resources', 'preferences');
INSERT INTO `gacl_axo_map` VALUES (36, 'resources', 'calendar');
INSERT INTO `gacl_axo_map` VALUES (36, 'resources', 'location');
INSERT INTO `gacl_axo_map` VALUES (36, 'resources', 'patient');
INSERT INTO `gacl_axo_map` VALUES (36, 'resources', 'patientfinder');
INSERT INTO `gacl_axo_map` VALUES (37, 'resources', 'appointment');
INSERT INTO `gacl_axo_map` VALUES (37, 'resources', 'calendar');
INSERT INTO `gacl_axo_map` VALUES (37, 'resources', 'location');
INSERT INTO `gacl_axo_map` VALUES (37, 'resources', 'patient');
INSERT INTO `gacl_axo_map` VALUES (37, 'resources', 'patientfinder');
INSERT INTO `gacl_axo_map` VALUES (38, 'resources', 'appointment');
INSERT INTO `gacl_axo_map` VALUES (38, 'resources', 'calendar');
INSERT INTO `gacl_axo_map` VALUES (38, 'resources', 'location');
INSERT INTO `gacl_axo_map` VALUES (38, 'resources', 'patient');
INSERT INTO `gacl_axo_map` VALUES (38, 'resources', 'patientfinder');
INSERT INTO `gacl_axo_map` VALUES (39, 'resources', 'appointment');
INSERT INTO `gacl_axo_map` VALUES (39, 'resources', 'calendar');
INSERT INTO `gacl_axo_map` VALUES (39, 'resources', 'event');
INSERT INTO `gacl_axo_map` VALUES (39, 'resources', 'location');
INSERT INTO `gacl_axo_map` VALUES (39, 'resources', 'occurence');
INSERT INTO `gacl_axo_map` VALUES (39, 'resources', 'patient');
INSERT INTO `gacl_axo_map` VALUES (39, 'resources', 'patientfinder');
INSERT INTO `gacl_axo_map` VALUES (39, 'resources', 'schedule');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'admin');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'appointment');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'billing');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'calendar');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'claim');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'coding');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'eob');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'event');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'feeschedule');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'insurance');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'location');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'occurence');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'patient');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'patientfinder');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'personschedule');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'practice');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'schedule');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'superbill');

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
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_axo_sections`
-- 

INSERT INTO `gacl_axo_sections` VALUES (0, 'resources', 10, 'Resources', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_sections_seq`
-- 

DROP TABLE IF EXISTS `gacl_axo_sections_seq`;
CREATE TABLE `gacl_axo_sections_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_axo_sections_seq`
-- 

INSERT INTO `gacl_axo_sections_seq` VALUES (24);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_seq`
-- 

DROP TABLE IF EXISTS `gacl_axo_seq`;
CREATE TABLE `gacl_axo_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_axo_seq`
-- 

INSERT INTO `gacl_axo_seq` VALUES (69);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_groups_aro_map`
-- 

DROP TABLE IF EXISTS `gacl_groups_aro_map`;
CREATE TABLE `gacl_groups_aro_map` (
  `group_id` int(11) NOT NULL default '0',
  `aro_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`aro_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_groups_aro_map`
-- 

INSERT INTO `gacl_groups_aro_map` VALUES (12, 15);
INSERT INTO `gacl_groups_aro_map` VALUES (20, 26);
INSERT INTO `gacl_groups_aro_map` VALUES (20, 28);
INSERT INTO `gacl_groups_aro_map` VALUES (20, 29);
INSERT INTO `gacl_groups_aro_map` VALUES (20, 30);
INSERT INTO `gacl_groups_aro_map` VALUES (20, 31);
INSERT INTO `gacl_groups_aro_map` VALUES (20, 32);
INSERT INTO `gacl_groups_aro_map` VALUES (20, 33);
INSERT INTO `gacl_groups_aro_map` VALUES (20, 34);
INSERT INTO `gacl_groups_aro_map` VALUES (20, 35);
INSERT INTO `gacl_groups_aro_map` VALUES (21, 27);
INSERT INTO `gacl_groups_aro_map` VALUES (24, 28);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_groups_axo_map`
-- 

DROP TABLE IF EXISTS `gacl_groups_axo_map`;
CREATE TABLE `gacl_groups_axo_map` (
  `group_id` int(11) NOT NULL default '0',
  `axo_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`axo_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_groups_axo_map`
-- 

INSERT INTO `gacl_groups_axo_map` VALUES (11, 0);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 16);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 17);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 18);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 19);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 36);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 37);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 38);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 39);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 40);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 41);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 42);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 43);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 44);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 45);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 46);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 47);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 48);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 49);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 50);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 51);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 52);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 53);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 54);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 55);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 56);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 57);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 58);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 59);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 60);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 61);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 62);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 63);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 64);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 65);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 66);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 67);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 68);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 69);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_phpgacl`
-- 

DROP TABLE IF EXISTS `gacl_phpgacl`;
CREATE TABLE `gacl_phpgacl` (
  `name` varchar(230) NOT NULL default '',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`name`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_phpgacl`
-- 

INSERT INTO `gacl_phpgacl` VALUES ('version', '3.3.3');
INSERT INTO `gacl_phpgacl` VALUES ('schema_version', '2.1');

SET FOREIGN_KEY_CHECKS=1;

COMMIT;
