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
INSERT INTO `states` VALUES ('AL','Alabama','USA'),('AK','Alaska','USA'),('AS','American Samoa','USA'),('AZ','Arizona','USA'),('AR','Arkansas','USA'),('AF','Armed Forces Africa','USA'),('AA','Armed Forces Americas','USA'),('AC','Armed Forces Canada','USA'),('AE','Armed Forces Europe','USA'),('AM','Armed Forces Middle East','USA'),('AP','Armed Forces Pacific','USA'),('CA','California','USA'),('CO','Colorado','USA'),('CT','Connecticut','USA'),('DE','Delaware','USA'),('DC','District of Columbia','USA'),('FM','Federated States Of Micronesia','USA'),('FL','Florida','USA'),('GA','Georgia','USA'),('GU','Guam','USA'),('HI','Hawaii','USA'),('ID','Idaho','USA'),('IL','Illinois','USA'),('IN','Indiana','USA'),('IA','Iowa','USA'),('KS','Kansas','USA'),('KY','Kentucky','USA'),('LA','Louisiana','USA'),('ME','Maine','USA'),('MH','Marshall Islands','USA'),('MD','Maryland','USA'),('MA','Massachusetts','USA'),('MI','Michigan','USA'),('MN','Minnesota','USA'),('MS','Mississippi','USA'),('MO','Missouri','USA'),('MT','Montana','USA'),('NE','Nebraska','USA'),('NV','Nevada','USA'),('NH','New Hampshire','USA'),('NJ','New Jersey','USA'),('NM','New Mexico','USA'),('NY','New York','USA'),('NC','North Carolina','USA'),('ND','North Dakota','USA'),('MP','Northern Mariana Islands','USA'),('OH','Ohio','USA'),('OK','Oklahoma','USA'),('OR','Oregon','USA'),('PW','Palau','USA'),('PA','Pennsylvania','USA'),('PR','Puerto Rico','USA'),('RI','Rhode Island','USA'),('SC','South Carolina','USA'),('SD','South Dakota','USA'),('TN','Tennessee','USA'),('TX','Texas','USA'),('UT','Utah','USA'),('VT','Vermont','USA'),('VI','Virgin Islands','USA'),('VA','Virginia','USA'),('WA','Washington','USA'),('WV','West Virginia','USA'),('WI','Wisconsin','USA'),('WY','Wyoming','USA'),('AB','Alberta','CAN'),('BC','British Columbia','CAN'),('MB','Manitoba','CAN'),('NF','Newfoundland','CAN'),('NB','New Brunswick','CAN'),('NS','Nova Scotia','CAN'),('NT','Northwest Territories','CAN'),('NU','Nunavut','CAN'),('ON','Ontario','CAN'),('PE','Prince Edward Island','CAN'),('QC','Quebec','CAN'),('SK','Saskatchewan','CAN'),('YT','Yukon Territory','CAN'),('NDS','Niedersachsen','DEU'),('BAW','Baden-WÃƒÂ¼rttemberg','DEU'),('BAY','Bayern','DEU'),('BER','Berlin','DEU'),('BRG','Brandenburg','DEU'),('BRE','Bremen','DEU'),('HAM','Hamburg','DEU'),('HES','Hessen','DEU'),('MEC','Mecklenburg-Vorpommern','DEU'),('NRW','Nordrhein-Westfalen','DEU'),('RHE','Rheinland-Pfalz','DEU'),('SAR','Saarland','DEU'),('SAS','Sachsen','DEU'),('SAC','Sachsen-Anhalt','DEU'),('SCN','Schleswig-Holstein','DEU'),('THE','ThÃƒÂ¼ringen','DEU'),('WI','Wien','AUT'),('NO','NiederÃƒÂ¶sterreich','AUT'),('OO','OberÃƒÂ¶sterreich','AUT'),('SB','Salzburg','AUT'),('KN','KÃƒÂ¤rnten','AUT'),('ST','Steiermark','AUT'),('TI','Tirol','AUT'),('BL','Burgenland','AUT'),('VB','Voralberg','AUT'),('AG','Aargau','CHE'),('AI','Appenzell Innerrhoden','CHE'),('AR','Appenzell Ausserrhoden','CHE'),('BE','Bern','CHE'),('BL','Basel-Landschaft','CHE'),('BS','Basel-Stadt','CHE'),('FR','Freiburg','CHE'),('GE','Genf','CHE'),('GL','Glarus','CHE'),('JU','GraubÃƒÂ¼nden','CHE'),('JU','Jura','CHE'),('LU','Luzern','CHE'),('NE','Neuenburg','CHE'),('NW','Nidwalden','CHE'),('OW','Obwalden','CHE'),('SG','St. Gallen','CHE'),('SH','Schaffhausen','CHE'),('SO','Solothurn','CHE'),('SZ','Schwyz','CHE'),('TG','Thurgau','CHE'),('TI','Tessin','CHE'),('UR','Uri','CHE'),('VD','Waadt','CHE'),('VS','Wallis','CHE'),('ZG','Zug','CHE'),('ZH','ZÃƒÂ¼rich','CHE'),('A CoruÃƒÂ±a','A CoruÃƒÂ±a','ESP'),('Alava','Alava','ESP'),('Albacete','Albacete','ESP'),('Alicante','Alicante','ESP'),('Almeria','Almeria','ESP'),('Asturias','Asturias','ESP'),('Avila','Avila','ESP'),('Badajoz','Badajoz','ESP'),('Baleares','Baleares','ESP'),('Barcelona','Barcelona','ESP'),('Burgos','Burgos','ESP'),('Caceres','Caceres','ESP'),('Cadiz','Cadiz','ESP'),('Cantabria','Cantabria','ESP'),('Castellon','Castellon','ESP'),('Ceuta','Ceuta','ESP'),('Ciudad Real','Ciudad Real','ESP'),('Cordoba','Cordoba','ESP'),('Cuenca','Cuenca','ESP'),('Girona','Girona','ESP'),('Granada','Granada','ESP'),('Guadalajara','Guadalajara','ESP'),('Guipuzcoa','Guipuzcoa','ESP'),('Huelva','Huelva','ESP'),('Huesca','Huesca','ESP'),('Jaen','Jaen','ESP'),('La Rioja','La Rioja','ESP'),('Las Palmas','Las Palmas','ESP'),('Leon','Leon','ESP'),('Lleida','Lleida','ESP'),('Lugo','Lugo','ESP'),('Madrid','Madrid','ESP'),('Malaga','Malaga','ESP'),('Melilla','Melilla','ESP'),('Murcia','Murcia','ESP'),('Navarra','Navarra','ESP'),('Ourense','Ourense','ESP'),('Palencia','Palencia','ESP'),('Pontevedra','Pontevedra','ESP'),('Salamanca','Salamanca','ESP'),('Santa Cruz de Tenerife','Santa Cruz de Tenerife','ESP'),('Segovia','Segovia','ESP'),('Sevilla','Sevilla','ESP'),('Soria','Soria','ESP'),('Tarragona','Tarragona','ESP'),('Teruel','Teruel','ESP'),('Toledo','Toledo','ESP'),('Valencia','Valencia','ESP'),('Valladolid','Valladolid','ESP'),('Vizcaya','Vizcaya','ESP'),('Zamora','Zamora','ESP'),('Zaragoza','Zaragoza','ESP');
UNLOCK TABLES;
/*!40000 ALTER TABLE `states` ENABLE KEYS */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

