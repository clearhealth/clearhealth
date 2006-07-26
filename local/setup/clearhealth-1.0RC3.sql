-- MySQL dump 10.9
--
-- Host: localhost    Database: ch-install
-- ------------------------------------------------------
-- Server version	4.1.15-Debian_1ubuntu5-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `account_note`
--

DROP TABLE IF EXISTS `account_note`;
CREATE TABLE `account_note` (
  `account_note_id` int(11) NOT NULL default '0',
  `patient_id` int(11) NOT NULL default '0',
  `claim_id` varchar(100) NOT NULL default '',
  `user_id` int(11) NOT NULL default '0',
  `date_posted` datetime NOT NULL default '0000-00-00 00:00:00',
  `note` text NOT NULL,
  `note_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`account_note_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='An address that can be for a company or a person. STARTEMPTY';

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='STARTWITHDATA';

--
-- Dumping data for table `adodbseq`
--


/*!40000 ALTER TABLE `adodbseq` DISABLE KEYS */;
LOCK TABLES `adodbseq` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `adodbseq` ENABLE KEYS */;

--
-- Table structure for table `appointment`
--

DROP TABLE IF EXISTS `appointment`;
CREATE TABLE `appointment` (
  `appointment_id` int(11) NOT NULL default '0',
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
  KEY `event_group_id` (`event_group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `enabled` tinyint(4) NOT NULL default '1',
  `provider_id` int(11) NOT NULL default '0',
  `procedure_id` int(11) NOT NULL default '0',
  `room_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`appointment_ruleset_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `audit_log`
--


/*!40000 ALTER TABLE `audit_log` DISABLE KEYS */;
LOCK TABLES `audit_log` WRITE;
INSERT INTO `audit_log` VALUES (800001,'enumerationvalue',811943,1,2,'','2006-07-25 11:39:17'),(800004,'enumerationvalue',811970,1,2,'','2006-07-25 11:39:17'),(800007,'enumerationvalue',820277,1,2,'','2006-07-25 11:39:17'),(800010,'enumerationvalue',300476,1,2,'','2006-07-25 11:39:17'),(800014,'enumerationvalue',301031,1,2,'','2006-07-25 11:39:17'),(800016,'enumerationvalue',811931,1,2,'','2006-07-25 11:39:17'),(800018,'enumerationvalue',811958,1,2,'','2006-07-25 11:39:17'),(800020,'enumerationvalue',608447,1,2,'','2006-07-25 11:39:17'),(800023,'enumerationvalue',811949,1,2,'','2006-07-25 11:39:17'),(800026,'enumerationvalue',811976,1,2,'','2006-07-25 11:39:17'),(800028,'enumerationvalue',601245,1,2,'','2006-07-25 11:39:17'),(800030,'enumerationvalue',811946,1,2,'','2006-07-25 11:39:17'),(800032,'enumerationvalue',811973,1,2,'','2006-07-25 11:39:17'),(800034,'enumerationvalue',300474,1,2,'','2006-07-25 11:39:17'),(800036,'enumerationvalue',811925,1,2,'','2006-07-25 11:39:17'),(800039,'enumerationvalue',811952,1,2,'','2006-07-25 11:39:17'),(800042,'enumerationvalue',300475,1,2,'','2006-07-25 11:39:17'),(800044,'enumerationvalue',811928,1,2,'','2006-07-25 11:39:17'),(800047,'enumerationvalue',811955,1,2,'','2006-07-25 11:39:17'),(800050,'enumerationvalue',300477,1,2,'','2006-07-25 11:39:17'),(800052,'enumerationvalue',811934,1,2,'','2006-07-25 11:39:17'),(800055,'enumerationvalue',811961,1,2,'','2006-07-25 11:39:17'),(800058,'enumerationvalue',300478,1,2,'','2006-07-25 11:39:17'),(800060,'enumerationvalue',811937,1,2,'','2006-07-25 11:39:17'),(800063,'enumerationvalue',811964,1,2,'','2006-07-25 11:39:17'),(800065,'enumerationvalue',300479,1,2,'','2006-07-25 11:39:17'),(800067,'enumerationvalue',811940,1,2,'','2006-07-25 11:39:17'),(800070,'enumerationvalue',811967,1,2,'','2006-07-25 11:39:17'),(800073,'enumerationvalue',820250,1,2,'','2006-07-25 11:39:17'),(800076,'enumerationvalue',820263,1,2,'','2006-07-25 11:39:17'),(800079,'enumerationvalue',823807,1,2,'','2006-07-25 11:39:17'),(800082,'enumerationvalue',834773,1,2,'','2006-07-25 11:39:17'),(800084,'enumerationvalue',834781,1,2,'','2006-07-25 11:39:17'),(800086,'enumerationvalue',834789,1,2,'','2006-07-25 11:39:17'),(800089,'enumerationvalue',834797,1,2,'','2006-07-25 11:39:17'),(800092,'enumerationvalue',834805,1,2,'','2006-07-25 11:39:18'),(800095,'enumerationvalue',834813,1,2,'','2006-07-25 11:39:18'),(800098,'enumerationvalue',834821,1,2,'','2006-07-25 11:39:18'),(800101,'enumerationvalue',301031,1,2,'','2006-07-25 11:40:15'),(800103,'enumerationvalue',811958,1,2,'','2006-07-25 11:40:15'),(800105,'enumerationvalue',811949,1,2,'','2006-07-25 11:40:15'),(800107,'enumerationvalue',811976,1,2,'','2006-07-25 11:40:15'),(800109,'enumerationvalue',811946,1,2,'','2006-07-25 11:40:15'),(800111,'enumerationvalue',811973,1,2,'','2006-07-25 11:40:16'),(800113,'enumerationvalue',811937,1,2,'','2006-07-25 11:40:16'),(800115,'enumerationvalue',811964,1,2,'','2006-07-25 11:40:16'),(800117,'enumerationvalue',300479,1,2,'','2006-07-25 11:40:48'),(800119,'enumerationvalue',601245,1,2,'','2006-07-25 11:40:48'),(800121,'enumerationvalue',608447,1,2,'','2006-07-25 11:40:48'),(800123,'enumerationvalue',820250,1,2,'','2006-07-25 11:40:48'),(800125,'enumerationvalue',820263,1,2,'','2006-07-25 11:40:49'),(800127,'enumerationvalue',834773,1,2,'','2006-07-25 11:40:49'),(800129,'enumerationvalue',300513,1,2,'','2006-07-25 11:43:29'),(800131,'enumerationvalue',301506,1,2,'','2006-07-25 11:43:29'),(800135,'enumerationvalue',300514,1,2,'','2006-07-25 11:43:29'),(800138,'enumerationvalue',608458,1,2,'','2006-07-25 11:43:29'),(800140,'enumerationvalue',608459,1,2,'','2006-07-25 11:43:29'),(800142,'enumerationvalue',834781,1,2,'','2006-07-25 11:44:21'),(800145,'enumerationvalue',800144,1,1,'','2006-07-25 11:45:09'),(800153,'enumerationvalue',800152,1,1,'','2006-07-25 11:45:09'),(800161,'enumerationvalue',800160,1,1,'','2006-07-25 11:45:09'),(800169,'enumerationvalue',800168,1,1,'','2006-07-25 11:45:09'),(800177,'enumerationvalue',800176,1,1,'','2006-07-25 11:45:09'),(800185,'enumerationvalue',800184,1,1,'','2006-07-25 11:45:09'),(800193,'enumerationvalue',800192,1,1,'','2006-07-25 11:45:09'),(800201,'enumerationvalue',800200,1,1,'','2006-07-25 11:45:10'),(800209,'enumerationvalue',800208,1,1,'','2006-07-25 11:45:10'),(800217,'enumerationvalue',800216,1,1,'','2006-07-25 11:45:10'),(800225,'enumerationvalue',800224,1,1,'','2006-07-25 11:45:10'),(800233,'enumerationvalue',800232,1,1,'','2006-07-25 11:45:10'),(800241,'enumerationvalue',800240,1,1,'','2006-07-25 11:45:10'),(800248,'enumerationvalue',834773,1,2,'','2006-07-25 11:45:27'),(800250,'enumerationvalue',834781,1,2,'','2006-07-25 11:45:27'),(800252,'enumerationvalue',834789,1,2,'','2006-07-25 11:45:27'),(800254,'enumerationvalue',834797,1,2,'','2006-07-25 11:45:27'),(800256,'enumerationvalue',834805,1,2,'','2006-07-25 11:45:27'),(800258,'enumerationvalue',834813,1,2,'','2006-07-25 11:45:27');
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `audit_log_field`
--


/*!40000 ALTER TABLE `audit_log_field` DISABLE KEYS */;
LOCK TABLES `audit_log_field` WRITE;
INSERT INTO `audit_log_field` VALUES (800002,800001,'extra1','811910',''),(800003,800001,'sort','0','1'),(800005,800004,'sort','0','1'),(800006,800004,'status','1','0'),(800008,800007,'sort','0','2'),(800009,800007,'status','1','0'),(800011,800010,'extra1','834335',''),(800012,800010,'sort','1','3'),(800013,800010,'status','1','0'),(800015,800014,'sort','1','4'),(800017,800016,'sort','1','5'),(800019,800018,'sort','1','6'),(800021,800020,'extra1','820241',''),(800022,800020,'sort','2','7'),(800024,800023,'extra1','820241',''),(800025,800023,'sort','2','8'),(800027,800026,'sort','2','9'),(800029,800028,'sort','3','10'),(800031,800030,'sort','3','11'),(800033,800032,'sort','3','12'),(800035,800034,'sort','4','13'),(800037,800036,'sort','4','14'),(800038,800036,'status','1','0'),(800040,800039,'sort','4','15'),(800041,800039,'status','1','0'),(800043,800042,'sort','5','16'),(800045,800044,'sort','5','17'),(800046,800044,'status','1','0'),(800048,800047,'sort','5','18'),(800049,800047,'status','1','0'),(800051,800050,'sort','6','19'),(800053,800052,'sort','6','20'),(800054,800052,'status','1','0'),(800056,800055,'sort','6','21'),(800057,800055,'status','1','0'),(800059,800058,'sort','7','22'),(800061,800060,'extra1','815720',''),(800062,800060,'sort','7','23'),(800064,800063,'sort','7','24'),(800066,800065,'sort','8','25'),(800068,800067,'sort','8','26'),(800069,800067,'status','1','0'),(800071,800070,'sort','8','27'),(800072,800070,'status','1','0'),(800074,800073,'extra1','820241',''),(800075,800073,'sort','9','28'),(800077,800076,'extra1','815720',''),(800078,800076,'sort','10','29'),(800080,800079,'sort','11','30'),(800081,800079,'status','1','0'),(800083,800082,'sort','12','31'),(800085,800084,'sort','13','32'),(800087,800086,'extra1','834666',''),(800088,800086,'sort','14','33'),(800090,800089,'extra1','834353',''),(800091,800089,'sort','15','34'),(800093,800092,'extra1','815720',''),(800094,800092,'sort','16','35'),(800096,800095,'extra1','815720',''),(800097,800095,'sort','17','36'),(800099,800098,'sort','18','37'),(800100,800098,'status','1','0'),(800102,800101,'status','1','0'),(800104,800103,'status','1','0'),(800106,800105,'status','1','0'),(800108,800107,'status','1','0'),(800110,800109,'status','1','0'),(800112,800111,'status','1','0'),(800114,800113,'status','1','0'),(800116,800115,'status','1','0'),(800118,800117,'sort','25','7'),(800120,800119,'sort','10','7'),(800122,800121,'sort','7','8'),(800124,800123,'sort','28','9'),(800126,800125,'sort','29','10'),(800128,800127,'sort','31','11'),(800130,800129,'extra1','815723',''),(800132,800131,'key','3','2'),(800133,800131,'value','medical appt','FP'),(800134,800131,'extra1','818861',''),(800136,800135,'key','2','3'),(800137,800135,'value','Other','CDP'),(800139,800138,'value','Yearly','CHDP'),(800141,800140,'value','Clearning','F/U'),(800143,800142,'value','New Patient  Office Visit','New Patient Office Visit'),(800146,800145,'key','1','6'),(800147,800145,'value','Hello','Sick'),(800148,800145,'extra1','',''),(800149,800145,'sort','0','6'),(800150,800145,'status','1','1'),(800151,800145,'enumeration_id','300012','300512'),(800154,800153,'key','1','7'),(800155,800153,'value','Hello','Lab Only'),(800156,800153,'extra1','',''),(800157,800153,'sort','0','7'),(800158,800153,'status','1','1'),(800159,800153,'enumeration_id','300012','300512'),(800162,800161,'key','1','8'),(800163,800161,'value','Hello','General Visit'),(800164,800161,'extra1','',''),(800165,800161,'sort','0','8'),(800166,800161,'status','1','1'),(800167,800161,'enumeration_id','300012','300512'),(800170,800169,'key','1','9'),(800171,800169,'value','Hello','Yearly'),(800172,800169,'extra1','',''),(800173,800169,'sort','0','9'),(800174,800169,'status','1','1'),(800175,800169,'enumeration_id','300012','300512'),(800178,800177,'key','1','10'),(800179,800177,'value','Hello','PAP'),(800180,800177,'extra1','',''),(800181,800177,'sort','0','10'),(800182,800177,'status','1','1'),(800183,800177,'enumeration_id','300012','300512'),(800186,800185,'key','1','11'),(800187,800185,'value','Hello','Treatment'),(800188,800185,'extra1','',''),(800189,800185,'sort','0','11'),(800190,800185,'status','1','1'),(800191,800185,'enumeration_id','300012','300512'),(800194,800193,'key','1','12'),(800195,800193,'value','Hello','Hospital F/U'),(800196,800193,'extra1','',''),(800197,800193,'sort','0','12'),(800198,800193,'status','1','1'),(800199,800193,'enumeration_id','300012','300512'),(800202,800201,'key','1','13'),(800203,800201,'value','Hello','New Patient Office Visit'),(800204,800201,'extra1','',''),(800205,800201,'sort','0','13'),(800206,800201,'status','1','1'),(800207,800201,'enumeration_id','300012','300512'),(800210,800209,'key','1','14'),(800211,800209,'value','Hello','Office Visit'),(800212,800209,'extra1','',''),(800213,800209,'sort','0','14'),(800214,800209,'status','1','1'),(800215,800209,'enumeration_id','300012','300512'),(800218,800217,'key','1','15'),(800219,800217,'value','Hello','Short Treatment'),(800220,800217,'extra1','',''),(800221,800217,'sort','0','15'),(800222,800217,'status','1','1'),(800223,800217,'enumeration_id','300012','300512'),(800226,800225,'key','1','16'),(800227,800225,'value','Hello','Medium Treatment'),(800228,800225,'extra1','',''),(800229,800225,'sort','0','16'),(800230,800225,'status','1','1'),(800231,800225,'enumeration_id','300012','300512'),(800234,800233,'key','1','17'),(800235,800233,'value','Hello','Long Treatment'),(800236,800233,'extra1','',''),(800237,800233,'sort','0','17'),(800238,800233,'status','1','1'),(800239,800233,'enumeration_id','300012','300512'),(800242,800241,'key','1','18'),(800243,800241,'value','Hello',''),(800244,800241,'extra1','',''),(800245,800241,'sort','0','18'),(800246,800241,'status','1','0'),(800247,800241,'enumeration_id','300012','300512'),(800249,800248,'key','13','12'),(800251,800250,'key','14','13'),(800253,800252,'key','15','14'),(800255,800254,'key','16','15'),(800257,800256,'key','17','16'),(800259,800258,'key','18','17');
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
  KEY `address_id` (`address_id`),
  KEY `building_id` (`building_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Links a building to a address specifying type. STARTEMPTY';

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `id` int(11) NOT NULL default '0',
  `description` text NOT NULL,
  `name` varchar(255) NOT NULL default '',
  `practice_id` int(11) NOT NULL default '0',
  `identifier` varchar(255) NOT NULL default '',
  `facility_code_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='STARTEMPTY';

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='STARTWITHDATA';

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='STARTEMPTY';

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
  `claim_id` int(11) NOT NULL default '0',
  `encounter_id` int(11) NOT NULL default '0',
  `identifier` varchar(255) NOT NULL default '',
  `total_billed` float(7,2) NOT NULL default '0.00',
  `total_paid` float(7,2) NOT NULL default '0.00',
  PRIMARY KEY  (`claim_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='STARTEMPTY';

--
-- Dumping data for table `clearhealth_claim`
--


/*!40000 ALTER TABLE `clearhealth_claim` DISABLE KEYS */;
LOCK TABLES `clearhealth_claim` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `clearhealth_claim` ENABLE KEYS */;

--
-- Table structure for table `codes`
--

DROP TABLE IF EXISTS `codes`;
CREATE TABLE `codes` (
  `code_id` int(11) NOT NULL auto_increment,
  `code_text` varchar(255) default NULL,
  `code_text_short` varchar(24) default NULL,
  `code` varchar(10) default NULL,
  `code_type` tinyint(2) default NULL,
  `modifier` varchar(5) default NULL,
  `units` tinyint(3) default NULL,
  `fee` decimal(7,2) default NULL,
  `superbill` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`code_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `coding_data_id` int(11) NOT NULL default '0',
  `foreign_id` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `code_id` int(11) NOT NULL default '0',
  `modifier` int(11) NOT NULL default '0',
  `units` float(5,2) NOT NULL default '1.00',
  `fee` float(11,2) NOT NULL default '0.00',
  `primary_code` tinyint(4) NOT NULL default '0',
  `code_order` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`coding_data_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `reason_id` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `coding_parent_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`coding_template_id`),
  KEY `practice_id` (`practice_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `company_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `notes` text NOT NULL,
  `initials` varchar(10) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `is_historic` enum('no','yes') NOT NULL default 'no',
  PRIMARY KEY  (`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Base Company record most of the data is linked in STARTEMPTY';

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
  KEY `company_id` (`company_id`),
  KEY `address_id` (`address_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Links a company to a address specifying the type STARTEMPTY';

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
  KEY `company_id` (`company_id`),
  KEY `related_company_id` (`related_company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Relates a company to another company STARTEMPTY';

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Links between company and phone_numbers STARTEMPTY';

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Link to specify company type';

--
-- Dumping data for table `company_type`
--


/*!40000 ALTER TABLE `company_type` DISABLE KEYS */;
LOCK TABLES `company_type` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `company_type` ENABLE KEYS */;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `id` int(11) NOT NULL default '0',
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `eligibility_log_id` int(11) NOT NULL default '0',
  `patient_id` int(11) NOT NULL default '0',
  `log_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `message` longtext NOT NULL,
  PRIMARY KEY  (`eligibility_log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  KEY `last_change_user_id` (`last_change_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `encounter_date_id` int(11) NOT NULL default '0',
  `encounter_id` int(11) NOT NULL default '0',
  `date_type` int(11) NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`encounter_date_id`),
  KEY `encounter_id` (`encounter_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `encounter_person_id` int(11) NOT NULL default '0',
  `encounter_id` int(11) NOT NULL default '0',
  `person_type` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`encounter_person_id`),
  KEY `encounter_id` (`encounter_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `encounter_value_id` int(11) NOT NULL default '0',
  `encounter_id` int(11) NOT NULL default '0',
  `value_type` int(11) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '0',
  PRIMARY KEY  (`encounter_value_id`),
  KEY `encounter_id` (`encounter_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `enumeration_definition`
--


/*!40000 ALTER TABLE `enumeration_definition` DISABLE KEYS */;
LOCK TABLES `enumeration_definition` WRITE;
INSERT INTO `enumeration_definition` VALUES (300466,'address_type','Address Type','Default'),(300472,'appointment_reasons','Appointment Reason','AppointmentReason'),(300480,'assigning','Assigning','Default'),(300485,'code_modifier','Code Modifier','Default'),(300492,'company_number_type','Company Number Type','Default'),(300495,'company_type','Company Type','Default'),(300497,'disposition','Disposition','Default'),(300501,'encounter_date_type','Encounter Date Type','Default'),(300510,'encounter_person_type','Encounter Person Type','Default'),(300512,'encounter_reason','Encounter Reason','EncounterReason'),(300515,'encounter_value_type','Encounter Value Type','Default'),(300521,'ethnicity','Ethnicity','Default'),(300524,'gender','Gender','Default'),(300528,'group_list','File Groups','Default'),(300532,'identifier_type','Identifier Type','Default'),(300535,'income','Income','Default'),(300540,'language','Languages','Default'),(300560,'marital_status','Marital Status','Default'),(300564,'migrant_status','Migrant Status','Default'),(300566,'number_type','Phone Number Type','Default'),(300572,'payer_type','Payer Type','Default'),(300582,'payment_type','Payment Type','Default'),(300589,'person_to_person_relation_type','Person to person relation type','Default'),(300594,'person_type','Person Type','PersonType'),(608614,'provider_number_type','Provider Number Type','MappedValue'),(300602,'provider_reporting_type','Provider Reporting Type','Default'),(300608,'quality_of_file','Quality of File','Default'),(300611,'race','Race','Default'),(608378,'confidential_family_planning_and_disease_codes','Confidential Family Planning and Disease Codes','ConfidentialFamilyPlanningAndDisease'),(300624,'state','State','Default'),(300677,'subscriber_to_patient_relationship','Subscriber to patient relationship','Default'),(300525,'system_reports','System Reports','Url'),(300818,'chronic_care_codes','Chronic Care Codes','Default'),(300852,'funds_source','Funds Source','Default'),(607809,'audit_type','Audit Type','Default'),(601227,'confidentiality_levels','Confidentiality Levels','Default'),(601942,'account_note_type','Account Note Type','Default'),(607814,'confidential_family_planning_codes','Confidential family planning codes','Default'),(607816,'confidential_disease_codes','Confidential_disease_codes','Default'),(607818,'days_of_week','Days of Week','Default'),(607826,'eob_adjustment_type','Eob Adjustment Type','Default'),(607830,'months_of_year','Months of Year','Default'),(607843,'recurrence_pattern_type','Recurrence Pattern Type','MappedValue'),(607849,'subscriber_to_patient','Subscriber to patient','Default'),(607852,'weeks_of_month','Weeks of Month','Default'),(812287,'dm_group_list','Document Group List','Default');
UNLOCK TABLES;
/*!40000 ALTER TABLE `enumeration_definition` ENABLE KEYS */;

--
-- Table structure for table `enumeration_value`
--

DROP TABLE IF EXISTS `enumeration_value`;
CREATE TABLE `enumeration_value` (
  `enumeration_value_id` int(11) NOT NULL default '0',
  `enumeration_id` int(11) NOT NULL default '0',
  `key` int(11) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  `sort` int(11) NOT NULL default '0',
  `extra1` varchar(255) NOT NULL default '',
  `extra2` varchar(255) NOT NULL default '',
  `status` int(1) NOT NULL default '1',
  PRIMARY KEY  (`enumeration_value_id`),
  KEY `key` (`key`),
  KEY `enumeration_id` (`enumeration_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `enumeration_value`
--


/*!40000 ALTER TABLE `enumeration_value` DISABLE KEYS */;
LOCK TABLES `enumeration_value` WRITE;
INSERT INTO `enumeration_value` VALUES (300013,300012,1,'Hello',0,'','',1),(300014,300012,2,'World',0,'','',1),(300016,300015,1,'test',0,'','',1),(300017,300015,2,'second test',1,'','',1),(300039,300038,1,'Home',0,'','',1),(300040,300038,2,'Billing',0,'','',1),(300041,300038,3,'Other',0,'','',1),(300042,300038,4,'Main',0,'','',1),(300043,300038,5,'Secondary',0,'','',1),(300045,300044,1,'Physical',0,'','',1),(300046,300044,2,'FP',1,'','',1),(300047,300044,3,'CDP',2,'','',1),(300048,300044,4,'CHDP',3,'','',1),(300049,300044,5,'F/U',4,'','',1),(300050,300044,6,'Sick',5,'','',1),(300051,300044,7,'Lab Only',6,'','',1),(300053,300052,1,'A - Assigned',0,'','',1),(300054,300052,2,'B - Assigned Lab Services Only',0,'','',1),(300055,300052,3,'C - Not Assigned',0,'','',1),(300056,300052,4,'P - Assignment Refused',0,'','',1),(300058,300057,1,'A0',0,'','',1),(300059,300057,2,'A1',0,'','',1),(300060,300057,3,'A2',0,'','',1),(300061,300057,4,'B1',0,'','',1),(300062,300057,5,'B2',0,'','',1),(300063,300057,6,'C6',0,'','',1),(300065,300064,1,'Primary',0,'','',1),(300066,300064,2,'Fax',0,'','',1),(300068,300067,1,'Insurance',0,'','',1),(300070,300069,1,'New',0,'','',1),(300071,300069,2,'Waiting',0,'','',1),(300072,300069,3,'Compete',0,'','',1),(300074,300073,1,'date_of_death',0,'','',1),(300075,300073,2,'date_last_seen',0,'','',1),(300076,300073,3,'date_of_onset',0,'','',1),(300077,300073,4,'date_of_initial_treatment',0,'','',1),(300078,300073,5,'date_of_cant_work_start',0,'','',1),(300079,300073,6,'date_of_cant_work_end',0,'','',1),(300080,300073,7,'date_of_hospitalization_start',0,'','',1),(300081,300073,8,'date_of_hospitalization_end',0,'','',1),(300083,300082,1,'Referring Provider',0,'','',1),(300085,300084,1,'Physical',0,'','',1),(300086,300084,2,'Other',0,'','',1),(300088,300087,1,'medicaid_resubmission_code',0,'','',1),(300089,300087,2,'prior_authorization_number',0,'','',1),(300090,300087,3,'auto_accident_state',0,'','',1),(300091,300087,4,'original_reference_number',0,'','',1),(300092,300087,5,'hcfa_10d_comment',0,'','',1),(300094,300093,1,'Hispanic',0,'','',1),(300095,300093,2,'Caucasian',0,'','',1),(300097,300096,1,'Male',0,'','',1),(300098,300096,2,'Female',0,'','',1),(300099,300096,3,'Unknown',0,'','',1),(300101,300100,1,'All',0,'','',1),(300102,300100,2,'Arizona',0,'','',1),(300103,300100,3,'California',0,'','',1),(300105,300104,1,'SSN',0,'','',1),(300106,300104,2,'EIN',0,'','',1),(300108,300107,1,'Unknown',0,'','',1),(300109,300107,2,'Under 100% of Poverty',0,'','',1),(300110,300107,3,'100-200% of Poverty',0,'','',1),(300111,300107,4,'Above 200% of Poverty',0,'','',1),(300113,300112,1,'English',0,'','',1),(300114,300112,2,'Spanish',0,'','',1),(300115,300112,3,'Chinese',0,'','',1),(300116,300112,4,'Japanese',0,'','',1),(300117,300112,5,'Korean',0,'','',1),(300118,300112,6,'Portuguese',0,'','',1),(300119,300112,7,'Russian',0,'','',1),(300120,300112,8,'Sign Language',0,'','',1),(300121,300112,9,'Vietnamese',0,'','',1),(300122,300112,10,'Tagalog',0,'','',1),(300123,300112,11,'Punjabi',0,'','',1),(300124,300112,12,'Hindustani',0,'','',1),(300125,300112,13,'Armenian',0,'','',1),(300126,300112,14,'Arabic',0,'','',1),(300127,300112,15,'Laotian',0,'','',1),(300128,300112,16,'Hmong',0,'','',1),(300129,300112,17,'Cambodian',0,'','',1),(300130,300112,18,'Finnish',0,'','',1),(300131,300112,19,'Other',0,'','',1),(300133,300132,1,'Single',0,'','',1),(300134,300132,2,'Married',0,'','',1),(300135,300132,3,'Other',0,'','',1),(300137,300136,1,'Migrant Worker',0,'','',1),(300139,300138,1,'Home',0,'','',1),(300140,300138,2,'Mobile',0,'','',1),(300141,300138,3,'Work',0,'','',1),(300142,300138,4,'Emergency',0,'','',1),(300143,300138,5,'Fax',0,'','',1),(300145,300144,1,'medicare',0,'','',1),(300146,300144,2,'champus',0,'','',1),(300147,300144,3,'medical',0,'','',1),(300148,300144,4,'private',0,'','',1),(300149,300144,5,'feca',0,'','',1),(300150,300144,6,'medicaid',0,'','',1),(300151,300144,7,'champusva',0,'','',1),(300152,300144,8,'otherhcfa',0,'','',1),(300153,300144,9,'litigation',0,'','',1),(300155,300154,1,'visa',0,'','',1),(300156,300154,2,'mastercard',0,'','',1),(300157,300154,3,'amex',0,'','',1),(300158,300154,4,'check',0,'','',1),(300159,300154,5,'cash',0,'','',1),(300160,300154,6,'remittance',0,'','',1),(300162,300161,1,'Dependant',0,'','',1),(300163,300161,2,'Spouse',0,'','',1),(300164,300161,3,'Grand Parent',0,'','',1),(300165,300161,4,'Other',0,'','',1),(300167,300166,1,'Patient',0,'','',1),(300168,300166,2,'Provider',0,'','',1),(300169,300166,3,'Mid-level',0,'','',1),(300170,300166,4,'Staff',0,'','',1),(300171,300166,5,'Subscriber',0,'','',1),(300173,300172,1,'State License',0,'','',1),(300175,300174,1,'MD',0,'','',1),(300176,300174,2,'RNFP',0,'','',1),(300177,300174,3,'RN',0,'','',1),(300178,300174,4,'PA',0,'','',1),(300179,300174,5,'MA',0,'','',1),(300181,300180,1,'Good',0,'','',1),(300182,300180,2,'Bad',0,'','',1),(300184,300183,1,'White/Hispanic',0,'','',1),(300185,300183,2,'Black',0,'','',1),(300186,300183,3,'Native American/Alaskan Native',0,'','',1),(300187,300183,4,'Asian/Pacific Islander',0,'','',1),(300188,300183,5,'Other/Unknown',0,'','',1),(300190,300189,1,'A - On file',0,'','',1),(300191,300189,2,'I - Informed Consent',0,'','',1),(300192,300189,3,'M - Limited Ability',0,'','',1),(300193,300189,4,'N - Not allowed',0,'','',1),(300194,300189,5,'O - On file',0,'','',1),(300195,300189,6,'Y - Has permission',0,'','',1),(300197,300196,1,'AL',0,'','',1),(300198,300196,2,'AK',0,'','',1),(300199,300196,3,'AZ',0,'','',1),(300200,300196,4,'AR',0,'','',1),(300201,300196,5,'CA',0,'','',1),(300202,300196,6,'CO',0,'','',1),(300203,300196,7,'CT',0,'','',1),(300204,300196,8,'DE',0,'','',1),(300205,300196,9,'DC',0,'','',1),(300206,300196,10,'FL',0,'','',1),(300207,300196,11,'GA',0,'','',1),(300208,300196,12,'HI',0,'','',1),(300209,300196,13,'ID',0,'','',1),(300210,300196,14,'IL',0,'','',1),(300211,300196,15,'IN',0,'','',1),(300212,300196,16,'IA',0,'','',1),(300213,300196,17,'KS',0,'','',1),(300214,300196,18,'KY',0,'','',1),(300215,300196,19,'LA',0,'','',1),(300216,300196,20,'ME',0,'','',1),(300217,300196,21,'MD',0,'','',1),(300218,300196,22,'MA',0,'','',1),(300219,300196,23,'MI',0,'','',1),(300220,300196,24,'MN',0,'','',1),(300221,300196,25,'MS',0,'','',1),(300222,300196,26,'MO',0,'','',1),(300223,300196,27,'MT',0,'','',1),(300224,300196,28,'NE',0,'','',1),(300225,300196,29,'NV',0,'','',1),(300226,300196,30,'NH',0,'','',1),(300227,300196,31,'NJ',0,'','',1),(300228,300196,32,'NM',0,'','',1),(300229,300196,33,'NY',0,'','',1),(300230,300196,34,'NC',0,'','',1),(300232,300196,36,'OH',0,'','',1),(300233,300196,37,'OK',0,'','',1),(300234,300196,38,'OR',0,'','',1),(300235,300196,39,'PA',0,'','',1),(300236,300196,40,'RI',0,'','',1),(300237,300196,41,'SC',0,'','',1),(300238,300196,42,'SD',0,'','',1),(300239,300196,43,'TN',0,'','',1),(300240,300196,44,'TX',0,'','',1),(300241,300196,45,'UT',0,'','',1),(300242,300196,46,'VT',0,'','',1),(300243,300196,47,'VA',0,'','',1),(300244,300196,48,'WA',0,'','',1),(300245,300196,49,'WV',0,'','',1),(300246,300196,50,'WI',0,'','',1),(300247,300196,51,'WY',0,'','',1),(300248,300196,52,'PR',0,'','',1),(300250,300249,1,'Spouse',0,'','',1),(300251,300249,2,'Parent',0,'','',1),(300253,300252,1,'Home',0,'','',1),(300254,300252,2,'Billing',0,'','',1),(300255,300252,3,'Other',0,'','',1),(300259,300258,1,'Physical',0,'','',1),(300260,300258,2,'FP',0,'','',1),(300261,300258,3,'CDP',0,'','',1),(300262,300258,4,'CHDP',0,'','',1),(300263,300258,5,'F/U',0,'','',1),(300264,300258,6,'Sick',0,'','',1),(300265,300258,7,'Lab Only',0,'','',1),(300267,300266,1,'A - Assigned',0,'','',1),(300291,300287,4,'date_of_initial_treatment',0,'','',1),(300292,300287,5,'date_of_cant_work_start',0,'','',1),(300293,300287,6,'date_of_cant_work_end',0,'','',1),(300294,300287,7,'date_of_hospitalization_start',0,'','',1),(300295,300287,8,'date_of_hospitalization_end',0,'','',1),(300297,300296,1,'Referring Provider',0,'','',1),(300299,300298,1,'Physical',0,'','',1),(300300,300298,2,'Other',0,'','',1),(300302,300301,1,'medicaid_resubmission_code',0,'','',1),(300306,300301,5,'hcfa_10d_comment',0,'','',1),(300308,300307,1,'Hispanic',0,'','',1),(300309,300307,2,'Caucasian',0,'','',1),(300311,300310,1,'Male',0,'','',1),(300312,300310,2,'Female',0,'','',1),(300313,300310,3,'Unknown',0,'','',1),(300315,300314,1,'All',0,'','',1),(300316,300314,2,'Arizona',0,'','',1),(300317,300314,3,'California',0,'','',1),(300319,300318,1,'SSN',0,'','',1),(300320,300318,2,'EIN',0,'','',1),(300322,300321,1,'Unknown',0,'','',1),(300323,300321,2,'Under 100% of Poverty',0,'','',1),(300324,300321,3,'100-200% of Poverty',0,'','',1),(300325,300321,4,'Above 200% of Poverty',0,'','',1),(300327,300326,1,'English',0,'','',1),(300328,300326,2,'Spanish',0,'','',1),(300329,300326,3,'Chinese',0,'','',1),(300330,300326,4,'Japanese',0,'','',1),(300331,300326,5,'Korean',0,'','',1),(300332,300326,6,'Portuguese',0,'','',1),(300333,300326,7,'Russian',0,'','',1),(300334,300326,8,'Sign Language',0,'','',1),(300335,300326,9,'Vietnamese',0,'','',1),(300336,300326,10,'Tagalog',0,'','',1),(300337,300326,11,'Punjabi',0,'','',1),(300338,300326,12,'Hindustani',0,'','',1),(300339,300326,13,'Armenian',0,'','',1),(300340,300326,14,'Arabic',0,'','',1),(300341,300326,15,'Laotian',0,'','',1),(300342,300326,16,'Hmong',0,'','',1),(300343,300326,17,'Cambodian',0,'','',1),(300344,300326,18,'Finnish',0,'','',1),(300347,300346,1,'Single',0,'','',1),(300348,300346,2,'Married',0,'','',1),(300349,300346,3,'Other',0,'','',1),(300351,300350,1,'Migrant Worker',0,'','',1),(300353,300352,1,'Home',0,'','',1),(300354,300352,2,'Mobile',0,'','',1),(300355,300352,3,'Work',0,'','',1),(300356,300352,4,'Emergency',0,'','',1),(300357,300352,5,'Fax',0,'','',1),(300359,300358,1,'medicare',0,'','',1),(300360,300358,2,'champus',0,'','',1),(300361,300358,3,'medical',0,'','',1),(300362,300358,4,'private',0,'','',1),(300363,300358,5,'feca',0,'','',1),(300364,300358,6,'medicaid',0,'','',1),(300365,300358,7,'champusva',0,'','',1),(300366,300358,8,'otherhcfa',0,'','',1),(300367,300358,9,'litigation',0,'','',1),(300369,300368,1,'visa',0,'','',1),(300370,300368,2,'mastercard',0,'','',1),(300371,300368,3,'amex',0,'','',1),(300372,300368,4,'check',0,'','',1),(300373,300368,5,'cash',0,'','',1),(300374,300368,6,'remittance',0,'','',1),(300376,300375,1,'Dependant',0,'','',1),(300377,300375,2,'Spouse',0,'','',1),(300378,300375,3,'Grand Parent',0,'','',1),(300379,300375,4,'Other',0,'','',1),(300381,300380,1,'Patient',0,'','',1),(300382,300380,2,'Provider',0,'','',1),(300383,300380,3,'Mid-level',0,'','',1),(300384,300380,4,'Staff',0,'','',1),(300385,300380,5,'Subscriber',0,'','',1),(300387,300386,1,'State License',0,'','',1),(300389,300388,1,'MD',0,'','',1),(300390,300388,2,'RNFP',0,'','',1),(300391,300388,3,'RN',0,'','',1),(300392,300388,4,'PA',0,'','',1),(300393,300388,5,'MA',0,'','',1),(300395,300394,1,'Good',0,'','',1),(300396,300394,2,'Bad',0,'','',1),(300398,300397,1,'White/Hispanic',0,'','',1),(300399,300397,2,'Black',0,'','',1),(300400,300397,3,'Native American/Alaskan Native',0,'','',1),(300401,300397,4,'Asian/Pacific Islander',0,'','',1),(300402,300397,5,'Other/Unknown',0,'','',1),(300404,300403,1,'A - On file',0,'','',1),(300405,300403,2,'I - Informed Consent',0,'','',1),(300406,300403,3,'M - Limited Ability',0,'','',1),(300407,300403,4,'N - Not allowed',0,'','',1),(300408,300403,5,'O - On file',0,'','',1),(300409,300403,6,'Y - Has permission',0,'','',1),(300411,300410,1,'AL',0,'','',1),(300412,300410,2,'AK',0,'','',1),(300413,300410,3,'AZ',0,'','',1),(300414,300410,4,'AR',0,'','',1),(300415,300410,5,'CA',0,'','',1),(300416,300410,6,'CO',0,'','',1),(300417,300410,7,'CT',0,'','',1),(300418,300410,8,'DE',0,'','',1),(300419,300410,9,'DC',0,'','',1),(300420,300410,10,'FL',0,'','',1),(300421,300410,11,'GA',0,'','',1),(300422,300410,12,'HI',0,'','',1),(300423,300410,13,'ID',0,'','',1),(300424,300410,14,'IL',0,'','',1),(300425,300410,15,'IN',0,'','',1),(300426,300410,16,'IA',0,'','',1),(300427,300410,17,'KS',0,'','',1),(300428,300410,18,'KY',0,'','',1),(300429,300410,19,'LA',0,'','',1),(300430,300410,20,'ME',0,'','',1),(300431,300410,21,'MD',0,'','',1),(300432,300410,22,'MA',0,'','',1),(300433,300410,23,'MI',0,'','',1),(300434,300410,24,'MN',0,'','',1),(300435,300410,25,'MS',0,'','',1),(300436,300410,26,'MO',0,'','',1),(300437,300410,27,'MT',0,'','',1),(300438,300410,28,'NE',0,'','',1),(300439,300410,29,'NV',0,'','',1),(300440,300410,30,'NH',0,'','',1),(300441,300410,31,'NJ',0,'','',1),(300442,300410,32,'NM',0,'','',1),(300443,300410,33,'NY',0,'','',1),(300444,300410,34,'NC',0,'','',1),(300445,300410,35,'ND',0,'','',1),(300446,300410,36,'OH',0,'','',1),(300447,300410,37,'OK',0,'','',1),(300448,300410,38,'OR',0,'','',1),(300449,300410,39,'PA',0,'','',1),(300450,300410,40,'RI',0,'','',1),(300451,300410,41,'SC',0,'','',1),(300452,300410,42,'SD',0,'','',1),(300453,300410,43,'TN',0,'','',1),(300454,300410,44,'TX',0,'','',1),(300455,300410,45,'UT',0,'','',1),(300456,300410,46,'VT',0,'','',1),(300457,300410,47,'VA',0,'','',1),(300458,300410,48,'WA',0,'','',1),(300459,300410,49,'WV',0,'','',1),(300460,300410,50,'WI',0,'','',1),(300461,300410,51,'WY',0,'','',1),(300462,300410,52,'PR',0,'','',1),(300464,300463,1,'Spouse',0,'','',1),(300465,300463,2,'Parent',0,'','',1),(300467,300466,2,'Home',0,'','',1),(300468,300466,1,'Billing',1,'','',1),(300469,300466,3,'Other',2,'','',1),(300470,300466,4,'Main',3,'','',1),(300471,300466,5,'Secondary',4,'','',1),(300474,300472,2,'FP',1,'','',1),(300475,300472,3,'CDP',2,'','',1),(800232,300512,17,'Long Treatment',16,'','',1),(300477,300472,5,'F/U',4,'','',1),(300478,300472,6,'Sick',5,'','',1),(300479,300472,7,'Lab Only',6,'','',1),(300481,300480,1,'A - Assigned',0,'','',1),(300482,300480,2,'B - Assigned Lab Services Only',0,'','',1),(300483,300480,3,'C - Not Assigned',0,'','',1),(300484,300480,4,'P - Assignment Refused',0,'','',1),(300486,300485,1,'A0',0,'','',1),(300487,300485,2,'A1',0,'','',1),(300488,300485,3,'A2',0,'','',1),(300489,300485,4,'B1',0,'','',1),(300490,300485,5,'B2',0,'','',1),(300491,300485,6,'C6',0,'','',1),(300493,300492,1,'Primary',0,'','',1),(300494,300492,2,'Fax',0,'','',1),(300496,300495,1,'Insurance',0,'','',1),(300498,300497,1,'New',0,'','',1),(300499,300497,2,'Waiting',0,'','',1),(300500,300497,3,'Compete',0,'','',1),(300502,300501,1,'date_of_death',0,'','',1),(300503,300501,2,'date_last_seen',0,'','',1),(300504,300501,3,'date_of_onset',0,'','',1),(300505,300501,4,'date_of_initial_treatment',0,'','',1),(300506,300501,5,'date_of_cant_work_start',0,'','',1),(300507,300501,6,'date_of_cant_work_end',0,'','',1),(300508,300501,7,'date_of_hospitalization_start',0,'','',1),(300509,300501,8,'date_of_hospitalization_end',0,'','',1),(300511,300510,1,'Referring Provider',0,'','',1),(300513,300512,1,'Physical',0,'','',1),(300514,300512,3,'CDP',2,'','',1),(300516,300515,1,'medicaid_resubmission_code',1,'','',1),(300517,300515,2,'prior_authorization_number',1,'','',1),(300518,300515,3,'auto_accident_state',2,'','',1),(300519,300515,4,'original_reference_number',3,'','',1),(300520,300515,5,'hcfa_10d_comment',4,'','',1),(300522,300521,1,'Hispanic',0,'','',1),(300523,300521,2,'Caucasian',1,'','',1),(300525,300524,1,'Male',0,'','',1),(300526,300525,1,'Patient Statement',0,'/Patient/statement','',1),(300527,300524,3,'Unknown',2,'','',1),(300529,300528,1,'All',0,'','',1),(300530,300528,2,'Arizona',0,'','',1),(300531,300528,3,'California',0,'','',1),(300533,300532,1,'SSN',1,'','',1),(300534,300532,2,'EIN',1,'','',1),(300536,300535,1,'Unknown',0,'','',1),(300537,300535,2,'Under 100% of Poverty',0,'','',1),(300538,300535,3,'100-200% of Poverty',0,'','',1),(300539,300535,4,'Above 200% of Poverty',0,'','',1),(300541,300540,1,'English',0,'','',1),(300542,300540,2,'Spanish',0,'','',1),(300543,300540,3,'Chinese',0,'','',1),(300544,300540,4,'Japanese',0,'','',1),(300545,300540,5,'Korean',0,'','',1),(300546,300540,6,'Portuguese',0,'','',1),(300547,300540,7,'Russian',0,'','',1),(300548,300540,8,'Sign Language',0,'','',1),(300549,300540,9,'Vietnamese',0,'','',1),(300550,300540,10,'Tagalog',0,'','',1),(300551,300540,11,'Punjabi',0,'','',1),(300552,300540,12,'Hindustani',0,'','',1),(300553,300540,13,'Armenian',0,'','',1),(300554,300540,14,'Arabic',0,'','',1),(300555,300540,15,'Laotian',0,'','',1),(300556,300540,16,'Hmong',0,'','',1),(300557,300540,17,'Cambodian',0,'','',1),(300558,300540,18,'Finnish',0,'','',1),(300559,300540,19,'Other',0,'','',1),(300561,300560,1,'Single',0,'','',1),(300562,300560,2,'Married',0,'','',1),(300563,300560,3,'Other',0,'','',1),(300565,300564,1,'Migrant Worker',0,'','',1),(300567,300566,1,'Home',0,'','',1),(300568,300566,2,'Mobile',0,'','',1),(300569,300566,3,'Work',0,'','',1),(300570,300566,4,'Emergency',0,'','',1),(300571,300566,5,'Fax',0,'','',1),(300573,300572,1,'medicare',0,'','',1),(300574,300572,2,'champus',2,'','',1),(300575,300572,3,'medical',3,'','',1),(300576,300572,4,'private pay',4,'','',1),(300577,300572,5,'feca',5,'','',1),(300578,300572,6,'medicaid',6,'','',1),(300579,300572,7,'champusva',7,'','',1),(300580,300572,8,'otherhcfa',8,'','',1),(300581,300572,9,'litigation',9,'','',1),(300583,300582,1,'visa',0,'','',1),(300584,300582,2,'mastercard',0,'','',1),(300585,300582,3,'amex',0,'','',1),(300586,300582,4,'check',0,'','',1),(300587,300582,5,'cash',0,'','',1),(300588,300582,6,'remittance',0,'','',1),(300590,300589,1,'Dependant',0,'','',1),(300591,300589,2,'Spouse',0,'','',1),(300592,300589,3,'Grand Parent',0,'','',1),(300593,300589,4,'Other',0,'','',1),(300595,300594,1,'Patient',0,'0','',1),(300596,300594,2,'Provider',1,'1','',1),(300597,300594,3,'Mid-level',2,'1','',1),(300598,300594,4,'Staff',3,'1','',1),(300599,300594,5,'Subscriber',4,'0','',1),(300601,300300,1,'State License',0,'','',1),(300603,300602,1,'MD',0,'','',1),(300604,300602,2,'RNFP',0,'','',1),(300605,300602,3,'RN',0,'','',1),(300606,300602,4,'PA',0,'','',1),(300607,300602,5,'MA',0,'','',1),(300609,300608,1,'Good',0,'','',1),(300610,300608,2,'Bad',0,'','',1),(300612,300611,1,'White/Hispanic',0,'','',1),(300613,300611,2,'Black',0,'','',1),(300614,300611,3,'Native American/Alaskan Native',0,'','',1),(300615,300611,4,'Asian/Pacific Islander',0,'','',1),(300616,300611,5,'Other/Unknown',0,'','',1),(811931,300472,4,'CHDP',3,'','',1),(300625,300624,1,'AL',1,'','',1),(300626,300624,2,'AK',0,'','',1),(300627,300624,3,'AZ',3,'','',1),(300628,300624,4,'AR',2,'','',1),(300629,300624,5,'CA',4,'','',1),(300630,300624,6,'CO',5,'','',1),(300631,300624,7,'CT',6,'','',1),(300632,300624,8,'DE',8,'','',1),(300633,300624,9,'DC',7,'','',1),(300634,300624,10,'FL',9,'','',1),(300635,300624,11,'GA',10,'','',1),(300636,300624,12,'HI',11,'','',1),(300637,300624,13,'ID',13,'','',1),(300638,300624,14,'IL',14,'','',1),(300639,300624,15,'IN',15,'','',1),(300640,300624,16,'IA',12,'','',1),(300641,300624,17,'KS',16,'','',1),(300642,300624,18,'KY',17,'','',1),(300643,300624,19,'LA',18,'','',1),(300644,300624,20,'ME',21,'','',1),(300645,300624,21,'MD',20,'','',1),(300646,300624,22,'MA',19,'','',1),(300647,300624,23,'MI',22,'','',1),(300648,300624,24,'MN',23,'','',1),(300649,300624,25,'MS',25,'','',1),(300650,300525,2,'Family Patient Statement',1,'/Patient/familyStatement','',1),(300651,300525,3,'Pull List',2,'/Appointment/pullList','',1),(300659,300624,35,'ND',28,'','',1),(300660,300624,36,'OH',34,'','',1),(300661,300624,37,'OK',35,'','',1),(300662,300624,38,'OR',36,'','',1),(300663,300624,39,'PA',37,'','',1),(300664,300624,40,'RI',39,'','',1),(300665,300624,41,'SC',40,'','',1),(300666,300624,42,'SD',41,'','',1),(300667,300624,43,'TN',42,'','',1),(300668,300624,44,'TX',43,'','',1),(300669,300624,45,'UT',44,'','',1),(300670,300624,46,'VT',46,'','',1),(300671,300624,47,'VA',45,'','',1),(300672,300624,48,'WA',47,'','',1),(300673,300624,49,'WV',49,'','',1),(300674,300624,50,'WI',48,'','',1),(300675,300624,51,'WY',50,'','',1),(300676,300624,52,'PR',38,'','',1),(300678,300677,1,'Self',0,'','',1),(300679,300677,2,'Spouse',1,'','',1),(601943,601942,1,'x12',0,'','',1),(300747,300525,4,'Route Slip',3,'/Encounter/routeSlip','',1),(300819,300818,1,'Diabetes',0,'','',1),(300820,300818,2,'Hypertension',2,'','',1),(300853,300852,1,'Patient',0,'','',1),(300854,300852,2,'Private Insurance',0,'','',1),(300855,300852,3,'State Program',0,'','',1),(300856,300852,4,'Federal Program',0,'','',1),(300932,300818,3,'hrt',1,'','',1),(301505,300524,2,'Female',0,'','',1),(301506,300512,2,'FP',1,'','',1),(301507,300521,3,'Asian',0,'','',1),(301508,300572,10,' private insurance',1,'','',1),(301522,300564,2,'Seasonal Worker',0,'','',1),(301523,300564,3,'No',0,'','',1),(301524,300564,4,'other',0,'','',1),(301538,300818,4,'Hypercholestrolemia',0,'','',1),(601228,601227,1,'1 - No Special Restrictions',1,'','',1),(601229,601227,2,'2 - Basic Confidentiality',1,'','',1),(601230,601227,3,'3 - Family Planning',2,'','',1),(601231,601227,4,'4 - Disease Confidentiality',3,'','',1),(601232,601227,5,'6 - Extreme Confidentiality',5,'','',1),(601244,300677,3,'Parent',0,'','',1),(601245,300472,8,'General Visit',7,'','',1),(607182,300624,53,'MO',24,'','',1),(607183,300624,54,'MT',26,'','',1),(607184,300624,55,'NV',32,'','',1),(607185,300624,56,'NH',29,'','',1),(607186,300624,57,'NJ',30,'','',1),(607187,300624,58,'NM',31,'','',1),(607188,300624,59,'NY',33,'','',1),(607189,300624,60,'NC',27,'','',1),(607547,300038,6,'Employer',6,'','',1),(607776,300466,6,'Employer',6,'','',1),(607810,607809,1,'insert',0,'','',1),(607811,607809,2,'update',1,'','',1),(607812,607809,3,'delete',2,'','',1),(607813,607809,4,'process',3,'','',1),(607815,607814,0,'A4770',1,'','',1),(607817,607816,0,'S2095',0,'','',1),(607819,607818,7,'Sunday',0,'','',1),(607820,607818,1,'Monday',0,'','',1),(607821,607818,2,'Tuesday',0,'','',1),(607822,607818,3,'Wednesday',0,'','',1),(607823,607818,4,'Thursday',0,'','',1),(607824,607818,5,'Friday',0,'','',1),(607825,607818,6,'Saturday',0,'','',1),(607827,607826,1,'Patient Responsibilty',1,'','',1),(607828,607826,2,'Total Charge',2,'','',1),(607829,607826,3,'Plan Type',3,'','',1),(607831,607830,1,'January',0,'','',1),(607832,607830,2,'February',0,'','',1),(607833,607830,3,'March',0,'','',1),(607834,607830,4,'April',0,'','',1),(607835,607830,5,'May',0,'','',1),(607836,607830,6,'June',0,'','',1),(607837,607830,7,'July',0,'','',1),(607838,607830,8,'August',0,'','',1),(607839,607830,9,'September',0,'','',1),(607840,607830,10,'October',0,'','',1),(607841,607830,11,'November',0,'','',1),(607842,607830,12,'December',0,'','',1),(607844,607843,1,'By Day (Every 3 Days)',0,'day','',1),(607845,607843,2,'onthweekBy Weekday Per Month (Every Third Tuesday)',1,'monthweek','',1),(607846,607843,3,'By Day of Month (Every Fifth)',2,'monthday','',1),(607847,607843,4,'By Day of Month Per Year (Every December 3rd)',3,'yearmonthday','',1),(607848,607843,5,'By Weekday Per Month Per Year (Every Third Tuesday of November)',4,'yearmonthweek','',1),(607850,607849,1,'Spouse',0,'','',1),(607851,607849,2,'Parent',0,'','',1),(607853,607852,0,'First',0,'','',1),(607854,607852,0,'Second',1,'','',1),(607855,607852,0,'Third',2,'','',1),(607856,607852,0,'Fourth',3,'','',1),(607857,607852,0,'Last',4,'','',1),(800224,300512,16,'Medium Treatment',15,'','',1),(820250,300472,10,'PAP',9,'','',1),(815037,607843,6,'By Days of Week',6,'dayweek','',1),(800192,300512,12,'Hospital F/U',11,'','',1),(607990,300515,6,'clia_number',5,'','',1),(607996,300515,7,'claim_filing_code',7,'','',1),(800184,300512,11,'Treatment',10,'','',1),(800176,300512,10,'PAP',9,'','',1),(800168,300512,9,'Yearly',8,'','',1),(608143,300532,3,'UPIN',2,'','',1),(608377,601227,6,'5 - Disease and Family Planning Confidentiality',4,'','',1),(608409,607814,1,'',1,'608410','1',1),(608410,607816,1,'testo',1,'','1',1),(608411,607814,2,'',2,'608412','1',1),(608412,607816,2,'adsf',1,'','1',1),(608447,300472,9,'Yearly',8,'','',1),(800216,300512,15,'Short Treatment',14,'','',1),(800160,300512,8,'General Visit',7,'','',1),(800152,300512,7,'Lab Only',6,'','',1),(811943,300472,1,'Physical',0,'','',1),(800208,300512,14,'Office Visit',13,'','',1),(800144,300512,6,'Sick',5,'','',1),(608458,300512,4,'CHDP',3,'','',1),(608459,300512,5,'F/U',4,'','',1),(608615,608614,1,'State License Number',1,'0B','',1),(608616,608614,2,'Blue Cross Provider Number',2,'1A','',1),(608617,608614,3,'Blue Shield Provider Number',3,'1B','',1),(608618,608614,4,'Medicare Provider Number',4,'1C','',1),(608619,608614,5,'Medicaid Provider Number',5,'1D','',1),(608620,608614,6,'Medi-cal',6,'1D','',1),(608621,608614,7,'Provider UPIN Number',7,'1G','',1),(608622,608614,8,'CHAMPUS Identification Number',8,'1H','',1),(608623,608614,9,'Facility ID Number',9,'1J','',1),(608624,608614,10,'Preferred Provider Organization Number',10,'B3','',1),(608625,608614,11,'Health Maintenance Organization Code Number',11,'BQ','',1),(608626,608614,12,'Employer\'s Identification Number',12,'EI','',1),(608627,608614,13,'Clinic Number',13,'FH','',1),(608628,608614,14,'Provider Commercial Number',14,'G2','',1),(608629,608614,15,'Provider Site Number',15,'G5','',1),(605833,600358,0,'By Days of Week',6,'','',1),(800571,300525,5,'Guarantor',5,'/Patient/Guarantor','',1),(800579,300525,6,'Aged Trail Balance',6,'/Patient/BalanceReport','',1),(800587,300525,7,'Statement (Report)',7,'/Patient/StatementReport','',1),(820263,300472,11,'Treatment',10,'','',1),(834773,300472,12,'Hospital F/U',11,'','',1),(834781,300472,13,'New Patient Office Visit',12,'','',1),(834789,300472,14,'Office Visit',13,'','',1),(834797,300472,15,'Short Treatment',14,'','',1),(834805,300472,16,'Medium Treatment',15,'','',1),(834813,300472,17,'Long Treatment',16,'','',1),(800200,300512,13,'New Patient Office Visit',12,'','',1);
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `event_id` int(11) NOT NULL auto_increment,
  `start` datetime NOT NULL default '0000-00-00 00:00:00',
  `end` datetime NOT NULL default '0000-00-00 00:00:00',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `event_group_id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `room_id` int(11) NOT NULL default '0',
  `schedule_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`event_group_id`),
  KEY `room_id` (`room_id`),
  KEY `schedule_id` (`schedule_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `event_group`
--


/*!40000 ALTER TABLE `event_group` DISABLE KEYS */;
LOCK TABLES `event_group` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `event_group` ENABLE KEYS */;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `website` varchar(255) NOT NULL default '',
  `contact_person` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `foreign_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `events`
--


/*!40000 ALTER TABLE `events` DISABLE KEYS */;
LOCK TABLES `events` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `events` ENABLE KEYS */;

--
-- Table structure for table `facility_codes`
--

DROP TABLE IF EXISTS `facility_codes`;
CREATE TABLE `facility_codes` (
  `facility_code_id` int(11) NOT NULL auto_increment,
  `code` varchar(5) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`facility_code_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Stores x12 facility_code code/human name combos';

--
-- Dumping data for table `facility_codes`
--


/*!40000 ALTER TABLE `facility_codes` DISABLE KEYS */;
LOCK TABLES `facility_codes` WRITE;
INSERT INTO `facility_codes` VALUES (1,'11','Office'),(2,'12','Home'),(3,'21','Inpatient Hospital'),(4,'22','Outpatient Hospital'),(5,'23','Emergency Room - Hospital'),(6,'24','Ambulatory Surgical Center'),(7,'25','Birthing Center'),(8,'26','Military Treatment Facility'),(9,'31','Skilled Nursing Facility'),(10,'32','Nursing Facility'),(11,'33','Custodial Care Facility'),(12,'34','Hospice'),(13,'41','Ambulance - Land'),(14,'42','Ambulance - Air or Water'),(15,'51','Inpatient Psychiatric Facility'),(16,'52','Psychiatric Facility Partial Hospitalization'),(17,'53','Community Mental Health Center'),(18,'54','Intermediate Care Facility/Mentally Retarded'),(19,'55','Residential Substance Abuse Treatment Facility'),(20,'56','Psychiatric Residential Treatment Center'),(21,'50','Federally Qualified Health Center'),(22,'60','Mass Immunization Center'),(23,'61','Comprehensive Inpatient Rehabilitation Facility'),(24,'62','Comprehensive Outpatient Rehabilitation Facility'),(25,'65','End Stage Renal Disease Treatment Facility'),(26,'71','State or Local Public Health Clinic'),(27,'72','Rural Health Clinic'),(28,'81','Independent Laboratory'),(29,'99','Other Unlisted Facility');
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='An address that can be for a company or a person';

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Base Company record most of the data is in linked tables';

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='A person in the system';

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `insurance_program_id` int(11) NOT NULL default '0',
  `type` enum('default','program') NOT NULL default 'default',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`fee_schedule_discount_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `type` enum('percent','flat') NOT NULL default 'percent',
  `disp_order` int(11) NOT NULL default '0',
  PRIMARY KEY  (`fee_schedule_discount_level_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fee_schedule_revision`
--


/*!40000 ALTER TABLE `fee_schedule_revision` DISABLE KEYS */;
LOCK TABLES `fee_schedule_revision` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `fee_schedule_revision` ENABLE KEYS */;

--
-- Table structure for table `form`
--

DROP TABLE IF EXISTS `form`;
CREATE TABLE `form` (
  `form_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`form_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Contains the EMR extending forms STARTWITHDATA';

--
-- Dumping data for table `form`
--


/*!40000 ALTER TABLE `form` DISABLE KEYS */;
LOCK TABLES `form` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `form` ENABLE KEYS */;

--
-- Table structure for table `form_data`
--

DROP TABLE IF EXISTS `form_data`;
CREATE TABLE `form_data` (
  `form_data_id` int(11) NOT NULL default '0',
  `form_id` int(11) NOT NULL default '0',
  `external_id` int(11) NOT NULL default '0',
  `last_edit` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`form_data_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Links in the form data STARTWITHDATA';

--
-- Dumping data for table `form_data`
--


/*!40000 ALTER TABLE `form_data` DISABLE KEYS */;
LOCK TABLES `form_data` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `form_data` ENABLE KEYS */;

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


/*!40000 ALTER TABLE `gacl_acl` DISABLE KEYS */;
LOCK TABLES `gacl_acl` WRITE;
INSERT INTO `gacl_acl` VALUES (26,'user',1,1,'','Give Superadmn and access to everything even when no resource is selected',1153853212),(24,'user',1,1,'','Give Super Admin access to everything ',1153853225),(38,'user',1,1,'','',1129066412),(40,'user',1,1,'','',1129066435),(36,'user',1,1,'','',1129066460),(37,'user',1,1,'','',1119041365),(32,'user',1,1,'','Give billing users basic access to those sections',1129066489),(33,'user',1,1,'','Give all users of the system access to basic app sections',1112057091),(39,'user',1,1,'','',1129066506);
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_acl_sections`
--


/*!40000 ALTER TABLE `gacl_acl_sections` DISABLE KEYS */;
LOCK TABLES `gacl_acl_sections` WRITE;
INSERT INTO `gacl_acl_sections` VALUES (1,'system',1,'System',0),(2,'user',2,'User',0);
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_acl_sections` ENABLE KEYS */;

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


/*!40000 ALTER TABLE `gacl_acl_seq` DISABLE KEYS */;
LOCK TABLES `gacl_acl_seq` WRITE;
INSERT INTO `gacl_acl_seq` VALUES (44),(44);
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_aco`
--


/*!40000 ALTER TABLE `gacl_aco` DISABLE KEYS */;
LOCK TABLES `gacl_aco` WRITE;
INSERT INTO `gacl_aco` VALUES (11,'actions','view',10,'view',0),(12,'actions','edit',11,'edit',0),(13,'actions','add',12,'add',0),(14,'actions','delete',13,'delete',0),(16,'actions','usage',9,'usage',0),(17,'actions','uploadFile',14,'Upload A file',0),(18,'actions','delete_owner',15,'Delete Owner',0),(19,'actions','edit_owner',16,'Edit Owner',0),(20,'actions','double_book',17,'Double Book Apointment',0),(21,'actions','override',1,'override',0);
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_aco_map`
--


/*!40000 ALTER TABLE `gacl_aco_map` DISABLE KEYS */;
LOCK TABLES `gacl_aco_map` WRITE;
INSERT INTO `gacl_aco_map` VALUES (24,'actions','add'),(24,'actions','delete'),(24,'actions','delete_owner'),(24,'actions','double_book'),(24,'actions','edit'),(24,'actions','edit_owner'),(24,'actions','override'),(24,'actions','uploadFile'),(24,'actions','usage'),(24,'actions','view'),(26,'actions','add'),(26,'actions','delete'),(26,'actions','delete_owner'),(26,'actions','double_book'),(26,'actions','edit'),(26,'actions','edit_owner'),(26,'actions','override'),(26,'actions','uploadFile'),(26,'actions','usage'),(26,'actions','view'),(32,'actions','add'),(32,'actions','delete'),(32,'actions','edit'),(32,'actions','usage'),(32,'actions','view'),(33,'actions','usage'),(33,'actions','view'),(36,'actions','usage'),(36,'actions','view'),(37,'actions','add'),(37,'actions','delete_owner'),(37,'actions','edit'),(37,'actions','usage'),(37,'actions','view'),(38,'actions','add'),(38,'actions','delete'),(38,'actions','delete_owner'),(38,'actions','edit'),(38,'actions','usage'),(38,'actions','view'),(39,'actions','add'),(39,'actions','delete'),(39,'actions','double_book'),(39,'actions','edit'),(39,'actions','uploadFile'),(39,'actions','usage'),(39,'actions','view'),(40,'actions','add'),(40,'actions','edit'),(40,'actions','usage'),(40,'actions','view');
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_aco_sections`
--


/*!40000 ALTER TABLE `gacl_aco_sections` DISABLE KEYS */;
LOCK TABLES `gacl_aco_sections` WRITE;
INSERT INTO `gacl_aco_sections` VALUES (11,'actions',10,'Actions',0);
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_aco_sections` ENABLE KEYS */;

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


/*!40000 ALTER TABLE `gacl_aco_sections_seq` DISABLE KEYS */;
LOCK TABLES `gacl_aco_sections_seq` WRITE;
INSERT INTO `gacl_aco_sections_seq` VALUES (11),(11);
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_aco_sections_seq` ENABLE KEYS */;

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


/*!40000 ALTER TABLE `gacl_aco_seq` DISABLE KEYS */;
LOCK TABLES `gacl_aco_seq` WRITE;
INSERT INTO `gacl_aco_seq` VALUES (21),(21);
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_aco_seq` ENABLE KEYS */;

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


/*!40000 ALTER TABLE `gacl_aro` DISABLE KEYS */;
LOCK TABLES `gacl_aro` WRITE;
INSERT INTO `gacl_aro` VALUES (15,'users','admin',10,'admin',0);
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_aro_groups`
--


/*!40000 ALTER TABLE `gacl_aro_groups` DISABLE KEYS */;
LOCK TABLES `gacl_aro_groups` WRITE;
INSERT INTO `gacl_aro_groups` VALUES (10,0,1,44,'Root','root'),(12,23,13,14,'System Admin','admin'),(19,10,2,11,'User Types','users'),(20,19,3,4,'Provider','provider'),(21,19,5,6,'Mid-level','mid-level'),(22,19,7,8,'Staff','staff'),(23,10,12,39,'Roles','roles'),(24,23,15,16,'Supervisor','supervisor'),(26,23,17,18,'Front Office','front_office'),(31,23,37,38,'Staff','role_staff'),(28,23,19,32,'Billing User','billing_user'),(29,23,33,34,'Medical Assistant','medical_assistant');
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_aro_groups` ENABLE KEYS */;

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


/*!40000 ALTER TABLE `gacl_aro_groups_id_seq` DISABLE KEYS */;
LOCK TABLES `gacl_aro_groups_id_seq` WRITE;
INSERT INTO `gacl_aro_groups_id_seq` VALUES (59),(59);
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_aro_groups_map`
--


/*!40000 ALTER TABLE `gacl_aro_groups_map` DISABLE KEYS */;
LOCK TABLES `gacl_aro_groups_map` WRITE;
INSERT INTO `gacl_aro_groups_map` VALUES (24,12),(26,12),(32,28),(33,20),(33,21),(33,22),(36,31),(37,31),(38,29),(39,24),(40,28);
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_aro_map`
--


/*!40000 ALTER TABLE `gacl_aro_map` DISABLE KEYS */;
LOCK TABLES `gacl_aro_map` WRITE;
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_aro_sections`
--


/*!40000 ALTER TABLE `gacl_aro_sections` DISABLE KEYS */;
LOCK TABLES `gacl_aro_sections` WRITE;
INSERT INTO `gacl_aro_sections` VALUES (10,'users',10,'Users',0);
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_aro_sections` ENABLE KEYS */;

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


/*!40000 ALTER TABLE `gacl_aro_sections_seq` DISABLE KEYS */;
LOCK TABLES `gacl_aro_sections_seq` WRITE;
INSERT INTO `gacl_aro_sections_seq` VALUES (11),(11);
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_aro_sections_seq` ENABLE KEYS */;

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


/*!40000 ALTER TABLE `gacl_aro_seq` DISABLE KEYS */;
LOCK TABLES `gacl_aro_seq` WRITE;
INSERT INTO `gacl_aro_seq` VALUES (48),(48);
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_axo`
--


/*!40000 ALTER TABLE `gacl_axo` DISABLE KEYS */;
LOCK TABLES `gacl_axo` WRITE;
INSERT INTO `gacl_axo` VALUES (0,'resources','main',10,'Section - Main',0),(19,'resources','preferences',10,'Section - Preferences',0),(17,'resources','default',10,'Section - Default',0),(16,'resources','access',10,'Section - Access',0),(44,'resources','practice',10,'Section - Practice',0),(43,'resources','personschedule',10,'Section - PersonSchedule',0),(42,'resources','patientfinder',10,'Section - PatientFinder',0),(41,'resources','patient',10,'Section - Patient',0),(40,'resources','location',10,'Section - Location',0),(39,'resources','feeschedule',10,'Section - FeeSchedule',0),(38,'resources','calendar',10,'Section - Calendar',0),(37,'resources','user',10,'Section - User',0),(36,'resources','enumeration',10,'Section - Enumeration',0),(45,'resources','report',10,'Section - Report',0),(46,'resources','schedule',10,'Section - Schedule',0),(47,'resources','form',10,'Section - Form',0),(48,'resources','billing',10,'Section - Billing',0),(49,'resources','admin',10,'Section - Admin',0),(50,'resources','document',10,'Section - Document',0),(51,'resources','documentcategory',10,'Section - DocumentCategory',0),(52,'resources','insurance',10,'Section - Insurance',0),(53,'resources','superbill',10,'Section - Superbill',0),(54,'resources','event',10,'Section - Event',0),(55,'resources','occurence',10,'Section - Occurence',0),(56,'resources','building',10,'Building',0),(57,'resources','room',10,'room',0),(58,'resources','pdf',10,'Section - PDF',0),(59,'resources','coding',10,'Section - Coding',0),(60,'resources','docs',10,'Section - Docs',0),(61,'resources','eob',10,'Section - Eob',0),(62,'resources','claim',10,'Section - Claim',0),(63,'resources','freebgateway',10,'Section - FreeBGateway',0),(64,'resources','main_calendar',1,'Main Group Calendar',0),(65,'resources','main_billing',2,'Main Group Billing',0),(66,'resources','main_patient',3,'Main Group Patient',0),(67,'resources','main_admin',4,'Main Group Admin',0),(68,'resources','account',10,'Section - Account',0),(69,'resources','appointment',10,'Section - Appointment',0),(70,'resources','ajax',10,'Section - Ajax',0),(71,'resources','images',10,'Section - Images',0),(72,'resources','css',10,'Section - Css',0),(73,'resources','myaccount',10,'Section - MyAccount',0),(74,'resources','patientdashboard',10,'Section - PatientDashboard',0),(75,'resources','summaryreport',10,'Section - SummaryReport',0),(76,'resources','encounter',10,'Section - Encounter',0),(77,'resources','test',10,'Section - Test',0),(78,'resources','appointmenttemplate',10,'Section - AppointmentTemplate',0),(79,'resources','occurencebreakdown',10,'Section - OccurenceBreakdown',0),(80,'resources','feeschedulediscount',10,'Section - FeeScheduleDiscount',0),(81,'resources','patientstatistics',10,'Section - PatientStatistics',0),(82,'resources','queue',10,'Section - Queue',0),(83,'resources','print',10,'Section - Print',0),(84,'resources','cronable',10,'Section - Cronable',0),(85,'resources','base_access',10,'Section - Base_Access',0),(86,'resources','ie7',10,'Section - Ie7',0),(87,'resources','crud',10,'Section - CRUD',0),(88,'resources','minimal',10,'Section - Minimal',0),(89,'resources','secondarypractice',10,'Section - SecondaryPractice',0),(90,'resources','masteraccounthistory',10,'Section - MasterAccountHistory',0),(91,'resources','widgetform',10,'Section - WidgetForm',0),(92,'resources','patientpaymentplan',10,'Section - PatientPaymentPlan',0),(93,'resources','appointmentruleset',10,'Section - AppointmentRuleset',0),(94,'resources','codingtemplate',10,'Section - CodingTemplate',0),(95,'resources','patientmerge',10,'Section - PatientMerge',0),(96,'resources','claimhistory',10,'Section - ClaimHistory',0),(97,'resources','auditlog',10,'Section - AuditLog',0),(98,'resources','visitqueue',10,'Section - VisitQueue',0),(99,'resources','medicaleligibility',10,'Section - MediCalEligibility',0),(100,'resources','labs',10,'Section - Labs',0),(101,'resources','labimporter',10,'Section - LabImporter',0),(102,'resources','x12import',10,'Section - X12Import',0),(103,'resources','x12apply',10,'Section - X12Apply',0),(104,'resources','calendaroccurence',10,'Section - CalendarOccurence',0),(105,'resources','calendarajaxevent',10,'Section - CalendarAJAXEvent',0),(106,'resources','calendardisplay',10,'Section - CalendarDisplay',0),(107,'resources','calendarevent',10,'Section - CalendarEvent',0),(108,'resources','personperson',10,'Section - PersonPerson',0),(109,'resources','codecategory',10,'Section - CodeCategory',0);
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_axo_groups`
--


/*!40000 ALTER TABLE `gacl_axo_groups` DISABLE KEYS */;
LOCK TABLES `gacl_axo_groups` WRITE;
INSERT INTO `gacl_axo_groups` VALUES (10,0,1,4,'Root','root'),(11,10,2,3,'All Site Sections','sections');
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_axo_groups` ENABLE KEYS */;

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


/*!40000 ALTER TABLE `gacl_axo_groups_id_seq` DISABLE KEYS */;
LOCK TABLES `gacl_axo_groups_id_seq` WRITE;
INSERT INTO `gacl_axo_groups_id_seq` VALUES (11);
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_axo_groups_map`
--


/*!40000 ALTER TABLE `gacl_axo_groups_map` DISABLE KEYS */;
LOCK TABLES `gacl_axo_groups_map` WRITE;
INSERT INTO `gacl_axo_groups_map` VALUES (24,11);
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_axo_map`
--


/*!40000 ALTER TABLE `gacl_axo_map` DISABLE KEYS */;
LOCK TABLES `gacl_axo_map` WRITE;
INSERT INTO `gacl_axo_map` VALUES (32,'resources','billing'),(32,'resources','claim'),(32,'resources','coding'),(32,'resources','document'),(32,'resources','eob'),(32,'resources','main_billing'),(32,'resources','patient'),(33,'resources','access'),(33,'resources','default'),(33,'resources','docs'),(33,'resources','pdf'),(33,'resources','preferences'),(36,'resources','calendar'),(36,'resources','location'),(36,'resources','main_calendar'),(36,'resources','main_patient'),(36,'resources','patient'),(36,'resources','patientfinder'),(37,'resources','appointment'),(37,'resources','calendar'),(37,'resources','location'),(37,'resources','patient'),(37,'resources','patientfinder'),(38,'resources','appointment'),(38,'resources','calendar'),(38,'resources','location'),(38,'resources','main_calendar'),(38,'resources','patient'),(38,'resources','patientfinder'),(39,'resources','appointment'),(39,'resources','calendar'),(39,'resources','event'),(39,'resources','location'),(39,'resources','main_calendar'),(39,'resources','occurence'),(39,'resources','patient'),(39,'resources','patientfinder'),(39,'resources','schedule'),(40,'resources','admin'),(40,'resources','appointment'),(40,'resources','billing'),(40,'resources','calendar'),(40,'resources','claim'),(40,'resources','coding'),(40,'resources','eob'),(40,'resources','event'),(40,'resources','feeschedule'),(40,'resources','insurance'),(40,'resources','location'),(40,'resources','main_billing'),(40,'resources','main_calendar'),(40,'resources','main_patient'),(40,'resources','occurence'),(40,'resources','patient'),(40,'resources','patientfinder'),(40,'resources','personschedule'),(40,'resources','practice'),(40,'resources','schedule'),(40,'resources','superbill');
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_axo_sections`
--


/*!40000 ALTER TABLE `gacl_axo_sections` DISABLE KEYS */;
LOCK TABLES `gacl_axo_sections` WRITE;
INSERT INTO `gacl_axo_sections` VALUES (0,'resources',10,'Resources',0);
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_axo_sections` ENABLE KEYS */;

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


/*!40000 ALTER TABLE `gacl_axo_sections_seq` DISABLE KEYS */;
LOCK TABLES `gacl_axo_sections_seq` WRITE;
INSERT INTO `gacl_axo_sections_seq` VALUES (32);
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_axo_sections_seq` ENABLE KEYS */;

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


/*!40000 ALTER TABLE `gacl_axo_seq` DISABLE KEYS */;
LOCK TABLES `gacl_axo_seq` WRITE;
INSERT INTO `gacl_axo_seq` VALUES (109);
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_groups_aro_map`
--


/*!40000 ALTER TABLE `gacl_groups_aro_map` DISABLE KEYS */;
LOCK TABLES `gacl_groups_aro_map` WRITE;
INSERT INTO `gacl_groups_aro_map` VALUES (12,15),(20,40),(20,43),(20,44),(20,46),(20,47),(22,48),(24,47),(24,48),(29,44),(31,40),(31,41),(31,42),(31,43);
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_groups_axo_map`
--


/*!40000 ALTER TABLE `gacl_groups_axo_map` DISABLE KEYS */;
LOCK TABLES `gacl_groups_axo_map` WRITE;
INSERT INTO `gacl_groups_axo_map` VALUES (11,0),(11,16),(11,17),(11,18),(11,19),(11,36),(11,37),(11,38),(11,39),(11,40),(11,41),(11,42),(11,43),(11,44),(11,45),(11,46),(11,47),(11,48),(11,49),(11,50),(11,51),(11,52),(11,53),(11,54),(11,55),(11,56),(11,57),(11,58),(11,59),(11,60),(11,61),(11,62),(11,63),(11,64),(11,65),(11,66),(11,67),(11,68),(11,69),(11,70),(11,71),(11,72),(11,73),(11,74),(11,75),(11,76),(11,77),(11,78),(11,79),(11,80),(11,81),(11,82),(11,83),(11,84),(11,85),(11,86),(11,87),(11,88),(11,89),(11,90),(11,91),(11,92),(11,93),(11,94),(11,95),(11,96),(11,97),(11,98),(11,99),(11,100),(11,101),(11,102),(11,103),(11,104),(11,105),(11,106),(11,107),(11,108),(11,109);
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gacl_phpgacl`
--


/*!40000 ALTER TABLE `gacl_phpgacl` DISABLE KEYS */;
LOCK TABLES `gacl_phpgacl` WRITE;
INSERT INTO `gacl_phpgacl` VALUES ('version','3.3.3'),('schema_version','2.1');
UNLOCK TABLES;
/*!40000 ALTER TABLE `gacl_phpgacl` ENABLE KEYS */;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `id` int(11) NOT NULL default '0',
  `control_id` varchar(50) NOT NULL default '',
  `message` longtext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `control_id` (`control_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `identifier_id` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  `identifier` varchar(100) NOT NULL default '',
  `identifier_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`identifier_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `insurance`
--


/*!40000 ALTER TABLE `insurance` DISABLE KEYS */;
LOCK TABLES `insurance` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `insurance` ENABLE KEYS */;

--
-- Table structure for table `insurance_program`
--

DROP TABLE IF EXISTS `insurance_program`;
CREATE TABLE `insurance_program` (
  `insurance_program_id` int(11) NOT NULL default '0',
  `payer_type` int(11) NOT NULL default '0',
  `company_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `fee_schedule_id` int(11) NOT NULL default '0',
  `x12_sender_id` varchar(255) NOT NULL default '',
  `x12_receiver_id` varchar(255) NOT NULL default '',
  `x12_version` varchar(255) NOT NULL default '',
  `address_id` int(11) NOT NULL default '0',
  `funds_source` int(11) NOT NULL default '0',
  PRIMARY KEY  (`insurance_program_id`),
  KEY `fee_schedule_id` (`fee_schedule_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `insured_relationship_id` int(11) NOT NULL default '0',
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
  PRIMARY KEY  (`insured_relationship_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `type` char(2) NOT NULL default '',
  `status` char(2) NOT NULL default '',
  `ordering_provider` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`lab_order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `abnormal_flag` char(2) NOT NULL default '',
  `result_status` char(1) NOT NULL default '',
  `observation_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `producer_id` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`lab_result_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lab_test`
--


/*!40000 ALTER TABLE `lab_test` DISABLE KEYS */;
LOCK TABLES `lab_test` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `lab_test` ENABLE KEYS */;

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
INSERT INTO `menu` VALUES (1,'a',1,'','children',0,'','','main'),(2,'default',1,'','children',10,'Actions','','main'),(3,'default',2,'','children',100,'Add Appointment','javascript:showAddAppointment()','RAW'),(4,'default',2,'','children',200,'Search','Appointment/Search','main'),(5,'default',2,'','children',300,'Filters','javascript:showCalendarFilters()','RAW'),(6,'default',2,'','children',400,'Day','CalendarDisplay/Day','main'),(7,'default',1,'','children',20,'Reports','','main/CalendarDisplay'),(8,'default',1,'','children',30,'Admin','','main'),(9,'default',8,'','children',100,'Schedules','Schedule/list','main'),(10,'default',8,'','children',200,'Templates','AppointmentTemplate/list','main'),(11,'patient',1,'','children',10,'Actions','','main'),(12,'patient',11,'','children',100,'Add Patient','Patient/Add','main'),(13,'patient',11,'','children',200,'Search','PatientFinder/Find','main'),(14,'patient',11,'','children',300,'Dashboard','PatientDashboard/View','main'),(15,'patient',11,'','children',400,'Add Encounter','Encounter/Add','main'),(16,'patient',11,'','children',500,'Documents','Document/List','main'),(17,'patient',1,'','children',20,'Reports','','main/Patient'),(18,'patient',1,'','children',30,'Admin','','main'),(19,'patient',18,'','children',100,'Merge Queue','PatientMerge/List','main'),(20,'billing',1,'','children',10,'Actions','',''),(21,'billing',20,'','children',100,'Claims','Claim/List','main'),(22,'billing',20,'','children',200,'Master Account History','MasterAccountHistory/View','main'),(23,'billing',1,'','children',20,'Reports','','main/Billing'),(24,'billing',1,'','children',30,'Admin','','main'),(25,'billing',24,'','children',100,'Payers','Insurance/List','main'),(26,'billing',24,'','children',200,'Fee Schedules','FeeSchedule/List','main'),(27,'billing',24,'','children',300,'Discount Tables','FeeScheduleDiscount/List','main'),(28,'billing',24,'','children',400,'Superbills','Superbill/List','main'),(29,'billing',24,'','children',500,'Import 835','X12Import/upload','main'),(30,'admin',1,'','children',10,'Calendar','','main'),(31,'admin',30,'','children',100,'Schedules','Schedule/list','main'),(32,'admin',30,'','children',200,'Templates','AppointmentTemplate/list','main'),(33,'admin',1,'','children',20,'Patient','','main'),(34,'admin',33,'','children',100,'Labs','Labs/List','main'),(35,'admin',33,'','children',200,'EMR Plugins','Form/List','main'),(36,'admin',33,'','children',300,'Document Categories','DocumentCategory/List','main'),(37,'admin',1,'','children',30,'Billing','','main'),(38,'admin',37,'','children',100,'Payers','Insurance/List','main'),(39,'admin',37,'','children',200,'Fee Schedules','FeeSchedule/List','main'),(40,'admin',37,'','children',300,'Discount Tables','FeeScheduleDiscount/List','main'),(41,'admin',37,'','children',400,'Superbills','Superbill/List','main'),(42,'admin',37,'','children',500,'Input 835','X12Import/upload','main'),(43,'admin',1,'','children',40,'Setup','','main'),(44,'admin',43,'','children',100,'Facilities','Location/List','main'),(45,'admin',43,'','children',200,'Users','User/List','main'),(46,'admin',43,'','children',300,'Enumerations','Enumeration/List','main'),(47,'admin',43,'','children',400,'ACL Editor','Admin/Acl','main'),(48,'admin',43,'','children',500,'Timed Events','Cronable/List','main'),(49,'admin',1,'','children',50,'Reports/Forms','','main'),(50,'admin',49,'','children',100,'Reports','Report/List','main'),(51,'admin',49,'','children',200,'Forms','Form/List','main'),(52,'admin',49,'','children',300,'Connect Reports','Report/Connect','main'),(53,'admin',49,'','children',400,'Connect Forms','Form/Connect','main'),(54,'all',1,'','children',5000,'Practice','','main'),(55,'all',1,'','children',400,'My Account','','main'),(56,'all',55,'','children',100,'Change Password','MyAccount/Password','main'),(57,'patient',1,'','children',-1,'Encounter Forms','','main/Encounter'),(58,'patient',1,'','children',-1,'Dashboard Forms','','main/Patient'),(59,'patient',1,'','children',-1,'Dashboard Reports','','main/Patient'),(60,'admin',49,'','children',500,'EMR Plugins','WidgetForm/List','main'),(61,'admin',43,'','children',0,'Building','Building/add','main'),(62,'admin',43,'','children',0,'Room','Room/add','main'),(63,'all',55,'','children',1000,'Logout','Access/logout','main'),(64,'admin',37,'','children',500,'Claim Template','CodingTemplate/list','main'),(65,'admin',30,'','children',100,'Appointment Rules','AppointmentRuleset/list','main'),(66,'default',8,'','children',300,'Appointment Rules','AppointmentRuleset/list','main'),(67,'patient',11,'','children',1000,'View Audit Log','AuditLog/list','main'),(68,'default',8,'','children',1000,'Visit Queue Templates','VisitQueue/ListTemplates','main'),(69,'admin',30,'','children',200,'Visit Queue Templates','VisitQueue/ListTemplates','main'),(70,'billing',24,'','children',600,'Claim Template','CodingTemplate/List','main'),(71,'billing',20,'','children',0,'Process Queues','Queue/process','main'),(72,'billing',20,'','children',0,'Process Queues','Queue/process','main'),(73,'admin',37,'','children',900,'Code Categories','CodeCategory/list','main'),(74,'billing',24,'','children',900,'Code Categories','CodeCategory/list','main');
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `menu_report`
--


/*!40000 ALTER TABLE `menu_report` DISABLE KEYS */;
LOCK TABLES `menu_report` WRITE;
INSERT INTO `menu_report` VALUES (810587,23,800614,'Aged Trail Balance',NULL),(810597,23,607649,'Balance Aging',NULL),(810598,23,607652,'Balance Aging By Patient',NULL),(810599,23,800634,'Balance Aging By Payer',NULL),(810600,23,607654,'Balance Aging By Provider',NULL),(817199,17,607877,'Family Patient Statement',NULL),(818831,30,607671,'No-Show Report',NULL),(818832,300819,607685,'Transaction Report',NULL),(818990,17,818961,'Email List',NULL);
UNLOCK TABLES;
/*!40000 ALTER TABLE `menu_report` ENABLE KEYS */;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `id` int(11) NOT NULL default '0',
  `foreign_id` int(11) NOT NULL default '0',
  `note` varchar(255) default NULL,
  `owner` int(11) default NULL,
  `date` datetime default NULL,
  `revision` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `foreign_id` (`owner`),
  KEY `foreign_id_2` (`foreign_id`),
  KEY `date` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `note`
--


/*!40000 ALTER TABLE `note` DISABLE KEYS */;
LOCK TABLES `note` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `note` ENABLE KEYS */;

--
-- Table structure for table `number`
--

DROP TABLE IF EXISTS `number`;
CREATE TABLE `number` (
  `number_id` int(11) NOT NULL default '0',
  `number_type` int(11) NOT NULL default '0',
  `notes` tinytext NOT NULL,
  `number` varchar(100) NOT NULL default '',
  `active` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`number_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='A phone number';

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  KEY `creator_id` (`creator_id`,`owner_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ordo_registry`
--


/*!40000 ALTER TABLE `ordo_registry` DISABLE KEYS */;
LOCK TABLES `ordo_registry` WRITE;
INSERT INTO `ordo_registry` VALUES (800001,1,1),(800002,1,1),(800003,1,1),(800004,1,1),(800005,1,1),(800006,1,1),(800007,1,1),(800008,1,1),(800009,1,1),(800010,1,1),(800011,1,1),(800012,1,1),(800013,1,1),(800014,1,1),(800015,1,1),(800016,1,1),(800017,1,1),(800018,1,1),(800019,1,1),(800020,1,1),(800021,1,1),(800022,1,1),(800023,1,1),(800024,1,1),(800025,1,1),(800026,1,1),(800027,1,1),(800028,1,1),(800029,1,1),(800030,1,1),(800031,1,1),(800032,1,1),(800033,1,1),(800034,1,1),(800035,1,1),(800036,1,1),(800037,1,1),(800038,1,1),(800039,1,1),(800040,1,1),(800041,1,1),(800042,1,1),(800043,1,1),(800044,1,1),(800045,1,1),(800046,1,1),(800047,1,1),(800048,1,1),(800049,1,1),(800050,1,1),(800051,1,1),(800052,1,1),(800053,1,1),(800054,1,1),(800055,1,1),(800056,1,1),(800057,1,1),(800058,1,1),(800059,1,1),(800060,1,1),(800061,1,1),(800062,1,1),(800063,1,1),(800064,1,1),(800065,1,1),(800066,1,1),(800067,1,1),(800068,1,1),(800069,1,1),(800070,1,1),(800071,1,1),(800072,1,1),(800073,1,1),(800074,1,1),(800075,1,1),(800076,1,1),(800077,1,1),(800078,1,1),(800079,1,1),(800080,1,1),(800081,1,1),(800082,1,1),(800083,1,1),(800084,1,1),(800085,1,1),(800086,1,1),(800087,1,1),(800088,1,1),(800089,1,1),(800090,1,1),(800091,1,1),(800092,1,1),(800093,1,1),(800094,1,1),(800095,1,1),(800096,1,1),(800097,1,1),(800098,1,1),(800099,1,1),(800100,1,1),(800101,1,1),(800102,1,1),(800103,1,1),(800104,1,1),(800105,1,1),(800106,1,1),(800107,1,1),(800108,1,1),(800109,1,1),(800110,1,1),(800111,1,1),(800112,1,1),(800113,1,1),(800114,1,1),(800115,1,1),(800116,1,1),(800117,1,1),(800118,1,1),(800119,1,1),(800120,1,1),(800121,1,1),(800122,1,1),(800123,1,1),(800124,1,1),(800125,1,1),(800126,1,1),(800127,1,1),(800128,1,1),(800129,1,1),(800130,1,1),(800131,1,1),(800132,1,1),(800133,1,1),(800134,1,1),(800135,1,1),(800136,1,1),(800137,1,1),(800138,1,1),(800139,1,1),(800140,1,1),(800141,1,1),(800142,1,1),(800143,1,1),(800144,1,1),(800145,1,1),(800146,1,1),(800147,1,1),(800148,1,1),(800149,1,1),(800150,1,1),(800151,1,1),(800152,1,1),(800153,1,1),(800154,1,1),(800155,1,1),(800156,1,1),(800157,1,1),(800158,1,1),(800159,1,1),(800160,1,1),(800161,1,1),(800162,1,1),(800163,1,1),(800164,1,1),(800165,1,1),(800166,1,1),(800167,1,1),(800168,1,1),(800169,1,1),(800170,1,1),(800171,1,1),(800172,1,1),(800173,1,1),(800174,1,1),(800175,1,1),(800176,1,1),(800177,1,1),(800178,1,1),(800179,1,1),(800180,1,1),(800181,1,1),(800182,1,1),(800183,1,1),(800184,1,1),(800185,1,1),(800186,1,1),(800187,1,1),(800188,1,1),(800189,1,1),(800190,1,1),(800191,1,1),(800192,1,1),(800193,1,1),(800194,1,1),(800195,1,1),(800196,1,1),(800197,1,1),(800198,1,1),(800199,1,1),(800200,1,1),(800201,1,1),(800202,1,1),(800203,1,1),(800204,1,1),(800205,1,1),(800206,1,1),(800207,1,1),(800208,1,1),(800209,1,1),(800210,1,1),(800211,1,1),(800212,1,1),(800213,1,1),(800214,1,1),(800215,1,1),(800216,1,1),(800217,1,1),(800218,1,1),(800219,1,1),(800220,1,1),(800221,1,1),(800222,1,1),(800223,1,1),(800224,1,1),(800225,1,1),(800226,1,1),(800227,1,1),(800228,1,1),(800229,1,1),(800230,1,1),(800231,1,1),(800232,1,1),(800233,1,1),(800234,1,1),(800235,1,1),(800236,1,1),(800237,1,1),(800238,1,1),(800239,1,1),(800240,1,1),(800241,1,1),(800242,1,1),(800243,1,1),(800244,1,1),(800245,1,1),(800246,1,1),(800247,1,1),(800248,1,1),(800249,1,1),(800250,1,1),(800251,1,1),(800252,1,1),(800253,1,1),(800254,1,1),(800255,1,1),(800256,1,1),(800257,1,1),(800258,1,1),(800259,1,1);
UNLOCK TABLES;
/*!40000 ALTER TABLE `ordo_registry` ENABLE KEYS */;

--
-- Table structure for table `patient`
--

DROP TABLE IF EXISTS `patient`;
CREATE TABLE `patient` (
  `person_id` int(11) NOT NULL default '0',
  `is_default_provider_primary` int(11) NOT NULL default '0',
  `default_provider` int(11) NOT NULL default '0',
  `record_number` int(11) NOT NULL default '0',
  `employer_name` varchar(255) NOT NULL default '',
  `confidentiality` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`),
  KEY `record_number` (`record_number`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='An patient extends the person entity';

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `patient_note_id` int(11) NOT NULL default '0',
  `patient_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `priority` int(11) NOT NULL default '0',
  `note_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `note` text NOT NULL,
  `deprecated` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`patient_note_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `patient_statistics`
--


/*!40000 ALTER TABLE `patient_statistics` DISABLE KEYS */;
LOCK TABLES `patient_statistics` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `patient_statistics` ENABLE KEYS */;

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL default '0',
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
  KEY `foreign_id` (`foreign_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `payment_claimline_id` int(11) NOT NULL default '0',
  `payment_id` int(11) NOT NULL default '0',
  `code_id` int(11) NOT NULL default '0',
  `paid` float(7,2) NOT NULL default '0.00',
  `writeoff` float(7,2) NOT NULL default '0.00',
  `carry` float(7,2) NOT NULL default '0.00',
  PRIMARY KEY  (`payment_claimline_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `identifier` varchar(100) NOT NULL default '',
  `identifier_type` int(11) NOT NULL default '0',
  `marital_status` int(11) NOT NULL default '0',
  `inactive` int(1) NOT NULL default '0',
  `primary_practice_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`),
  KEY `primary_practice_id` (`primary_practice_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='A person in the system';

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
  KEY `address_id` (`address_id`),
  KEY `person_id` (`person_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Links a person to a address specifying the address type';

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Links a person to a company and optionaly specifies the lin';

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
  KEY `person_id` (`person_id`),
  KEY `phone_id` (`number_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Links between people and phone_numbers';

--
-- Dumping data for table `person_number`
--


/*!40000 ALTER TABLE `person_number` DISABLE KEYS */;
LOCK TABLES `person_number` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `person_number` ENABLE KEYS */;

--
-- Table structure for table `person_person`
--

DROP TABLE IF EXISTS `person_person`;
CREATE TABLE `person_person` (
  `person_person_id` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  `related_person_id` int(11) NOT NULL default '0',
  `relation_type` int(11) NOT NULL default '0',
  `guarantor` tinyint(1) NOT NULL default '0',
  `guarantor_priority` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_person_id`),
  UNIQUE KEY `person_id` (`person_id`,`related_person_id`,`relation_type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Link to specify person type';

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
  KEY `address_id` (`address_id`),
  KEY `practice_id` (`practice_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Links a practice to a address specifying the address type';

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Links between people and phone_numbers';

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
  `practice_setting_id` int(11) NOT NULL default '0',
  `practice_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `value` text NOT NULL,
  `serialized` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`practice_setting_id`),
  UNIQUE KEY `practice_id` (`practice_id`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `website` varchar(255) NOT NULL default '',
  `identifier` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `preferences`
--


/*!40000 ALTER TABLE `preferences` DISABLE KEYS */;
LOCK TABLES `preferences` WRITE;
INSERT INTO `preferences` VALUES (9000,'Defaults','',0,1,1);
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `provider_to_insurance_id` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  `insurance_program_id` int(11) NOT NULL default '0',
  `provider_number` varchar(100) NOT NULL default '',
  `provider_number_type` int(11) NOT NULL default '0',
  `group_number` varchar(100) NOT NULL default '',
  `building_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`provider_to_insurance_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `record_sequence`
--


/*!40000 ALTER TABLE `record_sequence` DISABLE KEYS */;
LOCK TABLES `record_sequence` WRITE;
INSERT INTO `record_sequence` VALUES (10006);
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
  PRIMARY KEY  (`recurrence_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `pattern_type` enum('day','monthday','monthweek','yearmonthday','yearmonthweek') NOT NULL default 'day',
  `number` int(11) default NULL,
  `weekday` enum('1','2','3','4','5','6','7') default NULL,
  `month` enum('01','02','03','04','05','06','07','08','09','10','11','12') default NULL,
  `monthday` tinyint(2) default NULL,
  `week_of_month` enum('First','Second','Third','Fourth','Last') default NULL,
  PRIMARY KEY  (`recurrence_pattern_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `recurrence_pattern`
--


/*!40000 ALTER TABLE `recurrence_pattern` DISABLE KEYS */;
LOCK TABLES `recurrence_pattern` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `recurrence_pattern` ENABLE KEYS */;

--
-- Table structure for table `relationship`
--

DROP TABLE IF EXISTS `relationship`;
CREATE TABLE `relationship` (
  `relationship_id` int(11) NOT NULL auto_increment,
  `parent_type` varchar(255) NOT NULL default '',
  `parent_id` int(11) NOT NULL default '0',
  `child_type` varchar(255) NOT NULL default '',
  `child_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`relationship_id`),
  KEY `index` (`parent_type`,`parent_id`,`child_type`,`child_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Report templates';

--
-- Dumping data for table `report_templates`
--


/*!40000 ALTER TABLE `report_templates` DISABLE KEYS */;
LOCK TABLES `report_templates` WRITE;
INSERT INTO `report_templates` VALUES (607624,607623,'Default Template','yes',100005,''),(607626,607625,'Default Template','yes',100000,''),(607627,607623,'4701 template','no',100001,''),(607630,607629,'Default Template','yes',100005,''),(607631,607629,'4701 Template','no',100002,''),(607639,607638,'Default Template','yes',100000,''),(607641,607640,'Default Template','yes',100002,''),(607645,607638,'New Patient','no',100001,''),(607647,607646,'Default Template','yes',10002,''),(607649,607648,'Default Template','yes',100003,''),(607652,607651,'Default Template','yes',10002,''),(607654,607653,'Default Template','yes',10002,''),(607656,607655,'Default Template','yes',10003,''),(607658,607657,'Default Template','yes',10012,''),(607660,607659,'Default Template','yes',100005,''),(607662,607661,'Default Template','yes',10004,''),(607664,607663,'Default Template','yes',100000,''),(607666,607663,'Exit Report','no',100001,''),(607669,607668,'Default Template','yes',100002,''),(607671,607670,'Default Template','yes',10056,''),(607673,607672,'Default Template','yes',100000,''),(607674,607672,'Open Encounter Report','no',100000,''),(607675,607672,'','no',100000,''),(607677,607676,'Default Template','yes',10000,''),(607679,607678,'Default Template','yes',10051,''),(607681,607680,'Default Template','yes',10012,''),(607683,607682,'Default Template','yes',10003,''),(607685,607684,'Default Template','yes',100009,''),(607686,607684,'Transaction Report','no',100003,''),(607687,607684,'','no',100000,''),(607873,607872,'Default Template','yes',10003,''),(607877,607876,'Default Template','yes',100007,''),(607883,607882,'Default Template','yes',100012,''),(800490,621489,'Default Template','yes',10000,''),(800566,800557,'Default Template','yes',100006,''),(800604,800595,'Default Template','yes',100001,''),(800614,800605,'Default Template','yes',10004,''),(800624,800615,'Default Template','yes',10001,''),(800634,800625,'Default Template','yes',100001,''),(808304,808295,'Default Template','yes',100002,''),(808314,808305,'Default Template','yes',100000,''),(808324,808315,'Default Template','yes',100000,''),(810581,810572,'Default Template','yes',100001,''),(810582,810572,'Appointment Reminder','no',100002,''),(810583,810572,'','no',100000,'ApptReminderTpl'),(818961,818952,'Default Template','yes',100004,''),(827079,827070,'Default Template','yes',10000,'');
UNLOCK TABLES;
/*!40000 ALTER TABLE `report_templates` ENABLE KEYS */;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
CREATE TABLE `reports` (
  `id` int(11) NOT NULL auto_increment,
  `dbase` varchar(255) NOT NULL default '',
  `user` varchar(255) NOT NULL default '',
  `label` varchar(255) NOT NULL default '',
  `query` longtext NOT NULL,
  `description` mediumtext NOT NULL,
  `custom_id` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Report definitions TODO: change to Generic Seq';

--
-- Dumping data for table `reports`
--


/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
LOCK TABLES `reports` WRITE;
INSERT INTO `reports` VALUES (607648,'','','Balance Aging','SELECT \r\n	{link:controller=Encounter&action=edit&columnName=patient.encounter_id&id=patient.encounter_id} AS \'Encounter ID\',\r\n	{link:controller=PatientDashboard&action=view&columnName=patient.patient&id=patient.patient_id} AS \'Patient\',\r\n	patient.record_num `Patient #`,\r\n	patient.Payer,\r\n	IFNULL(sum(current.total_balance),0) as `Current`,\r\n	IFNULL(sum(30day.total_balance),0) as `30 Day`,\r\n	IFNULL(sum(60day.total_balance),0) as `60 Day`,\r\n	IFNULL(sum(90day.total_balance),0) as `90 Day`,\r\n	IFNULL(sum(120day.total_balance),0) as `120 Day`\r\nfrom \r\n(\r\n	SELECT \r\n		patient_id,\r\n		pers.person_id,\r\n		ip.company_id payer_id, \r\n		ip.name as `Payer`,\r\n		pat.record_number as record_num,\r\n		e.encounter_id,\r\n		CONCAT(pers.first_name,\" \",pers.last_name)as `Patient`\r\n	FROM encounter as e\r\n	INNER JOIN clearhealth_claim AS cc USING(encounter_id)\r\n	INNER JOIN storage_int as si on cc.encounter_id = si.foreign_key\r\n	INNER JOIN insurance_program ip on ip.insurance_program_id = si.value\r\n	INNER JOIN person as pers on e.patient_id = pers.person_id\r\n	INNER JOIN patient as pat on e.patient_id = pat.person_id\r\n)\r\npatient\r\nLEFT JOIN (	\r\n	SELECT patient_id,e.encounter_id,e.encounter_id,\r\n	(IFNULL(SUM(total_billed),0) - (IFNULL(SUM(total_paid),0) + IFNULL(SUM(writeoffs.writeoff),0))) AS total_balance\r\n	FROM encounter as e\r\n	INNER JOIN clearhealth_claim AS cc USING(encounter_id)\r\n	INNER JOIN storage_int as si on cc.encounter_id = si.foreign_key\r\n	INNER JOIN insurance_program ip on ip.insurance_program_id = si.value\r\n	INNER JOIN person as pers on e.patient_id = pers.person_id\r\n	LEFT JOIN (\r\n	SELECT\r\n		foreign_id,\r\n		IFNULL(SUM(writeoff),0) AS writeoff\r\n	FROM\r\n		payment \r\n	WHERE\r\n		encounter_id = 0\r\n	GROUP BY\r\n		foreign_id\r\n	) AS writeoffs ON(writeoffs.foreign_id = cc.claim_id)\r\n	WHERE \r\n		si.value_key = \'current_payer\'\r\n	AND\r\n		DATE_SUB(CURDATE(),INTERVAL 30 DAY) <= e.date_of_treatment\r\n\r\n	GROUP BY e.patient_id,cc.identifier, e.encounter_id\r\n\r\n)as `current` on current.patient_id = patient.person_id and current.encounter_id = patient.encounter_id\r\nLEFT JOIN (	\r\n	SELECT patient_id,e.encounter_id,e.encounter_id,\r\n	(IFNULL(SUM(total_billed),0) - (IFNULL(SUM(total_paid),0) + IFNULL(SUM(writeoffs.writeoff),0))) AS total_balance\r\n	FROM encounter as e\r\n	INNER JOIN clearhealth_claim AS cc USING(encounter_id)\r\n	INNER JOIN storage_int as si on cc.encounter_id = si.foreign_key\r\n	INNER JOIN insurance_program ip on ip.insurance_program_id = si.value\r\n	INNER JOIN person as pers on e.patient_id = pers.person_id\r\n	LEFT JOIN (\r\n	SELECT\r\n		foreign_id,\r\n		IFNULL(SUM(writeoff),0) AS writeoff\r\n	FROM\r\n		payment \r\n	WHERE\r\n		encounter_id = 0\r\n	GROUP BY\r\n		foreign_id\r\n	) AS writeoffs ON(writeoffs.foreign_id = cc.claim_id)\r\n	WHERE \r\n		si.value_key = \'current_payer\'\r\n	AND\r\n		DATE_SUB(CURDATE(),INTERVAL 30 DAY) > e.date_of_treatment\r\n	AND\r\n		DATE_SUB(CURDATE(),INTERVAL 60 DAY) <= e.date_of_treatment\r\n\r\n	GROUP BY e.patient_id,cc.identifier, e.encounter_id\r\n	\r\n)as `30day`  ON patient.person_id = 30day.patient_id and patient.encounter_id = 30day.encounter_id\r\n\r\nLEFT JOIN \r\n(\r\nSELECT patient_id,e.encounter_id,\r\n	(IFNULL(SUM(total_billed),0) - (IFNULL(SUM(total_paid),0) + IFNULL(SUM(writeoffs.writeoff),0))) AS total_balance\r\n	FROM encounter as e\r\n	INNER JOIN clearhealth_claim AS cc USING(encounter_id)\r\n	INNER JOIN storage_int as si on cc.encounter_id = si.foreign_key\r\n	INNER JOIN insurance_program ip on ip.insurance_program_id = si.value\r\n	INNER JOIN person as pers on e.patient_id = pers.person_id\r\n	LEFT JOIN (\r\n	SELECT\r\n		foreign_id,\r\n		IFNULL(SUM(writeoff),0) AS writeoff\r\n	FROM\r\n		payment \r\n	WHERE\r\n		encounter_id = 0\r\n	GROUP BY\r\n		foreign_id\r\n	) AS writeoffs ON(writeoffs.foreign_id = cc.claim_id)\r\n	WHERE \r\n		si.value_key = \'current_payer\'\r\n	AND\r\n		DATE_SUB(CURDATE(),INTERVAL 60 DAY) > e.date_of_treatment\r\n	AND\r\n		DATE_SUB(CURDATE(),INTERVAL 90 DAY) <= e.date_of_treatment\r\n\r\n	GROUP BY e.patient_id,cc.identifier, e.encounter_id\r\n\r\n) as `60day` ON patient.person_id = 60day.patient_id and patient.encounter_id = 60day.encounter_id\r\nLEFT JOIN \r\n(\r\nSELECT patient_id, e.encounter_id,\r\n	(IFNULL(SUM(total_billed),0) - (IFNULL(SUM(total_paid),0) + IFNULL(SUM(writeoffs.writeoff),0))) AS total_balance\r\n	FROM encounter as e\r\n	INNER JOIN clearhealth_claim AS cc USING(encounter_id)\r\n	INNER JOIN storage_int as si on cc.encounter_id = si.foreign_key\r\n	INNER JOIN insurance_program ip on ip.insurance_program_id = si.value\r\n	INNER JOIN person as pers on e.patient_id = pers.person_id\r\n	LEFT JOIN (\r\n	SELECT\r\n		foreign_id,\r\n		IFNULL(SUM(writeoff),0) AS writeoff\r\n	FROM\r\n		payment \r\n	WHERE\r\n		encounter_id = 0\r\n	GROUP BY\r\n		foreign_id\r\n	) AS writeoffs ON(writeoffs.foreign_id = cc.claim_id)\r\n	WHERE \r\n		si.value_key = \'current_payer\'\r\n	AND\r\n		DATE_SUB(CURDATE(),INTERVAL 90 DAY) > e.date_of_treatment\r\n	AND\r\n		DATE_SUB(CURDATE(),INTERVAL 120 DAY) <= e.date_of_treatment\r\n\r\n	GROUP BY e.patient_id,cc.identifier, e.encounter_id\r\n\r\n) as `90day` ON patient.person_id = 90day.patient_id and patient.encounter_id = 90day.encounter_id\r\nLEFT JOIN \r\n(\r\nSELECT patient_id,e.encounter_id,\r\n	(IFNULL(SUM(total_billed),0) - (IFNULL(SUM(total_paid),0) + IFNULL(SUM(writeoffs.writeoff),0))) AS total_balance\r\n	FROM encounter as e\r\n	INNER JOIN clearhealth_claim AS cc USING(encounter_id)\r\n	INNER JOIN storage_int as si on cc.encounter_id = si.foreign_key\r\n	INNER JOIN insurance_program ip on ip.insurance_program_id = si.value\r\n	INNER JOIN person as pers on e.patient_id = pers.person_id\r\n	LEFT JOIN (\r\n	SELECT\r\n		foreign_id,\r\n		IFNULL(SUM(writeoff),0) AS writeoff\r\n	FROM\r\n		payment \r\n	WHERE\r\n		encounter_id = 0\r\n	GROUP BY\r\n		foreign_id\r\n	) AS writeoffs ON(writeoffs.foreign_id = cc.claim_id)\r\n	WHERE \r\n		si.value_key = \'current_payer\'\r\n	AND\r\n		DATE_SUB(CURDATE(),INTERVAL 120 DAY) >e.date_of_treatment\r\n	\r\n	GROUP BY e.patient_id,cc.identifier, e.encounter_id\r\n\r\n) as `120day` ON patient.person_id = 120day.patient_id and patient.encounter_id = 120day.encounter_id\r\n/* end from */\r\nWHERE \r\n(\r\n	current.total_balance <> 0\r\nOR\r\n	30day.total_balance <> 0\r\nOR\r\n	60day.total_balance <> 0\r\nOR\r\n	90day.total_balance <> 0\r\nOR\r\n	120day.total_balance <> 0\r\n) and \r\n(\r\n	1=1 and if(LENGTH(\'[payer]\') > 0, patient.payer_id = \'[payer:query:select ip.insurance_program_id, concat(c.name,\'->\',ip.name) name from company c inner join insurance_program ip on c.company_id = ip.company_id order by c.name, ip.name]\',1)\r\n)\r\n\r\nGROUP BY\r\n	patient.person_id, patient.encounter_id \r\nORDER BY\r\n	patient, payer\r\n','Balance Aging','balanceaging'),(607651,'','','Balance Aging By Patient','---[by_payer]---\r\nSELECT\r\n	CONCAT(per.last_name, \', \', per.first_name) AS \'patient_name\',\r\n	pat.record_number,\r\n	CONCAT(c.name, \' > \', ip.name) AS payer,\r\n	(\r\n		SUM(CASE WHEN e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN total_billed ELSE 0 END) - \r\n		(\r\n			SUM(CASE WHEN e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN total_paid ELSE 0 END) +\r\n			SUM(CASE WHEN e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ELSE 0 END)\r\n		)\r\n	) AS `current`,\r\n	(\r\n		SUM(CASE WHEN \r\n			e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND\r\n			e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 31 DAY)\r\n			THEN total_billed ELSE 0 END) - \r\n		(\r\n			SUM(CASE WHEN\r\n				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND\r\n				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 31 DAY)\r\n				THEN total_paid ELSE 0 END) +\r\n			SUM(CASE WHEN \r\n				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND\r\n				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 31 DAY)\r\n				THEN IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ELSE 0 END)\r\n		)\r\n	) AS `31 - 60`,\r\n	(\r\n		SUM(CASE WHEN \r\n			e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 90 DAY) AND\r\n			e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 61 DAY)\r\n			THEN total_billed ELSE 0 END) - \r\n		(\r\n			SUM(CASE WHEN\r\n				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 90 DAY) AND\r\n				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 61 DAY)\r\n				THEN total_paid ELSE 0 END) +\r\n			SUM(CASE WHEN \r\n				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 90 DAY) AND\r\n				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 61 DAY)\r\n				THEN IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ELSE 0 END)\r\n		)\r\n	) AS `61 - 90`,\r\n	(\r\n		SUM(CASE WHEN \r\n			e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 120 DAY) AND\r\n			e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 91 DAY)\r\n			THEN total_billed ELSE 0 END) - \r\n		(\r\n			SUM(CASE WHEN\r\n				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 120 DAY) AND\r\n				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 91 DAY)\r\n				THEN total_paid ELSE 0 END) +\r\n			SUM(CASE WHEN \r\n				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 120 DAY) AND\r\n				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 91 DAY)\r\n				THEN IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ELSE 0 END)\r\n		)\r\n	) AS `91 - 120`,\r\n	(\r\n		SUM(CASE WHEN e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 121 DAY) THEN total_billed ELSE 0 END) - \r\n		(\r\n			SUM(CASE WHEN e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 121 DAY) THEN total_paid ELSE 0 END) +\r\n			SUM(CASE WHEN e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 121 DAY) THEN IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ELSE 0 END)\r\n		)\r\n	) AS `121+`\r\nFROM\r\n	person AS per\r\n	INNER JOIN patient AS pat USING(person_id)\r\n	INNER JOIN encounter AS e ON(pat.person_id = e.patient_id)\r\n	INNER JOIN clearhealth_claim AS cc USING(encounter_id)\r\n	LEFT JOIN (\r\n		SELECT\r\n			foreign_id,\r\n			SUM(writeoff) AS writeoff\r\n		FROM\r\n			payment \r\n		WHERE\r\n			encounter_id = 0\r\n		GROUP BY\r\n			foreign_id\r\n	) AS writeoffs ON(writeoffs.foreign_id = cc.claim_id)\r\n	INNER JOIN storage_int AS current_payer ON (current_payer.foreign_key = e.encounter_id AND current_payer.value_key = \"current_payer\")\r\n	INNER JOIN insurance_program AS ip ON(current_payer.value = ip.insurance_program_id)\r\n	INNER JOIN company AS c USING(company_id)\r\nGROUP BY\r\n	per.person_id,\r\n	payer\r\nORDER BY\r\n	per.last_name\r\n\r\n---[balance_totals,hideFilter,noPager]---\r\nSELECT\r\n	CONCAT(per.last_name, \', \', per.first_name) AS \'patient_name\',\r\n	pat.record_number,\r\n	(SUM(total_billed) - (SUM(total_paid) + IF(writeoffs.writeoff IS NULL, 0.00, SUM(writeoffs.writeoff)))) AS total_balance\r\nFROM\r\n	person AS per\r\n	INNER JOIN patient AS pat USING(person_id)\r\n	INNER JOIN encounter AS e ON(pat.person_id = e.patient_id)\r\n	INNER JOIN clearhealth_claim AS cc USING(encounter_id)\r\n	LEFT JOIN (\r\n		SELECT\r\n			foreign_id,\r\n			SUM(writeoff) AS writeoff\r\n		FROM\r\n			payment \r\n		WHERE\r\n			encounter_id = 0\r\n		GROUP BY\r\n			foreign_id\r\n	) AS writeoffs ON(writeoffs.foreign_id = cc.claim_id)\r\n	INNER JOIN storage_int AS current_payer ON (current_payer.foreign_key = e.encounter_id AND current_payer.value_key = \"current_payer\")\r\n	INNER JOIN insurance_program AS ip ON(current_payer.value = ip.insurance_program_id)\r\n	INNER JOIN company AS c USING(company_id)\r\nGROUP BY\r\n	per.person_id\r\nORDER BY\r\n	per.last_name\r\n\r\n','Balance Aging By Patient','balanceagingbypatient'),(607653,'','','Balance Aging By Provider','---[Provider_Balances]---\r\nSELECT\r\n	CONCAT(per.last_name, \', \', per.first_name) AS \'patient_name\',\r\n	CONCAT(c.name, \' > \', ip.name) AS payer,\r\n	(\r\n		SUM(CASE WHEN e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN total_billed ELSE 0 END) - \r\n		(\r\n			SUM(CASE WHEN e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN total_paid ELSE 0 END) +\r\n			SUM(CASE WHEN e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ELSE 0 END)\r\n		)\r\n	) AS `current`,\r\n	(\r\n		SUM(CASE WHEN \r\n			e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND\r\n			e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 31 DAY)\r\n			THEN total_billed ELSE 0 END) - \r\n		(\r\n			SUM(CASE WHEN\r\n				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND\r\n				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 31 DAY)\r\n				THEN total_paid ELSE 0 END) +\r\n			SUM(CASE WHEN \r\n				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND\r\n				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 31 DAY)\r\n				THEN IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ELSE 0 END)\r\n		)\r\n	) AS `31 - 60`,\r\n	(\r\n		SUM(CASE WHEN \r\n			e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 90 DAY) AND\r\n			e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 61 DAY)\r\n			THEN total_billed ELSE 0 END) - \r\n		(\r\n			SUM(CASE WHEN\r\n				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 90 DAY) AND\r\n				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 61 DAY)\r\n				THEN total_paid ELSE 0 END) +\r\n			SUM(CASE WHEN \r\n				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 90 DAY) AND\r\n				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 61 DAY)\r\n				THEN IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ELSE 0 END)\r\n		)\r\n	) AS `61 - 90`,\r\n	(\r\n		SUM(CASE WHEN \r\n			e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 120 DAY) AND\r\n			e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 91 DAY)\r\n			THEN total_billed ELSE 0 END) - \r\n		(\r\n			SUM(CASE WHEN\r\n				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 120 DAY) AND\r\n				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 91 DAY)\r\n				THEN total_paid ELSE 0 END) +\r\n			SUM(CASE WHEN \r\n				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 120 DAY) AND\r\n				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 91 DAY)\r\n				THEN IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ELSE 0 END)\r\n		)\r\n	) AS `91 - 120`,\r\n	(\r\n		SUM(CASE WHEN e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 121 DAY) THEN total_billed ELSE 0 END) - \r\n		(\r\n			SUM(CASE WHEN e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 121 DAY) THEN total_paid ELSE 0 END) +\r\n			SUM(CASE WHEN e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 121 DAY) THEN IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ELSE 0 END)\r\n		)\r\n	) AS `121+`,\r\n	SUM(total_billed) - (SUM(total_paid) + SUM(IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff))) AS `totals`\r\nFROM\r\n	person AS per\r\n	INNER JOIN provider AS pro USING(person_id)\r\n	INNER JOIN encounter AS e ON(pro.person_id = e.treating_person_id)\r\n	INNER JOIN clearhealth_claim AS cc USING(encounter_id)\r\n	LEFT JOIN (\r\n		SELECT\r\n			foreign_id,\r\n			SUM(writeoff) AS writeoff\r\n		FROM\r\n			payment \r\n		WHERE\r\n			encounter_id = 0\r\n		GROUP BY\r\n			foreign_id\r\n	) AS writeoffs ON(writeoffs.foreign_id = cc.claim_id)\r\n	INNER JOIN storage_int AS current_payer ON (current_payer.foreign_key = e.encounter_id AND current_payer.value_key = \"current_payer\")\r\n	INNER JOIN insurance_program AS ip ON(current_payer.value = ip.insurance_program_id)\r\n	INNER JOIN company AS c USING(company_id)\r\nGROUP BY\r\n	per.person_id\r\nORDER BY\r\n	per.last_name\r\n\r\n','Balance Aging By Provider','balanceagingbyprovider'),(607655,'','','Census','---[census]---\r\nselect\r\n concat(p.last_name,\', \',p.first_name) Patient,\r\n e.encounter_reason Reason,\r\n if (o.walkin,\'Y\',\'N\') `Walk-in?`,\r\n date_format(o.start,\'%a %m/%d/%Y\') `Date`,\r\n concat_ws(\' to \',date_format(o.start,\'%H:%i\'),date_format(o.end,\'%H:%i\')) `Time`,\r\n concat_ws(\', \',pro.last_name,pro.first_name) Provider,\r\n b.name Facility\r\nfrom\r\n occurences o\r\n inner join person p on o.external_id = p.person_id\r\n inner join encounter e on e.occurence_id = o.id\r\n left join person pro on e.treating_person_id = pro.person_id\r\n left join buildings b on e.building_id = b.id\r\nwhere\r\n if (\'[after]\',o.start > \'[after:date]\',1) and\r\n if (\'[before]\',o.end < \'[before:date]\',1) and\r\n if (\'[facility]\',e.building_id = \'[facility:query:select b.id, b.name from buildings b order by b.name]\',1) and\r\n if (\'[provider]\',o.user_id = \'[provider:query:select u.user_id, concat(p.last_name,\', \',p.first_name) name from user inner join person p on u.person_id = p.person_id inner join provider pro using(person_id)]\',1)\r\norder by\r\n `Date` DESC, `Time` DESC\r\n/***\r\ndsFilters-Reason|enumLookup&ds|encounter_reason\r\n***/\r\n---[total_encounters,hideFilter,noPager]---\r\nselect\r\n count(e.encounter_id) `Total Encounters`\r\nfrom\r\n encounter e \r\n left join occurences o on e.occurence_id = o.id\r\n left join person p on o.external_id = p.person_id\r\nwhere\r\n if (\'[after]\',e.date_of_treatment >= \'[after:date]\',1) and\r\n if (\'[before]\',e.date_of_treatment <= \'[before:date]\',1) and\r\n if (\'[facility]\',e.building_id = \'[facility]\',1) and\r\n if (\'[provider]\',o.user_id = \'[provider]\',1)\r\n---[total_encounters_by_reason,hideFilter,noPager]---\r\nselect\r\n e.encounter_reason `Reason`,\r\n count(e.encounter_id) `Total`\r\nfrom\r\n occurences o\r\n inner join person p on o.external_id = p.person_id\r\n inner join encounter e on e.occurence_id = o.id\r\nwhere\r\n if (\'[after]\',o.start > \'[after:date]\',1) and\r\n if (\'[before]\',o.end < \'[before:date]\',1) and\r\n if (\'[facility]\',e.building_id = \'[facility]\',1) and\r\n if (\'[provider]\',o.user_id = \'[provider]\',1)\r\ngroup by\r\n o.reason_code\r\n/***\r\ndsFilters-Reason|enumLookup&ds|encounter_reason\r\n***/\r\n---[total_encounters_by_walkin,hideFilter,noPager]---\r\nselect\r\n if (o.walkin,\'Y\',\'N\') `Walk-in?`,\r\n count(e.encounter_id) `Total`\r\nfrom\r\n occurences o\r\n inner join person p on o.external_id = p.person_id\r\n inner join encounter e on e.occurence_id = o.id\r\nwhere\r\n if (\'[after]\',o.start > \'[after:date]\',1) and\r\n if (\'[before]\',o.end < \'[before:date]\',1) and\r\n if (\'[facility]\',e.building_id = \'[facility]\',1) and\r\n if (\'[provider]\',o.user_id = \'[provider]\',1)\r\ngroup by\r\n o.walkin\r\n---[total_encounters_by_facility,hideFilter,noPager]---\r\nselect\r\n b.name Facility,\r\n count(e.encounter_id) total\r\nfrom\r\n occurences o\r\n inner join person p on o.external_id = p.person_id\r\n inner join encounter e on e.occurence_id = o.id\r\n inner join buildings b on e.building_id = b.id\r\nwhere\r\n if (\'[after]\',o.start > \'[after:date]\',1) and\r\n if (\'[before]\',o.end < \'[before:date]\',1) and\r\n if (\'[facility]\',e.building_id = \'[facility]\',1) and\r\n if (\'[provider]\',o.user_id = \'[provider]\',1)\r\ngroup by\r\n b.id\r\n---[total_encounters_by_provider,hideFilter,noPager]---\r\nselect\r\n concat_ws(\', \',pro.last_name,pro.first_name) Provider,\r\n count(e.encounter_id) total\r\nfrom\r\n occurences o\r\n inner join person p on o.external_id = p.person_id\r\n inner join encounter e on e.occurence_id = o.id\r\n inner join person pro on e.treating_person_id = pro.person_id\r\nwhere\r\n if (\'[after]\',o.start > \'[after:date]\',1) and\r\n if (\'[before]\',o.end < \'[before:date]\',1) and\r\n if (\'[facility]\',e.building_id = \'[facility]\',1) and\r\n if (\'[provider]\',o.user_id = \'[provider]\',1)\r\ngroup by\r\n pro.person_id\r\n','Census','census'),(607657,'','','Employer Detail Report','---[employer_detail,hideFilter]---\r\nSELECT\r\n {link:controller=PatientDashboard&action=view&columnName=CONCAT(first_name,\' \',last_name)&id=p.person_id} AS \'Name\',\r\n n.number AS \'Phone\'\r\nFROM\r\n patient AS p\r\n INNER JOIN person AS e USING(person_id)\r\n INNER JOIN person_address AS pa USING(person_id)\r\n INNER JOIN address AS a USING(address_id)\r\n LEFT JOIN person_number AS pn ON (pa.person_id = pn.person_id)\r\n LEFT JOIN number AS n ON (pn.number_id = n.number_id AND n.number_type = 1)\r\nWHERE\r\n a.name = \'[employer_name]\'\r\nGROUP BY\r\n p.person_id\r\nORDER BY\r\n first_name','Employer Detail Report','empdetailreport'),(607659,'','','Employer Report','SELECT\r\n CONCAT(\"<a href=\\\"\", \"{url:controller=Report&action=viewByCID}\", \"cid=empdetailreport&rf%5bemployer_name%5d=\", a.name, \"\\\">\", a.name, \"</a>\") AS \'Employer\',\r\n COUNT(p.person_id) AS \'# Patients\'\r\nFROM\r\n patient AS p\r\n INNER JOIN person_address AS pa USING(person_id)\r\n INNER JOIN address AS a USING(address_id)\r\nWHERE\r\n pa.address_type IN (\r\n   SELECT\r\n    ev.key\r\n   FROM\r\n    enumeration_value AS ev\r\n   WHERE\r\n    ev.value = \'Employer\')\r\nGROUP BY\r\n a.name','Employer Report','empreport'),(607661,'','','Encounter Census','---[total_encounters,noPager]---\r\nSELECT\r\n COUNT(e.encounter_id) `Total Encounters`\r\nFROM\r\n encounter e \r\n LEFT JOIN occurences AS o ON(e.occurence_id = o.id)\r\nWHERE\r\n IF (\'[after]\',e.date_of_treatment >= \'[after:date]\',1) AND\r\n IF (\'[before]\',e.date_of_treatment <= \'[before:date]\',1) AND\r\n IF (\'[facility]\',e.building_id = \'[facility:query:SELECT id, name FROM buildings ORDER BY name]\',1) AND\r\n IF (\'[provider]\',e.treating_person_id = \'[provider:query:SELECT prov.person_id, CONCAT(per.first_name, \" \", last_name) FROM provider AS prov JOIN person AS per USING(person_id)]\',1) AND\r\n IF (\'[reason]\',e.encounter_reason = \'[reason:enum:encounter_reason]\',1)\r\n---[total_encounters_by_reason,hideFilter,noPager]---\r\nSELECT\r\n e.encounter_reason `Reason`,\r\n count(e.encounter_id) `Total`\r\nFROM\r\n encounter AS e\r\n LEFT JOIN occurences AS o ON(e.occurence_id = o.id)\r\nWHERE\r\n IF (\'[after]\',e.date_of_treatment >= \'[after:date]\',1) AND\r\n IF (\'[before]\',e.date_of_treatment <= \'[before:date]\',1) AND\r\n IF (\'[facility]\',e.building_id = \'[facility:query:SELECT id, name FROM buildings ORDER BY name]\',1) AND\r\n IF (\'[provider]\',e.treating_person_id = \'[provider:query:SELECT prov.person_id, CONCAT(per.first_name, \" \", last_name) FROM provider AS prov JOIN person AS per USING(person_id)]\',1) AND\r\n IF (\'[reason]\',e.encounter_reason = \'[reason:enum:encounter_reason]\',1)\r\nGROUP BY\r\n e.encounter_reason\r\n/***\r\ndsFilters-Reason|enumLookup&ds|encounter_reason\r\n***/\r\n---[total_encounters_by_walkin,hideFilter,noPager]---\r\nSELECT\r\n IF (o.walkin,\'Y\',\'N\') `Walk-in?`,\r\n count(e.encounter_id) `Total`\r\nFROM\r\n encounter e\r\n INNER JOIN person AS p on e.patient_id = p.person_id\r\n LEFT JOIN occurences AS o on (e.occurence_id = o.id)\r\nWHERE\r\n IF (\'[after]\',e.date_of_treatment >= \'[after:date]\',1) AND\r\n IF (\'[before]\',e.date_of_treatment <= \'[before:date]\',1) AND\r\n IF (\'[facility]\',e.building_id = \'[facility:query:SELECT id, name FROM buildings ORDER BY name]\',1) AND\r\n IF (\'[provider]\',e.treating_person_id = \'[provider:query:SELECT prov.person_id, CONCAT(per.first_name, \" \", last_name) FROM provider AS prov JOIN person AS per USING(person_id)]\',1) AND\r\n IF (\'[reason]\',e.encounter_reason = \'[reason:enum:encounter_reason]\',1)\r\nGROUP BY\r\n `Walk-in?`\r\n---[total_encounters_by_facility,hideFilter,noPager]---\r\nSELECT\r\n b.name Facility,\r\n count(e.encounter_id) total\r\nFROM\r\n encounter AS e\r\n INNER JOIN buildings AS b ON(e.building_id = b.id)\r\n LEFT JOIN occurences AS o on (e.occurence_id = o.id)\r\nWHERE\r\n IF (\'[after]\',e.date_of_treatment >= \'[after:date]\',1) AND\r\n IF (\'[before]\',e.date_of_treatment <= \'[before:date]\',1) AND\r\n IF (\'[facility]\',e.building_id = \'[facility:query:SELECT id, name FROM buildings ORDER BY name]\',1) AND\r\n IF (\'[provider]\',e.treating_person_id = \'[provider:query:SELECT prov.person_id, CONCAT(per.first_name, \" \", last_name) FROM provider AS prov JOIN person AS per USING(person_id)]\',1) AND\r\n IF (\'[reason]\',e.encounter_reason = \'[reason:enum:encounter_reason]\',1)\r\nGROUP BY\r\n b.id\r\n---[total_encounters_by_provider,hideFilter,noPager]---\r\nSELECT\r\n concat_ws(\', \',pro.last_name,pro.first_name) Provider,\r\n count(e.encounter_id) total\r\nFROM\r\n encounter e\r\n INNER JOIN person AS pro ON(e.treating_person_id = pro.person_id)\r\n LEFT JOIN occurences AS o on (e.occurence_id = o.id)\r\nWHERE\r\n IF (\'[after]\',e.date_of_treatment >= \'[after:date]\',1) AND\r\n IF (\'[before]\',e.date_of_treatment <= \'[before:date]\',1) AND\r\n IF (\'[facility]\',e.building_id = \'[facility:query:SELECT id, name FROM buildings ORDER BY name]\',1) AND\r\n IF (\'[provider]\',e.treating_person_id = \'[provider:query:SELECT prov.person_id, CONCAT(per.first_name, \" \", last_name) FROM provider AS prov JOIN person AS per USING(person_id)]\',1) AND\r\n IF (\'[reason]\',e.encounter_reason = \'[reason:enum:encounter_reason]\',1)\r\nGROUP BY\r\n pro.person_id\r\n','Encounter Census','encountercensus'),(607663,'','','Exit Report','---[practice]---\r\nselect \r\n p.name,\r\n a.line1,\r\n a.line2,\r\n a.city,\r\n a.state,\r\n a.postal_code\r\nfrom practices p \r\ninner join buildings b on p.id = b.practice_id\r\ninner join encounter e on b.id = e.building_id\r\nleft join practice_address pa on p.id = pa.practice_id\r\nleft join address a using(address_id)\r\nwhere address_type = 4 and e.encounter_id = \'[encounter_id:CONTROLLER:C_Patient]\'\r\n---[treating_facility]---\r\nselect \r\n b.name,\r\n a.line1,\r\n a.line2,\r\n a.city,\r\n a.state,\r\n a.postal_code\r\nfrom buildings b\r\ninner join encounter e on b.id = e.building_id\r\nleft join building_address ba on b.id = ba.building_id\r\nleft join address a using(address_id)\r\nwhere e.encounter_id = \'[encounter_id:CONTROLLER:C_Patient]\'\r\n---[treating_provider]---\r\nselect \r\n per.salutation,\r\n per.last_name,\r\n per.first_name,\r\n p.state_license_number,\r\n a.line1,\r\n a.line2,\r\n a.city,\r\n a.state,\r\n a.postal_code,\r\n n.number\r\n\r\nfrom provider p\r\ninner join person per using(person_id)\r\ninner join encounter e on p.person_id = e.treating_person_id\r\nleft join person_address pa on p.person_id = pa.person_id\r\nleft join address a on a.address_id = pa.person_id and address_type = 1\r\nleft join person_number pn on p.person_id = pn.person_id\r\nleft join number n on n.number_id = pn.number_id and n.number_type = 1\r\nwhere\r\n e.encounter_id = \'[encounter_id:CONTROLLER:C_Patient]\'\r\n---[patient]---\r\nselect * from person p\r\ninner join patient pat using(person_id)\r\ninner join encounter e on p.person_id = e.patient_id\r\nleft join person_address pa on p.person_id = pa.person_id\r\nleft join address a on a.address_id = pa.address_id and address_type =1  \r\nleft join person_number pn on p.person_id = pn.person_id\r\nleft join number n on n.number_id = pn.number_id and n.number_type = 1\r\nwhere e.encounter_id = \'[encounter_id:CONTROLLER:C_Patient]\'\r\n---[code_list]--- \r\nselect cpt.code_text `Procedure`, cpt.code Code, \r\nconcat_ws(\', \'\r\n,max(case code_order when 1 then c.code else null end) \r\n,max(case code_order when 2 then c.code else null end)\r\n,max(case code_order when 3 then c.code else null end)\r\n,max(case code_order when 4 then c.code else null end) \r\n) Diagnosis, cd.modifier, cd.units, cd.fee\r\nfrom coding_data cd\r\ninner join codes c using(code_id)\r\ninner join codes cpt on cd.parent_id = cpt.code_id\r\ninner join encounter e on cd.foreign_id = e.encounter_id\r\nwhere e.encounter_id = \'[encounter_id:CONTROLLER:C_Patient]\'\r\ngroup by cd.parent_id\r\nunion\r\nselect \'Total\',\'\',\'\',null,sum(units),sum(fee)\r\nfrom coding_data cd\r\nwhere foreign_id = \'[encounter_id:CONTROLLER:C_Patient]\' and primary_code = 1\r\n---[payment_history]---\r\nselect \r\ndate_format(payment_date, \'%m/%d/%Y\'), amount, payment_type\r\nfrom payment\r\nwhere encounter_id = \'[encounter_id:CONTROLLER:C_Patient]\'\r\n---[encounter]---\r\nselect * from encounter e where e.encounter_id = \'[encounter_id:CONTROLLER:C_Patient]\'\r\n','Exit Report','exitreport'),(607668,'','','New Patient','---[new_patient]---\r\n SELECT CONCAT(first_name,\" \",last_name) as Name, MIN(date_of_treatment) \r\n as  \'First_Visit\', ev.value as Reason, if(walkin,\'yes\',\'no\') as \'walk_in\'\r\n FROM encounter\r\n INNER JOIN person as pers ON pers.person_id = patient_id\r\n INNER JOIN enumeration_definition as ed on ed.name = \'encounter_reason\'\r\n INNER JOIN enumeration_value as ev on ev.enumeration_id = ed.enumeration_id\r\n INNER JOIN occurences on external_id = patient_id\r\n WHERE DATE_SUB(CURDATE(),INTERVAL ifnull(\'[search_days]\',0) DAY) <=  \r\n date_of_treatment\r\n GROUP BY patient_id\r\n ORDER BY date_of_treatment, patient_id\r\n \r\n ---[walkin_count,hideFilter,noPager]---\r\n SELECT Count(encounter_id) as total, SUM(CASE WHEN walkin=1 THEN 1 ELSE 0 END) as walkins, SUM(CASE  WHEN walkin=0 THEN 1 ELSE 0 END) as scheduled, CONCAT(ROUND(((sum(CASE WHEN walkin=1 THEN 1 END)/count(encounter_id)))*100),\'%\') as \'Walkin Percent\'\r\n FROM encounter\r\n INNER JOIN occurences ON external_id = patient_id\r\n WHERE DATE_SUB( CURDATE( ) , INTERVAL ifnull(\'[search_days]\',0)\r\n DAY ) <= date_of_treatment','New Patient Report','newpatient'),(607670,'','','No-Show Report','select \r\n date_format(o.start,\'%a %m/%d/%Y\') `Date`,\r\n concat_ws(\' to \',date_format(o.start,\'%H:%i\'),date_format(o.end,\'%H:%i\')) `Time`, \r\n concat(\r\n  floor((unix_timestamp(o.end) - unix_timestamp(o.start)) / 60 / 60),\r\n  \' hours \',\r\n  floor((unix_timestamp(o.end) - unix_timestamp(o.start)) / 60 % 60),\r\n  \' minutes\'\r\n )Duration, \r\n concat(b.name, \'->\', r.name) Location, \r\n concat(p.last_name,\', \',p.first_name) Patient, \r\n o.notes Title, \r\n o.reason_code Reason, \r\n concat(pro.last_name,\', \',pro.first_name) Provider\r\nfrom \r\n schedules s \r\n inner join events e on e.foreign_id = s.id\r\n inner join occurences o on e.id = o.event_id\r\n inner join rooms r on o.location_id = r.id\r\n inner join buildings b on b.id = r.building_id\r\n inner join person p on o.external_id = p.person_id\r\n inner join user u on o.user_id = u.user_id\r\n inner join person pro on u.person_id = pro.person_id\r\nwhere \r\n s.schedule_code = \'ns\' and\r\n if (\'[after]\',o.start > \'[after:date]\',1) and\r\n if (\'[before]\',o.end < \'[before:date]\',1) and\r\n if (\'[facility]\',o.location_id = \'[facility:query:select r.id, concat(r.name,\'->\',b.name) name from rooms r inner join buildings b on r.building_id = b.id]\',1)\r\n\r\n/***\r\ndsFilters-Reason|enumLookup&ds|appointment_reasons\r\n***/\r\n','No-Show Report','noshow'),(607672,'','','Open Encounter Report','SELECT \r\n	DATE_FORMAT(date_of_treatment,\'%m/%d/%Y\') AS date_of_treatment, \r\n	DATE_FORMAT(timestamp,\'%m/%d/%Y\') AS last_change, \r\n	concat_ws(\', \',p.last_name,p.first_name) AS patient,\r\n	e.encounter_id,\r\n	b.name AS \"facility\",\r\n	CONCAT_WS(\', \', prov_person.last_name, prov_person.first_name) AS provider,\r\n	insurer.name AS insurance,\r\n	c.code_text AS \'Primary Diagnosis\'\r\nFROM\r\n	encounter AS e\r\n	INNER JOIN person AS p on e.patient_id = p.person_id\r\n	JOIN buildings AS b ON(b.id = e.building_id)\r\n	JOIN provider AS prov ON(e.treating_person_id = prov.person_id)\r\n	JOIN person AS prov_person USING(person_id)\r\n	LEFT JOIN storage_int AS curprog ON(e.encounter_id = curprog.foreign_key AND curprog.value_key = \'current_payer\') \r\n	LEFT JOIN insurance_program AS insurer ON(curprog.value = insurer.insurance_program_id)\r\n	LEFT JOIN coding_data AS cd ON (e.encounter_id = cd.foreign_id)\r\n	LEFT JOIN codes AS c ON (cd.code_id = c.code_id)\r\nWHERE\r\n	e.status = \'open\' AND\r\n        (cd.primary_code = 1 OR cd.primary_code IS NULL) AND\r\n	IF (\'[after]\', e.date_of_treatment >= \'[after:date]\', 1) AND\r\n	IF (\'[before]\', e.date_of_treatment <= \'[before:date]\', 1) AND\r\n	IF (\'[facility]\', e.building_id = \'[facility:query:SELECT id, name FROM buildings ORDER BY name]\', 1) AND\r\n	IF (\'[provider]\', e.treating_person_id = \'[provider:query:SELECT prov.person_id, CONCAT(per.first_name, \" \", last_name) FROM provider AS prov JOIN person AS per USING(person_id)]\', 1) AND\r\n	IF (\'[insurance]\', insurer.insurance_program_id = \'[insurance:query:SELECT insurance_program_id, name FROM insurance_program WHERE LENGTH(name) > 0]\', 1)\r\nGROUP BY\r\n	e.encounter_id\r\n\r\n','Open Encounter Report','openencounter'),(607676,'','','Open Route Slips','Select\r\n date_format(e.date_of_treatment,\'%m/%d/%Y\') `Date of Treatment`,\r\n concat(per.last_name,\', \',per.first_name,\' #\',record_number) Patient,\r\n concat(pro.last_name,\', \',pro.first_name) Provider,\r\n route_slip_id `Route Slip #`,\r\n date_format(report_date,\'%m/%d/%Y\') `Route Slip Date`,\r\n count(cd.coding_data_id) `# Claim Lines`\r\nfrom\r\n route_slip rs\r\n inner join encounter e using(encounter_id)\r\n inner join patient p on e.patient_id = p.person_id\r\n inner join person per using(person_id)\r\n inner join person pro on e.treating_person_id = pro.person_id\r\n left join  coding_data cd on e.encounter_id = cd.foreign_id\r\nwhere\r\n e.status = \'open\'\r\ngroup by\r\n route_slip_id,\r\n e.encounter_id\r\norder by report_date DESC\r\n','Open Route Slips','openrouteslips'),(607678,'','','Patient Aging','SELECT FP.claim_id,FP.record_number,SUM(CL.amount), SUM(CL.amount_paid) as paid, \r\nP.first_name, P.last_name\r\nFROM fbperson as FP\r\nINNER JOIN patient as PT on FP.record_number = PT.record_number\r\nINNER JOIN `person` as P on PT.person_id = P.person_id\r\nINNER JOIN fbclaimline as CL  on FP.claim_id = CL.claim_id\r\nINNER JOIN fbclaim as C on CL.claim_id = C.claim_id\r\nGROUP BY PT.record_number','Patient Aging','patientaging'),(607680,'','','Patient Statement History','---[Patient,infoBox]---\r\nselect\r\n last_name, first_name, record_number `Record #`\r\nfrom \r\n patient\r\n inner join person using(person_id)\r\nwhere\r\n patient.person_id = \'<<[patient_id:C_patient]>>\'\r\n---[Statement_History]---\r\nselect\r\n statement_number `Statement #`,\r\n date_format(date_generated,\'%m/%d/%Y\') Date,\r\n amount,\r\nconcat(\'<a href=\"report?report_id=\',report_id,\'&template_id=\',template_id,\'&fromSnapshot=\',sh.report_snapshot_id,\'\">View</a>\') View \r\nfrom\r\nstatement_history sh\r\n left join report_snapshot using(report_snapshot_id)\r\nwhere\r\n type = 1 and\r\n patient_id = \'<<[patient_id:C_patient]>>\'\r\norder by Date\r\n\r\n','Patient Statement History','patientstatementhistory'),(607682,'','','Procedure Productivity','SELECT  C.code, C.code_text as Description, count(`procedure`) as count, SUM(C.fee) as Charges, CL.units, \r\nCL.amount_paid, C.units,AVG(C.fee) as Average\r\nFROM fbclaimline as CL inner join codes  as C \r\nON  `procedure` = C.code\r\nWHERE date_of_treatment >= \'[start_date_token:date]\' and date_of_treatment <= \'[end_date_token:date]\'\r\nGROUP BY  `procedure`','Procedure Productivity','procedureproductivity'),(607684,'','','Transaction Report','/* Sql for a Transaction Report, tagged for using the standard reporting\r\nmechanism */\r\n---[Transaction_List]---\r\nselect \r\ndate_format(e.date_of_treatment,\'%Y-%m-%d\') `payment_date`,\r\nconcat_ws(\', \',p.last_name,p.first_name) patient,\r\npat.record_number,\r\npay.payment_type AS \'Payment Type\',\r\nformat(if(isnull(pay.amount),0,pay.amount),2) amount,\r\nconcat_ws(\', \',pro.last_name,pro.first_name) provider,\r\nev.value encounter_note, \r\nconcat_ws(\', \',per.last_name,per.first_name) user\r\nfrom encounter e\r\nleft join payment pay on pay.encounter_id = e.encounter_id\r\nleft join person p on e.patient_id = p.person_id\r\nleft join patient pat on p.person_id = pat.person_id\r\nleft join person pro on e.treating_person_id = pro.person_id\r\nleft join encounter_value ev on e.encounter_id = ev.encounter_id  AND\r\nev.value_type =1\r\nleft JOIN user u on e.created_by_user_id = u.user_id \r\nleft JOIN person per on per.person_id = u.person_id\r\nwhere \r\n\r\n(if (\'[user]\',e.created_by_user_id =\'[user:query:select user_id,\r\nconcat_ws(\', \',last_name,first_name) name from user u inner join person\r\np using(person_id) order by last_name, first_name]\',1)\r\nor\r\nif (\'[user2]\',e.created_by_user_id =\'[user2:query:select user_id,\r\nconcat_ws(\', \',last_name,first_name) name from user u inner join person\r\np using(person_id) order by last_name, first_name]\',0)\r\nor\r\nif (\'[user3]\',e.created_by_user_id =\'[user3:query:select user_id,\r\nconcat_ws(\', \',last_name,first_name) name from user u inner join person\r\np using(person_id) order by last_name, first_name]\',0)\r\n)\r\nand \r\nif (\'[date]\', e.date_of_treatment = \'[date:date]\', e.date_of_treatment = CURDATE()) and\r\nif(\'[facility]\',e.building_id = \'[facility:query:select id, name\r\nfrom buildings order by name]\',1)\r\n\r\n\r\n\r\n/***\r\ndsFilters-Payment Type|enumLookup&ds|payment_type\r\n***/\r\n---[Total_payment_amount,hideFilter]---\r\nselect \r\nsum(pay.amount) total\r\nfrom payment pay\r\ninner join encounter e on pay.encounter_id = e.encounter_id\r\ninner join person p on e.patient_id = p.person_id\r\ninner join patient pat on p.person_id = pat.person_id\r\ninner join person pro on e.treating_person_id = pro.person_id\r\nwhere if (\'[user]\',e.created_by_user_id =\r\n\'[user]\',1)\r\n and pay.payment_date = \'[date:date]\'\r\nand if(\'[facility]\',e.building_id = \'[facility]\',1)\r\n\r\n\r\n---[Total_payment_amount_by_type,hideFilter]---\r\nselect \r\npay_ev.value AS \'Payment Type\',\r\nsum(pay.amount) total\r\nfrom encounter e\r\ninner join payment pay on pay.encounter_id = e.encounter_id\r\nleft join person p on e.patient_id = p.person_id\r\nleft join patient pat on p.person_id = pat.person_id\r\nleft join person pro on e.treating_person_id = pro.person_id\r\nJOIN enumeration_value AS pay_ev ON (pay.payment_type = pay_ev.key)\r\nJOIN enumeration_definition AS pay_ed ON(pay_ev.enumeration_id = pay_ed.enumeration_id AND pay_ed.name = \'payment_type\')\r\nwhere \r\nif (\'[user]\',e.created_by_user_id = \'[user]\',1) and pay.payment_date = \'[date:date]\' and if(\'[facility]\',e.building_id = \'[facility]\',1)\r\n\r\ngroup by payment_type\r\n\r\n---[Total_encounters_by_provider,hideFilter]---\r\nselect \r\nconcat_ws(\', \',pro.last_name,pro.first_name) provider,\r\ncount(distinct e.encounter_id) total\r\nfrom encounter e\r\nleft join payment pay on pay.encounter_id = e.encounter_id\r\nleft join person p on e.patient_id = p.person_id\r\nleft join patient pat on p.person_id = pat.person_id\r\nleft join person pro on e.treating_person_id = pro.person_id\r\nwhere \r\n(if (\'[user]\',e.created_by_user_id = \'[user]\',1) and e.date_of_treatment\r\n= \'[date:date]\' and if(\'[facility]\',e.building_id = \'[facility]\',1)\r\nor\r\nif (\'[user2]\',e.created_by_user_id = \'[user2]\',1) and\r\ne.date_of_treatment = \'[date:date]\' and if(\'[facility]\',e.building_id =\r\n\'[facility]\',0)\r\nor\r\nif (\'[user3]\',e.created_by_user_id = \'[user3]\',1) and\r\ne.date_of_treatment = \'[date:date]\' and if(\'[facility]\',e.building_id =\r\n\'[facility]\',0))\r\ngroup by provider\r\n---[Total_encounters,hideFilter]---\r\nselect \r\ncount(distinct e.encounter_id) total\r\nfrom encounter e\r\nleft join payment pay on pay.encounter_id = e.encounter_id\r\nleft join person p on e.patient_id = p.person_id\r\nleft join patient pat on p.person_id = pat.person_id\r\nleft join person pro on e.treating_person_id = pro.person_id\r\nwhere \r\n(if (\'[user]\',e.created_by_user_id = \'[user]\',1) and e.date_of_treatment\r\n= \'[date:date]\' and if(\'[facility]\',e.building_id = \'[facility]\',1)\r\nor\r\nif (\'[user2]\',e.created_by_user_id = \'[user2]\',2) and\r\ne.date_of_treatment = \'[date:date]\' and if(\'[facility]\',e.building_id =\r\n\'[facility]\',1)\r\nor\r\nif (\'[user3]\',e.created_by_user_id = \'[user3]\',3) and\r\ne.date_of_treatment = \'[date:date]\' and if(\'[facility]\',e.building_id =\r\n\'[facility]\',1))\r\n','Transaction Report','transactionreport'),(607876,'','','Family Patient Statement','select \'No Query Found\' error','','familypatientstatement'),(607882,'','','Patient Statements','select \'No Query Found\' error','','patientstatements'),(800557,'','','Daily Deposit','---[checks]---\r\nSELECT payment_date, payment_type, ref_num as \\\\\\\'Check Num\\\\\\\', amount FROM payment INNER JOIN encounter e using (encounter_id) INNER JOIN buildings b on(e.building_id = b.id) WHERE payment_type = 4 and payment_date = \\\\\\\'[Date:date]\\\\\\\' and e.building_id = \\\\\\\'[facility:query:select b.id, CONCAT(p.name, \\\\\\\'->\\\\\\\', b.name)\r\nfrom practices p INNER JOIN buildings b on b.practice_id = p.id order by p.name, b.name]\\\\\\\' GROUP BY payment_id \r\n\r\n/*** dsFilters-payment_type|enumLookup&ds|payment_type ***/\r\n\r\n---[check_total,hideFilter]---\r\nSELECT payment_date,  payment_type, SUM(amount) as \\\\\\\'Total\\\\\\\' FROM payment INNER JOIN encounter e using (encounter_id) INNER JOIN buildings b on(e.building_id = b.id) WHERE payment_type = 4 and payment_date = \\\\\\\'[Date:date]\\\\\\\' and e.building_id = \\\\\\\'[facility:query:select b.id, CONCAT(p.name, \\\\\\\'->\\\\\\\', b.name)\r\nfrom practices p INNER JOIN buildings b on b.practice_id = p.id order by p.name, b.name]\\\\\\\' GROUP BY payment_date\r\n\r\n/*** dsFilters-payment_type|enumLookup&ds|payment_type ***/\r\n\r\n---[cash_total,hideFilter]---\r\nSELECT payment_date, payment_type, SUM(amount) as \\\\\\\'Total Cash\\\\\\\' FROM payment INNER JOIN encounter e using (encounter_id) INNER JOIN buildings b on(e.building_id = b.id) WHERE payment_type = 5 and payment_date = \\\\\\\'[Date:date]\\\\\\\' and e.building_id = \\\\\\\'[facility:query:select b.id, CONCAT(p.name, \\\\\\\'->\\\\\\\', b.name)\r\nfrom practices p INNER JOIN buildings b on b.practice_id = p.id order by p.name, b.name]\\\\\\\'GROUP BY payment_date\r\n\r\n/*** dsFilters-payment_type|enumLookup&ds|payment_type ***/\r\n','Test of the Daily Deposit Report','dailydeposit'),(800595,'','','Guarantor','select \'No Query Found\' error','','guarantor'),(800605,'','','Aged Trail Balance','select \\\'No Query Found\\\' error','','AgedTrailBalance'),(800615,'','','Statement Report','select \\\'No Query Found\\\' error','','StatementReport'),(800625,'','','Balance Aging By Payer','---[by_payer]---\r\nSELECT\r\n	CONCAT(\"<a href=\\\"\", \"{url:controller=Report&action=viewByCID}\", \"cid=by_payer_drilldown&rf%5binsurance_program_id%5d=\", ip.insurance_program_id, \"\\\">\", c.name, \" > \", ip.name, \"</a>\") AS payer,\r\n	(\r\n		SUM(CASE WHEN e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN total_billed ELSE 0 END) - \r\n		(\r\n			SUM(CASE WHEN e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN total_paid ELSE 0 END) +\r\n			SUM(CASE WHEN e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ELSE 0 END)\r\n		)\r\n	) AS `current`,\r\n	(\r\n		SUM(CASE WHEN \r\n			e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND\r\n			e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 31 DAY)\r\n			THEN total_billed ELSE 0 END) - \r\n		(\r\n			SUM(CASE WHEN\r\n				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND\r\n				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 31 DAY)\r\n				THEN total_paid ELSE 0 END) +\r\n			SUM(CASE WHEN \r\n				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND\r\n				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 31 DAY)\r\n				THEN IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ELSE 0 END)\r\n		)\r\n	) AS `31 - 60`,\r\n	(\r\n		SUM(CASE WHEN \r\n			e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 90 DAY) AND\r\n			e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 61 DAY)\r\n			THEN total_billed ELSE 0 END) - \r\n		(\r\n			SUM(CASE WHEN\r\n				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 90 DAY) AND\r\n				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 61 DAY)\r\n				THEN total_paid ELSE 0 END) +\r\n			SUM(CASE WHEN \r\n				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 90 DAY) AND\r\n				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 61 DAY)\r\n				THEN IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ELSE 0 END)\r\n		)\r\n	) AS `61 - 90`,\r\n	(\r\n		SUM(CASE WHEN \r\n			e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 120 DAY) AND\r\n			e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 91 DAY)\r\n			THEN total_billed ELSE 0 END) - \r\n		(\r\n			SUM(CASE WHEN\r\n				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 120 DAY) AND\r\n				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 91 DAY)\r\n				THEN total_paid ELSE 0 END) +\r\n			SUM(CASE WHEN \r\n				e.date_of_treatment >= DATE_SUB(NOW(), INTERVAL 120 DAY) AND\r\n				e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 91 DAY)\r\n				THEN IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ELSE 0 END)\r\n		)\r\n	) AS `91 - 120`,\r\n	(\r\n		SUM(CASE WHEN e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 121 DAY) THEN total_billed ELSE 0 END) - \r\n		(\r\n			SUM(CASE WHEN e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 121 DAY) THEN total_paid ELSE 0 END) +\r\n			SUM(CASE WHEN e.date_of_treatment <= DATE_SUB(NOW(), INTERVAL 121 DAY) THEN IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ELSE 0 END)\r\n		)\r\n	) AS `121+`,\r\n	( SUM(total_billed) - ( SUM(total_paid) + SUM( IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ) ) ) AS `balance`\r\nFROM\r\n	patient AS pat\r\n	INNER JOIN encounter AS e ON(pat.person_id = e.patient_id)\r\n	INNER JOIN clearhealth_claim AS cc USING(encounter_id)\r\n	LEFT JOIN (\r\n		SELECT\r\n			foreign_id,\r\n			SUM(writeoff) AS writeoff\r\n		FROM\r\n			payment \r\n		WHERE\r\n			encounter_id = 0\r\n		GROUP BY\r\n			foreign_id\r\n	) AS writeoffs ON(writeoffs.foreign_id = cc.claim_id)\r\n	INNER JOIN storage_int AS current_payer ON (current_payer.foreign_key = e.encounter_id)\r\n	INNER JOIN insurance_program AS ip ON(current_payer.value = ip.insurance_program_id)\r\n	INNER JOIN company AS c USING(company_id)\r\nGROUP BY\r\n	payer\r\nORDER BY\r\n	balance DESC\r\n','','BalanceAgingByPayer'),(808295,'','','Payer Report','---[payer_report]---\r\nSELECT \r\n  CONCAT(\"<a href=\\\"\", \"{url:controller=Report&action=viewByCID}\", \"cid=payercompanydrilldown&rf%5bcompany_id%5d=\", c.company_id, \"\\\">\", c.name, \"</a>\") AS Payer,\r\n  CONCAT(\"<a href=\\\"\", \"{url:controller=Report&action=viewByCID}\", \"cid=payerprogramdrilldown&rf%5binsurance_program_id%5d=\", ip.insurance_program_id, \"\\\">\", ip.name, \"</a>\") AS Program_Name,\r\n  COUNT(per.person_id) AS Total_Patients\r\nFROM \r\n  company AS c\r\n  LEFT JOIN insurance_program AS ip ON c.company_id = ip.company_id\r\n  LEFT JOIN insured_relationship AS ir ON ip.insurance_program_id = ir.insurance_program_id\r\n  LEFT JOIN person AS per ON ir.person_id = per.person_id\r\nGROUP BY ip.insurance_program_id\r\nORDER BY Total_Patients, Payer, Program_Name\r\n','','payer_report'),(808305,'','','Payer Company Drilldown','---[payer_company_drilldown,hideFilter]---\r\nSELECT \r\n  CONCAT(per.last_name, \', \', per.first_name) AS \'Patient Name\',\r\n  per.identifier AS SSN,\r\n  per.gender as Gender,\r\n  per.date_of_birth AS Birthday,\r\n  ip.insurance_program_id AS \'Group Number\',\r\n  ip.name AS \'Group Name\'\r\nFROM \r\n  company AS c\r\n  LEFT JOIN insurance_program AS ip ON c.company_id = ip.company_id\r\n  LEFT JOIN insured_relationship AS ir ON ip.insurance_program_id = ir.insurance_program_id\r\n  LEFT JOIN person AS per ON ir.person_id = per.person_id\r\nWHERE ip.company_id = [company_id]\r\nORDER BY per.last_name, per.first_name\r\n\r\n/***\r\ndsFilters-Gender|enumLookup&ds|gender\r\n***/\r\n','','payercompanydrilldown'),(808315,'','','Payer Program Drilldown','---[payer_program_drilldown,hideFilter]---\r\nSELECT \r\n  CONCAT(per.last_name, \', \', per.first_name) AS \'Patient Name\',\r\n  per.identifier AS SSN,\r\n  per.gender as Gender,\r\n  per.date_of_birth AS Birthday,\r\n  ip.insurance_program_id AS \'Group Number\',\r\n  ip.name AS \'Group Name\'\r\nFROM \r\n  company AS c\r\n  LEFT JOIN insurance_program AS ip ON c.company_id = ip.company_id\r\n  LEFT JOIN insured_relationship AS ir ON ip.insurance_program_id = ir.insurance_program_id\r\n  LEFT JOIN person AS per ON ir.person_id = per.person_id\r\nWHERE ip.insurance_program_id = [insurance_program_id]\r\nORDER BY per.last_name, per.first_name\r\n\r\n/***\r\ndsFilters-Gender|enumLookup&ds|gender\r\n***/\r\n','','payerprogramdrilldown'),(810572,'','','Appointment Reminder','SELECT\r\n	a.*,\r\n	e.*,\r\n	DATE_FORMAT(e.start, \"%W, the %D of %M, %Y, at %l:%i %p\") AS appointment_date,\r\n	pat.*,\r\n	per.*,\r\n	ad.line1, ad.line2, ad.city, ad.postal_code,\r\n	ad_state_enum.value AS state,\r\n	prac.name AS practice_name,\r\n	prac_ad.line1 AS practice_line1, \r\n	prac_ad.line2 AS practice_line2,\r\n	prac_ad.city AS practice_city,\r\n	prac_ad_state_enum.value AS practice_state,\r\n	prac_ad.postal_code AS practice_postal_code,\r\n	CONCAT_WS(\'-\', \r\n		LEFT(prac_num.number, 3),\r\n		LEFT(RIGHT(prac_num.number, 7), 3),\r\n		RIGHT(prac_num.number, 4)\r\n	) AS practice_phone\r\nFROM\r\n	appointment AS a\r\n	INNER JOIN event AS e USING(event_id)\r\n	INNER JOIN patient AS pat ON(a.patient_id = pat.person_id)\r\n	INNER JOIN person AS per USING(person_id)\r\n	INNER JOIN person_address AS per_a USING(person_id)\r\n	INNER JOIN address AS ad USING(address_id)\r\n	INNER JOIN enumeration_value AS ad_state_enum ON(ad.state = ad_state_enum.key)\r\n	INNER JOIN enumeration_definition AS ad_state_enum_def ON(\r\n		ad_state_enum.enumeration_id = ad_state_enum_def.enumeration_id AND\r\n		ad_state_enum_def.name = \"state\"\r\n	)\r\n	INNER JOIN practices AS prac ON (a.practice_id = prac.id)\r\n	INNER JOIN practice_address AS prac_add_tie ON(prac.id = prac_add_tie.practice_id)\r\n	INNER JOIN address AS prac_ad USING(address_id)\r\n	INNER JOIN enumeration_value AS prac_ad_type ON (\r\n		prac_add_tie.address_type = prac_ad_type.key AND\r\n		prac_ad_type.value = \'Main\'\r\n	)\r\n	INNER JOIN enumeration_definition AS prac_ad_type_def ON (\r\n		prac_ad_type.enumeration_id = prac_ad_type_def.enumeration_id AND \r\n		prac_ad_type_def.name = \"address_type\"\r\n	)\r\n	INNER JOIN enumeration_value AS prac_ad_state_enum ON(prac_ad.state = prac_ad_state_enum.key)\r\n	INNER JOIN enumeration_definition AS prac_ad_state_enum_def ON(\r\n		prac_ad_state_enum.enumeration_id = prac_ad_state_enum_def.enumeration_id AND\r\n		prac_ad_state_enum_def.name = \"state\"\r\n	)\r\n	INNER JOIN practice_number ON(prac.id = practice_number.practice_id)\r\n	INNER JOIN number AS prac_num USING(number_id)\r\nWHERE\r\n	e.start >= NOW() AND\r\n	e.start <= DATE_ADD(NOW(), INTERVAL 14 DAY)\r\nGROUP BY\r\n	a.appointment_id','','AppointmentReminder'),(818952,'','','Patient Email','---[patient_email]---\r\nSELECT email, confidentiality\r\nFROM patient pat INNER JOIN person p using (person_id)\r\nWHERE p.inactive = \'0\'\r\nAND p.email != \'\' AND p.last_name like \'%[last_name:string]%\'\r\n\r\n/***\r\ndsFilters-confidentiality|enumLookup&ds|confidentiality_levels\r\n***/','','patient_email'),(827070,'','','Balance Aging By Payer Drilldown','---[by_payer_drilldown,hideFilter]---\r\nSELECT\r\n	e.date_of_treatment AS `date_of_service`,\r\n	c.code_text AS `procedure`,\r\n	CONCAT(per.first_name, \' \', per.last_name, \' (\', pat.person_id, \')\') AS `name`,\r\n	cc.total_billed AS `billed`,\r\n	cc.total_paid AS `paid`,\r\n	( SUM(cc.total_billed) - ( SUM(cc.total_paid) + SUM( IF(writeoffs.writeoff IS NULL, 0, writeoffs.writeoff) ) ) ) AS balance\r\nFROM\r\n	patient AS pat\r\n	INNER JOIN encounter AS e ON(pat.person_id = e.patient_id)\r\n	INNER JOIN clearhealth_claim AS cc USING(encounter_id)\r\n	LEFT JOIN (\r\n		SELECT foreign_id, SUM(writeoff) AS writeoff\r\n		FROM payment \r\n		WHERE encounter_id = 0\r\n		GROUP BY foreign_id\r\n	) AS writeoffs ON(writeoffs.foreign_id = cc.claim_id)\r\n	INNER JOIN person AS per ON per.person_id = pat.person_id\r\n	LEFT JOIN coding_data AS cd ON cd.foreign_id = e.encounter_id\r\n	LEFT JOIN codes AS c ON c.code_id = cd.code_id\r\nGROUP BY\r\n	name\r\n','','by_payer_drilldown');
UNLOCK TABLES;
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
CREATE TABLE `rooms` (
  `id` int(11) NOT NULL default '0',
  `description` text NOT NULL,
  `number_seats` int(11) NOT NULL default '0',
  `building_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `color` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `schedule_id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(150) default NULL,
  `description_long` text,
  `description_short` text,
  `schedule_code` varchar(255) default NULL,
  `provider_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`schedule_id`),
  KEY `provider_id` (`provider_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `schedule_event`
--


/*!40000 ALTER TABLE `schedule_event` DISABLE KEYS */;
LOCK TABLES `schedule_event` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `schedule_event` ENABLE KEYS */;

--
-- Table structure for table `schedules`
--

DROP TABLE IF EXISTS `schedules`;
CREATE TABLE `schedules` (
  `id` int(11) NOT NULL default '0',
  `schedule_code` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `description_long` text NOT NULL,
  `description_short` text NOT NULL,
  `practice_id` int(11) NOT NULL default '0',
  `user_id` int(11) default NULL,
  `room_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `schedules`
--


/*!40000 ALTER TABLE `schedules` DISABLE KEYS */;
LOCK TABLES `schedules` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `schedules` ENABLE KEYS */;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `secondary_practice`
--


/*!40000 ALTER TABLE `secondary_practice` DISABLE KEYS */;
LOCK TABLES `secondary_practice` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `secondary_practice` ENABLE KEYS */;

--
-- Table structure for table `sequences`
--

DROP TABLE IF EXISTS `sequences`;
CREATE TABLE `sequences` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sequences`
--


/*!40000 ALTER TABLE `sequences` DISABLE KEYS */;
LOCK TABLES `sequences` WRITE;
INSERT INTO `sequences` VALUES (800259);
UNLOCK TABLES;
/*!40000 ALTER TABLE `sequences` ENABLE KEYS */;

--
-- Table structure for table `sequences_named`
--

DROP TABLE IF EXISTS `sequences_named`;
CREATE TABLE `sequences_named` (
  `name` varchar(255) NOT NULL default '',
  `counter` int(11) NOT NULL default '0',
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sequences_named`
--


/*!40000 ALTER TABLE `sequences_named` DISABLE KEYS */;
LOCK TABLES `sequences_named` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `sequences_named` ENABLE KEYS */;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `statement_sequence`
--


/*!40000 ALTER TABLE `statement_sequence` DISABLE KEYS */;
LOCK TABLES `statement_sequence` WRITE;
INSERT INTO `statement_sequence` VALUES (1000);
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `states`
--


/*!40000 ALTER TABLE `states` DISABLE KEYS */;
LOCK TABLES `states` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `states` ENABLE KEYS */;

--
-- Table structure for table `storage_date`
--

DROP TABLE IF EXISTS `storage_date`;
CREATE TABLE `storage_date` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(50) NOT NULL default '',
  `value` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`foreign_key`,`value_key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Generic way to store date values';

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
  PRIMARY KEY  (`foreign_key`,`value_key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Generic way to store integer values (also boolean)';

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
  PRIMARY KEY  (`foreign_key`,`value_key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Generic way to string values';

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
  PRIMARY KEY  (`foreign_key`,`value_key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Generic way to string values';

--
-- Dumping data for table `storage_text`
--


/*!40000 ALTER TABLE `storage_text` DISABLE KEYS */;
LOCK TABLES `storage_text` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `storage_text` ENABLE KEYS */;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `superbill_data`
--


/*!40000 ALTER TABLE `superbill_data` DISABLE KEYS */;
LOCK TABLES `superbill_data` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `superbill_data` ENABLE KEYS */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `user_id` int(11) NOT NULL default '0',
  `username` varchar(55) NOT NULL default '',
  `password` varchar(255) NOT NULL default '',
  `nickname` varchar(255) NOT NULL default '',
  `color` varchar(255) NOT NULL default '',
  `person_id` int(11) default NULL,
  `disabled` enum('yes','no') NOT NULL default 'yes',
  `default_location_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `username` (`username`),
  KEY `person_id` (`person_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Users in the System';

--
-- Dumping data for table `user`
--


/*!40000 ALTER TABLE `user` DISABLE KEYS */;
LOCK TABLES `user` WRITE;
INSERT INTO `user` VALUES (1,'admin','admin','adm','#dddddd',0,'no',0);
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `visit_queue_reason_id` int(11) NOT NULL auto_increment,
  `ordernum` int(11) NOT NULL default '0',
  `appt_length` time NOT NULL default '01:00:00',
  `reason` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`visit_queue_reason_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `visit_queue_template_id` int(11) NOT NULL auto_increment,
  `number_of_appointments` int(11) NOT NULL default '0',
  `visit_queue_reason_id` int(11) NOT NULL default '0',
  `visit_queue_rule_id` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`visit_queue_template_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  PRIMARY KEY  (`widget_form_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `zipcodes`
--


/*!40000 ALTER TABLE `zipcodes` DISABLE KEYS */;
LOCK TABLES `zipcodes` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `zipcodes` ENABLE KEYS */;

INSERT INTO `category` VALUES (1, 'Clearhealth', 'Clearhealth', 0, 1, 1);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

