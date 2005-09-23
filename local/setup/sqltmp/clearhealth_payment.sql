CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL default '0',
  `foreign_id` int(11) NOT NULL default '0',
  `encounter_id` int(11) NOT NULL default '0',
  `payment_type` int(11) NOT NULL default '0',
  `amount` float(11,2) NOT NULL default '0.00',
  `writeoff` float(11,2) NOT NULL default '0.00',
  `user_id` int(11) NOT NULL default '0',
  `timestamp` timestamp NOT NULL,
  `payer_id` int(11) NOT NULL default '0',
  `payment_date` date NOT NULL default '0000-00-00',
  `title` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`payment_id`),
  KEY `foreign_id` (`foreign_id`)
) TYPE=MyISAM;
CREATE TABLE `payment_claimline` (
  `payment_claimline_id` int(11) NOT NULL default '0',
  `payment_id` int(11) NOT NULL default '0',
  `code_id` int(11) NOT NULL default '0',
  `paid` float(7,2) NOT NULL default '0.00',
  `writeoff` float(7,2) NOT NULL default '0.00',
  `carry` float(7,2) NOT NULL default '0.00',
  PRIMARY KEY  (`payment_claimline_id`)
) TYPE=MyISAM;
