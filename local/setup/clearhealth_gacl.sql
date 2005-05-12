-- phpMyAdmin SQL Dump
-- version 2.6.1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: May 12, 2005 at 07:50 AM
-- Server version: 4.1.10
-- PHP Version: 4.3.10
-- 
-- Database: `clearhealth`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_acl`
-- 

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

INSERT INTO `gacl_acl` VALUES (26, 'user', 1, 1, '', 'Give Superadmn and Supervisors access to everything', 1112056945);
INSERT INTO `gacl_acl` VALUES (24, 'user', 1, 1, '', 'Give Super Admin and Supervisor access to everything even when no resource is selected', 1112056973);
INSERT INTO `gacl_acl` VALUES (30, 'user', 1, 1, '', 'Give Calendar users and Supervisors access to basic calendar functions', 1115842900);
INSERT INTO `gacl_acl` VALUES (29, 'user', 0, 1, '', 'Deny Non-admin users access to some system wide configuration sections', 1115843200);
INSERT INTO `gacl_acl` VALUES (31, 'user', 1, 1, '', 'Give Calendar supervisors the ability to double book', 1112057044);
INSERT INTO `gacl_acl` VALUES (32, 'user', 1, 1, '', 'Give billing users basic access to those sections', 1115843254);
INSERT INTO `gacl_acl` VALUES (33, 'user', 1, 1, '', 'Give all users of the system access to basic app sections', 1112057091);
INSERT INTO `gacl_acl` VALUES (35, 'user', 1, 1, '', '', 1112803381);
INSERT INTO `gacl_acl` VALUES (36, 'user', 1, 1, '', 'Default System users have access to Billing, Calendar and Patient', 1115848370);
INSERT INTO `gacl_acl` VALUES (37, 'user', 0, 1, '', 'Only the Admin user has access to the Admin menu', 1115848687);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_acl_sections`
-- 

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

CREATE TABLE `gacl_acl_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_acl_seq`
-- 

INSERT INTO `gacl_acl_seq` VALUES (37);
INSERT INTO `gacl_acl_seq` VALUES (37);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aco`
-- 

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
INSERT INTO `gacl_aco_map` VALUES (30, 'actions', 'add');
INSERT INTO `gacl_aco_map` VALUES (30, 'actions', 'delete');
INSERT INTO `gacl_aco_map` VALUES (30, 'actions', 'edit');
INSERT INTO `gacl_aco_map` VALUES (30, 'actions', 'usage');
INSERT INTO `gacl_aco_map` VALUES (30, 'actions', 'view');
INSERT INTO `gacl_aco_map` VALUES (31, 'actions', 'double_book');
INSERT INTO `gacl_aco_map` VALUES (32, 'actions', 'add');
INSERT INTO `gacl_aco_map` VALUES (32, 'actions', 'delete');
INSERT INTO `gacl_aco_map` VALUES (32, 'actions', 'edit');
INSERT INTO `gacl_aco_map` VALUES (32, 'actions', 'usage');
INSERT INTO `gacl_aco_map` VALUES (32, 'actions', 'view');
INSERT INTO `gacl_aco_map` VALUES (33, 'actions', 'usage');
INSERT INTO `gacl_aco_map` VALUES (33, 'actions', 'view');
INSERT INTO `gacl_aco_map` VALUES (35, 'actions', 'double_book');
INSERT INTO `gacl_aco_map` VALUES (36, 'actions', 'add');
INSERT INTO `gacl_aco_map` VALUES (36, 'actions', 'delete');
INSERT INTO `gacl_aco_map` VALUES (36, 'actions', 'delete_owner');
INSERT INTO `gacl_aco_map` VALUES (36, 'actions', 'edit');
INSERT INTO `gacl_aco_map` VALUES (36, 'actions', 'edit_owner');
INSERT INTO `gacl_aco_map` VALUES (36, 'actions', 'uploadFile');
INSERT INTO `gacl_aco_map` VALUES (36, 'actions', 'usage');
INSERT INTO `gacl_aco_map` VALUES (36, 'actions', 'view');
INSERT INTO `gacl_aco_map` VALUES (37, 'actions', 'add');
INSERT INTO `gacl_aco_map` VALUES (37, 'actions', 'delete');
INSERT INTO `gacl_aco_map` VALUES (37, 'actions', 'delete_owner');
INSERT INTO `gacl_aco_map` VALUES (37, 'actions', 'edit');
INSERT INTO `gacl_aco_map` VALUES (37, 'actions', 'edit_owner');
INSERT INTO `gacl_aco_map` VALUES (37, 'actions', 'uploadFile');
INSERT INTO `gacl_aco_map` VALUES (37, 'actions', 'usage');
INSERT INTO `gacl_aco_map` VALUES (37, 'actions', 'view');

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aco_sections`
-- 

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
INSERT INTO `gacl_aro` VALUES (38, 'users', 'mtrotter', 500023, 'mtrotter', 1);
INSERT INTO `gacl_aro` VALUES (39, 'users', 'test1', 500195, 'test1', 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_groups`
-- 

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

INSERT INTO `gacl_aro_groups` VALUES (10, 0, 1, 22, 'Root', 'root');
INSERT INTO `gacl_aro_groups` VALUES (12, 23, 11, 12, 'System Admin', 'admin');
INSERT INTO `gacl_aro_groups` VALUES (19, 10, 2, 9, 'User Types', 'users');
INSERT INTO `gacl_aro_groups` VALUES (20, 19, 3, 4, 'Provider', 'provider');
INSERT INTO `gacl_aro_groups` VALUES (21, 19, 5, 6, 'Mid-level', 'mid-level');
INSERT INTO `gacl_aro_groups` VALUES (22, 19, 7, 8, 'Staff', 'staff');
INSERT INTO `gacl_aro_groups` VALUES (23, 10, 10, 21, 'Roles', 'roles');
INSERT INTO `gacl_aro_groups` VALUES (24, 23, 13, 14, 'Supervisor', 'supervisor');
INSERT INTO `gacl_aro_groups` VALUES (26, 23, 15, 16, 'Calendar Supervisor', 'calendar_supervisor');
INSERT INTO `gacl_aro_groups` VALUES (27, 23, 17, 18, 'Calendar User', 'calendar_user');
INSERT INTO `gacl_aro_groups` VALUES (28, 23, 19, 20, 'Billing User', 'billing_user');

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_groups_id_seq`
-- 

CREATE TABLE `gacl_aro_groups_id_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_aro_groups_id_seq`
-- 

INSERT INTO `gacl_aro_groups_id_seq` VALUES (28);
INSERT INTO `gacl_aro_groups_id_seq` VALUES (28);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_groups_map`
-- 

CREATE TABLE `gacl_aro_groups_map` (
  `acl_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acl_id`,`group_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_aro_groups_map`
-- 

INSERT INTO `gacl_aro_groups_map` VALUES (24, 12);
INSERT INTO `gacl_aro_groups_map` VALUES (24, 24);
INSERT INTO `gacl_aro_groups_map` VALUES (26, 12);
INSERT INTO `gacl_aro_groups_map` VALUES (26, 24);
INSERT INTO `gacl_aro_groups_map` VALUES (29, 20);
INSERT INTO `gacl_aro_groups_map` VALUES (29, 21);
INSERT INTO `gacl_aro_groups_map` VALUES (29, 22);
INSERT INTO `gacl_aro_groups_map` VALUES (29, 24);
INSERT INTO `gacl_aro_groups_map` VALUES (29, 26);
INSERT INTO `gacl_aro_groups_map` VALUES (29, 27);
INSERT INTO `gacl_aro_groups_map` VALUES (29, 28);
INSERT INTO `gacl_aro_groups_map` VALUES (30, 20);
INSERT INTO `gacl_aro_groups_map` VALUES (30, 21);
INSERT INTO `gacl_aro_groups_map` VALUES (30, 22);
INSERT INTO `gacl_aro_groups_map` VALUES (30, 26);
INSERT INTO `gacl_aro_groups_map` VALUES (30, 27);
INSERT INTO `gacl_aro_groups_map` VALUES (31, 26);
INSERT INTO `gacl_aro_groups_map` VALUES (32, 28);
INSERT INTO `gacl_aro_groups_map` VALUES (33, 20);
INSERT INTO `gacl_aro_groups_map` VALUES (33, 21);
INSERT INTO `gacl_aro_groups_map` VALUES (33, 22);
INSERT INTO `gacl_aro_groups_map` VALUES (36, 20);
INSERT INTO `gacl_aro_groups_map` VALUES (36, 21);
INSERT INTO `gacl_aro_groups_map` VALUES (36, 22);
INSERT INTO `gacl_aro_groups_map` VALUES (37, 20);
INSERT INTO `gacl_aro_groups_map` VALUES (37, 21);
INSERT INTO `gacl_aro_groups_map` VALUES (37, 22);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_map`
-- 

CREATE TABLE `gacl_aro_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_aro_map`
-- 

INSERT INTO `gacl_aro_map` VALUES (35, 'users', 'admin');

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_sections`
-- 

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

CREATE TABLE `gacl_aro_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_aro_seq`
-- 

INSERT INTO `gacl_aro_seq` VALUES (39);
INSERT INTO `gacl_aro_seq` VALUES (39);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo`
-- 

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

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_groups`
-- 

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

INSERT INTO `gacl_axo_groups` VALUES (10, 0, 1, 20, 'Root', 'root');
INSERT INTO `gacl_axo_groups` VALUES (11, 10, 2, 3, 'All Site Sections', 'sections');
INSERT INTO `gacl_axo_groups` VALUES (12, 10, 4, 5, 'Patient', 'patient');
INSERT INTO `gacl_axo_groups` VALUES (18, 10, 14, 15, 'Billing', 'billing');
INSERT INTO `gacl_axo_groups` VALUES (19, 10, 16, 17, 'Calendar', 'calendar');
INSERT INTO `gacl_axo_groups` VALUES (20, 10, 18, 19, 'Admin', 'admin');

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_groups_id_seq`
-- 

CREATE TABLE `gacl_axo_groups_id_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_axo_groups_id_seq`
-- 

INSERT INTO `gacl_axo_groups_id_seq` VALUES (20);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_groups_map`
-- 

CREATE TABLE `gacl_axo_groups_map` (
  `acl_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acl_id`,`group_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_axo_groups_map`
-- 

INSERT INTO `gacl_axo_groups_map` VALUES (24, 11);
INSERT INTO `gacl_axo_groups_map` VALUES (35, 11);
INSERT INTO `gacl_axo_groups_map` VALUES (36, 12);
INSERT INTO `gacl_axo_groups_map` VALUES (36, 18);
INSERT INTO `gacl_axo_groups_map` VALUES (36, 19);
INSERT INTO `gacl_axo_groups_map` VALUES (37, 20);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_map`
-- 

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
INSERT INTO `gacl_axo_map` VALUES (30, 'resources', 'calendar');
INSERT INTO `gacl_axo_map` VALUES (30, 'resources', 'main_calendar');
INSERT INTO `gacl_axo_map` VALUES (31, 'resources', 'calendar');
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

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_sections`
-- 

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

CREATE TABLE `gacl_axo_sections_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_axo_sections_seq`
-- 

INSERT INTO `gacl_axo_sections_seq` VALUES (23);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_seq`
-- 

CREATE TABLE `gacl_axo_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_axo_seq`
-- 

INSERT INTO `gacl_axo_seq` VALUES (68);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_groups_aro_map`
-- 

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
INSERT INTO `gacl_groups_aro_map` VALUES (20, 36);
INSERT INTO `gacl_groups_aro_map` VALUES (20, 37);
INSERT INTO `gacl_groups_aro_map` VALUES (20, 38);
INSERT INTO `gacl_groups_aro_map` VALUES (21, 27);
INSERT INTO `gacl_groups_aro_map` VALUES (22, 39);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_groups_axo_map`
-- 

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
INSERT INTO `gacl_groups_axo_map` VALUES (12, 41);
INSERT INTO `gacl_groups_axo_map` VALUES (12, 50);
INSERT INTO `gacl_groups_axo_map` VALUES (12, 51);
INSERT INTO `gacl_groups_axo_map` VALUES (12, 58);
INSERT INTO `gacl_groups_axo_map` VALUES (12, 60);
INSERT INTO `gacl_groups_axo_map` VALUES (12, 66);
INSERT INTO `gacl_groups_axo_map` VALUES (18, 48);
INSERT INTO `gacl_groups_axo_map` VALUES (18, 59);
INSERT INTO `gacl_groups_axo_map` VALUES (18, 62);
INSERT INTO `gacl_groups_axo_map` VALUES (18, 63);
INSERT INTO `gacl_groups_axo_map` VALUES (18, 65);
INSERT INTO `gacl_groups_axo_map` VALUES (19, 38);
INSERT INTO `gacl_groups_axo_map` VALUES (19, 43);
INSERT INTO `gacl_groups_axo_map` VALUES (19, 46);
INSERT INTO `gacl_groups_axo_map` VALUES (19, 55);
INSERT INTO `gacl_groups_axo_map` VALUES (19, 64);
INSERT INTO `gacl_groups_axo_map` VALUES (20, 36);
INSERT INTO `gacl_groups_axo_map` VALUES (20, 37);
INSERT INTO `gacl_groups_axo_map` VALUES (20, 44);
INSERT INTO `gacl_groups_axo_map` VALUES (20, 46);
INSERT INTO `gacl_groups_axo_map` VALUES (20, 49);
INSERT INTO `gacl_groups_axo_map` VALUES (20, 52);
INSERT INTO `gacl_groups_axo_map` VALUES (20, 56);
INSERT INTO `gacl_groups_axo_map` VALUES (20, 57);
INSERT INTO `gacl_groups_axo_map` VALUES (20, 67);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_phpgacl`
-- 

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
        
