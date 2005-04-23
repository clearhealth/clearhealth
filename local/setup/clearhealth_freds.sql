-- phpMyAdmin SQL Dump
-- version 2.6.1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Apr 23, 2005 at 11:20 AM
-- Server version: 4.1.10
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='An address that can be for a company or a person. STARTEMPTY';

-- 
-- Dumping data for table `address`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `adodbseq`
-- 

CREATE TABLE `adodbseq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='STARTWITHDATA';

-- 
-- Dumping data for table `adodbseq`
-- 

INSERT INTO `adodbseq` VALUES (1);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Links a building to a address specifying type. STARTEMPTY';

-- 
-- Dumping data for table `building_address`
-- 


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='STARTEMPTY';

-- 
-- Dumping data for table `buildings`
-- 


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='STARTWITHDATA';

-- 
-- Dumping data for table `category`
-- 

INSERT INTO `category` VALUES (1, 'ClearHealth', '', 0, 0, 6);

-- --------------------------------------------------------

-- 
-- Table structure for table `category_to_document`
-- 

CREATE TABLE `category_to_document` (
  `category_id` int(11) NOT NULL default '0',
  `document_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`category_id`,`document_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='STARTEMPTY';

-- 
-- Dumping data for table `category_to_document`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `clearhealth_claim`
-- 

CREATE TABLE `clearhealth_claim` (
  `claim_id` int(11) NOT NULL default '0',
  `encounter_id` int(11) NOT NULL default '0',
  `identifier` varchar(255) NOT NULL default '',
  `total_billed` float(7,2) NOT NULL default '0.00',
  `total_paid` float(7,2) NOT NULL default '0.00',
  PRIMARY KEY  (`claim_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='STARTEMPTY';

-- 
-- Dumping data for table `clearhealth_claim`
-- 


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='CPT code database STARTEMPTY';

-- 
-- Dumping data for table `codes`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `coding_data`
-- 

CREATE TABLE `coding_data` (
  `coding_data_id` int(11) NOT NULL default '0',
  `foreign_id` int(11) NOT NULL default '0',
  `parent_id` int(11) NOT NULL default '0',
  `code_id` int(11) NOT NULL default '0',
  `modifier` int(11) NOT NULL default '0',
  `units` float(5,2) NOT NULL default '1.00',
  `fee` float(11,2) NOT NULL default '0.00',
  `primary_code` tinyint(4) NOT NULL default '0',
  `code_order` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`coding_data_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='STARTEMPTY';

-- 
-- Dumping data for table `coding_data`
-- 


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Base Company record most of the data is linked in STARTEMPTY';

-- 
-- Dumping data for table `company`
-- 


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Links a company to a address specifying the type STARTEMPTY';

-- 
-- Dumping data for table `company_address`
-- 


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Relates a company to another company STARTEMPTY';

-- 
-- Dumping data for table `company_company`
-- 


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Links between company and phone_numbers STARTEMPTY';

-- 
-- Dumping data for table `company_number`
-- 


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Link to specify company type';

-- 
-- Dumping data for table `company_type`
-- 


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
  `revision` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `foreign_id` int(11) default NULL,
  `group_id` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `revision` (`revision`),
  KEY `foreign_id` (`foreign_id`),
  KEY `owner` (`owner`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `document`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `encounter`
-- 

CREATE TABLE `encounter` (
  `encounter_id` int(11) NOT NULL default '0',
  `encounter_reason` int(11) NOT NULL default '0',
  `patient_id` int(11) NOT NULL default '0',
  `building_id` int(11) NOT NULL default '0',
  `date_of_treatment` datetime NOT NULL default '0000-00-00 00:00:00',
  `treating_person_id` int(11) NOT NULL default '0',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `last_change_user_id` int(11) NOT NULL default '0',
  `status` enum('closed','open','billed') NOT NULL default 'open',
  `occurence_id` int(11) default NULL,
  PRIMARY KEY  (`encounter_id`),
  KEY `building_id` (`building_id`),
  KEY `treating_person_id` (`treating_person_id`),
  KEY `last_change_user_id` (`last_change_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `encounter`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `encounter_date`
-- 

CREATE TABLE `encounter_date` (
  `encounter_date_id` int(11) NOT NULL default '0',
  `encounter_id` int(11) NOT NULL default '0',
  `date_type` int(11) NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`encounter_date_id`),
  KEY `encounter_id` (`encounter_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `encounter_date`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `encounter_person`
-- 

CREATE TABLE `encounter_person` (
  `encounter_person_id` int(11) NOT NULL default '0',
  `encounter_id` int(11) NOT NULL default '0',
  `person_type` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`encounter_person_id`),
  KEY `encounter_id` (`encounter_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `encounter_person`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `encounter_value`
-- 

CREATE TABLE `encounter_value` (
  `encounter_value_id` int(11) NOT NULL default '0',
  `encounter_id` int(11) NOT NULL default '0',
  `value_type` int(11) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '0',
  PRIMARY KEY  (`encounter_value_id`),
  KEY `encounter_id` (`encounter_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `encounter_value`
-- 


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
  `state` enum('AL','AK','AZ','AR','CA','CO','CT','DE','DC','FL','GA','HI','ID','IL','IN','IA','KS','KY','LA','ME','MD','MA','MI','MN','MS','MO','MT','NE','NV','NH','NJ','NM','NY','NC','ND','OH','OK','OR','PA','RI','SC','SD','TN','TX','UT','VT','VA','WA','WV','WI','WY','PR') NOT NULL default 'AL',
  `group_list` enum('All','Arizona','California') NOT NULL default 'All',
  `company_type` enum('Insurance') NOT NULL default 'Insurance',
  `assigning` enum('A - Assigned','B - Assigned Lab Services Only','C - Not Assigned','P - Assignment Refused') NOT NULL default 'A - Assigned',
  `relation_of_information_code` enum('A - On file','I - Informed Consent','M - Limited Ability','N - Not allowed','O - On file','Y - Has permission') NOT NULL default 'A - On file',
  `person_type` enum('Patient','Provider','Mid-level','Staff','Subscriber') NOT NULL default 'Patient',
  `provider_number_type` enum('State License') NOT NULL default 'State License',
  `subscriber_to_patient_relationship` enum('Self','Parent','Spouse','Other') NOT NULL default 'Self' COMMENT '\0\0\0\0\0\0\0\0\0\0\0!\0\0�',
  `person_to_person_relation_type` enum('Dependant','Spouse','Grand Parent','Other') NOT NULL default 'Dependant',
  `identifier_type` enum('SSN','EIN') NOT NULL default 'SSN',
  `code_modifier` enum('A0','A1','A2','B1','B2','C6') NOT NULL default 'A0',
  `encounter_reason` enum('Physical','Other') NOT NULL default 'Physical',
  `encounter_date_type` enum('Initial Visit Date','Update me please') NOT NULL default 'Initial Visit Date',
  `encounter_person_type` enum('blah') NOT NULL default 'blah',
  `payment_type` enum('visa','mastercard','amex','check','cash','remittance') NOT NULL default 'visa',
  `marital_status` enum('Single','Married','Other') NOT NULL default 'Single',
  `language` enum('English','Spanish','Chinese','Japanese','Korean','Portuguese','Russian','Sign Language','Vietnamese','Tagalog','Punjabi','Hindustani','Armenian','Arabic','Laotian','Hmong','Cambodian','Other') NOT NULL default 'English',
  `ethnicity` enum('Hispanic','Caucasian') NOT NULL default 'Hispanic',
  `race` enum('White/Hispanic','Black','Native American/Alaskan Native','Asian/Pacific Islander','Other/Unknown') NOT NULL default 'White/Hispanic',
  `migrant_status` enum('Seasonal Agricultural/Migrant Worker') NOT NULL default 'Seasonal Agricultural/Migrant Worker',
  `appointment_reasons` enum('Physical','FP','CDP','CHDP','F/U','Sick','Lab Only') NOT NULL default 'Physical',
  `address_type` enum('Home','Billing','Other','Main','Secondary') NOT NULL default 'Home',
  `income` enum('Unknown','Under 100% of Poverty','100-200% of Poverty','Above 200% of Poverty') NOT NULL default 'Unknown' COMMENT '\0\0\0\0\0\0\0\0\0\0\0!\0\0�',
  `number_type` enum('Home','Mobile','Work','Emergency') NOT NULL default 'Home' COMMENT '\0\0\0\0\0\0\0\0\0\0\0!\0\0�',
  `payer_type` enum('medicare','champus','medical','private','feca','medicaid','champusva','otherhcfa','litigation') NOT NULL default 'medicare',
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='enums stored as new col, metadata 1 row perenumSTARTWITHDATA';

-- 
-- Dumping data for table `enumeration`
-- 

INSERT INTO `enumeration` VALUES ('gender', 'Gender', '', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('person_type', 'Person Type', '', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('company_type', 'Company Type', '', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('state', 'State', '', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('number_type', 'Phone Number Type', '', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('company_number_type', 'Company Number Type', '', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('address_type', 'Address Type', '', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('disposition', 'Disposition', '', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('quality_of_file', 'Quality of File', '', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('group_list', 'File Groups', '', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('identifier_type', 'Identifier Type', '', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('assigning', 'Assigning', '', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('relation_of_information_code', '', '', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('provider_number_type', 'Provider Number Type', '', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('subscriber_to_patient', 'Subscriber to patient', '', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('payer_type', 'Payer Type', '', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('person_to_person_relation_type', 'Person to person relation type', '', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('code_modifier', 'Code Modifier', 'Modifiers available for codes.', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('encounter_reason', 'Encounter Reason', 'Reasons for an encounter', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('encounter_date_type', 'Encounter Date Type', 'Types for extra dates attached to an encounter', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('encounter_person_type', 'Encounter Person Type', '', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('payment_type', 'Payment Type', 'Types of payments', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('ethnicity', 'Ethnicity', '', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('marital_status', 'Marital Status', '', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('language', 'Languages', '', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('race', 'Race', '', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('income', 'Income', '', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('migrant status', 'Migrant Status', '', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('migrant_status', 'Migrant Status', '', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('appointment_reason', 'Appointment Reason', '', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');
INSERT INTO `enumeration` VALUES ('appointment_reasons', 'Appointment Reason', '', 'Male', 'Primary', 'Good', 'New', '', 'All', 'Insurance', 'A - Assigned', 'A - On file', 'Patient', 'State License', 'Self', 'Dependant', 'SSN', 'A0', 'Physical', 'Initial Visit Date', 'blah', 'visa', 'Single', 'English', '', 'White/Hispanic', 'Seasonal Agricultural/Migrant Worker', 'Physical', 'Home', 'Unknown', 'Home', 'medicare');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `events`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `fbaddress`
-- 

CREATE TABLE `fbaddress` (
  `address_id` int(11) NOT NULL default '0',
  `external_id` int(11) NOT NULL default '0',
  `type` enum('default') NOT NULL default 'default',
  `name` varchar(100) NOT NULL default '',
  `line1` varchar(255) NOT NULL default '',
  `line2` varchar(255) NOT NULL default '',
  `city` varchar(255) NOT NULL default '',
  `state` varchar(5) NOT NULL default '0',
  `zip` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`address_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='An address that can be for a company or a person';

-- 
-- Dumping data for table `fbaddress`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `fbclaim`
-- 

CREATE TABLE `fbclaim` (
  `claim_id` int(11) NOT NULL default '0',
  `claim_identifier` varchar(255) NOT NULL default '',
  `revision` int(11) NOT NULL default '0',
  `status` enum('new','pending','sent','archive') NOT NULL default 'new',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `date_sent` datetime NOT NULL default '0000-00-00 00:00:00',
  `format` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`claim_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `fbclaim`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `fbclaimline`
-- 

CREATE TABLE `fbclaimline` (
  `claimline_id` int(11) NOT NULL default '0',
  `claim_id` int(11) NOT NULL default '0',
  `procedure` varchar(10) NOT NULL default '',
  `modifier` varchar(4) NOT NULL default '',
  `amount` float(11,2) NOT NULL default '0.00',
  `units` float(5,2) NOT NULL default '0.00',
  `comment` varchar(80) NOT NULL default '',
  `comment_type` varchar(10) NOT NULL default '',
  `date_of_treatment` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`claimline_id`),
  KEY `claim_id` (`claim_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `fbclaimline`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `fbcompany`
-- 

CREATE TABLE `fbcompany` (
  `company_id` int(11) NOT NULL default '0',
  `claim_id` int(11) NOT NULL default '0',
  `index` tinyint(4) NOT NULL default '0',
  `identifier` varchar(25) NOT NULL default '',
  `identifier_type` varchar(10) NOT NULL default '',
  `type` varchar(50) NOT NULL default '',
  `name` varchar(100) NOT NULL default '',
  `phone_number` varchar(45) NOT NULL default '',
  PRIMARY KEY  (`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Base Company record most of the data is in linked tables';

-- 
-- Dumping data for table `fbcompany`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `fbdiagnoses`
-- 

CREATE TABLE `fbdiagnoses` (
  `id` int(11) NOT NULL default '0',
  `claimline_id` int(11) NOT NULL default '0',
  `diagnosis` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `fbdiagnoses`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `fblatest_revision`
-- 

CREATE TABLE `fblatest_revision` (
  `claim_identifier` varchar(255) NOT NULL default '',
  `revision` int(11) NOT NULL default '0',
  PRIMARY KEY  (`claim_identifier`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `fblatest_revision`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `fbperson`
-- 

CREATE TABLE `fbperson` (
  `person_id` int(11) NOT NULL default '0',
  `claim_id` int(11) NOT NULL default '0',
  `index` tinyint(4) NOT NULL default '0',
  `type` varchar(50) NOT NULL default '',
  `identifier` varchar(100) NOT NULL default '',
  `identifier_type` varchar(10) NOT NULL default '',
  `record_number` varchar(255) NOT NULL default '',
  `salutation` varchar(20) NOT NULL default '',
  `last_name` varchar(100) NOT NULL default '',
  `first_name` varchar(100) NOT NULL default '',
  `middle_name` varchar(50) NOT NULL default '',
  `gender` enum('M','F','O') default NULL,
  `date_of_birth` date NOT NULL default '0000-00-00',
  `phone_number` varchar(45) NOT NULL default '',
  `comment` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`person_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='A person in the system';

-- 
-- Dumping data for table `fbperson`
-- 

INSERT INTO `fbperson` VALUES (18441, 19445, 0, 'FBPatient', '233-45-7763', 'SSN', '2706', '', 'Hewett', 'Margart', 'Floe', NULL, '1987-12-13', '', '');
INSERT INTO `fbperson` VALUES (18443, 19445, 0, 'FBProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (18449, 19445, 0, 'FBReferringProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (18451, 19445, 0, 'FBSupervisingProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (18453, 19445, 0, 'FBResponsibleParty', '233-45-7763', 'SSN', '2706', '', 'Hewett', 'Margart', 'Floe', NULL, '1987-12-13', '', '');
INSERT INTO `fbperson` VALUES (18466, 18623, 0, 'FBPatient', '111-22-3333', 'SSN', '3799', '', 'Furl', 'Janay', 'Veldhuizen', '', '1919-10-17', '', '');
INSERT INTO `fbperson` VALUES (18468, 18623, 0, 'FBProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (18474, 18623, 0, 'FBReferringProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (18476, 18623, 0, 'FBSupervisingProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (18478, 18623, 0, 'FBResponsibleParty', '648-72-1904', 'SSN', '3799', '', 'Furl', 'Janay', 'Veldhuizen', NULL, '1919-10-17', '', '');
INSERT INTO `fbperson` VALUES (18488, 19641, 0, 'FBPatient', '099-11-7607', 'SSN', '3113', '', 'Quarto', 'Maggie', 'Biehl', NULL, '1915-11-23', '', '');
INSERT INTO `fbperson` VALUES (18490, 19641, 0, 'FBProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (18496, 19641, 0, 'FBReferringProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (18498, 19641, 0, 'FBSupervisingProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (18500, 19641, 0, 'FBResponsibleParty', '099-11-7607', 'SSN', '3113', '', 'Quarto', 'Maggie', 'Biehl', NULL, '1915-11-23', '', '');
INSERT INTO `fbperson` VALUES (18512, 20348, 0, 'FBPatient', '099-11-7607', 'SSN', '3113', '', 'Quarto', 'Maggie', 'Biehl', NULL, '1915-11-23', '', '');
INSERT INTO `fbperson` VALUES (18514, 20348, 0, 'FBProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (18520, 20348, 0, 'FBReferringProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (18522, 20348, 0, 'FBSupervisingProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (18524, 20348, 0, 'FBResponsibleParty', '099-11-7607', 'SSN', '3113', '', 'Quarto', 'Maggie', 'Biehl', NULL, '1915-11-23', '', '');
INSERT INTO `fbperson` VALUES (18533, 20381, 0, 'FBPatient', '562-84-1994', 'SSN', '2533', '', 'Jeskie', 'Dannielle', 'Sillery', NULL, '1991-03-11', '', '');
INSERT INTO `fbperson` VALUES (18535, 20381, 0, 'FBProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (18541, 20381, 0, 'FBReferringProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (18543, 20381, 0, 'FBSupervisingProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (18545, 20381, 0, 'FBResponsibleParty', '562-84-1994', 'SSN', '2533', '', 'Jeskie', 'Dannielle', 'Sillery', NULL, '1991-03-11', '', '');
INSERT INTO `fbperson` VALUES (18556, 19684, 0, 'FBPatient', '234-06-8001', 'SSN', '3459', '', 'Debus', 'Piper', 'Desamparo', NULL, '1984-09-25', '', '');
INSERT INTO `fbperson` VALUES (18558, 19684, 0, 'FBProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (18564, 19684, 0, 'FBReferringProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (18566, 19684, 0, 'FBSupervisingProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (18568, 19684, 0, 'FBResponsibleParty', '234-06-8001', 'SSN', '3459', '', 'Debus', 'Piper', 'Desamparo', NULL, '1984-09-25', '', '');
INSERT INTO `fbperson` VALUES (18577, 19453, 0, 'FBPatient', '479-47-9690', 'SSN', '4458', '', 'Radican', 'Joana', 'Kofford', NULL, '1911-11-04', '', '');
INSERT INTO `fbperson` VALUES (18579, 19453, 0, 'FBProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (18585, 19453, 0, 'FBReferringProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (18587, 19453, 0, 'FBSupervisingProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (18589, 19453, 0, 'FBResponsibleParty', '479-47-9690', 'SSN', '4458', '', 'Radican', 'Joana', 'Kofford', NULL, '1911-11-04', '', '');
INSERT INTO `fbperson` VALUES (18599, 19449, 0, 'FBPatient', '465-52-2099', 'SSN', '2431', '', 'Federer', 'Dorinda', 'Carlie', NULL, '1955-03-22', '', '');
INSERT INTO `fbperson` VALUES (18601, 19449, 0, 'FBProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (18607, 19449, 0, 'FBReferringProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (18609, 19449, 0, 'FBSupervisingProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (18611, 19449, 0, 'FBResponsibleParty', '465-52-2099', 'SSN', '2431', '', 'Federer', 'Dorinda', 'Carlie', NULL, '1955-03-22', '', '');
INSERT INTO `fbperson` VALUES (18627, 20381, 0, 'FBSubscriber', '', '34', '', '', 'George', 'Jetson', '', '', '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19263, 19435, 0, 'FBPatient', '557-85-5725', 'SSN', '3573', '', 'Bado', 'Sheron', 'Robello', NULL, '1965-05-06', '', '');
INSERT INTO `fbperson` VALUES (19265, 19435, 0, 'FBProvider', '11111111', 'SSN', '', 'Dr', 'Augustin', 'Alfred', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19271, 19435, 0, 'FBReferringProvider', '11111111', 'SSN', '', 'Dr', 'Augustin', 'Alfred', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19273, 19435, 0, 'FBSupervisingProvider', '11111111', 'SSN', '', 'Dr', 'Augustin', 'Alfred', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19275, 19435, 0, 'FBResponsibleParty', '557-85-5725', 'SSN', '3573', '', 'Bado', 'Sheron', 'Robello', NULL, '1965-05-06', '', '');
INSERT INTO `fbperson` VALUES (19284, 19431, 0, 'FBPatient', '123-32-2323', 'SSN', '15', '', 'smith-jones', 'nancy', '', 'F', '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19286, 19431, 0, 'FBProvider', '', 'SSN', '', 'Dr', 'Doctor', 'Random', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19292, 19431, 0, 'FBReferringProvider', '', 'SSN', '', 'Dr', 'Doctor', 'Random', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19294, 19431, 0, 'FBSupervisingProvider', '', 'SSN', '', 'Dr', 'Doctor', 'Random', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19296, 19431, 0, 'FBResponsibleParty', '123-32-2323', 'SSN', '15', '', 'smith-jones', 'nancy', '', 'F', '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19303, 19424, 0, 'FBPatient', '123-32-2323', 'SSN', '15', '', 'smith-jones', 'nancy', '', 'F', '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19305, 19424, 0, 'FBProvider', '', 'SSN', '', 'Dr', 'Doctor', 'Random', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19311, 19424, 0, 'FBReferringProvider', '', 'SSN', '', 'Dr', 'Doctor', 'Random', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19313, 19424, 0, 'FBSupervisingProvider', '', 'SSN', '', 'Dr', 'Doctor', 'Random', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19315, 19424, 0, 'FBResponsibleParty', '123-32-2323', 'SSN', '15', '', 'smith-jones', 'nancy', '', 'F', '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19400, 19440, 0, 'FBPatient', '', '34', '', '', 'Trotter', 'Fred', '', '', '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19476, 19643, 0, 'FBPatient', '752-69-8322', 'SSN', '2235', '', 'Ancic', 'Alleen', 'Handville', NULL, '1974-04-29', '', '');
INSERT INTO `fbperson` VALUES (19478, 19643, 0, 'FBProvider', '', 'SSN', '', '', 'Minton', 'Michelle', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19484, 19643, 0, 'FBReferringProvider', '', 'SSN', '', '', 'Minton', 'Michelle', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19486, 19643, 0, 'FBSupervisingProvider', '', 'SSN', '', '', 'Minton', 'Michelle', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19488, 19643, 0, 'FBResponsibleParty', '752-69-8322', 'SSN', '2235', '', 'Ancic', 'Alleen', 'Handville', NULL, '1974-04-29', '', '');
INSERT INTO `fbperson` VALUES (19496, 19493, 0, 'FBPatient', '938-94-4688', 'SSN', '2582', '', 'Macgillivray', 'Rodger', 'Isaacson', NULL, '1966-05-06', '', '');
INSERT INTO `fbperson` VALUES (19498, 19493, 0, 'FBProvider', '', 'SSN', '', '', 'Minton', 'Michelle', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19504, 19493, 0, 'FBReferringProvider', '', 'SSN', '', '', 'Minton', 'Michelle', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19506, 19493, 0, 'FBSupervisingProvider', '', 'SSN', '', '', 'Minton', 'Michelle', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19508, 19493, 0, 'FBResponsibleParty', '938-94-4688', 'SSN', '2582', '', 'Macgillivray', 'Rodger', 'Isaacson', NULL, '1966-05-06', '', '');
INSERT INTO `fbperson` VALUES (19515, 19513, 0, 'FBPatient', '600-20-9282', 'SSN', '3356', '', 'Recore', 'Evonne', 'Tenore', NULL, '1945-10-18', '', '');
INSERT INTO `fbperson` VALUES (19517, 19513, 0, 'FBProvider', '', 'SSN', '', '', 'Minton', 'Michelle', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19523, 19513, 0, 'FBReferringProvider', '', 'SSN', '', '', 'Minton', 'Michelle', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19525, 19513, 0, 'FBSupervisingProvider', '', 'SSN', '', '', 'Minton', 'Michelle', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19527, 19513, 0, 'FBResponsibleParty', '600-20-9282', 'SSN', '3356', '', 'Recore', 'Evonne', 'Tenore', NULL, '1945-10-18', '', '');
INSERT INTO `fbperson` VALUES (19535, 19654, 0, 'FBPatient', '752-69-8322', 'SSN', '2235', '', 'Ancic', 'Alleen', 'Handville', NULL, '1974-04-29', '', '');
INSERT INTO `fbperson` VALUES (19537, 19654, 0, 'FBProvider', '', 'SSN', '', '', 'Minton', 'Michelle', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19543, 19654, 0, 'FBReferringProvider', '', 'SSN', '', '', 'Minton', 'Michelle', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19545, 19654, 0, 'FBSupervisingProvider', '', 'SSN', '', '', 'Minton', 'Michelle', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19547, 19654, 0, 'FBResponsibleParty', '752-69-8322', 'SSN', '2235', '', 'Ancic', 'Alleen', 'Handville', NULL, '1974-04-29', '', '');
INSERT INTO `fbperson` VALUES (19554, 19711, 0, 'FBPatient', '752-69-8322', 'SSN', '2235', '', 'Ancic', 'Alleen', 'Handville', NULL, '1974-04-29', '', '');
INSERT INTO `fbperson` VALUES (19556, 19711, 0, 'FBProvider', '', 'SSN', '', '', 'Minton', 'Michelle', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19562, 19711, 0, 'FBReferringProvider', '', 'SSN', '', '', 'Minton', 'Michelle', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19564, 19711, 0, 'FBSupervisingProvider', '', 'SSN', '', '', 'Minton', 'Michelle', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19566, 19711, 0, 'FBResponsibleParty', '752-69-8322', 'SSN', '2235', '', 'Ancic', 'Alleen', 'Handville', NULL, '1974-04-29', '', '');
INSERT INTO `fbperson` VALUES (19572, 19571, 0, 'FBPatient', '752-69-8322', 'SSN', '2235', '', 'Ancic', 'Alleen', 'Handville', NULL, '1974-04-29', '', '');
INSERT INTO `fbperson` VALUES (19574, 19571, 0, 'FBProvider', '', 'SSN', '', '', 'Minton', 'Michelle', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19580, 19571, 0, 'FBReferringProvider', '', 'SSN', '', '', 'Minton', 'Michelle', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19582, 19571, 0, 'FBSupervisingProvider', '', 'SSN', '', '', 'Minton', 'Michelle', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19584, 19571, 0, 'FBResponsibleParty', '752-69-8322', 'SSN', '2235', '', 'Ancic', 'Alleen', 'Handville', NULL, '1974-04-29', '', '');
INSERT INTO `fbperson` VALUES (19591, 19589, 0, 'FBPatient', '752-69-8322', 'SSN', '2235', '', 'Ancic', 'Alleen', 'Handville', NULL, '1974-04-29', '', '');
INSERT INTO `fbperson` VALUES (19593, 19589, 0, 'FBProvider', '', 'SSN', '', '', 'Minton', 'Michelle', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19599, 19589, 0, 'FBReferringProvider', '', 'SSN', '', '', 'Minton', 'Michelle', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19601, 19589, 0, 'FBSupervisingProvider', '', 'SSN', '', '', 'Minton', 'Michelle', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19603, 19589, 0, 'FBResponsibleParty', '752-69-8322', 'SSN', '2235', '', 'Ancic', 'Alleen', 'Handville', NULL, '1974-04-29', '', '');
INSERT INTO `fbperson` VALUES (19609, 19608, 0, 'FBPatient', '752-69-8322', 'SSN', '2235', '', 'Ancic', 'Alleen', 'Handville', NULL, '1974-04-29', '', '');
INSERT INTO `fbperson` VALUES (19611, 19608, 0, 'FBProvider', '', 'SSN', '', '', 'Minton', 'Michelle', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19617, 19608, 0, 'FBReferringProvider', '', 'SSN', '', '', 'Minton', 'Michelle', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19619, 19608, 0, 'FBSupervisingProvider', '', 'SSN', '', '', 'Minton', 'Michelle', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (19621, 19608, 0, 'FBResponsibleParty', '752-69-8322', 'SSN', '2235', '', 'Ancic', 'Alleen', 'Handville', NULL, '1974-04-29', '', '');
INSERT INTO `fbperson` VALUES (20021, 20039, 0, 'FBPatient', '123456789', 'SSN', '123', '', 'Payne', 'Ima', '', 'F', '1970-01-01', '', '');
INSERT INTO `fbperson` VALUES (20023, 20039, 0, 'FBProvider', '', 'SSN', '', '', 'Minton', 'Michelle', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20029, 20039, 0, 'FBReferringProvider', '', 'SSN', '', '', 'Minton', 'Michelle', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20031, 20039, 0, 'FBSupervisingProvider', '', 'SSN', '', '', 'Minton', 'Michelle', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20033, 20039, 0, 'FBResponsibleParty', '123456789', 'SSN', '123', '', 'Payne', 'Ima', '', 'F', '1970-01-01', '', '');
INSERT INTO `fbperson` VALUES (20127, 20117, 0, 'FBPatient', '693-32-5147', 'SSN', '2545', '', 'Weichman', 'Glendora', 'Schofell', NULL, '1992-06-11', '', '');
INSERT INTO `fbperson` VALUES (20129, 20117, 0, 'FBSubscriber', '693-32-5147', 'SSN', '', '', 'Weichman', 'Glendora', 'Schofell', NULL, '1992-06-11', '', '');
INSERT INTO `fbperson` VALUES (20133, 20117, 0, 'FBProvider', '66666666', 'SSN', '', 'Dr.', 'Everstone', 'Elaine', 'E', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20139, 20117, 0, 'FBReferringProvider', '66666666', 'SSN', '', 'Dr.', 'Everstone', 'Elaine', 'E', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20141, 20117, 0, 'FBSupervisingProvider', '66666666', 'SSN', '', 'Dr.', 'Everstone', 'Elaine', 'E', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20143, 20117, 0, 'FBResponsibleParty', '693-32-5147', 'SSN', '2545', '', 'Weichman', 'Glendora', 'Schofell', NULL, '1992-06-11', '', '');
INSERT INTO `fbperson` VALUES (20159, 20158, 0, 'FBPatient', '693-32-5147', 'SSN', '2545', '', 'Weichman', 'Glendora', 'Schofell', NULL, '1992-06-11', '', '');
INSERT INTO `fbperson` VALUES (20161, 20158, 0, 'FBSubscriber', '693-32-5147', 'SSN', '', '', 'Weichman', 'Glendora', 'Schofell', NULL, '1992-06-11', '', '');
INSERT INTO `fbperson` VALUES (20165, 20158, 0, 'FBProvider', '66666666', 'SSN', '', 'Dr.', 'Everstone', 'Elaine', 'E', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20171, 20158, 0, 'FBReferringProvider', '66666666', 'SSN', '', 'Dr.', 'Everstone', 'Elaine', 'E', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20173, 20158, 0, 'FBSupervisingProvider', '66666666', 'SSN', '', 'Dr.', 'Everstone', 'Elaine', 'E', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20175, 20158, 0, 'FBResponsibleParty', '693-32-5147', 'SSN', '2545', '', 'Weichman', 'Glendora', 'Schofell', NULL, '1992-06-11', '', '');
INSERT INTO `fbperson` VALUES (20183, 20180, 0, 'FBPatient', '693-32-5147', 'SSN', '2545', '', 'Weichman', 'Glendora', 'Schofell', NULL, '1992-06-11', '', '');
INSERT INTO `fbperson` VALUES (20185, 20180, 0, 'FBSubscriber', '693-32-5147', 'SSN', '', '', 'Weichman', 'Glendora', 'Schofell', NULL, '1992-06-11', '', '');
INSERT INTO `fbperson` VALUES (20189, 20180, 0, 'FBProvider', '66666666', 'SSN', '', 'Dr.', 'Everstone', 'Elaine', 'E', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20195, 20180, 0, 'FBReferringProvider', '66666666', 'SSN', '', 'Dr.', 'Everstone', 'Elaine', 'E', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20197, 20180, 0, 'FBSupervisingProvider', '66666666', 'SSN', '', 'Dr.', 'Everstone', 'Elaine', 'E', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20199, 20180, 0, 'FBResponsibleParty', '693-32-5147', 'SSN', '2545', '', 'Weichman', 'Glendora', 'Schofell', NULL, '1992-06-11', '', '');
INSERT INTO `fbperson` VALUES (20207, 20206, 0, 'FBPatient', '693-32-5147', 'SSN', '2545', '', 'Weichman', 'Glendora', 'Schofell', NULL, '1992-06-11', '', '');
INSERT INTO `fbperson` VALUES (20209, 20206, 0, 'FBSubscriber', '693-32-5147', 'SSN', '', '', 'Weichman', 'Glendora', 'Schofell', NULL, '1992-06-11', '', '');
INSERT INTO `fbperson` VALUES (20213, 20206, 0, 'FBProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20219, 20206, 0, 'FBReferringProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20221, 20206, 0, 'FBSupervisingProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20223, 20206, 0, 'FBResponsibleParty', '693-32-5147', 'SSN', '2545', '', 'Weichman', 'Glendora', 'Schofell', NULL, '1992-06-11', '', '');
INSERT INTO `fbperson` VALUES (20230, 20229, 0, 'FBPatient', '693-32-5147', 'SSN', '2545', '', 'Weichman', 'Glendora', 'Schofell', NULL, '1992-06-11', '', '');
INSERT INTO `fbperson` VALUES (20232, 20229, 0, 'FBSubscriber', '693-32-5147', 'SSN', '', '', 'Weichman', 'Glendora', 'Schofell', NULL, '1992-06-11', '', '');
INSERT INTO `fbperson` VALUES (20236, 20229, 0, 'FBProvider', '66666666', 'SSN', '', 'Dr.', 'Everstone', 'Elaine', 'E', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20242, 20229, 0, 'FBReferringProvider', '66666666', 'SSN', '', 'Dr.', 'Everstone', 'Elaine', 'E', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20244, 20229, 0, 'FBSupervisingProvider', '66666666', 'SSN', '', 'Dr.', 'Everstone', 'Elaine', 'E', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20246, 20229, 0, 'FBResponsibleParty', '693-32-5147', 'SSN', '2545', '', 'Weichman', 'Glendora', 'Schofell', NULL, '1992-06-11', '', '');
INSERT INTO `fbperson` VALUES (20258, 20257, 0, 'FBPatient', '098-28-5023', 'SSN', '2657', '', 'Ladewig', 'Thea', 'Heon', NULL, '1936-12-31', '', '');
INSERT INTO `fbperson` VALUES (20260, 20257, 0, 'FBProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20266, 20257, 0, 'FBReferringProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20268, 20257, 0, 'FBSupervisingProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20270, 20257, 0, 'FBResponsibleParty', '098-28-5023', 'SSN', '2657', '', 'Ladewig', 'Thea', 'Heon', NULL, '1936-12-31', '', '');
INSERT INTO `fbperson` VALUES (20280, 20277, 0, 'FBPatient', '140-12-5530', 'SSN', '3253', '', 'Obnegon', 'Detra', 'Ibraham', NULL, '1961-09-24', '', '');
INSERT INTO `fbperson` VALUES (20282, 20277, 0, 'FBProvider', '1233323J', 'SSN', '', '', 'Conrad', 'Joe', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20288, 20277, 0, 'FBReferringProvider', '1233323J', 'SSN', '', '', 'Conrad', 'Joe', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20290, 20277, 0, 'FBSupervisingProvider', '1233323J', 'SSN', '', '', 'Conrad', 'Joe', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20292, 20277, 0, 'FBResponsibleParty', '140-12-5530', 'SSN', '3253', '', 'Obnegon', 'Detra', 'Ibraham', NULL, '1961-09-24', '', '');
INSERT INTO `fbperson` VALUES (20300, 20297, 0, 'FBPatient', '140-12-5530', 'SSN', '3253', '', 'Obnegon', 'Detra', 'Ibraham', NULL, '1961-09-24', '', '');
INSERT INTO `fbperson` VALUES (20302, 20297, 0, 'FBProvider', '1233323J', 'SSN', '', '', 'Conrad', 'Joe', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20308, 20297, 0, 'FBReferringProvider', '1233323J', 'SSN', '', '', 'Conrad', 'Joe', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20310, 20297, 0, 'FBSupervisingProvider', '1233323J', 'SSN', '', '', 'Conrad', 'Joe', '', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20312, 20297, 0, 'FBResponsibleParty', '140-12-5530', 'SSN', '3253', '', 'Obnegon', 'Detra', 'Ibraham', NULL, '1961-09-24', '', '');
INSERT INTO `fbperson` VALUES (20389, 20388, 0, 'FBPatient', '634-63-6494', 'SSN', '2634', '', 'Abundis', 'Robena', 'Forwood', NULL, '1914-02-22', '', '');
INSERT INTO `fbperson` VALUES (20391, 20388, 0, 'FBProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20397, 20388, 0, 'FBReferringProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20399, 20388, 0, 'FBSupervisingProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20401, 20388, 0, 'FBResponsibleParty', '634-63-6494', 'SSN', '2634', '', 'Abundis', 'Robena', 'Forwood', NULL, '1914-02-22', '', '');
INSERT INTO `fbperson` VALUES (20408, 20407, 0, 'FBPatient', '634-63-6494', 'SSN', '2634', '', 'Abundis', 'Robena', 'Forwood', NULL, '1914-02-22', '', '');
INSERT INTO `fbperson` VALUES (20410, 20407, 0, 'FBProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20416, 20407, 0, 'FBReferringProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20418, 20407, 0, 'FBSupervisingProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (20420, 20407, 0, 'FBResponsibleParty', '634-63-6494', 'SSN', '2634', '', 'Abundis', 'Robena', 'Forwood', NULL, '1914-02-22', '', '');
INSERT INTO `fbperson` VALUES (200427, 200426, 0, 'FBPatient', '634-63-6494', 'SSN', '2634', '', 'Abundis', 'Robena', 'Forwood', NULL, '1914-02-22', '', '');
INSERT INTO `fbperson` VALUES (200429, 200426, 0, 'FBProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (200435, 200426, 0, 'FBReferringProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (200437, 200426, 0, 'FBSupervisingProvider', '3412344132', 'SSN', '', 'Dr.', 'Agona', 'Albert', 'A', NULL, '0000-00-00', '', '');
INSERT INTO `fbperson` VALUES (200439, 200426, 0, 'FBResponsibleParty', '634-63-6494', 'SSN', '2634', '', 'Abundis', 'Robena', 'Forwood', NULL, '1914-02-22', '', '');

-- --------------------------------------------------------

-- 
-- Table structure for table `fbpractice`
-- 

CREATE TABLE `fbpractice` (
  `practice_id` int(11) NOT NULL default '0',
  `claim_id` int(11) NOT NULL default '0',
  `billing_contact_person_id` int(11) NOT NULL default '0',
  `treating_location_company_company_id` int(11) NOT NULL default '0',
  `billing_location_company_id` int(11) NOT NULL default '0',
  `provider_person_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`practice_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `fbpractice`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `fee_schedule`
-- 

CREATE TABLE `fee_schedule` (
  `fee_schedule_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `label` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `priority` int(11) NOT NULL default '2',
  PRIMARY KEY  (`fee_schedule_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `fee_schedule`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `fee_schedule_data`
-- 

CREATE TABLE `fee_schedule_data` (
  `code_id` int(11) NOT NULL default '0',
  `revision_id` int(11) NOT NULL default '0',
  `fee_schedule_id` int(11) NOT NULL default '0',
  `data` float(11,2) NOT NULL default '0.00',
  `formula` varchar(255) NOT NULL default '',
  `mapped_code` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`code_id`,`revision_id`,`fee_schedule_id`),
  KEY `fee_schedule_id` (`fee_schedule_id`),
  KEY `revision_id` (`revision_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `fee_schedule_data`
-- 


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `fee_schedule_revision`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `form`
-- 

CREATE TABLE `form` (
  `form_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`form_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Contains the EMR extending forms STARTWITHDATA';

-- 
-- Dumping data for table `form`
-- 

INSERT INTO `form` VALUES (800, 'Test Data', 'Some random data');
INSERT INTO `form` VALUES (1710, 'Patient Vitals', 'Patient Vital Statistics');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Links in the form data STARTWITHDATA';

-- 
-- Dumping data for table `form_data`
-- 

INSERT INTO `form_data` VALUES (2057, 800, 1110, '2005-03-14 15:09:50');
INSERT INTO `form_data` VALUES (20350, 800, 10061, '2005-04-08 09:05:24');
INSERT INTO `form_data` VALUES (20351, 800, 10001, '2005-04-08 09:07:50');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='ACL Table';

-- 
-- Dumping data for table `gacl_acl`
-- 

INSERT INTO `gacl_acl` VALUES (26, 'user', 1, 1, '', 'Give Superadmn and Supervisors access to everything', 1112056945);
INSERT INTO `gacl_acl` VALUES (24, 'user', 1, 1, '', 'Give Super Admin and Supervisor access to everything even when no resource is selected', 1112056973);
INSERT INTO `gacl_acl` VALUES (30, 'user', 1, 1, '', 'Give Calendar users and Supervisors access to basic calendar functions', 1112160903);
INSERT INTO `gacl_acl` VALUES (29, 'user', 0, 1, '', 'Deny Supervisors access to some system wide configuration sections', 1112057023);
INSERT INTO `gacl_acl` VALUES (31, 'user', 1, 1, '', 'Give Calendar supervisors the ability to double book', 1112057044);
INSERT INTO `gacl_acl` VALUES (32, 'user', 1, 1, '', 'Give billing users basic access to those sections', 1112160920);
INSERT INTO `gacl_acl` VALUES (33, 'user', 1, 1, '', 'Give all users of the system access to basic app sections', 1112057091);
INSERT INTO `gacl_acl` VALUES (35, 'user', 1, 1, '', '', 1112803381);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_acl_sections`
-- 

INSERT INTO `gacl_acl_sections` VALUES (1, 'system', 1, 'System', 0);
INSERT INTO `gacl_acl_sections` VALUES (2, 'user', 2, 'User', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_acl_seq`
-- 

CREATE TABLE `gacl_acl_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_acl_seq`
-- 

INSERT INTO `gacl_acl_seq` VALUES (35);
INSERT INTO `gacl_acl_seq` VALUES (35);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
INSERT INTO `gacl_aco` VALUES (20, 'actions', 'double_book', 17, 'Double Book Apointment', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aco_map`
-- 

CREATE TABLE `gacl_aco_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_aco_map`
-- 

INSERT INTO `gacl_aco_map` VALUES (24, 'actions', 'add');
INSERT INTO `gacl_aco_map` VALUES (24, 'actions', 'delete');
INSERT INTO `gacl_aco_map` VALUES (24, 'actions', 'delete_owner');
INSERT INTO `gacl_aco_map` VALUES (24, 'actions', 'edit');
INSERT INTO `gacl_aco_map` VALUES (24, 'actions', 'edit_owner');
INSERT INTO `gacl_aco_map` VALUES (24, 'actions', 'uploadFile');
INSERT INTO `gacl_aco_map` VALUES (24, 'actions', 'usage');
INSERT INTO `gacl_aco_map` VALUES (24, 'actions', 'view');
INSERT INTO `gacl_aco_map` VALUES (26, 'actions', 'add');
INSERT INTO `gacl_aco_map` VALUES (26, 'actions', 'delete');
INSERT INTO `gacl_aco_map` VALUES (26, 'actions', 'delete_owner');
INSERT INTO `gacl_aco_map` VALUES (26, 'actions', 'edit');
INSERT INTO `gacl_aco_map` VALUES (26, 'actions', 'edit_owner');
INSERT INTO `gacl_aco_map` VALUES (26, 'actions', 'uploadFile');
INSERT INTO `gacl_aco_map` VALUES (26, 'actions', 'usage');
INSERT INTO `gacl_aco_map` VALUES (26, 'actions', 'view');
INSERT INTO `gacl_aco_map` VALUES (29, 'actions', 'add');
INSERT INTO `gacl_aco_map` VALUES (29, 'actions', 'edit');
INSERT INTO `gacl_aco_map` VALUES (30, 'actions', 'add');
INSERT INTO `gacl_aco_map` VALUES (30, 'actions', 'delete');
INSERT INTO `gacl_aco_map` VALUES (30, 'actions', 'edit');
INSERT INTO `gacl_aco_map` VALUES (30, 'actions', 'usage');
INSERT INTO `gacl_aco_map` VALUES (30, 'actions', 'view');
INSERT INTO `gacl_aco_map` VALUES (31, 'actions', 'double_book');
INSERT INTO `gacl_aco_map` VALUES (32, 'actions', 'add');
INSERT INTO `gacl_aco_map` VALUES (32, 'actions', 'delete');
INSERT INTO `gacl_aco_map` VALUES (32, 'actions', 'edit');
INSERT INTO `gacl_aco_map` VALUES (32, 'actions', 'usage');
INSERT INTO `gacl_aco_map` VALUES (32, 'actions', 'view');
INSERT INTO `gacl_aco_map` VALUES (33, 'actions', 'usage');
INSERT INTO `gacl_aco_map` VALUES (33, 'actions', 'view');
INSERT INTO `gacl_aco_map` VALUES (35, 'actions', 'double_book');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_aco_sections`
-- 

INSERT INTO `gacl_aco_sections` VALUES (11, 'actions', 10, 'Actions', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aco_sections_seq`
-- 

CREATE TABLE `gacl_aco_sections_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_aco_sections_seq`
-- 

INSERT INTO `gacl_aco_sections_seq` VALUES (11);
INSERT INTO `gacl_aco_sections_seq` VALUES (11);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aco_seq`
-- 

CREATE TABLE `gacl_aco_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_aco_seq`
-- 

INSERT INTO `gacl_aco_seq` VALUES (20);
INSERT INTO `gacl_aco_seq` VALUES (20);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_aro`
-- 

INSERT INTO `gacl_aro` VALUES (15, 'users', 'admin', 10, 'Admin', 0);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_aro_groups`
-- 

INSERT INTO `gacl_aro_groups` VALUES (10, 0, 1, 22, 'Root', 'root');
INSERT INTO `gacl_aro_groups` VALUES (12, 23, 11, 12, 'System Admin', 'admin');
INSERT INTO `gacl_aro_groups` VALUES (19, 10, 2, 9, 'User Types', 'users');
INSERT INTO `gacl_aro_groups` VALUES (20, 19, 3, 4, 'Provider', 'provider');
INSERT INTO `gacl_aro_groups` VALUES (21, 19, 5, 6, 'Mid-level', 'mid-level');
INSERT INTO `gacl_aro_groups` VALUES (22, 19, 7, 8, 'Staff', 'staff');
INSERT INTO `gacl_aro_groups` VALUES (23, 10, 10, 21, 'Roles', 'roles');
INSERT INTO `gacl_aro_groups` VALUES (24, 23, 13, 14, 'Supervisor', 'supervisor');
INSERT INTO `gacl_aro_groups` VALUES (26, 23, 15, 16, 'Calendar Supervisor', 'calendar_supervisor');
INSERT INTO `gacl_aro_groups` VALUES (27, 23, 17, 18, 'Calendar User', 'calendar_user');
INSERT INTO `gacl_aro_groups` VALUES (28, 23, 19, 20, 'Billing User', 'billing_user');

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_groups_id_seq`
-- 

CREATE TABLE `gacl_aro_groups_id_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_aro_groups_id_seq`
-- 

INSERT INTO `gacl_aro_groups_id_seq` VALUES (28);
INSERT INTO `gacl_aro_groups_id_seq` VALUES (28);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_groups_map`
-- 

CREATE TABLE `gacl_aro_groups_map` (
  `acl_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acl_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_aro_groups_map`
-- 

INSERT INTO `gacl_aro_groups_map` VALUES (24, 12);
INSERT INTO `gacl_aro_groups_map` VALUES (24, 24);
INSERT INTO `gacl_aro_groups_map` VALUES (26, 12);
INSERT INTO `gacl_aro_groups_map` VALUES (26, 24);
INSERT INTO `gacl_aro_groups_map` VALUES (29, 24);
INSERT INTO `gacl_aro_groups_map` VALUES (30, 26);
INSERT INTO `gacl_aro_groups_map` VALUES (30, 27);
INSERT INTO `gacl_aro_groups_map` VALUES (31, 26);
INSERT INTO `gacl_aro_groups_map` VALUES (32, 28);
INSERT INTO `gacl_aro_groups_map` VALUES (33, 20);
INSERT INTO `gacl_aro_groups_map` VALUES (33, 21);
INSERT INTO `gacl_aro_groups_map` VALUES (33, 22);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_map`
-- 

CREATE TABLE `gacl_aro_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_aro_map`
-- 

INSERT INTO `gacl_aro_map` VALUES (35, 'users', 'admin');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_aro_sections`
-- 

INSERT INTO `gacl_aro_sections` VALUES (10, 'users', 10, 'Users', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_sections_seq`
-- 

CREATE TABLE `gacl_aro_sections_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_aro_sections_seq`
-- 

INSERT INTO `gacl_aro_sections_seq` VALUES (11);
INSERT INTO `gacl_aro_sections_seq` VALUES (11);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_seq`
-- 

CREATE TABLE `gacl_aro_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_aro_seq`
-- 

INSERT INTO `gacl_aro_seq` VALUES (36);
INSERT INTO `gacl_aro_seq` VALUES (36);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
INSERT INTO `gacl_axo` VALUES (58, 'resources', 'pdf', 10, 'Section - PDF', 0);
INSERT INTO `gacl_axo` VALUES (59, 'resources', 'coding', 10, 'Section - Coding', 0);
INSERT INTO `gacl_axo` VALUES (60, 'resources', 'docs', 10, 'Section - Docs', 0);
INSERT INTO `gacl_axo` VALUES (61, 'resources', 'eob', 10, 'Section - Eob', 0);
INSERT INTO `gacl_axo` VALUES (62, 'resources', 'claim', 10, 'Section - Claim', 0);
INSERT INTO `gacl_axo` VALUES (63, 'resources', 'freebgateway', 10, 'Section - FreeBGateway', 0);
INSERT INTO `gacl_axo` VALUES (64, 'resources', 'main_calendar', 1, 'Main Group Calendar', 0);
INSERT INTO `gacl_axo` VALUES (65, 'resources', 'main_billing', 2, 'Main Group Billing', 0);
INSERT INTO `gacl_axo` VALUES (66, 'resources', 'main_patient', 3, 'Main Group Patient', 0);
INSERT INTO `gacl_axo` VALUES (67, 'resources', 'main_admin', 4, 'Main Group Admin', 0);
INSERT INTO `gacl_axo` VALUES (68, 'resources', 'account', 10, 'Section - Account', 0);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_axo_groups`
-- 

INSERT INTO `gacl_axo_groups` VALUES (10, 0, 1, 4, 'Root', 'root');
INSERT INTO `gacl_axo_groups` VALUES (11, 10, 2, 3, 'All Site Sections', 'sections');

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_groups_id_seq`
-- 

CREATE TABLE `gacl_axo_groups_id_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_axo_groups_id_seq`
-- 

INSERT INTO `gacl_axo_groups_id_seq` VALUES (11);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_groups_map`
-- 

CREATE TABLE `gacl_axo_groups_map` (
  `acl_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`acl_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_axo_groups_map`
-- 

INSERT INTO `gacl_axo_groups_map` VALUES (24, 11);
INSERT INTO `gacl_axo_groups_map` VALUES (35, 11);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_map`
-- 

CREATE TABLE `gacl_axo_map` (
  `acl_id` int(11) NOT NULL default '0',
  `section_value` varchar(230) NOT NULL default '0',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`acl_id`,`section_value`,`value`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_axo_map`
-- 

INSERT INTO `gacl_axo_map` VALUES (29, 'resources', 'documentcategory');
INSERT INTO `gacl_axo_map` VALUES (29, 'resources', 'enumeration');
INSERT INTO `gacl_axo_map` VALUES (29, 'resources', 'feeschedule');
INSERT INTO `gacl_axo_map` VALUES (29, 'resources', 'form');
INSERT INTO `gacl_axo_map` VALUES (29, 'resources', 'report');
INSERT INTO `gacl_axo_map` VALUES (29, 'resources', 'superbill');
INSERT INTO `gacl_axo_map` VALUES (29, 'resources', 'user');
INSERT INTO `gacl_axo_map` VALUES (30, 'resources', 'calendar');
INSERT INTO `gacl_axo_map` VALUES (30, 'resources', 'main_calendar');
INSERT INTO `gacl_axo_map` VALUES (31, 'resources', 'calendar');
INSERT INTO `gacl_axo_map` VALUES (32, 'resources', 'billing');
INSERT INTO `gacl_axo_map` VALUES (32, 'resources', 'claim');
INSERT INTO `gacl_axo_map` VALUES (32, 'resources', 'coding');
INSERT INTO `gacl_axo_map` VALUES (32, 'resources', 'document');
INSERT INTO `gacl_axo_map` VALUES (32, 'resources', 'eob');
INSERT INTO `gacl_axo_map` VALUES (32, 'resources', 'main_billing');
INSERT INTO `gacl_axo_map` VALUES (32, 'resources', 'patient');
INSERT INTO `gacl_axo_map` VALUES (33, 'resources', 'access');
INSERT INTO `gacl_axo_map` VALUES (33, 'resources', 'default');
INSERT INTO `gacl_axo_map` VALUES (33, 'resources', 'docs');
INSERT INTO `gacl_axo_map` VALUES (33, 'resources', 'pdf');
INSERT INTO `gacl_axo_map` VALUES (33, 'resources', 'preferences');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_axo_sections`
-- 

INSERT INTO `gacl_axo_sections` VALUES (0, 'resources', 10, 'Resources', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_sections_seq`
-- 

CREATE TABLE `gacl_axo_sections_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_axo_sections_seq`
-- 

INSERT INTO `gacl_axo_sections_seq` VALUES (23);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_seq`
-- 

CREATE TABLE `gacl_axo_seq` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_axo_seq`
-- 

INSERT INTO `gacl_axo_seq` VALUES (68);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_groups_aro_map`
-- 

CREATE TABLE `gacl_groups_aro_map` (
  `group_id` int(11) NOT NULL default '0',
  `aro_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`aro_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_groups_aro_map`
-- 

INSERT INTO `gacl_groups_aro_map` VALUES (12, 15);
INSERT INTO `gacl_groups_aro_map` VALUES (20, 26);
INSERT INTO `gacl_groups_aro_map` VALUES (20, 28);
INSERT INTO `gacl_groups_aro_map` VALUES (20, 29);
INSERT INTO `gacl_groups_aro_map` VALUES (20, 30);
INSERT INTO `gacl_groups_aro_map` VALUES (20, 31);
INSERT INTO `gacl_groups_aro_map` VALUES (20, 32);
INSERT INTO `gacl_groups_aro_map` VALUES (20, 33);
INSERT INTO `gacl_groups_aro_map` VALUES (20, 34);
INSERT INTO `gacl_groups_aro_map` VALUES (20, 35);
INSERT INTO `gacl_groups_aro_map` VALUES (20, 36);
INSERT INTO `gacl_groups_aro_map` VALUES (21, 27);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_groups_axo_map`
-- 

CREATE TABLE `gacl_groups_axo_map` (
  `group_id` int(11) NOT NULL default '0',
  `axo_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`axo_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
INSERT INTO `gacl_groups_axo_map` VALUES (11, 58);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 59);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 60);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 61);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 62);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 63);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 64);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 65);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 66);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 67);
INSERT INTO `gacl_groups_axo_map` VALUES (11, 68);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_phpgacl`
-- 

CREATE TABLE `gacl_phpgacl` (
  `name` varchar(230) NOT NULL default '',
  `value` varchar(230) NOT NULL default '',
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `gacl_phpgacl`
-- 

INSERT INTO `gacl_phpgacl` VALUES ('version', '3.3.3');
INSERT INTO `gacl_phpgacl` VALUES ('schema_version', '2.1');

-- --------------------------------------------------------

-- 
-- Table structure for table `groups`
-- 

CREATE TABLE `groups` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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

CREATE TABLE `identifier` (
  `identifier_id` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  `identifier` varchar(100) NOT NULL default '',
  `identifier_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`identifier_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `identifier`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `import_map`
-- 

CREATE TABLE `import_map` (
  `old_id` int(11) NOT NULL default '0',
  `new_id` int(11) default NULL,
  `old_table_name` varchar(100) NOT NULL default '',
  `new_object_name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`old_id`,`old_table_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `import_map`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `insurance`
-- 

CREATE TABLE `insurance` (
  `company_id` int(11) NOT NULL default '0',
  `fee_schedule_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`),
  KEY `fee_schedule_id` (`fee_schedule_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `insurance`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `insurance_program`
-- 

CREATE TABLE `insurance_program` (
  `insurance_program_id` int(11) NOT NULL default '0',
  `payer_type` int(11) NOT NULL default '0',
  `company_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `fee_schedule_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`insurance_program_id`),
  KEY `fee_schedule_id` (`fee_schedule_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `insurance_program`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `insured_relationship`
-- 

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `insured_relationship`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `menu`
-- 

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `menu`
-- 

INSERT INTO `menu` VALUES (1, '', 1, '', 'children', 0, '', '', 'main');
INSERT INTO `menu` VALUES (2, 'default', 39, '', 'children', 100, 'Logout', 'Access/logout', 'main');
INSERT INTO `menu` VALUES (3, 'default', 39, '', 'children', 10, 'Preferences', 'Preferences/list', 'main');
INSERT INTO `menu` VALUES (4, 'admin', 1, '', 'children', 800, 'Reports', '', 'main/Admin');
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
INSERT INTO `menu` VALUES (29, 'patient', 68, '', 'children', 10, 'Fillout Form', 'Form/fillout', 'main');
INSERT INTO `menu` VALUES (30, 'patient', 1, '', 'children', 100, 'Patients', '', '');
INSERT INTO `menu` VALUES (31, 'patient', 30, '', 'children', 20, 'Add Patient', 'Patient/edit', 'main');
INSERT INTO `menu` VALUES (32, 'admin', 5, '', 'children', 160, 'List Insurance Companies', 'Insurance/list', 'main');
INSERT INTO `menu` VALUES (33, 'admin', 5, '', 'children', 170, 'Add Insurance Company', 'Insurance/edit', 'main');
INSERT INTO `menu` VALUES (36, 'admin', 81, '', 'children', 50, 'Document Categories', 'DocumentCategory/list', 'main');
INSERT INTO `menu` VALUES (37, 'patient', 68, '', 'children', 20, 'Documents', 'Document/list', 'main');
INSERT INTO `menu` VALUES (38, 'admin', 45, '', 'children', 30, 'Edit Superbill', 'Superbill/list', 'main');
INSERT INTO `menu` VALUES (39, 'default', 1, '', 'children', 300, 'My Account', '', 'main');
INSERT INTO `menu` VALUES (81, 'admin', 1, '', 'children', 700, 'System', '', '');
INSERT INTO `menu` VALUES (42, 'billing', 1, '', 'children', 300, 'Reports', '', 'main/Billing');
INSERT INTO `menu` VALUES (43, 'default', 1, '', 'children', 200, 'Reports', '', 'main/Calendar');
INSERT INTO `menu` VALUES (44, 'patient', 1, '', 'children', 300, 'Reports', '', 'main/Patient');
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
INSERT INTO `menu` VALUES (64, 'billing', 62, '', 'children', 20, 'Add Claim', 'Claim/edit', 'freeb2');
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
INSERT INTO `menu` VALUES (76, 'billing', 1, '', 'children', 600, 'Help', '', '');
INSERT INTO `menu` VALUES (77, 'billing', 76, '', 'children', 10, 'API Docs', 'Docs/api', 'main');
INSERT INTO `menu` VALUES (78, 'admin', 1, '', 'children', 1000, 'Help', '', '');
INSERT INTO `menu` VALUES (79, 'admin', 78, '', 'children', 10, 'API Docs', 'Docs/api', 'main');
INSERT INTO `menu` VALUES (83, 'admin', 5, '', 'children', 5, 'List Schedules/Facilities', 'Location/list', 'main');
INSERT INTO `menu` VALUES (84, 'admin', 5, '', 'children', 20, 'Add New Practice', 'Location/edit_practice', 'main');
INSERT INTO `menu` VALUES (85, 'admin', 4, '', 'children', 5, 'List Reports', 'Report/list', 'main');
INSERT INTO `menu` VALUES (86, 'admin', 1, '', 'children', 900, '', 'Admin/default', 'main');
INSERT INTO `menu` VALUES (87, 'admin', 4, '', 'children', 50, 'Connect Report', 'Report/connect', 'main');
INSERT INTO `menu` VALUES (88, 'billing', 1, '', 'children', 0, '', 'Billing/default', 'main');
INSERT INTO `menu` VALUES (89, 'patient', 1, '', 'children', -1, 'Dashboard Reports', '', 'main/Patient');
INSERT INTO `menu` VALUES (90, 'patient', 1, '', 'children', -1, 'Dashboard Forms', '', 'main/Patient');
INSERT INTO `menu` VALUES (91, 'patient', 1, '', 'children', -1, 'Encounter Forms', '', 'main/Encounter');
INSERT INTO `menu` VALUES (92, 'admin', 26, '', 'children', 100, 'Connect', 'Form/connect', 'main');
INSERT INTO `menu` VALUES (93, 'billing', 1, '', 'children', 0, '', 'Eob/Payment', 'main');
INSERT INTO `menu` VALUES (94, 'default', 39, '', 'children', 50, 'Change Password', 'User/password', 'main');
INSERT INTO `menu` VALUES (95, 'patient', 46, '', 'children', 50, 'Change Password', 'User/password', 'main');
INSERT INTO `menu` VALUES (96, 'billing', 49, '', 'children', 50, 'Change Password', 'User/password', 'main');
INSERT INTO `menu` VALUES (97, 'admin', 59, '', 'children', 50, 'Change Password', 'User/password', 'main');
INSERT INTO `menu` VALUES (98, 'admin', 81, '', 'children', 800, 'ACL Editor', 'Admin/acl', 'main');
INSERT INTO `menu` VALUES (99, 'patient', 1, '', 'children', 1000, '', 'Account/history', 'main');

-- --------------------------------------------------------

-- 
-- Table structure for table `menu_form`
-- 

CREATE TABLE `menu_form` (
  `menu_form_id` int(11) NOT NULL default '0',
  `menu_id` int(11) NOT NULL default '0',
  `form_id` int(11) NOT NULL default '0',
  `title` varchar(50) NOT NULL default '',
  `custom_action` varchar(255) default NULL,
  PRIMARY KEY  (`menu_form_id`),
  KEY `menu_id` (`menu_id`),
  KEY `form_id` (`form_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `menu_form`
-- 

INSERT INTO `menu_form` VALUES (2064, 90, 800, 'Test Data', NULL);
INSERT INTO `menu_form` VALUES (2066, 91, 1710, 'Patient Vitals', NULL);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `menu_report`
-- 

INSERT INTO `menu_report` VALUES (1714, 42, 792, 'Code Report', NULL);
INSERT INTO `menu_report` VALUES (1715, 4, 792, 'Code Report', NULL);
INSERT INTO `menu_report` VALUES (2054, 44, 792, 'Test', NULL);
INSERT INTO `menu_report` VALUES (2055, 89, 792, 'Selected Test', NULL);
INSERT INTO `menu_report` VALUES (8170, 44, 8169, 'multi test', NULL);
INSERT INTO `menu_report` VALUES (20317, 62, 10, 'Coffee Analysis', NULL);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `name_history`
-- 


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
  `revision` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `foreign_id` (`owner`),
  KEY `foreign_id_2` (`foreign_id`),
  KEY `date` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `note`
-- 


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='A phone number';

-- 
-- Dumping data for table `number`
-- 


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
  `reason_code` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `occurences`
-- 


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Stores which items are owned by which user';

-- 
-- Dumping data for table `ownership`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `patient`
-- 

CREATE TABLE `patient` (
  `person_id` int(11) NOT NULL default '0',
  `is_default_provider_primary` int(11) NOT NULL default '0',
  `default_provider` int(11) NOT NULL default '0',
  `record_number` int(11) NOT NULL default '0',
  `employer_name` varchar(255) NOT NULL default '' COMMENT '\0\0\0\0\0\0\0\0\0\0\0!\0\0�',
  PRIMARY KEY  (`person_id`),
  KEY `record_number` (`record_number`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='An patient extends the person entity';

-- 
-- Dumping data for table `patient`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `patient_statistics`
-- 

CREATE TABLE `patient_statistics` (
  `person_id` int(11) NOT NULL default '0',
  `ethnicity` int(11) NOT NULL default '0',
  `race` int(11) NOT NULL default '0',
  `income` int(11) NOT NULL default '0',
  `language` int(11) NOT NULL default '0',
  `migrant_status` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `patient_statistics`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `payment`
-- 

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL default '0',
  `foreign_id` int(11) NOT NULL default '0',
  `encounter_id` int(11) NOT NULL default '0',
  `payment_type` int(11) NOT NULL default '0',
  `amount` float(11,2) NOT NULL default '0.00',
  `writeoff` float(11,2) NOT NULL default '0.00',
  `user_id` int(11) NOT NULL default '0',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `payer_id` int(11) NOT NULL default '0',
  `payment_date` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`payment_id`),
  KEY `foreign_id` (`foreign_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `payment`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `payment_claimline`
-- 

CREATE TABLE `payment_claimline` (
  `payment_claimline_id` int(11) NOT NULL default '0',
  `payment_id` int(11) NOT NULL default '0',
  `code_id` int(11) NOT NULL default '0',
  `paid` float(7,2) NOT NULL default '0.00',
  `writeoff` float(7,2) NOT NULL default '0.00',
  `carry` float(7,2) NOT NULL default '0.00',
  PRIMARY KEY  (`payment_claimline_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `payment_claimline`
-- 


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='A person in the system';

-- 
-- Dumping data for table `person`
-- 


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Links a person to a address specifying the address type';

-- 
-- Dumping data for table `person_address`
-- 


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Links a person to a company and optionaly specifies the lin';

-- 
-- Dumping data for table `person_company`
-- 


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Links between people and phone_numbers';

-- 
-- Dumping data for table `person_number`
-- 


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `person_person`
-- 


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Link to specify person type';

-- 
-- Dumping data for table `person_type`
-- 


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Links a practice to a address specifying the address type';

-- 
-- Dumping data for table `practice_address`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `practice_number`
-- 

CREATE TABLE `practice_number` (
  `practice_id` int(11) NOT NULL default '0',
  `number_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`practice_id`,`number_id`),
  KEY `person_id` (`practice_id`),
  KEY `phone_id` (`number_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Links between people and phone_numbers';

-- 
-- Dumping data for table `practice_number`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `practices`
-- 

CREATE TABLE `practices` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `website` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `practices`
-- 


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `preferences`
-- 


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `provider`
-- 


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `provider_to_insurance`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `record_sequence`
-- 

CREATE TABLE `record_sequence` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `record_sequence`
-- 

INSERT INTO `record_sequence` VALUES (22);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `rooms`
-- 


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `schedules`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `sequences`
-- 

CREATE TABLE `sequences` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `sequences`
-- 

INSERT INTO `sequences` VALUES (200442);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `states`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `storage_date`
-- 

CREATE TABLE `storage_date` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(50) NOT NULL default '',
  `value` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`foreign_key`,`value_key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Generic way to store date values';

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
INSERT INTO `storage_date` VALUES (8223, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (8223, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (8223, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (8223, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (8223, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (8223, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (8223, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (8223, '0', '0000-00-00');
INSERT INTO `storage_date` VALUES (17091, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (17091, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (17091, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (17091, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (17091, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (17091, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (17091, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (17091, '0', '0000-00-00');
INSERT INTO `storage_date` VALUES (17119, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (17119, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (17119, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (17119, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (17119, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (17119, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (17119, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (17119, '0', '0000-00-00');
INSERT INTO `storage_date` VALUES (17146, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (17146, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (17146, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (17146, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (17146, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (17146, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (17146, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (17146, '0', '0000-00-00');
INSERT INTO `storage_date` VALUES (17173, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (17173, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (17173, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (17173, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (17173, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (17173, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (17173, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (17173, '0', '0000-00-00');
INSERT INTO `storage_date` VALUES (17199, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (17199, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (17199, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (17199, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (17199, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (17199, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (17199, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (17199, '0', '0000-00-00');
INSERT INTO `storage_date` VALUES (17233, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (17233, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (17233, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (17233, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (17233, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (17233, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (17233, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (17233, '0', '0000-00-00');
INSERT INTO `storage_date` VALUES (17256, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (17256, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (17256, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (17256, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (17256, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (17256, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (17256, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (17256, '0', '0000-00-00');
INSERT INTO `storage_date` VALUES (17280, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (17280, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (17280, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (17280, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (17280, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (17280, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (17280, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (17280, '0', '0000-00-00');
INSERT INTO `storage_date` VALUES (17306, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (17306, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (17306, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (17306, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (17306, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (17306, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (17306, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (17306, '0', '0000-00-00');
INSERT INTO `storage_date` VALUES (17332, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (17332, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (17332, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (17332, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (17332, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (17332, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (17332, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (17332, '0', '0000-00-00');
INSERT INTO `storage_date` VALUES (17359, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (17359, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (17359, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (17359, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (17359, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (17359, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (17359, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (17359, '0', '0000-00-00');
INSERT INTO `storage_date` VALUES (18441, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (18441, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (18441, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (18441, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (18441, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (18441, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (18441, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (18441, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (18466, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (18466, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (18466, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (18466, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (18466, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (18466, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (18466, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (18466, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (18488, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (18488, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (18488, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (18488, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (18488, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (18488, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (18488, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (18488, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (18512, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (18512, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (18512, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (18512, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (18512, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (18512, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (18512, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (18512, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (18533, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (18533, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (18533, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (18533, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (18533, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (18533, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (18533, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (18533, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (18556, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (18556, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (18556, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (18556, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (18556, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (18556, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (18556, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (18556, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (18577, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (18577, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (18577, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (18577, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (18577, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (18577, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (18577, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (18577, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (18599, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (18599, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (18599, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (18599, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (18599, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (18599, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (18599, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (18599, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (19263, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (19263, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (19263, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (19263, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (19263, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (19263, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (19263, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (19263, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (19284, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (19284, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (19284, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (19284, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (19284, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (19284, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (19284, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (19284, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (19303, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (19303, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (19303, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (19303, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (19303, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (19303, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (19303, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (19303, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (19400, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (19400, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (19400, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (19400, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (19400, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (19400, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (19400, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (19400, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (19476, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (19476, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (19476, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (19476, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (19476, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (19476, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (19476, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (19476, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (19496, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (19496, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (19496, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (19496, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (19496, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (19496, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (19496, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (19496, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (19515, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (19515, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (19515, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (19515, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (19515, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (19515, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (19515, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (19515, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (19535, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (19535, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (19535, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (19535, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (19535, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (19535, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (19535, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (19535, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (19554, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (19554, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (19554, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (19554, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (19554, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (19554, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (19554, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (19554, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (19572, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (19572, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (19572, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (19572, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (19572, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (19572, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (19572, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (19572, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (19591, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (19591, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (19591, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (19591, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (19591, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (19591, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (19591, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (19591, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (19609, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (19609, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (19609, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (19609, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (19609, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (19609, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (19609, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (19609, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (20021, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (20021, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (20021, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (20021, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (20021, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (20021, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (20021, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (20021, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (20127, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (20127, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (20127, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (20127, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (20127, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (20127, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (20127, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (20127, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (20159, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (20159, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (20159, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (20159, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (20159, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (20159, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (20159, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (20159, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (20183, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (20183, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (20183, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (20183, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (20183, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (20183, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (20183, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (20183, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (20207, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (20207, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (20207, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (20207, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (20207, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (20207, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (20207, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (20207, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (20230, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (20230, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (20230, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (20230, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (20230, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (20230, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (20230, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (20230, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (20258, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (20258, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (20258, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (20258, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (20258, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (20258, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (20258, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (20258, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (20280, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (20280, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (20280, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (20280, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (20280, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (20280, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (20280, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (20280, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (20300, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (20300, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (20300, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (20300, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (20300, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (20300, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (20300, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (20300, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (20350, 'test_data', '2005-04-06');
INSERT INTO `storage_date` VALUES (20351, 'test_data', '2005-04-08');
INSERT INTO `storage_date` VALUES (20389, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (20389, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (20389, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (20389, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (20389, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (20389, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (20389, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (20389, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (20408, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (20408, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (20408, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (20408, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (20408, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (20408, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (20408, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (20408, '0', '1969-12-31');
INSERT INTO `storage_date` VALUES (200427, 'date_of_death', '0000-00-00');
INSERT INTO `storage_date` VALUES (200427, 'date_last_seen', '0000-00-00');
INSERT INTO `storage_date` VALUES (200427, 'date_of_onset', '0000-00-00');
INSERT INTO `storage_date` VALUES (200427, 'date_of_initial_treatment', '0000-00-00');
INSERT INTO `storage_date` VALUES (200427, 'date_of_cant_work_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (200427, 'date_of_cant_work_end', '0000-00-00');
INSERT INTO `storage_date` VALUES (200427, 'date_of_hospitalization_start', '0000-00-00');
INSERT INTO `storage_date` VALUES (200427, '0', '1969-12-31');

-- --------------------------------------------------------

-- 
-- Table structure for table `storage_int`
-- 

CREATE TABLE `storage_int` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(50) NOT NULL default '',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`foreign_key`,`value_key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Generic way to store integer values (also boolean)';

-- 
-- Dumping data for table `storage_int`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `storage_string`
-- 

CREATE TABLE `storage_string` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(50) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`foreign_key`,`value_key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Generic way to string values';

-- 
-- Dumping data for table `storage_string`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `storage_text`
-- 

CREATE TABLE `storage_text` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(255) NOT NULL default '',
  `value` longtext NOT NULL,
  PRIMARY KEY  (`foreign_key`,`value_key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Generic way to string values';

-- 
-- Dumping data for table `storage_text`
-- 


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `superbill_data`
-- 


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Users in the System';

-- 
-- Dumping data for table `user`
-- 

INSERT INTO `user` VALUES (1, 'admin', 'admin', '', '', NULL, 'no', 1125);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `users_groups`
-- 

INSERT INTO `users_groups` VALUES (1, 1, 1, 0, '');
INSERT INTO `users_groups` VALUES (634, 306, 1, 0, '');
INSERT INTO `users_groups` VALUES (635, 306, 0, 0, '');
        
