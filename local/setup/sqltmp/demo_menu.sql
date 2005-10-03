/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO,MYSQL323' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
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
) TYPE=MyISAM;


/*!40000 ALTER TABLE `menu_report` DISABLE KEYS */;
LOCK TABLES `menu_report` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `menu_report` ENABLE KEYS */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

