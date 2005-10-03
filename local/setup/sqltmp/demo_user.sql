/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO,MYSQL323' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
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
) TYPE=MyISAM COMMENT='Users in the System';


/*!40000 ALTER TABLE `user` DISABLE KEYS */;
LOCK TABLES `user` WRITE;
INSERT INTO `user` VALUES (1,'admin','admin','','',NULL,'no',502549),(502567,'mtrotter','mtrotter','mt','00FFFF',502566,'no',502550),(502558,'aeinstein','aeinstein','emc2','FFCC99',502557,'no',502549),(502576,'mminksi','mminski','mm','CC99FF',502575,'no',502551);
UNLOCK TABLES;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

