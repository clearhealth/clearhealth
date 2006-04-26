-- 
-- Table structure for table `visit_queue`
-- 

CREATE TABLE `visit_queue` (
  `visit_queue_id` int(11) NOT NULL default '0',
  `visit_queue_template_id` int(11) NOT NULL default '0',
  `provider_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`visit_queue_id`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `visit_queue_reason`
-- 

CREATE TABLE `visit_queue_reason` (
  `visit_queue_reason_id` int(11) NOT NULL auto_increment,
  `ordernum` int(11) NOT NULL default '0',
  `appt_length` TIME DEFAULT '1:00' NOT NULL,
  `reason` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`visit_queue_reason_id`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `visit_queue_template`
-- 

CREATE TABLE `visit_queue_template` (
  `visit_queue_template_id` int(11) NOT NULL auto_increment,
  `number_of_appointments` int(11) NOT NULL default '0',
  `visit_queue_reason_id` int(11) NOT NULL default '0',
  `visit_queue_rule_id` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`visit_queue_template_id`)
);
