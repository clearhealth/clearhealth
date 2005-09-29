CREATE TABLE `ownership` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `id` (`id`)
) TYPE=MyISAM;
INSERT INTO `ownership` VALUES (502530,1),(502531,1),(502532,1),(502533,1),(502534,1),(502535,1),(502536,1),(502537,1),(502538,1),(505001,1);
