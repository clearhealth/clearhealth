/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO,MYSQL323' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

/*!40000 ALTER TABLE `schedules` DISABLE KEYS */;
LOCK TABLES `schedules` WRITE;
INSERT INTO `schedules` VALUES (502530,'NS','No Shows','This is primarily for reporting purposes. When a patients appointment is set to no-show. They are assigned to this schedule. You must have two events groups names \"No Shows\" and \"Cancelations\"','Schedule No Shows are assigned to',0,0,0),(502531,'ADM','Admin Events','Anything added to the admin schedule will appear on every calendar. Use this for practice-wide meetings.','Admin Events appear on every schedule',0,0,0),(502559,'PS','Dr Einstien\'s Schedule','Example Schedule do not use in production','',502539,502558,0),(502568,'PS','Dr Trotter\'s Schedule','','',0,502567,0),(502577,'PS','Dr Minsky\'s Schedule','Demo Schedule do not use in production. This one is particularly non-realistic. B/c this provider is in three places at once. Easier than having 10 providers though','',502539,502576,0);
UNLOCK TABLES;
/*!40000 ALTER TABLE `schedules` ENABLE KEYS */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

