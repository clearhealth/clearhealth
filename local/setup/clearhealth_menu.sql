
-- 
-- Table structure for table `menu`
-- 

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
) TYPE=MyISAM;

-- 
-- Dumping data for table `menu`
-- 

INSERT INTO `menu` VALUES (1, '', 1, '', 'children', 0, '', '', 'main');
INSERT INTO `menu` VALUES (2, 'default', 39, '', 'children', 100, 'Logout', 'Access/logout', 'main');
INSERT INTO `menu` VALUES (3, 'default', 39, '', 'children', 10, 'Preferences', 'Preferences/list', 'main');
INSERT INTO `menu` VALUES (4, 'admin', 1, '', 'children', 800, 'Reports', '', 'main/Admin');
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
INSERT INTO `menu` VALUES (29, 'patient', 68, '', 'children', 10, 'Fillout Form', 'Form/fillout', 'main');
INSERT INTO `menu` VALUES (30, 'patient', 1, '', 'children', 100, 'Patients', '', '');
INSERT INTO `menu` VALUES (31, 'patient', 30, '', 'children', 20, 'Add Patient', 'Patient/edit', 'main');
INSERT INTO `menu` VALUES (32, 'admin', 5, '', 'children', 160, 'List Insurance Companies', 'Insurance/list', 'main');
INSERT INTO `menu` VALUES (33, 'admin', 5, '', 'children', 170, 'Add Insurance Company', 'Insurance/edit', 'main');
INSERT INTO `menu` VALUES (36, 'admin', 81, '', 'children', 50, 'Document Categories', 'DocumentCategory/list', 'main');
INSERT INTO `menu` VALUES (37, 'patient', 68, '', 'children', 20, 'Documents', 'Document/list', 'main');
INSERT INTO `menu` VALUES (38, 'admin', 45, '', 'children', 30, 'Edit Superbill', 'Superbill/list', 'main');
INSERT INTO `menu` VALUES (39, 'default', 1, '', 'children', 300, 'My Account', '', 'main');
INSERT INTO `menu` VALUES (81, 'admin', 1, '', 'children', 700, 'System', '', '');
INSERT INTO `menu` VALUES (42, 'billing', 1, '', 'children', 300, 'Reports', '', 'main/Billing');
INSERT INTO `menu` VALUES (43, 'default', 1, '', 'children', 200, 'Reports', '', 'main/Calendar');
INSERT INTO `menu` VALUES (44, 'patient', 1, '', 'children', 300, 'Reports', '', 'main/Patient');
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
INSERT INTO `menu` VALUES (64, 'billing', 62, '', 'children', 20, 'Add Claim', 'Claim/edit', 'freeb2');
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
INSERT INTO `menu` VALUES (76, 'billing', 1, '', 'children', 600, 'Help', '', '');
INSERT INTO `menu` VALUES (77, 'billing', 76, '', 'children', 10, 'API Docs', 'Docs/api', 'main');
INSERT INTO `menu` VALUES (78, 'admin', 1, '', 'children', 1000, 'Help', '', '');
INSERT INTO `menu` VALUES (79, 'admin', 78, '', 'children', 10, 'API Docs', 'Docs/api', 'main');
INSERT INTO `menu` VALUES (83, 'admin', 5, '', 'children', 5, 'List Schedules/Facilities', 'Location/list', 'main');
INSERT INTO `menu` VALUES (84, 'admin', 5, '', 'children', 20, 'Add New Practice', 'Location/edit_practice', 'main');
INSERT INTO `menu` VALUES (85, 'admin', 4, '', 'children', 5, 'List Reports', 'Report/list', 'main');
INSERT INTO `menu` VALUES (86, 'admin', 1, '', 'children', 900, '', 'Admin/default', 'main');
INSERT INTO `menu` VALUES (87, 'admin', 4, '', 'children', 50, 'Connect Report', 'Report/connect', 'main');
INSERT INTO `menu` VALUES (88, 'billing', 1, '', 'children', 0, '', 'Billing/default', 'main');
INSERT INTO `menu` VALUES (89, 'patient', 1, '', 'children', -1, 'Dashboard Reports', '', 'main/Patient');
INSERT INTO `menu` VALUES (90, 'patient', 1, '', 'children', -1, 'Dashboard Forms', '', 'main/Patient');
INSERT INTO `menu` VALUES (91, 'patient', 1, '', 'children', -1, 'Encounter Forms', '', 'main/Encounter');
INSERT INTO `menu` VALUES (92, 'admin', 26, '', 'children', 100, 'Connect', 'Form/connect', 'main');
INSERT INTO `menu` VALUES (93, 'billing', 1, '', 'children', 0, '', 'Eob/Payment', 'main');
INSERT INTO `menu` VALUES (94, 'default', 39, '', 'children', 50, 'Change Password', 'User/password', 'main');
INSERT INTO `menu` VALUES (95, 'patient', 46, '', 'children', 50, 'Change Password', 'User/password', 'main');
INSERT INTO `menu` VALUES (96, 'billing', 49, '', 'children', 50, 'Change Password', 'User/password', 'main');
INSERT INTO `menu` VALUES (97, 'admin', 59, '', 'children', 50, 'Change Password', 'User/password', 'main');
INSERT INTO `menu` VALUES (98, 'admin', 81, '', 'children', 800, 'ACL Editor', 'Admin/acl', 'main');
        
-- MySQL dump 10.9
--
-- Host: localhost    Database: clearhealth
-- ------------------------------------------------------
-- Server version	4.1.12

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `menu`
--


/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
LOCK TABLES `menu` WRITE;
INSERT INTO `menu` VALUES (1,'',1,'','children',0,'','','main'),(2,'default',39,'','children',100,'Logout','Access/logout','main'),(3,'default',39,'','children',10,'Preferences','Preferences/list','main'),(4,'admin',1,'','children',800,'Reports','','main/Admin'),(5,'admin',1,'','children',100,'Entities','',''),(7,'admin',5,'','children',10,'Add New Schedule','Location/edit_schedule','main'),(8,'default',5,'','children',20,'Add New Practice','Location/edit_practive','main'),(9,'admin',5,'','children',30,'Add New Building','Location/edit_building','main'),(10,'admin',5,'','children',40,'Add New Room','Location/edit_room','main'),(82,'admin',26,'','children',10,'List Forms','Form/list','main'),(12,'default',65,'','children',10,'Day','Calendar/day','main'),(13,'default',65,'','children',50,'Week Brief','Calendar/week','main'),(14,'default',65,'','children',20,'Week','Calendar/week_grid','main'),(15,'default',65,'','children',30,'Month','Calendar/month','main'),(16,'default',65,'','children',40,'Day Brief','Calendar/day_brief','main'),(17,'default',65,'','children',60,'Search','Calendar/search','main'),(18,'admin',45,'','children',10,'List Fee Schedules','FeeSchedule/default','main'),(19,'admin',45,'','children',20,'Add Fee Schedule','FeeSchedule/edit','main'),(20,'admin',4,'','children',10,'Add Report','Report/edit','main'),(21,'admin',81,'','children',10,'List Users','User/list','main'),(22,'admin',81,'','children',20,'Add User','User/edit','main'),(80,'admin',1,'','children',200,'Calendar','',''),(24,'admin',81,'','children',30,'List Enumerations','Enumeration/list','main'),(25,'admin',81,'','children',40,'Add Enumeration','Enumeration/edit','main'),(26,'admin',1,'','children',750,'Forms','',''),(27,'admin',26,'','children',20,'Add Form','Form/edit','main'),(28,'admin',26,'','children',30,'View Form Data','Form/view','main'),(29,'patient',68,'','children',10,'Fillout Form','Form/fillout','main'),(30,'patient',1,'','children',100,'Patients','',''),(31,'patient',30,'','children',20,'Add Patient','Patient/edit','main'),(32,'admin',5,'','children',160,'List Insurance Companies','Insurance/list','main'),(33,'admin',5,'','children',170,'Add Insurance Company','Insurance/edit','main'),(36,'admin',81,'','children',50,'Document Categories','DocumentCategory/list','main'),(37,'patient',68,'','children',20,'Documents','Document/list','main'),(38,'admin',45,'','children',30,'Edit Superbill','Superbill/list','main'),(39,'default',1,'','children',300,'My Account','','main'),(81,'admin',1,'','children',700,'System','',''),(42,'billing',1,'','children',300,'Reports','','main/Billing'),(43,'default',1,'','children',200,'Reports','','main/Calendar'),(44,'patient',1,'','children',300,'Reports','','main/Patient'),(45,'admin',1,'','children',300,'Billing','','main'),(46,'patient',1,'','children',400,'My Account','','main'),(47,'patient',46,'','children',100,'Logout','Access/logout','main'),(48,'patient',46,'','children',10,'Preferences','Preferences/list','main'),(49,'billing',1,'','children',500,'My Account','','main'),(57,'billing',49,'','children',100,'Logout','Access/logout','main'),(58,'billing',49,'','children',10,'Preferences','Preferences/list','main'),(59,'admin',1,'','children',900,'My Account','','main'),(60,'admin',59,'','children',100,'Logout','Access/logout','main'),(61,'admin',59,'','children',10,'Preferences','Preferences/list','main'),(62,'billing',1,'','children',100,'Claims','','freeb2'),(63,'billing',62,'','children',10,'List Claims','Claim/list','freeb2'),(64,'billing',62,'','children',20,'Add Claim','Claim/edit','freeb2'),(65,'default',1,'','children',100,'View','',''),(66,'default',1,'','children',400,'Help','',''),(67,'patient',30,'','children',10,'List Patients','Patient/list','main'),(68,'patient',1,'','children',200,'Actions','',''),(69,'patient',30,'','children',30,'Search','PatientFinder/find','main'),(70,'patient',68,'','children',30,'Encounter','Patient/encounter','main'),(71,'default',66,'','children',10,'API Docs','Docs/api','main'),(72,'patient',68,'','children',5,'Dashboard','Patient/dashboard','main'),(74,'patient',1,'','children',500,'Help','',''),(75,'patient',74,'','children',10,'API Docs','Docs/api','main'),(76,'billing',1,'','children',600,'Help','',''),(77,'billing',76,'','children',10,'API Docs','Docs/api','main'),(78,'admin',1,'','children',1000,'Help','',''),(79,'admin',78,'','children',10,'API Docs','Docs/api','main'),(83,'admin',5,'','children',5,'List Facilities','Location/list','main'),(84,'admin',5,'','children',20,'Add New Practice','Location/edit_practice','main'),(85,'admin',4,'','children',5,'List Reports','Report/list','main'),(86,'admin',1,'','children',900,'','Admin/default','main'),(87,'admin',4,'','children',50,'Connect Report','Report/connect','main'),(88,'billing',1,'','children',0,'','Billing/default','main'),(89,'patient',1,'','children',-1,'Dashboard Reports','','main/Patient'),(90,'patient',1,'','children',-1,'Dashboard Forms','','main/Patient'),(91,'patient',1,'','children',-1,'Encounter Forms','','main/Encounter'),(92,'admin',26,'','children',100,'Connect','Form/connect','main'),(93,'billing',1,'','children',0,'','Eob/Payment','main'),(94,'default',39,'','children',50,'Change Password','MyAccount/password','main'),(95,'patient',46,'','children',50,'Change Password','MyAccount/password','main'),(96,'billing',49,'','children',50,'Change Password','MyAccount/password','main'),(97,'admin',59,'','children',50,'Change Password','MyAccount/password','main'),(98,'admin',81,'','children',800,'ACL Editor','Admin/acl','main'),(100,'admin',5,'','children',4,'List Schedules','Location/schedules','main'),(101,'default',1,'','children',700,'Admin','','main'),(102,'default',101,'','children',10,'Add New Schedule','Location/edit_schedule','main'),(103,'default',101,'','children',4,'List Schedules','Location/schedules','main'),(104,'billing',1,'','children',800,'Admin','','main'),(105,'billing',104,'','children',160,'List Insurance Companies','Insurance/list','main'),(106,'billing',104,'','children',170,'Add Insurance Company','Insurance/edit','main'),(107,'patient',44,'','children',5,'Summary Report','Patient/summary_report','main');
UNLOCK TABLES;
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- MySQL dump 10.9
--
-- Host: localhost    Database: clearhealth
-- ------------------------------------------------------
-- Server version	4.1.12

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `menu_form`
--


/*!40000 ALTER TABLE `menu_form` DISABLE KEYS */;
LOCK TABLES `menu_form` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `menu_form` ENABLE KEYS */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- MySQL dump 10.9
--
-- Host: localhost    Database: clearhealth
-- ------------------------------------------------------
-- Server version	4.1.12

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `menu_report`
--


/*!40000 ALTER TABLE `menu_report` DISABLE KEYS */;
LOCK TABLES `menu_report` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `menu_report` ENABLE KEYS */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

