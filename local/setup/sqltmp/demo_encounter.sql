/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO,MYSQL323' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `encounter_value`;
CREATE TABLE `encounter_value` (
  `encounter_value_id` int(11) NOT NULL default '0',
  `encounter_id` int(11) NOT NULL default '0',
  `value_type` int(11) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '0',
  PRIMARY KEY  (`encounter_value_id`),
  KEY `encounter_id` (`encounter_id`)
) TYPE=MyISAM;


/*!40000 ALTER TABLE `encounter_value` DISABLE KEYS */;
LOCK TABLES `encounter_value` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `encounter_value` ENABLE KEYS */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

