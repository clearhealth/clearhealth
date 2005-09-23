CREATE TABLE `groups` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
INSERT INTO `groups` VALUES (1,'superadmin'),(2,'practice_admin'),(3,'usage'),(0,'provider');
