-- phpMyAdmin SQL Dump
-- version 2.6.1-rc2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Mar 10, 2005 at 11:52 AM
-- Server version: 4.0.23
-- PHP Version: 4.3.10
-- 
-- Database: `clearhealth`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `address`
-- 

CREATE TABLE `address` (
  `address_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `line1` varchar(255) NOT NULL default '',
  `line2` varchar(255) NOT NULL default '',
  `city` varchar(255) NOT NULL default '',
  `region` int(11) NOT NULL default '0',
  `county` int(11) NOT NULL default '0',
  `state` int(11) NOT NULL default '0',
  `postal_code` varchar(255) NOT NULL default '',
  `notes` text NOT NULL,
  PRIMARY KEY  (`address_id`)
) TYPE=MyISAM COMMENT='An address that can be for a company or a person';

-- --------------------------------------------------------

-- 
-- Table structure for table `adodbseq`
-- 

CREATE TABLE `adodbseq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `building_address`
-- 

CREATE TABLE `building_address` (
  `building_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  `address_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`building_id`,`address_id`),
  KEY `address_id` (`address_id`),
  KEY `building_id` (`building_id`)
) TYPE=MyISAM COMMENT='Links a building to a address specifying the address type';

-- --------------------------------------------------------

-- 
-- Table structure for table `buildings`
-- 

CREATE TABLE `buildings` (
  `id` int(11) NOT NULL default '0',
  `description` text NOT NULL,
  `name` varchar(255) NOT NULL default '',
  `practice_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `category`
-- 

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
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `category_to_document`
-- 

CREATE TABLE `category_to_document` (
  `category_id` int(11) NOT NULL default '0',
  `document_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`category_id`,`document_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `codes`
-- 

CREATE TABLE `codes` (
  `code_id` int(11) NOT NULL auto_increment,
  `code_text` varchar(255) default NULL,
  `code_text_short` varchar(24) default NULL,
  `code` varchar(10) default NULL,
  `code_type` tinyint(2) default NULL,
  `modifier` varchar(5) default NULL,
  `units` tinyint(3) default NULL,
  `fee` decimal(7,2) default NULL,
  `superbill` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`code_id`)
) TYPE=MyISAM AUTO_INCREMENT=27493 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `coding_data`
-- 

CREATE TABLE `coding_data` (
  `coding_data_id` int(11) NOT NULL default '0',
  `encounter_id` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `code_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`coding_data_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `company`
-- 

CREATE TABLE `company` (
  `company_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `notes` text NOT NULL,
  `initials` varchar(10) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `is_historic` enum('no','yes') NOT NULL default 'no',
  PRIMARY KEY  (`company_id`)
) TYPE=MyISAM COMMENT='Base Company record most of the data is in linked tables';

-- --------------------------------------------------------

-- 
-- Table structure for table `company_address`
-- 

CREATE TABLE `company_address` (
  `company_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  `address_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`,`address_id`),
  KEY `company_id` (`company_id`),
  KEY `address_id` (`address_id`)
) TYPE=MyISAM COMMENT='Links a company to a address specifying the address type';

-- --------------------------------------------------------

-- 
-- Table structure for table `company_company`
-- 

CREATE TABLE `company_company` (
  `company_id` int(11) NOT NULL default '0',
  `related_company_id` int(11) NOT NULL default '0',
  `company_relation_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`,`related_company_id`),
  KEY `company_id` (`company_id`),
  KEY `related_company_id` (`related_company_id`)
) TYPE=MyISAM COMMENT='Relates a company to another company specify the type with a';

-- --------------------------------------------------------

-- 
-- Table structure for table `company_number`
-- 

CREATE TABLE `company_number` (
  `company_id` int(11) NOT NULL default '0',
  `number_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`,`number_id`),
  KEY `company_id` (`company_id`),
  KEY `number_id` (`number_id`)
) TYPE=MyISAM COMMENT='Links between company and phone_numbers';

-- --------------------------------------------------------

-- 
-- Table structure for table `company_type`
-- 

CREATE TABLE `company_type` (
  `company_id` int(11) NOT NULL default '0',
  `company_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`,`company_type`),
  KEY `company_id` (`company_id`),
  KEY `company_type` (`company_type`)
) TYPE=MyISAM COMMENT='Link to specify company type';

-- --------------------------------------------------------

-- 
-- Table structure for table `countries`
-- 

CREATE TABLE `countries` (
  `countries_name` varchar(64) NOT NULL default '',
  `countries_iso_code_2` char(2) NOT NULL default '',
  `countries_iso_code_3` char(3) NOT NULL default '',
  PRIMARY KEY  (`countries_iso_code_3`),
  KEY `IDX_COUNTRIES_NAME` (`countries_name`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `document`
-- 

CREATE TABLE `document` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `type` enum('file_url','blob','web_url') default NULL,
  `size` int(11) default NULL,
  `date` datetime default NULL,
  `url` varchar(255) default NULL,
  `mimetype` varchar(255) default NULL,
  `pages` int(11) default NULL,
  `owner` int(11) default NULL,
  `revision` timestamp(14) NOT NULL,
  `foreign_id` int(11) default NULL,
  `group_id` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `revision` (`revision`),
  KEY `foreign_id` (`foreign_id`),
  KEY `owner` (`owner`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `enumeration`
-- 

CREATE TABLE `enumeration` (
  `name` varchar(100) NOT NULL default '',
  `title` varchar(100) NOT NULL default '',
  `description` tinytext NOT NULL,
  `gender` enum('Male','Female','Not Specified') NOT NULL default 'Male',
  `company_number_type` enum('Primary','Fax') NOT NULL default 'Primary',
  `quality_of_file` enum('Good','Bad') NOT NULL default 'Good',
  `disposition` enum('New','Waiting','Compete') NOT NULL default 'New',
  `state` enum('Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District of Columbia','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio','Oklahoma','Oregon','Pennsylvania','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virginia','West Virginia','Wisconsin','Wyoming') NOT NULL default 'Alabama',
  `group_list` enum('All','Arizona','California') NOT NULL default 'All',
  `company_type` enum('Insurance') NOT NULL default 'Insurance',
  `assigning` enum('A - Assigned','B - Assigned Lab Services Only','C - Not Assigned','P - Assignment Refused') NOT NULL default 'A - Assigned',
  `relation_of_information_code` enum('A - On file','I - Informed Consent','M - Limited Ability','N - Not allowed','O - On file','Y - Has permission') NOT NULL default 'A - On file',
  `person_type` enum('Patient','Provider','Mid-level','Staff','Subscriber') NOT NULL default 'Patient',
  `provider_number_type` enum('State License') NOT NULL default 'State License',
  `subscriber_to_patient_relationship` enum('Self','Mother','Father') NOT NULL default 'Self',
  `payer_type` enum('medicare') NOT NULL default 'medicare',
  `person_to_person_relation_type` enum('Dependant','Spouse','Grand Parent','Other') NOT NULL default 'Dependant',
  `identifier_type` enum('SSN','EIN') NOT NULL default 'SSN',
  `number_type` enum('Home','Mobile','Work','Emergency') NOT NULL default 'Home',
  `address_type` enum('Home','Billing','Other') NOT NULL default 'Home',
  PRIMARY KEY  (`name`)
) TYPE=MyISAM COMMENT='Each enum stored as a new col, metadata in 1 row per enum';

-- --------------------------------------------------------

-- 
-- Table structure for table `events`
-- 

CREATE TABLE `events` (
  `id` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `website` varchar(255) NOT NULL default '',
  `contact_person` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `foreign_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `fee_schedule`
-- 

CREATE TABLE `fee_schedule` (
  `fee_schedule_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `label` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`fee_schedule_id`),
  UNIQUE KEY `name` (`name`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `fee_schedule_data`
-- 

CREATE TABLE `fee_schedule_data` (
  `code_id` int(11) NOT NULL default '0',
  `revision_id` int(11) NOT NULL default '0',
  `fee_schedule_id` int(11) NOT NULL default '0',
  `data` double NOT NULL default '0',
  `formula` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`code_id`,`revision_id`,`fee_schedule_id`),
  KEY `fee_schedule_id` (`fee_schedule_id`),
  KEY `revision_id` (`revision_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `fee_schedule_revision`
-- 

CREATE TABLE `fee_schedule_revision` (
  `revision_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `update_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`revision_id`),
  KEY `user_id` (`user_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `form`
-- 

CREATE TABLE `form` (
  `form_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`form_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `form_data`
-- 

CREATE TABLE `form_data` (
  `form_data_id` int(11) NOT NULL default '0',
  `form_id` int(11) NOT NULL default '0',
  `external_id` int(11) NOT NULL default '0',
  `last_edit` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`form_data_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_acl`
-- 

CREATE TABLE `gacl_acl` (
  `id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default 'system',
  `allow` int(11) NOT NULL default '0',
  `enabled` int(11) NOT NULL default '0',
  `return_value` longtext,
  `note` longtext,
  `updated_date` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `gacl_enabled_acl` (`enabled`),
  KEY `gacl_section_value_acl` (`section_value`),
  KEY `gacl_updated_date_acl` (`updated_date`)
) TYPE=MyISAM COMMENT='ACL Table';

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_acl_sections`
-- 

CREATE TABLE `gacl_acl_sections` (
  `id` int(11) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(230) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_value_acl_sections` (`value`),
  KEY `gacl_hidden_acl_sections` (`hidden`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_acl_seq`
-- 

CREATE TABLE `gacl_acl_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aco`
-- 

CREATE TABLE `gacl_aco` (
  `id` int(11) NOT NULL default '0',
  `section_value` varchar(240) NOT NULL default '0',
  `value` varchar(240) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_section_value_value_aco` (`section_value`,`value`),
  KEY `gacl_hidden_aco` (`hidden`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aco_map`
-- 

CREATE TABLE `gacl_aco_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aco_sections`
-- 

CREATE TABLE `gacl_aco_sections` (
  `id` int(11) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(230) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_value_aco_sections` (`value`),
  KEY `gacl_hidden_aco_sections` (`hidden`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aco_sections_seq`
-- 

CREATE TABLE `gacl_aco_sections_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aco_seq`
-- 

CREATE TABLE `gacl_aco_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro`
-- 

CREATE TABLE `gacl_aro` (
  `id` int(11) NOT NULL default '0',
  `section_value` varchar(240) NOT NULL default '0',
  `value` varchar(240) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_section_value_value_aro` (`section_value`,`value`),
  KEY `gacl_hidden_aro` (`hidden`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_groups`
-- 

CREATE TABLE `gacl_aro_groups` (
  `id` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `lft` int(11) NOT NULL default '0',
  `rgt` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`,`value`),
  UNIQUE KEY `gacl_value_aro_groups` (`value`),
  KEY `gacl_parent_id_aro_groups` (`parent_id`),
  KEY `gacl_lft_rgt_aro_groups` (`lft`,`rgt`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_groups_id_seq`
-- 

CREATE TABLE `gacl_aro_groups_id_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_groups_map`
-- 

CREATE TABLE `gacl_aro_groups_map` (
  `acl_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acl_id`,`group_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_map`
-- 

CREATE TABLE `gacl_aro_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_sections`
-- 

CREATE TABLE `gacl_aro_sections` (
  `id` int(11) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(230) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_value_aro_sections` (`value`),
  KEY `gacl_hidden_aro_sections` (`hidden`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_sections_seq`
-- 

CREATE TABLE `gacl_aro_sections_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_seq`
-- 

CREATE TABLE `gacl_aro_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo`
-- 

CREATE TABLE `gacl_axo` (
  `id` int(11) NOT NULL default '0',
  `section_value` varchar(240) NOT NULL default '0',
  `value` varchar(240) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_section_value_value_axo` (`section_value`,`value`),
  KEY `gacl_hidden_axo` (`hidden`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_groups`
-- 

CREATE TABLE `gacl_axo_groups` (
  `id` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `lft` int(11) NOT NULL default '0',
  `rgt` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`,`value`),
  UNIQUE KEY `gacl_value_axo_groups` (`value`),
  KEY `gacl_parent_id_axo_groups` (`parent_id`),
  KEY `gacl_lft_rgt_axo_groups` (`lft`,`rgt`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_groups_id_seq`
-- 

CREATE TABLE `gacl_axo_groups_id_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_groups_map`
-- 

CREATE TABLE `gacl_axo_groups_map` (
  `acl_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acl_id`,`group_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_map`
-- 

CREATE TABLE `gacl_axo_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_sections`
-- 

CREATE TABLE `gacl_axo_sections` (
  `id` int(11) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  `order_value` int(11) NOT NULL default '0',
  `name` varchar(230) NOT NULL default '',
  `hidden` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `gacl_value_axo_sections` (`value`),
  KEY `gacl_hidden_axo_sections` (`hidden`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_sections_seq`
-- 

CREATE TABLE `gacl_axo_sections_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_seq`
-- 

CREATE TABLE `gacl_axo_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_groups_aro_map`
-- 

CREATE TABLE `gacl_groups_aro_map` (
  `group_id` int(11) NOT NULL default '0',
  `aro_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`aro_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_groups_axo_map`
-- 

CREATE TABLE `gacl_groups_axo_map` (
  `group_id` int(11) NOT NULL default '0',
  `axo_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`axo_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_phpgacl`
-- 

CREATE TABLE `gacl_phpgacl` (
  `name` varchar(230) NOT NULL default '',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`name`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `groups`
-- 

CREATE TABLE `groups` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `identifier`
-- 

CREATE TABLE `identifier` (
  `identifier_id` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  `identifier` varchar(100) NOT NULL default '',
  `identifier_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`identifier_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `insurance_program`
-- 

CREATE TABLE `insurance_program` (
  `insurance_program_id` int(11) NOT NULL default '0',
  `payer_type` int(11) NOT NULL default '0',
  `company_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`insurance_program_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `insured_relationship`
-- 

CREATE TABLE `insured_relationship` (
  `insured_relationship_id` int(11) NOT NULL default '0',
  `insurance_program_id` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  `subsciber_id` int(11) NOT NULL default '0',
  `subscriber_to_patient_relationship` int(11) NOT NULL default '0',
  `copay` float(11,2) NOT NULL default '0.00',
  `assigning` int(11) NOT NULL default '0',
  `group_name` varchar(100) NOT NULL default '',
  `group_number` varchar(100) NOT NULL default '',
  `default_provider` int(11) NOT NULL default '0',
  PRIMARY KEY  (`insured_relationship_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `menu_report`
-- 

CREATE TABLE `menu_report` (
  `menu_report_id` int(11) NOT NULL default '0',
  `menu_id` int(11) NOT NULL default '0',
  `report_template_id` int(11) NOT NULL default '0',
  `title` varchar(50) NOT NULL default '',
  `custom_action` varchar(255) default NULL,
  PRIMARY KEY  (`menu_report_id`),
  KEY `menu_id` (`menu_id`),
  KEY `report_template_id` (`report_template_id`)
) TYPE=InnoDB;

-- --------------------------------------------------------

-- 
-- Table structure for table `name_history`
-- 

CREATE TABLE `name_history` (
  `name_history_id` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  `first_name` varchar(100) NOT NULL default '',
  `last_name` varchar(100) NOT NULL default '',
  `middle_name` varchar(50) NOT NULL default '',
  `update_date` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`name_history_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `note`
-- 

CREATE TABLE `note` (
  `id` int(11) NOT NULL default '0',
  `foreign_id` int(11) NOT NULL default '0',
  `note` varchar(255) default NULL,
  `owner` int(11) default NULL,
  `date` datetime default NULL,
  `revision` timestamp(14) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `foreign_id` (`owner`),
  KEY `foreign_id_2` (`foreign_id`),
  KEY `date` (`date`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `number`
-- 

CREATE TABLE `number` (
  `number_id` int(11) NOT NULL default '0',
  `number_type` int(11) NOT NULL default '0',
  `notes` tinytext NOT NULL,
  `number` varchar(100) NOT NULL default '',
  `active` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`number_id`)
) TYPE=MyISAM COMMENT='A phone number';

-- --------------------------------------------------------

-- 
-- Table structure for table `occurences`
-- 

CREATE TABLE `occurences` (
  `id` int(11) NOT NULL default '0',
  `event_id` int(11) NOT NULL default '0',
  `start` datetime NOT NULL default '0000-00-00 00:00:00',
  `end` datetime NOT NULL default '0000-00-00 00:00:00',
  `notes` varchar(255) NOT NULL default '',
  `location_id` int(11) NOT NULL default '0',
  `user_id` int(11) default NULL,
  `last_change_id` int(11) default NULL,
  `external_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `ownership`
-- 

CREATE TABLE `ownership` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `id` (`id`)
) TYPE=MyISAM COMMENT='Stores which items are owned by which user';

-- --------------------------------------------------------

-- 
-- Table structure for table `patient`
-- 

CREATE TABLE `patient` (
  `person_id` int(11) NOT NULL default '0',
  `is_default_provider_primary` int(11) NOT NULL default '0',
  `default_provider` int(11) NOT NULL default '0',
  `record_number` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`)
) TYPE=MyISAM COMMENT='An patient extends the person entity';

-- --------------------------------------------------------

-- 
-- Table structure for table `person`
-- 

CREATE TABLE `person` (
  `person_id` int(11) NOT NULL default '0',
  `salutation` varchar(20) NOT NULL default '',
  `last_name` varchar(100) NOT NULL default '',
  `first_name` varchar(100) NOT NULL default '',
  `middle_name` varchar(50) NOT NULL default '',
  `gender` int(11) NOT NULL default '0',
  `initials` varchar(10) NOT NULL default '',
  `date_of_birth` date NOT NULL default '0000-00-00',
  `summary` varchar(100) NOT NULL default '',
  `title` varchar(100) NOT NULL default '',
  `notes` text NOT NULL,
  `email` varchar(100) NOT NULL default '',
  `secondary_email` varchar(100) NOT NULL default '',
  `has_photo` enum('0','1') NOT NULL default '0',
  `identifier` varchar(100) NOT NULL default '',
  `identifier_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`)
) TYPE=MyISAM COMMENT='A person in the system';

-- --------------------------------------------------------

-- 
-- Table structure for table `person_address`
-- 

CREATE TABLE `person_address` (
  `person_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  `address_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`,`address_id`),
  KEY `address_id` (`address_id`),
  KEY `person_id` (`person_id`)
) TYPE=MyISAM COMMENT='Links a person to a address specifying the address type';

-- --------------------------------------------------------

-- 
-- Table structure for table `person_company`
-- 

CREATE TABLE `person_company` (
  `person_id` int(11) NOT NULL default '0',
  `company_id` int(11) NOT NULL default '0',
  `person_type` int(11) default NULL,
  PRIMARY KEY  (`person_id`,`company_id`),
  KEY `person_id` (`person_id`),
  KEY `company_id` (`company_id`)
) TYPE=MyISAM COMMENT='Links a person to a company and optionaly specifies the lin';

-- --------------------------------------------------------

-- 
-- Table structure for table `person_number`
-- 

CREATE TABLE `person_number` (
  `person_id` int(11) NOT NULL default '0',
  `number_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`,`number_id`),
  KEY `person_id` (`person_id`),
  KEY `phone_id` (`number_id`)
) TYPE=MyISAM COMMENT='Links between people and phone_numbers';

-- --------------------------------------------------------

-- 
-- Table structure for table `person_person`
-- 

CREATE TABLE `person_person` (
  `person_person_id` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  `related_person_id` int(11) NOT NULL default '0',
  `relation_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_person_id`),
  UNIQUE KEY `person_id` (`person_id`,`related_person_id`,`relation_type`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `person_type`
-- 

CREATE TABLE `person_type` (
  `person_id` int(11) NOT NULL default '0',
  `person_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`,`person_type`),
  KEY `person_id` (`person_id`),
  KEY `person_type` (`person_type`)
) TYPE=MyISAM COMMENT='Link to specify person type';

-- --------------------------------------------------------

-- 
-- Table structure for table `practice_address`
-- 

CREATE TABLE `practice_address` (
  `practice_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  `address_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`practice_id`,`address_id`),
  KEY `address_id` (`address_id`),
  KEY `practice_id` (`practice_id`)
) TYPE=MyISAM COMMENT='Links a practice to a address specifying the address type';

-- --------------------------------------------------------

-- 
-- Table structure for table `practices`
-- 

CREATE TABLE `practices` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `website` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `preferences`
-- 

CREATE TABLE `preferences` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  `parent` int(11) NOT NULL default '0',
  `lft` int(11) NOT NULL default '0',
  `rght` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `parent` (`parent`),
  KEY `lft` (`lft`,`rght`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `provider`
-- 

CREATE TABLE `provider` (
  `person_id` int(11) NOT NULL default '0',
  `state_license_number` varchar(100) NOT NULL default '',
  `clia_number` varchar(100) NOT NULL default '',
  `dea_number` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`person_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `provider_to_insurance`
-- 

CREATE TABLE `provider_to_insurance` (
  `provider_to_insurance_id` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  `insurance_program_id` int(11) NOT NULL default '0',
  `provider_number` varchar(100) NOT NULL default '',
  `provider_number_type` int(11) NOT NULL default '0',
  `group_number` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`provider_to_insurance_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `record_sequence`
-- 

CREATE TABLE `record_sequence` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

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
) TYPE=MyISAM COMMENT='Report templates';

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
) TYPE=MyISAM COMMENT='Report definitions TODO: change to Generic Seq' AUTO_INCREMENT=792 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `rooms`
-- 

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL default '0',
  `description` text NOT NULL,
  `number_seats` int(11) NOT NULL default '0',
  `building_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `schedules`
-- 

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL default '0',
  `schedule_code` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `description_long` text NOT NULL,
  `description_short` text NOT NULL,
  `practice_id` int(11) NOT NULL default '0',
  `user_id` int(11) default NULL,
  `room_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `sequences`
-- 

CREATE TABLE `sequences` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `states`
-- 

CREATE TABLE `states` (
  `zone_code` varchar(32) NOT NULL default '',
  `zone_name` varchar(32) NOT NULL default '',
  `country` char(3) default NULL,
  PRIMARY KEY  (`zone_code`,`zone_name`),
  KEY `country` (`country`),
  KEY `zone_code` (`zone_code`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `storage_date`
-- 

CREATE TABLE `storage_date` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(50) NOT NULL default '',
  `value` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`foreign_key`,`value_key`)
) TYPE=MyISAM COMMENT='Generic way to store date values';

-- --------------------------------------------------------

-- 
-- Table structure for table `storage_int`
-- 

CREATE TABLE `storage_int` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(50) NOT NULL default '',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`foreign_key`,`value_key`)
) TYPE=MyISAM COMMENT='Generic way to store integer values (also boolean)';

-- --------------------------------------------------------

-- 
-- Table structure for table `storage_string`
-- 

CREATE TABLE `storage_string` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(50) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`foreign_key`,`value_key`)
) TYPE=MyISAM COMMENT='Generic way to string values';

-- --------------------------------------------------------

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

-- --------------------------------------------------------

-- 
-- Table structure for table `user`
-- 

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL default '0',
  `username` varchar(55) NOT NULL default '',
  `password` varchar(255) NOT NULL default '',
  `nickname` varchar(255) NOT NULL default '',
  `color` varchar(255) NOT NULL default '',
  `person_id` int(11) default NULL,
  `disabled` enum('yes','no') NOT NULL default 'yes',
  `default_location_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `username` (`username`),
  KEY `person_id` (`person_id`)
) TYPE=MyISAM COMMENT='Users in the System';

-- --------------------------------------------------------

-- 
-- Table structure for table `users_groups`
-- 

CREATE TABLE `users_groups` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  `foreign_id` int(11) NOT NULL default '0',
  `table` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_id` (`user_id`,`group_id`,`foreign_id`,`table`)
) TYPE=MyISAM;
