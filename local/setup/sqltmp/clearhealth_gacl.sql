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
) TYPE=MyISAM COMMENT='ACL Table';
INSERT INTO `gacl_acl` VALUES (26,'user',1,1,'','Give Superadmn and Supervisors access to everything',1119041473),(24,'user',1,1,'','Give Super Admin access to everything even when no resource is selected',1119041463),(38,'user',1,1,'','',1119041415),(40,'user',1,1,'','',1119041810),(29,'user',0,1,'','Deny Supervisors access to some system wide configuration sections',1112057023),(36,'user',1,1,'','',1119041256),(37,'user',1,1,'','',1119041365),(32,'user',1,1,'','Give billing users basic access to those sections',1112160920),(33,'user',1,1,'','Give all users of the system access to basic app sections',1112057091),(39,'user',1,1,'','',1119041735);
CREATE TABLE `gacl_acl_sections` (
  `id` int(11) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(230) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_value_acl_sections` (`value`),
  KEY `gacl_hidden_acl_sections` (`hidden`)
) TYPE=MyISAM;
INSERT INTO `gacl_acl_sections` VALUES (1,'system',1,'System',0),(2,'user',2,'User',0);
CREATE TABLE `gacl_acl_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;
INSERT INTO `gacl_acl_seq` VALUES (40),(40);
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
) TYPE=MyISAM;
INSERT INTO `gacl_aco` VALUES (11,'actions','view',10,'view',0),(12,'actions','edit',11,'edit',0),(13,'actions','add',12,'add',0),(14,'actions','delete',13,'delete',0),(16,'actions','usage',9,'usage',0),(17,'actions','uploadFile',14,'Upload A file',0),(18,'actions','delete_owner',15,'Delete Owner',0),(19,'actions','edit_owner',16,'Edit Owner',0),(20,'actions','double_book',17,'Double Book Apointment',0);
CREATE TABLE `gacl_aco_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) TYPE=MyISAM;
INSERT INTO `gacl_aco_map` VALUES (24,'actions','add'),(24,'actions','delete'),(24,'actions','delete_owner'),(24,'actions','edit'),(24,'actions','edit_owner'),(24,'actions','double_book'),(24,'actions','uploadFile'),(24,'actions','usage'),(24,'actions','view'),(26,'actions','add'),(26,'actions','delete'),(26,'actions','delete_owner'),(26,'actions','edit'),(26,'actions','edit_owner'),(26,'actions','uploadFile'),(26,'actions','usage'),(26,'actions','view'),(29,'actions','add'),(29,'actions','edit'),(32,'actions','add'),(32,'actions','delete'),(32,'actions','edit'),(32,'actions','usage'),(32,'actions','view'),(33,'actions','usage'),(33,'actions','view'),(36,'actions','usage'),(36,'actions','view'),(37,'actions','add'),(37,'actions','delete_owner'),(37,'actions','edit'),(37,'actions','usage'),(37,'actions','view'),(38,'actions','add'),(38,'actions','delete'),(38,'actions','delete_owner'),(38,'actions','edit'),(38,'actions','usage'),(38,'actions','view'),(39,'actions','add'),(39,'actions','delete'),(39,'actions','double_book'),(39,'actions','edit'),(39,'actions','uploadFile'),(39,'actions','usage'),(39,'actions','view'),(40,'actions','add'),(40,'actions','edit'),(40,'actions','usage'),(40,'actions','view');
CREATE TABLE `gacl_aco_sections` (
  `id` int(11) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(230) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_value_aco_sections` (`value`),
  KEY `gacl_hidden_aco_sections` (`hidden`)
) TYPE=MyISAM;
INSERT INTO `gacl_aco_sections` VALUES (11,'actions',10,'Actions',0);
CREATE TABLE `gacl_aco_sections_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;
INSERT INTO `gacl_aco_sections_seq` VALUES (11),(11);
CREATE TABLE `gacl_aco_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;
INSERT INTO `gacl_aco_seq` VALUES (20),(20);
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
) TYPE=MyISAM;
INSERT INTO `gacl_aro` VALUES (15,'users','admin',10,'Admin',0);
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
) TYPE=MyISAM;
INSERT INTO `gacl_aro_groups` VALUES (10,0,1,26,'Root','root'),(12,23,11,12,'System Admin','admin'),(19,10,2,9,'User Types','users'),(20,19,3,4,'Provider','provider'),(21,19,5,6,'Mid-level','mid-level'),(22,19,7,8,'Staff','staff'),(23,10,10,25,'Roles','roles'),(24,23,13,14,'Supervisor','supervisor'),(26,23,15,16,'Front Office','front_office'),(31,23,23,24,'Staff','role_staff'),(28,23,17,18,'Biller','billing_user'),(29,23,19,20,'Medical Assistant','medical_assistant');
CREATE TABLE `gacl_aro_groups_id_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;
INSERT INTO `gacl_aro_groups_id_seq` VALUES (31),(31);
CREATE TABLE `gacl_aro_groups_map` (
  `acl_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acl_id`,`group_id`)
) TYPE=MyISAM;
INSERT INTO `gacl_aro_groups_map` VALUES (24,12),(26,24),(29,24),(32,28),(33,20),(33,21),(33,22),(36,31),(37,31),(38,29),(39,24),(40,28);
CREATE TABLE `gacl_aro_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) TYPE=MyISAM;
CREATE TABLE `gacl_aro_sections` (
  `id` int(11) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(230) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_value_aro_sections` (`value`),
  KEY `gacl_hidden_aro_sections` (`hidden`)
) TYPE=MyISAM;
INSERT INTO `gacl_aro_sections` VALUES (10,'users',10,'Users',0);
CREATE TABLE `gacl_aro_sections_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;
INSERT INTO `gacl_aro_sections_seq` VALUES (11),(11);
CREATE TABLE `gacl_aro_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;
INSERT INTO `gacl_aro_seq` VALUES (38),(38);
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
) TYPE=MyISAM;
INSERT INTO `gacl_axo` VALUES (0,'resources','main',10,'Section - Main',0),(19,'resources','preferences',10,'Section - Preferences',0),(17,'resources','default',10,'Section - Default',0),(16,'resources','access',10,'Section - Access',0),(44,'resources','practice',10,'Section - Practice',0),(43,'resources','personschedule',10,'Section - PersonSchedule',0),(42,'resources','patientfinder',10,'Section - PatientFinder',0),(41,'resources','patient',10,'Section - Patient',0),(40,'resources','location',10,'Section - Location',0),(39,'resources','feeschedule',10,'Section - FeeSchedule',0),(38,'resources','calendar',10,'Section - Calendar',0),(37,'resources','user',10,'Section - User',0),(36,'resources','enumeration',10,'Section - Enumeration',0),(45,'resources','report',10,'Section - Report',0),(46,'resources','schedule',10,'Section - Schedule',0),(47,'resources','form',10,'Section - Form',0),(48,'resources','billing',10,'Section - Billing',0),(49,'resources','admin',10,'Section - Admin',0),(50,'resources','document',10,'Section - Document',0),(51,'resources','documentcategory',10,'Section - DocumentCategory',0),(52,'resources','insurance',10,'Section - Insurance',0),(53,'resources','superbill',10,'Section - Superbill',0),(54,'resources','event',10,'Section - Event',0),(55,'resources','occurence',10,'Section - Occurence',0),(56,'resources','building',10,'Building',0),(57,'resources','room',10,'room',0),(58,'resources','pdf',10,'Section - PDF',0),(59,'resources','coding',10,'Section - Coding',0),(60,'resources','docs',10,'Section - Docs',0),(61,'resources','eob',10,'Section - Eob',0),(62,'resources','claim',10,'Section - Claim',0),(63,'resources','freebgateway',10,'Section - FreeBGateway',0),(64,'resources','main_calendar',1,'Main Group Calendar',0),(65,'resources','main_billing',2,'Main Group Billing',0),(66,'resources','main_patient',3,'Main Group Patient',0),(67,'resources','main_admin',4,'Main Group Admin',0),(68,'resources','account',10,'Section - Account',0),(69,'resources','appointment',10,'Section - Appointment',0);
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
) TYPE=MyISAM;
INSERT INTO `gacl_axo_groups` VALUES (10,0,1,4,'Root','root'),(11,10,2,3,'All Site Sections','sections');
CREATE TABLE `gacl_axo_groups_id_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;
INSERT INTO `gacl_axo_groups_id_seq` VALUES (11);
CREATE TABLE `gacl_axo_groups_map` (
  `acl_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acl_id`,`group_id`)
) TYPE=MyISAM;
INSERT INTO `gacl_axo_groups_map` VALUES (24,11);
CREATE TABLE `gacl_axo_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) TYPE=MyISAM;
INSERT INTO `gacl_axo_map` VALUES (29,'resources','documentcategory'),(29,'resources','enumeration'),(29,'resources','feeschedule'),(29,'resources','form'),(29,'resources','report'),(29,'resources','superbill'),(29,'resources','user'),(32,'resources','billing'),(32,'resources','claim'),(32,'resources','coding'),(32,'resources','document'),(32,'resources','eob'),(32,'resources','main_billing'),(32,'resources','patient'),(33,'resources','access'),(33,'resources','default'),(33,'resources','docs'),(33,'resources','pdf'),(33,'resources','preferences'),(36,'resources','calendar'),(36,'resources','location'),(36,'resources','patient'),(36,'resources','patientfinder'),(37,'resources','appointment'),(37,'resources','calendar'),(37,'resources','location'),(37,'resources','patient'),(37,'resources','patientfinder'),(38,'resources','appointment'),(38,'resources','calendar'),(38,'resources','location'),(38,'resources','patient'),(38,'resources','patientfinder'),(39,'resources','appointment'),(39,'resources','calendar'),(39,'resources','event'),(39,'resources','location'),(39,'resources','occurence'),(39,'resources','patient'),(39,'resources','patientfinder'),(39,'resources','schedule'),(40,'resources','admin'),(40,'resources','appointment'),(40,'resources','billing'),(40,'resources','calendar'),(40,'resources','claim'),(40,'resources','coding'),(40,'resources','eob'),(40,'resources','event'),(40,'resources','feeschedule'),(40,'resources','insurance'),(40,'resources','location'),(40,'resources','occurence'),(40,'resources','patient'),(40,'resources','patientfinder'),(40,'resources','personschedule'),(40,'resources','practice'),(40,'resources','schedule'),(40,'resources','superbill');
CREATE TABLE `gacl_axo_sections` (
  `id` int(11) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(230) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_value_axo_sections` (`value`),
  KEY `gacl_hidden_axo_sections` (`hidden`)
) TYPE=MyISAM;
INSERT INTO `gacl_axo_sections` VALUES (0,'resources',10,'Resources',0);
CREATE TABLE `gacl_axo_sections_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;
INSERT INTO `gacl_axo_sections_seq` VALUES (24);
CREATE TABLE `gacl_axo_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;
INSERT INTO `gacl_axo_seq` VALUES (69);
CREATE TABLE `gacl_groups_aro_map` (
  `group_id` int(11) NOT NULL default '0',
  `aro_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`aro_id`)
) TYPE=MyISAM;
INSERT INTO `gacl_groups_aro_map` VALUES (12,15);
CREATE TABLE `gacl_groups_axo_map` (
  `group_id` int(11) NOT NULL default '0',
  `axo_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`axo_id`)
) TYPE=MyISAM;
INSERT INTO `gacl_groups_axo_map` VALUES (11,0),(11,16),(11,17),(11,18),(11,19),(11,36),(11,37),(11,38),(11,39),(11,40),(11,41),(11,42),(11,43),(11,44),(11,45),(11,46),(11,47),(11,48),(11,49),(11,50),(11,51),(11,52),(11,53),(11,54),(11,55),(11,56),(11,57),(11,58),(11,59),(11,60),(11,61),(11,62),(11,63),(11,64),(11,65),(11,66),(11,67),(11,68),(11,69);
CREATE TABLE `gacl_phpgacl` (
  `name` varchar(230) NOT NULL default '',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`name`)
) TYPE=MyISAM;
INSERT INTO `gacl_phpgacl` VALUES ('version','3.3.3'),('schema_version','2.1');
