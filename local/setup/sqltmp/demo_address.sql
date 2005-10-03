/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO,MYSQL323' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
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
) TYPE=MyISAM COMMENT='An address that can be for a company or a person. STARTEMPTY';


/*!40000 ALTER TABLE `address` DISABLE KEYS */;
LOCK TABLES `address` WRITE;
INSERT INTO `address` VALUES (502540,'','5000 Hardy drv suite 600','','Tempe',0,0,3,'85282',''),(502541,'','','','',0,0,1,'',''),(502546,'','5001 S Hardy rd #1','','Tempe',0,0,3,'85282',''),(502548,'','5002 S Hardy #2','','Tempe',0,0,3,'85282',''),(502556,'ExampleInscoAddress','1 Insco way box 1111','','Tempe',0,0,3,'85282','Example Insurance Company Address'),(502565,'Example Doctor Address','1 Einstien way','','Tempe',0,0,3,'85282','Example Doctor Address'),(502574,'Example Doctor','1 Ukiah way','','Tempe',0,0,3,'85282','Example Doctor Address'),(502582,'example doctor address','1 example way','','tempe',0,0,3,'85282','example doctor address'),(505197,'Example Patient','1 Cruz Way','','Tempe',0,0,3,'85282','Example patient home'),(505201,'Demo Patient address','1 Cruz way','','Tempe',0,0,3,'85282','Demo patient address'),(505204,'juniors address','Juniors address','','Tempe',0,0,3,'85282','Demo patient address');
UNLOCK TABLES;
/*!40000 ALTER TABLE `address` ENABLE KEYS */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

