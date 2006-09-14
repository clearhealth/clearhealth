
CREATE TABLE `code_to_category` (
  `code_category_id` int(11) NOT NULL default '0',
  `code_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`code_category_id`,`code_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
        

