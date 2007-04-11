-- MySQL dump 9.11
--
-- Host: localhost    Database: db102
-- ------------------------------------------------------
-- Server version	4.0.23a

--
-- Table structure for table `folders`
--

DROP TABLE IF EXISTS folders;
CREATE TABLE folders (
  folder_id int(10) unsigned NOT NULL auto_increment,
  label varchar(50) NOT NULL default '',
  create_date datetime default '0000-00-00 00:00:00',
  modify_date datetime default '0000-00-00 00:00:00',
  webdavname varchar(255) NOT NULL default '',
  PRIMARY KEY  (folder_id)
) TYPE=MyISAM;

--
-- Dumping data for table `folders`
--


--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS notes;
CREATE TABLE notes (
  note_id int(10) unsigned NOT NULL default '0',
  revision_id int(10) unsigned NOT NULL default '0',
  user_id int(10) unsigned NOT NULL default '0',
  note mediumtext NOT NULL,
  create_date datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (note_id),
  KEY revision_id (revision_id,user_id)
) TYPE=MyISAM;

--
-- Dumping data for table `notes`
--


--
-- Table structure for table `revisions`
--

DROP TABLE IF EXISTS revisions;
CREATE TABLE revisions (
  revision_id int(10) unsigned NOT NULL auto_increment,
  storable_id int(10) unsigned NOT NULL default '0',
  revision int(10) unsigned NOT NULL default '0',
  create_date datetime NOT NULL default '0000-00-00 00:00:00',
  user_id int(10) unsigned NOT NULL default '0',
  filesize int(11) default NULL,
  PRIMARY KEY  (revision_id),
  KEY storable_id (storable_id,revision),
  KEY modify_date (create_date),
  KEY user_id (user_id)
) TYPE=MyISAM;

--
-- Dumping data for table `revisions`
--


--
-- Table structure for table `revisions_db`
--

DROP TABLE IF EXISTS revisions_db;
CREATE TABLE revisions_db (
  revision_id int(10) unsigned NOT NULL default '0',
  filedata blob NOT NULL,
  PRIMARY KEY  (revision_id)
) TYPE=MyISAM;

--
-- Dumping data for table `revisions_db`
--


--
-- Table structure for table `sequences`
--

DROP TABLE IF EXISTS sequences;
CREATE TABLE sequences (
  id int(11) NOT NULL default '0'
) TYPE=MyISAM;

--
-- Dumping data for table `sequences`
--

INSERT INTO sequences VALUES (24);

--
-- Table structure for table `storables`
--

DROP TABLE IF EXISTS storables;
CREATE TABLE storables (
  storable_id int(10) unsigned NOT NULL default '0',
  type tinyint(3) unsigned NOT NULL default '0',
  mimetype varchar(25) NOT NULL default '',
  filename varchar(255) NOT NULL default '',
  storage_type char(2) default NULL,
  create_date datetime default '0000-00-00 00:00:00',
  last_revision_id int(11) default NULL,
  webdavname varchar(255) NOT NULL default '',
  PRIMARY KEY  (storable_id),
  KEY type (type)
) TYPE=MyISAM;

--
-- Dumping data for table `storables`
--


--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS tags;
CREATE TABLE tags (
  tag_id int(10) unsigned NOT NULL auto_increment,
  tag varchar(255) NOT NULL default '',
  PRIMARY KEY  (tag_id),
  UNIQUE KEY tag (tag)
) TYPE=MyISAM;

--
-- Dumping data for table `tags`
--


--
-- Table structure for table `tags_storables`
--

DROP TABLE IF EXISTS tags_storables;
CREATE TABLE tags_storables (
  tag_id int(10) unsigned NOT NULL default '0',
  storable_id int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (tag_id,storable_id)
) TYPE=MyISAM;

--
-- Dumping data for table `tags_storables`
--


--
-- Table structure for table `tree`
--

DROP TABLE IF EXISTS tree;
CREATE TABLE tree (
  tree_id int(10) unsigned NOT NULL auto_increment,
  lft int(10) unsigned NOT NULL default '0',
  rght int(10) unsigned NOT NULL default '0',
  level int(10) unsigned NOT NULL default '0',
  node_type varchar(15) NOT NULL default '',
  node_id int(255) unsigned NOT NULL default '0',
  UNIQUE KEY storable_id (tree_id),
  KEY lft (lft,rght,level),
  KEY node_type (node_type)
) TYPE=MyISAM;

--
-- Dumping data for table `tree`
--

INSERT INTO tree VALUES (1,1,2,0,'',0);

