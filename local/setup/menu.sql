-- phpMyAdmin SQL Dump
-- version 2.6.1-rc2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Mar 10, 2005 at 10:06 AM
-- Server version: 4.0.23
-- PHP Version: 4.3.10
-- 
-- Database: `clearhealth`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `menu`
-- 

DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
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
) TYPE=MyISAM AUTO_INCREMENT=87 ;

-- 
-- Dumping data for table `menu`
-- 

INSERT INTO `menu` VALUES (1, '', 1, '', 'children', 0, '', '', 'main');
INSERT INTO `menu` VALUES (2, 'default', 39, '', 'children', 100, 'Logout', 'Access/logout', 'main');
INSERT INTO `menu` VALUES (3, 'default', 39, '', 'children', 10, 'Preferences', 'Preferences/list', 'main');
INSERT INTO `menu` VALUES (4, 'admin', 1, '', 'children', 800, 'Reports', '', '');
INSERT INTO `menu` VALUES (5, 'admin', 1, '', 'children', 100, 'Entities', '', '');
INSERT INTO `menu` VALUES (7, 'admin', 5, '', 'children', 10, 'Add New Schedule', 'Location/edit_schedule', 'main');
INSERT INTO `menu` VALUES (8, 'default', 5, '', 'children', 20, 'Add New Practice', 'Location/edit_practive', 'main');
INSERT INTO `menu` VALUES (9, 'admin', 5, '', 'children', 30, 'Add New Building', 'Location/edit_building', 'main');
INSERT INTO `menu` VALUES (10, 'admin', 5, '', 'children', 40, 'Add New Room', 'Location/edit_room', 'main');
INSERT INTO `menu` VALUES (82, 'admin', 26, '', 'children', 10, 'List Forms', 'Form/list', 'main');
INSERT INTO `menu` VALUES (12, 'default', 65, '', 'children', 10, 'Day', 'Calendar/day', 'main');
INSERT INTO `menu` VALUES (13, 'default', 65, '', 'children', 50, 'Week Brief', 'Calendar/week', 'main');
INSERT INTO `menu` VALUES (14, 'default', 65, '', 'children', 20, 'Week', 'Calendar/week_grid', 'main');
INSERT INTO `menu` VALUES (15, 'default', 65, '', 'children', 30, 'Month', 'Calendar/month', 'main');
INSERT INTO `menu` VALUES (16, 'default', 65, '', 'children', 40, 'Day Brief', 'Calendar/day_brief', 'main');
INSERT INTO `menu` VALUES (17, 'default', 65, '', 'children', 60, 'Search', 'Calendar/search', 'main');
INSERT INTO `menu` VALUES (18, 'admin', 45, '', 'children', 10, 'List Fee Schedules', 'FeeSchedule/default', 'main');
INSERT INTO `menu` VALUES (19, 'admin', 45, '', 'children', 20, 'Add Fee Schedule', 'FeeSchedule/edit', 'main');
INSERT INTO `menu` VALUES (20, 'admin', 4, '', 'children', 10, 'Add Report', 'Report/edit', 'main');
INSERT INTO `menu` VALUES (21, 'admin', 81, '', 'children', 10, 'List Users', 'User/list', 'main');
INSERT INTO `menu` VALUES (22, 'admin', 81, '', 'children', 20, 'Add User', 'User/edit', 'main');
INSERT INTO `menu` VALUES (80, 'admin', 1, '', 'children', 200, 'Calendar', '', '');
INSERT INTO `menu` VALUES (24, 'admin', 81, '', 'children', 30, 'List Enumerations', 'Enumeration/list', 'main');
INSERT INTO `menu` VALUES (25, 'admin', 81, '', 'children', 40, 'Add Enumeration', 'Enumeration/edit', 'main');
INSERT INTO `menu` VALUES (26, 'admin', 1, '', 'children', 750, 'Forms', '', '');
INSERT INTO `menu` VALUES (27, 'admin', 26, '', 'children', 20, 'Add Form', 'Form/edit', 'main');
INSERT INTO `menu` VALUES (28, 'admin', 26, '', 'children', 30, 'View Form Data', 'Form/view', 'main');
INSERT INTO `menu` VALUES (29, 'patient', 68, '', 'children', 10, 'Forms', 'Form/fillout', 'main');
INSERT INTO `menu` VALUES (30, 'patient', 1, '', 'children', 100, 'Patients', '', '');
INSERT INTO `menu` VALUES (31, 'patient', 30, '', 'children', 20, 'Add Patient', 'Patient/edit', 'main');
INSERT INTO `menu` VALUES (32, 'admin', 5, '', 'children', 160, 'List Insurance Companies', 'Insurance/list', 'main');
INSERT INTO `menu` VALUES (33, 'admin', 5, '', 'children', 170, 'Add Insurance Company', 'Insurance/edit', 'main');
INSERT INTO `menu` VALUES (36, 'admin', 81, '', 'children', 50, 'Document Categories', 'DocumentCategory/list', 'main');
INSERT INTO `menu` VALUES (37, 'patient', 68, '', 'children', 20, 'Documents', 'Document/list', 'main');
INSERT INTO `menu` VALUES (38, 'admin', 45, '', 'children', 30, 'Edit Superbill', 'Superbill/list', 'main');
INSERT INTO `menu` VALUES (39, 'default', 1, '', 'children', 300, 'My Account', '', 'main');
INSERT INTO `menu` VALUES (81, 'admin', 1, '', 'children', 700, 'System', '', '');
INSERT INTO `menu` VALUES (73, 'billing', 62, '', 'children', 30, 'Search', 'Claim/search', 'freeb2');
INSERT INTO `menu` VALUES (42, 'billing', 1, '', 'children', 300, 'Reports', 'Billing/reports', 'main');
INSERT INTO `menu` VALUES (43, 'default', 1, '', 'children', 200, 'Reports', '', '');
INSERT INTO `menu` VALUES (44, 'patient', 1, '', 'children', 300, 'Reports', 'Patient/reports', 'main');
INSERT INTO `menu` VALUES (45, 'admin', 1, '', 'children', 300, 'Billing', '', 'main');
INSERT INTO `menu` VALUES (46, 'patient', 1, '', 'children', 400, 'My Account', '', 'main');
INSERT INTO `menu` VALUES (47, 'patient', 46, '', 'children', 100, 'Logout', 'Access/logout', 'main');
INSERT INTO `menu` VALUES (48, 'patient', 46, '', 'children', 10, 'Preferences', 'Preferences/list', 'main');
INSERT INTO `menu` VALUES (49, 'billing', 1, '', 'children', 500, 'My Account', '', 'main');
INSERT INTO `menu` VALUES (57, 'billing', 49, '', 'children', 100, 'Logout', 'Access/logout', 'main');
INSERT INTO `menu` VALUES (58, 'billing', 49, '', 'children', 10, 'Preferences', 'Preferences/list', 'main');
INSERT INTO `menu` VALUES (59, 'admin', 1, '', 'children', 900, 'My Account', '', 'main');
INSERT INTO `menu` VALUES (60, 'admin', 59, '', 'children', 100, 'Logout', 'Access/logout', 'main');
INSERT INTO `menu` VALUES (61, 'admin', 59, '', 'children', 10, 'Preferences', 'Preferences/list', 'main');
INSERT INTO `menu` VALUES (62, 'billing', 1, '', 'children', 100, 'Claims', '', 'freeb2');
INSERT INTO `menu` VALUES (63, 'billing', 62, '', 'children', 10, 'List Claims', 'Claim/list', 'freeb2');
INSERT INTO `menu` VALUES (64, 'billing', 62, '', 'children', 20, 'Add Claim', 'Claim/list', 'freeb2');
INSERT INTO `menu` VALUES (65, 'default', 1, '', 'children', 100, 'View', '', '');
INSERT INTO `menu` VALUES (66, 'default', 1, '', 'children', 400, 'Help', '', '');
INSERT INTO `menu` VALUES (67, 'patient', 30, '', 'children', 10, 'List Patients', 'Patient/list', 'main');
INSERT INTO `menu` VALUES (68, 'patient', 1, '', 'children', 200, 'Actions', '', '');
INSERT INTO `menu` VALUES (69, 'patient', 30, '', 'children', 30, 'Search', 'PatientFinder/find', 'main');
INSERT INTO `menu` VALUES (70, 'patient', 68, '', 'children', 30, 'Encounter', 'Patient/encounter', 'main');
INSERT INTO `menu` VALUES (71, 'default', 66, '', 'children', 10, 'API Docs', 'Docs/api', 'main');
INSERT INTO `menu` VALUES (72, 'patient', 68, '', 'children', 5, 'Dashboard', 'Patient/dashboard', 'main');
INSERT INTO `menu` VALUES (74, 'patient', 1, '', 'children', 500, 'Help', '', '');
INSERT INTO `menu` VALUES (75, 'patient', 74, '', 'children', 10, 'API Docs', 'Docs/api', 'main');
INSERT INTO `menu` VALUES (76, 'billing', 1, '', 'children', 500, 'Help', '', '');
INSERT INTO `menu` VALUES (77, 'billing', 76, '', 'children', 10, 'API Docs', 'Docs/api', 'main');
INSERT INTO `menu` VALUES (78, 'admin', 1, '', 'children', 1000, 'Help', '', '');
INSERT INTO `menu` VALUES (79, 'admin', 78, '', 'children', 10, 'API Docs', 'Docs/api', 'main');
INSERT INTO `menu` VALUES (83, 'admin', 5, '', 'children', 5, 'List Schedules/Facilities', 'Location/list', 'main');
INSERT INTO `menu` VALUES (84, 'admin', 5, '', 'children', 20, 'Add New Practice', 'Location/edit_practice', 'main');
INSERT INTO `menu` VALUES (85, 'admin', 4, '', 'children', 5, 'List Reports', 'Report/list', 'main');
INSERT INTO `menu` VALUES (86, 'admin', 1, '', 'children', 900, '', 'Admin/default', 'main');
