-- phpMyAdmin SQL Dump
-- version 2.6.1-rc2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Mar 09, 2005 at 01:46 PM
-- Server version: 4.0.23
-- PHP Version: 4.3.10

SET FOREIGN_KEY_CHECKS=0;

SET AUTOCOMMIT=0;
START TRANSACTION;

-- 
-- Database: `clearhealth`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `address`
-- 

DROP TABLE IF EXISTS `address`;
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
) TYPE=InnoDB COMMENT='An address that can be for a company or a person';

-- 
-- Dumping data for table `address`
-- 

INSERT INTO `address` VALUES (771, 'Home', '123 E Main St', 'Suite 2', 'Mesa', 0, 0, 2, '85205', 'use this address');
INSERT INTO `address` VALUES (773, '', '', '', '', 0, 0, 0, '', '');
INSERT INTO `address` VALUES (774, 'Home', '123 E Main St', 'Suite 2', 'Mesa', 0, 0, 2, '85205', 'use this address');
INSERT INTO `address` VALUES (776, '', '', '', '', 0, 0, 0, '', '');
INSERT INTO `address` VALUES (777, 'Home', '123 E Main St', 'Suite 2', 'Mesa', 0, 0, 2, '85205', 'use this address');
INSERT INTO `address` VALUES (779, '', '', '', '', 0, 0, 0, '', '');
INSERT INTO `address` VALUES (780, 'Home', '123 E Main', 'Suite 2', 'Mesa', 0, 0, 2, '85205', 'use this address');
INSERT INTO `address` VALUES (782, '', '', '', '', 0, 0, 0, '', '');
INSERT INTO `address` VALUES (783, 'Other', '323 Soth St', '', 'Mesa', 0, 0, 2, '85210', '');
INSERT INTO `address` VALUES (957, 'main', 'test', 'test blah', 'blah', 0, 0, 1, '123', 'blah');

-- --------------------------------------------------------

-- 
-- Table structure for table `adodbseq`
-- 

DROP TABLE IF EXISTS `adodbseq`;
CREATE TABLE `adodbseq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `adodbseq`
-- 

INSERT INTO `adodbseq` VALUES (1);

-- --------------------------------------------------------

-- 
-- Table structure for table `building_address`
-- 

DROP TABLE IF EXISTS `building_address`;
CREATE TABLE `building_address` (
  `building_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  `address_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`building_id`,`address_id`),
  KEY `address_id` (`address_id`),
  KEY `building_id` (`building_id`)
) TYPE=InnoDB COMMENT='Links a building to a address specifying the address type';

-- 
-- Dumping data for table `building_address`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `buildings`
-- 

DROP TABLE IF EXISTS `buildings`;
CREATE TABLE `buildings` (
  `id` int(11) NOT NULL default '0',
  `description` text NOT NULL,
  `name` varchar(255) NOT NULL default '',
  `practice_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `buildings`
-- 

INSERT INTO `buildings` VALUES (12, '', 'Main Office', 2);
INSERT INTO `buildings` VALUES (620, '', 'North County', 2);

-- --------------------------------------------------------

-- 
-- Table structure for table `category`
-- 

DROP TABLE IF EXISTS `category`;
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

-- 
-- Dumping data for table `category`
-- 

INSERT INTO `category` VALUES (1, 'ClearHealth', '', 0, 0, 6);
INSERT INTO `category` VALUES (991, 'Test', '', 1, 0, 3);
INSERT INTO `category` VALUES (992, 'Test2', '', 1, 4, 5);
INSERT INTO `category` VALUES (993, 'Sub Category', '', 991, 1, 2);

-- --------------------------------------------------------

-- 
-- Table structure for table `category_to_document`
-- 

DROP TABLE IF EXISTS `category_to_document`;
CREATE TABLE `category_to_document` (
  `category_id` int(11) NOT NULL default '0',
  `document_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`category_id`,`document_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `category_to_document`
-- 

INSERT INTO `category_to_document` VALUES (993, 996);

-- --------------------------------------------------------

-- 
-- Table structure for table `coding_data`
-- 

DROP TABLE IF EXISTS `coding_data`;
CREATE TABLE `coding_data` (
  `coding_data_id` int(11) NOT NULL default '0',
  `encounter_id` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `code_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`coding_data_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `coding_data`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `company`
-- 

DROP TABLE IF EXISTS `company`;
CREATE TABLE `company` (
  `company_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `notes` text NOT NULL,
  `initials` varchar(10) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `is_historic` enum('no','yes') NOT NULL default 'no',
  PRIMARY KEY  (`company_id`)
) TYPE=InnoDB COMMENT='Base Company record most of the data is in linked tables';

-- 
-- Dumping data for table `company`
-- 

INSERT INTO `company` VALUES (968, 'Test', '', '', '', '', 'no');

-- --------------------------------------------------------

-- 
-- Table structure for table `company_address`
-- 

DROP TABLE IF EXISTS `company_address`;
CREATE TABLE `company_address` (
  `company_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  `address_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`,`address_id`),
  KEY `company_id` (`company_id`),
  KEY `address_id` (`address_id`)
) TYPE=InnoDB COMMENT='Links a company to a address specifying the address type';

-- 
-- Dumping data for table `company_address`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `company_company`
-- 

DROP TABLE IF EXISTS `company_company`;
CREATE TABLE `company_company` (
  `company_id` int(11) NOT NULL default '0',
  `related_company_id` int(11) NOT NULL default '0',
  `company_relation_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`,`related_company_id`),
  KEY `company_id` (`company_id`),
  KEY `related_company_id` (`related_company_id`)
) TYPE=InnoDB COMMENT='Relates a company to another company specify the type with a';

-- 
-- Dumping data for table `company_company`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `company_number`
-- 

DROP TABLE IF EXISTS `company_number`;
CREATE TABLE `company_number` (
  `company_id` int(11) NOT NULL default '0',
  `number_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`,`number_id`),
  KEY `company_id` (`company_id`),
  KEY `number_id` (`number_id`)
) TYPE=InnoDB COMMENT='Links between company and phone_numbers';

-- 
-- Dumping data for table `company_number`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `company_type`
-- 

DROP TABLE IF EXISTS `company_type`;
CREATE TABLE `company_type` (
  `company_id` int(11) NOT NULL default '0',
  `company_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`,`company_type`),
  KEY `company_id` (`company_id`),
  KEY `company_type` (`company_type`)
) TYPE=InnoDB COMMENT='Link to specify company type';

-- 
-- Dumping data for table `company_type`
-- 

INSERT INTO `company_type` VALUES (968, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `countries`
-- 

DROP TABLE IF EXISTS `countries`;
CREATE TABLE `countries` (
  `countries_name` varchar(64) NOT NULL default '',
  `countries_iso_code_2` char(2) NOT NULL default '',
  `countries_iso_code_3` char(3) NOT NULL default '',
  PRIMARY KEY  (`countries_iso_code_3`),
  KEY `IDX_COUNTRIES_NAME` (`countries_name`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `countries`
-- 

INSERT INTO `countries` VALUES ('Afghanistan', 'AF', 'AFG');
INSERT INTO `countries` VALUES ('Albania', 'AL', 'ALB');
INSERT INTO `countries` VALUES ('Algeria', 'DZ', 'DZA');
INSERT INTO `countries` VALUES ('American Samoa', 'AS', 'ASM');
INSERT INTO `countries` VALUES ('Andorra', 'AD', 'AND');
INSERT INTO `countries` VALUES ('Angola', 'AO', 'AGO');
INSERT INTO `countries` VALUES ('Anguilla', 'AI', 'AIA');
INSERT INTO `countries` VALUES ('Antarctica', 'AQ', 'ATA');
INSERT INTO `countries` VALUES ('Antigua and Barbuda', 'AG', 'ATG');
INSERT INTO `countries` VALUES ('Argentina', 'AR', 'ARG');
INSERT INTO `countries` VALUES ('Armenia', 'AM', 'ARM');
INSERT INTO `countries` VALUES ('Aruba', 'AW', 'ABW');
INSERT INTO `countries` VALUES ('Australia', 'AU', 'AUS');
INSERT INTO `countries` VALUES ('Austria', 'AT', 'AUT');
INSERT INTO `countries` VALUES ('Azerbaijan', 'AZ', 'AZE');
INSERT INTO `countries` VALUES ('Bahamas', 'BS', 'BHS');
INSERT INTO `countries` VALUES ('Bahrain', 'BH', 'BHR');
INSERT INTO `countries` VALUES ('Bangladesh', 'BD', 'BGD');
INSERT INTO `countries` VALUES ('Barbados', 'BB', 'BRB');
INSERT INTO `countries` VALUES ('Belarus', 'BY', 'BLR');
INSERT INTO `countries` VALUES ('Belgium', 'BE', 'BEL');
INSERT INTO `countries` VALUES ('Belize', 'BZ', 'BLZ');
INSERT INTO `countries` VALUES ('Benin', 'BJ', 'BEN');
INSERT INTO `countries` VALUES ('Bermuda', 'BM', 'BMU');
INSERT INTO `countries` VALUES ('Bhutan', 'BT', 'BTN');
INSERT INTO `countries` VALUES ('Bolivia', 'BO', 'BOL');
INSERT INTO `countries` VALUES ('Bosnia and Herzegowina', 'BA', 'BIH');
INSERT INTO `countries` VALUES ('Botswana', 'BW', 'BWA');
INSERT INTO `countries` VALUES ('Bouvet Island', 'BV', 'BVT');
INSERT INTO `countries` VALUES ('Brazil', 'BR', 'BRA');
INSERT INTO `countries` VALUES ('British Indian Ocean Territory', 'IO', 'IOT');
INSERT INTO `countries` VALUES ('Brunei Darussalam', 'BN', 'BRN');
INSERT INTO `countries` VALUES ('Bulgaria', 'BG', 'BGR');
INSERT INTO `countries` VALUES ('Burkina Faso', 'BF', 'BFA');
INSERT INTO `countries` VALUES ('Burundi', 'BI', 'BDI');
INSERT INTO `countries` VALUES ('Cambodia', 'KH', 'KHM');
INSERT INTO `countries` VALUES ('Cameroon', 'CM', 'CMR');
INSERT INTO `countries` VALUES ('Canada', 'CA', 'CAN');
INSERT INTO `countries` VALUES ('Cape Verde', 'CV', 'CPV');
INSERT INTO `countries` VALUES ('Cayman Islands', 'KY', 'CYM');
INSERT INTO `countries` VALUES ('Central African Republic', 'CF', 'CAF');
INSERT INTO `countries` VALUES ('Chad', 'TD', 'TCD');
INSERT INTO `countries` VALUES ('Chile', 'CL', 'CHL');
INSERT INTO `countries` VALUES ('China', 'CN', 'CHN');
INSERT INTO `countries` VALUES ('Christmas Island', 'CX', 'CXR');
INSERT INTO `countries` VALUES ('Cocos (Keeling) Islands', 'CC', 'CCK');
INSERT INTO `countries` VALUES ('Colombia', 'CO', 'COL');
INSERT INTO `countries` VALUES ('Comoros', 'KM', 'COM');
INSERT INTO `countries` VALUES ('Congo', 'CG', 'COG');
INSERT INTO `countries` VALUES ('Cook Islands', 'CK', 'COK');
INSERT INTO `countries` VALUES ('Costa Rica', 'CR', 'CRI');
INSERT INTO `countries` VALUES ('Cote D''Ivoire', 'CI', 'CIV');
INSERT INTO `countries` VALUES ('Croatia', 'HR', 'HRV');
INSERT INTO `countries` VALUES ('Cuba', 'CU', 'CUB');
INSERT INTO `countries` VALUES ('Cyprus', 'CY', 'CYP');
INSERT INTO `countries` VALUES ('Czech Republic', 'CZ', 'CZE');
INSERT INTO `countries` VALUES ('Denmark', 'DK', 'DNK');
INSERT INTO `countries` VALUES ('Djibouti', 'DJ', 'DJI');
INSERT INTO `countries` VALUES ('Dominica', 'DM', 'DMA');
INSERT INTO `countries` VALUES ('Dominican Republic', 'DO', 'DOM');
INSERT INTO `countries` VALUES ('East Timor', 'TP', 'TMP');
INSERT INTO `countries` VALUES ('Ecuador', 'EC', 'ECU');
INSERT INTO `countries` VALUES ('Egypt', 'EG', 'EGY');
INSERT INTO `countries` VALUES ('El Salvador', 'SV', 'SLV');
INSERT INTO `countries` VALUES ('Equatorial Guinea', 'GQ', 'GNQ');
INSERT INTO `countries` VALUES ('Eritrea', 'ER', 'ERI');
INSERT INTO `countries` VALUES ('Estonia', 'EE', 'EST');
INSERT INTO `countries` VALUES ('Ethiopia', 'ET', 'ETH');
INSERT INTO `countries` VALUES ('Falkland Islands (Malvinas)', 'FK', 'FLK');
INSERT INTO `countries` VALUES ('Faroe Islands', 'FO', 'FRO');
INSERT INTO `countries` VALUES ('Fiji', 'FJ', 'FJI');
INSERT INTO `countries` VALUES ('Finland', 'FI', 'FIN');
INSERT INTO `countries` VALUES ('France', 'FR', 'FRA');
INSERT INTO `countries` VALUES ('France, Metropolitan', 'FX', 'FXX');
INSERT INTO `countries` VALUES ('French Guiana', 'GF', 'GUF');
INSERT INTO `countries` VALUES ('French Polynesia', 'PF', 'PYF');
INSERT INTO `countries` VALUES ('French Southern Territories', 'TF', 'ATF');
INSERT INTO `countries` VALUES ('Gabon', 'GA', 'GAB');
INSERT INTO `countries` VALUES ('Gambia', 'GM', 'GMB');
INSERT INTO `countries` VALUES ('Georgia', 'GE', 'GEO');
INSERT INTO `countries` VALUES ('Germany', 'DE', 'DEU');
INSERT INTO `countries` VALUES ('Ghana', 'GH', 'GHA');
INSERT INTO `countries` VALUES ('Gibraltar', 'GI', 'GIB');
INSERT INTO `countries` VALUES ('Greece', 'GR', 'GRC');
INSERT INTO `countries` VALUES ('Greenland', 'GL', 'GRL');
INSERT INTO `countries` VALUES ('Grenada', 'GD', 'GRD');
INSERT INTO `countries` VALUES ('Guadeloupe', 'GP', 'GLP');
INSERT INTO `countries` VALUES ('Guam', 'GU', 'GUM');
INSERT INTO `countries` VALUES ('Guatemala', 'GT', 'GTM');
INSERT INTO `countries` VALUES ('Guinea', 'GN', 'GIN');
INSERT INTO `countries` VALUES ('Guinea-bissau', 'GW', 'GNB');
INSERT INTO `countries` VALUES ('Guyana', 'GY', 'GUY');
INSERT INTO `countries` VALUES ('Haiti', 'HT', 'HTI');
INSERT INTO `countries` VALUES ('Heard and Mc Donald Islands', 'HM', 'HMD');
INSERT INTO `countries` VALUES ('Honduras', 'HN', 'HND');
INSERT INTO `countries` VALUES ('Hong Kong', 'HK', 'HKG');
INSERT INTO `countries` VALUES ('Hungary', 'HU', 'HUN');
INSERT INTO `countries` VALUES ('Iceland', 'IS', 'ISL');
INSERT INTO `countries` VALUES ('India', 'IN', 'IND');
INSERT INTO `countries` VALUES ('Indonesia', 'ID', 'IDN');
INSERT INTO `countries` VALUES ('Iran (Islamic Republic of)', 'IR', 'IRN');
INSERT INTO `countries` VALUES ('Iraq', 'IQ', 'IRQ');
INSERT INTO `countries` VALUES ('Ireland', 'IE', 'IRL');
INSERT INTO `countries` VALUES ('Israel', 'IL', 'ISR');
INSERT INTO `countries` VALUES ('Italy', 'IT', 'ITA');
INSERT INTO `countries` VALUES ('Jamaica', 'JM', 'JAM');
INSERT INTO `countries` VALUES ('Japan', 'JP', 'JPN');
INSERT INTO `countries` VALUES ('Jordan', 'JO', 'JOR');
INSERT INTO `countries` VALUES ('Kazakhstan', 'KZ', 'KAZ');
INSERT INTO `countries` VALUES ('Kenya', 'KE', 'KEN');
INSERT INTO `countries` VALUES ('Kiribati', 'KI', 'KIR');
INSERT INTO `countries` VALUES ('Korea, Democratic People''s Republic of', 'KP', 'PRK');
INSERT INTO `countries` VALUES ('Korea, Republic of', 'KR', 'KOR');
INSERT INTO `countries` VALUES ('Kuwait', 'KW', 'KWT');
INSERT INTO `countries` VALUES ('Kyrgyzstan', 'KG', 'KGZ');
INSERT INTO `countries` VALUES ('Lao People''s Democratic Republic', 'LA', 'LAO');
INSERT INTO `countries` VALUES ('Latvia', 'LV', 'LVA');
INSERT INTO `countries` VALUES ('Lebanon', 'LB', 'LBN');
INSERT INTO `countries` VALUES ('Lesotho', 'LS', 'LSO');
INSERT INTO `countries` VALUES ('Liberia', 'LR', 'LBR');
INSERT INTO `countries` VALUES ('Libyan Arab Jamahiriya', 'LY', 'LBY');
INSERT INTO `countries` VALUES ('Liechtenstein', 'LI', 'LIE');
INSERT INTO `countries` VALUES ('Lithuania', 'LT', 'LTU');
INSERT INTO `countries` VALUES ('Luxembourg', 'LU', 'LUX');
INSERT INTO `countries` VALUES ('Macau', 'MO', 'MAC');
INSERT INTO `countries` VALUES ('Macedonia, The Former Yugoslav Republic of', 'MK', 'MKD');
INSERT INTO `countries` VALUES ('Madagascar', 'MG', 'MDG');
INSERT INTO `countries` VALUES ('Malawi', 'MW', 'MWI');
INSERT INTO `countries` VALUES ('Malaysia', 'MY', 'MYS');
INSERT INTO `countries` VALUES ('Maldives', 'MV', 'MDV');
INSERT INTO `countries` VALUES ('Mali', 'ML', 'MLI');
INSERT INTO `countries` VALUES ('Malta', 'MT', 'MLT');
INSERT INTO `countries` VALUES ('Marshall Islands', 'MH', 'MHL');
INSERT INTO `countries` VALUES ('Martinique', 'MQ', 'MTQ');
INSERT INTO `countries` VALUES ('Mauritania', 'MR', 'MRT');
INSERT INTO `countries` VALUES ('Mauritius', 'MU', 'MUS');
INSERT INTO `countries` VALUES ('Mayotte', 'YT', 'MYT');
INSERT INTO `countries` VALUES ('Mexico', 'MX', 'MEX');
INSERT INTO `countries` VALUES ('Micronesia, Federated States of', 'FM', 'FSM');
INSERT INTO `countries` VALUES ('Moldova, Republic of', 'MD', 'MDA');
INSERT INTO `countries` VALUES ('Monaco', 'MC', 'MCO');
INSERT INTO `countries` VALUES ('Mongolia', 'MN', 'MNG');
INSERT INTO `countries` VALUES ('Montserrat', 'MS', 'MSR');
INSERT INTO `countries` VALUES ('Morocco', 'MA', 'MAR');
INSERT INTO `countries` VALUES ('Mozambique', 'MZ', 'MOZ');
INSERT INTO `countries` VALUES ('Myanmar', 'MM', 'MMR');
INSERT INTO `countries` VALUES ('Namibia', 'NA', 'NAM');
INSERT INTO `countries` VALUES ('Nauru', 'NR', 'NRU');
INSERT INTO `countries` VALUES ('Nepal', 'NP', 'NPL');
INSERT INTO `countries` VALUES ('Netherlands', 'NL', 'NLD');
INSERT INTO `countries` VALUES ('Netherlands Antilles', 'AN', 'ANT');
INSERT INTO `countries` VALUES ('New Caledonia', 'NC', 'NCL');
INSERT INTO `countries` VALUES ('New Zealand', 'NZ', 'NZL');
INSERT INTO `countries` VALUES ('Nicaragua', 'NI', 'NIC');
INSERT INTO `countries` VALUES ('Niger', 'NE', 'NER');
INSERT INTO `countries` VALUES ('Nigeria', 'NG', 'NGA');
INSERT INTO `countries` VALUES ('Niue', 'NU', 'NIU');
INSERT INTO `countries` VALUES ('Norfolk Island', 'NF', 'NFK');
INSERT INTO `countries` VALUES ('Northern Mariana Islands', 'MP', 'MNP');
INSERT INTO `countries` VALUES ('Norway', 'NO', 'NOR');
INSERT INTO `countries` VALUES ('Oman', 'OM', 'OMN');
INSERT INTO `countries` VALUES ('Pakistan', 'PK', 'PAK');
INSERT INTO `countries` VALUES ('Palau', 'PW', 'PLW');
INSERT INTO `countries` VALUES ('Panama', 'PA', 'PAN');
INSERT INTO `countries` VALUES ('Papua New Guinea', 'PG', 'PNG');
INSERT INTO `countries` VALUES ('Paraguay', 'PY', 'PRY');
INSERT INTO `countries` VALUES ('Peru', 'PE', 'PER');
INSERT INTO `countries` VALUES ('Philippines', 'PH', 'PHL');
INSERT INTO `countries` VALUES ('Pitcairn', 'PN', 'PCN');
INSERT INTO `countries` VALUES ('Poland', 'PL', 'POL');
INSERT INTO `countries` VALUES ('Portugal', 'PT', 'PRT');
INSERT INTO `countries` VALUES ('Puerto Rico', 'PR', 'PRI');
INSERT INTO `countries` VALUES ('Qatar', 'QA', 'QAT');
INSERT INTO `countries` VALUES ('Reunion', 'RE', 'REU');
INSERT INTO `countries` VALUES ('Romania', 'RO', 'ROM');
INSERT INTO `countries` VALUES ('Russian Federation', 'RU', 'RUS');
INSERT INTO `countries` VALUES ('Rwanda', 'RW', 'RWA');
INSERT INTO `countries` VALUES ('Saint Kitts and Nevis', 'KN', 'KNA');
INSERT INTO `countries` VALUES ('Saint Lucia', 'LC', 'LCA');
INSERT INTO `countries` VALUES ('Saint Vincent and the Grenadines', 'VC', 'VCT');
INSERT INTO `countries` VALUES ('Samoa', 'WS', 'WSM');
INSERT INTO `countries` VALUES ('San Marino', 'SM', 'SMR');
INSERT INTO `countries` VALUES ('Sao Tome and Principe', 'ST', 'STP');
INSERT INTO `countries` VALUES ('Saudi Arabia', 'SA', 'SAU');
INSERT INTO `countries` VALUES ('Senegal', 'SN', 'SEN');
INSERT INTO `countries` VALUES ('Seychelles', 'SC', 'SYC');
INSERT INTO `countries` VALUES ('Sierra Leone', 'SL', 'SLE');
INSERT INTO `countries` VALUES ('Singapore', 'SG', 'SGP');
INSERT INTO `countries` VALUES ('Slovakia (Slovak Republic)', 'SK', 'SVK');
INSERT INTO `countries` VALUES ('Slovenia', 'SI', 'SVN');
INSERT INTO `countries` VALUES ('Solomon Islands', 'SB', 'SLB');
INSERT INTO `countries` VALUES ('Somalia', 'SO', 'SOM');
INSERT INTO `countries` VALUES ('South Africa', 'ZA', 'ZAF');
INSERT INTO `countries` VALUES ('South Georgia and the South Sandwich Islands', 'GS', 'SGS');
INSERT INTO `countries` VALUES ('Spain', 'ES', 'ESP');
INSERT INTO `countries` VALUES ('Sri Lanka', 'LK', 'LKA');
INSERT INTO `countries` VALUES ('St. Helena', 'SH', 'SHN');
INSERT INTO `countries` VALUES ('St. Pierre and Miquelon', 'PM', 'SPM');
INSERT INTO `countries` VALUES ('Sudan', 'SD', 'SDN');
INSERT INTO `countries` VALUES ('Suriname', 'SR', 'SUR');
INSERT INTO `countries` VALUES ('Svalbard and Jan Mayen Islands', 'SJ', 'SJM');
INSERT INTO `countries` VALUES ('Swaziland', 'SZ', 'SWZ');
INSERT INTO `countries` VALUES ('Sweden', 'SE', 'SWE');
INSERT INTO `countries` VALUES ('Switzerland', 'CH', 'CHE');
INSERT INTO `countries` VALUES ('Syrian Arab Republic', 'SY', 'SYR');
INSERT INTO `countries` VALUES ('Taiwan', 'TW', 'TWN');
INSERT INTO `countries` VALUES ('Tajikistan', 'TJ', 'TJK');
INSERT INTO `countries` VALUES ('Tanzania, United Republic of', 'TZ', 'TZA');
INSERT INTO `countries` VALUES ('Thailand', 'TH', 'THA');
INSERT INTO `countries` VALUES ('Togo', 'TG', 'TGO');
INSERT INTO `countries` VALUES ('Tokelau', 'TK', 'TKL');
INSERT INTO `countries` VALUES ('Tonga', 'TO', 'TON');
INSERT INTO `countries` VALUES ('Trinidad and Tobago', 'TT', 'TTO');
INSERT INTO `countries` VALUES ('Tunisia', 'TN', 'TUN');
INSERT INTO `countries` VALUES ('Turkey', 'TR', 'TUR');
INSERT INTO `countries` VALUES ('Turkmenistan', 'TM', 'TKM');
INSERT INTO `countries` VALUES ('Turks and Caicos Islands', 'TC', 'TCA');
INSERT INTO `countries` VALUES ('Tuvalu', 'TV', 'TUV');
INSERT INTO `countries` VALUES ('Uganda', 'UG', 'UGA');
INSERT INTO `countries` VALUES ('Ukraine', 'UA', 'UKR');
INSERT INTO `countries` VALUES ('United Arab Emirates', 'AE', 'ARE');
INSERT INTO `countries` VALUES ('United Kingdom', 'GB', 'GBR');
INSERT INTO `countries` VALUES ('United States', 'US', 'USA');
INSERT INTO `countries` VALUES ('United States Minor Outlying Islands', 'UM', 'UMI');
INSERT INTO `countries` VALUES ('Uruguay', 'UY', 'URY');
INSERT INTO `countries` VALUES ('Uzbekistan', 'UZ', 'UZB');
INSERT INTO `countries` VALUES ('Vanuatu', 'VU', 'VUT');
INSERT INTO `countries` VALUES ('Vatican City State (Holy See)', 'VA', 'VAT');
INSERT INTO `countries` VALUES ('Venezuela', 'VE', 'VEN');
INSERT INTO `countries` VALUES ('Viet Nam', 'VN', 'VNM');
INSERT INTO `countries` VALUES ('Virgin Islands (British)', 'VG', 'VGB');
INSERT INTO `countries` VALUES ('Virgin Islands (U.S.)', 'VI', 'VIR');
INSERT INTO `countries` VALUES ('Wallis and Futuna Islands', 'WF', 'WLF');
INSERT INTO `countries` VALUES ('Western Sahara', 'EH', 'ESH');
INSERT INTO `countries` VALUES ('Yemen', 'YE', 'YEM');
INSERT INTO `countries` VALUES ('Yugoslavia', 'YU', 'YUG');
INSERT INTO `countries` VALUES ('Zaire', 'ZR', 'ZAR');
INSERT INTO `countries` VALUES ('Zambia', 'ZM', 'ZMB');
INSERT INTO `countries` VALUES ('Zimbabwe', 'ZW', 'ZWE');

-- --------------------------------------------------------

-- 
-- Table structure for table `document`
-- 

DROP TABLE IF EXISTS `document`;
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

-- 
-- Dumping data for table `document`
-- 

INSERT INTO `document` VALUES (996, 'Sunset', '', 71189, '2005-03-08 12:51:19', 'file://C:\\sandbox\\clearhealth\\clearhealth\\trunk/user/documents/0/Sunset.jpg', 'image/jpeg', NULL, NULL, '20050308125119', 0, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `enumeration`
-- 

DROP TABLE IF EXISTS `enumeration`;
CREATE TABLE `enumeration` (
  `name` varchar(20) NOT NULL default '',
  `title` varchar(100) NOT NULL default '',
  `description` tinytext NOT NULL,
  `gender` enum('Male','Female','Not Specified') NOT NULL default 'Male',
  `company_number_type` enum('Primary','Fax') NOT NULL default 'Primary',
  `address_type` enum('Main','Billing','Shipping') NOT NULL default 'Main',
  `quality_of_file` enum('Good','Bad') NOT NULL default 'Good',
  `number_type` enum('Main','Mobile','Work') NOT NULL default 'Main',
  `disposition` enum('New','Waiting','Compete') NOT NULL default 'New',
  `state` enum('Alaska','Arizona','California') NOT NULL default 'Alaska',
  `group_list` enum('All','Arizona','California') NOT NULL default 'All',
  `identifier_type` enum('SSN') NOT NULL default 'SSN',
  `company_type` enum('Insurance') NOT NULL default 'Insurance',
  `assigning` enum('A - Assigned','B - Assigned Lab Services Only','C - Not Assigned','P - Assignment Refused') NOT NULL default 'A - Assigned',
  `relation_of_information_code` enum('A - On file','I - Informed Consent','M - Limited Ability','N - Not allowed','O - On file','Y - Has permission') NOT NULL default 'A - On file',
  `person_type` enum('Patient','Provider','Mid-level','Staff','Subscriber') NOT NULL default 'Patient',
  `provider_number_type` enum('State License') NOT NULL default 'State License',
  `subscriber_to_patient_relationship` enum('Self','Mother','Father') NOT NULL default 'Self',
  `payer_type` enum('medicare') NOT NULL default 'medicare',
  PRIMARY KEY  (`name`)
) TYPE=MyISAM COMMENT='Each enum stored as a new col, metadata in 1 row per enum';

-- 
-- Dumping data for table `enumeration`
-- 

INSERT INTO `enumeration` VALUES ('gender', 'Gender', '', 'Male', 'Primary', 'Main', 'Good', 'Main', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare');
INSERT INTO `enumeration` VALUES ('person_type', 'Person Type', '', 'Male', 'Primary', 'Main', 'Good', 'Main', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare');
INSERT INTO `enumeration` VALUES ('company_type', 'Company Type', '', 'Male', 'Primary', 'Main', 'Good', 'Main', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare');
INSERT INTO `enumeration` VALUES ('state', 'State', '', 'Male', 'Primary', 'Main', 'Good', 'Main', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare');
INSERT INTO `enumeration` VALUES ('number_type', 'Phone Number Type', '', 'Male', 'Primary', 'Main', 'Good', 'Main', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare');
INSERT INTO `enumeration` VALUES ('company_number_type', 'Company Number Type', '', 'Male', 'Primary', 'Main', 'Good', 'Main', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare');
INSERT INTO `enumeration` VALUES ('address_type', 'Address Type', '', 'Male', 'Primary', 'Main', 'Good', 'Main', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare');
INSERT INTO `enumeration` VALUES ('disposition', 'Disposition', '', 'Male', 'Primary', 'Main', 'Good', 'Main', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare');
INSERT INTO `enumeration` VALUES ('quality_of_file', 'Quality of File', '', 'Male', 'Primary', 'Main', 'Good', 'Main', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare');
INSERT INTO `enumeration` VALUES ('group_list', 'File Groups', '', 'Male', 'Primary', 'Main', 'Good', 'Main', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare');
INSERT INTO `enumeration` VALUES ('identifier_type', 'Identifier Type', '', 'Male', 'Primary', 'Main', 'Good', 'Main', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare');
INSERT INTO `enumeration` VALUES ('assigning', '', '', 'Male', 'Primary', 'Main', 'Good', 'Main', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare');
INSERT INTO `enumeration` VALUES ('relation_of_informat', '', '', 'Male', 'Primary', 'Main', 'Good', 'Main', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare');
INSERT INTO `enumeration` VALUES ('provider_number_type', '', '', 'Male', 'Primary', 'Main', 'Good', 'Main', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare');
INSERT INTO `enumeration` VALUES ('subscriber_to_patien', '', '', 'Male', 'Primary', 'Main', 'Good', 'Main', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare');
INSERT INTO `enumeration` VALUES ('payer_type', 'Payer Type', '', 'Male', 'Primary', 'Main', 'Good', 'Main', 'New', 'Alaska', 'All', 'SSN', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'medicare');

-- --------------------------------------------------------

-- 
-- Table structure for table `events`
-- 

DROP TABLE IF EXISTS `events`;
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

-- 
-- Dumping data for table `events`
-- 

INSERT INTO `events` VALUES (152, 'North County Schedule', '', '', '', '', 126);
INSERT INTO `events` VALUES (502, 'north county schedule', '', '', '', '', 501);
INSERT INTO `events` VALUES (527, 'this is an event', '', '', '', '', 0);
INSERT INTO `events` VALUES (631, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (530, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (532, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (534, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (536, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (538, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (540, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (542, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (544, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (546, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (548, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (550, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (552, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (554, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (556, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (558, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (560, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (562, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (574, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (576, 'South County', '', '', '', '', 126);
INSERT INTO `events` VALUES (624, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (641, '', '', '', '', '', 640);
INSERT INTO `events` VALUES (638, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (642, 'Mirra Mesa Office hours', '', '', '', '', 640);
INSERT INTO `events` VALUES (643, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (644, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (645, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (646, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (647, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (648, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (650, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (652, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (654, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (656, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (658, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (660, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (663, 'Josh''s Test Schedule', '', '', '', '', 662);
INSERT INTO `events` VALUES (664, 'Josh''s Test Schedule', '', '', '', '', 0);
INSERT INTO `events` VALUES (665, 'Josh''s Test Schedule', '', '', '', '', 0);
INSERT INTO `events` VALUES (666, 'Josh''s Test Schedule #2', '', '', '', '', 0);
INSERT INTO `events` VALUES (667, 'Josh''s Test Schedule', '', '', '', '', 0);
INSERT INTO `events` VALUES (668, 'Josh''s Test Schedule', '', '', '', '', 0);
INSERT INTO `events` VALUES (669, 'Josh''s Test Schedule', '', '', '', '', 0);
INSERT INTO `events` VALUES (670, 'Josh''s Test Schedule', '', '', '', '', 0);
INSERT INTO `events` VALUES (680, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (682, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (684, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (686, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (688, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (690, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (692, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (694, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (696, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (698, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (700, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (702, '', '', '', '', '', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `fee_schedule`
-- 

DROP TABLE IF EXISTS `fee_schedule`;
CREATE TABLE `fee_schedule` (
  `fee_schedule_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `label` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`fee_schedule_id`),
  UNIQUE KEY `name` (`name`)
) TYPE=InnoDB;

-- 
-- Dumping data for table `fee_schedule`
-- 

INSERT INTO `fee_schedule` VALUES (711, 'test', 'Test', 'My Test Schedule');
INSERT INTO `fee_schedule` VALUES (1054, 'medicare', 'Medicare', 'Medicare Fee Schedule');

-- --------------------------------------------------------

-- 
-- Table structure for table `fee_schedule_data`
-- 

DROP TABLE IF EXISTS `fee_schedule_data`;
CREATE TABLE `fee_schedule_data` (
  `code_id` int(11) NOT NULL default '0',
  `revision_id` int(11) NOT NULL default '0',
  `fee_schedule_id` int(11) NOT NULL default '0',
  `data` double NOT NULL default '0',
  `formula` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`code_id`,`revision_id`,`fee_schedule_id`),
  KEY `fee_schedule_id` (`fee_schedule_id`),
  KEY `revision_id` (`revision_id`)
) TYPE=InnoDB;

-- 
-- Dumping data for table `fee_schedule_data`
-- 

INSERT INTO `fee_schedule_data` VALUES (1, 1, 711, 22, '');
INSERT INTO `fee_schedule_data` VALUES (2, 1, 711, 23, '');
INSERT INTO `fee_schedule_data` VALUES (3, 1, 711, 14.34, '');
INSERT INTO `fee_schedule_data` VALUES (4, 1, 711, 34, '');
INSERT INTO `fee_schedule_data` VALUES (5, 1, 711, 12, '');
INSERT INTO `fee_schedule_data` VALUES (6, 1, 711, 42.67, '');
INSERT INTO `fee_schedule_data` VALUES (9, 1, 711, 0, '');
INSERT INTO `fee_schedule_data` VALUES (10, 1, 711, 34, '');
INSERT INTO `fee_schedule_data` VALUES (13, 1, 711, 45.45, '');
INSERT INTO `fee_schedule_data` VALUES (15, 1, 711, 0, '');
INSERT INTO `fee_schedule_data` VALUES (21, 1, 711, 45, '');
INSERT INTO `fee_schedule_data` VALUES (22, 1, 711, 0, '');
INSERT INTO `fee_schedule_data` VALUES (23, 1, 711, 0, '');
INSERT INTO `fee_schedule_data` VALUES (24, 1, 711, 45, '');
INSERT INTO `fee_schedule_data` VALUES (25, 1, 711, 0, '');
INSERT INTO `fee_schedule_data` VALUES (26, 1, 711, 0, '');
INSERT INTO `fee_schedule_data` VALUES (27, 1, 711, 0, '');
INSERT INTO `fee_schedule_data` VALUES (28, 1, 711, 34, '');
INSERT INTO `fee_schedule_data` VALUES (29, 1, 711, 0, '');
INSERT INTO `fee_schedule_data` VALUES (30, 1, 711, 0, '');
INSERT INTO `fee_schedule_data` VALUES (35, 1, 711, 0, '');
INSERT INTO `fee_schedule_data` VALUES (36, 1, 711, 0, '');
INSERT INTO `fee_schedule_data` VALUES (37, 1, 711, 34.12, '');
INSERT INTO `fee_schedule_data` VALUES (38, 1, 711, 0, '');
INSERT INTO `fee_schedule_data` VALUES (39, 1, 711, 0, '');
INSERT INTO `fee_schedule_data` VALUES (40, 1, 711, 0, '');
INSERT INTO `fee_schedule_data` VALUES (42, 1, 711, 0, '');
INSERT INTO `fee_schedule_data` VALUES (45, 1, 711, 0, '');
INSERT INTO `fee_schedule_data` VALUES (26737, 1, 711, 45, '');
INSERT INTO `fee_schedule_data` VALUES (26739, 1, 711, 34, '');
INSERT INTO `fee_schedule_data` VALUES (26747, 1, 711, 21, '');
INSERT INTO `fee_schedule_data` VALUES (26748, 1, 1054, 8, '');
INSERT INTO `fee_schedule_data` VALUES (26749, 1, 1054, 7, '');
INSERT INTO `fee_schedule_data` VALUES (26750, 1, 711, 34, '');
INSERT INTO `fee_schedule_data` VALUES (26751, 1, 1054, 6, '');
INSERT INTO `fee_schedule_data` VALUES (26752, 1, 1054, 5, '');
INSERT INTO `fee_schedule_data` VALUES (26753, 1, 1054, 4, '');
INSERT INTO `fee_schedule_data` VALUES (26754, 1, 711, 45, '');
INSERT INTO `fee_schedule_data` VALUES (26755, 1, 1054, 3, '');
INSERT INTO `fee_schedule_data` VALUES (26756, 1, 1054, 2, '');
INSERT INTO `fee_schedule_data` VALUES (26757, 1, 1054, 56, '');
INSERT INTO `fee_schedule_data` VALUES (26758, 1, 1054, 0, '');
INSERT INTO `fee_schedule_data` VALUES (26759, 1, 711, 1, '');
INSERT INTO `fee_schedule_data` VALUES (26760, 1, 1054, 34, '');
INSERT INTO `fee_schedule_data` VALUES (26761, 1, 1054, 45, '');

-- --------------------------------------------------------

-- 
-- Table structure for table `fee_schedule_revision`
-- 

DROP TABLE IF EXISTS `fee_schedule_revision`;
CREATE TABLE `fee_schedule_revision` (
  `revision_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `update_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`revision_id`),
  KEY `user_id` (`user_id`)
) TYPE=InnoDB;

-- 
-- Dumping data for table `fee_schedule_revision`
-- 

INSERT INTO `fee_schedule_revision` VALUES (1, 0, '2005-03-02 11:58:20', 'default');

-- --------------------------------------------------------

-- 
-- Table structure for table `form`
-- 

DROP TABLE IF EXISTS `form`;
CREATE TABLE `form` (
  `form_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`form_id`)
) TYPE=InnoDB;

-- 
-- Dumping data for table `form`
-- 

INSERT INTO `form` VALUES (800, 'Test Data', 'Some random data');

-- --------------------------------------------------------

-- 
-- Table structure for table `form_data`
-- 

DROP TABLE IF EXISTS `form_data`;
CREATE TABLE `form_data` (
  `form_data_id` int(11) NOT NULL default '0',
  `form_id` int(11) NOT NULL default '0',
  `external_id` int(11) NOT NULL default '0',
  `last_edit` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`form_data_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `form_data`
-- 

INSERT INTO `form_data` VALUES (809, 800, 0, '2005-03-04 16:54:38');

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_acl`
-- 

DROP TABLE IF EXISTS `gacl_acl`;
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

-- 
-- Dumping data for table `gacl_acl`
-- 

INSERT INTO `gacl_acl` VALUES (26, 'user', 1, 1, '', '', 1110310784);
INSERT INTO `gacl_acl` VALUES (24, 'user', 1, 1, '', '', 1110310727);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_acl_sections`
-- 

DROP TABLE IF EXISTS `gacl_acl_sections`;
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

-- 
-- Dumping data for table `gacl_acl_sections`
-- 

INSERT INTO `gacl_acl_sections` VALUES (1, 'system', 1, 'System', 0);
INSERT INTO `gacl_acl_sections` VALUES (2, 'user', 2, 'User', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_acl_seq`
-- 

DROP TABLE IF EXISTS `gacl_acl_seq`;
CREATE TABLE `gacl_acl_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_acl_seq`
-- 

INSERT INTO `gacl_acl_seq` VALUES (26);
INSERT INTO `gacl_acl_seq` VALUES (26);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aco`
-- 

DROP TABLE IF EXISTS `gacl_aco`;
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

-- 
-- Dumping data for table `gacl_aco`
-- 

INSERT INTO `gacl_aco` VALUES (11, 'actions', 'view', 10, 'view', 0);
INSERT INTO `gacl_aco` VALUES (12, 'actions', 'edit', 11, 'edit', 0);
INSERT INTO `gacl_aco` VALUES (13, 'actions', 'add', 12, 'add', 0);
INSERT INTO `gacl_aco` VALUES (14, 'actions', 'delete', 13, 'delete', 0);
INSERT INTO `gacl_aco` VALUES (16, 'actions', 'usage', 9, 'usage', 0);
INSERT INTO `gacl_aco` VALUES (17, 'actions', 'uploadFile', 14, 'Upload A file', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aco_map`
-- 

DROP TABLE IF EXISTS `gacl_aco_map`;
CREATE TABLE `gacl_aco_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_aco_map`
-- 

INSERT INTO `gacl_aco_map` VALUES (24, 'actions', 'add');
INSERT INTO `gacl_aco_map` VALUES (24, 'actions', 'delete');
INSERT INTO `gacl_aco_map` VALUES (24, 'actions', 'edit');
INSERT INTO `gacl_aco_map` VALUES (24, 'actions', 'uploadFile');
INSERT INTO `gacl_aco_map` VALUES (24, 'actions', 'usage');
INSERT INTO `gacl_aco_map` VALUES (24, 'actions', 'view');
INSERT INTO `gacl_aco_map` VALUES (26, 'actions', 'uploadFile');

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aco_sections`
-- 

DROP TABLE IF EXISTS `gacl_aco_sections`;
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

-- 
-- Dumping data for table `gacl_aco_sections`
-- 

INSERT INTO `gacl_aco_sections` VALUES (11, 'actions', 10, 'Actions', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aco_sections_seq`
-- 

DROP TABLE IF EXISTS `gacl_aco_sections_seq`;
CREATE TABLE `gacl_aco_sections_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_aco_sections_seq`
-- 

INSERT INTO `gacl_aco_sections_seq` VALUES (11);
INSERT INTO `gacl_aco_sections_seq` VALUES (11);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aco_seq`
-- 

DROP TABLE IF EXISTS `gacl_aco_seq`;
CREATE TABLE `gacl_aco_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_aco_seq`
-- 

INSERT INTO `gacl_aco_seq` VALUES (17);
INSERT INTO `gacl_aco_seq` VALUES (17);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro`
-- 

DROP TABLE IF EXISTS `gacl_aro`;
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

-- 
-- Dumping data for table `gacl_aro`
-- 

INSERT INTO `gacl_aro` VALUES (15, 'users', 'admin', 10, 'Admin', 0);
INSERT INTO `gacl_aro` VALUES (23, 'users', 'jeichorn', 100, 'jeichorn', 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_groups`
-- 

DROP TABLE IF EXISTS `gacl_aro_groups`;
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

-- 
-- Dumping data for table `gacl_aro_groups`
-- 

INSERT INTO `gacl_aro_groups` VALUES (10, 0, 1, 4, 'Root', 'root');
INSERT INTO `gacl_aro_groups` VALUES (12, 10, 2, 3, 'System Admin', 'admin');

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_groups_id_seq`
-- 

DROP TABLE IF EXISTS `gacl_aro_groups_id_seq`;
CREATE TABLE `gacl_aro_groups_id_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_aro_groups_id_seq`
-- 

INSERT INTO `gacl_aro_groups_id_seq` VALUES (17);
INSERT INTO `gacl_aro_groups_id_seq` VALUES (17);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_groups_map`
-- 

DROP TABLE IF EXISTS `gacl_aro_groups_map`;
CREATE TABLE `gacl_aro_groups_map` (
  `acl_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acl_id`,`group_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_aro_groups_map`
-- 

INSERT INTO `gacl_aro_groups_map` VALUES (24, 12);
INSERT INTO `gacl_aro_groups_map` VALUES (26, 12);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_map`
-- 

DROP TABLE IF EXISTS `gacl_aro_map`;
CREATE TABLE `gacl_aro_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_aro_map`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_sections`
-- 

DROP TABLE IF EXISTS `gacl_aro_sections`;
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

-- 
-- Dumping data for table `gacl_aro_sections`
-- 

INSERT INTO `gacl_aro_sections` VALUES (10, 'users', 10, 'Users', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_sections_seq`
-- 

DROP TABLE IF EXISTS `gacl_aro_sections_seq`;
CREATE TABLE `gacl_aro_sections_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_aro_sections_seq`
-- 

INSERT INTO `gacl_aro_sections_seq` VALUES (11);
INSERT INTO `gacl_aro_sections_seq` VALUES (11);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_seq`
-- 

DROP TABLE IF EXISTS `gacl_aro_seq`;
CREATE TABLE `gacl_aro_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_aro_seq`
-- 

INSERT INTO `gacl_aro_seq` VALUES (23);
INSERT INTO `gacl_aro_seq` VALUES (23);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo`
-- 

DROP TABLE IF EXISTS `gacl_axo`;
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

-- 
-- Dumping data for table `gacl_axo`
-- 

INSERT INTO `gacl_axo` VALUES (0, 'resources', 'main', 10, 'Section - Main', 0);
INSERT INTO `gacl_axo` VALUES (19, 'resources', 'preferences', 10, 'Section - Preferences', 0);
INSERT INTO `gacl_axo` VALUES (17, 'resources', 'default', 10, 'Section - Default', 0);
INSERT INTO `gacl_axo` VALUES (16, 'resources', 'access', 10, 'Section - Access', 0);
INSERT INTO `gacl_axo` VALUES (44, 'resources', 'practice', 10, 'Section - Practice', 0);
INSERT INTO `gacl_axo` VALUES (43, 'resources', 'personschedule', 10, 'Section - PersonSchedule', 0);
INSERT INTO `gacl_axo` VALUES (42, 'resources', 'patientfinder', 10, 'Section - PatientFinder', 0);
INSERT INTO `gacl_axo` VALUES (41, 'resources', 'patient', 10, 'Section - Patient', 0);
INSERT INTO `gacl_axo` VALUES (40, 'resources', 'location', 10, 'Section - Location', 0);
INSERT INTO `gacl_axo` VALUES (39, 'resources', 'feeschedule', 10, 'Section - FeeSchedule', 0);
INSERT INTO `gacl_axo` VALUES (38, 'resources', 'calendar', 10, 'Section - Calendar', 0);
INSERT INTO `gacl_axo` VALUES (37, 'resources', 'user', 10, 'Section - User', 0);
INSERT INTO `gacl_axo` VALUES (36, 'resources', 'enumeration', 10, 'Section - Enumeration', 0);
INSERT INTO `gacl_axo` VALUES (45, 'resources', 'report', 10, 'Section - Report', 0);
INSERT INTO `gacl_axo` VALUES (46, 'resources', 'schedule', 10, 'Section - Schedule', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_groups`
-- 

DROP TABLE IF EXISTS `gacl_axo_groups`;
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

-- 
-- Dumping data for table `gacl_axo_groups`
-- 

INSERT INTO `gacl_axo_groups` VALUES (10, 0, 1, 4, 'Root', 'root');
INSERT INTO `gacl_axo_groups` VALUES (11, 10, 2, 3, 'All Site Sections', 'sections');

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_groups_id_seq`
-- 

DROP TABLE IF EXISTS `gacl_axo_groups_id_seq`;
CREATE TABLE `gacl_axo_groups_id_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_axo_groups_id_seq`
-- 

INSERT INTO `gacl_axo_groups_id_seq` VALUES (11);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_groups_map`
-- 

DROP TABLE IF EXISTS `gacl_axo_groups_map`;
CREATE TABLE `gacl_axo_groups_map` (
  `acl_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acl_id`,`group_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_axo_groups_map`
-- 

INSERT INTO `gacl_axo_groups_map` VALUES (24, 11);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_map`
-- 

DROP TABLE IF EXISTS `gacl_axo_map`;
CREATE TABLE `gacl_axo_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_axo_map`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_sections`
-- 

DROP TABLE IF EXISTS `gacl_axo_sections`;
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

-- 
-- Dumping data for table `gacl_axo_sections`
-- 

INSERT INTO `gacl_axo_sections` VALUES (0, 'resources', 10, 'Resources', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_sections_seq`
-- 

DROP TABLE IF EXISTS `gacl_axo_sections_seq`;
CREATE TABLE `gacl_axo_sections_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_axo_sections_seq`
-- 

INSERT INTO `gacl_axo_sections_seq` VALUES (19);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_seq`
-- 

DROP TABLE IF EXISTS `gacl_axo_seq`;
CREATE TABLE `gacl_axo_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_axo_seq`
-- 

INSERT INTO `gacl_axo_seq` VALUES (46);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_groups_aro_map`
-- 

DROP TABLE IF EXISTS `gacl_groups_aro_map`;
CREATE TABLE `gacl_groups_aro_map` (
  `group_id` int(11) NOT NULL default '0',
  `aro_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`aro_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_groups_aro_map`
-- 

INSERT INTO `gacl_groups_aro_map` VALUES (12, 15);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_groups_axo_map`
-- 

DROP TABLE IF EXISTS `gacl_groups_axo_map`;
CREATE TABLE `gacl_groups_axo_map` (
  `group_id` int(11) NOT NULL default '0',
  `axo_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`axo_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_groups_axo_map`
-- 

INSERT INTO `gacl_groups_axo_map` VALUES (11, 0);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 16);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 17);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 18);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 19);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 36);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 37);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 38);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 39);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 40);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 41);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 42);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 43);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 44);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 45);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 46);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_phpgacl`
-- 

DROP TABLE IF EXISTS `gacl_phpgacl`;
CREATE TABLE `gacl_phpgacl` (
  `name` varchar(230) NOT NULL default '',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`name`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_phpgacl`
-- 

INSERT INTO `gacl_phpgacl` VALUES ('version', '3.3.3');
INSERT INTO `gacl_phpgacl` VALUES ('schema_version', '2.1');

-- --------------------------------------------------------

-- 
-- Table structure for table `groups`
-- 

DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `groups`
-- 

INSERT INTO `groups` VALUES (1, 'superadmin');
INSERT INTO `groups` VALUES (2, 'practice_admin');
INSERT INTO `groups` VALUES (3, 'usage');
INSERT INTO `groups` VALUES (0, 'provider');

-- --------------------------------------------------------

-- 
-- Table structure for table `identifier`
-- 

DROP TABLE IF EXISTS `identifier`;
CREATE TABLE `identifier` (
  `identifier_id` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  `identifier` varchar(100) NOT NULL default '',
  `identifier_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`identifier_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `identifier`
-- 

INSERT INTO `identifier` VALUES (960, 0, 'blah', 1);
INSERT INTO `identifier` VALUES (961, 0, 'blah', 1);
INSERT INTO `identifier` VALUES (962, 0, 'blah', 1);
INSERT INTO `identifier` VALUES (963, 0, 'blah', 1);
INSERT INTO `identifier` VALUES (964, 0, 'blah', 1);
INSERT INTO `identifier` VALUES (965, 0, 'blah', 1);
INSERT INTO `identifier` VALUES (966, 0, 'blah', 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `insurance_program`
-- 

DROP TABLE IF EXISTS `insurance_program`;
CREATE TABLE `insurance_program` (
  `insurance_program_id` int(11) NOT NULL default '0',
  `payer_type` int(11) NOT NULL default '0',
  `company_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`insurance_program_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `insurance_program`
-- 

INSERT INTO `insurance_program` VALUES (989, 1, 968, 'Test Program');

-- --------------------------------------------------------

-- 
-- Table structure for table `insured_relationship`
-- 

DROP TABLE IF EXISTS `insured_relationship`;
CREATE TABLE `insured_relationship` (
  `insured_relationship_id` int(11) NOT NULL default '0',
  `insurance_program_id` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  `subsciber_id` int(11) NOT NULL default '0',
  `subscriber_to_patient_relationship` int(11) NOT NULL default '0',
  `copay` double NOT NULL default '0',
  `assigning` int(11) NOT NULL default '0',
  `group_name` varchar(100) NOT NULL default '',
  `group_number` varchar(100) NOT NULL default '',
  `default_provider` int(11) NOT NULL default '0',
  PRIMARY KEY  (`insured_relationship_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `insured_relationship`
-- 

INSERT INTO `insured_relationship` VALUES (974, 0, 955, 0, 0, 15, 0, 'blah', 'blah', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `name_history`
-- 

DROP TABLE IF EXISTS `name_history`;
CREATE TABLE `name_history` (
  `name_history_id` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  `first_name` varchar(100) NOT NULL default '',
  `last_name` varchar(100) NOT NULL default '',
  `middle_name` varchar(50) NOT NULL default '',
  `update_date` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`name_history_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `name_history`
-- 

INSERT INTO `name_history` VALUES (959, 955, 'Fred', 'Flinstone', 'Q', '2005-03-07');
INSERT INTO `name_history` VALUES (998, 983, 'Joshua', 'Eichorn', '', '2005-03-08');

-- --------------------------------------------------------

-- 
-- Table structure for table `note`
-- 

DROP TABLE IF EXISTS `note`;
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

-- 
-- Dumping data for table `note`
-- 

INSERT INTO `note` VALUES (997, 996, 'This is a note', NULL, '2005-03-08 12:52:38', '20050308125238');

-- --------------------------------------------------------

-- 
-- Table structure for table `number`
-- 

DROP TABLE IF EXISTS `number`;
CREATE TABLE `number` (
  `number_id` int(11) NOT NULL default '0',
  `number_type` int(11) NOT NULL default '0',
  `notes` tinytext NOT NULL,
  `number` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`number_id`)
) TYPE=InnoDB COMMENT='A phone number';

-- 
-- Dumping data for table `number`
-- 

INSERT INTO `number` VALUES (784, 1, '', '480-361-1331');
INSERT INTO `number` VALUES (956, 1, 'tewt', '12345678');

-- --------------------------------------------------------

-- 
-- Table structure for table `occurences`
-- 

DROP TABLE IF EXISTS `occurences`;
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

-- 
-- Dumping data for table `occurences`
-- 

INSERT INTO `occurences` VALUES (563, 562, '2004-11-19 06:45:00', '2004-11-19 07:00:00', 'test', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (633, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0, 0, NULL, NULL);
INSERT INTO `occurences` VALUES (632, 631, '2004-11-24 06:00:00', '2004-11-24 07:45:00', 'This is a test', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (568, 152, '2004-11-06 07:45:00', '2004-11-06 09:00:00', '', 0, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (569, 152, '2004-11-13 07:45:00', '2004-11-13 09:00:00', '', 0, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (570, 152, '2004-11-20 07:45:00', '2004-11-20 09:00:00', '', 0, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (571, 152, '2004-11-27 07:45:00', '2004-11-27 09:00:00', '', 0, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (575, 574, '2004-11-15 06:00:00', '2004-11-15 07:00:00', 'this is an appointment', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (577, 576, '2004-11-03 06:00:00', '2004-11-03 17:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (578, 576, '2004-11-07 06:00:00', '2004-11-07 13:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (579, 576, '2004-11-10 06:00:00', '2004-11-10 17:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (581, 576, '2004-11-17 06:00:00', '2004-11-17 17:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (582, 576, '2004-11-21 06:00:00', '2004-11-21 13:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (583, 576, '2004-11-24 06:00:00', '2004-11-24 17:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (584, 576, '2004-11-28 06:00:00', '2004-11-28 13:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (585, 576, '2004-12-01 06:00:00', '2004-12-01 17:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (586, 576, '2004-12-05 06:00:00', '2004-12-05 13:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (587, 576, '2004-12-08 06:00:00', '2004-12-08 17:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (588, 576, '2004-12-12 06:00:00', '2004-12-12 13:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (589, 576, '2004-12-15 06:00:00', '2004-12-15 17:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (590, 576, '2004-12-19 06:00:00', '2004-12-19 13:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (591, 576, '2004-12-22 06:00:00', '2004-12-22 17:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (592, 576, '2004-12-26 06:00:00', '2004-12-26 13:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (593, 576, '2004-12-29 06:00:00', '2004-12-29 17:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (594, 576, '2005-01-02 06:00:00', '2005-01-02 13:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (595, 576, '2005-01-05 06:00:00', '2005-01-05 17:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (596, 576, '2005-01-09 06:00:00', '2005-01-09 13:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (597, 576, '2005-01-12 06:00:00', '2005-01-12 17:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (598, 576, '2005-01-16 06:00:00', '2005-01-16 13:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (599, 576, '2005-01-19 06:00:00', '2005-01-19 17:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (600, 576, '2005-01-23 06:00:00', '2005-01-23 13:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (601, 576, '2005-01-26 06:00:00', '2005-01-26 17:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (602, 576, '2005-01-30 06:00:00', '2005-01-30 13:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (603, 576, '2005-02-02 06:00:00', '2005-02-02 17:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (604, 576, '2005-02-06 06:00:00', '2005-02-06 13:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (605, 576, '2005-02-09 06:00:00', '2005-02-09 17:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (606, 576, '2005-02-13 06:00:00', '2005-02-13 13:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (607, 576, '2005-02-16 06:00:00', '2005-02-16 17:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (608, 576, '2005-02-20 06:00:00', '2005-02-20 13:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (609, 576, '2005-02-23 06:00:00', '2005-02-23 17:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (610, 576, '2005-02-27 06:00:00', '2005-02-27 13:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (611, 576, '2005-03-02 06:00:00', '2005-03-02 17:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (612, 576, '2005-03-06 06:00:00', '2005-03-06 13:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (613, 576, '2005-03-09 06:00:00', '2005-03-09 17:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (614, 576, '2005-03-13 06:00:00', '2005-03-13 13:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (615, 576, '2005-03-16 06:00:00', '2005-03-16 17:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (616, 576, '2005-03-20 06:00:00', '2005-03-20 13:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (617, 576, '2005-03-23 06:00:00', '2005-03-23 17:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (618, 576, '2005-03-27 06:00:00', '2005-03-27 13:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (619, 576, '2005-03-30 06:00:00', '2005-03-30 17:00:00', '', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (625, 624, '2004-11-14 06:30:00', '2004-11-14 07:45:00', 'this is a test', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (626, 0, '2004-11-14 06:30:00', '2004-11-14 07:00:00', 'test', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (627, 0, '2004-11-14 13:30:00', '2004-11-14 15:00:00', 'test', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (628, 0, '2004-11-14 13:30:00', '2004-11-14 15:45:00', 'test', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (629, 0, '2004-11-14 13:30:00', '2004-11-14 15:45:00', 'test', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (630, 0, '2004-11-14 06:45:00', '2004-11-14 08:30:00', 'test', 14, 1, NULL, NULL);
INSERT INTO `occurences` VALUES (636, 502, '2005-02-01 10:00:00', '2005-02-01 17:00:00', '', 14, 306, NULL, NULL);
INSERT INTO `occurences` VALUES (637, 502, '2005-02-02 08:00:00', '2005-02-02 13:00:00', '', 14, 306, NULL, NULL);
INSERT INTO `occurences` VALUES (639, 638, '2005-02-01 10:00:00', '2005-02-01 11:00:00', 'test', 14, 306, NULL, NULL);
INSERT INTO `occurences` VALUES (649, 648, '2005-02-16 11:00:00', '2005-02-16 12:00:00', 'test', 0, 306, NULL, NULL);
INSERT INTO `occurences` VALUES (651, 650, '2005-02-16 11:00:00', '2005-02-16 12:00:00', 'test', 0, 306, NULL, NULL);
INSERT INTO `occurences` VALUES (653, 652, '2005-02-16 11:00:00', '2005-02-16 12:00:00', 'test', 0, 306, NULL, NULL);
INSERT INTO `occurences` VALUES (655, 654, '2005-02-16 10:00:00', '2005-02-16 10:30:00', 'Test 2', 0, 306, NULL, NULL);
INSERT INTO `occurences` VALUES (703, 702, '2005-02-16 07:45:00', '2005-02-16 10:00:00', 'An Apointment', 14, 306, 1, 273);
INSERT INTO `occurences` VALUES (671, 663, '2005-02-07 08:00:00', '2005-02-07 10:00:00', '', 14, 306, NULL, NULL);
INSERT INTO `occurences` VALUES (672, 663, '2005-02-14 08:00:00', '2005-02-14 10:00:00', '', 14, 306, NULL, NULL);
INSERT INTO `occurences` VALUES (673, 663, '2005-02-21 08:00:00', '2005-02-21 10:00:00', '', 14, 306, NULL, NULL);
INSERT INTO `occurences` VALUES (674, 663, '2005-02-07 08:00:00', '2005-02-07 10:00:00', '', 14, 306, NULL, NULL);
INSERT INTO `occurences` VALUES (675, 663, '2005-02-14 08:00:00', '2005-02-14 10:00:00', '', 14, 306, NULL, NULL);
INSERT INTO `occurences` VALUES (676, 663, '2005-02-21 08:00:00', '2005-02-21 10:00:00', '', 14, 306, NULL, NULL);
INSERT INTO `occurences` VALUES (677, 663, '2005-02-07 08:00:00', '2005-02-07 11:00:00', '', 14, 306, NULL, NULL);
INSERT INTO `occurences` VALUES (678, 663, '2005-02-14 08:00:00', '2005-02-14 11:00:00', '', 14, 306, NULL, NULL);
INSERT INTO `occurences` VALUES (679, 663, '2005-02-21 08:00:00', '2005-02-21 11:00:00', '', 14, 306, NULL, NULL);
INSERT INTO `occurences` VALUES (681, 680, '2005-02-16 04:00:00', '2005-02-16 05:00:00', 'Blah', 14, 306, NULL, NULL);
INSERT INTO `occurences` VALUES (693, 692, '2005-02-18 09:00:00', '2005-02-18 10:00:00', 'test', 14, 306, NULL, NULL);
INSERT INTO `occurences` VALUES (695, 694, '2005-02-18 09:00:00', '2005-02-18 10:00:00', 'test', 14, 306, NULL, NULL);
INSERT INTO `occurences` VALUES (697, 696, '2005-02-18 09:00:00', '2005-02-18 10:00:00', 'test', 14, 306, NULL, NULL);
INSERT INTO `occurences` VALUES (699, 698, '2005-02-18 09:00:00', '2005-02-18 10:00:00', 'test', 14, 306, NULL, NULL);
INSERT INTO `occurences` VALUES (701, 700, '2005-02-18 09:00:00', '2005-02-18 10:00:00', 'test', 14, 306, 1, 273);

-- --------------------------------------------------------

-- 
-- Table structure for table `ownership`
-- 

DROP TABLE IF EXISTS `ownership`;
CREATE TABLE `ownership` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `id` (`id`)
) TYPE=InnoDB COMMENT='Stores which items are owned by which user';

-- 
-- Dumping data for table `ownership`
-- 

INSERT INTO `ownership` VALUES (8, 1);
INSERT INTO `ownership` VALUES (640, 1);
INSERT INTO `ownership` VALUES (641, 1);
INSERT INTO `ownership` VALUES (642, 1);
INSERT INTO `ownership` VALUES (643, 1);
INSERT INTO `ownership` VALUES (644, 1);
INSERT INTO `ownership` VALUES (645, 1);
INSERT INTO `ownership` VALUES (646, 1);
INSERT INTO `ownership` VALUES (647, 1);
INSERT INTO `ownership` VALUES (648, 1);
INSERT INTO `ownership` VALUES (649, 1);
INSERT INTO `ownership` VALUES (650, 1);
INSERT INTO `ownership` VALUES (651, 1);
INSERT INTO `ownership` VALUES (652, 1);
INSERT INTO `ownership` VALUES (653, 1);
INSERT INTO `ownership` VALUES (654, 1);
INSERT INTO `ownership` VALUES (655, 1);
INSERT INTO `ownership` VALUES (656, 1);
INSERT INTO `ownership` VALUES (657, 1);
INSERT INTO `ownership` VALUES (658, 1);
INSERT INTO `ownership` VALUES (659, 1);
INSERT INTO `ownership` VALUES (660, 1);
INSERT INTO `ownership` VALUES (661, 1);
INSERT INTO `ownership` VALUES (662, 1);
INSERT INTO `ownership` VALUES (663, 1);
INSERT INTO `ownership` VALUES (664, 1);
INSERT INTO `ownership` VALUES (665, 1);
INSERT INTO `ownership` VALUES (666, 1);
INSERT INTO `ownership` VALUES (667, 1);
INSERT INTO `ownership` VALUES (668, 1);
INSERT INTO `ownership` VALUES (669, 1);
INSERT INTO `ownership` VALUES (670, 1);
INSERT INTO `ownership` VALUES (671, 1);
INSERT INTO `ownership` VALUES (672, 1);
INSERT INTO `ownership` VALUES (673, 1);
INSERT INTO `ownership` VALUES (674, 1);
INSERT INTO `ownership` VALUES (675, 1);
INSERT INTO `ownership` VALUES (676, 1);
INSERT INTO `ownership` VALUES (677, 1);
INSERT INTO `ownership` VALUES (678, 1);
INSERT INTO `ownership` VALUES (679, 1);
INSERT INTO `ownership` VALUES (680, 1);
INSERT INTO `ownership` VALUES (681, 1);
INSERT INTO `ownership` VALUES (682, 1);
INSERT INTO `ownership` VALUES (683, 1);
INSERT INTO `ownership` VALUES (684, 1);
INSERT INTO `ownership` VALUES (685, 1);
INSERT INTO `ownership` VALUES (686, 1);
INSERT INTO `ownership` VALUES (687, 1);
INSERT INTO `ownership` VALUES (688, 1);
INSERT INTO `ownership` VALUES (689, 1);
INSERT INTO `ownership` VALUES (690, 1);
INSERT INTO `ownership` VALUES (691, 1);
INSERT INTO `ownership` VALUES (692, 1);
INSERT INTO `ownership` VALUES (693, 1);
INSERT INTO `ownership` VALUES (694, 1);
INSERT INTO `ownership` VALUES (695, 1);
INSERT INTO `ownership` VALUES (696, 1);
INSERT INTO `ownership` VALUES (697, 1);
INSERT INTO `ownership` VALUES (698, 1);
INSERT INTO `ownership` VALUES (699, 1);
INSERT INTO `ownership` VALUES (700, 1);
INSERT INTO `ownership` VALUES (701, 1);
INSERT INTO `ownership` VALUES (702, 1);
INSERT INTO `ownership` VALUES (703, 1);
INSERT INTO `ownership` VALUES (704, 1);
INSERT INTO `ownership` VALUES (705, 1);
INSERT INTO `ownership` VALUES (706, 1);
INSERT INTO `ownership` VALUES (707, 1);
INSERT INTO `ownership` VALUES (708, 1);
INSERT INTO `ownership` VALUES (709, 1);
INSERT INTO `ownership` VALUES (710, 1);
INSERT INTO `ownership` VALUES (711, 1);
INSERT INTO `ownership` VALUES (712, 1);
INSERT INTO `ownership` VALUES (713, 1);
INSERT INTO `ownership` VALUES (714, 1);
INSERT INTO `ownership` VALUES (715, 1);
INSERT INTO `ownership` VALUES (716, 1);
INSERT INTO `ownership` VALUES (717, 1);
INSERT INTO `ownership` VALUES (718, 1);
INSERT INTO `ownership` VALUES (719, 1);
INSERT INTO `ownership` VALUES (720, 1);
INSERT INTO `ownership` VALUES (721, 1);
INSERT INTO `ownership` VALUES (722, 1);
INSERT INTO `ownership` VALUES (723, 1);
INSERT INTO `ownership` VALUES (724, 1);
INSERT INTO `ownership` VALUES (725, 1);
INSERT INTO `ownership` VALUES (726, 1);
INSERT INTO `ownership` VALUES (727, 1);
INSERT INTO `ownership` VALUES (728, 1);
INSERT INTO `ownership` VALUES (729, 1);
INSERT INTO `ownership` VALUES (730, 1);
INSERT INTO `ownership` VALUES (731, 1);
INSERT INTO `ownership` VALUES (732, 1);
INSERT INTO `ownership` VALUES (733, 1);
INSERT INTO `ownership` VALUES (734, 1);
INSERT INTO `ownership` VALUES (735, 1);
INSERT INTO `ownership` VALUES (736, 1);
INSERT INTO `ownership` VALUES (737, 1);
INSERT INTO `ownership` VALUES (738, 1);
INSERT INTO `ownership` VALUES (739, 1);
INSERT INTO `ownership` VALUES (740, 1);
INSERT INTO `ownership` VALUES (741, 1);
INSERT INTO `ownership` VALUES (742, 1);
INSERT INTO `ownership` VALUES (743, 1);
INSERT INTO `ownership` VALUES (744, 1);
INSERT INTO `ownership` VALUES (745, 1);
INSERT INTO `ownership` VALUES (746, 1);
INSERT INTO `ownership` VALUES (747, 1);
INSERT INTO `ownership` VALUES (748, 1);
INSERT INTO `ownership` VALUES (749, 1);
INSERT INTO `ownership` VALUES (750, 1);
INSERT INTO `ownership` VALUES (751, 1);
INSERT INTO `ownership` VALUES (752, 1);
INSERT INTO `ownership` VALUES (753, 1);
INSERT INTO `ownership` VALUES (754, 1);
INSERT INTO `ownership` VALUES (755, 1);
INSERT INTO `ownership` VALUES (756, 1);
INSERT INTO `ownership` VALUES (757, 1);
INSERT INTO `ownership` VALUES (758, 1);
INSERT INTO `ownership` VALUES (759, 1);
INSERT INTO `ownership` VALUES (760, 1);
INSERT INTO `ownership` VALUES (761, 1);
INSERT INTO `ownership` VALUES (762, 1);
INSERT INTO `ownership` VALUES (763, 1);
INSERT INTO `ownership` VALUES (764, 1);
INSERT INTO `ownership` VALUES (765, 1);
INSERT INTO `ownership` VALUES (766, 1);
INSERT INTO `ownership` VALUES (767, 1);
INSERT INTO `ownership` VALUES (768, 1);
INSERT INTO `ownership` VALUES (769, 1);
INSERT INTO `ownership` VALUES (770, 1);
INSERT INTO `ownership` VALUES (771, 1);
INSERT INTO `ownership` VALUES (772, 1);
INSERT INTO `ownership` VALUES (773, 1);
INSERT INTO `ownership` VALUES (774, 1);
INSERT INTO `ownership` VALUES (775, 1);
INSERT INTO `ownership` VALUES (776, 1);
INSERT INTO `ownership` VALUES (777, 1);
INSERT INTO `ownership` VALUES (778, 1);
INSERT INTO `ownership` VALUES (779, 1);
INSERT INTO `ownership` VALUES (780, 1);
INSERT INTO `ownership` VALUES (781, 1);
INSERT INTO `ownership` VALUES (782, 1);
INSERT INTO `ownership` VALUES (783, 1);
INSERT INTO `ownership` VALUES (784, 1);
INSERT INTO `ownership` VALUES (785, 1);
INSERT INTO `ownership` VALUES (786, 1);
INSERT INTO `ownership` VALUES (787, 1);
INSERT INTO `ownership` VALUES (788, 1);
INSERT INTO `ownership` VALUES (789, 1);
INSERT INTO `ownership` VALUES (790, 1);
INSERT INTO `ownership` VALUES (791, 1);
INSERT INTO `ownership` VALUES (793, 1);
INSERT INTO `ownership` VALUES (794, 1);
INSERT INTO `ownership` VALUES (795, 1);
INSERT INTO `ownership` VALUES (796, 1);
INSERT INTO `ownership` VALUES (797, 1);
INSERT INTO `ownership` VALUES (798, 1);
INSERT INTO `ownership` VALUES (799, 1);
INSERT INTO `ownership` VALUES (800, 1);
INSERT INTO `ownership` VALUES (801, 1);
INSERT INTO `ownership` VALUES (802, 1);
INSERT INTO `ownership` VALUES (803, 1);
INSERT INTO `ownership` VALUES (804, 1);
INSERT INTO `ownership` VALUES (805, 1);
INSERT INTO `ownership` VALUES (806, 1);
INSERT INTO `ownership` VALUES (807, 1);
INSERT INTO `ownership` VALUES (808, 1);
INSERT INTO `ownership` VALUES (809, 1);
INSERT INTO `ownership` VALUES (972, 1);
INSERT INTO `ownership` VALUES (973, 1);
INSERT INTO `ownership` VALUES (974, 1);
INSERT INTO `ownership` VALUES (975, 1);
INSERT INTO `ownership` VALUES (976, 1);
INSERT INTO `ownership` VALUES (977, 1);
INSERT INTO `ownership` VALUES (978, 1);
INSERT INTO `ownership` VALUES (979, 1);
INSERT INTO `ownership` VALUES (980, 1);
INSERT INTO `ownership` VALUES (981, 1);
INSERT INTO `ownership` VALUES (982, 1);
INSERT INTO `ownership` VALUES (983, 1);
INSERT INTO `ownership` VALUES (984, 1);
INSERT INTO `ownership` VALUES (985, 1);
INSERT INTO `ownership` VALUES (986, 1);
INSERT INTO `ownership` VALUES (987, 1);
INSERT INTO `ownership` VALUES (988, 1);
INSERT INTO `ownership` VALUES (989, 1);
INSERT INTO `ownership` VALUES (990, 1);
INSERT INTO `ownership` VALUES (994, 1);
INSERT INTO `ownership` VALUES (995, 1);
INSERT INTO `ownership` VALUES (996, 1);
INSERT INTO `ownership` VALUES (997, 1);
INSERT INTO `ownership` VALUES (998, 1);
INSERT INTO `ownership` VALUES (999, 1);
INSERT INTO `ownership` VALUES (1000, 1);
INSERT INTO `ownership` VALUES (1001, 1);
INSERT INTO `ownership` VALUES (1002, 1);
INSERT INTO `ownership` VALUES (1003, 1);
INSERT INTO `ownership` VALUES (1004, 1);
INSERT INTO `ownership` VALUES (1005, 1);
INSERT INTO `ownership` VALUES (1006, 1);
INSERT INTO `ownership` VALUES (1007, 1);
INSERT INTO `ownership` VALUES (1008, 1);
INSERT INTO `ownership` VALUES (1009, 1);
INSERT INTO `ownership` VALUES (1010, 1);
INSERT INTO `ownership` VALUES (1011, 1);
INSERT INTO `ownership` VALUES (1012, 1);
INSERT INTO `ownership` VALUES (1013, 1);
INSERT INTO `ownership` VALUES (1014, 1);
INSERT INTO `ownership` VALUES (1015, 1);
INSERT INTO `ownership` VALUES (1016, 1);
INSERT INTO `ownership` VALUES (1017, 1);
INSERT INTO `ownership` VALUES (1018, 1);
INSERT INTO `ownership` VALUES (1019, 1);
INSERT INTO `ownership` VALUES (1020, 1);
INSERT INTO `ownership` VALUES (1021, 1);
INSERT INTO `ownership` VALUES (1022, 1);
INSERT INTO `ownership` VALUES (1023, 1);
INSERT INTO `ownership` VALUES (1024, 1);
INSERT INTO `ownership` VALUES (1025, 1);
INSERT INTO `ownership` VALUES (1026, 1);
INSERT INTO `ownership` VALUES (1027, 1);
INSERT INTO `ownership` VALUES (1028, 1);
INSERT INTO `ownership` VALUES (1029, 1);
INSERT INTO `ownership` VALUES (1030, 1);
INSERT INTO `ownership` VALUES (1031, 1);
INSERT INTO `ownership` VALUES (1032, 1);
INSERT INTO `ownership` VALUES (1033, 1);
INSERT INTO `ownership` VALUES (1034, 1);
INSERT INTO `ownership` VALUES (1035, 1);
INSERT INTO `ownership` VALUES (1036, 1);
INSERT INTO `ownership` VALUES (1037, 1);
INSERT INTO `ownership` VALUES (1038, 1);
INSERT INTO `ownership` VALUES (1039, 1);
INSERT INTO `ownership` VALUES (1040, 1);
INSERT INTO `ownership` VALUES (1041, 1);
INSERT INTO `ownership` VALUES (1042, 1);
INSERT INTO `ownership` VALUES (1043, 1);
INSERT INTO `ownership` VALUES (1044, 1);
INSERT INTO `ownership` VALUES (1045, 1);
INSERT INTO `ownership` VALUES (1046, 1);
INSERT INTO `ownership` VALUES (1047, 1);
INSERT INTO `ownership` VALUES (1048, 1);
INSERT INTO `ownership` VALUES (1049, 1);
INSERT INTO `ownership` VALUES (1050, 1);
INSERT INTO `ownership` VALUES (1051, 1);
INSERT INTO `ownership` VALUES (1052, 1);
INSERT INTO `ownership` VALUES (1053, 1);
INSERT INTO `ownership` VALUES (1054, 1);
INSERT INTO `ownership` VALUES (810, 5430);
INSERT INTO `ownership` VALUES (811, 5430);
INSERT INTO `ownership` VALUES (812, 5430);
INSERT INTO `ownership` VALUES (813, 5430);
INSERT INTO `ownership` VALUES (814, 5430);
INSERT INTO `ownership` VALUES (815, 5430);
INSERT INTO `ownership` VALUES (816, 5430);
INSERT INTO `ownership` VALUES (817, 5430);
INSERT INTO `ownership` VALUES (818, 5430);
INSERT INTO `ownership` VALUES (819, 5430);
INSERT INTO `ownership` VALUES (820, 5430);
INSERT INTO `ownership` VALUES (821, 5430);
INSERT INTO `ownership` VALUES (822, 5430);
INSERT INTO `ownership` VALUES (823, 5430);
INSERT INTO `ownership` VALUES (824, 5430);
INSERT INTO `ownership` VALUES (825, 5430);
INSERT INTO `ownership` VALUES (826, 5430);
INSERT INTO `ownership` VALUES (827, 5430);
INSERT INTO `ownership` VALUES (828, 5430);
INSERT INTO `ownership` VALUES (829, 5430);
INSERT INTO `ownership` VALUES (830, 5430);
INSERT INTO `ownership` VALUES (831, 5430);
INSERT INTO `ownership` VALUES (832, 5430);
INSERT INTO `ownership` VALUES (833, 5430);
INSERT INTO `ownership` VALUES (834, 5430);
INSERT INTO `ownership` VALUES (835, 5430);
INSERT INTO `ownership` VALUES (836, 5430);
INSERT INTO `ownership` VALUES (837, 5430);
INSERT INTO `ownership` VALUES (838, 5430);
INSERT INTO `ownership` VALUES (839, 5430);
INSERT INTO `ownership` VALUES (840, 5430);
INSERT INTO `ownership` VALUES (841, 5430);
INSERT INTO `ownership` VALUES (842, 5430);
INSERT INTO `ownership` VALUES (843, 5430);
INSERT INTO `ownership` VALUES (844, 5430);
INSERT INTO `ownership` VALUES (845, 5430);
INSERT INTO `ownership` VALUES (846, 5430);
INSERT INTO `ownership` VALUES (847, 5430);
INSERT INTO `ownership` VALUES (848, 5430);
INSERT INTO `ownership` VALUES (849, 5430);
INSERT INTO `ownership` VALUES (850, 5430);
INSERT INTO `ownership` VALUES (851, 5430);
INSERT INTO `ownership` VALUES (852, 5430);
INSERT INTO `ownership` VALUES (853, 5430);
INSERT INTO `ownership` VALUES (854, 5430);
INSERT INTO `ownership` VALUES (855, 5430);
INSERT INTO `ownership` VALUES (856, 5430);
INSERT INTO `ownership` VALUES (857, 5430);
INSERT INTO `ownership` VALUES (858, 5430);
INSERT INTO `ownership` VALUES (859, 5430);
INSERT INTO `ownership` VALUES (860, 5430);
INSERT INTO `ownership` VALUES (861, 5430);
INSERT INTO `ownership` VALUES (862, 5430);
INSERT INTO `ownership` VALUES (863, 5430);
INSERT INTO `ownership` VALUES (864, 5430);
INSERT INTO `ownership` VALUES (865, 5430);
INSERT INTO `ownership` VALUES (866, 5430);
INSERT INTO `ownership` VALUES (867, 5430);
INSERT INTO `ownership` VALUES (868, 5430);
INSERT INTO `ownership` VALUES (869, 5430);
INSERT INTO `ownership` VALUES (870, 5430);
INSERT INTO `ownership` VALUES (871, 5430);
INSERT INTO `ownership` VALUES (872, 5430);
INSERT INTO `ownership` VALUES (873, 5430);
INSERT INTO `ownership` VALUES (874, 5430);
INSERT INTO `ownership` VALUES (875, 5430);
INSERT INTO `ownership` VALUES (876, 5430);
INSERT INTO `ownership` VALUES (877, 5430);
INSERT INTO `ownership` VALUES (878, 5430);
INSERT INTO `ownership` VALUES (879, 5430);
INSERT INTO `ownership` VALUES (880, 5430);
INSERT INTO `ownership` VALUES (881, 5430);
INSERT INTO `ownership` VALUES (882, 5430);
INSERT INTO `ownership` VALUES (883, 5430);
INSERT INTO `ownership` VALUES (884, 5430);
INSERT INTO `ownership` VALUES (885, 5430);
INSERT INTO `ownership` VALUES (886, 5430);
INSERT INTO `ownership` VALUES (887, 5430);
INSERT INTO `ownership` VALUES (888, 5430);
INSERT INTO `ownership` VALUES (889, 5430);
INSERT INTO `ownership` VALUES (890, 5430);
INSERT INTO `ownership` VALUES (891, 5430);
INSERT INTO `ownership` VALUES (892, 5430);
INSERT INTO `ownership` VALUES (893, 5430);
INSERT INTO `ownership` VALUES (894, 5430);
INSERT INTO `ownership` VALUES (895, 5430);
INSERT INTO `ownership` VALUES (896, 5430);
INSERT INTO `ownership` VALUES (897, 5430);
INSERT INTO `ownership` VALUES (898, 5430);
INSERT INTO `ownership` VALUES (899, 5430);
INSERT INTO `ownership` VALUES (900, 5430);
INSERT INTO `ownership` VALUES (901, 5430);
INSERT INTO `ownership` VALUES (902, 5430);
INSERT INTO `ownership` VALUES (903, 5430);
INSERT INTO `ownership` VALUES (904, 5430);
INSERT INTO `ownership` VALUES (905, 5430);
INSERT INTO `ownership` VALUES (906, 5430);
INSERT INTO `ownership` VALUES (907, 5430);
INSERT INTO `ownership` VALUES (908, 5430);
INSERT INTO `ownership` VALUES (909, 5430);
INSERT INTO `ownership` VALUES (910, 5430);
INSERT INTO `ownership` VALUES (911, 5430);
INSERT INTO `ownership` VALUES (912, 5430);
INSERT INTO `ownership` VALUES (913, 5430);
INSERT INTO `ownership` VALUES (914, 5430);
INSERT INTO `ownership` VALUES (915, 5430);
INSERT INTO `ownership` VALUES (916, 5430);
INSERT INTO `ownership` VALUES (917, 5430);
INSERT INTO `ownership` VALUES (918, 5430);
INSERT INTO `ownership` VALUES (919, 5430);
INSERT INTO `ownership` VALUES (920, 5430);
INSERT INTO `ownership` VALUES (921, 5430);
INSERT INTO `ownership` VALUES (922, 5430);
INSERT INTO `ownership` VALUES (923, 5430);
INSERT INTO `ownership` VALUES (924, 5430);
INSERT INTO `ownership` VALUES (925, 5430);
INSERT INTO `ownership` VALUES (926, 5430);
INSERT INTO `ownership` VALUES (927, 5430);
INSERT INTO `ownership` VALUES (928, 5430);
INSERT INTO `ownership` VALUES (929, 5430);
INSERT INTO `ownership` VALUES (930, 5430);
INSERT INTO `ownership` VALUES (931, 5430);
INSERT INTO `ownership` VALUES (932, 5430);
INSERT INTO `ownership` VALUES (933, 5430);
INSERT INTO `ownership` VALUES (934, 5430);
INSERT INTO `ownership` VALUES (935, 5430);
INSERT INTO `ownership` VALUES (936, 5430);
INSERT INTO `ownership` VALUES (937, 5430);
INSERT INTO `ownership` VALUES (938, 5430);
INSERT INTO `ownership` VALUES (939, 5430);
INSERT INTO `ownership` VALUES (940, 5430);
INSERT INTO `ownership` VALUES (941, 5430);
INSERT INTO `ownership` VALUES (942, 5430);
INSERT INTO `ownership` VALUES (943, 5430);
INSERT INTO `ownership` VALUES (944, 5430);
INSERT INTO `ownership` VALUES (945, 5430);
INSERT INTO `ownership` VALUES (946, 5430);
INSERT INTO `ownership` VALUES (947, 5430);
INSERT INTO `ownership` VALUES (948, 5430);
INSERT INTO `ownership` VALUES (949, 5430);
INSERT INTO `ownership` VALUES (950, 5430);
INSERT INTO `ownership` VALUES (951, 5430);
INSERT INTO `ownership` VALUES (952, 5430);
INSERT INTO `ownership` VALUES (953, 5430);
INSERT INTO `ownership` VALUES (954, 5430);
INSERT INTO `ownership` VALUES (955, 5430);
INSERT INTO `ownership` VALUES (956, 5430);
INSERT INTO `ownership` VALUES (957, 5430);
INSERT INTO `ownership` VALUES (958, 5430);
INSERT INTO `ownership` VALUES (959, 5430);
INSERT INTO `ownership` VALUES (960, 5430);
INSERT INTO `ownership` VALUES (961, 5430);
INSERT INTO `ownership` VALUES (962, 5430);
INSERT INTO `ownership` VALUES (963, 5430);
INSERT INTO `ownership` VALUES (964, 5430);
INSERT INTO `ownership` VALUES (965, 5430);
INSERT INTO `ownership` VALUES (966, 5430);
INSERT INTO `ownership` VALUES (967, 5430);
INSERT INTO `ownership` VALUES (968, 5430);
INSERT INTO `ownership` VALUES (969, 5430);
INSERT INTO `ownership` VALUES (970, 5430);
INSERT INTO `ownership` VALUES (971, 5430);

-- --------------------------------------------------------

-- 
-- Table structure for table `patient`
-- 

DROP TABLE IF EXISTS `patient`;
CREATE TABLE `patient` (
  `person_id` int(11) NOT NULL default '0',
  `is_default_provider_primary` int(11) NOT NULL default '0',
  `default_provider` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`)
) TYPE=InnoDB COMMENT='An patient extends the person entity';

-- 
-- Dumping data for table `patient`
-- 

INSERT INTO `patient` VALUES (955, 0, 0);
INSERT INTO `patient` VALUES (975, 0, 0);
INSERT INTO `patient` VALUES (977, 0, 0);
INSERT INTO `patient` VALUES (979, 0, 0);
INSERT INTO `patient` VALUES (981, 0, 0);
INSERT INTO `patient` VALUES (983, 0, 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `person`
-- 

DROP TABLE IF EXISTS `person`;
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
) TYPE=InnoDB COMMENT='A person in the system';

-- 
-- Dumping data for table `person`
-- 

INSERT INTO `person` VALUES (955, '', 'Eichorn', 'Joshua', 'R', 0, '', '0000-00-00', '', '', '', '', '', '', '123-456-7890', 1);
INSERT INTO `person` VALUES (983, '', 'Person', 'Random', '', 1, '', '0000-00-00', '', '', '', '', '', '', '12345678', 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `person_address`
-- 

DROP TABLE IF EXISTS `person_address`;
CREATE TABLE `person_address` (
  `person_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  `address_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`,`address_id`),
  KEY `address_id` (`address_id`),
  KEY `person_id` (`person_id`)
) TYPE=InnoDB COMMENT='Links a person to a address specifying the address type';

-- 
-- Dumping data for table `person_address`
-- 

INSERT INTO `person_address` VALUES (955, 957, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `person_company`
-- 

DROP TABLE IF EXISTS `person_company`;
CREATE TABLE `person_company` (
  `person_id` int(11) NOT NULL default '0',
  `company_id` int(11) NOT NULL default '0',
  `person_type` int(11) default NULL,
  PRIMARY KEY  (`person_id`,`company_id`),
  KEY `person_id` (`person_id`),
  KEY `company_id` (`company_id`)
) TYPE=InnoDB COMMENT='Links a person to a company and optionaly specifies the lin';

-- 
-- Dumping data for table `person_company`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `person_number`
-- 

DROP TABLE IF EXISTS `person_number`;
CREATE TABLE `person_number` (
  `person_id` int(11) NOT NULL default '0',
  `number_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`,`number_id`),
  KEY `person_id` (`person_id`),
  KEY `phone_id` (`number_id`)
) TYPE=InnoDB COMMENT='Links between people and phone_numbers';

-- 
-- Dumping data for table `person_number`
-- 

INSERT INTO `person_number` VALUES (2, 784);
INSERT INTO `person_number` VALUES (786, 793);
INSERT INTO `person_number` VALUES (786, 794);
INSERT INTO `person_number` VALUES (955, 956);

-- --------------------------------------------------------

-- 
-- Table structure for table `person_person`
-- 

DROP TABLE IF EXISTS `person_person`;
CREATE TABLE `person_person` (
  `person_id` int(11) NOT NULL default '0',
  `related_person_id` int(11) NOT NULL default '0',
  `relation_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`,`related_person_id`,`relation_type`),
  KEY `person_id` (`person_id`),
  KEY `related_person_id` (`related_person_id`)
) TYPE=InnoDB;

-- 
-- Dumping data for table `person_person`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `person_type`
-- 

DROP TABLE IF EXISTS `person_type`;
CREATE TABLE `person_type` (
  `person_id` int(11) NOT NULL default '0',
  `person_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`,`person_type`),
  KEY `person_id` (`person_id`),
  KEY `person_type` (`person_type`)
) TYPE=InnoDB COMMENT='Link to specify person type';

-- 
-- Dumping data for table `person_type`
-- 

INSERT INTO `person_type` VALUES (983, 2);

-- --------------------------------------------------------

-- 
-- Table structure for table `practice_address`
-- 

DROP TABLE IF EXISTS `practice_address`;
CREATE TABLE `practice_address` (
  `practice_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  `address_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`practice_id`,`address_id`),
  KEY `address_id` (`address_id`),
  KEY `practice_id` (`practice_id`)
) TYPE=InnoDB COMMENT='Links a practice to a address specifying the address type';

-- 
-- Dumping data for table `practice_address`
-- 

INSERT INTO `practice_address` VALUES (2, 769, 0);
INSERT INTO `practice_address` VALUES (2, 772, 0);
INSERT INTO `practice_address` VALUES (2, 775, 0);
INSERT INTO `practice_address` VALUES (2, 778, 0);
INSERT INTO `practice_address` VALUES (2, 780, 1);
INSERT INTO `practice_address` VALUES (2, 781, 0);
INSERT INTO `practice_address` VALUES (2, 783, 2);

-- --------------------------------------------------------

-- 
-- Table structure for table `practices`
-- 

DROP TABLE IF EXISTS `practices`;
CREATE TABLE `practices` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `website` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `practices`
-- 

INSERT INTO `practices` VALUES (2, 'Medical Associates', '');
INSERT INTO `practices` VALUES (746, '', '');
INSERT INTO `practices` VALUES (753, '', '');

-- --------------------------------------------------------

-- 
-- Table structure for table `preferences`
-- 

DROP TABLE IF EXISTS `preferences`;
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

-- 
-- Dumping data for table `preferences`
-- 

INSERT INTO `preferences` VALUES (9000, 'Defaults', '', 0, 1, 4);
INSERT INTO `preferences` VALUES (9001, 'Special Event Color', '#123444', 9000, 2, 3);

-- --------------------------------------------------------

-- 
-- Table structure for table `provider`
-- 

DROP TABLE IF EXISTS `provider`;
CREATE TABLE `provider` (
  `person_id` int(11) NOT NULL default '0',
  `state_license_number` varchar(100) NOT NULL default '',
  `clia_number` varchar(100) NOT NULL default '',
  `dea_number` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`person_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `provider`
-- 

INSERT INTO `provider` VALUES (983, '', '', '');

-- --------------------------------------------------------

-- 
-- Table structure for table `provider_to_insurance`
-- 

DROP TABLE IF EXISTS `provider_to_insurance`;
CREATE TABLE `provider_to_insurance` (
  `provider_to_insurance_id` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  `insurance_program_id` int(11) NOT NULL default '0',
  `provider_number` varchar(100) NOT NULL default '',
  `provider_number_type` int(11) NOT NULL default '0',
  `group_number` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`provider_to_insurance_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `provider_to_insurance`
-- 

INSERT INTO `provider_to_insurance` VALUES (990, 983, 989, '123456', 1, 'blah blah');
INSERT INTO `provider_to_insurance` VALUES (999, 983, 989, '', 1, '');

-- --------------------------------------------------------

-- 
-- Table structure for table `report_templates`
-- 

DROP TABLE IF EXISTS `report_templates`;
CREATE TABLE `report_templates` (
  `report_template_id` int(11) NOT NULL default '0',
  `report_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `is_default` enum('yes','no') NOT NULL default 'yes',
  PRIMARY KEY  (`report_template_id`),
  KEY `report_id` (`report_id`)
) TYPE=InnoDB COMMENT='Report templates';

-- 
-- Dumping data for table `report_templates`
-- 

INSERT INTO `report_templates` VALUES (9, 8, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (10, 8, 'List View', 'no');
INSERT INTO `report_templates` VALUES (11, 10, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (792, 791, 'Default Template', 'yes');

-- --------------------------------------------------------

-- 
-- Table structure for table `reports`
-- 

DROP TABLE IF EXISTS `reports`;
CREATE TABLE `reports` (
  `id` int(11) NOT NULL auto_increment,
  `dbase` varchar(255) NOT NULL default '',
  `user` varchar(255) NOT NULL default '',
  `label` varchar(255) NOT NULL default '',
  `query` text NOT NULL,
  `description` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=InnoDB COMMENT='Report definitions TODO: change to Generic Seq' AUTO_INCREMENT=792 ;

-- 
-- Dumping data for table `reports`
-- 

INSERT INTO `reports` VALUES (8, '', '', 'User List', 'select * from users', '');
INSERT INTO `reports` VALUES (791, '', '', 'Codes with Fee Schedule', 'select code, code_text, data as fee from codes c inner join fee_schedule_data fsd using(code_id)', 'Codes that have had a feed added to them');

-- --------------------------------------------------------

-- 
-- Table structure for table `rooms`
-- 

DROP TABLE IF EXISTS `rooms`;
CREATE TABLE `rooms` (
  `id` int(11) NOT NULL default '0',
  `description` text NOT NULL,
  `number_seats` int(11) NOT NULL default '0',
  `building_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `rooms`
-- 

INSERT INTO `rooms` VALUES (14, 'this is for basic exams', 4, 12, 'Exam 1');
INSERT INTO `rooms` VALUES (622, '', 0, 620, 'Exam 1');

-- --------------------------------------------------------

-- 
-- Table structure for table `schedules`
-- 

DROP TABLE IF EXISTS `schedules`;
CREATE TABLE `schedules` (
  `id` int(11) NOT NULL default '0',
  `schedule_code` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `description_long` text NOT NULL,
  `description_short` text NOT NULL,
  `practice_id` int(11) NOT NULL default '0',
  `user_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `schedules`
-- 

INSERT INTO `schedules` VALUES (126, 'PS', 'Dr Smiths Schedule', '', '', 2, 1);
INSERT INTO `schedules` VALUES (501, 'PS', 'Provider 2', '', '', 2, 306);
INSERT INTO `schedules` VALUES (640, 'PS', 'Jnelson''s Office hours', '', '', 2, 306);
INSERT INTO `schedules` VALUES (662, 'PS', 'Test Schedule', '', '', 2, 306);

-- --------------------------------------------------------

-- 
-- Table structure for table `sequences`
-- 

DROP TABLE IF EXISTS `sequences`;
CREATE TABLE `sequences` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `sequences`
-- 

INSERT INTO `sequences` VALUES (1054);

-- --------------------------------------------------------

-- 
-- Table structure for table `states`
-- 

DROP TABLE IF EXISTS `states`;
CREATE TABLE `states` (
  `zone_code` varchar(32) NOT NULL default '',
  `zone_name` varchar(32) NOT NULL default '',
  `country` char(3) default NULL,
  PRIMARY KEY  (`zone_code`,`zone_name`),
  KEY `country` (`country`),
  KEY `zone_code` (`zone_code`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `states`
-- 

INSERT INTO `states` VALUES ('AL', 'Alabama', 'USA');
INSERT INTO `states` VALUES ('AK', 'Alaska', 'USA');
INSERT INTO `states` VALUES ('AS', 'American Samoa', 'USA');
INSERT INTO `states` VALUES ('AZ', 'Arizona', 'USA');
INSERT INTO `states` VALUES ('AR', 'Arkansas', 'USA');
INSERT INTO `states` VALUES ('AF', 'Armed Forces Africa', 'USA');
INSERT INTO `states` VALUES ('AA', 'Armed Forces Americas', 'USA');
INSERT INTO `states` VALUES ('AC', 'Armed Forces Canada', 'USA');
INSERT INTO `states` VALUES ('AE', 'Armed Forces Europe', 'USA');
INSERT INTO `states` VALUES ('AM', 'Armed Forces Middle East', 'USA');
INSERT INTO `states` VALUES ('AP', 'Armed Forces Pacific', 'USA');
INSERT INTO `states` VALUES ('CA', 'California', 'USA');
INSERT INTO `states` VALUES ('CO', 'Colorado', 'USA');
INSERT INTO `states` VALUES ('CT', 'Connecticut', 'USA');
INSERT INTO `states` VALUES ('DE', 'Delaware', 'USA');
INSERT INTO `states` VALUES ('DC', 'District of Columbia', 'USA');
INSERT INTO `states` VALUES ('FM', 'Federated States Of Micronesia', 'USA');
INSERT INTO `states` VALUES ('FL', 'Florida', 'USA');
INSERT INTO `states` VALUES ('GA', 'Georgia', 'USA');
INSERT INTO `states` VALUES ('GU', 'Guam', 'USA');
INSERT INTO `states` VALUES ('HI', 'Hawaii', 'USA');
INSERT INTO `states` VALUES ('ID', 'Idaho', 'USA');
INSERT INTO `states` VALUES ('IL', 'Illinois', 'USA');
INSERT INTO `states` VALUES ('IN', 'Indiana', 'USA');
INSERT INTO `states` VALUES ('IA', 'Iowa', 'USA');
INSERT INTO `states` VALUES ('KS', 'Kansas', 'USA');
INSERT INTO `states` VALUES ('KY', 'Kentucky', 'USA');
INSERT INTO `states` VALUES ('LA', 'Louisiana', 'USA');
INSERT INTO `states` VALUES ('ME', 'Maine', 'USA');
INSERT INTO `states` VALUES ('MH', 'Marshall Islands', 'USA');
INSERT INTO `states` VALUES ('MD', 'Maryland', 'USA');
INSERT INTO `states` VALUES ('MA', 'Massachusetts', 'USA');
INSERT INTO `states` VALUES ('MI', 'Michigan', 'USA');
INSERT INTO `states` VALUES ('MN', 'Minnesota', 'USA');
INSERT INTO `states` VALUES ('MS', 'Mississippi', 'USA');
INSERT INTO `states` VALUES ('MO', 'Missouri', 'USA');
INSERT INTO `states` VALUES ('MT', 'Montana', 'USA');
INSERT INTO `states` VALUES ('NE', 'Nebraska', 'USA');
INSERT INTO `states` VALUES ('NV', 'Nevada', 'USA');
INSERT INTO `states` VALUES ('NH', 'New Hampshire', 'USA');
INSERT INTO `states` VALUES ('NJ', 'New Jersey', 'USA');
INSERT INTO `states` VALUES ('NM', 'New Mexico', 'USA');
INSERT INTO `states` VALUES ('NY', 'New York', 'USA');
INSERT INTO `states` VALUES ('NC', 'North Carolina', 'USA');
INSERT INTO `states` VALUES ('ND', 'North Dakota', 'USA');
INSERT INTO `states` VALUES ('MP', 'Northern Mariana Islands', 'USA');
INSERT INTO `states` VALUES ('OH', 'Ohio', 'USA');
INSERT INTO `states` VALUES ('OK', 'Oklahoma', 'USA');
INSERT INTO `states` VALUES ('OR', 'Oregon', 'USA');
INSERT INTO `states` VALUES ('PW', 'Palau', 'USA');
INSERT INTO `states` VALUES ('PA', 'Pennsylvania', 'USA');
INSERT INTO `states` VALUES ('PR', 'Puerto Rico', 'USA');
INSERT INTO `states` VALUES ('RI', 'Rhode Island', 'USA');
INSERT INTO `states` VALUES ('SC', 'South Carolina', 'USA');
INSERT INTO `states` VALUES ('SD', 'South Dakota', 'USA');
INSERT INTO `states` VALUES ('TN', 'Tennessee', 'USA');
INSERT INTO `states` VALUES ('TX', 'Texas', 'USA');
INSERT INTO `states` VALUES ('UT', 'Utah', 'USA');
INSERT INTO `states` VALUES ('VT', 'Vermont', 'USA');
INSERT INTO `states` VALUES ('VI', 'Virgin Islands', 'USA');
INSERT INTO `states` VALUES ('VA', 'Virginia', 'USA');
INSERT INTO `states` VALUES ('WA', 'Washington', 'USA');
INSERT INTO `states` VALUES ('WV', 'West Virginia', 'USA');
INSERT INTO `states` VALUES ('WI', 'Wisconsin', 'USA');
INSERT INTO `states` VALUES ('WY', 'Wyoming', 'USA');
INSERT INTO `states` VALUES ('AB', 'Alberta', 'CAN');
INSERT INTO `states` VALUES ('BC', 'British Columbia', 'CAN');
INSERT INTO `states` VALUES ('MB', 'Manitoba', 'CAN');
INSERT INTO `states` VALUES ('NF', 'Newfoundland', 'CAN');
INSERT INTO `states` VALUES ('NB', 'New Brunswick', 'CAN');
INSERT INTO `states` VALUES ('NS', 'Nova Scotia', 'CAN');
INSERT INTO `states` VALUES ('NT', 'Northwest Territories', 'CAN');
INSERT INTO `states` VALUES ('NU', 'Nunavut', 'CAN');
INSERT INTO `states` VALUES ('ON', 'Ontario', 'CAN');
INSERT INTO `states` VALUES ('PE', 'Prince Edward Island', 'CAN');
INSERT INTO `states` VALUES ('QC', 'Quebec', 'CAN');
INSERT INTO `states` VALUES ('SK', 'Saskatchewan', 'CAN');
INSERT INTO `states` VALUES ('YT', 'Yukon Territory', 'CAN');
INSERT INTO `states` VALUES ('NDS', 'Niedersachsen', 'DEU');
INSERT INTO `states` VALUES ('BAW', 'Baden-Württemberg', 'DEU');
INSERT INTO `states` VALUES ('BAY', 'Bayern', 'DEU');
INSERT INTO `states` VALUES ('BER', 'Berlin', 'DEU');
INSERT INTO `states` VALUES ('BRG', 'Brandenburg', 'DEU');
INSERT INTO `states` VALUES ('BRE', 'Bremen', 'DEU');
INSERT INTO `states` VALUES ('HAM', 'Hamburg', 'DEU');
INSERT INTO `states` VALUES ('HES', 'Hessen', 'DEU');
INSERT INTO `states` VALUES ('MEC', 'Mecklenburg-Vorpommern', 'DEU');
INSERT INTO `states` VALUES ('NRW', 'Nordrhein-Westfalen', 'DEU');
INSERT INTO `states` VALUES ('RHE', 'Rheinland-Pfalz', 'DEU');
INSERT INTO `states` VALUES ('SAR', 'Saarland', 'DEU');
INSERT INTO `states` VALUES ('SAS', 'Sachsen', 'DEU');
INSERT INTO `states` VALUES ('SAC', 'Sachsen-Anhalt', 'DEU');
INSERT INTO `states` VALUES ('SCN', 'Schleswig-Holstein', 'DEU');
INSERT INTO `states` VALUES ('THE', 'Thüringen', 'DEU');
INSERT INTO `states` VALUES ('WI', 'Wien', 'AUT');
INSERT INTO `states` VALUES ('NO', 'Niederösterreich', 'AUT');
INSERT INTO `states` VALUES ('OO', 'Oberösterreich', 'AUT');
INSERT INTO `states` VALUES ('SB', 'Salzburg', 'AUT');
INSERT INTO `states` VALUES ('KN', 'Kärnten', 'AUT');
INSERT INTO `states` VALUES ('ST', 'Steiermark', 'AUT');
INSERT INTO `states` VALUES ('TI', 'Tirol', 'AUT');
INSERT INTO `states` VALUES ('BL', 'Burgenland', 'AUT');
INSERT INTO `states` VALUES ('VB', 'Voralberg', 'AUT');
INSERT INTO `states` VALUES ('AG', 'Aargau', 'CHE');
INSERT INTO `states` VALUES ('AI', 'Appenzell Innerrhoden', 'CHE');
INSERT INTO `states` VALUES ('AR', 'Appenzell Ausserrhoden', 'CHE');
INSERT INTO `states` VALUES ('BE', 'Bern', 'CHE');
INSERT INTO `states` VALUES ('BL', 'Basel-Landschaft', 'CHE');
INSERT INTO `states` VALUES ('BS', 'Basel-Stadt', 'CHE');
INSERT INTO `states` VALUES ('FR', 'Freiburg', 'CHE');
INSERT INTO `states` VALUES ('GE', 'Genf', 'CHE');
INSERT INTO `states` VALUES ('GL', 'Glarus', 'CHE');
INSERT INTO `states` VALUES ('JU', 'Graubünden', 'CHE');
INSERT INTO `states` VALUES ('JU', 'Jura', 'CHE');
INSERT INTO `states` VALUES ('LU', 'Luzern', 'CHE');
INSERT INTO `states` VALUES ('NE', 'Neuenburg', 'CHE');
INSERT INTO `states` VALUES ('NW', 'Nidwalden', 'CHE');
INSERT INTO `states` VALUES ('OW', 'Obwalden', 'CHE');
INSERT INTO `states` VALUES ('SG', 'St. Gallen', 'CHE');
INSERT INTO `states` VALUES ('SH', 'Schaffhausen', 'CHE');
INSERT INTO `states` VALUES ('SO', 'Solothurn', 'CHE');
INSERT INTO `states` VALUES ('SZ', 'Schwyz', 'CHE');
INSERT INTO `states` VALUES ('TG', 'Thurgau', 'CHE');
INSERT INTO `states` VALUES ('TI', 'Tessin', 'CHE');
INSERT INTO `states` VALUES ('UR', 'Uri', 'CHE');
INSERT INTO `states` VALUES ('VD', 'Waadt', 'CHE');
INSERT INTO `states` VALUES ('VS', 'Wallis', 'CHE');
INSERT INTO `states` VALUES ('ZG', 'Zug', 'CHE');
INSERT INTO `states` VALUES ('ZH', 'Zürich', 'CHE');
INSERT INTO `states` VALUES ('A Coruña', 'A Coruña', 'ESP');
INSERT INTO `states` VALUES ('Alava', 'Alava', 'ESP');
INSERT INTO `states` VALUES ('Albacete', 'Albacete', 'ESP');
INSERT INTO `states` VALUES ('Alicante', 'Alicante', 'ESP');
INSERT INTO `states` VALUES ('Almeria', 'Almeria', 'ESP');
INSERT INTO `states` VALUES ('Asturias', 'Asturias', 'ESP');
INSERT INTO `states` VALUES ('Avila', 'Avila', 'ESP');
INSERT INTO `states` VALUES ('Badajoz', 'Badajoz', 'ESP');
INSERT INTO `states` VALUES ('Baleares', 'Baleares', 'ESP');
INSERT INTO `states` VALUES ('Barcelona', 'Barcelona', 'ESP');
INSERT INTO `states` VALUES ('Burgos', 'Burgos', 'ESP');
INSERT INTO `states` VALUES ('Caceres', 'Caceres', 'ESP');
INSERT INTO `states` VALUES ('Cadiz', 'Cadiz', 'ESP');
INSERT INTO `states` VALUES ('Cantabria', 'Cantabria', 'ESP');
INSERT INTO `states` VALUES ('Castellon', 'Castellon', 'ESP');
INSERT INTO `states` VALUES ('Ceuta', 'Ceuta', 'ESP');
INSERT INTO `states` VALUES ('Ciudad Real', 'Ciudad Real', 'ESP');
INSERT INTO `states` VALUES ('Cordoba', 'Cordoba', 'ESP');
INSERT INTO `states` VALUES ('Cuenca', 'Cuenca', 'ESP');
INSERT INTO `states` VALUES ('Girona', 'Girona', 'ESP');
INSERT INTO `states` VALUES ('Granada', 'Granada', 'ESP');
INSERT INTO `states` VALUES ('Guadalajara', 'Guadalajara', 'ESP');
INSERT INTO `states` VALUES ('Guipuzcoa', 'Guipuzcoa', 'ESP');
INSERT INTO `states` VALUES ('Huelva', 'Huelva', 'ESP');
INSERT INTO `states` VALUES ('Huesca', 'Huesca', 'ESP');
INSERT INTO `states` VALUES ('Jaen', 'Jaen', 'ESP');
INSERT INTO `states` VALUES ('La Rioja', 'La Rioja', 'ESP');
INSERT INTO `states` VALUES ('Las Palmas', 'Las Palmas', 'ESP');
INSERT INTO `states` VALUES ('Leon', 'Leon', 'ESP');
INSERT INTO `states` VALUES ('Lleida', 'Lleida', 'ESP');
INSERT INTO `states` VALUES ('Lugo', 'Lugo', 'ESP');
INSERT INTO `states` VALUES ('Madrid', 'Madrid', 'ESP');
INSERT INTO `states` VALUES ('Malaga', 'Malaga', 'ESP');
INSERT INTO `states` VALUES ('Melilla', 'Melilla', 'ESP');
INSERT INTO `states` VALUES ('Murcia', 'Murcia', 'ESP');
INSERT INTO `states` VALUES ('Navarra', 'Navarra', 'ESP');
INSERT INTO `states` VALUES ('Ourense', 'Ourense', 'ESP');
INSERT INTO `states` VALUES ('Palencia', 'Palencia', 'ESP');
INSERT INTO `states` VALUES ('Pontevedra', 'Pontevedra', 'ESP');
INSERT INTO `states` VALUES ('Salamanca', 'Salamanca', 'ESP');
INSERT INTO `states` VALUES ('Santa Cruz de Tenerife', 'Santa Cruz de Tenerife', 'ESP');
INSERT INTO `states` VALUES ('Segovia', 'Segovia', 'ESP');
INSERT INTO `states` VALUES ('Sevilla', 'Sevilla', 'ESP');
INSERT INTO `states` VALUES ('Soria', 'Soria', 'ESP');
INSERT INTO `states` VALUES ('Tarragona', 'Tarragona', 'ESP');
INSERT INTO `states` VALUES ('Teruel', 'Teruel', 'ESP');
INSERT INTO `states` VALUES ('Toledo', 'Toledo', 'ESP');
INSERT INTO `states` VALUES ('Valencia', 'Valencia', 'ESP');
INSERT INTO `states` VALUES ('Valladolid', 'Valladolid', 'ESP');
INSERT INTO `states` VALUES ('Vizcaya', 'Vizcaya', 'ESP');
INSERT INTO `states` VALUES ('Zamora', 'Zamora', 'ESP');
INSERT INTO `states` VALUES ('Zaragoza', 'Zaragoza', 'ESP');

-- --------------------------------------------------------

-- 
-- Table structure for table `storage_date`
-- 

DROP TABLE IF EXISTS `storage_date`;
CREATE TABLE `storage_date` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(50) NOT NULL default '',
  `value` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`foreign_key`,`value_key`)
) TYPE=InnoDB COMMENT='Generic way to store date values';

-- 
-- Dumping data for table `storage_date`
-- 

INSERT INTO `storage_date` VALUES (802, 'test_string', '2005-03-10');
INSERT INTO `storage_date` VALUES (803, 'test_string', '2005-03-01');
INSERT INTO `storage_date` VALUES (804, 'test_string', '2005-03-11');
INSERT INTO `storage_date` VALUES (805, 'test_string', '2005-03-11');
INSERT INTO `storage_date` VALUES (806, 'test_string', '2005-03-31');
INSERT INTO `storage_date` VALUES (807, 'test_string', '2005-03-31');
INSERT INTO `storage_date` VALUES (808, 'test_string', '2005-03-31');
INSERT INTO `storage_date` VALUES (809, 'test_data', '2005-03-04');

-- --------------------------------------------------------

-- 
-- Table structure for table `storage_int`
-- 

DROP TABLE IF EXISTS `storage_int`;
CREATE TABLE `storage_int` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(50) NOT NULL default '',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`foreign_key`,`value_key`)
) TYPE=InnoDB COMMENT='Generic way to store integer values (also boolean)';

-- 
-- Dumping data for table `storage_int`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `storage_string`
-- 

DROP TABLE IF EXISTS `storage_string`;
CREATE TABLE `storage_string` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(50) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`foreign_key`,`value_key`)
) TYPE=InnoDB COMMENT='Generic way to string values';

-- 
-- Dumping data for table `storage_string`
-- 

INSERT INTO `storage_string` VALUES (802, '0', 'Blah blah');
INSERT INTO `storage_string` VALUES (803, '0', 'test 3');
INSERT INTO `storage_string` VALUES (804, '0', 'test 4');
INSERT INTO `storage_string` VALUES (805, '0', 'test 5');
INSERT INTO `storage_string` VALUES (806, '0', 'blah 45');
INSERT INTO `storage_string` VALUES (807, '0', 'blah 45');
INSERT INTO `storage_string` VALUES (808, '0', 'blah 45');
INSERT INTO `storage_string` VALUES (809, 'test_string', 'Test');
INSERT INTO `storage_string` VALUES (968, 'email', '');

-- --------------------------------------------------------

-- 
-- Table structure for table `superbill_data`
-- 

DROP TABLE IF EXISTS `superbill_data`;
CREATE TABLE `superbill_data` (
  `superbill_data_id` int(11) NOT NULL default '0',
  `superbill_id` int(11) NOT NULL default '0',
  `code_id` int(11) NOT NULL default '0',
  `status` int(11) NOT NULL default '0',
  PRIMARY KEY  (`superbill_data_id`),
  UNIQUE KEY `superbill_id` (`superbill_id`,`code_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `superbill_data`
-- 

INSERT INTO `superbill_data` VALUES (1026, 1, 26754, 0);
INSERT INTO `superbill_data` VALUES (1027, 1, 26752, 1);
INSERT INTO `superbill_data` VALUES (1028, 1, 26747, 1);
INSERT INTO `superbill_data` VALUES (1029, 1, 26744, 1);
INSERT INTO `superbill_data` VALUES (1030, 1, 26739, 1);
INSERT INTO `superbill_data` VALUES (1031, 1, 26735, 1);
INSERT INTO `superbill_data` VALUES (1032, 1, 26730, 1);
INSERT INTO `superbill_data` VALUES (1033, 1, 26729, 1);
INSERT INTO `superbill_data` VALUES (1034, 1, 26758, 1);
INSERT INTO `superbill_data` VALUES (1039, 1, 26761, 1);
INSERT INTO `superbill_data` VALUES (1042, 1, 26760, 0);
INSERT INTO `superbill_data` VALUES (1041, 1, 26759, 1);
INSERT INTO `superbill_data` VALUES (1051, 1, 26750, 1);
INSERT INTO `superbill_data` VALUES (1052, 1, 26749, 1);
INSERT INTO `superbill_data` VALUES (1053, 1, 26748, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `user`
-- 

DROP TABLE IF EXISTS `user`;
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
) TYPE=InnoDB COMMENT='Users in the System';

-- 
-- Dumping data for table `user`
-- 

INSERT INTO `user` VALUES (1, 'admin', 'admin', '', '', NULL, 'no', 0);
INSERT INTO `user` VALUES (984, 'jeichorn', 'test', '', '', 983, '', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `users_groups`
-- 

DROP TABLE IF EXISTS `users_groups`;
CREATE TABLE `users_groups` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  `foreign_id` int(11) NOT NULL default '0',
  `table` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_id` (`user_id`,`group_id`,`foreign_id`,`table`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `users_groups`
-- 

INSERT INTO `users_groups` VALUES (1, 1, 1, 0, '');
INSERT INTO `users_groups` VALUES (634, 306, 1, 0, '');
INSERT INTO `users_groups` VALUES (635, 306, 0, 0, '');

-- 
-- Constraints for dumped tables
-- 

-- 
-- Constraints for table `company`
-- 
ALTER TABLE `company`
  ADD CONSTRAINT `company_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `ownership` (`id`);

-- 
-- Constraints for table `company_address`
-- 
ALTER TABLE `company_address`
  ADD CONSTRAINT `company_address_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`),
  ADD CONSTRAINT `company_address_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `address` (`address_id`);

-- 
-- Constraints for table `company_company`
-- 
ALTER TABLE `company_company`
  ADD CONSTRAINT `company_company_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`),
  ADD CONSTRAINT `company_company_ibfk_2` FOREIGN KEY (`related_company_id`) REFERENCES `company` (`company_id`);

-- 
-- Constraints for table `company_number`
-- 
ALTER TABLE `company_number`
  ADD CONSTRAINT `company_number_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `ownership` (`id`),
  ADD CONSTRAINT `company_number_ibfk_2` FOREIGN KEY (`number_id`) REFERENCES `number` (`number_id`);

-- 
-- Constraints for table `company_type`
-- 
ALTER TABLE `company_type`
  ADD CONSTRAINT `company_type_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`);

-- 
-- Constraints for table `fee_schedule_data`
-- 
ALTER TABLE `fee_schedule_data`
  ADD CONSTRAINT `fee_schedule_data_ibfk_2` FOREIGN KEY (`revision_id`) REFERENCES `fee_schedule_revision` (`revision_id`),
  ADD CONSTRAINT `fee_schedule_data_ibfk_3` FOREIGN KEY (`fee_schedule_id`) REFERENCES `fee_schedule` (`fee_schedule_id`);

-- 
-- Constraints for table `person`
-- 
ALTER TABLE `person`
  ADD CONSTRAINT `person_ibfk_7` FOREIGN KEY (`person_id`) REFERENCES `ownership` (`id`);

-- 
-- Constraints for table `person_type`
-- 
ALTER TABLE `person_type`
  ADD CONSTRAINT `person_type_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`);

-- 
-- Constraints for table `user`
-- 
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`);

SET FOREIGN_KEY_CHECKS=1;

COMMIT;
