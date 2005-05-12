
-- 
-- Table structure for table `superbill_data`
-- 

CREATE TABLE `superbill_data` (
  `superbill_data_id` int(11) NOT NULL default '0',
  `superbill_id` int(11) NOT NULL default '0',
  `code_id` int(11) NOT NULL default '0',
  `status` int(11) NOT NULL default '0',
  PRIMARY KEY  (`superbill_data_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `superbill_data`
-- 

INSERT INTO `superbill_data` VALUES (1000, 1, 0, 1);
        
