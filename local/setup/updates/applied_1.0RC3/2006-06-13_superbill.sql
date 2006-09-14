
CREATE TABLE `superbill` (
  `superbill_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `practice_id` int(11) NOT NULL default '0',
  `status` int(11) NOT NULL default '0',
  PRIMARY KEY  (`superbill_id`),
  KEY `practice_id` (`practice_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
        

