-- 
-- `form` table structure - new system_name field
-- 

CREATE TABLE `form` (
  `form_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `system_name` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`form_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Contains the EMR extending forms STARTWITHDATA';