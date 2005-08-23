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

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

