-- phpMyAdmin SQL Dump
-- version 2.6.1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: May 03, 2005 at 12:11 PM
-- Server version: 4.1.10
-- PHP Version: 4.3.10
-- 
-- Database: `clearhealth`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `report_templates`
-- 

CREATE TABLE `report_templates` (
  `report_template_id` int(11) NOT NULL default '0',
  `report_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `is_default` enum('yes','no') NOT NULL default 'yes',
  PRIMARY KEY  (`report_template_id`),
  KEY `report_id` (`report_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Report templates';

-- 
-- Dumping data for table `report_templates`
-- 

INSERT INTO `report_templates` VALUES (201803, 17857, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (17077, 17075, 'Invoice View', 'no');
INSERT INTO `report_templates` VALUES (17859, 17857, 'MCC Superbill Form', 'no');

-- --------------------------------------------------------

-- 
-- Table structure for table `reports`
-- 

CREATE TABLE `reports` (
  `id` int(11) NOT NULL auto_increment,
  `dbase` varchar(255) NOT NULL default '',
  `user` varchar(255) NOT NULL default '',
  `label` varchar(255) NOT NULL default '',
  `query` text NOT NULL,
  `description` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Report definitions TODO: change to Generic Seq';

-- 
-- Dumping data for table `reports`
-- 

INSERT INTO `reports` VALUES (8, '', '', 'User List', 'select * from user where user_id = [user_id]', '');
INSERT INTO `reports` VALUES (791, '', '', 'Codes with Fee Schedule', 'select code, code_text, data as fee from codes c inner join fee_schedule_data fsd using(code_id)', 'Codes that have had a feed added to them');
INSERT INTO `reports` VALUES (8168, '', '', 'Multi-query test', '---[users]---\r\nselect * from user\r\n---[reports]---\r\nselect * from reports', '');
INSERT INTO `reports` VALUES (8182, '', '', 'Sub Query test', 'select * from encounter where treating_person_id = ''[provider:query-select p.person_id, concat_ws('' '',last_name,first_name) name from person p inner join person_type using(person_id) where person_type = 2]''', '');
INSERT INTO `reports` VALUES (17075, '', '', 'Exit Report', '---[practice]---\r\nselect \r\n p.name,\r\n a.line1,\r\n a.line2,\r\n a.city,\r\n a.state,\r\n a.postal_code\r\nfrom practices p \r\ninner join buildings b on p.id = b.practice_id\r\ninner join encounter e on b.id = e.building_id\r\nleft join practice_address pa on p.id = pa.practice_id\r\nleft join address a using(address_id)\r\nwhere address_type = 4 and e.encounter_id = ''[encounter_id:GET]''\r\n---[treating_facility]---\r\nselect \r\n b.name,\r\n a.line1,\r\n a.line2,\r\n a.city,\r\n a.state,\r\n a.postal_code\r\nfrom buildings b\r\ninner join encounter e on b.id = e.building_id\r\nleft join building_address ba on b.id = ba.building_id\r\nleft join address a using(address_id)\r\nwhere e.encounter_id = ''[encounter_id:GET]''\r\n---[treating_provider]---\r\nselect \r\n per.salutation,\r\n per.last_name,\r\n per.first_name,\r\n p.state_license_number,\r\n a.line1,\r\n a.line2,\r\n a.city,\r\n a.state,\r\n a.postal_code,\r\n n.number\r\n\r\nfrom provider p\r\ninner join person per using(person_id)\r\ninner join encounter e on p.person_id = e.treating_person_id\r\nleft join person_address pa on p.person_id = pa.person_id\r\nleft join address a using(address_id)\r\nleft join person_number pn on p.person_id = pn.person_id\r\nleft join number n using(number_id)\r\nwhere n.number_type = 1 and address_type =1  and e.encounter_id = ''[encounter_id:GET]''\r\n---[patient]---\r\nselect * from person p\r\ninner join patient pat using(person_id)\r\ninner join encounter e on p.person_id = e.patient_id\r\nleft join person_address pa on p.person_id = pa.person_id\r\nleft join address a using(address_id)\r\nleft join person_number pn on p.person_id = pn.person_id\r\nleft join number n using(number_id)\r\nwhere n.number_type = 1 and address_type =1  and e.encounter_id = ''[encounter_id:GET]''\r\n---[code_list]--- \r\nselect cpt.code_text `Procedure`, cpt.code Code, \r\nconcat_ws('', ''\r\n,max(case code_order when 1 then c.code else null end) \r\n,max(case code_order when 2 then c.code else null end)\r\n,max(case code_order when 3 then c.code else null end)\r\n,max(case code_order when 4 then c.code else null end) \r\n) Diagnosis, cd.modifier, cd.units, cd.fee\r\nfrom coding_data cd\r\ninner join codes c using(code_id)\r\ninner join codes cpt on cd.parent_id = cpt.code_id\r\ninner join encounter e on cd.foreign_id = e.encounter_id\r\nwhere e.encounter_id = ''[encounter_id:GET]''\r\ngroup by cd.parent_id\r\nunion\r\nselect ''Total'','''','''',null,sum(units),sum(fee)\r\nfrom coding_data cd\r\nwhere foreign_id = ''[encounter_id:GET]'' and primary_code = 1\r\n---[payment_history]---\r\nselect \r\npayment_date, amount, payment_type\r\nfrom payment\r\nwhere encounter_id = ''[encounter_id:GET]''\r\n---[encounter]---\r\nselect * from encounter e where e.encounter_id = ''[encounter_id:GET]''', '');
INSERT INTO `reports` VALUES (17857, '', '', 'Superbill Form', '---[practice]---\r\nselect \r\n p.name,\r\n a.line1,\r\n a.line2,\r\n a.city,\r\n a.state,\r\n a.postal_code\r\nfrom practices p \r\ninner join buildings b on p.id = b.practice_id\r\ninner join encounter e on b.id = e.building_id\r\nleft join practice_address pa on p.id = pa.practice_id\r\nleft join address a using(address_id)\r\nwhere address_type = 4 and e.encounter_id = ''[encounter_id:GET]''\r\n---[treating_facility]---\r\nselect \r\n b.name,\r\n a.line1,\r\n a.line2,\r\n a.city,\r\n a.state,\r\n a.postal_code\r\nfrom buildings b\r\ninner join encounter e on b.id = e.building_id\r\nleft join building_address ba on b.id = ba.building_id\r\nleft join address a using(address_id)\r\nwhere e.encounter_id = ''[encounter_id:GET]''\r\n---[treating_provider]---\r\nselect \r\n per.salutation,\r\n per.last_name,\r\n per.first_name,\r\n p.state_license_number,\r\n a.line1,\r\n a.line2,\r\n a.city,\r\n a.state,\r\n a.postal_code,\r\n n.number\r\n\r\nfrom provider p\r\ninner join person per using(person_id)\r\ninner join encounter e on p.person_id = e.treating_person_id\r\nleft join person_address pa on p.person_id = pa.person_id\r\nleft join address a using(address_id)\r\nleft join person_number pn on p.person_id = pn.person_id\r\nleft join number n using(number_id)\r\nwhere  e.encounter_id = ''[encounter_id:GET]'' limit 1\r\n---[patient]---\r\nselect * from person p\r\ninner join patient pat using(person_id)\r\ninner join encounter e on p.person_id = e.patient_id\r\nleft join person_address pa on p.person_id = pa.person_id\r\nleft join address a using(address_id)\r\nleft join person_number pn on p.person_id = pn.person_id\r\nleft join number n using(number_id)\r\nwhere n.number_type = 1 and address_type =1  and e.encounter_id = ''[encounter_id:GET]''\r\n\r\n---[encounter]---\r\nselect * from encounter e where e.encounter_id = ''[encounter_id:GET]''', 'Superbill Intake Form');
        
