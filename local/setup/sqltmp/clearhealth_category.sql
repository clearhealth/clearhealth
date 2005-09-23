CREATE TABLE `category` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  `parent` int(11) NOT NULL default '0',
  `lft` int(11) NOT NULL default '0',
  `rght` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `parent` (`parent`),
  KEY `lft` (`lft`,`rght`)
) TYPE=MyISAM COMMENT='STARTWITHDATA';
INSERT INTO `category` VALUES (1,'ClearHealth','',0,0,6);
CREATE TABLE `category_to_document` (
  `category_id` int(11) NOT NULL default '0',
  `document_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`category_id`,`document_id`)
) TYPE=MyISAM COMMENT='STARTEMPTY';
