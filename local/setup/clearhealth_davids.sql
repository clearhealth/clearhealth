-- phpMyAdmin SQL Dump
-- version 2.6.1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Mar 19, 2005 at 02:20 AM
-- Server version: 4.0.18
-- PHP Version: 4.3.4

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
) TYPE=MyISAM COMMENT='An address that can be for a company or a person';

-- 
-- Dumping data for table `address`
-- 

INSERT INTO `address` VALUES (1124, '', '1 main st.', '', '', 0, 0, 0, '', '');
INSERT INTO `address` VALUES (8012, '', '1234 Some Street', '', 'Some City', 0, 0, 5, '12345', 'Some House');
INSERT INTO `address` VALUES (8016, '', '123 A Street', '', 'Los Angeles', 0, 0, 5, '90008', '');
INSERT INTO `address` VALUES (8020, '', '123 A Street', '', 'Los Angeles', 0, 0, 5, '90008', '');
INSERT INTO `address` VALUES (8024, '', '123 A Street', '', 'Los Angeles', 0, 0, 5, '90008', '');
INSERT INTO `address` VALUES (8028, '', '123 A Street', '', 'Los Angeles', 0, 0, 5, '90008', '');
INSERT INTO `address` VALUES (8032, '', '123 A Street', '', 'Los Angeles', 0, 0, 5, '90008', '');
INSERT INTO `address` VALUES (8036, '', '123 A Street', '', 'Los Angeles', 0, 0, 5, '90008', '');
INSERT INTO `address` VALUES (8040, '', '123 A Street', '', 'Los Angeles', 0, 0, 5, '90008', '');

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
) TYPE=MyISAM COMMENT='Links a building to a address specifying the address type';

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

INSERT INTO `buildings` VALUES (1123, '', 'Ukiah Office', 1122);

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
  `foreign_id` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `code_id` int(11) NOT NULL default '0',
  `modifier` int(11) NOT NULL default '0',
  `units` float(5,2) NOT NULL default '1.00',
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
) TYPE=MyISAM COMMENT='Base Company record most of the data is in linked tables';

-- 
-- Dumping data for table `company`
-- 

INSERT INTO `company` VALUES (1113, 'Grand Insurance Co', '', '', '', 'www.grand.com', 'no');
INSERT INTO `company` VALUES (2049, 'Blue Cross/ Blue Shield', 'bcbs', '', '', '', 'no');

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
) TYPE=MyISAM COMMENT='Links a company to a address specifying the address type';

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
) TYPE=MyISAM COMMENT='Relates a company to another company specify the type with a';

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
) TYPE=MyISAM COMMENT='Links between company and phone_numbers';

-- 
-- Dumping data for table `company_number`
-- 

INSERT INTO `company_number` VALUES (1113, 1115);

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
) TYPE=MyISAM COMMENT='Link to specify company type';

-- 
-- Dumping data for table `company_type`
-- 

INSERT INTO `company_type` VALUES (968, 1);
INSERT INTO `company_type` VALUES (1072, 1);
INSERT INTO `company_type` VALUES (1113, 1);
INSERT INTO `company_type` VALUES (2049, 1);

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
-- Table structure for table `encounter`
-- 

DROP TABLE IF EXISTS `encounter`;
CREATE TABLE `encounter` (
  `encounter_id` int(11) NOT NULL default '0',
  `encounter_reason` int(11) NOT NULL default '0',
  `patient_id` int(11) NOT NULL default '0',
  `building_id` int(11) NOT NULL default '0',
  `date_of_treatment` datetime NOT NULL default '0000-00-00 00:00:00',
  `treating_person_id` int(11) NOT NULL default '0',
  `timestamp` timestamp(14) NOT NULL,
  `last_change_user_id` int(11) NOT NULL default '0',
  `status` enum('closed','open','billed') NOT NULL default 'open',
  `occurence_id` int(11) default NULL,
  PRIMARY KEY  (`encounter_id`),
  KEY `building_id` (`building_id`),
  KEY `treating_person_id` (`treating_person_id`),
  KEY `last_change_user_id` (`last_change_user_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `encounter`
-- 

INSERT INTO `encounter` VALUES (2093, 1, 1707, 1123, '2005-03-15 00:00:00', 1110, '00000000000000', 0, 'open', 0);
INSERT INTO `encounter` VALUES (8061, 0, 1110, 1123, '2005-03-19 00:00:00', 1110, '00000000000000', 0, 'open', 8058);
INSERT INTO `encounter` VALUES (8063, 0, 1110, 1123, '2005-03-18 00:00:00', 1120, '00000000000000', 0, 'closed', 8056);

-- --------------------------------------------------------

-- 
-- Table structure for table `encounter_date`
-- 

DROP TABLE IF EXISTS `encounter_date`;
CREATE TABLE `encounter_date` (
  `encounter_date_id` int(11) NOT NULL default '0',
  `encounter_id` int(11) NOT NULL default '0',
  `date_type` int(11) NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`encounter_date_id`),
  KEY `encounter_id` (`encounter_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `encounter_date`
-- 

INSERT INTO `encounter_date` VALUES (2094, 2093, 1, '2005-03-07 00:00:00');

-- --------------------------------------------------------

-- 
-- Table structure for table `encounter_person`
-- 

DROP TABLE IF EXISTS `encounter_person`;
CREATE TABLE `encounter_person` (
  `encounter_person_id` int(11) NOT NULL default '0',
  `encounter_id` int(11) NOT NULL default '0',
  `person_type` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`encounter_person_id`),
  KEY `encounter_id` (`encounter_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `encounter_person`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `encounter_value`
-- 

DROP TABLE IF EXISTS `encounter_value`;
CREATE TABLE `encounter_value` (
  `encounter_value_id` int(11) NOT NULL default '0',
  `encounter_id` int(11) NOT NULL default '0',
  `value_type` int(11) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '0',
  PRIMARY KEY  (`encounter_value_id`),
  KEY `encounter_id` (`encounter_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `encounter_value`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `enumeration`
-- 

DROP TABLE IF EXISTS `enumeration`;
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
  `person_to_person_relation_type` enum('Dependant','Spouse','Grand Parent','Other') NOT NULL default 'Dependant',
  `number_type` enum('Home','Mobile','Work','Emergency') NOT NULL default 'Home',
  `address_type` enum('Home','Billing','Other') NOT NULL default 'Home',
  `appointment_reasons` enum('Physical','Followup','Tests') NOT NULL default 'Physical',
  `payer_type` enum('medicare','HMO','PPO','EPO','champus') NOT NULL default 'medicare',
  `identifier_type` enum('SSN','EIN','Special ID','') NOT NULL default 'SSN',
  PRIMARY KEY  (`name`)
) TYPE=MyISAM COMMENT='Each enum stored as a new col, metadata in 1 row per enum';

-- 
-- Dumping data for table `enumeration`
-- 

INSERT INTO `enumeration` VALUES ('gender', 'Gender', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'Home', 'Home', 'Physical', 'medicare', 'SSN');
INSERT INTO `enumeration` VALUES ('person_type', 'Person Type', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'Home', 'Home', 'Physical', 'medicare', 'SSN');
INSERT INTO `enumeration` VALUES ('company_type', 'Company Type', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'Home', 'Home', 'Physical', 'medicare', 'SSN');
INSERT INTO `enumeration` VALUES ('state', 'State', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'Home', 'Home', 'Physical', 'medicare', 'SSN');
INSERT INTO `enumeration` VALUES ('number_type', 'Phone Number Type', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'Home', 'Home', 'Physical', 'medicare', 'SSN');
INSERT INTO `enumeration` VALUES ('company_number_type', 'Company Number Type', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'Home', 'Home', 'Physical', 'medicare', 'SSN');
INSERT INTO `enumeration` VALUES ('address_type', 'Address Type', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'Home', 'Home', 'Physical', 'medicare', 'SSN');
INSERT INTO `enumeration` VALUES ('disposition', 'Disposition', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'Home', 'Home', 'Physical', 'medicare', 'SSN');
INSERT INTO `enumeration` VALUES ('quality_of_file', 'Quality of File', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'Home', 'Home', 'Physical', 'medicare', 'SSN');
INSERT INTO `enumeration` VALUES ('group_list', 'File Groups', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'Home', 'Home', 'Physical', 'medicare', 'SSN');
INSERT INTO `enumeration` VALUES ('identifier_type', 'Identifier Type', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'Home', 'Home', 'Physical', 'medicare', 'SSN');
INSERT INTO `enumeration` VALUES ('assigning', 'Assigning', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'Home', 'Home', 'Physical', 'medicare', 'SSN');
INSERT INTO `enumeration` VALUES ('relation_of_information_code', '', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'Home', 'Home', 'Physical', 'medicare', 'SSN');
INSERT INTO `enumeration` VALUES ('provider_number_type', 'Provider Number Type', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'Home', 'Home', 'Physical', 'medicare', 'SSN');
INSERT INTO `enumeration` VALUES ('subscriber_to_patient', 'Subscriber to patient', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'Home', 'Home', 'Physical', 'medicare', 'SSN');
INSERT INTO `enumeration` VALUES ('payer_type', 'Payer Type', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'Home', 'Home', 'Physical', 'medicare', 'SSN');
INSERT INTO `enumeration` VALUES ('person_to_person_relation_type', 'Person to person relation type', '', 'Male', 'Primary', 'Good', 'New', 'Alaska', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'Home', 'Home', 'Physical', 'medicare', 'SSN');
INSERT INTO `enumeration` VALUES ('appointment_reasons', 'Appointment Reasons', '', 'Male', 'Primary', 'Good', 'New', 'Alabama', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'Home', 'Home', 'Physical', 'medicare', 'SSN');

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

INSERT INTO `events` VALUES (1128, 'Office Hours', '', '', '', '', 1127);
INSERT INTO `events` VALUES (1562, 'Office Hours', '', '', '', '', 1561);
INSERT INTO `events` VALUES (1807, 'Normal Hours', '', '', '', '', 1806);
INSERT INTO `events` VALUES (8055, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (8057, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (8059, '', '', '', '', '', 0);
INSERT INTO `events` VALUES (8060, '', '', '', '', '', 0);

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
) TYPE=MyISAM;

-- 
-- Dumping data for table `fee_schedule`
-- 

INSERT INTO `fee_schedule` VALUES (2053, 'test', 'test', 'test');

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
) TYPE=MyISAM;

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
INSERT INTO `fee_schedule_data` VALUES (26752, 1, 711, 34, '');
INSERT INTO `fee_schedule_data` VALUES (26747, 1, 711, 45, '');

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
) TYPE=MyISAM;

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
) TYPE=MyISAM;

-- 
-- Dumping data for table `form`
-- 

INSERT INTO `form` VALUES (800, 'Test Data', 'Some random data');
INSERT INTO `form` VALUES (1710, 'Patient Vitals', 'Patient Vital Statistics');

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
INSERT INTO `form_data` VALUES (1010, 800, 0, '2005-03-08 19:03:03');
INSERT INTO `form_data` VALUES (2057, 800, 1110, '2005-03-14 15:09:50');

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
INSERT INTO `gacl_acl` VALUES (27, 'user', 1, 1, '', '', 1110340743);
INSERT INTO `gacl_acl` VALUES (28, 'user', 1, 1, '', '', 1110342647);

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

INSERT INTO `gacl_acl_seq` VALUES (28);
INSERT INTO `gacl_acl_seq` VALUES (28);

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
INSERT INTO `gacl_aco` VALUES (18, 'actions', 'delete_owner', 15, 'Delete Owner', 0);
INSERT INTO `gacl_aco` VALUES (19, 'actions', 'edit_owner', 16, 'Edit Owner', 0);

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
INSERT INTO `gacl_aco_map` VALUES (27, 'actions', 'delete_owner');
INSERT INTO `gacl_aco_map` VALUES (28, 'actions', 'edit_owner');

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

INSERT INTO `gacl_aco_seq` VALUES (19);
INSERT INTO `gacl_aco_seq` VALUES (19);

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
INSERT INTO `gacl_aro` VALUES (24, 'users', 'jconrad', 100, 'jconrad', 1);
INSERT INTO `gacl_aro` VALUES (25, 'users', 'mminton', 100, 'mminton', 1);

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
INSERT INTO `gacl_aro_groups_map` VALUES (27, 12);
INSERT INTO `gacl_aro_groups_map` VALUES (28, 12);

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

INSERT INTO `gacl_aro_seq` VALUES (25);
INSERT INTO `gacl_aro_seq` VALUES (25);

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
INSERT INTO `gacl_axo` VALUES (47, 'resources', 'form', 10, 'Section - Form', 0);
INSERT INTO `gacl_axo` VALUES (48, 'resources', 'billing', 10, 'Section - Billing', 0);
INSERT INTO `gacl_axo` VALUES (49, 'resources', 'admin', 10, 'Section - Admin', 0);
INSERT INTO `gacl_axo` VALUES (50, 'resources', 'document', 10, 'Section - Document', 0);
INSERT INTO `gacl_axo` VALUES (51, 'resources', 'documentcategory', 10, 'Section - DocumentCategory', 0);
INSERT INTO `gacl_axo` VALUES (52, 'resources', 'insurance', 10, 'Section - Insurance', 0);
INSERT INTO `gacl_axo` VALUES (53, 'resources', 'superbill', 10, 'Section - Superbill', 0);
INSERT INTO `gacl_axo` VALUES (54, 'resources', 'event', 10, 'Section - Event', 0);
INSERT INTO `gacl_axo` VALUES (55, 'resources', 'occurence', 10, 'Section - Occurence', 0);
INSERT INTO `gacl_axo` VALUES (56, 'resources', 'building', 10, 'Building', 0);
INSERT INTO `gacl_axo` VALUES (57, 'resources', 'room', 10, 'room', 0);

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
INSERT INTO `gacl_axo_groups_map` VALUES (27, 11);
INSERT INTO `gacl_axo_groups_map` VALUES (28, 11);

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

INSERT INTO `gacl_axo_sections_seq` VALUES (20);

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

INSERT INTO `gacl_axo_seq` VALUES (57);

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
INSERT INTO `gacl_groups_axo_map` VALUES (11, 47);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 48);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 49);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 50);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 51);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 52);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 53);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 54);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 55);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 56);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 57);

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

INSERT INTO `insurance_program` VALUES (1114, 2, 1113, 'Professional Care');
INSERT INTO `insurance_program` VALUES (2050, 1, 2049, 'Health America');

-- --------------------------------------------------------

-- 
-- Table structure for table `insured_relationship`
-- 

DROP TABLE IF EXISTS `insured_relationship`;
CREATE TABLE `insured_relationship` (
  `insured_relationship_id` int(11) NOT NULL default '0',
  `insurance_program_id` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  `subscriber_id` int(11) NOT NULL default '0',
  `subscriber_to_patient_relationship` int(11) NOT NULL default '0',
  `copay` float(11,2) NOT NULL default '0.00',
  `assigning` int(11) NOT NULL default '0',
  `group_name` varchar(100) NOT NULL default '',
  `group_number` varchar(100) NOT NULL default '',
  `default_provider` int(11) NOT NULL default '0',
  `program_order` int(11) NOT NULL default '0',
  PRIMARY KEY  (`insured_relationship_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `insured_relationship`
-- 

INSERT INTO `insured_relationship` VALUES (1708, 1114, 1707, 8039, 2, 25.00, 0, '123', '111-1232323', 0, 1);
INSERT INTO `insured_relationship` VALUES (2048, 1114, 1110, 0, 0, 35.00, 0, '1234', '1234', 0, 0);
INSERT INTO `insured_relationship` VALUES (2051, 2050, 1110, 0, 0, 35.00, 0, '345545', '2345534', 0, 0);
INSERT INTO `insured_relationship` VALUES (8045, 2050, 1707, 1707, 1, 13.00, 0, 'ABC', '111-AB4567', 0, 2);
INSERT INTO `insured_relationship` VALUES (8049, 1114, 1707, 1707, 1, 45.00, 0, '456', 'blah blah', 0, 3);

-- --------------------------------------------------------

-- 
-- Table structure for table `menu`
-- 

DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `menu_id` int(11) NOT NULL auto_increment,
  `site_section` varchar(50) NOT NULL default 'default',
  `parent` int(11) NOT NULL default '0',
  `dynamic_key` varchar(50) NOT NULL default '',
  `section` enum('children','more','dynamic') NOT NULL default 'children',
  `display_order` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `action` varchar(255) NOT NULL default '',
  `prefix` varchar(100) NOT NULL default 'main',
  PRIMARY KEY  (`menu_id`)
) TYPE=MyISAM AUTO_INCREMENT=88 ;

-- 
-- Dumping data for table `menu`
-- 

INSERT INTO `menu` VALUES (1, '', 1, '', 'children', 0, '', '', 'main');
INSERT INTO `menu` VALUES (2, 'default', 39, '', 'children', 100, 'Logout', 'Access/logout', 'main');
INSERT INTO `menu` VALUES (3, 'default', 39, '', 'children', 10, 'Preferences', 'Preferences/list', 'main');
INSERT INTO `menu` VALUES (4, 'admin', 1, '', 'children', 800, 'Reports', '', '');
INSERT INTO `menu` VALUES (5, 'admin', 1, '', 'children', 100, 'Entities', '', '');
INSERT INTO `menu` VALUES (7, 'admin', 5, '', 'children', 10, 'Add New Schedule', 'Location/edit_schedule', 'main');
INSERT INTO `menu` VALUES (8, 'default', 5, '', 'children', 20, 'Add New Practice', 'Location/edit_practive', 'main');
INSERT INTO `menu` VALUES (9, 'admin', 5, '', 'children', 30, 'Add New Building', 'Location/edit_building', 'main');
INSERT INTO `menu` VALUES (10, 'admin', 5, '', 'children', 40, 'Add New Room', 'Location/edit_room', 'main');
INSERT INTO `menu` VALUES (82, 'admin', 26, '', 'children', 10, 'List Forms', 'Form/list', 'main');
INSERT INTO `menu` VALUES (12, 'default', 65, '', 'children', 10, 'Day', 'Calendar/day', 'main');
INSERT INTO `menu` VALUES (13, 'default', 65, '', 'children', 50, 'Week Brief', 'Calendar/week', 'main');
INSERT INTO `menu` VALUES (14, 'default', 65, '', 'children', 20, 'Week', 'Calendar/week_grid', 'main');
INSERT INTO `menu` VALUES (15, 'default', 65, '', 'children', 30, 'Month', 'Calendar/month', 'main');
INSERT INTO `menu` VALUES (16, 'default', 65, '', 'children', 40, 'Day Brief', 'Calendar/day_brief', 'main');
INSERT INTO `menu` VALUES (17, 'default', 65, '', 'children', 60, 'Search', 'Calendar/search', 'main');
INSERT INTO `menu` VALUES (18, 'admin', 45, '', 'children', 10, 'List Fee Schedules', 'FeeSchedule/default', 'main');
INSERT INTO `menu` VALUES (19, 'admin', 45, '', 'children', 20, 'Add Fee Schedule', 'FeeSchedule/edit', 'main');
INSERT INTO `menu` VALUES (20, 'admin', 4, '', 'children', 10, 'Add Report', 'Report/edit', 'main');
INSERT INTO `menu` VALUES (21, 'admin', 81, '', 'children', 10, 'List Users', 'User/list', 'main');
INSERT INTO `menu` VALUES (22, 'admin', 81, '', 'children', 20, 'Add User', 'User/edit', 'main');
INSERT INTO `menu` VALUES (80, 'admin', 1, '', 'children', 200, 'Calendar', '', '');
INSERT INTO `menu` VALUES (24, 'admin', 81, '', 'children', 30, 'List Enumerations', 'Enumeration/list', 'main');
INSERT INTO `menu` VALUES (25, 'admin', 81, '', 'children', 40, 'Add Enumeration', 'Enumeration/edit', 'main');
INSERT INTO `menu` VALUES (26, 'admin', 1, '', 'children', 750, 'Forms', '', '');
INSERT INTO `menu` VALUES (27, 'admin', 26, '', 'children', 20, 'Add Form', 'Form/edit', 'main');
INSERT INTO `menu` VALUES (28, 'admin', 26, '', 'children', 30, 'View Form Data', 'Form/view', 'main');
INSERT INTO `menu` VALUES (29, 'patient', 68, '', 'children', 10, 'Forms', 'Form/fillout', 'main');
INSERT INTO `menu` VALUES (30, 'patient', 1, '', 'children', 100, 'Patients', '', '');
INSERT INTO `menu` VALUES (31, 'patient', 30, '', 'children', 20, 'Add Patient', 'Patient/edit', 'main');
INSERT INTO `menu` VALUES (32, 'admin', 5, '', 'children', 160, 'List Insurance Companies', 'Insurance/list', 'main');
INSERT INTO `menu` VALUES (33, 'admin', 5, '', 'children', 170, 'Add Insurance Company', 'Insurance/edit', 'main');
INSERT INTO `menu` VALUES (36, 'admin', 81, '', 'children', 50, 'Document Categories', 'DocumentCategory/list', 'main');
INSERT INTO `menu` VALUES (37, 'patient', 68, '', 'children', 20, 'Documents', 'Document/list', 'main');
INSERT INTO `menu` VALUES (38, 'admin', 45, '', 'children', 30, 'Edit Superbill', 'Superbill/list', 'main');
INSERT INTO `menu` VALUES (39, 'default', 1, '', 'children', 300, 'My Account', '', 'main');
INSERT INTO `menu` VALUES (81, 'admin', 1, '', 'children', 700, 'System', '', '');
INSERT INTO `menu` VALUES (73, 'billing', 62, '', 'children', 30, 'Search', 'Claim/search', 'freeb2');
INSERT INTO `menu` VALUES (42, 'billing', 1, '', 'children', 300, 'Reports', 'Billing/reports', 'main');
INSERT INTO `menu` VALUES (43, 'default', 1, '', 'children', 200, 'Reports', '', '');
INSERT INTO `menu` VALUES (44, 'patient', 1, '', 'children', 300, 'Reports', 'Patient/reports', 'main');
INSERT INTO `menu` VALUES (45, 'admin', 1, '', 'children', 300, 'Billing', '', 'main');
INSERT INTO `menu` VALUES (46, 'patient', 1, '', 'children', 400, 'My Account', '', 'main');
INSERT INTO `menu` VALUES (47, 'patient', 46, '', 'children', 100, 'Logout', 'Access/logout', 'main');
INSERT INTO `menu` VALUES (48, 'patient', 46, '', 'children', 10, 'Preferences', 'Preferences/list', 'main');
INSERT INTO `menu` VALUES (49, 'billing', 1, '', 'children', 500, 'My Account', '', 'main');
INSERT INTO `menu` VALUES (57, 'billing', 49, '', 'children', 100, 'Logout', 'Access/logout', 'main');
INSERT INTO `menu` VALUES (58, 'billing', 49, '', 'children', 10, 'Preferences', 'Preferences/list', 'main');
INSERT INTO `menu` VALUES (59, 'admin', 1, '', 'children', 900, 'My Account', '', 'main');
INSERT INTO `menu` VALUES (60, 'admin', 59, '', 'children', 100, 'Logout', 'Access/logout', 'main');
INSERT INTO `menu` VALUES (61, 'admin', 59, '', 'children', 10, 'Preferences', 'Preferences/list', 'main');
INSERT INTO `menu` VALUES (62, 'billing', 1, '', 'children', 100, 'Claims', '', 'freeb2');
INSERT INTO `menu` VALUES (63, 'billing', 62, '', 'children', 10, 'List Claims', 'Claim/list', 'freeb2');
INSERT INTO `menu` VALUES (64, 'billing', 62, '', 'children', 20, 'Add Claim', 'Claim/list', 'freeb2');
INSERT INTO `menu` VALUES (65, 'default', 1, '', 'children', 100, 'View', '', '');
INSERT INTO `menu` VALUES (66, 'default', 1, '', 'children', 400, 'Help', '', '');
INSERT INTO `menu` VALUES (67, 'patient', 30, '', 'children', 10, 'List Patients', 'Patient/list', 'main');
INSERT INTO `menu` VALUES (68, 'patient', 1, '', 'children', 200, 'Actions', '', '');
INSERT INTO `menu` VALUES (69, 'patient', 30, '', 'children', 30, 'Search', 'PatientFinder/find', 'main');
INSERT INTO `menu` VALUES (70, 'patient', 68, '', 'children', 30, 'Encounter', 'Patient/encounter', 'main');
INSERT INTO `menu` VALUES (71, 'default', 66, '', 'children', 10, 'API Docs', 'Docs/api', 'main');
INSERT INTO `menu` VALUES (72, 'patient', 68, '', 'children', 5, 'Dashboard', 'Patient/dashboard', 'main');
INSERT INTO `menu` VALUES (74, 'patient', 1, '', 'children', 500, 'Help', '', '');
INSERT INTO `menu` VALUES (75, 'patient', 74, '', 'children', 10, 'API Docs', 'Docs/api', 'main');
INSERT INTO `menu` VALUES (76, 'billing', 1, '', 'children', 500, 'Help', '', '');
INSERT INTO `menu` VALUES (77, 'billing', 76, '', 'children', 10, 'API Docs', 'Docs/api', 'main');
INSERT INTO `menu` VALUES (78, 'admin', 1, '', 'children', 1000, 'Help', '', '');
INSERT INTO `menu` VALUES (79, 'admin', 78, '', 'children', 10, 'API Docs', 'Docs/api', 'main');
INSERT INTO `menu` VALUES (83, 'admin', 5, '', 'children', 5, 'List Schedules/Facilities', 'Location/list', 'main');
INSERT INTO `menu` VALUES (84, 'admin', 5, '', 'children', 20, 'Add New Practice', 'Location/edit_practice', 'main');
INSERT INTO `menu` VALUES (85, 'admin', 4, '', 'children', 5, 'List Reports', 'Report/list', 'main');
INSERT INTO `menu` VALUES (86, 'admin', 1, '', 'children', 900, '', 'Admin/default', 'main');
INSERT INTO `menu` VALUES (87, 'admin', 4, '', 'children', 50, 'Connect Report', 'Report/connect', 'main');

-- --------------------------------------------------------

-- 
-- Table structure for table `menu_form`
-- 

DROP TABLE IF EXISTS `menu_form`;
CREATE TABLE `menu_form` (
  `menu_form_id` int(11) NOT NULL default '0',
  `menu_id` int(11) NOT NULL default '0',
  `form_id` int(11) NOT NULL default '0',
  `title` varchar(50) NOT NULL default '',
  `custom_action` varchar(255) default NULL,
  PRIMARY KEY  (`menu_form_id`),
  KEY `menu_id` (`menu_id`),
  KEY `form_id` (`form_id`)
) TYPE=InnoDB;

-- 
-- Dumping data for table `menu_form`
-- 

INSERT INTO `menu_form` VALUES (2064, 90, 800, 'Test Data', NULL);
INSERT INTO `menu_form` VALUES (2066, 91, 1710, 'Patient Vitals', NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `menu_report`
-- 

DROP TABLE IF EXISTS `menu_report`;
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

-- 
-- Dumping data for table `menu_report`
-- 

INSERT INTO `menu_report` VALUES (1714, 42, 792, 'Code Report', NULL);
INSERT INTO `menu_report` VALUES (1715, 4, 792, 'Code Report', NULL);
INSERT INTO `menu_report` VALUES (2054, 44, 792, 'Test', NULL);
INSERT INTO `menu_report` VALUES (2055, 89, 792, 'Selected Test', NULL);

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

INSERT INTO `name_history` VALUES (1712, 1711, 'nancy', 'jones', '', '2005-03-10');
INSERT INTO `name_history` VALUES (1713, 1711, 'nancy', 'jones3', '', '2005-03-10');
INSERT INTO `name_history` VALUES (8042, 8039, 'Random', 'Person2', '', '2005-03-17');
INSERT INTO `name_history` VALUES (8044, 8039, 'Random', 'Person3', '', '2005-03-17');

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
  `active` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`number_id`)
) TYPE=MyISAM COMMENT='A phone number';

-- 
-- Dumping data for table `number`
-- 

INSERT INTO `number` VALUES (1115, 1, '', '555-555-5555', 1);
INSERT INTO `number` VALUES (1709, 4, '', '555-555-5551', 1);
INSERT INTO `number` VALUES (2056, 1, '', '555-555-5555', 1);
INSERT INTO `number` VALUES (8017, 0, '', '123-123-4567', 1);
INSERT INTO `number` VALUES (8021, 0, '', '123-123-4567', 1);
INSERT INTO `number` VALUES (8025, 0, '', '123-123-4567', 1);
INSERT INTO `number` VALUES (8029, 0, '', '123-123-4567', 1);
INSERT INTO `number` VALUES (8033, 0, '', '123-123-4567', 1);
INSERT INTO `number` VALUES (8037, 0, '', '123-123-4567', 1);
INSERT INTO `number` VALUES (8041, 0, '', '123-123-4567', 1);

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
  `reason_code` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `occurences`
-- 

INSERT INTO `occurences` VALUES (1129, 1128, '2005-01-03 08:00:00', '2005-01-03 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1130, 1128, '2005-01-03 12:00:00', '2005-01-03 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1131, 1128, '2005-01-04 08:00:00', '2005-01-04 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1132, 1128, '2005-01-04 12:00:00', '2005-01-04 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1133, 1128, '2005-01-05 08:00:00', '2005-01-05 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1134, 1128, '2005-01-05 12:00:00', '2005-01-05 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1135, 1128, '2005-01-06 12:00:00', '2005-01-06 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1136, 1128, '2005-01-07 08:00:00', '2005-01-07 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1137, 1128, '2005-01-07 12:00:00', '2005-01-07 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1138, 1128, '2005-01-10 08:00:00', '2005-01-10 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1139, 1128, '2005-01-10 12:00:00', '2005-01-10 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1140, 1128, '2005-01-11 08:00:00', '2005-01-11 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1141, 1128, '2005-01-11 12:00:00', '2005-01-11 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1142, 1128, '2005-01-12 08:00:00', '2005-01-12 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1143, 1128, '2005-01-12 12:00:00', '2005-01-12 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1144, 1128, '2005-01-13 12:00:00', '2005-01-13 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1145, 1128, '2005-01-14 08:00:00', '2005-01-14 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1146, 1128, '2005-01-14 12:00:00', '2005-01-14 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1147, 1128, '2005-01-17 08:00:00', '2005-01-17 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1148, 1128, '2005-01-17 12:00:00', '2005-01-17 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1149, 1128, '2005-01-18 08:00:00', '2005-01-18 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1150, 1128, '2005-01-18 12:00:00', '2005-01-18 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1151, 1128, '2005-01-19 08:00:00', '2005-01-19 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1152, 1128, '2005-01-19 12:00:00', '2005-01-19 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1153, 1128, '2005-01-20 12:00:00', '2005-01-20 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1154, 1128, '2005-01-21 08:00:00', '2005-01-21 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1155, 1128, '2005-01-21 12:00:00', '2005-01-21 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1156, 1128, '2005-01-24 08:00:00', '2005-01-24 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1157, 1128, '2005-01-24 12:00:00', '2005-01-24 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1158, 1128, '2005-01-25 08:00:00', '2005-01-25 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1159, 1128, '2005-01-25 12:00:00', '2005-01-25 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1160, 1128, '2005-01-26 08:00:00', '2005-01-26 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1161, 1128, '2005-01-26 12:00:00', '2005-01-26 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1162, 1128, '2005-01-27 12:00:00', '2005-01-27 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1163, 1128, '2005-01-28 08:00:00', '2005-01-28 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1164, 1128, '2005-01-28 12:00:00', '2005-01-28 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1165, 1128, '2005-01-31 08:00:00', '2005-01-31 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1166, 1128, '2005-01-31 12:00:00', '2005-01-31 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1167, 1128, '2005-02-01 08:00:00', '2005-02-01 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1168, 1128, '2005-02-01 12:00:00', '2005-02-01 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1169, 1128, '2005-02-02 08:00:00', '2005-02-02 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1170, 1128, '2005-02-02 12:00:00', '2005-02-02 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1171, 1128, '2005-02-03 12:00:00', '2005-02-03 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1172, 1128, '2005-02-04 08:00:00', '2005-02-04 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1173, 1128, '2005-02-04 12:00:00', '2005-02-04 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1174, 1128, '2005-02-07 08:00:00', '2005-02-07 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1175, 1128, '2005-02-07 12:00:00', '2005-02-07 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1176, 1128, '2005-02-08 08:00:00', '2005-02-08 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1177, 1128, '2005-02-08 12:00:00', '2005-02-08 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1178, 1128, '2005-02-09 08:00:00', '2005-02-09 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1179, 1128, '2005-02-09 12:00:00', '2005-02-09 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1180, 1128, '2005-02-10 12:00:00', '2005-02-10 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1181, 1128, '2005-02-11 08:00:00', '2005-02-11 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1182, 1128, '2005-02-11 12:00:00', '2005-02-11 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1183, 1128, '2005-02-14 08:00:00', '2005-02-14 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1184, 1128, '2005-02-14 12:00:00', '2005-02-14 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1185, 1128, '2005-02-15 08:00:00', '2005-02-15 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1186, 1128, '2005-02-15 12:00:00', '2005-02-15 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1187, 1128, '2005-02-16 08:00:00', '2005-02-16 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1188, 1128, '2005-02-16 12:00:00', '2005-02-16 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1189, 1128, '2005-02-17 12:00:00', '2005-02-17 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1190, 1128, '2005-02-18 08:00:00', '2005-02-18 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1191, 1128, '2005-02-18 12:00:00', '2005-02-18 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1192, 1128, '2005-02-21 08:00:00', '2005-02-21 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1193, 1128, '2005-02-21 12:00:00', '2005-02-21 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1194, 1128, '2005-02-22 08:00:00', '2005-02-22 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1195, 1128, '2005-02-22 12:00:00', '2005-02-22 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1196, 1128, '2005-02-23 08:00:00', '2005-02-23 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1197, 1128, '2005-02-23 12:00:00', '2005-02-23 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1198, 1128, '2005-02-24 12:00:00', '2005-02-24 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1199, 1128, '2005-02-25 08:00:00', '2005-02-25 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1200, 1128, '2005-02-25 12:00:00', '2005-02-25 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1201, 1128, '2005-02-28 08:00:00', '2005-02-28 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1202, 1128, '2005-02-28 12:00:00', '2005-02-28 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1203, 1128, '2005-03-01 08:00:00', '2005-03-01 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1204, 1128, '2005-03-01 12:00:00', '2005-03-01 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1205, 1128, '2005-03-02 08:00:00', '2005-03-02 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1206, 1128, '2005-03-02 12:00:00', '2005-03-02 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1207, 1128, '2005-03-03 12:00:00', '2005-03-03 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1208, 1128, '2005-03-04 08:00:00', '2005-03-04 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1209, 1128, '2005-03-04 12:00:00', '2005-03-04 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1210, 1128, '2005-03-07 08:00:00', '2005-03-07 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1211, 1128, '2005-03-07 12:00:00', '2005-03-07 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1212, 1128, '2005-03-08 08:00:00', '2005-03-08 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1213, 1128, '2005-03-08 12:00:00', '2005-03-08 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1214, 1128, '2005-03-09 08:00:00', '2005-03-09 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1215, 1128, '2005-03-09 12:00:00', '2005-03-09 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1216, 1128, '2005-03-10 12:00:00', '2005-03-10 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1217, 1128, '2005-03-11 08:00:00', '2005-03-11 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1218, 1128, '2005-03-11 12:00:00', '2005-03-11 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1219, 1128, '2005-03-14 08:00:00', '2005-03-14 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1220, 1128, '2005-03-14 12:00:00', '2005-03-14 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1221, 1128, '2005-03-15 08:00:00', '2005-03-15 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1222, 1128, '2005-03-15 12:00:00', '2005-03-15 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1223, 1128, '2005-03-16 08:00:00', '2005-03-16 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1224, 1128, '2005-03-16 12:00:00', '2005-03-16 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1225, 1128, '2005-03-17 12:00:00', '2005-03-17 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1226, 1128, '2005-03-18 08:00:00', '2005-03-18 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1227, 1128, '2005-03-18 12:00:00', '2005-03-18 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1228, 1128, '2005-03-21 08:00:00', '2005-03-21 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1229, 1128, '2005-03-21 12:00:00', '2005-03-21 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1230, 1128, '2005-03-22 08:00:00', '2005-03-22 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1231, 1128, '2005-03-22 12:00:00', '2005-03-22 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1232, 1128, '2005-03-23 08:00:00', '2005-03-23 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1233, 1128, '2005-03-23 12:00:00', '2005-03-23 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1234, 1128, '2005-03-24 12:00:00', '2005-03-24 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1235, 1128, '2005-03-25 08:00:00', '2005-03-25 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1236, 1128, '2005-03-25 12:00:00', '2005-03-25 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1237, 1128, '2005-03-28 08:00:00', '2005-03-28 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1238, 1128, '2005-03-28 12:00:00', '2005-03-28 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1239, 1128, '2005-03-29 08:00:00', '2005-03-29 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1240, 1128, '2005-03-29 12:00:00', '2005-03-29 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1241, 1128, '2005-03-30 08:00:00', '2005-03-30 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1242, 1128, '2005-03-30 12:00:00', '2005-03-30 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1243, 1128, '2005-03-31 12:00:00', '2005-03-31 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1244, 1128, '2005-04-01 08:00:00', '2005-04-01 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1245, 1128, '2005-04-01 12:00:00', '2005-04-01 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1246, 1128, '2005-04-04 08:00:00', '2005-04-04 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1247, 1128, '2005-04-04 12:00:00', '2005-04-04 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1248, 1128, '2005-04-05 08:00:00', '2005-04-05 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1249, 1128, '2005-04-05 12:00:00', '2005-04-05 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1250, 1128, '2005-04-06 08:00:00', '2005-04-06 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1251, 1128, '2005-04-06 12:00:00', '2005-04-06 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1252, 1128, '2005-04-07 12:00:00', '2005-04-07 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1253, 1128, '2005-04-08 08:00:00', '2005-04-08 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1254, 1128, '2005-04-08 12:00:00', '2005-04-08 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1255, 1128, '2005-04-11 08:00:00', '2005-04-11 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1256, 1128, '2005-04-11 12:00:00', '2005-04-11 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1257, 1128, '2005-04-12 08:00:00', '2005-04-12 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1258, 1128, '2005-04-12 12:00:00', '2005-04-12 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1259, 1128, '2005-04-13 08:00:00', '2005-04-13 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1260, 1128, '2005-04-13 12:00:00', '2005-04-13 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1261, 1128, '2005-04-14 12:00:00', '2005-04-14 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1262, 1128, '2005-04-15 08:00:00', '2005-04-15 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1263, 1128, '2005-04-15 12:00:00', '2005-04-15 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1264, 1128, '2005-04-18 08:00:00', '2005-04-18 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1265, 1128, '2005-04-18 12:00:00', '2005-04-18 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1266, 1128, '2005-04-19 08:00:00', '2005-04-19 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1267, 1128, '2005-04-19 12:00:00', '2005-04-19 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1268, 1128, '2005-04-20 08:00:00', '2005-04-20 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1269, 1128, '2005-04-20 12:00:00', '2005-04-20 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1270, 1128, '2005-04-21 12:00:00', '2005-04-21 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1271, 1128, '2005-04-22 08:00:00', '2005-04-22 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1272, 1128, '2005-04-22 12:00:00', '2005-04-22 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1273, 1128, '2005-04-25 08:00:00', '2005-04-25 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1274, 1128, '2005-04-25 12:00:00', '2005-04-25 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1275, 1128, '2005-04-26 08:00:00', '2005-04-26 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1276, 1128, '2005-04-26 12:00:00', '2005-04-26 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1277, 1128, '2005-04-27 08:00:00', '2005-04-27 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1278, 1128, '2005-04-27 12:00:00', '2005-04-27 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1279, 1128, '2005-04-28 12:00:00', '2005-04-28 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1280, 1128, '2005-04-29 08:00:00', '2005-04-29 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1281, 1128, '2005-04-29 12:00:00', '2005-04-29 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1282, 1128, '2005-05-02 08:00:00', '2005-05-02 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1283, 1128, '2005-05-02 12:00:00', '2005-05-02 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1284, 1128, '2005-05-03 08:00:00', '2005-05-03 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1285, 1128, '2005-05-03 12:00:00', '2005-05-03 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1286, 1128, '2005-05-04 08:00:00', '2005-05-04 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1287, 1128, '2005-05-04 12:00:00', '2005-05-04 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1288, 1128, '2005-05-05 12:00:00', '2005-05-05 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1289, 1128, '2005-05-06 08:00:00', '2005-05-06 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1290, 1128, '2005-05-06 12:00:00', '2005-05-06 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1291, 1128, '2005-05-09 08:00:00', '2005-05-09 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1292, 1128, '2005-05-09 12:00:00', '2005-05-09 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1293, 1128, '2005-05-10 08:00:00', '2005-05-10 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1294, 1128, '2005-05-10 12:00:00', '2005-05-10 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1295, 1128, '2005-05-11 08:00:00', '2005-05-11 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1296, 1128, '2005-05-11 12:00:00', '2005-05-11 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1297, 1128, '2005-05-12 12:00:00', '2005-05-12 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1298, 1128, '2005-05-13 08:00:00', '2005-05-13 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1299, 1128, '2005-05-13 12:00:00', '2005-05-13 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1300, 1128, '2005-05-16 08:00:00', '2005-05-16 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1301, 1128, '2005-05-16 12:00:00', '2005-05-16 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1302, 1128, '2005-05-17 08:00:00', '2005-05-17 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1303, 1128, '2005-05-17 12:00:00', '2005-05-17 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1304, 1128, '2005-05-18 08:00:00', '2005-05-18 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1305, 1128, '2005-05-18 12:00:00', '2005-05-18 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1306, 1128, '2005-05-19 12:00:00', '2005-05-19 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1307, 1128, '2005-05-20 08:00:00', '2005-05-20 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1308, 1128, '2005-05-20 12:00:00', '2005-05-20 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1309, 1128, '2005-05-23 08:00:00', '2005-05-23 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1310, 1128, '2005-05-23 12:00:00', '2005-05-23 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1311, 1128, '2005-05-24 08:00:00', '2005-05-24 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1312, 1128, '2005-05-24 12:00:00', '2005-05-24 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1313, 1128, '2005-05-25 08:00:00', '2005-05-25 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1314, 1128, '2005-05-25 12:00:00', '2005-05-25 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1315, 1128, '2005-05-26 12:00:00', '2005-05-26 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1316, 1128, '2005-05-27 08:00:00', '2005-05-27 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1317, 1128, '2005-05-27 12:00:00', '2005-05-27 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1318, 1128, '2005-05-30 08:00:00', '2005-05-30 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1319, 1128, '2005-05-30 12:00:00', '2005-05-30 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1320, 1128, '2005-05-31 08:00:00', '2005-05-31 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1321, 1128, '2005-05-31 12:00:00', '2005-05-31 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1322, 1128, '2005-06-01 08:00:00', '2005-06-01 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1323, 1128, '2005-06-01 12:00:00', '2005-06-01 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1324, 1128, '2005-06-02 12:00:00', '2005-06-02 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1325, 1128, '2005-06-03 08:00:00', '2005-06-03 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1326, 1128, '2005-06-03 12:00:00', '2005-06-03 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1327, 1128, '2005-06-06 08:00:00', '2005-06-06 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1328, 1128, '2005-06-06 12:00:00', '2005-06-06 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1329, 1128, '2005-06-07 08:00:00', '2005-06-07 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1330, 1128, '2005-06-07 12:00:00', '2005-06-07 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1331, 1128, '2005-06-08 08:00:00', '2005-06-08 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1332, 1128, '2005-06-08 12:00:00', '2005-06-08 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1333, 1128, '2005-06-09 12:00:00', '2005-06-09 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1334, 1128, '2005-06-10 08:00:00', '2005-06-10 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1335, 1128, '2005-06-10 12:00:00', '2005-06-10 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1336, 1128, '2005-06-13 08:00:00', '2005-06-13 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1337, 1128, '2005-06-13 12:00:00', '2005-06-13 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1338, 1128, '2005-06-14 08:00:00', '2005-06-14 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1339, 1128, '2005-06-14 12:00:00', '2005-06-14 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1340, 1128, '2005-06-15 08:00:00', '2005-06-15 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1341, 1128, '2005-06-15 12:00:00', '2005-06-15 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1342, 1128, '2005-06-16 12:00:00', '2005-06-16 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1343, 1128, '2005-06-17 08:00:00', '2005-06-17 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1344, 1128, '2005-06-17 12:00:00', '2005-06-17 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1345, 1128, '2005-06-20 08:00:00', '2005-06-20 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1346, 1128, '2005-06-20 12:00:00', '2005-06-20 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1347, 1128, '2005-06-21 08:00:00', '2005-06-21 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1348, 1128, '2005-06-21 12:00:00', '2005-06-21 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1349, 1128, '2005-06-22 08:00:00', '2005-06-22 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1350, 1128, '2005-06-22 12:00:00', '2005-06-22 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1351, 1128, '2005-06-23 12:00:00', '2005-06-23 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1352, 1128, '2005-06-24 08:00:00', '2005-06-24 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1353, 1128, '2005-06-24 12:00:00', '2005-06-24 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1354, 1128, '2005-06-27 08:00:00', '2005-06-27 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1355, 1128, '2005-06-27 12:00:00', '2005-06-27 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1356, 1128, '2005-06-28 08:00:00', '2005-06-28 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1357, 1128, '2005-06-28 12:00:00', '2005-06-28 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1358, 1128, '2005-06-29 08:00:00', '2005-06-29 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1359, 1128, '2005-06-29 12:00:00', '2005-06-29 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1360, 1128, '2005-06-30 12:00:00', '2005-06-30 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1361, 1128, '2005-07-01 08:00:00', '2005-07-01 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1362, 1128, '2005-07-01 12:00:00', '2005-07-01 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1363, 1128, '2005-07-04 08:00:00', '2005-07-04 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1364, 1128, '2005-07-04 12:00:00', '2005-07-04 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1365, 1128, '2005-07-05 08:00:00', '2005-07-05 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1366, 1128, '2005-07-05 12:00:00', '2005-07-05 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1367, 1128, '2005-07-06 08:00:00', '2005-07-06 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1368, 1128, '2005-07-06 12:00:00', '2005-07-06 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1369, 1128, '2005-07-07 12:00:00', '2005-07-07 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1370, 1128, '2005-07-08 08:00:00', '2005-07-08 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1371, 1128, '2005-07-08 12:00:00', '2005-07-08 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1372, 1128, '2005-07-11 08:00:00', '2005-07-11 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1373, 1128, '2005-07-11 12:00:00', '2005-07-11 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1374, 1128, '2005-07-12 08:00:00', '2005-07-12 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1375, 1128, '2005-07-12 12:00:00', '2005-07-12 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1376, 1128, '2005-07-13 08:00:00', '2005-07-13 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1377, 1128, '2005-07-13 12:00:00', '2005-07-13 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1378, 1128, '2005-07-14 12:00:00', '2005-07-14 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1379, 1128, '2005-07-15 08:00:00', '2005-07-15 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1380, 1128, '2005-07-15 12:00:00', '2005-07-15 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1381, 1128, '2005-07-18 08:00:00', '2005-07-18 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1382, 1128, '2005-07-18 12:00:00', '2005-07-18 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1383, 1128, '2005-07-19 08:00:00', '2005-07-19 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1384, 1128, '2005-07-19 12:00:00', '2005-07-19 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1385, 1128, '2005-07-20 08:00:00', '2005-07-20 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1386, 1128, '2005-07-20 12:00:00', '2005-07-20 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1387, 1128, '2005-07-21 12:00:00', '2005-07-21 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1388, 1128, '2005-07-22 08:00:00', '2005-07-22 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1389, 1128, '2005-07-22 12:00:00', '2005-07-22 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1390, 1128, '2005-07-25 08:00:00', '2005-07-25 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1391, 1128, '2005-07-25 12:00:00', '2005-07-25 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1392, 1128, '2005-07-26 08:00:00', '2005-07-26 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1393, 1128, '2005-07-26 12:00:00', '2005-07-26 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1394, 1128, '2005-07-27 08:00:00', '2005-07-27 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1395, 1128, '2005-07-27 12:00:00', '2005-07-27 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1396, 1128, '2005-07-28 12:00:00', '2005-07-28 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1397, 1128, '2005-07-29 08:00:00', '2005-07-29 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1398, 1128, '2005-07-29 12:00:00', '2005-07-29 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1399, 1128, '2005-08-01 08:00:00', '2005-08-01 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1400, 1128, '2005-08-01 12:00:00', '2005-08-01 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1401, 1128, '2005-08-02 08:00:00', '2005-08-02 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1402, 1128, '2005-08-02 12:00:00', '2005-08-02 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1403, 1128, '2005-08-03 08:00:00', '2005-08-03 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1404, 1128, '2005-08-03 12:00:00', '2005-08-03 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1405, 1128, '2005-08-04 12:00:00', '2005-08-04 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1406, 1128, '2005-08-05 08:00:00', '2005-08-05 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1407, 1128, '2005-08-05 12:00:00', '2005-08-05 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1408, 1128, '2005-08-08 08:00:00', '2005-08-08 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1409, 1128, '2005-08-08 12:00:00', '2005-08-08 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1410, 1128, '2005-08-09 08:00:00', '2005-08-09 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1411, 1128, '2005-08-09 12:00:00', '2005-08-09 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1412, 1128, '2005-08-10 08:00:00', '2005-08-10 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1413, 1128, '2005-08-10 12:00:00', '2005-08-10 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1414, 1128, '2005-08-11 12:00:00', '2005-08-11 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1415, 1128, '2005-08-12 08:00:00', '2005-08-12 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1416, 1128, '2005-08-12 12:00:00', '2005-08-12 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1417, 1128, '2005-08-15 08:00:00', '2005-08-15 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1418, 1128, '2005-08-15 12:00:00', '2005-08-15 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1419, 1128, '2005-08-16 08:00:00', '2005-08-16 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1420, 1128, '2005-08-16 12:00:00', '2005-08-16 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1421, 1128, '2005-08-17 08:00:00', '2005-08-17 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1422, 1128, '2005-08-17 12:00:00', '2005-08-17 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1423, 1128, '2005-08-18 12:00:00', '2005-08-18 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1424, 1128, '2005-08-19 08:00:00', '2005-08-19 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1425, 1128, '2005-08-19 12:00:00', '2005-08-19 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1426, 1128, '2005-08-22 08:00:00', '2005-08-22 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1427, 1128, '2005-08-22 12:00:00', '2005-08-22 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1428, 1128, '2005-08-23 08:00:00', '2005-08-23 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1429, 1128, '2005-08-23 12:00:00', '2005-08-23 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1430, 1128, '2005-08-24 08:00:00', '2005-08-24 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1431, 1128, '2005-08-24 12:00:00', '2005-08-24 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1432, 1128, '2005-08-25 12:00:00', '2005-08-25 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1433, 1128, '2005-08-26 08:00:00', '2005-08-26 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1434, 1128, '2005-08-26 12:00:00', '2005-08-26 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1435, 1128, '2005-08-29 08:00:00', '2005-08-29 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1436, 1128, '2005-08-29 12:00:00', '2005-08-29 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1437, 1128, '2005-08-30 08:00:00', '2005-08-30 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1438, 1128, '2005-08-30 12:00:00', '2005-08-30 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1439, 1128, '2005-08-31 08:00:00', '2005-08-31 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1440, 1128, '2005-08-31 12:00:00', '2005-08-31 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1441, 1128, '2005-09-01 12:00:00', '2005-09-01 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1442, 1128, '2005-09-02 08:00:00', '2005-09-02 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1443, 1128, '2005-09-02 12:00:00', '2005-09-02 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1444, 1128, '2005-09-05 08:00:00', '2005-09-05 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1445, 1128, '2005-09-05 12:00:00', '2005-09-05 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1446, 1128, '2005-09-06 08:00:00', '2005-09-06 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1447, 1128, '2005-09-06 12:00:00', '2005-09-06 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1448, 1128, '2005-09-07 08:00:00', '2005-09-07 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1449, 1128, '2005-09-07 12:00:00', '2005-09-07 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1450, 1128, '2005-09-08 12:00:00', '2005-09-08 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1451, 1128, '2005-09-09 08:00:00', '2005-09-09 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1452, 1128, '2005-09-09 12:00:00', '2005-09-09 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1453, 1128, '2005-09-12 08:00:00', '2005-09-12 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1454, 1128, '2005-09-12 12:00:00', '2005-09-12 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1455, 1128, '2005-09-13 08:00:00', '2005-09-13 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1456, 1128, '2005-09-13 12:00:00', '2005-09-13 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1457, 1128, '2005-09-14 08:00:00', '2005-09-14 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1458, 1128, '2005-09-14 12:00:00', '2005-09-14 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1459, 1128, '2005-09-15 12:00:00', '2005-09-15 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1460, 1128, '2005-09-16 08:00:00', '2005-09-16 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1461, 1128, '2005-09-16 12:00:00', '2005-09-16 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1462, 1128, '2005-09-19 08:00:00', '2005-09-19 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1463, 1128, '2005-09-19 12:00:00', '2005-09-19 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1464, 1128, '2005-09-20 08:00:00', '2005-09-20 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1465, 1128, '2005-09-20 12:00:00', '2005-09-20 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1466, 1128, '2005-09-21 08:00:00', '2005-09-21 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1467, 1128, '2005-09-21 12:00:00', '2005-09-21 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1468, 1128, '2005-09-22 12:00:00', '2005-09-22 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1469, 1128, '2005-09-23 08:00:00', '2005-09-23 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1470, 1128, '2005-09-23 12:00:00', '2005-09-23 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1471, 1128, '2005-09-26 08:00:00', '2005-09-26 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1472, 1128, '2005-09-26 12:00:00', '2005-09-26 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1473, 1128, '2005-09-27 08:00:00', '2005-09-27 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1474, 1128, '2005-09-27 12:00:00', '2005-09-27 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1475, 1128, '2005-09-28 08:00:00', '2005-09-28 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1476, 1128, '2005-09-28 12:00:00', '2005-09-28 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1477, 1128, '2005-09-29 12:00:00', '2005-09-29 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1478, 1128, '2005-09-30 08:00:00', '2005-09-30 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1479, 1128, '2005-09-30 12:00:00', '2005-09-30 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1480, 1128, '2005-10-03 08:00:00', '2005-10-03 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1481, 1128, '2005-10-03 12:00:00', '2005-10-03 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1482, 1128, '2005-10-04 08:00:00', '2005-10-04 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1483, 1128, '2005-10-04 12:00:00', '2005-10-04 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1484, 1128, '2005-10-05 08:00:00', '2005-10-05 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1485, 1128, '2005-10-05 12:00:00', '2005-10-05 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1486, 1128, '2005-10-06 12:00:00', '2005-10-06 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1487, 1128, '2005-10-07 08:00:00', '2005-10-07 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1488, 1128, '2005-10-07 12:00:00', '2005-10-07 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1489, 1128, '2005-10-10 08:00:00', '2005-10-10 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1490, 1128, '2005-10-10 12:00:00', '2005-10-10 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1491, 1128, '2005-10-11 08:00:00', '2005-10-11 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1492, 1128, '2005-10-11 12:00:00', '2005-10-11 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1493, 1128, '2005-10-12 08:00:00', '2005-10-12 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1494, 1128, '2005-10-12 12:00:00', '2005-10-12 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1495, 1128, '2005-10-13 12:00:00', '2005-10-13 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1496, 1128, '2005-10-14 08:00:00', '2005-10-14 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1497, 1128, '2005-10-14 12:00:00', '2005-10-14 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1498, 1128, '2005-10-17 08:00:00', '2005-10-17 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1499, 1128, '2005-10-17 12:00:00', '2005-10-17 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1500, 1128, '2005-10-18 08:00:00', '2005-10-18 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1501, 1128, '2005-10-18 12:00:00', '2005-10-18 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1502, 1128, '2005-10-19 08:00:00', '2005-10-19 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1503, 1128, '2005-10-19 12:00:00', '2005-10-19 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1504, 1128, '2005-10-20 12:00:00', '2005-10-20 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1505, 1128, '2005-10-21 08:00:00', '2005-10-21 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1506, 1128, '2005-10-21 12:00:00', '2005-10-21 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1507, 1128, '2005-10-24 08:00:00', '2005-10-24 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1508, 1128, '2005-10-24 12:00:00', '2005-10-24 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1509, 1128, '2005-10-25 08:00:00', '2005-10-25 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1510, 1128, '2005-10-25 12:00:00', '2005-10-25 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1511, 1128, '2005-10-26 08:00:00', '2005-10-26 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1512, 1128, '2005-10-26 12:00:00', '2005-10-26 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1513, 1128, '2005-10-27 12:00:00', '2005-10-27 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1514, 1128, '2005-10-28 08:00:00', '2005-10-28 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1515, 1128, '2005-10-28 12:00:00', '2005-10-28 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1516, 1128, '2005-10-31 08:00:00', '2005-10-31 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1517, 1128, '2005-10-31 12:00:00', '2005-10-31 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1518, 1128, '2005-11-01 08:00:00', '2005-11-01 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1519, 1128, '2005-11-01 12:00:00', '2005-11-01 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1520, 1128, '2005-11-02 08:00:00', '2005-11-02 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1521, 1128, '2005-11-02 12:00:00', '2005-11-02 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1522, 1128, '2005-11-03 12:00:00', '2005-11-03 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1523, 1128, '2005-11-04 08:00:00', '2005-11-04 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1524, 1128, '2005-11-04 12:00:00', '2005-11-04 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1525, 1128, '2005-11-07 08:00:00', '2005-11-07 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1526, 1128, '2005-11-07 12:00:00', '2005-11-07 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1527, 1128, '2005-11-08 08:00:00', '2005-11-08 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1528, 1128, '2005-11-08 12:00:00', '2005-11-08 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1529, 1128, '2005-11-09 08:00:00', '2005-11-09 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1530, 1128, '2005-11-09 12:00:00', '2005-11-09 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1531, 1128, '2005-11-10 12:00:00', '2005-11-10 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1532, 1128, '2005-11-11 08:00:00', '2005-11-11 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1533, 1128, '2005-11-11 12:00:00', '2005-11-11 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1534, 1128, '2005-11-14 08:00:00', '2005-11-14 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1535, 1128, '2005-11-14 12:00:00', '2005-11-14 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1536, 1128, '2005-11-15 08:00:00', '2005-11-15 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1537, 1128, '2005-11-15 12:00:00', '2005-11-15 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1538, 1128, '2005-11-16 08:00:00', '2005-11-16 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1539, 1128, '2005-11-16 12:00:00', '2005-11-16 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1540, 1128, '2005-11-17 12:00:00', '2005-11-17 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1541, 1128, '2005-11-18 08:00:00', '2005-11-18 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1542, 1128, '2005-11-18 12:00:00', '2005-11-18 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1543, 1128, '2005-11-21 08:00:00', '2005-11-21 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1544, 1128, '2005-11-21 12:00:00', '2005-11-21 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1545, 1128, '2005-11-22 08:00:00', '2005-11-22 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1546, 1128, '2005-11-22 12:00:00', '2005-11-22 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1547, 1128, '2005-11-23 08:00:00', '2005-11-23 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1548, 1128, '2005-11-23 12:00:00', '2005-11-23 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1549, 1128, '2005-11-24 12:00:00', '2005-11-24 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1550, 1128, '2005-11-25 08:00:00', '2005-11-25 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1551, 1128, '2005-11-25 12:00:00', '2005-11-25 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1552, 1128, '2005-11-28 08:00:00', '2005-11-28 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1553, 1128, '2005-11-28 12:00:00', '2005-11-28 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1554, 1128, '2005-11-29 08:00:00', '2005-11-29 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1555, 1128, '2005-11-29 12:00:00', '2005-11-29 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1556, 1128, '2005-11-30 08:00:00', '2005-11-30 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1557, 1128, '2005-11-30 12:00:00', '2005-11-30 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1558, 1128, '2005-12-01 12:00:00', '2005-12-01 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1559, 1128, '2005-12-02 08:00:00', '2005-12-02 11:00:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1560, 1128, '2005-12-02 12:00:00', '2005-12-02 16:15:00', '', 1125, 1121, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1563, 1562, '2005-01-06 09:00:00', '2005-01-06 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1564, 1562, '2005-01-07 10:00:00', '2005-01-07 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1565, 1562, '2005-01-07 15:00:00', '2005-01-07 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1566, 1562, '2005-01-13 09:00:00', '2005-01-13 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1567, 1562, '2005-01-14 10:00:00', '2005-01-14 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1568, 1562, '2005-01-14 15:00:00', '2005-01-14 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1569, 1562, '2005-01-20 09:00:00', '2005-01-20 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1570, 1562, '2005-01-21 10:00:00', '2005-01-21 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1571, 1562, '2005-01-21 15:00:00', '2005-01-21 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1572, 1562, '2005-01-27 09:00:00', '2005-01-27 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1573, 1562, '2005-01-28 10:00:00', '2005-01-28 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1574, 1562, '2005-01-28 15:00:00', '2005-01-28 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1575, 1562, '2005-02-03 09:00:00', '2005-02-03 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1576, 1562, '2005-02-04 10:00:00', '2005-02-04 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1577, 1562, '2005-02-04 15:00:00', '2005-02-04 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1578, 1562, '2005-02-10 09:00:00', '2005-02-10 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1579, 1562, '2005-02-11 10:00:00', '2005-02-11 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1580, 1562, '2005-02-11 15:00:00', '2005-02-11 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1581, 1562, '2005-02-17 09:00:00', '2005-02-17 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1582, 1562, '2005-02-18 10:00:00', '2005-02-18 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1583, 1562, '2005-02-18 15:00:00', '2005-02-18 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1584, 1562, '2005-02-24 09:00:00', '2005-02-24 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1585, 1562, '2005-02-25 10:00:00', '2005-02-25 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1586, 1562, '2005-02-25 15:00:00', '2005-02-25 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1587, 1562, '2005-03-03 09:00:00', '2005-03-03 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1588, 1562, '2005-03-04 10:00:00', '2005-03-04 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1589, 1562, '2005-03-04 15:00:00', '2005-03-04 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1590, 1562, '2005-03-10 09:00:00', '2005-03-10 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1591, 1562, '2005-03-11 10:00:00', '2005-03-11 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1592, 1562, '2005-03-11 15:00:00', '2005-03-11 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1593, 1562, '2005-03-17 09:00:00', '2005-03-17 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1594, 1562, '2005-03-18 10:00:00', '2005-03-18 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1595, 1562, '2005-03-18 15:00:00', '2005-03-18 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1596, 1562, '2005-03-24 09:00:00', '2005-03-24 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1597, 1562, '2005-03-25 10:00:00', '2005-03-25 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1598, 1562, '2005-03-25 15:00:00', '2005-03-25 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1599, 1562, '2005-03-31 09:00:00', '2005-03-31 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1600, 1562, '2005-04-01 10:00:00', '2005-04-01 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1601, 1562, '2005-04-01 15:00:00', '2005-04-01 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1602, 1562, '2005-04-07 09:00:00', '2005-04-07 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1603, 1562, '2005-04-08 10:00:00', '2005-04-08 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1604, 1562, '2005-04-08 15:00:00', '2005-04-08 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1605, 1562, '2005-04-14 09:00:00', '2005-04-14 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1606, 1562, '2005-04-15 10:00:00', '2005-04-15 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1607, 1562, '2005-04-15 15:00:00', '2005-04-15 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1608, 1562, '2005-04-21 09:00:00', '2005-04-21 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1609, 1562, '2005-04-22 10:00:00', '2005-04-22 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1610, 1562, '2005-04-22 15:00:00', '2005-04-22 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1611, 1562, '2005-04-28 09:00:00', '2005-04-28 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1612, 1562, '2005-04-29 10:00:00', '2005-04-29 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1613, 1562, '2005-04-29 15:00:00', '2005-04-29 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1614, 1562, '2005-05-05 09:00:00', '2005-05-05 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1615, 1562, '2005-05-06 10:00:00', '2005-05-06 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1616, 1562, '2005-05-06 15:00:00', '2005-05-06 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1617, 1562, '2005-05-12 09:00:00', '2005-05-12 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1618, 1562, '2005-05-13 10:00:00', '2005-05-13 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1619, 1562, '2005-05-13 15:00:00', '2005-05-13 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1620, 1562, '2005-05-19 09:00:00', '2005-05-19 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1621, 1562, '2005-05-20 10:00:00', '2005-05-20 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1622, 1562, '2005-05-20 15:00:00', '2005-05-20 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1623, 1562, '2005-05-26 09:00:00', '2005-05-26 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1624, 1562, '2005-05-27 10:00:00', '2005-05-27 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1625, 1562, '2005-05-27 15:00:00', '2005-05-27 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1626, 1562, '2005-06-02 09:00:00', '2005-06-02 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1627, 1562, '2005-06-03 10:00:00', '2005-06-03 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1628, 1562, '2005-06-03 15:00:00', '2005-06-03 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1629, 1562, '2005-06-09 09:00:00', '2005-06-09 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1630, 1562, '2005-06-10 10:00:00', '2005-06-10 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1631, 1562, '2005-06-10 15:00:00', '2005-06-10 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1632, 1562, '2005-06-16 09:00:00', '2005-06-16 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1633, 1562, '2005-06-17 10:00:00', '2005-06-17 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1634, 1562, '2005-06-17 15:00:00', '2005-06-17 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1635, 1562, '2005-06-23 09:00:00', '2005-06-23 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1636, 1562, '2005-06-24 10:00:00', '2005-06-24 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1637, 1562, '2005-06-24 15:00:00', '2005-06-24 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1638, 1562, '2005-06-30 09:00:00', '2005-06-30 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1639, 1562, '2005-07-01 10:00:00', '2005-07-01 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1640, 1562, '2005-07-01 15:00:00', '2005-07-01 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1641, 1562, '2005-07-07 09:00:00', '2005-07-07 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1642, 1562, '2005-07-08 10:00:00', '2005-07-08 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1643, 1562, '2005-07-08 15:00:00', '2005-07-08 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1644, 1562, '2005-07-14 09:00:00', '2005-07-14 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1645, 1562, '2005-07-15 10:00:00', '2005-07-15 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1646, 1562, '2005-07-15 15:00:00', '2005-07-15 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1647, 1562, '2005-07-21 09:00:00', '2005-07-21 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1648, 1562, '2005-07-22 10:00:00', '2005-07-22 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1649, 1562, '2005-07-22 15:00:00', '2005-07-22 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1650, 1562, '2005-07-28 09:00:00', '2005-07-28 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1651, 1562, '2005-07-29 10:00:00', '2005-07-29 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1652, 1562, '2005-07-29 15:00:00', '2005-07-29 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1653, 1562, '2005-08-04 09:00:00', '2005-08-04 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1654, 1562, '2005-08-05 10:00:00', '2005-08-05 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1655, 1562, '2005-08-05 15:00:00', '2005-08-05 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1656, 1562, '2005-08-11 09:00:00', '2005-08-11 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1657, 1562, '2005-08-12 10:00:00', '2005-08-12 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1658, 1562, '2005-08-12 15:00:00', '2005-08-12 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1659, 1562, '2005-08-18 09:00:00', '2005-08-18 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1660, 1562, '2005-08-19 10:00:00', '2005-08-19 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1661, 1562, '2005-08-19 15:00:00', '2005-08-19 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1662, 1562, '2005-08-25 09:00:00', '2005-08-25 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1663, 1562, '2005-08-26 10:00:00', '2005-08-26 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1664, 1562, '2005-08-26 15:00:00', '2005-08-26 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1665, 1562, '2005-09-01 09:00:00', '2005-09-01 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1666, 1562, '2005-09-02 10:00:00', '2005-09-02 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1667, 1562, '2005-09-02 15:00:00', '2005-09-02 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1668, 1562, '2005-09-08 09:00:00', '2005-09-08 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1669, 1562, '2005-09-09 10:00:00', '2005-09-09 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1670, 1562, '2005-09-09 15:00:00', '2005-09-09 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1671, 1562, '2005-09-15 09:00:00', '2005-09-15 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1672, 1562, '2005-09-16 10:00:00', '2005-09-16 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1673, 1562, '2005-09-16 15:00:00', '2005-09-16 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1674, 1562, '2005-09-22 09:00:00', '2005-09-22 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1675, 1562, '2005-09-23 10:00:00', '2005-09-23 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1676, 1562, '2005-09-23 15:00:00', '2005-09-23 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1677, 1562, '2005-09-29 09:00:00', '2005-09-29 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1678, 1562, '2005-09-30 10:00:00', '2005-09-30 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1679, 1562, '2005-09-30 15:00:00', '2005-09-30 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1680, 1562, '2005-10-06 09:00:00', '2005-10-06 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1681, 1562, '2005-10-07 10:00:00', '2005-10-07 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1682, 1562, '2005-10-07 15:00:00', '2005-10-07 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1683, 1562, '2005-10-13 09:00:00', '2005-10-13 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1684, 1562, '2005-10-14 10:00:00', '2005-10-14 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1685, 1562, '2005-10-14 15:00:00', '2005-10-14 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1686, 1562, '2005-10-20 09:00:00', '2005-10-20 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1687, 1562, '2005-10-21 10:00:00', '2005-10-21 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1688, 1562, '2005-10-21 15:00:00', '2005-10-21 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1689, 1562, '2005-10-27 09:00:00', '2005-10-27 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1690, 1562, '2005-10-28 10:00:00', '2005-10-28 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1691, 1562, '2005-10-28 15:00:00', '2005-10-28 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1692, 1562, '2005-11-03 09:00:00', '2005-11-03 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1693, 1562, '2005-11-04 10:00:00', '2005-11-04 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1694, 1562, '2005-11-04 15:00:00', '2005-11-04 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1695, 1562, '2005-11-10 09:00:00', '2005-11-10 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1696, 1562, '2005-11-11 10:00:00', '2005-11-11 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1697, 1562, '2005-11-11 15:00:00', '2005-11-11 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1698, 1562, '2005-11-17 09:00:00', '2005-11-17 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1699, 1562, '2005-11-18 10:00:00', '2005-11-18 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1700, 1562, '2005-11-18 15:00:00', '2005-11-18 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1701, 1562, '2005-11-24 09:00:00', '2005-11-24 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1702, 1562, '2005-11-25 10:00:00', '2005-11-25 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1703, 1562, '2005-11-25 15:00:00', '2005-11-25 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1704, 1562, '2005-12-01 09:00:00', '2005-12-01 15:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1705, 1562, '2005-12-02 10:00:00', '2005-12-02 14:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1706, 1562, '2005-12-02 15:00:00', '2005-12-02 19:00:00', '', 1125, 1111, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1808, 1807, '2005-01-03 09:00:00', '2005-01-03 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1809, 1807, '2005-01-04 09:00:00', '2005-01-04 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1810, 1807, '2005-01-05 09:00:00', '2005-01-05 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1811, 1807, '2005-01-06 09:00:00', '2005-01-06 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1812, 1807, '2005-01-07 09:00:00', '2005-01-07 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1813, 1807, '2005-01-10 09:00:00', '2005-01-10 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1814, 1807, '2005-01-11 09:00:00', '2005-01-11 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1815, 1807, '2005-01-12 09:00:00', '2005-01-12 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1816, 1807, '2005-01-13 09:00:00', '2005-01-13 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1817, 1807, '2005-01-14 09:00:00', '2005-01-14 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1818, 1807, '2005-01-17 09:00:00', '2005-01-17 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1819, 1807, '2005-01-18 09:00:00', '2005-01-18 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1820, 1807, '2005-01-19 09:00:00', '2005-01-19 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1821, 1807, '2005-01-20 09:00:00', '2005-01-20 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1822, 1807, '2005-01-21 09:00:00', '2005-01-21 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1823, 1807, '2005-01-24 09:00:00', '2005-01-24 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1824, 1807, '2005-01-25 09:00:00', '2005-01-25 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1825, 1807, '2005-01-26 09:00:00', '2005-01-26 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1826, 1807, '2005-01-27 09:00:00', '2005-01-27 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1827, 1807, '2005-01-28 09:00:00', '2005-01-28 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1828, 1807, '2005-01-31 09:00:00', '2005-01-31 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1829, 1807, '2005-02-01 09:00:00', '2005-02-01 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1830, 1807, '2005-02-02 09:00:00', '2005-02-02 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1831, 1807, '2005-02-03 09:00:00', '2005-02-03 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1832, 1807, '2005-02-04 09:00:00', '2005-02-04 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1833, 1807, '2005-02-07 09:00:00', '2005-02-07 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1834, 1807, '2005-02-08 09:00:00', '2005-02-08 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1835, 1807, '2005-02-09 09:00:00', '2005-02-09 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1836, 1807, '2005-02-10 09:00:00', '2005-02-10 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1837, 1807, '2005-02-11 09:00:00', '2005-02-11 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1838, 1807, '2005-02-14 09:00:00', '2005-02-14 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1839, 1807, '2005-02-15 09:00:00', '2005-02-15 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1840, 1807, '2005-02-16 09:00:00', '2005-02-16 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1841, 1807, '2005-02-17 09:00:00', '2005-02-17 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1842, 1807, '2005-02-18 09:00:00', '2005-02-18 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1843, 1807, '2005-02-21 09:00:00', '2005-02-21 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1844, 1807, '2005-02-22 09:00:00', '2005-02-22 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1845, 1807, '2005-02-23 09:00:00', '2005-02-23 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1846, 1807, '2005-02-24 09:00:00', '2005-02-24 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1847, 1807, '2005-02-25 09:00:00', '2005-02-25 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1848, 1807, '2005-02-28 09:00:00', '2005-02-28 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1849, 1807, '2005-03-01 09:00:00', '2005-03-01 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1850, 1807, '2005-03-02 09:00:00', '2005-03-02 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1851, 1807, '2005-03-03 09:00:00', '2005-03-03 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1852, 1807, '2005-03-04 09:00:00', '2005-03-04 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1853, 1807, '2005-03-07 09:00:00', '2005-03-07 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1854, 1807, '2005-03-08 09:00:00', '2005-03-08 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1855, 1807, '2005-03-09 09:00:00', '2005-03-09 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1856, 1807, '2005-03-10 09:00:00', '2005-03-10 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1857, 1807, '2005-03-11 09:00:00', '2005-03-11 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1858, 1807, '2005-03-14 09:00:00', '2005-03-14 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1859, 1807, '2005-03-15 09:00:00', '2005-03-15 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1860, 1807, '2005-03-16 09:00:00', '2005-03-16 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1861, 1807, '2005-03-17 09:00:00', '2005-03-17 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1862, 1807, '2005-03-18 09:00:00', '2005-03-18 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1863, 1807, '2005-03-21 09:00:00', '2005-03-21 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1864, 1807, '2005-03-22 09:00:00', '2005-03-22 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1865, 1807, '2005-03-23 09:00:00', '2005-03-23 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1866, 1807, '2005-03-24 09:00:00', '2005-03-24 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1867, 1807, '2005-03-25 09:00:00', '2005-03-25 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1868, 1807, '2005-03-28 09:00:00', '2005-03-28 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1869, 1807, '2005-03-29 09:00:00', '2005-03-29 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1870, 1807, '2005-03-30 09:00:00', '2005-03-30 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1871, 1807, '2005-03-31 09:00:00', '2005-03-31 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1872, 1807, '2005-04-01 09:00:00', '2005-04-01 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1873, 1807, '2005-04-04 09:00:00', '2005-04-04 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1874, 1807, '2005-04-05 09:00:00', '2005-04-05 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1875, 1807, '2005-04-06 09:00:00', '2005-04-06 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1876, 1807, '2005-04-07 09:00:00', '2005-04-07 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1877, 1807, '2005-04-08 09:00:00', '2005-04-08 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1878, 1807, '2005-04-11 09:00:00', '2005-04-11 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1879, 1807, '2005-04-12 09:00:00', '2005-04-12 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1880, 1807, '2005-04-13 09:00:00', '2005-04-13 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1881, 1807, '2005-04-14 09:00:00', '2005-04-14 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1882, 1807, '2005-04-15 09:00:00', '2005-04-15 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1883, 1807, '2005-04-18 09:00:00', '2005-04-18 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1884, 1807, '2005-04-19 09:00:00', '2005-04-19 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1885, 1807, '2005-04-20 09:00:00', '2005-04-20 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1886, 1807, '2005-04-21 09:00:00', '2005-04-21 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1887, 1807, '2005-04-22 09:00:00', '2005-04-22 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1888, 1807, '2005-04-25 09:00:00', '2005-04-25 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1889, 1807, '2005-04-26 09:00:00', '2005-04-26 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1890, 1807, '2005-04-27 09:00:00', '2005-04-27 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1891, 1807, '2005-04-28 09:00:00', '2005-04-28 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1892, 1807, '2005-04-29 09:00:00', '2005-04-29 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1893, 1807, '2005-05-02 09:00:00', '2005-05-02 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1894, 1807, '2005-05-03 09:00:00', '2005-05-03 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1895, 1807, '2005-05-04 09:00:00', '2005-05-04 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1896, 1807, '2005-05-05 09:00:00', '2005-05-05 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1897, 1807, '2005-05-06 09:00:00', '2005-05-06 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1898, 1807, '2005-05-09 09:00:00', '2005-05-09 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1899, 1807, '2005-05-10 09:00:00', '2005-05-10 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1900, 1807, '2005-05-11 09:00:00', '2005-05-11 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1901, 1807, '2005-05-12 09:00:00', '2005-05-12 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1902, 1807, '2005-05-13 09:00:00', '2005-05-13 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1903, 1807, '2005-05-16 09:00:00', '2005-05-16 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1904, 1807, '2005-05-17 09:00:00', '2005-05-17 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1905, 1807, '2005-05-18 09:00:00', '2005-05-18 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1906, 1807, '2005-05-19 09:00:00', '2005-05-19 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1907, 1807, '2005-05-20 09:00:00', '2005-05-20 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1908, 1807, '2005-05-23 09:00:00', '2005-05-23 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1909, 1807, '2005-05-24 09:00:00', '2005-05-24 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1910, 1807, '2005-05-25 09:00:00', '2005-05-25 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1911, 1807, '2005-05-26 09:00:00', '2005-05-26 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1912, 1807, '2005-05-27 09:00:00', '2005-05-27 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1913, 1807, '2005-05-30 09:00:00', '2005-05-30 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1914, 1807, '2005-05-31 09:00:00', '2005-05-31 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1915, 1807, '2005-06-01 09:00:00', '2005-06-01 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1916, 1807, '2005-06-02 09:00:00', '2005-06-02 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1917, 1807, '2005-06-03 09:00:00', '2005-06-03 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1918, 1807, '2005-06-06 09:00:00', '2005-06-06 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1919, 1807, '2005-06-07 09:00:00', '2005-06-07 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1920, 1807, '2005-06-08 09:00:00', '2005-06-08 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1921, 1807, '2005-06-09 09:00:00', '2005-06-09 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1922, 1807, '2005-06-10 09:00:00', '2005-06-10 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1923, 1807, '2005-06-13 09:00:00', '2005-06-13 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1924, 1807, '2005-06-14 09:00:00', '2005-06-14 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1925, 1807, '2005-06-15 09:00:00', '2005-06-15 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1926, 1807, '2005-06-16 09:00:00', '2005-06-16 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1927, 1807, '2005-06-17 09:00:00', '2005-06-17 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1928, 1807, '2005-06-20 09:00:00', '2005-06-20 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1929, 1807, '2005-06-21 09:00:00', '2005-06-21 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1930, 1807, '2005-06-22 09:00:00', '2005-06-22 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1931, 1807, '2005-06-23 09:00:00', '2005-06-23 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1932, 1807, '2005-06-24 09:00:00', '2005-06-24 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1933, 1807, '2005-06-27 09:00:00', '2005-06-27 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1934, 1807, '2005-06-28 09:00:00', '2005-06-28 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1935, 1807, '2005-06-29 09:00:00', '2005-06-29 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1936, 1807, '2005-06-30 09:00:00', '2005-06-30 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1937, 1807, '2005-07-01 09:00:00', '2005-07-01 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1938, 1807, '2005-07-04 09:00:00', '2005-07-04 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1939, 1807, '2005-07-05 09:00:00', '2005-07-05 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1940, 1807, '2005-07-06 09:00:00', '2005-07-06 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1941, 1807, '2005-07-07 09:00:00', '2005-07-07 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1942, 1807, '2005-07-08 09:00:00', '2005-07-08 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1943, 1807, '2005-07-11 09:00:00', '2005-07-11 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1944, 1807, '2005-07-12 09:00:00', '2005-07-12 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1945, 1807, '2005-07-13 09:00:00', '2005-07-13 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1946, 1807, '2005-07-14 09:00:00', '2005-07-14 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1947, 1807, '2005-07-15 09:00:00', '2005-07-15 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1948, 1807, '2005-07-18 09:00:00', '2005-07-18 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1949, 1807, '2005-07-19 09:00:00', '2005-07-19 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1950, 1807, '2005-07-20 09:00:00', '2005-07-20 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1951, 1807, '2005-07-21 09:00:00', '2005-07-21 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1952, 1807, '2005-07-22 09:00:00', '2005-07-22 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1953, 1807, '2005-07-25 09:00:00', '2005-07-25 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1954, 1807, '2005-07-26 09:00:00', '2005-07-26 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1955, 1807, '2005-07-27 09:00:00', '2005-07-27 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1956, 1807, '2005-07-28 09:00:00', '2005-07-28 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1957, 1807, '2005-07-29 09:00:00', '2005-07-29 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1958, 1807, '2005-08-01 09:00:00', '2005-08-01 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1959, 1807, '2005-08-02 09:00:00', '2005-08-02 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1960, 1807, '2005-08-03 09:00:00', '2005-08-03 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1961, 1807, '2005-08-04 09:00:00', '2005-08-04 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1962, 1807, '2005-08-05 09:00:00', '2005-08-05 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1963, 1807, '2005-08-08 09:00:00', '2005-08-08 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1964, 1807, '2005-08-09 09:00:00', '2005-08-09 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1965, 1807, '2005-08-10 09:00:00', '2005-08-10 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1966, 1807, '2005-08-11 09:00:00', '2005-08-11 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1967, 1807, '2005-08-12 09:00:00', '2005-08-12 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1968, 1807, '2005-08-15 09:00:00', '2005-08-15 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1969, 1807, '2005-08-16 09:00:00', '2005-08-16 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1970, 1807, '2005-08-17 09:00:00', '2005-08-17 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1971, 1807, '2005-08-18 09:00:00', '2005-08-18 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1972, 1807, '2005-08-19 09:00:00', '2005-08-19 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1973, 1807, '2005-08-22 09:00:00', '2005-08-22 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1974, 1807, '2005-08-23 09:00:00', '2005-08-23 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1975, 1807, '2005-08-24 09:00:00', '2005-08-24 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1976, 1807, '2005-08-25 09:00:00', '2005-08-25 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1977, 1807, '2005-08-26 09:00:00', '2005-08-26 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1978, 1807, '2005-08-29 09:00:00', '2005-08-29 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1979, 1807, '2005-08-30 09:00:00', '2005-08-30 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1980, 1807, '2005-08-31 09:00:00', '2005-08-31 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1981, 1807, '2005-09-01 09:00:00', '2005-09-01 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1982, 1807, '2005-09-02 09:00:00', '2005-09-02 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1983, 1807, '2005-09-05 09:00:00', '2005-09-05 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1984, 1807, '2005-09-06 09:00:00', '2005-09-06 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1985, 1807, '2005-09-07 09:00:00', '2005-09-07 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1986, 1807, '2005-09-08 09:00:00', '2005-09-08 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1987, 1807, '2005-09-09 09:00:00', '2005-09-09 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1988, 1807, '2005-09-12 09:00:00', '2005-09-12 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1989, 1807, '2005-09-13 09:00:00', '2005-09-13 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1990, 1807, '2005-09-14 09:00:00', '2005-09-14 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1991, 1807, '2005-09-15 09:00:00', '2005-09-15 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1992, 1807, '2005-09-16 09:00:00', '2005-09-16 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1993, 1807, '2005-09-19 09:00:00', '2005-09-19 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1994, 1807, '2005-09-20 09:00:00', '2005-09-20 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1995, 1807, '2005-09-21 09:00:00', '2005-09-21 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1996, 1807, '2005-09-22 09:00:00', '2005-09-22 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1997, 1807, '2005-09-23 09:00:00', '2005-09-23 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1998, 1807, '2005-09-26 09:00:00', '2005-09-26 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (1999, 1807, '2005-09-27 09:00:00', '2005-09-27 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2000, 1807, '2005-09-28 09:00:00', '2005-09-28 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2001, 1807, '2005-09-29 09:00:00', '2005-09-29 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2002, 1807, '2005-09-30 09:00:00', '2005-09-30 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2003, 1807, '2005-10-03 09:00:00', '2005-10-03 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2004, 1807, '2005-10-04 09:00:00', '2005-10-04 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2005, 1807, '2005-10-05 09:00:00', '2005-10-05 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2006, 1807, '2005-10-06 09:00:00', '2005-10-06 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2007, 1807, '2005-10-07 09:00:00', '2005-10-07 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2008, 1807, '2005-10-10 09:00:00', '2005-10-10 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2009, 1807, '2005-10-11 09:00:00', '2005-10-11 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2010, 1807, '2005-10-12 09:00:00', '2005-10-12 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2011, 1807, '2005-10-13 09:00:00', '2005-10-13 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2012, 1807, '2005-10-14 09:00:00', '2005-10-14 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2013, 1807, '2005-10-17 09:00:00', '2005-10-17 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2014, 1807, '2005-10-18 09:00:00', '2005-10-18 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2015, 1807, '2005-10-19 09:00:00', '2005-10-19 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2016, 1807, '2005-10-20 09:00:00', '2005-10-20 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2017, 1807, '2005-10-21 09:00:00', '2005-10-21 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2018, 1807, '2005-10-24 09:00:00', '2005-10-24 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2019, 1807, '2005-10-25 09:00:00', '2005-10-25 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2020, 1807, '2005-10-26 09:00:00', '2005-10-26 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2021, 1807, '2005-10-27 09:00:00', '2005-10-27 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2022, 1807, '2005-10-28 09:00:00', '2005-10-28 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2023, 1807, '2005-10-31 09:00:00', '2005-10-31 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2024, 1807, '2005-11-01 09:00:00', '2005-11-01 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2025, 1807, '2005-11-02 09:00:00', '2005-11-02 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2026, 1807, '2005-11-03 09:00:00', '2005-11-03 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2027, 1807, '2005-11-04 09:00:00', '2005-11-04 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2028, 1807, '2005-11-07 09:00:00', '2005-11-07 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2029, 1807, '2005-11-08 09:00:00', '2005-11-08 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2030, 1807, '2005-11-09 09:00:00', '2005-11-09 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2031, 1807, '2005-11-10 09:00:00', '2005-11-10 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2032, 1807, '2005-11-11 09:00:00', '2005-11-11 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2033, 1807, '2005-11-14 09:00:00', '2005-11-14 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2034, 1807, '2005-11-15 09:00:00', '2005-11-15 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2035, 1807, '2005-11-16 09:00:00', '2005-11-16 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2036, 1807, '2005-11-17 09:00:00', '2005-11-17 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2037, 1807, '2005-11-18 09:00:00', '2005-11-18 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2038, 1807, '2005-11-21 09:00:00', '2005-11-21 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2039, 1807, '2005-11-22 09:00:00', '2005-11-22 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2040, 1807, '2005-11-23 09:00:00', '2005-11-23 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2041, 1807, '2005-11-24 09:00:00', '2005-11-24 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2042, 1807, '2005-11-25 09:00:00', '2005-11-25 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2043, 1807, '2005-11-28 09:00:00', '2005-11-28 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2044, 1807, '2005-11-29 09:00:00', '2005-11-29 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2045, 1807, '2005-11-30 09:00:00', '2005-11-30 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2046, 1807, '2005-12-01 09:00:00', '2005-12-01 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (2047, 1807, '2005-12-02 09:00:00', '2005-12-02 16:00:00', '', 1125, 0, 1, NULL, 0);
INSERT INTO `occurences` VALUES (8056, 8055, '2005-03-18 08:00:00', '2005-03-18 08:15:00', 'test', 1125, 1121, 1, 1110, 0);
INSERT INTO `occurences` VALUES (8058, 8060, '2005-03-18 10:00:00', '2005-03-18 10:15:00', 'test', 1125, 1111, 1, 1110, 0);

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
) TYPE=MyISAM COMMENT='Stores which items are owned by which user';

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
INSERT INTO `ownership` VALUES (1055, 1);
INSERT INTO `ownership` VALUES (1056, 1);
INSERT INTO `ownership` VALUES (1057, 1);
INSERT INTO `ownership` VALUES (1058, 1);
INSERT INTO `ownership` VALUES (1059, 1);
INSERT INTO `ownership` VALUES (1060, 1);
INSERT INTO `ownership` VALUES (1061, 1);
INSERT INTO `ownership` VALUES (1062, 1);
INSERT INTO `ownership` VALUES (1063, 1);
INSERT INTO `ownership` VALUES (1064, 1);
INSERT INTO `ownership` VALUES (1065, 1);
INSERT INTO `ownership` VALUES (1066, 1);
INSERT INTO `ownership` VALUES (1067, 1);
INSERT INTO `ownership` VALUES (1068, 1);
INSERT INTO `ownership` VALUES (1069, 1);
INSERT INTO `ownership` VALUES (1070, 1);
INSERT INTO `ownership` VALUES (1071, 1);
INSERT INTO `ownership` VALUES (1072, 1);
INSERT INTO `ownership` VALUES (1073, 1);
INSERT INTO `ownership` VALUES (1074, 1);
INSERT INTO `ownership` VALUES (1075, 1);
INSERT INTO `ownership` VALUES (1076, 1);
INSERT INTO `ownership` VALUES (1077, 1);
INSERT INTO `ownership` VALUES (1078, 1);
INSERT INTO `ownership` VALUES (1079, 1);
INSERT INTO `ownership` VALUES (1080, 1);
INSERT INTO `ownership` VALUES (1081, 1);
INSERT INTO `ownership` VALUES (1082, 1);
INSERT INTO `ownership` VALUES (1083, 1);
INSERT INTO `ownership` VALUES (1084, 1);
INSERT INTO `ownership` VALUES (1085, 1);
INSERT INTO `ownership` VALUES (1086, 1);
INSERT INTO `ownership` VALUES (1087, 1);
INSERT INTO `ownership` VALUES (1088, 1);
INSERT INTO `ownership` VALUES (1089, 1);
INSERT INTO `ownership` VALUES (1090, 1);
INSERT INTO `ownership` VALUES (1091, 1);
INSERT INTO `ownership` VALUES (1092, 1);
INSERT INTO `ownership` VALUES (1093, 1);
INSERT INTO `ownership` VALUES (1094, 1);
INSERT INTO `ownership` VALUES (1095, 1);
INSERT INTO `ownership` VALUES (1096, 1);
INSERT INTO `ownership` VALUES (1097, 1);
INSERT INTO `ownership` VALUES (1098, 1);
INSERT INTO `ownership` VALUES (1099, 1);
INSERT INTO `ownership` VALUES (1100, 1);
INSERT INTO `ownership` VALUES (1101, 1);
INSERT INTO `ownership` VALUES (1102, 1);
INSERT INTO `ownership` VALUES (1103, 1);
INSERT INTO `ownership` VALUES (1104, 1);
INSERT INTO `ownership` VALUES (1105, 1);
INSERT INTO `ownership` VALUES (1106, 1);
INSERT INTO `ownership` VALUES (1107, 1);
INSERT INTO `ownership` VALUES (1108, 1);
INSERT INTO `ownership` VALUES (1109, 1);
INSERT INTO `ownership` VALUES (1110, 1);
INSERT INTO `ownership` VALUES (1111, 1);
INSERT INTO `ownership` VALUES (1112, 1);
INSERT INTO `ownership` VALUES (1113, 1);
INSERT INTO `ownership` VALUES (1114, 1);
INSERT INTO `ownership` VALUES (1115, 1);
INSERT INTO `ownership` VALUES (1116, 1);
INSERT INTO `ownership` VALUES (1117, 1);
INSERT INTO `ownership` VALUES (1118, 1);
INSERT INTO `ownership` VALUES (1119, 1);
INSERT INTO `ownership` VALUES (1120, 1);
INSERT INTO `ownership` VALUES (1121, 1);
INSERT INTO `ownership` VALUES (1122, 1);
INSERT INTO `ownership` VALUES (1123, 1);
INSERT INTO `ownership` VALUES (1124, 1);
INSERT INTO `ownership` VALUES (1125, 1);
INSERT INTO `ownership` VALUES (1126, 1);
INSERT INTO `ownership` VALUES (1127, 1);
INSERT INTO `ownership` VALUES (1128, 1);
INSERT INTO `ownership` VALUES (1129, 1);
INSERT INTO `ownership` VALUES (1130, 1);
INSERT INTO `ownership` VALUES (1131, 1);
INSERT INTO `ownership` VALUES (1132, 1);
INSERT INTO `ownership` VALUES (1133, 1);
INSERT INTO `ownership` VALUES (1134, 1);
INSERT INTO `ownership` VALUES (1135, 1);
INSERT INTO `ownership` VALUES (1136, 1);
INSERT INTO `ownership` VALUES (1137, 1);
INSERT INTO `ownership` VALUES (1138, 1);
INSERT INTO `ownership` VALUES (1139, 1);
INSERT INTO `ownership` VALUES (1140, 1);
INSERT INTO `ownership` VALUES (1141, 1);
INSERT INTO `ownership` VALUES (1142, 1);
INSERT INTO `ownership` VALUES (1143, 1);
INSERT INTO `ownership` VALUES (1144, 1);
INSERT INTO `ownership` VALUES (1145, 1);
INSERT INTO `ownership` VALUES (1146, 1);
INSERT INTO `ownership` VALUES (1147, 1);
INSERT INTO `ownership` VALUES (1148, 1);
INSERT INTO `ownership` VALUES (1149, 1);
INSERT INTO `ownership` VALUES (1150, 1);
INSERT INTO `ownership` VALUES (1151, 1);
INSERT INTO `ownership` VALUES (1152, 1);
INSERT INTO `ownership` VALUES (1153, 1);
INSERT INTO `ownership` VALUES (1154, 1);
INSERT INTO `ownership` VALUES (1155, 1);
INSERT INTO `ownership` VALUES (1156, 1);
INSERT INTO `ownership` VALUES (1157, 1);
INSERT INTO `ownership` VALUES (1158, 1);
INSERT INTO `ownership` VALUES (1159, 1);
INSERT INTO `ownership` VALUES (1160, 1);
INSERT INTO `ownership` VALUES (1161, 1);
INSERT INTO `ownership` VALUES (1162, 1);
INSERT INTO `ownership` VALUES (1163, 1);
INSERT INTO `ownership` VALUES (1164, 1);
INSERT INTO `ownership` VALUES (1165, 1);
INSERT INTO `ownership` VALUES (1166, 1);
INSERT INTO `ownership` VALUES (1167, 1);
INSERT INTO `ownership` VALUES (1168, 1);
INSERT INTO `ownership` VALUES (1169, 1);
INSERT INTO `ownership` VALUES (1170, 1);
INSERT INTO `ownership` VALUES (1171, 1);
INSERT INTO `ownership` VALUES (1172, 1);
INSERT INTO `ownership` VALUES (1173, 1);
INSERT INTO `ownership` VALUES (1174, 1);
INSERT INTO `ownership` VALUES (1175, 1);
INSERT INTO `ownership` VALUES (1176, 1);
INSERT INTO `ownership` VALUES (1177, 1);
INSERT INTO `ownership` VALUES (1178, 1);
INSERT INTO `ownership` VALUES (1179, 1);
INSERT INTO `ownership` VALUES (1180, 1);
INSERT INTO `ownership` VALUES (1181, 1);
INSERT INTO `ownership` VALUES (1182, 1);
INSERT INTO `ownership` VALUES (1183, 1);
INSERT INTO `ownership` VALUES (1184, 1);
INSERT INTO `ownership` VALUES (1185, 1);
INSERT INTO `ownership` VALUES (1186, 1);
INSERT INTO `ownership` VALUES (1187, 1);
INSERT INTO `ownership` VALUES (1188, 1);
INSERT INTO `ownership` VALUES (1189, 1);
INSERT INTO `ownership` VALUES (1190, 1);
INSERT INTO `ownership` VALUES (1191, 1);
INSERT INTO `ownership` VALUES (1192, 1);
INSERT INTO `ownership` VALUES (1193, 1);
INSERT INTO `ownership` VALUES (1194, 1);
INSERT INTO `ownership` VALUES (1195, 1);
INSERT INTO `ownership` VALUES (1196, 1);
INSERT INTO `ownership` VALUES (1197, 1);
INSERT INTO `ownership` VALUES (1198, 1);
INSERT INTO `ownership` VALUES (1199, 1);
INSERT INTO `ownership` VALUES (1200, 1);
INSERT INTO `ownership` VALUES (1201, 1);
INSERT INTO `ownership` VALUES (1202, 1);
INSERT INTO `ownership` VALUES (1203, 1);
INSERT INTO `ownership` VALUES (1204, 1);
INSERT INTO `ownership` VALUES (1205, 1);
INSERT INTO `ownership` VALUES (1206, 1);
INSERT INTO `ownership` VALUES (1207, 1);
INSERT INTO `ownership` VALUES (1208, 1);
INSERT INTO `ownership` VALUES (1209, 1);
INSERT INTO `ownership` VALUES (1210, 1);
INSERT INTO `ownership` VALUES (1211, 1);
INSERT INTO `ownership` VALUES (1212, 1);
INSERT INTO `ownership` VALUES (1213, 1);
INSERT INTO `ownership` VALUES (1214, 1);
INSERT INTO `ownership` VALUES (1215, 1);
INSERT INTO `ownership` VALUES (1216, 1);
INSERT INTO `ownership` VALUES (1217, 1);
INSERT INTO `ownership` VALUES (1218, 1);
INSERT INTO `ownership` VALUES (1219, 1);
INSERT INTO `ownership` VALUES (1220, 1);
INSERT INTO `ownership` VALUES (1221, 1);
INSERT INTO `ownership` VALUES (1222, 1);
INSERT INTO `ownership` VALUES (1223, 1);
INSERT INTO `ownership` VALUES (1224, 1);
INSERT INTO `ownership` VALUES (1225, 1);
INSERT INTO `ownership` VALUES (1226, 1);
INSERT INTO `ownership` VALUES (1227, 1);
INSERT INTO `ownership` VALUES (1228, 1);
INSERT INTO `ownership` VALUES (1229, 1);
INSERT INTO `ownership` VALUES (1230, 1);
INSERT INTO `ownership` VALUES (1231, 1);
INSERT INTO `ownership` VALUES (1232, 1);
INSERT INTO `ownership` VALUES (1233, 1);
INSERT INTO `ownership` VALUES (1234, 1);
INSERT INTO `ownership` VALUES (1235, 1);
INSERT INTO `ownership` VALUES (1236, 1);
INSERT INTO `ownership` VALUES (1237, 1);
INSERT INTO `ownership` VALUES (1238, 1);
INSERT INTO `ownership` VALUES (1239, 1);
INSERT INTO `ownership` VALUES (1240, 1);
INSERT INTO `ownership` VALUES (1241, 1);
INSERT INTO `ownership` VALUES (1242, 1);
INSERT INTO `ownership` VALUES (1243, 1);
INSERT INTO `ownership` VALUES (1244, 1);
INSERT INTO `ownership` VALUES (1245, 1);
INSERT INTO `ownership` VALUES (1246, 1);
INSERT INTO `ownership` VALUES (1247, 1);
INSERT INTO `ownership` VALUES (1248, 1);
INSERT INTO `ownership` VALUES (1249, 1);
INSERT INTO `ownership` VALUES (1250, 1);
INSERT INTO `ownership` VALUES (1251, 1);
INSERT INTO `ownership` VALUES (1252, 1);
INSERT INTO `ownership` VALUES (1253, 1);
INSERT INTO `ownership` VALUES (1254, 1);
INSERT INTO `ownership` VALUES (1255, 1);
INSERT INTO `ownership` VALUES (1256, 1);
INSERT INTO `ownership` VALUES (1257, 1);
INSERT INTO `ownership` VALUES (1258, 1);
INSERT INTO `ownership` VALUES (1259, 1);
INSERT INTO `ownership` VALUES (1260, 1);
INSERT INTO `ownership` VALUES (1261, 1);
INSERT INTO `ownership` VALUES (1262, 1);
INSERT INTO `ownership` VALUES (1263, 1);
INSERT INTO `ownership` VALUES (1264, 1);
INSERT INTO `ownership` VALUES (1265, 1);
INSERT INTO `ownership` VALUES (1266, 1);
INSERT INTO `ownership` VALUES (1267, 1);
INSERT INTO `ownership` VALUES (1268, 1);
INSERT INTO `ownership` VALUES (1269, 1);
INSERT INTO `ownership` VALUES (1270, 1);
INSERT INTO `ownership` VALUES (1271, 1);
INSERT INTO `ownership` VALUES (1272, 1);
INSERT INTO `ownership` VALUES (1273, 1);
INSERT INTO `ownership` VALUES (1274, 1);
INSERT INTO `ownership` VALUES (1275, 1);
INSERT INTO `ownership` VALUES (1276, 1);
INSERT INTO `ownership` VALUES (1277, 1);
INSERT INTO `ownership` VALUES (1278, 1);
INSERT INTO `ownership` VALUES (1279, 1);
INSERT INTO `ownership` VALUES (1280, 1);
INSERT INTO `ownership` VALUES (1281, 1);
INSERT INTO `ownership` VALUES (1282, 1);
INSERT INTO `ownership` VALUES (1283, 1);
INSERT INTO `ownership` VALUES (1284, 1);
INSERT INTO `ownership` VALUES (1285, 1);
INSERT INTO `ownership` VALUES (1286, 1);
INSERT INTO `ownership` VALUES (1287, 1);
INSERT INTO `ownership` VALUES (1288, 1);
INSERT INTO `ownership` VALUES (1289, 1);
INSERT INTO `ownership` VALUES (1290, 1);
INSERT INTO `ownership` VALUES (1291, 1);
INSERT INTO `ownership` VALUES (1292, 1);
INSERT INTO `ownership` VALUES (1293, 1);
INSERT INTO `ownership` VALUES (1294, 1);
INSERT INTO `ownership` VALUES (1295, 1);
INSERT INTO `ownership` VALUES (1296, 1);
INSERT INTO `ownership` VALUES (1297, 1);
INSERT INTO `ownership` VALUES (1298, 1);
INSERT INTO `ownership` VALUES (1299, 1);
INSERT INTO `ownership` VALUES (1300, 1);
INSERT INTO `ownership` VALUES (1301, 1);
INSERT INTO `ownership` VALUES (1302, 1);
INSERT INTO `ownership` VALUES (1303, 1);
INSERT INTO `ownership` VALUES (1304, 1);
INSERT INTO `ownership` VALUES (1305, 1);
INSERT INTO `ownership` VALUES (1306, 1);
INSERT INTO `ownership` VALUES (1307, 1);
INSERT INTO `ownership` VALUES (1308, 1);
INSERT INTO `ownership` VALUES (1309, 1);
INSERT INTO `ownership` VALUES (1310, 1);
INSERT INTO `ownership` VALUES (1311, 1);
INSERT INTO `ownership` VALUES (1312, 1);
INSERT INTO `ownership` VALUES (1313, 1);
INSERT INTO `ownership` VALUES (1314, 1);
INSERT INTO `ownership` VALUES (1315, 1);
INSERT INTO `ownership` VALUES (1316, 1);
INSERT INTO `ownership` VALUES (1317, 1);
INSERT INTO `ownership` VALUES (1318, 1);
INSERT INTO `ownership` VALUES (1319, 1);
INSERT INTO `ownership` VALUES (1320, 1);
INSERT INTO `ownership` VALUES (1321, 1);
INSERT INTO `ownership` VALUES (1322, 1);
INSERT INTO `ownership` VALUES (1323, 1);
INSERT INTO `ownership` VALUES (1324, 1);
INSERT INTO `ownership` VALUES (1325, 1);
INSERT INTO `ownership` VALUES (1326, 1);
INSERT INTO `ownership` VALUES (1327, 1);
INSERT INTO `ownership` VALUES (1328, 1);
INSERT INTO `ownership` VALUES (1329, 1);
INSERT INTO `ownership` VALUES (1330, 1);
INSERT INTO `ownership` VALUES (1331, 1);
INSERT INTO `ownership` VALUES (1332, 1);
INSERT INTO `ownership` VALUES (1333, 1);
INSERT INTO `ownership` VALUES (1334, 1);
INSERT INTO `ownership` VALUES (1335, 1);
INSERT INTO `ownership` VALUES (1336, 1);
INSERT INTO `ownership` VALUES (1337, 1);
INSERT INTO `ownership` VALUES (1338, 1);
INSERT INTO `ownership` VALUES (1339, 1);
INSERT INTO `ownership` VALUES (1340, 1);
INSERT INTO `ownership` VALUES (1341, 1);
INSERT INTO `ownership` VALUES (1342, 1);
INSERT INTO `ownership` VALUES (1343, 1);
INSERT INTO `ownership` VALUES (1344, 1);
INSERT INTO `ownership` VALUES (1345, 1);
INSERT INTO `ownership` VALUES (1346, 1);
INSERT INTO `ownership` VALUES (1347, 1);
INSERT INTO `ownership` VALUES (1348, 1);
INSERT INTO `ownership` VALUES (1349, 1);
INSERT INTO `ownership` VALUES (1350, 1);
INSERT INTO `ownership` VALUES (1351, 1);
INSERT INTO `ownership` VALUES (1352, 1);
INSERT INTO `ownership` VALUES (1353, 1);
INSERT INTO `ownership` VALUES (1354, 1);
INSERT INTO `ownership` VALUES (1355, 1);
INSERT INTO `ownership` VALUES (1356, 1);
INSERT INTO `ownership` VALUES (1357, 1);
INSERT INTO `ownership` VALUES (1358, 1);
INSERT INTO `ownership` VALUES (1359, 1);
INSERT INTO `ownership` VALUES (1360, 1);
INSERT INTO `ownership` VALUES (1361, 1);
INSERT INTO `ownership` VALUES (1362, 1);
INSERT INTO `ownership` VALUES (1363, 1);
INSERT INTO `ownership` VALUES (1364, 1);
INSERT INTO `ownership` VALUES (1365, 1);
INSERT INTO `ownership` VALUES (1366, 1);
INSERT INTO `ownership` VALUES (1367, 1);
INSERT INTO `ownership` VALUES (1368, 1);
INSERT INTO `ownership` VALUES (1369, 1);
INSERT INTO `ownership` VALUES (1370, 1);
INSERT INTO `ownership` VALUES (1371, 1);
INSERT INTO `ownership` VALUES (1372, 1);
INSERT INTO `ownership` VALUES (1373, 1);
INSERT INTO `ownership` VALUES (1374, 1);
INSERT INTO `ownership` VALUES (1375, 1);
INSERT INTO `ownership` VALUES (1376, 1);
INSERT INTO `ownership` VALUES (1377, 1);
INSERT INTO `ownership` VALUES (1378, 1);
INSERT INTO `ownership` VALUES (1379, 1);
INSERT INTO `ownership` VALUES (1380, 1);
INSERT INTO `ownership` VALUES (1381, 1);
INSERT INTO `ownership` VALUES (1382, 1);
INSERT INTO `ownership` VALUES (1383, 1);
INSERT INTO `ownership` VALUES (1384, 1);
INSERT INTO `ownership` VALUES (1385, 1);
INSERT INTO `ownership` VALUES (1386, 1);
INSERT INTO `ownership` VALUES (1387, 1);
INSERT INTO `ownership` VALUES (1388, 1);
INSERT INTO `ownership` VALUES (1389, 1);
INSERT INTO `ownership` VALUES (1390, 1);
INSERT INTO `ownership` VALUES (1391, 1);
INSERT INTO `ownership` VALUES (1392, 1);
INSERT INTO `ownership` VALUES (1393, 1);
INSERT INTO `ownership` VALUES (1394, 1);
INSERT INTO `ownership` VALUES (1395, 1);
INSERT INTO `ownership` VALUES (1396, 1);
INSERT INTO `ownership` VALUES (1397, 1);
INSERT INTO `ownership` VALUES (1398, 1);
INSERT INTO `ownership` VALUES (1399, 1);
INSERT INTO `ownership` VALUES (1400, 1);
INSERT INTO `ownership` VALUES (1401, 1);
INSERT INTO `ownership` VALUES (1402, 1);
INSERT INTO `ownership` VALUES (1403, 1);
INSERT INTO `ownership` VALUES (1404, 1);
INSERT INTO `ownership` VALUES (1405, 1);
INSERT INTO `ownership` VALUES (1406, 1);
INSERT INTO `ownership` VALUES (1407, 1);
INSERT INTO `ownership` VALUES (1408, 1);
INSERT INTO `ownership` VALUES (1409, 1);
INSERT INTO `ownership` VALUES (1410, 1);
INSERT INTO `ownership` VALUES (1411, 1);
INSERT INTO `ownership` VALUES (1412, 1);
INSERT INTO `ownership` VALUES (1413, 1);
INSERT INTO `ownership` VALUES (1414, 1);
INSERT INTO `ownership` VALUES (1415, 1);
INSERT INTO `ownership` VALUES (1416, 1);
INSERT INTO `ownership` VALUES (1417, 1);
INSERT INTO `ownership` VALUES (1418, 1);
INSERT INTO `ownership` VALUES (1419, 1);
INSERT INTO `ownership` VALUES (1420, 1);
INSERT INTO `ownership` VALUES (1421, 1);
INSERT INTO `ownership` VALUES (1422, 1);
INSERT INTO `ownership` VALUES (1423, 1);
INSERT INTO `ownership` VALUES (1424, 1);
INSERT INTO `ownership` VALUES (1425, 1);
INSERT INTO `ownership` VALUES (1426, 1);
INSERT INTO `ownership` VALUES (1427, 1);
INSERT INTO `ownership` VALUES (1428, 1);
INSERT INTO `ownership` VALUES (1429, 1);
INSERT INTO `ownership` VALUES (1430, 1);
INSERT INTO `ownership` VALUES (1431, 1);
INSERT INTO `ownership` VALUES (1432, 1);
INSERT INTO `ownership` VALUES (1433, 1);
INSERT INTO `ownership` VALUES (1434, 1);
INSERT INTO `ownership` VALUES (1435, 1);
INSERT INTO `ownership` VALUES (1436, 1);
INSERT INTO `ownership` VALUES (1437, 1);
INSERT INTO `ownership` VALUES (1438, 1);
INSERT INTO `ownership` VALUES (1439, 1);
INSERT INTO `ownership` VALUES (1440, 1);
INSERT INTO `ownership` VALUES (1441, 1);
INSERT INTO `ownership` VALUES (1442, 1);
INSERT INTO `ownership` VALUES (1443, 1);
INSERT INTO `ownership` VALUES (1444, 1);
INSERT INTO `ownership` VALUES (1445, 1);
INSERT INTO `ownership` VALUES (1446, 1);
INSERT INTO `ownership` VALUES (1447, 1);
INSERT INTO `ownership` VALUES (1448, 1);
INSERT INTO `ownership` VALUES (1449, 1);
INSERT INTO `ownership` VALUES (1450, 1);
INSERT INTO `ownership` VALUES (1451, 1);
INSERT INTO `ownership` VALUES (1452, 1);
INSERT INTO `ownership` VALUES (1453, 1);
INSERT INTO `ownership` VALUES (1454, 1);
INSERT INTO `ownership` VALUES (1455, 1);
INSERT INTO `ownership` VALUES (1456, 1);
INSERT INTO `ownership` VALUES (1457, 1);
INSERT INTO `ownership` VALUES (1458, 1);
INSERT INTO `ownership` VALUES (1459, 1);
INSERT INTO `ownership` VALUES (1460, 1);
INSERT INTO `ownership` VALUES (1461, 1);
INSERT INTO `ownership` VALUES (1462, 1);
INSERT INTO `ownership` VALUES (1463, 1);
INSERT INTO `ownership` VALUES (1464, 1);
INSERT INTO `ownership` VALUES (1465, 1);
INSERT INTO `ownership` VALUES (1466, 1);
INSERT INTO `ownership` VALUES (1467, 1);
INSERT INTO `ownership` VALUES (1468, 1);
INSERT INTO `ownership` VALUES (1469, 1);
INSERT INTO `ownership` VALUES (1470, 1);
INSERT INTO `ownership` VALUES (1471, 1);
INSERT INTO `ownership` VALUES (1472, 1);
INSERT INTO `ownership` VALUES (1473, 1);
INSERT INTO `ownership` VALUES (1474, 1);
INSERT INTO `ownership` VALUES (1475, 1);
INSERT INTO `ownership` VALUES (1476, 1);
INSERT INTO `ownership` VALUES (1477, 1);
INSERT INTO `ownership` VALUES (1478, 1);
INSERT INTO `ownership` VALUES (1479, 1);
INSERT INTO `ownership` VALUES (1480, 1);
INSERT INTO `ownership` VALUES (1481, 1);
INSERT INTO `ownership` VALUES (1482, 1);
INSERT INTO `ownership` VALUES (1483, 1);
INSERT INTO `ownership` VALUES (1484, 1);
INSERT INTO `ownership` VALUES (1485, 1);
INSERT INTO `ownership` VALUES (1486, 1);
INSERT INTO `ownership` VALUES (1487, 1);
INSERT INTO `ownership` VALUES (1488, 1);
INSERT INTO `ownership` VALUES (1489, 1);
INSERT INTO `ownership` VALUES (1490, 1);
INSERT INTO `ownership` VALUES (1491, 1);
INSERT INTO `ownership` VALUES (1492, 1);
INSERT INTO `ownership` VALUES (1493, 1);
INSERT INTO `ownership` VALUES (1494, 1);
INSERT INTO `ownership` VALUES (1495, 1);
INSERT INTO `ownership` VALUES (1496, 1);
INSERT INTO `ownership` VALUES (1497, 1);
INSERT INTO `ownership` VALUES (1498, 1);
INSERT INTO `ownership` VALUES (1499, 1);
INSERT INTO `ownership` VALUES (1500, 1);
INSERT INTO `ownership` VALUES (1501, 1);
INSERT INTO `ownership` VALUES (1502, 1);
INSERT INTO `ownership` VALUES (1503, 1);
INSERT INTO `ownership` VALUES (1504, 1);
INSERT INTO `ownership` VALUES (1505, 1);
INSERT INTO `ownership` VALUES (1506, 1);
INSERT INTO `ownership` VALUES (1507, 1);
INSERT INTO `ownership` VALUES (1508, 1);
INSERT INTO `ownership` VALUES (1509, 1);
INSERT INTO `ownership` VALUES (1510, 1);
INSERT INTO `ownership` VALUES (1511, 1);
INSERT INTO `ownership` VALUES (1512, 1);
INSERT INTO `ownership` VALUES (1513, 1);
INSERT INTO `ownership` VALUES (1514, 1);
INSERT INTO `ownership` VALUES (1515, 1);
INSERT INTO `ownership` VALUES (1516, 1);
INSERT INTO `ownership` VALUES (1517, 1);
INSERT INTO `ownership` VALUES (1518, 1);
INSERT INTO `ownership` VALUES (1519, 1);
INSERT INTO `ownership` VALUES (1520, 1);
INSERT INTO `ownership` VALUES (1521, 1);
INSERT INTO `ownership` VALUES (1522, 1);
INSERT INTO `ownership` VALUES (1523, 1);
INSERT INTO `ownership` VALUES (1524, 1);
INSERT INTO `ownership` VALUES (1525, 1);
INSERT INTO `ownership` VALUES (1526, 1);
INSERT INTO `ownership` VALUES (1527, 1);
INSERT INTO `ownership` VALUES (1528, 1);
INSERT INTO `ownership` VALUES (1529, 1);
INSERT INTO `ownership` VALUES (1530, 1);
INSERT INTO `ownership` VALUES (1531, 1);
INSERT INTO `ownership` VALUES (1532, 1);
INSERT INTO `ownership` VALUES (1533, 1);
INSERT INTO `ownership` VALUES (1534, 1);
INSERT INTO `ownership` VALUES (1535, 1);
INSERT INTO `ownership` VALUES (1536, 1);
INSERT INTO `ownership` VALUES (1537, 1);
INSERT INTO `ownership` VALUES (1538, 1);
INSERT INTO `ownership` VALUES (1539, 1);
INSERT INTO `ownership` VALUES (1540, 1);
INSERT INTO `ownership` VALUES (1541, 1);
INSERT INTO `ownership` VALUES (1542, 1);
INSERT INTO `ownership` VALUES (1543, 1);
INSERT INTO `ownership` VALUES (1544, 1);
INSERT INTO `ownership` VALUES (1545, 1);
INSERT INTO `ownership` VALUES (1546, 1);
INSERT INTO `ownership` VALUES (1547, 1);
INSERT INTO `ownership` VALUES (1548, 1);
INSERT INTO `ownership` VALUES (1549, 1);
INSERT INTO `ownership` VALUES (1550, 1);
INSERT INTO `ownership` VALUES (1551, 1);
INSERT INTO `ownership` VALUES (1552, 1);
INSERT INTO `ownership` VALUES (1553, 1);
INSERT INTO `ownership` VALUES (1554, 1);
INSERT INTO `ownership` VALUES (1555, 1);
INSERT INTO `ownership` VALUES (1556, 1);
INSERT INTO `ownership` VALUES (1557, 1);
INSERT INTO `ownership` VALUES (1558, 1);
INSERT INTO `ownership` VALUES (1559, 1);
INSERT INTO `ownership` VALUES (1560, 1);
INSERT INTO `ownership` VALUES (1561, 1);
INSERT INTO `ownership` VALUES (1562, 1);
INSERT INTO `ownership` VALUES (1563, 1);
INSERT INTO `ownership` VALUES (1564, 1);
INSERT INTO `ownership` VALUES (1565, 1);
INSERT INTO `ownership` VALUES (1566, 1);
INSERT INTO `ownership` VALUES (1567, 1);
INSERT INTO `ownership` VALUES (1568, 1);
INSERT INTO `ownership` VALUES (1569, 1);
INSERT INTO `ownership` VALUES (1570, 1);
INSERT INTO `ownership` VALUES (1571, 1);
INSERT INTO `ownership` VALUES (1572, 1);
INSERT INTO `ownership` VALUES (1573, 1);
INSERT INTO `ownership` VALUES (1574, 1);
INSERT INTO `ownership` VALUES (1575, 1);
INSERT INTO `ownership` VALUES (1576, 1);
INSERT INTO `ownership` VALUES (1577, 1);
INSERT INTO `ownership` VALUES (1578, 1);
INSERT INTO `ownership` VALUES (1579, 1);
INSERT INTO `ownership` VALUES (1580, 1);
INSERT INTO `ownership` VALUES (1581, 1);
INSERT INTO `ownership` VALUES (1582, 1);
INSERT INTO `ownership` VALUES (1583, 1);
INSERT INTO `ownership` VALUES (1584, 1);
INSERT INTO `ownership` VALUES (1585, 1);
INSERT INTO `ownership` VALUES (1586, 1);
INSERT INTO `ownership` VALUES (1587, 1);
INSERT INTO `ownership` VALUES (1588, 1);
INSERT INTO `ownership` VALUES (1589, 1);
INSERT INTO `ownership` VALUES (1590, 1);
INSERT INTO `ownership` VALUES (1591, 1);
INSERT INTO `ownership` VALUES (1592, 1);
INSERT INTO `ownership` VALUES (1593, 1);
INSERT INTO `ownership` VALUES (1594, 1);
INSERT INTO `ownership` VALUES (1595, 1);
INSERT INTO `ownership` VALUES (1596, 1);
INSERT INTO `ownership` VALUES (1597, 1);
INSERT INTO `ownership` VALUES (1598, 1);
INSERT INTO `ownership` VALUES (1599, 1);
INSERT INTO `ownership` VALUES (1600, 1);
INSERT INTO `ownership` VALUES (1601, 1);
INSERT INTO `ownership` VALUES (1602, 1);
INSERT INTO `ownership` VALUES (1603, 1);
INSERT INTO `ownership` VALUES (1604, 1);
INSERT INTO `ownership` VALUES (1605, 1);
INSERT INTO `ownership` VALUES (1606, 1);
INSERT INTO `ownership` VALUES (1607, 1);
INSERT INTO `ownership` VALUES (1608, 1);
INSERT INTO `ownership` VALUES (1609, 1);
INSERT INTO `ownership` VALUES (1610, 1);
INSERT INTO `ownership` VALUES (1611, 1);
INSERT INTO `ownership` VALUES (1612, 1);
INSERT INTO `ownership` VALUES (1613, 1);
INSERT INTO `ownership` VALUES (1614, 1);
INSERT INTO `ownership` VALUES (1615, 1);
INSERT INTO `ownership` VALUES (1616, 1);
INSERT INTO `ownership` VALUES (1617, 1);
INSERT INTO `ownership` VALUES (1618, 1);
INSERT INTO `ownership` VALUES (1619, 1);
INSERT INTO `ownership` VALUES (1620, 1);
INSERT INTO `ownership` VALUES (1621, 1);
INSERT INTO `ownership` VALUES (1622, 1);
INSERT INTO `ownership` VALUES (1623, 1);
INSERT INTO `ownership` VALUES (1624, 1);
INSERT INTO `ownership` VALUES (1625, 1);
INSERT INTO `ownership` VALUES (1626, 1);
INSERT INTO `ownership` VALUES (1627, 1);
INSERT INTO `ownership` VALUES (1628, 1);
INSERT INTO `ownership` VALUES (1629, 1);
INSERT INTO `ownership` VALUES (1630, 1);
INSERT INTO `ownership` VALUES (1631, 1);
INSERT INTO `ownership` VALUES (1632, 1);
INSERT INTO `ownership` VALUES (1633, 1);
INSERT INTO `ownership` VALUES (1634, 1);
INSERT INTO `ownership` VALUES (1635, 1);
INSERT INTO `ownership` VALUES (1636, 1);
INSERT INTO `ownership` VALUES (1637, 1);
INSERT INTO `ownership` VALUES (1638, 1);
INSERT INTO `ownership` VALUES (1639, 1);
INSERT INTO `ownership` VALUES (1640, 1);
INSERT INTO `ownership` VALUES (1641, 1);
INSERT INTO `ownership` VALUES (1642, 1);
INSERT INTO `ownership` VALUES (1643, 1);
INSERT INTO `ownership` VALUES (1644, 1);
INSERT INTO `ownership` VALUES (1645, 1);
INSERT INTO `ownership` VALUES (1646, 1);
INSERT INTO `ownership` VALUES (1647, 1);
INSERT INTO `ownership` VALUES (1648, 1);
INSERT INTO `ownership` VALUES (1649, 1);
INSERT INTO `ownership` VALUES (1650, 1);
INSERT INTO `ownership` VALUES (1651, 1);
INSERT INTO `ownership` VALUES (1652, 1);
INSERT INTO `ownership` VALUES (1653, 1);
INSERT INTO `ownership` VALUES (1654, 1);
INSERT INTO `ownership` VALUES (1655, 1);
INSERT INTO `ownership` VALUES (1656, 1);
INSERT INTO `ownership` VALUES (1657, 1);
INSERT INTO `ownership` VALUES (1658, 1);
INSERT INTO `ownership` VALUES (1659, 1);
INSERT INTO `ownership` VALUES (1660, 1);
INSERT INTO `ownership` VALUES (1661, 1);
INSERT INTO `ownership` VALUES (1662, 1);
INSERT INTO `ownership` VALUES (1663, 1);
INSERT INTO `ownership` VALUES (1664, 1);
INSERT INTO `ownership` VALUES (1665, 1);
INSERT INTO `ownership` VALUES (1666, 1);
INSERT INTO `ownership` VALUES (1667, 1);
INSERT INTO `ownership` VALUES (1668, 1);
INSERT INTO `ownership` VALUES (1669, 1);
INSERT INTO `ownership` VALUES (1670, 1);
INSERT INTO `ownership` VALUES (1671, 1);
INSERT INTO `ownership` VALUES (1672, 1);
INSERT INTO `ownership` VALUES (1673, 1);
INSERT INTO `ownership` VALUES (1674, 1);
INSERT INTO `ownership` VALUES (1675, 1);
INSERT INTO `ownership` VALUES (1676, 1);
INSERT INTO `ownership` VALUES (1677, 1);
INSERT INTO `ownership` VALUES (1678, 1);
INSERT INTO `ownership` VALUES (1679, 1);
INSERT INTO `ownership` VALUES (1680, 1);
INSERT INTO `ownership` VALUES (1681, 1);
INSERT INTO `ownership` VALUES (1682, 1);
INSERT INTO `ownership` VALUES (1683, 1);
INSERT INTO `ownership` VALUES (1684, 1);
INSERT INTO `ownership` VALUES (1685, 1);
INSERT INTO `ownership` VALUES (1686, 1);
INSERT INTO `ownership` VALUES (1687, 1);
INSERT INTO `ownership` VALUES (1688, 1);
INSERT INTO `ownership` VALUES (1689, 1);
INSERT INTO `ownership` VALUES (1690, 1);
INSERT INTO `ownership` VALUES (1691, 1);
INSERT INTO `ownership` VALUES (1692, 1);
INSERT INTO `ownership` VALUES (1693, 1);
INSERT INTO `ownership` VALUES (1694, 1);
INSERT INTO `ownership` VALUES (1695, 1);
INSERT INTO `ownership` VALUES (1696, 1);
INSERT INTO `ownership` VALUES (1697, 1);
INSERT INTO `ownership` VALUES (1698, 1);
INSERT INTO `ownership` VALUES (1699, 1);
INSERT INTO `ownership` VALUES (1700, 1);
INSERT INTO `ownership` VALUES (1701, 1);
INSERT INTO `ownership` VALUES (1702, 1);
INSERT INTO `ownership` VALUES (1703, 1);
INSERT INTO `ownership` VALUES (1704, 1);
INSERT INTO `ownership` VALUES (1705, 1);
INSERT INTO `ownership` VALUES (1706, 1);
INSERT INTO `ownership` VALUES (1707, 1);
INSERT INTO `ownership` VALUES (1708, 1);
INSERT INTO `ownership` VALUES (1709, 1);
INSERT INTO `ownership` VALUES (1710, 1);
INSERT INTO `ownership` VALUES (1711, 1);
INSERT INTO `ownership` VALUES (1712, 1);
INSERT INTO `ownership` VALUES (1713, 1);
INSERT INTO `ownership` VALUES (1714, 1);
INSERT INTO `ownership` VALUES (1715, 1);
INSERT INTO `ownership` VALUES (1806, 1);
INSERT INTO `ownership` VALUES (1807, 1);
INSERT INTO `ownership` VALUES (1808, 1);
INSERT INTO `ownership` VALUES (1809, 1);
INSERT INTO `ownership` VALUES (1810, 1);
INSERT INTO `ownership` VALUES (1811, 1);
INSERT INTO `ownership` VALUES (1812, 1);
INSERT INTO `ownership` VALUES (1813, 1);
INSERT INTO `ownership` VALUES (1814, 1);
INSERT INTO `ownership` VALUES (1815, 1);
INSERT INTO `ownership` VALUES (1816, 1);
INSERT INTO `ownership` VALUES (1817, 1);
INSERT INTO `ownership` VALUES (1818, 1);
INSERT INTO `ownership` VALUES (1819, 1);
INSERT INTO `ownership` VALUES (1820, 1);
INSERT INTO `ownership` VALUES (1821, 1);
INSERT INTO `ownership` VALUES (1822, 1);
INSERT INTO `ownership` VALUES (1823, 1);
INSERT INTO `ownership` VALUES (1824, 1);
INSERT INTO `ownership` VALUES (1825, 1);
INSERT INTO `ownership` VALUES (1826, 1);
INSERT INTO `ownership` VALUES (1827, 1);
INSERT INTO `ownership` VALUES (1828, 1);
INSERT INTO `ownership` VALUES (1829, 1);
INSERT INTO `ownership` VALUES (1830, 1);
INSERT INTO `ownership` VALUES (1831, 1);
INSERT INTO `ownership` VALUES (1832, 1);
INSERT INTO `ownership` VALUES (1833, 1);
INSERT INTO `ownership` VALUES (1834, 1);
INSERT INTO `ownership` VALUES (1835, 1);
INSERT INTO `ownership` VALUES (1836, 1);
INSERT INTO `ownership` VALUES (1837, 1);
INSERT INTO `ownership` VALUES (1838, 1);
INSERT INTO `ownership` VALUES (1839, 1);
INSERT INTO `ownership` VALUES (1840, 1);
INSERT INTO `ownership` VALUES (1841, 1);
INSERT INTO `ownership` VALUES (1842, 1);
INSERT INTO `ownership` VALUES (1843, 1);
INSERT INTO `ownership` VALUES (1844, 1);
INSERT INTO `ownership` VALUES (1845, 1);
INSERT INTO `ownership` VALUES (1846, 1);
INSERT INTO `ownership` VALUES (1847, 1);
INSERT INTO `ownership` VALUES (1848, 1);
INSERT INTO `ownership` VALUES (1849, 1);
INSERT INTO `ownership` VALUES (1850, 1);
INSERT INTO `ownership` VALUES (1851, 1);
INSERT INTO `ownership` VALUES (1852, 1);
INSERT INTO `ownership` VALUES (1853, 1);
INSERT INTO `ownership` VALUES (1854, 1);
INSERT INTO `ownership` VALUES (1855, 1);
INSERT INTO `ownership` VALUES (1856, 1);
INSERT INTO `ownership` VALUES (1857, 1);
INSERT INTO `ownership` VALUES (1858, 1);
INSERT INTO `ownership` VALUES (1859, 1);
INSERT INTO `ownership` VALUES (1860, 1);
INSERT INTO `ownership` VALUES (1861, 1);
INSERT INTO `ownership` VALUES (1862, 1);
INSERT INTO `ownership` VALUES (1863, 1);
INSERT INTO `ownership` VALUES (1864, 1);
INSERT INTO `ownership` VALUES (1865, 1);
INSERT INTO `ownership` VALUES (1866, 1);
INSERT INTO `ownership` VALUES (1867, 1);
INSERT INTO `ownership` VALUES (1868, 1);
INSERT INTO `ownership` VALUES (1869, 1);
INSERT INTO `ownership` VALUES (1870, 1);
INSERT INTO `ownership` VALUES (1871, 1);
INSERT INTO `ownership` VALUES (1872, 1);
INSERT INTO `ownership` VALUES (1873, 1);
INSERT INTO `ownership` VALUES (1874, 1);
INSERT INTO `ownership` VALUES (1875, 1);
INSERT INTO `ownership` VALUES (1876, 1);
INSERT INTO `ownership` VALUES (1877, 1);
INSERT INTO `ownership` VALUES (1878, 1);
INSERT INTO `ownership` VALUES (1879, 1);
INSERT INTO `ownership` VALUES (1880, 1);
INSERT INTO `ownership` VALUES (1881, 1);
INSERT INTO `ownership` VALUES (1882, 1);
INSERT INTO `ownership` VALUES (1883, 1);
INSERT INTO `ownership` VALUES (1884, 1);
INSERT INTO `ownership` VALUES (1885, 1);
INSERT INTO `ownership` VALUES (1886, 1);
INSERT INTO `ownership` VALUES (1887, 1);
INSERT INTO `ownership` VALUES (1888, 1);
INSERT INTO `ownership` VALUES (1889, 1);
INSERT INTO `ownership` VALUES (1890, 1);
INSERT INTO `ownership` VALUES (1891, 1);
INSERT INTO `ownership` VALUES (1892, 1);
INSERT INTO `ownership` VALUES (1893, 1);
INSERT INTO `ownership` VALUES (1894, 1);
INSERT INTO `ownership` VALUES (1895, 1);
INSERT INTO `ownership` VALUES (1896, 1);
INSERT INTO `ownership` VALUES (1897, 1);
INSERT INTO `ownership` VALUES (1898, 1);
INSERT INTO `ownership` VALUES (1899, 1);
INSERT INTO `ownership` VALUES (1900, 1);
INSERT INTO `ownership` VALUES (1901, 1);
INSERT INTO `ownership` VALUES (1902, 1);
INSERT INTO `ownership` VALUES (1903, 1);
INSERT INTO `ownership` VALUES (1904, 1);
INSERT INTO `ownership` VALUES (1905, 1);
INSERT INTO `ownership` VALUES (1906, 1);
INSERT INTO `ownership` VALUES (1907, 1);
INSERT INTO `ownership` VALUES (1908, 1);
INSERT INTO `ownership` VALUES (1909, 1);
INSERT INTO `ownership` VALUES (1910, 1);
INSERT INTO `ownership` VALUES (1911, 1);
INSERT INTO `ownership` VALUES (1912, 1);
INSERT INTO `ownership` VALUES (1913, 1);
INSERT INTO `ownership` VALUES (1914, 1);
INSERT INTO `ownership` VALUES (1915, 1);
INSERT INTO `ownership` VALUES (1916, 1);
INSERT INTO `ownership` VALUES (1917, 1);
INSERT INTO `ownership` VALUES (1918, 1);
INSERT INTO `ownership` VALUES (1919, 1);
INSERT INTO `ownership` VALUES (1920, 1);
INSERT INTO `ownership` VALUES (1921, 1);
INSERT INTO `ownership` VALUES (1922, 1);
INSERT INTO `ownership` VALUES (1923, 1);
INSERT INTO `ownership` VALUES (1924, 1);
INSERT INTO `ownership` VALUES (1925, 1);
INSERT INTO `ownership` VALUES (1926, 1);
INSERT INTO `ownership` VALUES (1927, 1);
INSERT INTO `ownership` VALUES (1928, 1);
INSERT INTO `ownership` VALUES (1929, 1);
INSERT INTO `ownership` VALUES (1930, 1);
INSERT INTO `ownership` VALUES (1931, 1);
INSERT INTO `ownership` VALUES (1932, 1);
INSERT INTO `ownership` VALUES (1933, 1);
INSERT INTO `ownership` VALUES (1934, 1);
INSERT INTO `ownership` VALUES (1935, 1);
INSERT INTO `ownership` VALUES (1936, 1);
INSERT INTO `ownership` VALUES (1937, 1);
INSERT INTO `ownership` VALUES (1938, 1);
INSERT INTO `ownership` VALUES (1939, 1);
INSERT INTO `ownership` VALUES (1940, 1);
INSERT INTO `ownership` VALUES (1941, 1);
INSERT INTO `ownership` VALUES (1942, 1);
INSERT INTO `ownership` VALUES (1943, 1);
INSERT INTO `ownership` VALUES (1944, 1);
INSERT INTO `ownership` VALUES (1945, 1);
INSERT INTO `ownership` VALUES (1946, 1);
INSERT INTO `ownership` VALUES (1947, 1);
INSERT INTO `ownership` VALUES (1948, 1);
INSERT INTO `ownership` VALUES (1949, 1);
INSERT INTO `ownership` VALUES (1950, 1);
INSERT INTO `ownership` VALUES (1951, 1);
INSERT INTO `ownership` VALUES (1952, 1);
INSERT INTO `ownership` VALUES (1953, 1);
INSERT INTO `ownership` VALUES (1954, 1);
INSERT INTO `ownership` VALUES (1955, 1);
INSERT INTO `ownership` VALUES (1956, 1);
INSERT INTO `ownership` VALUES (1957, 1);
INSERT INTO `ownership` VALUES (1958, 1);
INSERT INTO `ownership` VALUES (1959, 1);
INSERT INTO `ownership` VALUES (1960, 1);
INSERT INTO `ownership` VALUES (1961, 1);
INSERT INTO `ownership` VALUES (1962, 1);
INSERT INTO `ownership` VALUES (1963, 1);
INSERT INTO `ownership` VALUES (1964, 1);
INSERT INTO `ownership` VALUES (1965, 1);
INSERT INTO `ownership` VALUES (1966, 1);
INSERT INTO `ownership` VALUES (1967, 1);
INSERT INTO `ownership` VALUES (1968, 1);
INSERT INTO `ownership` VALUES (1969, 1);
INSERT INTO `ownership` VALUES (1970, 1);
INSERT INTO `ownership` VALUES (1971, 1);
INSERT INTO `ownership` VALUES (1972, 1);
INSERT INTO `ownership` VALUES (1973, 1);
INSERT INTO `ownership` VALUES (1974, 1);
INSERT INTO `ownership` VALUES (1975, 1);
INSERT INTO `ownership` VALUES (1976, 1);
INSERT INTO `ownership` VALUES (1977, 1);
INSERT INTO `ownership` VALUES (1978, 1);
INSERT INTO `ownership` VALUES (1979, 1);
INSERT INTO `ownership` VALUES (1980, 1);
INSERT INTO `ownership` VALUES (1981, 1);
INSERT INTO `ownership` VALUES (1982, 1);
INSERT INTO `ownership` VALUES (1983, 1);
INSERT INTO `ownership` VALUES (1984, 1);
INSERT INTO `ownership` VALUES (1985, 1);
INSERT INTO `ownership` VALUES (1986, 1);
INSERT INTO `ownership` VALUES (1987, 1);
INSERT INTO `ownership` VALUES (1988, 1);
INSERT INTO `ownership` VALUES (1989, 1);
INSERT INTO `ownership` VALUES (1990, 1);
INSERT INTO `ownership` VALUES (1991, 1);
INSERT INTO `ownership` VALUES (1992, 1);
INSERT INTO `ownership` VALUES (1993, 1);
INSERT INTO `ownership` VALUES (1994, 1);
INSERT INTO `ownership` VALUES (1995, 1);
INSERT INTO `ownership` VALUES (1996, 1);
INSERT INTO `ownership` VALUES (1997, 1);
INSERT INTO `ownership` VALUES (1998, 1);
INSERT INTO `ownership` VALUES (1999, 1);
INSERT INTO `ownership` VALUES (2000, 1);
INSERT INTO `ownership` VALUES (2001, 1);
INSERT INTO `ownership` VALUES (2002, 1);
INSERT INTO `ownership` VALUES (2003, 1);
INSERT INTO `ownership` VALUES (2004, 1);
INSERT INTO `ownership` VALUES (2005, 1);
INSERT INTO `ownership` VALUES (2006, 1);
INSERT INTO `ownership` VALUES (2007, 1);
INSERT INTO `ownership` VALUES (2008, 1);
INSERT INTO `ownership` VALUES (2009, 1);
INSERT INTO `ownership` VALUES (2010, 1);
INSERT INTO `ownership` VALUES (2011, 1);
INSERT INTO `ownership` VALUES (2012, 1);
INSERT INTO `ownership` VALUES (2013, 1);
INSERT INTO `ownership` VALUES (2014, 1);
INSERT INTO `ownership` VALUES (2015, 1);
INSERT INTO `ownership` VALUES (2016, 1);
INSERT INTO `ownership` VALUES (2017, 1);
INSERT INTO `ownership` VALUES (2018, 1);
INSERT INTO `ownership` VALUES (2019, 1);
INSERT INTO `ownership` VALUES (2020, 1);
INSERT INTO `ownership` VALUES (2021, 1);
INSERT INTO `ownership` VALUES (2022, 1);
INSERT INTO `ownership` VALUES (2023, 1);
INSERT INTO `ownership` VALUES (2024, 1);
INSERT INTO `ownership` VALUES (2025, 1);
INSERT INTO `ownership` VALUES (2026, 1);
INSERT INTO `ownership` VALUES (2027, 1);
INSERT INTO `ownership` VALUES (2028, 1);
INSERT INTO `ownership` VALUES (2029, 1);
INSERT INTO `ownership` VALUES (2030, 1);
INSERT INTO `ownership` VALUES (2031, 1);
INSERT INTO `ownership` VALUES (2032, 1);
INSERT INTO `ownership` VALUES (2033, 1);
INSERT INTO `ownership` VALUES (2034, 1);
INSERT INTO `ownership` VALUES (2035, 1);
INSERT INTO `ownership` VALUES (2036, 1);
INSERT INTO `ownership` VALUES (2037, 1);
INSERT INTO `ownership` VALUES (2038, 1);
INSERT INTO `ownership` VALUES (2039, 1);
INSERT INTO `ownership` VALUES (2040, 1);
INSERT INTO `ownership` VALUES (2041, 1);
INSERT INTO `ownership` VALUES (2042, 1);
INSERT INTO `ownership` VALUES (2043, 1);
INSERT INTO `ownership` VALUES (2044, 1);
INSERT INTO `ownership` VALUES (2045, 1);
INSERT INTO `ownership` VALUES (2046, 1);
INSERT INTO `ownership` VALUES (2047, 1);
INSERT INTO `ownership` VALUES (2048, 1);
INSERT INTO `ownership` VALUES (2049, 1);
INSERT INTO `ownership` VALUES (2050, 1);
INSERT INTO `ownership` VALUES (2051, 1);
INSERT INTO `ownership` VALUES (2052, 1);
INSERT INTO `ownership` VALUES (2053, 1);
INSERT INTO `ownership` VALUES (2054, 1);
INSERT INTO `ownership` VALUES (2055, 1);
INSERT INTO `ownership` VALUES (2056, 1);
INSERT INTO `ownership` VALUES (2057, 1);
INSERT INTO `ownership` VALUES (2058, 1);
INSERT INTO `ownership` VALUES (2059, 1);
INSERT INTO `ownership` VALUES (2060, 1);
INSERT INTO `ownership` VALUES (2061, 1);
INSERT INTO `ownership` VALUES (2062, 1);
INSERT INTO `ownership` VALUES (2063, 1);
INSERT INTO `ownership` VALUES (2064, 1);
INSERT INTO `ownership` VALUES (2065, 1);
INSERT INTO `ownership` VALUES (2066, 1);
INSERT INTO `ownership` VALUES (2067, 1);
INSERT INTO `ownership` VALUES (2068, 1);
INSERT INTO `ownership` VALUES (2069, 1);
INSERT INTO `ownership` VALUES (2070, 1);
INSERT INTO `ownership` VALUES (2071, 1);
INSERT INTO `ownership` VALUES (2072, 1);
INSERT INTO `ownership` VALUES (2073, 1);
INSERT INTO `ownership` VALUES (2074, 1);
INSERT INTO `ownership` VALUES (2075, 1);
INSERT INTO `ownership` VALUES (2076, 1);
INSERT INTO `ownership` VALUES (2077, 1);
INSERT INTO `ownership` VALUES (2078, 1);
INSERT INTO `ownership` VALUES (2079, 1);
INSERT INTO `ownership` VALUES (2080, 1);
INSERT INTO `ownership` VALUES (2081, 1);
INSERT INTO `ownership` VALUES (2082, 1);
INSERT INTO `ownership` VALUES (2083, 1);
INSERT INTO `ownership` VALUES (2084, 1);
INSERT INTO `ownership` VALUES (2085, 1);
INSERT INTO `ownership` VALUES (2086, 1);
INSERT INTO `ownership` VALUES (2087, 1);
INSERT INTO `ownership` VALUES (2088, 1);
INSERT INTO `ownership` VALUES (2089, 1);
INSERT INTO `ownership` VALUES (2090, 1);
INSERT INTO `ownership` VALUES (2091, 1);
INSERT INTO `ownership` VALUES (2092, 1);
INSERT INTO `ownership` VALUES (2093, 1);
INSERT INTO `ownership` VALUES (2094, 1);
INSERT INTO `ownership` VALUES (8001, 1);
INSERT INTO `ownership` VALUES (8002, 1);
INSERT INTO `ownership` VALUES (8003, 1);
INSERT INTO `ownership` VALUES (8004, 1);
INSERT INTO `ownership` VALUES (8005, 1);
INSERT INTO `ownership` VALUES (8006, 1);
INSERT INTO `ownership` VALUES (8007, 1);
INSERT INTO `ownership` VALUES (8008, 1);
INSERT INTO `ownership` VALUES (8009, 1);
INSERT INTO `ownership` VALUES (8010, 1);
INSERT INTO `ownership` VALUES (8011, 1);
INSERT INTO `ownership` VALUES (8012, 1);
INSERT INTO `ownership` VALUES (8013, 1);
INSERT INTO `ownership` VALUES (8014, 1);
INSERT INTO `ownership` VALUES (8015, 1);
INSERT INTO `ownership` VALUES (8016, 1);
INSERT INTO `ownership` VALUES (8017, 1);
INSERT INTO `ownership` VALUES (8018, 1);
INSERT INTO `ownership` VALUES (8019, 1);
INSERT INTO `ownership` VALUES (8020, 1);
INSERT INTO `ownership` VALUES (8021, 1);
INSERT INTO `ownership` VALUES (8022, 1);
INSERT INTO `ownership` VALUES (8023, 1);
INSERT INTO `ownership` VALUES (8024, 1);
INSERT INTO `ownership` VALUES (8025, 1);
INSERT INTO `ownership` VALUES (8026, 1);
INSERT INTO `ownership` VALUES (8027, 1);
INSERT INTO `ownership` VALUES (8028, 1);
INSERT INTO `ownership` VALUES (8029, 1);
INSERT INTO `ownership` VALUES (8030, 1);
INSERT INTO `ownership` VALUES (8031, 1);
INSERT INTO `ownership` VALUES (8032, 1);
INSERT INTO `ownership` VALUES (8033, 1);
INSERT INTO `ownership` VALUES (8034, 1);
INSERT INTO `ownership` VALUES (8035, 1);
INSERT INTO `ownership` VALUES (8036, 1);
INSERT INTO `ownership` VALUES (8037, 1);
INSERT INTO `ownership` VALUES (8038, 1);
INSERT INTO `ownership` VALUES (8039, 1);
INSERT INTO `ownership` VALUES (8040, 1);
INSERT INTO `ownership` VALUES (8041, 1);
INSERT INTO `ownership` VALUES (8042, 1);
INSERT INTO `ownership` VALUES (8043, 1);
INSERT INTO `ownership` VALUES (8044, 1);
INSERT INTO `ownership` VALUES (8045, 1);
INSERT INTO `ownership` VALUES (8046, 1);
INSERT INTO `ownership` VALUES (8047, 1);
INSERT INTO `ownership` VALUES (8048, 1);
INSERT INTO `ownership` VALUES (8049, 1);
INSERT INTO `ownership` VALUES (8050, 1);
INSERT INTO `ownership` VALUES (8051, 1);
INSERT INTO `ownership` VALUES (8052, 1);
INSERT INTO `ownership` VALUES (8053, 1);
INSERT INTO `ownership` VALUES (8054, 1);
INSERT INTO `ownership` VALUES (8055, 1);
INSERT INTO `ownership` VALUES (8056, 1);
INSERT INTO `ownership` VALUES (8057, 1);
INSERT INTO `ownership` VALUES (8058, 1);
INSERT INTO `ownership` VALUES (8059, 1);
INSERT INTO `ownership` VALUES (8060, 1);
INSERT INTO `ownership` VALUES (8061, 1);
INSERT INTO `ownership` VALUES (8062, 1);
INSERT INTO `ownership` VALUES (8063, 1);
INSERT INTO `ownership` VALUES (8064, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `patient`
-- 

DROP TABLE IF EXISTS `patient`;
CREATE TABLE `patient` (
  `person_id` int(11) NOT NULL default '0',
  `is_default_provider_primary` int(11) NOT NULL default '0',
  `default_provider` int(11) NOT NULL default '0',
  `record_number` int(11) NOT NULL default '0',
  `employer_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`person_id`),
  KEY `record_number` (`record_number`)
) TYPE=MyISAM COMMENT='An patient extends the person entity';

-- 
-- Dumping data for table `patient`
-- 

INSERT INTO `patient` VALUES (1110, 0, 0, 12, 'smith jones co');
INSERT INTO `patient` VALUES (1120, 0, 0, 13, '');
INSERT INTO `patient` VALUES (1707, 0, 0, 14, '');
INSERT INTO `patient` VALUES (1711, 0, 0, 15, '');

-- --------------------------------------------------------

-- 
-- Table structure for table `patient_statistics`
-- 

DROP TABLE IF EXISTS `patient_statistics`;
CREATE TABLE `patient_statistics` (
  `person_id` int(11) NOT NULL default '0',
  `ethnicity` int(11) NOT NULL default '0',
  `race` int(11) NOT NULL default '0',
  `income` int(11) NOT NULL default '0',
  `language` int(11) NOT NULL default '0',
  `migrant` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `patient_statistics`
-- 

INSERT INTO `patient_statistics` VALUES (8001, 1, 0, 0, 0, 0);
INSERT INTO `patient_statistics` VALUES (8002, 1, 0, 0, 0, 0);
INSERT INTO `patient_statistics` VALUES (8003, 1, 0, 0, 0, 0);
INSERT INTO `patient_statistics` VALUES (8004, 1, 0, 0, 0, 0);
INSERT INTO `patient_statistics` VALUES (8005, 1, 0, 0, 0, 0);
INSERT INTO `patient_statistics` VALUES (8006, 1, 0, 0, 0, 0);
INSERT INTO `patient_statistics` VALUES (8007, 1, 0, 0, 0, 0);
INSERT INTO `patient_statistics` VALUES (8008, 1, 0, 0, 0, 0);
INSERT INTO `patient_statistics` VALUES (8009, 1, 0, 0, 0, 0);
INSERT INTO `patient_statistics` VALUES (8010, 1, 0, 0, 0, 0);
INSERT INTO `patient_statistics` VALUES (8011, 1, 0, 0, 0, 0);
INSERT INTO `patient_statistics` VALUES (8013, 1, 0, 0, 0, 0);
INSERT INTO `patient_statistics` VALUES (8014, 1, 0, 0, 0, 0);
INSERT INTO `patient_statistics` VALUES (8018, 1, 0, 0, 0, 0);
INSERT INTO `patient_statistics` VALUES (8022, 1, 0, 0, 0, 0);
INSERT INTO `patient_statistics` VALUES (8026, 1, 0, 0, 0, 0);
INSERT INTO `patient_statistics` VALUES (8030, 1, 0, 0, 0, 0);
INSERT INTO `patient_statistics` VALUES (8034, 1, 0, 0, 0, 0);
INSERT INTO `patient_statistics` VALUES (8038, 1, 0, 0, 0, 0);
INSERT INTO `patient_statistics` VALUES (8043, 1, 0, 0, 0, 0);
INSERT INTO `patient_statistics` VALUES (8046, 1, 0, 0, 0, 0);
INSERT INTO `patient_statistics` VALUES (8048, 1, 0, 0, 0, 0);
INSERT INTO `patient_statistics` VALUES (8050, 1, 0, 0, 0, 0);
INSERT INTO `patient_statistics` VALUES (8051, 1, 0, 0, 0, 0);
INSERT INTO `patient_statistics` VALUES (8052, 1, 0, 0, 0, 0);
INSERT INTO `patient_statistics` VALUES (8053, 1, 0, 0, 0, 0);
INSERT INTO `patient_statistics` VALUES (8054, 1, 0, 0, 0, 0);
INSERT INTO `patient_statistics` VALUES (8064, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `payment`
-- 

DROP TABLE IF EXISTS `payment`;
CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL default '0',
  `foreign_id` int(11) NOT NULL default '0',
  `payment_type` int(11) NOT NULL default '0',
  `amount` float(11,2) NOT NULL default '0.00',
  `user_id` int(11) NOT NULL default '0',
  `timestamp` int(11) NOT NULL default '0',
  PRIMARY KEY  (`payment_id`),
  KEY `foreign_id` (`foreign_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `payment`
-- 

INSERT INTO `payment` VALUES (6001, 2093, 1, 12.00, 0, 0);
INSERT INTO `payment` VALUES (8062, 8061, 0, 14.00, 0, 0);

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
) TYPE=MyISAM COMMENT='A person in the system';

-- 
-- Dumping data for table `person`
-- 

INSERT INTO `person` VALUES (1110, '', 'Conrad', 'Joe', '', 0, '', '0000-00-00', '', '', 'pediatric specialist', 'jconorad@example.com', '', '', '112-23-2321', 1);
INSERT INTO `person` VALUES (1120, '', 'Minton', 'Michelle', '', 0, '', '0000-00-00', '', '', '', '', '', '', '234-44-4543', 1);
INSERT INTO `person` VALUES (1707, '', 'Jones', 'Nancy', '', 2, '', '1955-07-16', '', '', '', '', '', '', '123-34-3432', 1);
INSERT INTO `person` VALUES (1711, '', 'smith-jones', 'nancy', '', 2, '', '0000-00-00', '', '', '', '', '', '', '123-32-2323', 1);
INSERT INTO `person` VALUES (8039, '', 'Person', 'Random', '', 0, '', '0000-00-00', '', '', '', '', '', '', '', 0);

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
) TYPE=MyISAM COMMENT='Links a person to a address specifying the address type';

-- 
-- Dumping data for table `person_address`
-- 

INSERT INTO `person_address` VALUES (1110, 8012, 1);
INSERT INTO `person_address` VALUES (8015, 8016, 0);
INSERT INTO `person_address` VALUES (8019, 8020, 0);
INSERT INTO `person_address` VALUES (8023, 8024, 0);
INSERT INTO `person_address` VALUES (8027, 8028, 0);
INSERT INTO `person_address` VALUES (8031, 8032, 0);
INSERT INTO `person_address` VALUES (8035, 8036, 0);
INSERT INTO `person_address` VALUES (8039, 8040, 0);

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
) TYPE=MyISAM COMMENT='Links a person to a company and optionaly specifies the lin';

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
) TYPE=MyISAM COMMENT='Links between people and phone_numbers';

-- 
-- Dumping data for table `person_number`
-- 

INSERT INTO `person_number` VALUES (1110, 2056);
INSERT INTO `person_number` VALUES (1707, 1709);
INSERT INTO `person_number` VALUES (8015, 8017);
INSERT INTO `person_number` VALUES (8019, 8021);
INSERT INTO `person_number` VALUES (8023, 8025);
INSERT INTO `person_number` VALUES (8027, 8029);
INSERT INTO `person_number` VALUES (8031, 8033);
INSERT INTO `person_number` VALUES (8035, 8037);
INSERT INTO `person_number` VALUES (8039, 8041);

-- --------------------------------------------------------

-- 
-- Table structure for table `person_person`
-- 

DROP TABLE IF EXISTS `person_person`;
CREATE TABLE `person_person` (
  `person_person_id` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  `related_person_id` int(11) NOT NULL default '0',
  `relation_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_person_id`),
  UNIQUE KEY `person_id` (`person_id`,`related_person_id`,`relation_type`)
) TYPE=MyISAM;

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
) TYPE=MyISAM COMMENT='Link to specify person type';

-- 
-- Dumping data for table `person_type`
-- 

INSERT INTO `person_type` VALUES (1110, 2);
INSERT INTO `person_type` VALUES (1120, 2);
INSERT INTO `person_type` VALUES (8015, 0);
INSERT INTO `person_type` VALUES (8019, 0);
INSERT INTO `person_type` VALUES (8023, 0);
INSERT INTO `person_type` VALUES (8027, 0);
INSERT INTO `person_type` VALUES (8031, 5);
INSERT INTO `person_type` VALUES (8035, 5);
INSERT INTO `person_type` VALUES (8039, 5);

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
) TYPE=MyISAM COMMENT='Links a practice to a address specifying the address type';

-- 
-- Dumping data for table `practice_address`
-- 


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

INSERT INTO `practices` VALUES (1122, 'Medical Practice Inc.', '');

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
INSERT INTO `provider` VALUES (1110, '1233323J', '', '22342242');
INSERT INTO `provider` VALUES (1120, '', '', '');

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

INSERT INTO `provider_to_insurance` VALUES (1119, 1110, 1114, '2456633455', 1, '234BB');

-- --------------------------------------------------------

-- 
-- Table structure for table `record_sequence`
-- 

DROP TABLE IF EXISTS `record_sequence`;
CREATE TABLE `record_sequence` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `record_sequence`
-- 

INSERT INTO `record_sequence` VALUES (15);

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
) TYPE=MyISAM COMMENT='Report templates';

-- 
-- Dumping data for table `report_templates`
-- 

INSERT INTO `report_templates` VALUES (9, 8, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (10, 8, 'List View', 'no');
INSERT INTO `report_templates` VALUES (11, 10, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (792, 791, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1716, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1717, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1718, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1719, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1720, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1721, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1722, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1723, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1724, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1725, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1726, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1727, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1728, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1729, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1730, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1731, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1732, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1733, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1734, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1735, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1736, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1737, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1738, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1739, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1740, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1741, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1742, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1743, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1744, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1745, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1746, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1747, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1748, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1749, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1750, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1751, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1752, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1753, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1754, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1755, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1756, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1757, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1758, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1759, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1760, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1761, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1762, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1763, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1764, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1765, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1766, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1767, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1768, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1769, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1770, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1771, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1772, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1773, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1774, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1775, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1776, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1777, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1778, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1779, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1780, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1781, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1782, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1783, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1784, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1785, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1786, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1787, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1788, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1789, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1790, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1791, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1792, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1793, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1794, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1795, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1796, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1797, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1798, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1799, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1800, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1801, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1802, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1803, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1804, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1805, 0, 'Default Template', 'yes');

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
) TYPE=MyISAM COMMENT='Report definitions TODO: change to Generic Seq' AUTO_INCREMENT=792 ;

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

INSERT INTO `rooms` VALUES (1125, '', 15, 1123, 'Exam 1');
INSERT INTO `rooms` VALUES (1126, '', 1, 1123, 'XRAY');

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
  `room_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `schedules`
-- 

INSERT INTO `schedules` VALUES (126, 'PS', 'Dr Smiths Schedule', '', '', 2, 984, 0);
INSERT INTO `schedules` VALUES (501, 'PS', 'Provider 2', '', '', 2, 306, 0);
INSERT INTO `schedules` VALUES (640, 'PS', 'Jnelson''s Office hours', '', '', 2, 984, 0);
INSERT INTO `schedules` VALUES (662, 'PS', 'Test Schedule', '', '', 2, 306, 0);
INSERT INTO `schedules` VALUES (1009, 'PS', 'Test Schedule', '', 'testing 123', 2, 984, 0);
INSERT INTO `schedules` VALUES (1011, 'PS', 'XRay Schedule', '', '', 2, 0, 14);
INSERT INTO `schedules` VALUES (1127, 'PS', 'Michelles Schedule', '', '', 1122, 1121, 0);
INSERT INTO `schedules` VALUES (1561, 'PS', 'Joes Schedule', '', '', 1122, 1111, 0);
INSERT INTO `schedules` VALUES (1806, 'PS', 'Exam 1', '', '', 1122, 0, 1125);

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

INSERT INTO `sequences` VALUES (8064);

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
) TYPE=MyISAM COMMENT='Generic way to store date values';

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
INSERT INTO `storage_date` VALUES (1010, 'test_data', '2005-03-09');
INSERT INTO `storage_date` VALUES (2057, 'test_data', '2005-03-15');

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
) TYPE=MyISAM COMMENT='Generic way to store integer values (also boolean)';

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
) TYPE=MyISAM COMMENT='Generic way to string values';

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
INSERT INTO `storage_string` VALUES (1010, 'test_string', 'test this');
INSERT INTO `storage_string` VALUES (1072, 'email', '');
INSERT INTO `storage_string` VALUES (1113, 'email', '');
INSERT INTO `storage_string` VALUES (2049, 'email', '');
INSERT INTO `storage_string` VALUES (2057, 'test_string', 'Yep a test');

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
  PRIMARY KEY  (`superbill_data_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `superbill_data`
-- 

INSERT INTO `superbill_data` VALUES (998, 1, 0, 1);
INSERT INTO `superbill_data` VALUES (999, 1, 0, 1);
INSERT INTO `superbill_data` VALUES (1000, 1, 0, 1);
INSERT INTO `superbill_data` VALUES (1001, 1, 0, 1);
INSERT INTO `superbill_data` VALUES (1002, 1, 0, 1);
INSERT INTO `superbill_data` VALUES (1003, 1, 0, 1);
INSERT INTO `superbill_data` VALUES (1004, 1, 0, 1);
INSERT INTO `superbill_data` VALUES (1005, 1, 26761, 1);
INSERT INTO `superbill_data` VALUES (1006, 1, 26752, 1);
INSERT INTO `superbill_data` VALUES (1007, 1, 26751, 1);
INSERT INTO `superbill_data` VALUES (1008, 1, 26758, 1);

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
) TYPE=MyISAM COMMENT='Users in the System';

-- 
-- Dumping data for table `user`
-- 

INSERT INTO `user` VALUES (1, 'admin', 'admin', '', '', NULL, 'no', 0);
INSERT INTO `user` VALUES (984, 'jeichorn', 'test', 'jei', '336666', 983, '', 0);
INSERT INTO `user` VALUES (1111, 'jconrad', 'demo', 'jac', 'FF9966', 1110, 'no', 0);
INSERT INTO `user` VALUES (1121, 'mminton', 'demo', 'mm', '99CCCC', 1120, 'no', 0);

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

COMMIT;
