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
) TYPE=MyISAM COMMENT='An address that can be for a company or a person. STARTEMPTY';
CREATE TABLE `adodbseq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM COMMENT='STARTWITHDATA';
CREATE TABLE `appointment_template` (
  `appointment_template_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`appointment_template_id`)
) TYPE=MyISAM;
CREATE TABLE `building_address` (
  `building_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  `address_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`building_id`,`address_id`),
  KEY `address_id` (`address_id`),
  KEY `building_id` (`building_id`)
) TYPE=MyISAM COMMENT='Links a building to a address specifying type. STARTEMPTY';
CREATE TABLE `building_program_identifier` (
  `building_id` int(11) NOT NULL default '0',
  `program_id` int(11) NOT NULL default '0',
  `identifier` varchar(255) NOT NULL default '',
  `x12_sender_id` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`building_id`,`program_id`)
) TYPE=MyISAM;
CREATE TABLE `buildings` (
  `id` int(11) NOT NULL default '0',
  `description` text NOT NULL,
  `name` varchar(255) NOT NULL default '',
  `practice_id` int(11) NOT NULL default '0',
  `identifier` varchar(255) NOT NULL default '',
  `facility_code_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='STARTEMPTY';
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
) TYPE=MyISAM COMMENT='STARTWITHDATA';
INSERT INTO `category` VALUES (1,'ClearHealth','',0,0,6);
CREATE TABLE `category_to_document` (
  `category_id` int(11) NOT NULL default '0',
  `document_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`category_id`,`document_id`)
) TYPE=MyISAM COMMENT='STARTEMPTY';
CREATE TABLE `clearhealth_claim` (
  `claim_id` int(11) NOT NULL default '0',
  `encounter_id` int(11) NOT NULL default '0',
  `identifier` varchar(255) NOT NULL default '',
  `total_billed` float(7,2) NOT NULL default '0.00',
  `total_paid` float(7,2) NOT NULL default '0.00',
  PRIMARY KEY  (`claim_id`)
) TYPE=MyISAM COMMENT='STARTEMPTY';
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
) TYPE=MyISAM; 
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
) TYPE=MyISAM;
CREATE TABLE `company` (
  `company_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `notes` text NOT NULL,
  `initials` varchar(10) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `is_historic` enum('no','yes') NOT NULL default 'no',
  PRIMARY KEY  (`company_id`)
) TYPE=MyISAM COMMENT='Base Company record most of the data is linked in STARTEMPTY';
CREATE TABLE `company_address` (
  `company_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  `address_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`,`address_id`),
  KEY `company_id` (`company_id`),
  KEY `address_id` (`address_id`)
) TYPE=MyISAM COMMENT='Links a company to a address specifying the type STARTEMPTY';
CREATE TABLE `company_company` (
  `company_id` int(11) NOT NULL default '0',
  `related_company_id` int(11) NOT NULL default '0',
  `company_relation_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`,`related_company_id`),
  KEY `company_id` (`company_id`),
  KEY `related_company_id` (`related_company_id`)
) TYPE=MyISAM COMMENT='Relates a company to another company STARTEMPTY';
CREATE TABLE `company_number` (
  `company_id` int(11) NOT NULL default '0',
  `number_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`,`number_id`),
  KEY `company_id` (`company_id`),
  KEY `number_id` (`number_id`)
) TYPE=MyISAM COMMENT='Links between company and phone_numbers STARTEMPTY';
CREATE TABLE `company_type` (
  `company_id` int(11) NOT NULL default '0',
  `company_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`,`company_type`),
  KEY `company_id` (`company_id`),
  KEY `company_type` (`company_type`)
) TYPE=MyISAM COMMENT='Link to specify company type';
CREATE TABLE `countries` (
  `countries_name` varchar(64) NOT NULL default '',
  `countries_iso_code_2` char(2) NOT NULL default '',
  `countries_iso_code_3` char(3) NOT NULL default '',
  PRIMARY KEY  (`countries_iso_code_3`),
  KEY `IDX_COUNTRIES_NAME` (`countries_name`)
) TYPE=MyISAM;
INSERT INTO `countries` VALUES ('Afghanistan','AF','AFG'),('Albania','AL','ALB'),('Algeria','DZ','DZA'),('American Samoa','AS','ASM'),('Andorra','AD','AND'),('Angola','AO','AGO'),('Anguilla','AI','AIA'),('Antarctica','AQ','ATA'),('Antigua and Barbuda','AG','ATG'),('Argentina','AR','ARG'),('Armenia','AM','ARM'),('Aruba','AW','ABW'),('Australia','AU','AUS'),('Austria','AT','AUT'),('Azerbaijan','AZ','AZE'),('Bahamas','BS','BHS'),('Bahrain','BH','BHR'),('Bangladesh','BD','BGD'),('Barbados','BB','BRB'),('Belarus','BY','BLR'),('Belgium','BE','BEL'),('Belize','BZ','BLZ'),('Benin','BJ','BEN'),('Bermuda','BM','BMU'),('Bhutan','BT','BTN'),('Bolivia','BO','BOL'),('Bosnia and Herzegowina','BA','BIH'),('Botswana','BW','BWA'),('Bouvet Island','BV','BVT'),('Brazil','BR','BRA'),('British Indian Ocean Territory','IO','IOT'),('Brunei Darussalam','BN','BRN'),('Bulgaria','BG','BGR'),('Burkina Faso','BF','BFA'),('Burundi','BI','BDI'),('Cambodia','KH','KHM'),('Cameroon','CM','CMR'),('Canada','CA','CAN'),('Cape Verde','CV','CPV'),('Cayman Islands','KY','CYM'),('Central African Republic','CF','CAF'),('Chad','TD','TCD'),('Chile','CL','CHL'),('China','CN','CHN'),('Christmas Island','CX','CXR'),('Cocos (Keeling) Islands','CC','CCK'),('Colombia','CO','COL'),('Comoros','KM','COM'),('Congo','CG','COG'),('Cook Islands','CK','COK'),('Costa Rica','CR','CRI'),('Cote D\'Ivoire','CI','CIV'),('Croatia','HR','HRV'),('Cuba','CU','CUB'),('Cyprus','CY','CYP'),('Czech Republic','CZ','CZE'),('Denmark','DK','DNK'),('Djibouti','DJ','DJI'),('Dominica','DM','DMA'),('Dominican Republic','DO','DOM'),('East Timor','TP','TMP'),('Ecuador','EC','ECU'),('Egypt','EG','EGY'),('El Salvador','SV','SLV'),('Equatorial Guinea','GQ','GNQ'),('Eritrea','ER','ERI'),('Estonia','EE','EST'),('Ethiopia','ET','ETH'),('Falkland Islands (Malvinas)','FK','FLK'),('Faroe Islands','FO','FRO'),('Fiji','FJ','FJI'),('Finland','FI','FIN'),('France','FR','FRA'),('France, Metropolitan','FX','FXX'),('French Guiana','GF','GUF'),('French Polynesia','PF','PYF'),('French Southern Territories','TF','ATF'),('Gabon','GA','GAB'),('Gambia','GM','GMB'),('Georgia','GE','GEO'),('Germany','DE','DEU'),('Ghana','GH','GHA'),('Gibraltar','GI','GIB'),('Greece','GR','GRC'),('Greenland','GL','GRL'),('Grenada','GD','GRD'),('Guadeloupe','GP','GLP'),('Guam','GU','GUM'),('Guatemala','GT','GTM'),('Guinea','GN','GIN'),('Guinea-bissau','GW','GNB'),('Guyana','GY','GUY'),('Haiti','HT','HTI'),('Heard and Mc Donald Islands','HM','HMD'),('Honduras','HN','HND'),('Hong Kong','HK','HKG'),('Hungary','HU','HUN'),('Iceland','IS','ISL'),('India','IN','IND'),('Indonesia','ID','IDN'),('Iran (Islamic Republic of)','IR','IRN'),('Iraq','IQ','IRQ'),('Ireland','IE','IRL'),('Israel','IL','ISR'),('Italy','IT','ITA'),('Jamaica','JM','JAM'),('Japan','JP','JPN'),('Jordan','JO','JOR'),('Kazakhstan','KZ','KAZ'),('Kenya','KE','KEN'),('Kiribati','KI','KIR'),('Korea, Democratic People\'s Republic of','KP','PRK'),('Korea, Republic of','KR','KOR'),('Kuwait','KW','KWT'),('Kyrgyzstan','KG','KGZ'),('Lao People\'s Democratic Republic','LA','LAO'),('Latvia','LV','LVA'),('Lebanon','LB','LBN'),('Lesotho','LS','LSO'),('Liberia','LR','LBR'),('Libyan Arab Jamahiriya','LY','LBY'),('Liechtenstein','LI','LIE'),('Lithuania','LT','LTU'),('Luxembourg','LU','LUX'),('Macau','MO','MAC'),('Macedonia, The Former Yugoslav Republic of','MK','MKD'),('Madagascar','MG','MDG'),('Malawi','MW','MWI'),('Malaysia','MY','MYS'),('Maldives','MV','MDV'),('Mali','ML','MLI'),('Malta','MT','MLT'),('Marshall Islands','MH','MHL'),('Martinique','MQ','MTQ'),('Mauritania','MR','MRT'),('Mauritius','MU','MUS'),('Mayotte','YT','MYT'),('Mexico','MX','MEX'),('Micronesia, Federated States of','FM','FSM'),('Moldova, Republic of','MD','MDA'),('Monaco','MC','MCO'),('Mongolia','MN','MNG'),('Montserrat','MS','MSR'),('Morocco','MA','MAR'),('Mozambique','MZ','MOZ'),('Myanmar','MM','MMR'),('Namibia','NA','NAM'),('Nauru','NR','NRU'),('Nepal','NP','NPL'),('Netherlands','NL','NLD'),('Netherlands Antilles','AN','ANT'),('New Caledonia','NC','NCL'),('New Zealand','NZ','NZL'),('Nicaragua','NI','NIC'),('Niger','NE','NER'),('Nigeria','NG','NGA'),('Niue','NU','NIU'),('Norfolk Island','NF','NFK'),('Northern Mariana Islands','MP','MNP'),('Norway','NO','NOR'),('Oman','OM','OMN'),('Pakistan','PK','PAK'),('Palau','PW','PLW'),('Panama','PA','PAN'),('Papua New Guinea','PG','PNG'),('Paraguay','PY','PRY'),('Peru','PE','PER'),('Philippines','PH','PHL'),('Pitcairn','PN','PCN'),('Poland','PL','POL'),('Portugal','PT','PRT'),('Puerto Rico','PR','PRI'),('Qatar','QA','QAT'),('Reunion','RE','REU'),('Romania','RO','ROM'),('Russian Federation','RU','RUS'),('Rwanda','RW','RWA'),('Saint Kitts and Nevis','KN','KNA'),('Saint Lucia','LC','LCA'),('Saint Vincent and the Grenadines','VC','VCT'),('Samoa','WS','WSM'),('San Marino','SM','SMR'),('Sao Tome and Principe','ST','STP'),('Saudi Arabia','SA','SAU'),('Senegal','SN','SEN'),('Seychelles','SC','SYC'),('Sierra Leone','SL','SLE'),('Singapore','SG','SGP'),('Slovakia (Slovak Republic)','SK','SVK'),('Slovenia','SI','SVN'),('Solomon Islands','SB','SLB'),('Somalia','SO','SOM'),('South Africa','ZA','ZAF'),('South Georgia and the South Sandwich Islands','GS','SGS'),('Spain','ES','ESP'),('Sri Lanka','LK','LKA'),('St. Helena','SH','SHN'),('St. Pierre and Miquelon','PM','SPM'),('Sudan','SD','SDN'),('Suriname','SR','SUR'),('Svalbard and Jan Mayen Islands','SJ','SJM'),('Swaziland','SZ','SWZ'),('Sweden','SE','SWE'),('Switzerland','CH','CHE'),('Syrian Arab Republic','SY','SYR'),('Taiwan','TW','TWN'),('Tajikistan','TJ','TJK'),('Tanzania, United Republic of','TZ','TZA'),('Thailand','TH','THA'),('Togo','TG','TGO'),('Tokelau','TK','TKL'),('Tonga','TO','TON'),('Trinidad and Tobago','TT','TTO'),('Tunisia','TN','TUN'),('Turkey','TR','TUR'),('Turkmenistan','TM','TKM'),('Turks and Caicos Islands','TC','TCA'),('Tuvalu','TV','TUV'),('Uganda','UG','UGA'),('Ukraine','UA','UKR'),('United Arab Emirates','AE','ARE'),('United Kingdom','GB','GBR'),('United States','US','USA'),('United States Minor Outlying Islands','UM','UMI'),('Uruguay','UY','URY'),('Uzbekistan','UZ','UZB'),('Vanuatu','VU','VUT'),('Vatican City State (Holy See)','VA','VAT'),('Venezuela','VE','VEN'),('Viet Nam','VN','VNM'),('Virgin Islands (British)','VG','VGB'),('Virgin Islands (U.S.)','VI','VIR'),('Wallis and Futuna Islands','WF','WLF'),('Western Sahara','EH','ESH'),('Yemen','YE','YEM'),('Yugoslavia','YU','YUG'),('Zaire','ZR','ZAR'),('Zambia','ZM','ZMB'),('Zimbabwe','ZW','ZWE');
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
  `revision` timestamp NOT NULL,
  `foreign_id` int(11) default NULL,
  `group_id` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `revision` (`revision`),
  KEY `foreign_id` (`foreign_id`),
  KEY `owner` (`owner`)
) TYPE=MyISAM;
CREATE TABLE `encounter` (
  `encounter_id` int(11) NOT NULL default '0',
  `encounter_reason` int(11) NOT NULL default '0',
  `patient_id` int(11) NOT NULL default '0',
  `building_id` int(11) NOT NULL default '0',
  `date_of_treatment` datetime NOT NULL default '0000-00-00 00:00:00',
  `treating_person_id` int(11) NOT NULL default '0',
  `timestamp` timestamp NOT NULL,
  `last_change_user_id` int(11) NOT NULL default '0',
  `status` enum('closed','open','billed') NOT NULL default 'open',
  `occurence_id` int(11) default NULL,
  `created_by_user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`encounter_id`),
  KEY `building_id` (`building_id`),
  KEY `treating_person_id` (`treating_person_id`),
  KEY `last_change_user_id` (`last_change_user_id`)
) TYPE=MyISAM;
CREATE TABLE `encounter_date` (
  `encounter_date_id` int(11) NOT NULL default '0',
  `encounter_id` int(11) NOT NULL default '0',
  `date_type` int(11) NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`encounter_date_id`),
  KEY `encounter_id` (`encounter_id`)
) TYPE=MyISAM;
CREATE TABLE `encounter_person` (
  `encounter_person_id` int(11) NOT NULL default '0',
  `encounter_id` int(11) NOT NULL default '0',
  `person_type` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`encounter_person_id`),
  KEY `encounter_id` (`encounter_id`)
) TYPE=MyISAM;
CREATE TABLE `encounter_value` (
  `encounter_value_id` int(11) NOT NULL default '0',
  `encounter_id` int(11) NOT NULL default '0',
  `value_type` int(11) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '0',
  PRIMARY KEY  (`encounter_value_id`),
  KEY `encounter_id` (`encounter_id`)
) TYPE=MyISAM;
CREATE TABLE `enumeration_value` (
  `enumeration_value_id` int(11) NOT NULL default '0',
  `enumeration_id` int(11) NOT NULL default '0',
  `key` int(11) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  `sort` int(11) NOT NULL default '0',
  `extra1` varchar(255) NOT NULL default '',
  `extra2` varchar(255) NOT NULL default '',
  `status` int(1) NOT NULL default '1',
  PRIMARY KEY  (`enumeration_value_id`)
) TYPE=MyISAM;


-- 
-- Table structure for table `enumeration_definition`
-- 

CREATE TABLE `enumeration_definition` (
  `enumeration_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `type` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`enumeration_id`),
  UNIQUE KEY `name` (`name`)
) TYPE=MyISAM;


CREATE TABLE `enumeration_value_practice` (
`enumeration_value_id` INT NOT NULL ,
`practice_id` INT NOT NULL ,
PRIMARY KEY ( `enumeration_value_id` , `practice_id` )
);


INSERT INTO `enumeration_definition` VALUES (300466,'address_type','Address Type','Default');
INSERT INTO `enumeration_definition` VALUES (300472,'appointment_reasons','Appointment Reason','AppointmentReason');
INSERT INTO `enumeration_definition` VALUES (300480,'assigning','Assigning','Default');
INSERT INTO `enumeration_definition` VALUES (300485,'code_modifier','Code Modifier','Default');
INSERT INTO `enumeration_definition` VALUES (300492,'company_number_type','Company Number Type','Default');
INSERT INTO `enumeration_definition` VALUES (300495,'company_type','Company Type','Default');
INSERT INTO `enumeration_definition` VALUES (300497,'disposition','Disposition','Default');
INSERT INTO `enumeration_definition` VALUES (300501,'encounter_date_type','Encounter Date Type','Default');
INSERT INTO `enumeration_definition` VALUES (300510,'encounter_person_type','Encounter Person Type','Default');
INSERT INTO `enumeration_definition` VALUES (300512,'encounter_reason','Encounter Reason','Default');
INSERT INTO `enumeration_definition` VALUES (300515,'encounter_value_type','Encounter Value Type','Default');
INSERT INTO `enumeration_definition` VALUES (300521,'ethnicity','Ethnicity','Default');
INSERT INTO `enumeration_definition` VALUES (300524,'gender','Gender','Default');
INSERT INTO `enumeration_definition` VALUES (300528,'group_list','File Groups','Default');
INSERT INTO `enumeration_definition` VALUES (300532,'identifier_type','Identifier Type','Default');
INSERT INTO `enumeration_definition` VALUES (300535,'income','Income','Default');
INSERT INTO `enumeration_definition` VALUES (300540,'language','Languages','Default');
INSERT INTO `enumeration_definition` VALUES (300560,'marital_status','Marital Status','Default');
INSERT INTO `enumeration_definition` VALUES (300564,'migrant_status','Migrant Status','Default');
INSERT INTO `enumeration_definition` VALUES (300566,'number_type','Phone Number Type','Default');
INSERT INTO `enumeration_definition` VALUES (300572,'payer_type','Payer Type','Default');
INSERT INTO `enumeration_definition` VALUES (300582,'payment_type','Payment Type','Default');
INSERT INTO `enumeration_definition` VALUES (300589,'person_to_person_relation_type','Person to person relation type','Default');
INSERT INTO `enumeration_definition` VALUES (300594,'person_type','Person Type','PersonType');
INSERT INTO `enumeration_definition` VALUES (300300,'provider_number_type','Provider Number Type','Default');
INSERT INTO `enumeration_definition` VALUES (300602,'provider_reporting_type','Provider Reporting Type','Default');
INSERT INTO `enumeration_definition` VALUES (300608,'quality_of_file','Quality of File','Default');
INSERT INTO `enumeration_definition` VALUES (300611,'race','Race','Default');
INSERT INTO `enumeration_definition` VALUES (300617,'relation_of_information_code','Relation Of Information Code','Default');
INSERT INTO `enumeration_definition` VALUES (300624,'state','State','Default');
INSERT INTO `enumeration_definition` VALUES (300677,'subscriber_to_patient','Subscriber to patient','Default');
INSERT INTO `enumeration_definition` VALUES (300525,'system_reports','System Reports','Url');
INSERT INTO `enumeration_definition` VALUES (300818,'chronic_care_codes','Chronic Care Codes','Default');
INSERT INTO `enumeration_definition` VALUES (300852,'funds_source','Funds Source','Default');
INSERT INTO `enumeration_definition` VALUES (601041,'depression','Depression','Appointment Reason');

INSERT INTO `enumeration_value` VALUES (300013,300012,1,'Hello',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300014,300012,2,'World',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300016,300015,1,'test',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300017,300015,2,'second test',1,'','',1);
INSERT INTO `enumeration_value` VALUES (300039,300038,1,'Home',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300040,300038,2,'Billing',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300041,300038,3,'Other',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300042,300038,4,'Main',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300043,300038,5,'Secondary',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300045,300044,1,'Physical',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300046,300044,2,'FP',1,'','',1);
INSERT INTO `enumeration_value` VALUES (300047,300044,3,'CDP',2,'','',1);
INSERT INTO `enumeration_value` VALUES (300048,300044,4,'CHDP',3,'','',1);
INSERT INTO `enumeration_value` VALUES (300049,300044,5,'F/U',4,'','',1);
INSERT INTO `enumeration_value` VALUES (300050,300044,6,'Sick',5,'','',1);
INSERT INTO `enumeration_value` VALUES (300051,300044,7,'Lab Only',6,'','',1);
INSERT INTO `enumeration_value` VALUES (300053,300052,1,'A - Assigned',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300054,300052,2,'B - Assigned Lab Services Only',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300055,300052,3,'C - Not Assigned',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300056,300052,4,'P - Assignment Refused',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300058,300057,1,'A0',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300059,300057,2,'A1',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300060,300057,3,'A2',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300061,300057,4,'B1',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300062,300057,5,'B2',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300063,300057,6,'C6',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300065,300064,1,'Primary',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300066,300064,2,'Fax',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300068,300067,1,'Insurance',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300070,300069,1,'New',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300071,300069,2,'Waiting',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300072,300069,3,'Compete',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300074,300073,1,'date_of_death',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300075,300073,2,'date_last_seen',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300076,300073,3,'date_of_onset',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300077,300073,4,'date_of_initial_treatment',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300078,300073,5,'date_of_cant_work_start',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300079,300073,6,'date_of_cant_work_end',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300080,300073,7,'date_of_hospitalization_start',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300081,300073,8,'date_of_hospitalization_end',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300083,300082,1,'Referring Provider',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300085,300084,1,'Physical',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300086,300084,2,'Other',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300088,300087,1,'medicaid_resubmission_code',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300089,300087,2,'prior_authorization_number',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300090,300087,3,'auto_accident_state',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300091,300087,4,'original_reference_number',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300092,300087,5,'hcfa_10d_comment',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300094,300093,1,'Hispanic',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300095,300093,2,'Caucasian',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300097,300096,1,'Male',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300098,300096,2,'Female',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300099,300096,3,'Unknown',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300101,300100,1,'All',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300102,300100,2,'Arizona',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300103,300100,3,'California',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300105,300104,1,'SSN',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300106,300104,2,'EIN',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300108,300107,1,'Unknown',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300109,300107,2,'Under 100% of Poverty',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300110,300107,3,'100-200% of Poverty',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300111,300107,4,'Above 200% of Poverty',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300113,300112,1,'English',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300114,300112,2,'Spanish',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300115,300112,3,'Chinese',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300116,300112,4,'Japanese',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300117,300112,5,'Korean',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300118,300112,6,'Portuguese',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300119,300112,7,'Russian',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300120,300112,8,'Sign Language',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300121,300112,9,'Vietnamese',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300122,300112,10,'Tagalog',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300123,300112,11,'Punjabi',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300124,300112,12,'Hindustani',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300125,300112,13,'Armenian',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300126,300112,14,'Arabic',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300127,300112,15,'Laotian',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300128,300112,16,'Hmong',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300129,300112,17,'Cambodian',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300130,300112,18,'Finnish',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300131,300112,19,'Other',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300133,300132,1,'Single',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300134,300132,2,'Married',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300135,300132,3,'Other',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300137,300136,1,'Migrant Worker',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300139,300138,1,'Home',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300140,300138,2,'Mobile',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300141,300138,3,'Work',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300142,300138,4,'Emergency',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300143,300138,5,'Fax',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300145,300144,1,'medicare',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300146,300144,2,'champus',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300147,300144,3,'medical',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300148,300144,4,'private',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300149,300144,5,'feca',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300150,300144,6,'medicaid',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300151,300144,7,'champusva',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300152,300144,8,'otherhcfa',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300153,300144,9,'litigation',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300155,300154,1,'visa',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300156,300154,2,'mastercard',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300157,300154,3,'amex',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300158,300154,4,'check',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300159,300154,5,'cash',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300160,300154,6,'remittance',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300162,300161,1,'Dependant',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300163,300161,2,'Spouse',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300164,300161,3,'Grand Parent',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300165,300161,4,'Other',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300167,300166,1,'Patient',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300168,300166,2,'Provider',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300169,300166,3,'Mid-level',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300170,300166,4,'Staff',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300171,300166,5,'Subscriber',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300173,300172,1,'State License',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300175,300174,1,'MD',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300176,300174,2,'RNFP',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300177,300174,3,'RN',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300178,300174,4,'PA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300179,300174,5,'MA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300181,300180,1,'Good',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300182,300180,2,'Bad',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300184,300183,1,'White/Hispanic',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300185,300183,2,'Black',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300186,300183,3,'Native American/Alaskan Native',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300187,300183,4,'Asian/Pacific Islander',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300188,300183,5,'Other/Unknown',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300190,300189,1,'A - On file',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300191,300189,2,'I - Informed Consent',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300192,300189,3,'M - Limited Ability',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300193,300189,4,'N - Not allowed',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300194,300189,5,'O - On file',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300195,300189,6,'Y - Has permission',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300197,300196,1,'AL',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300198,300196,2,'AK',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300199,300196,3,'AZ',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300200,300196,4,'AR',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300201,300196,5,'CA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300202,300196,6,'CO',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300203,300196,7,'CT',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300204,300196,8,'DE',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300205,300196,9,'DC',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300206,300196,10,'FL',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300207,300196,11,'GA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300208,300196,12,'HI',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300209,300196,13,'ID',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300210,300196,14,'IL',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300211,300196,15,'IN',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300212,300196,16,'IA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300213,300196,17,'KS',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300214,300196,18,'KY',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300215,300196,19,'LA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300216,300196,20,'ME',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300217,300196,21,'MD',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300218,300196,22,'MA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300219,300196,23,'MI',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300220,300196,24,'MN',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300221,300196,25,'MS',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300222,300196,26,'MO',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300223,300196,27,'MT',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300224,300196,28,'NE',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300225,300196,29,'NV',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300226,300196,30,'NH',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300227,300196,31,'NJ',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300228,300196,32,'NM',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300229,300196,33,'NY',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300230,300196,34,'NC',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300231,300472,1,'Cleaning',0,'300228','',1);
INSERT INTO `enumeration_value` VALUES (300232,300196,36,'OH',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300233,300196,37,'OK',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300234,300196,38,'OR',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300235,300196,39,'PA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300236,300196,40,'RI',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300237,300196,41,'SC',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300238,300196,42,'SD',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300239,300196,43,'TN',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300240,300196,44,'TX',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300241,300196,45,'UT',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300242,300196,46,'VT',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300243,300196,47,'VA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300244,300196,48,'WA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300245,300196,49,'WV',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300246,300196,50,'WI',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300247,300196,51,'WY',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300248,300196,52,'PR',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300250,300249,1,'Spouse',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300251,300249,2,'Parent',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300253,300252,1,'Home',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300254,300252,2,'Billing',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300255,300252,3,'Other',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300256,300472,0,'Root Canal',0,'300265','',1);
INSERT INTO `enumeration_value` VALUES (300257,300472,0,'',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300259,300258,1,'Physical',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300260,300258,2,'FP',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300261,300258,3,'CDP',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300262,300258,4,'CHDP',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300263,300258,5,'F/U',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300264,300258,6,'Sick',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300265,300258,7,'Lab Only',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300267,300266,1,'A - Assigned',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300291,300287,4,'date_of_initial_treatment',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300292,300287,5,'date_of_cant_work_start',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300293,300287,6,'date_of_cant_work_end',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300294,300287,7,'date_of_hospitalization_start',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300295,300287,8,'date_of_hospitalization_end',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300297,300296,1,'Referring Provider',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300299,300298,1,'Physical',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300300,300298,2,'Other',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300302,300301,1,'medicaid_resubmission_code',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300305,300472,2,'Root Canal',2,'300265','',1);
INSERT INTO `enumeration_value` VALUES (300306,300301,5,'hcfa_10d_comment',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300308,300307,1,'Hispanic',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300309,300307,2,'Caucasian',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300311,300310,1,'Male',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300312,300310,2,'Female',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300313,300310,3,'Unknown',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300315,300314,1,'All',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300316,300314,2,'Arizona',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300317,300314,3,'California',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300319,300318,1,'SSN',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300320,300318,2,'EIN',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300322,300321,1,'Unknown',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300323,300321,2,'Under 100% of Poverty',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300324,300321,3,'100-200% of Poverty',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300325,300321,4,'Above 200% of Poverty',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300327,300326,1,'English',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300328,300326,2,'Spanish',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300329,300326,3,'Chinese',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300330,300326,4,'Japanese',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300331,300326,5,'Korean',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300332,300326,6,'Portuguese',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300333,300326,7,'Russian',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300334,300326,8,'Sign Language',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300335,300326,9,'Vietnamese',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300336,300326,10,'Tagalog',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300337,300326,11,'Punjabi',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300338,300326,12,'Hindustani',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300339,300326,13,'Armenian',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300340,300326,14,'Arabic',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300341,300326,15,'Laotian',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300342,300326,16,'Hmong',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300343,300326,17,'Cambodian',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300344,300326,18,'Finnish',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300345,300472,3,'Extractions',1,'300327','',1);
INSERT INTO `enumeration_value` VALUES (300347,300346,1,'Single',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300348,300346,2,'Married',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300349,300346,3,'Other',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300351,300350,1,'Migrant Worker',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300353,300352,1,'Home',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300354,300352,2,'Mobile',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300355,300352,3,'Work',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300356,300352,4,'Emergency',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300357,300352,5,'Fax',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300359,300358,1,'medicare',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300360,300358,2,'champus',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300361,300358,3,'medical',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300362,300358,4,'private',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300363,300358,5,'feca',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300364,300358,6,'medicaid',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300365,300358,7,'champusva',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300366,300358,8,'otherhcfa',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300367,300358,9,'litigation',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300369,300368,1,'visa',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300370,300368,2,'mastercard',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300371,300368,3,'amex',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300372,300368,4,'check',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300373,300368,5,'cash',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300374,300368,6,'remittance',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300376,300375,1,'Dependant',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300377,300375,2,'Spouse',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300378,300375,3,'Grand Parent',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300379,300375,4,'Other',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300381,300380,1,'Patient',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300382,300380,2,'Provider',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300383,300380,3,'Mid-level',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300384,300380,4,'Staff',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300385,300380,5,'Subscriber',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300387,300386,1,'State License',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300389,300388,1,'MD',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300390,300388,2,'RNFP',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300391,300388,3,'RN',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300392,300388,4,'PA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300393,300388,5,'MA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300395,300394,1,'Good',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300396,300394,2,'Bad',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300398,300397,1,'White/Hispanic',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300399,300397,2,'Black',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300400,300397,3,'Native American/Alaskan Native',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300401,300397,4,'Asian/Pacific Islander',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300402,300397,5,'Other/Unknown',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300404,300403,1,'A - On file',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300405,300403,2,'I - Informed Consent',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300406,300403,3,'M - Limited Ability',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300407,300403,4,'N - Not allowed',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300408,300403,5,'O - On file',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300409,300403,6,'Y - Has permission',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300411,300410,1,'AL',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300412,300410,2,'AK',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300413,300410,3,'AZ',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300414,300410,4,'AR',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300415,300410,5,'CA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300416,300410,6,'CO',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300417,300410,7,'CT',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300418,300410,8,'DE',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300419,300410,9,'DC',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300420,300410,10,'FL',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300421,300410,11,'GA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300422,300410,12,'HI',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300423,300410,13,'ID',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300424,300410,14,'IL',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300425,300410,15,'IN',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300426,300410,16,'IA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300427,300410,17,'KS',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300428,300410,18,'KY',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300429,300410,19,'LA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300430,300410,20,'ME',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300431,300410,21,'MD',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300432,300410,22,'MA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300433,300410,23,'MI',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300434,300410,24,'MN',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300435,300410,25,'MS',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300436,300410,26,'MO',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300437,300410,27,'MT',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300438,300410,28,'NE',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300439,300410,29,'NV',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300440,300410,30,'NH',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300441,300410,31,'NJ',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300442,300410,32,'NM',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300443,300410,33,'NY',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300444,300410,34,'NC',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300445,300410,35,'ND',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300446,300410,36,'OH',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300447,300410,37,'OK',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300448,300410,38,'OR',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300449,300410,39,'PA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300450,300410,40,'RI',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300451,300410,41,'SC',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300452,300410,42,'SD',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300453,300410,43,'TN',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300454,300410,44,'TX',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300455,300410,45,'UT',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300456,300410,46,'VT',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300457,300410,47,'VA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300458,300410,48,'WA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300459,300410,49,'WV',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300460,300410,50,'WI',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300461,300410,51,'WY',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300462,300410,52,'PR',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300464,300463,1,'Spouse',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300465,300463,2,'Parent',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300467,300466,2,'Home',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300468,300466,1,'Billing',1,'','',1);
INSERT INTO `enumeration_value` VALUES (300469,300466,3,'Other',2,'','',1);
INSERT INTO `enumeration_value` VALUES (300470,300466,4,'Main',3,'','',1);
INSERT INTO `enumeration_value` VALUES (300471,300466,5,'Secondary',4,'','',1);
INSERT INTO `enumeration_value` VALUES (300473,300472,1,'Physical',5,'','',1);
INSERT INTO `enumeration_value` VALUES (300474,300472,2,'FP',6,'','',1);
INSERT INTO `enumeration_value` VALUES (300475,300472,3,'CDP',7,'','',1);
INSERT INTO `enumeration_value` VALUES (300476,300472,4,'CHDP',8,'','',1);
INSERT INTO `enumeration_value` VALUES (300477,300472,5,'F/U',9,'','',1);
INSERT INTO `enumeration_value` VALUES (300478,300472,6,'Sick',10,'','',1);
INSERT INTO `enumeration_value` VALUES (300479,300472,7,'Lab Only',11,'','',1);
INSERT INTO `enumeration_value` VALUES (300481,300480,1,'A - Assigned',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300482,300480,2,'B - Assigned Lab Services Only',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300483,300480,3,'C - Not Assigned',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300484,300480,4,'P - Assignment Refused',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300486,300485,1,'A0',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300487,300485,2,'A1',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300488,300485,3,'A2',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300489,300485,4,'B1',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300490,300485,5,'B2',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300491,300485,6,'C6',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300493,300492,1,'Primary',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300494,300492,2,'Fax',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300496,300495,1,'Insurance',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300498,300497,1,'New',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300499,300497,2,'Waiting',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300500,300497,3,'Compete',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300502,300501,1,'date_of_death',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300503,300501,2,'date_last_seen',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300504,300501,3,'date_of_onset',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300505,300501,4,'date_of_initial_treatment',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300506,300501,5,'date_of_cant_work_start',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300507,300501,6,'date_of_cant_work_end',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300508,300501,7,'date_of_hospitalization_start',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300509,300501,8,'date_of_hospitalization_end',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300511,300510,1,'Referring Provider',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300513,300512,1,'Physical',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300514,300512,2,'Other',1,'','',1);
INSERT INTO `enumeration_value` VALUES (300516,300515,1,'medicaid_resubmission_code',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300517,300515,2,'prior_authorization_number',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300518,300515,3,'auto_accident_state',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300519,300515,4,'original_reference_number',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300520,300515,5,'hcfa_10d_comment',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300522,300521,1,'Hispanic',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300523,300521,2,'Caucasian',1,'','',1);
INSERT INTO `enumeration_value` VALUES (300525,300524,1,'Male',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300526,300525,1,'Patient Statement',0,'/Patient/statement','',1);
INSERT INTO `enumeration_value` VALUES (300527,300524,3,'Unknown',2,'','',1);
INSERT INTO `enumeration_value` VALUES (300529,300528,1,'All',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300530,300528,2,'Arizona',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300531,300528,3,'California',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300533,300532,1,'SSN',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300534,300532,2,'EIN',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300536,300535,1,'Unknown',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300537,300535,2,'Under 100% of Poverty',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300538,300535,3,'100-200% of Poverty',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300539,300535,4,'Above 200% of Poverty',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300541,300540,1,'English',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300542,300540,2,'Spanish',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300543,300540,3,'Chinese',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300544,300540,4,'Japanese',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300545,300540,5,'Korean',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300546,300540,6,'Portuguese',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300547,300540,7,'Russian',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300548,300540,8,'Sign Language',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300549,300540,9,'Vietnamese',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300550,300540,10,'Tagalog',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300551,300540,11,'Punjabi',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300552,300540,12,'Hindustani',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300553,300540,13,'Armenian',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300554,300540,14,'Arabic',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300555,300540,15,'Laotian',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300556,300540,16,'Hmong',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300557,300540,17,'Cambodian',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300558,300540,18,'Finnish',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300559,300540,19,'Other',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300561,300560,1,'Single',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300562,300560,2,'Married',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300563,300560,3,'Other',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300565,300564,1,'Migrant Worker',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300567,300566,1,'Home',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300568,300566,2,'Mobile',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300569,300566,3,'Work',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300570,300566,4,'Emergency',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300571,300566,5,'Fax',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300573,300572,1,'medicare',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300574,300572,2,'champus',2,'','',1);
INSERT INTO `enumeration_value` VALUES (300575,300572,3,'medical',3,'','',1);
INSERT INTO `enumeration_value` VALUES (300576,300572,4,'private pay',4,'','',1);
INSERT INTO `enumeration_value` VALUES (300577,300572,5,'feca',5,'','',1);
INSERT INTO `enumeration_value` VALUES (300578,300572,6,'medicaid',6,'','',1);
INSERT INTO `enumeration_value` VALUES (300579,300572,7,'champusva',7,'','',1);
INSERT INTO `enumeration_value` VALUES (300580,300572,8,'otherhcfa',8,'','',1);
INSERT INTO `enumeration_value` VALUES (300581,300572,9,'litigation',9,'','',1);
INSERT INTO `enumeration_value` VALUES (300583,300582,1,'visa',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300584,300582,2,'mastercard',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300585,300582,3,'amex',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300586,300582,4,'check',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300587,300582,5,'cash',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300588,300582,6,'remittance',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300590,300589,1,'Dependant',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300591,300589,2,'Spouse',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300592,300589,3,'Grand Parent',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300593,300589,4,'Other',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300595,300594,1,'Patient',0,'0','',1);
INSERT INTO `enumeration_value` VALUES (300596,300594,2,'Provider',1,'1','',1);
INSERT INTO `enumeration_value` VALUES (300597,300594,3,'Mid-level',2,'1','',1);
INSERT INTO `enumeration_value` VALUES (300598,300594,4,'Staff',3,'1','',1);
INSERT INTO `enumeration_value` VALUES (300599,300594,5,'Subscriber',4,'0','',1);
INSERT INTO `enumeration_value` VALUES (300601,300300,1,'State License',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300603,300602,1,'MD',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300604,300602,2,'RNFP',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300605,300602,3,'RN',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300606,300602,4,'PA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300607,300602,5,'MA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300609,300608,1,'Good',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300610,300608,2,'Bad',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300612,300611,1,'White/Hispanic',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300613,300611,2,'Black',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300614,300611,3,'Native American/Alaskan Native',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300615,300611,4,'Asian/Pacific Islander',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300616,300611,5,'Other/Unknown',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300618,300617,1,'A - On file',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300619,300617,2,'I - Informed Consent',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300620,300617,3,'M - Limited Ability',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300621,300617,4,'N - Not allowed',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300622,300617,5,'O - On file',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300623,300617,6,'Y - Has permission',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300625,300624,1,'AL',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300626,300624,2,'AK',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300627,300624,3,'AZ',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300628,300624,4,'AR',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300629,300624,5,'CA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300630,300624,6,'CO',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300631,300624,7,'CT',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300632,300624,8,'DE',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300633,300624,9,'DC',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300634,300624,10,'FL',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300635,300624,11,'GA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300636,300624,12,'HI',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300637,300624,13,'ID',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300638,300624,14,'IL',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300639,300624,15,'IN',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300640,300624,16,'IA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300641,300624,17,'KS',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300642,300624,18,'KY',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300643,300624,19,'LA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300644,300624,20,'ME',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300645,300624,21,'MD',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300646,300624,22,'MA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300647,300624,23,'MI',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300648,300624,24,'MN',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300649,300624,25,'MS',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300650,300525,2,'Family Patient Statement',1,'/Patient/familyStatement','',1);
INSERT INTO `enumeration_value` VALUES (300651,300525,3,'Pull List',2,'/Appointment/pullList','',1);
INSERT INTO `enumeration_value` VALUES (300652,300472,1,'Physical',5,'','',1);
INSERT INTO `enumeration_value` VALUES (300653,300472,2,'FP',6,'','',1);
INSERT INTO `enumeration_value` VALUES (300654,300472,3,'CDP',7,'','',1);
INSERT INTO `enumeration_value` VALUES (300655,300472,4,'CHDP',8,'','',1);
INSERT INTO `enumeration_value` VALUES (300656,300472,5,'F/U',9,'','',1);
INSERT INTO `enumeration_value` VALUES (300657,300472,6,'Sick',10,'','',1);
INSERT INTO `enumeration_value` VALUES (300658,300472,7,'Lab Only',11,'','',1);
INSERT INTO `enumeration_value` VALUES (300659,300624,35,'ND',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300660,300624,36,'OH',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300661,300624,37,'OK',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300662,300624,38,'OR',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300663,300624,39,'PA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300664,300624,40,'RI',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300665,300624,41,'SC',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300666,300624,42,'SD',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300667,300624,43,'TN',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300668,300624,44,'TX',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300669,300624,45,'UT',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300670,300624,46,'VT',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300671,300624,47,'VA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300672,300624,48,'WA',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300673,300624,49,'WV',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300674,300624,50,'WI',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300675,300624,51,'WY',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300676,300624,52,'PR',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300678,300677,1,'Spouse',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300679,300677,2,'Parent',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300701,300472,1,'Cleaning',0,'300228','',1);
INSERT INTO `enumeration_value` VALUES (300747,300525,4,'Route Slip',3,'/Encounter/routeSlip','',1);
INSERT INTO `enumeration_value` VALUES (300819,300818,1,'Diabetes',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300820,300818,2,'Hypertension',2,'','',1);
INSERT INTO `enumeration_value` VALUES (300853,300852,1,'Patient',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300854,300852,2,'Private Insurance',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300855,300852,3,'State Program',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300856,300852,4,'Federal Program',0,'','',1);
INSERT INTO `enumeration_value` VALUES (300932,300818,3,'hrt',1,'','',1);
INSERT INTO `enumeration_value` VALUES (301031,300472,1,'Physical',5,'','',1);
INSERT INTO `enumeration_value` VALUES (301032,300472,2,'FP',6,'','',1);
INSERT INTO `enumeration_value` VALUES (301033,300472,3,'CDP',7,'','',1);
INSERT INTO `enumeration_value` VALUES (301034,300472,4,'CHDP',8,'','',1);
INSERT INTO `enumeration_value` VALUES (301035,300472,5,'F/U',9,'','',1);
INSERT INTO `enumeration_value` VALUES (301036,300472,6,'Sick',10,'','',1);
INSERT INTO `enumeration_value` VALUES (301037,300472,7,'Lab Only',11,'','',1);
INSERT INTO `enumeration_value` VALUES (301038,300472,8,'Cleaning',0,'601027','',1);
INSERT INTO `enumeration_value` VALUES (301042,300472,9,'Depression',0,'601027','',1);
INSERT INTO `enumeration_value` VALUES (301504,300466,6,'',5,'','',0);
INSERT INTO `enumeration_value` VALUES (301505,300524,2,'Female',0,'','',1);
INSERT INTO `enumeration_value` VALUES (301506,300512,3,'medical appt',0,'','',1);
INSERT INTO `enumeration_value` VALUES (301507,300521,3,'Asian',0,'','',1);
INSERT INTO `enumeration_value` VALUES (301508,300572,10,' private insurance',1,'','',1);
INSERT INTO `enumeration_value` VALUES (301522,300564,2,'Seasonal Worker',0,'','',1);
INSERT INTO `enumeration_value` VALUES (301523,300564,3,'No',0,'','',1);
INSERT INTO `enumeration_value` VALUES (301524,300564,4,'other',0,'','',1);
INSERT INTO `enumeration_value` VALUES (301538,300818,4,'Hypercholestrolemia',0,'','',1);
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
INSERT INTO `events` VALUES (502532,'No Shows','','','','',502530),(502530,'','','','','',0),(502535,'Cancelations','','','','',502530);
CREATE TABLE `facility_codes` (
  `facility_code_id` int(11) NOT NULL auto_increment,
  `code` varchar(5) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`facility_code_id`)
) TYPE=MyISAM COMMENT='Stores x12 facility_code code/human name combos';
INSERT INTO `facility_codes` VALUES (1,'11','Office'),(2,'12','Home'),(3,'21','Inpatient Hospital'),(4,'22','Outpatient Hospital'),(5,'23','Emergency Room - Hospital'),(6,'24','Ambulatory Surgical Center'),(7,'25','Birthing Center'),(8,'26','Military Treatment Facility'),(9,'31','Skilled Nursing Facility'),(10,'32','Nursing Facility'),(11,'33','Custodial Care Facility'),(12,'34','Hospice'),(13,'41','Ambulance - Land'),(14,'42','Ambulance - Air or Water'),(15,'51','Inpatient Psychiatric Facility'),(16,'52','Psychiatric Facility Partial Hospitalization'),(17,'53','Community Mental Health Center'),(18,'54','Intermediate Care Facility/Mentally Retarded'),(19,'55','Residential Substance Abuse Treatment Facility'),(20,'56','Psychiatric Residential Treatment Center'),(21,'50','Federally Qualified Health Center'),(22,'60','Mass Immunization Center'),(23,'61','Comprehensive Inpatient Rehabilitation Facility'),(24,'62','Comprehensive Outpatient Rehabilitation Facility'),(25,'65','End Stage Renal Disease Treatment Facility'),(26,'71','State or Local Public Health Clinic'),(27,'72','Rural Health Clinic'),(28,'81','Independent Laboratory'),(29,'99','Other Unlisted Facility');
CREATE TABLE `fee_schedule` (
  `fee_schedule_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `label` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `priority` int(11) NOT NULL default '2',
  PRIMARY KEY  (`fee_schedule_id`),
  UNIQUE KEY `name` (`name`)
) TYPE=MyISAM;
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
) TYPE=MyISAM;
CREATE TABLE `fee_schedule_revision` (
  `revision_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `update_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`revision_id`),
  KEY `user_id` (`user_id`)
) TYPE=MyISAM;
CREATE TABLE `fee_schedule_discount` (
  `fee_schedule_discount_id` int(11) NOT NULL default '0',
  `practice_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`fee_schedule_discount_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE `fee_schedule_discount_income` (
  `fee_schedule_discount_income_id` int(11) NOT NULL default '0',
  `fee_schedule_discount_id` int(11) NOT NULL default '0',
  `fee_schedule_discount_level_id` int(11) NOT NULL default '0',
  `family_size` int(11) NOT NULL default '0',
  `income` float(9,2) NOT NULL default '0.00',
  PRIMARY KEY  (`fee_schedule_discount_income_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE `fee_schedule_discount_level` (
  `fee_schedule_discount_level_id` int(11) NOT NULL default '0',
  `fee_schedule_discount_id` int(11) NOT NULL default '0',
  `discount` float(5,2) NOT NULL default '0.00',
  `disp_order` int(11) NOT NULL default '0',
  PRIMARY KEY  (`fee_schedule_discount_level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `form` (
  `form_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`form_id`)
) TYPE=MyISAM COMMENT='Contains the EMR extending forms STARTWITHDATA';
INSERT INTO `form` VALUES (800,'Test Data','Some random data'),(1710,'Patient Vitals','Patient Vital Statistics');
CREATE TABLE `form_data` (
  `form_data_id` int(11) NOT NULL default '0',
  `form_id` int(11) NOT NULL default '0',
  `external_id` int(11) NOT NULL default '0',
  `last_edit` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`form_data_id`)
) TYPE=MyISAM COMMENT='Links in the form data STARTWITHDATA';
-- phpMyAdmin SQL Dump
-- version 2.6.1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Oct 17, 2005 at 04:30 PM
-- Server version: 4.0.18
-- PHP Version: 4.3.4
-- 
-- Database: `clearhealth`
-- 

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

-- 
-- Dumping data for table `gacl_acl`
-- 

INSERT INTO `gacl_acl` VALUES (26, 'user', 1, 1, '', 'Give Superadmn and access to everything even when no resource is selected', 1129066391);
INSERT INTO `gacl_acl` VALUES (24, 'user', 1, 1, '', 'Give Super Admin access to everything ', 1129066383);
INSERT INTO `gacl_acl` VALUES (38, 'user', 1, 1, '', '', 1129066412);
INSERT INTO `gacl_acl` VALUES (40, 'user', 1, 1, '', '', 1129066435);
INSERT INTO `gacl_acl` VALUES (36, 'user', 1, 1, '', '', 1129066460);
INSERT INTO `gacl_acl` VALUES (37, 'user', 1, 1, '', '', 1119041365);
INSERT INTO `gacl_acl` VALUES (32, 'user', 1, 1, '', 'Give billing users basic access to those sections', 1129066489);
INSERT INTO `gacl_acl` VALUES (33, 'user', 1, 1, '', 'Give all users of the system access to basic app sections', 1112057091);
INSERT INTO `gacl_acl` VALUES (39, 'user', 1, 1, '', '', 1129066506);

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
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_acl_seq`
-- 

INSERT INTO `gacl_acl_seq` VALUES (40);
INSERT INTO `gacl_acl_seq` VALUES (40);

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
) TYPE=MyISAM;

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
INSERT INTO `gacl_aco_map` VALUES (32, 'actions', 'add');
INSERT INTO `gacl_aco_map` VALUES (32, 'actions', 'delete');
INSERT INTO `gacl_aco_map` VALUES (32, 'actions', 'edit');
INSERT INTO `gacl_aco_map` VALUES (32, 'actions', 'usage');
INSERT INTO `gacl_aco_map` VALUES (32, 'actions', 'view');
INSERT INTO `gacl_aco_map` VALUES (33, 'actions', 'usage');
INSERT INTO `gacl_aco_map` VALUES (33, 'actions', 'view');
INSERT INTO `gacl_aco_map` VALUES (36, 'actions', 'usage');
INSERT INTO `gacl_aco_map` VALUES (36, 'actions', 'view');
INSERT INTO `gacl_aco_map` VALUES (37, 'actions', 'add');
INSERT INTO `gacl_aco_map` VALUES (37, 'actions', 'delete_owner');
INSERT INTO `gacl_aco_map` VALUES (37, 'actions', 'edit');
INSERT INTO `gacl_aco_map` VALUES (37, 'actions', 'usage');
INSERT INTO `gacl_aco_map` VALUES (37, 'actions', 'view');
INSERT INTO `gacl_aco_map` VALUES (38, 'actions', 'add');
INSERT INTO `gacl_aco_map` VALUES (38, 'actions', 'delete');
INSERT INTO `gacl_aco_map` VALUES (38, 'actions', 'delete_owner');
INSERT INTO `gacl_aco_map` VALUES (38, 'actions', 'edit');
INSERT INTO `gacl_aco_map` VALUES (38, 'actions', 'usage');
INSERT INTO `gacl_aco_map` VALUES (38, 'actions', 'view');
INSERT INTO `gacl_aco_map` VALUES (39, 'actions', 'add');
INSERT INTO `gacl_aco_map` VALUES (39, 'actions', 'delete');
INSERT INTO `gacl_aco_map` VALUES (39, 'actions', 'double_book');
INSERT INTO `gacl_aco_map` VALUES (39, 'actions', 'edit');
INSERT INTO `gacl_aco_map` VALUES (39, 'actions', 'uploadFile');
INSERT INTO `gacl_aco_map` VALUES (39, 'actions', 'usage');
INSERT INTO `gacl_aco_map` VALUES (39, 'actions', 'view');
INSERT INTO `gacl_aco_map` VALUES (40, 'actions', 'add');
INSERT INTO `gacl_aco_map` VALUES (40, 'actions', 'edit');
INSERT INTO `gacl_aco_map` VALUES (40, 'actions', 'usage');
INSERT INTO `gacl_aco_map` VALUES (40, 'actions', 'view');

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

CREATE TABLE `gacl_aco_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

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
) TYPE=MyISAM;

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
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_aro_groups`
-- 

INSERT INTO `gacl_aro_groups` VALUES (10, 0, 1, 26, 'Root', 'root');
INSERT INTO `gacl_aro_groups` VALUES (12, 23, 11, 12, 'System Admin', 'admin');
INSERT INTO `gacl_aro_groups` VALUES (19, 10, 2, 9, 'User Types', 'users');
INSERT INTO `gacl_aro_groups` VALUES (20, 19, 3, 4, 'Provider', 'provider');
INSERT INTO `gacl_aro_groups` VALUES (21, 19, 5, 6, 'Mid-level', 'mid-level');
INSERT INTO `gacl_aro_groups` VALUES (22, 19, 7, 8, 'Staff', 'staff');
INSERT INTO `gacl_aro_groups` VALUES (23, 10, 10, 25, 'Roles', 'roles');
INSERT INTO `gacl_aro_groups` VALUES (24, 23, 13, 14, 'Supervisor', 'supervisor');
INSERT INTO `gacl_aro_groups` VALUES (26, 23, 15, 16, 'Front Office', 'front_office');
INSERT INTO `gacl_aro_groups` VALUES (31, 23, 23, 24, 'Staff', 'role_staff');
INSERT INTO `gacl_aro_groups` VALUES (28, 23, 17, 18, 'Biller', 'billing_user');
INSERT INTO `gacl_aro_groups` VALUES (29, 23, 19, 20, 'Medical Assistant', 'medical_assistant');

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_groups_id_seq`
-- 

CREATE TABLE `gacl_aro_groups_id_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_aro_groups_id_seq`
-- 

INSERT INTO `gacl_aro_groups_id_seq` VALUES (31);
INSERT INTO `gacl_aro_groups_id_seq` VALUES (31);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_aro_groups_map`
-- 

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
INSERT INTO `gacl_aro_groups_map` VALUES (32, 28);
INSERT INTO `gacl_aro_groups_map` VALUES (33, 20);
INSERT INTO `gacl_aro_groups_map` VALUES (33, 21);
INSERT INTO `gacl_aro_groups_map` VALUES (33, 22);
INSERT INTO `gacl_aro_groups_map` VALUES (36, 31);
INSERT INTO `gacl_aro_groups_map` VALUES (37, 31);
INSERT INTO `gacl_aro_groups_map` VALUES (38, 29);
INSERT INTO `gacl_aro_groups_map` VALUES (39, 24);
INSERT INTO `gacl_aro_groups_map` VALUES (40, 28);

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

-- 
-- Dumping data for table `gacl_aro_map`
-- 


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

CREATE TABLE `gacl_aro_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_aro_seq`
-- 

INSERT INTO `gacl_aro_seq` VALUES (39);
INSERT INTO `gacl_aro_seq` VALUES (39);

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
INSERT INTO `gacl_axo` VALUES (69, 'resources', 'appointment', 10, 'Section - Appointment', 0);

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
) TYPE=MyISAM;

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
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_axo_groups_map`
-- 

INSERT INTO `gacl_axo_groups_map` VALUES (24, 11);

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

-- 
-- Dumping data for table `gacl_axo_map`
-- 

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
INSERT INTO `gacl_axo_map` VALUES (36, 'resources', 'calendar');
INSERT INTO `gacl_axo_map` VALUES (36, 'resources', 'location');
INSERT INTO `gacl_axo_map` VALUES (36, 'resources', 'main_calendar');
INSERT INTO `gacl_axo_map` VALUES (36, 'resources', 'main_patient');
INSERT INTO `gacl_axo_map` VALUES (36, 'resources', 'patient');
INSERT INTO `gacl_axo_map` VALUES (36, 'resources', 'patientfinder');
INSERT INTO `gacl_axo_map` VALUES (37, 'resources', 'appointment');
INSERT INTO `gacl_axo_map` VALUES (37, 'resources', 'calendar');
INSERT INTO `gacl_axo_map` VALUES (37, 'resources', 'location');
INSERT INTO `gacl_axo_map` VALUES (37, 'resources', 'patient');
INSERT INTO `gacl_axo_map` VALUES (37, 'resources', 'patientfinder');
INSERT INTO `gacl_axo_map` VALUES (38, 'resources', 'appointment');
INSERT INTO `gacl_axo_map` VALUES (38, 'resources', 'calendar');
INSERT INTO `gacl_axo_map` VALUES (38, 'resources', 'location');
INSERT INTO `gacl_axo_map` VALUES (38, 'resources', 'main_calendar');
INSERT INTO `gacl_axo_map` VALUES (38, 'resources', 'patient');
INSERT INTO `gacl_axo_map` VALUES (38, 'resources', 'patientfinder');
INSERT INTO `gacl_axo_map` VALUES (39, 'resources', 'appointment');
INSERT INTO `gacl_axo_map` VALUES (39, 'resources', 'calendar');
INSERT INTO `gacl_axo_map` VALUES (39, 'resources', 'event');
INSERT INTO `gacl_axo_map` VALUES (39, 'resources', 'location');
INSERT INTO `gacl_axo_map` VALUES (39, 'resources', 'main_calendar');
INSERT INTO `gacl_axo_map` VALUES (39, 'resources', 'occurence');
INSERT INTO `gacl_axo_map` VALUES (39, 'resources', 'patient');
INSERT INTO `gacl_axo_map` VALUES (39, 'resources', 'patientfinder');
INSERT INTO `gacl_axo_map` VALUES (39, 'resources', 'schedule');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'admin');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'appointment');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'billing');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'calendar');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'claim');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'coding');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'eob');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'event');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'feeschedule');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'insurance');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'location');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'main_billing');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'main_calendar');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'main_patient');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'occurence');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'patient');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'patientfinder');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'personschedule');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'practice');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'schedule');
INSERT INTO `gacl_axo_map` VALUES (40, 'resources', 'superbill');

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
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_axo_sections_seq`
-- 

INSERT INTO `gacl_axo_sections_seq` VALUES (24);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_axo_seq`
-- 

CREATE TABLE `gacl_axo_seq` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- 
-- Dumping data for table `gacl_axo_seq`
-- 

INSERT INTO `gacl_axo_seq` VALUES (69);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_groups_aro_map`
-- 

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
INSERT INTO `gacl_groups_axo_map` VALUES (11, 69);

-- --------------------------------------------------------

-- 
-- Table structure for table `gacl_phpgacl`
-- 

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
CREATE TABLE `group_occurence` (
  `group_occurence_id` int(11) NOT NULL default '0',
  `occurence_id` int(11) NOT NULL default '0',
  `patient_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`group_occurence_id`),
  UNIQUE KEY `occurence_id` (`occurence_id`,`patient_id`)
) TYPE=MyISAM;
CREATE TABLE `groups` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
INSERT INTO `groups` VALUES (1,'superadmin'),(2,'practice_admin'),(3,'usage'),(0,'provider');
CREATE TABLE `identifier` (
  `identifier_id` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  `identifier` varchar(100) NOT NULL default '',
  `identifier_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`identifier_id`)
) TYPE=MyISAM;
CREATE TABLE `import_map` (
  `old_id` int(11) NOT NULL default '0',
  `new_id` int(11) default NULL,
  `old_table_name` varchar(100) NOT NULL default '',
  `new_object_name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`old_id`,`old_table_name`)
) TYPE=MyISAM;
CREATE TABLE `insurance` (
  `company_id` int(11) NOT NULL default '0',
  `fee_schedule_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`company_id`),
  KEY `fee_schedule_id` (`fee_schedule_id`)
) TYPE=MyISAM;
CREATE TABLE `insurance_program` (
  `insurance_program_id` int(11) NOT NULL default '0',
  `payer_type` int(11) NOT NULL default '0',
  `company_id` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `fee_schedule_id` int(11) NOT NULL default '0',
  `x12_sender_id` varchar(255) NOT NULL default '',
  `x12_receiver_id` varchar(255) NOT NULL default '',
  `x12_version` varchar(255) NOT NULL default '',
  `address_id` int(11) NOT NULL default '0',
  `funds_source` int(11) NOT NULL default '0',
  PRIMARY KEY  (`insurance_program_id`),
  KEY `fee_schedule_id` (`fee_schedule_id`)
) TYPE=MyISAM;
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
  `effective_start` date NOT NULL default '0000-00-00',
  `effective_end` date NOT NULL default '0000-00-00',
  `active` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`insured_relationship_id`)
) TYPE=MyISAM;
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
) TYPE=MyISAM;
INSERT INTO `menu` VALUES (1,'',1,'','children',0,'','','main'),(2,'default',39,'','children',100,'Logout','Access/logout','main'),(3,'default',39,'','children',10,'Preferences','Preferences/list','main'),(4,'admin',1,'','children',800,'Reports','','main/Admin'),(5,'admin',1,'','children',400,'Schedules','',''),(7,'admin',5,'','children',10,'Add New Schedule','Location/edit_schedule','main'),(8,'default',5,'','children',20,'Add New Practice','Location/edit_practive','main'),(9,'admin',80,'','children',30,'Add New Building','Location/edit_building','main'),(10,'admin',80,'','children',40,'Add New Room','Location/edit_room','main'),(82,'admin',26,'','children',10,'List Forms','Form/list','main'),(12,'default',65,'','children',10,'Day','Calendar/day','main'),(13,'default',65,'','children',50,'Week Brief','Calendar/week','main'),(14,'default',65,'','children',20,'Week','Calendar/week_grid','main'),(15,'default',65,'','children',30,'Month','Calendar/month','main'),(16,'default',65,'','children',40,'Day Brief','Calendar/day_brief','main'),(17,'default',65,'','children',60,'Search','Calendar/search','main'),(18,'admin',45,'','children',10,'List Fee Schedules','FeeSchedule/default','main'),(19,'admin',45,'','children',20,'Add Fee Schedule','FeeSchedule/edit','main'),(20,'admin',4,'','children',10,'Add Report','Report/edit','main'),(21,'admin',81,'','children',10,'List Users','User/list','main'),(22,'admin',81,'','children',20,'Add User','User/edit','main'),(80,'admin',1,'','children',100,'Facilities','',''),(24,'admin',110,'','children',30,'List Enumerations','Enumeration/list','main'),(25,'admin',110,'','children',40,'Add Enumeration','Enumeration/edit','main'),(26,'admin',1,'','children',750,'Forms','',''),(27,'admin',26,'','children',20,'Add Form','Form/edit','main'),(28,'admin',26,'','children',30,'View Form Data','Form/view','main'),(29,'patient',68,'','children',10,'Fillout Form','Form/fillout','main'),(30,'patient',1,'','children',100,'Patients','',''),(31,'patient',30,'','children',20,'Add Patient','Patient/edit','main'),(32,'admin',109,'','children',160,'List Payers','Insurance/list','main'),(33,'admin',109,'','children',170,'Add Payer','Insurance/edit','main'),(36,'admin',110,'','children',50,'Document Categories','DocumentCategory/list','main'),(37,'patient',68,'','children',20,'Documents','Document/list','main'),(38,'admin',45,'','children',30,'Edit Superbill','Superbill/list','main'),(39,'default',1,'','children',300,'My Account','','main'),(81,'admin',1,'','children',300,'Users','',''),(42,'billing',1,'','children',300,'Reports','','main/Billing'),(43,'default',1,'','children',200,'Reports','','main/Calendar'),(44,'patient',1,'','children',300,'Reports','','main/Patient'),(45,'admin',1,'','children',200,'Billing','','main'),(46,'patient',1,'','children',400,'My Account','','main'),(47,'patient',46,'','children',100,'Logout','Access/logout','main'),(48,'patient',46,'','children',10,'Preferences','Preferences/list','main'),(49,'billing',1,'','children',500,'My Account','','main'),(57,'billing',49,'','children',100,'Logout','Access/logout','main'),(58,'billing',49,'','children',10,'Preferences','Preferences/list','main'),(59,'admin',1,'','children',900,'My Account','','main'),(60,'admin',59,'','children',100,'Logout','Access/logout','main'),(61,'admin',59,'','children',10,'Preferences','Preferences/list','main'),(62,'billing',1,'','children',100,'Claims','','freeb2'),(63,'billing',62,'','children',10,'List Claims','Claim/list','freeb2'),(64,'billing',62,'','children',20,'Add Claim','Claim/edit','freeb2'),(65,'default',1,'','children',100,'View','',''),(66,'default',1,'','children',400,'Help','',''),(67,'patient',30,'','children',10,'List Patients','Patient/list','main'),(68,'patient',1,'','children',200,'Actions','',''),(69,'patient',30,'','children',30,'Search','PatientFinder/find','main'),(70,'patient',68,'','children',30,'Encounter','Encounter/add','main'),(71,'default',66,'','children',10,'API Docs','Docs/api','main'),(72,'patient',68,'','children',5,'Dashboard','PatientDashboard/view','main'),(74,'patient',1,'','children',500,'Help','',''),(75,'patient',74,'','children',10,'API Docs','Docs/api','main'),(76,'billing',1,'','children',600,'Help','',''),(77,'billing',76,'','children',10,'API Docs','Docs/api','main'),(78,'admin',1,'','children',1000,'Help','',''),(79,'admin',78,'','children',10,'API Docs','Docs/api','main'),(83,'admin',80,'','children',5,'List Facilities','Location/list','main'),(84,'admin',80,'','children',20,'Add New Practice','Location/edit_practice','main'),(85,'admin',4,'','children',5,'List Reports','Report/list','main'),(86,'admin',1,'','children',900,'','Admin/default','main'),(87,'admin',4,'','children',50,'Connect Report','Report/connect','main'),(88,'billing',1,'','children',0,'','Billing/default','main'),(89,'patient',1,'','children',-1,'Dashboard Reports','','main/Patient'),(90,'patient',1,'','children',-1,'Dashboard Forms','','main/Patient'),(91,'patient',1,'','children',-1,'Encounter Forms','','main/Encounter'),(92,'admin',26,'','children',100,'Connect','Form/connect','main'),(93,'billing',1,'','children',0,'','Eob/Payment','main'),(94,'default',39,'','children',50,'Change Password','MyAccount/password','main'),(95,'patient',46,'','children',50,'Change Password','MyAccount/password','main'),(96,'billing',49,'','children',50,'Change Password','MyAccount/password','main'),(97,'admin',59,'','children',50,'Change Password','MyAccount/password','main'),(98,'admin',110,'','children',800,'ACL Editor','Admin/acl','main'),(100,'admin',5,'','children',4,'List Schedules','Location/schedules','main'),(101,'default',1,'','children',700,'Admin','','main'),(102,'default',101,'','children',10,'Add New Schedule','Location/edit_schedule','main'),(103,'default',101,'','children',4,'List Schedules','Location/schedules','main'),(104,'billing',1,'','children',800,'Admin','','main'),(105,'billing',104,'','children',160,'List Payers','Insurance/list','main'),(106,'billing',104,'','children',170,'Add Payers','Insurance/edit','main'),(109,'admin',1,'','children',250,'Payers','','main'),(110,'admin',1,'','children',700,'System','','main'),(111,'default',0,'','children',0,'','','main'),
('112', 'admin', '1', '', 'children', '5000', 'Practice', '', 'main'), 
('113', 'billing', '1', '', 'children', '5000', 'Practice', '', 'main'), 
('114', 'patient', '1', '', 'children', '5000', 'Practice', '', 'main'), 
('115', 'default', '1', '', 'children', '5000', 'Practice', '', 'main'),
('116', 'admin', '5', '', 'children', '300', 'List Appointment Templates', 'AppointmentTemplate/list', 'main'),
('117', 'admin', '5', '', 'children', '310', 'Add Appointment Template', 'AppointmentTemplate/add', 'main'),
('118', 'admin', '5', '', 'children', '0', 'Edit Appointment Template', 'AppointmentTemplate/edit', 'main');
CREATE TABLE `menu_form` (
  `menu_form_id` int(11) NOT NULL default '0',
  `menu_id` int(11) NOT NULL default '0',
  `form_id` int(11) NOT NULL default '0',
  `title` varchar(50) NOT NULL default '',
  `custom_action` varchar(255) default NULL,
  PRIMARY KEY  (`menu_form_id`),
  KEY `menu_id` (`menu_id`),
  KEY `form_id` (`form_id`)
) TYPE=MyISAM;
INSERT INTO `menu_form` VALUES (505001,91,1710,'Patient Vitals',NULL);
CREATE TABLE `menu_report` (
  `menu_report_id` int(11) NOT NULL default '0',
  `menu_id` int(11) NOT NULL default '0',
  `report_template_id` int(11) NOT NULL default '0',
  `title` varchar(50) NOT NULL default '',
  `custom_action` varchar(255) default NULL,
  PRIMARY KEY  (`menu_report_id`),
  KEY `menu_id` (`menu_id`),
  KEY `report_template_id` (`report_template_id`)
) TYPE=MyISAM;
CREATE TABLE `name_history` (
  `name_history_id` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  `first_name` varchar(100) NOT NULL default '',
  `last_name` varchar(100) NOT NULL default '',
  `middle_name` varchar(50) NOT NULL default '',
  `update_date` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`name_history_id`)
) TYPE=MyISAM;
CREATE TABLE `note` (
  `id` int(11) NOT NULL default '0',
  `foreign_id` int(11) NOT NULL default '0',
  `note` varchar(255) default NULL,
  `owner` int(11) default NULL,
  `date` datetime default NULL,
  `revision` timestamp NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `foreign_id` (`owner`),
  KEY `foreign_id_2` (`foreign_id`),
  KEY `date` (`date`)
) TYPE=MyISAM;
CREATE TABLE `number` (
  `number_id` int(11) NOT NULL default '0',
  `number_type` int(11) NOT NULL default '0',
  `notes` tinytext NOT NULL,
  `number` varchar(100) NOT NULL default '',
  `active` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`number_id`)
) TYPE=MyISAM COMMENT='A phone number';
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
  `timestamp` timestamp NOT NULL,
  `walkin` tinyint(4) NOT NULL default '0',
  `group_appointment` tinyint(4) NOT NULL default '0',
  `creator_id` int(11) default 0,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

CREATE TABLE `occurence_breakdown` (
  `occurence_breakdown_id` int(11) NOT NULL default '0',
  `occurence_id` int(11) NOT NULL default '0',
  `index` int(11) default '0',
  `offset` int(11) NOT NULL default '0',
  `length` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`occurence_breakdown_id`)
) TYPE=MyISAM;

CREATE TABLE `ownership` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `id` (`id`)
) TYPE=MyISAM;
CREATE TABLE `patient` (
  `person_id` int(11) NOT NULL default '0',
  `is_default_provider_primary` int(11) NOT NULL default '0',
  `default_provider` int(11) NOT NULL default '0',
  `record_number` int(11) NOT NULL default '0',
  `employer_name` varchar(255) NOT NULL default '' COMMENT '\0\0\0\0\0\0\0\0\0\0\0!\0\0ï¿½',
  PRIMARY KEY  (`person_id`),
  KEY `record_number` (`record_number`)
) TYPE=MyISAM COMMENT='An patient extends the person entity';
CREATE TABLE `patient_note` (
  `patient_note_id` int(11) NOT NULL default '0',
  `patient_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `priority` int(11) NOT NULL default '0',
  `note_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `note` text NOT NULL,
  `deprecated` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`patient_note_id`)
) TYPE=MyISAM;
CREATE TABLE `patient_statistics` (
  `person_id` int(11) NOT NULL default '0',
  `ethnicity` int(11) NOT NULL default '0',
  `race` int(11) NOT NULL default '0',
  `income` int(11) NOT NULL default '0',
  `language` int(11) NOT NULL default '0',
  `migrant_status` int(11) NOT NULL default '0',
  `registration_location` int(11) NOT NULL default '0',
  `sign_in_date` date NOT NULL default '0000-00-00',
  `monthly_income` int(11) NOT NULL default '0',
  `family_size` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`)
) TYPE=MyISAM;
CREATE TABLE `patient_chronic_code` (
`patient_id` int( 11 ) NOT NULL default '0',
`chronic_care_code` int( 11 ) NOT NULL default '0',
PRIMARY KEY ( `patient_id` , `chronic_care_code` )
);
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
  `title` varchar(255) NOT NULL default '',
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
  `marital_status` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`)
) TYPE=MyISAM COMMENT='A person in the system';
CREATE TABLE `person_address` (
  `person_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  `address_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`,`address_id`),
  KEY `address_id` (`address_id`),
  KEY `person_id` (`person_id`)
) TYPE=MyISAM COMMENT='Links a person to a address specifying the address type';
CREATE TABLE `person_company` (
  `person_id` int(11) NOT NULL default '0',
  `company_id` int(11) NOT NULL default '0',
  `person_type` int(11) default NULL,
  PRIMARY KEY  (`person_id`,`company_id`),
  KEY `person_id` (`person_id`),
  KEY `company_id` (`company_id`)
) TYPE=MyISAM COMMENT='Links a person to a company and optionaly specifies the lin';
CREATE TABLE `person_number` (
  `person_id` int(11) NOT NULL default '0',
  `number_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`,`number_id`),
  KEY `person_id` (`person_id`),
  KEY `phone_id` (`number_id`)
) TYPE=MyISAM COMMENT='Links between people and phone_numbers';
CREATE TABLE `person_person` (
  `person_person_id` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  `related_person_id` int(11) NOT NULL default '0',
  `relation_type` int(11) NOT NULL default '0',
  `guarantor` tinyint(1) NOT NULL default '0',
  `guarantor_priority` int(11) NOT NULL default'0',
  PRIMARY KEY  (`person_person_id`),
  UNIQUE KEY `person_id` (`person_id`,`related_person_id`,`relation_type`)
) TYPE=MyISAM;
CREATE TABLE `person_type` (
  `person_id` int(11) NOT NULL default '0',
  `person_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`,`person_type`),
  KEY `person_id` (`person_id`),
  KEY `person_type` (`person_type`)
) TYPE=MyISAM COMMENT='Link to specify person type';
CREATE TABLE `practice_address` (
  `practice_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  `address_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`practice_id`,`address_id`),
  KEY `address_id` (`address_id`),
  KEY `practice_id` (`practice_id`)
) TYPE=MyISAM COMMENT='Links a practice to a address specifying the address type';
CREATE TABLE `practice_number` (
  `practice_id` int(11) NOT NULL default '0',
  `number_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`practice_id`,`number_id`),
  KEY `person_id` (`practice_id`),
  KEY `phone_id` (`number_id`)
) TYPE=MyISAM COMMENT='Links between people and phone_numbers';
CREATE TABLE `practice_setting` (
  `practice_setting_id` int(11) NOT NULL default '0',
  `practice_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `value` text NOT NULL,
  `serialized` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`practice_setting_id`),
  UNIQUE KEY `practice_id` (`practice_id`,`name`)
) TYPE=MyISAM;
CREATE TABLE `practices` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `website` varchar(255) NOT NULL default '',
  `identifier` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
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
INSERT INTO `preferences` VALUES (9000,'Defaults','',0,1,4),(9001,'Special Event Color','#123444',9000,2,3);
CREATE TABLE `provider` (
  `person_id` int(11) NOT NULL default '0',
  `state_license_number` varchar(100) NOT NULL default '',
  `clia_number` varchar(100) NOT NULL default '',
  `dea_number` varchar(100) NOT NULL default '',
  `bill_as` int(11) NOT NULL default '0',
  `report_as` int(11) NOT NULL default '0',
  PRIMARY KEY  (`person_id`)
) TYPE=MyISAM;
CREATE TABLE `provider_to_insurance` (
  `provider_to_insurance_id` int(11) NOT NULL default '0',
  `person_id` int(11) NOT NULL default '0',
  `insurance_program_id` int(11) NOT NULL default '0',
  `provider_number` varchar(100) NOT NULL default '',
  `provider_number_type` int(11) NOT NULL default '0',
  `group_number` varchar(100) NOT NULL default '',
  `building_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`provider_to_insurance_id`)
) TYPE=MyISAM;
CREATE TABLE `record_sequence` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
INSERT INTO `record_sequence` VALUES (200000);
CREATE TABLE `report_templates` (
  `report_template_id` int(11) NOT NULL default '0',
  `report_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `is_default` enum('yes','no') NOT NULL default 'yes',
  `sequence` int(11) NOT NULL default '100000',
  PRIMARY KEY  (`report_template_id`),
  KEY `report_id` (`report_id`)
) TYPE=MyISAM COMMENT='Report templates';
CREATE TABLE `report_snapshot` (
  `report_snapshot_id` int(11) NOT NULL default '0',
  `report_id` int(11) NOT NULL default '0',
  `template_id` int(11) NOT NULL default '0',
  `snapshot_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `data` longtext NOT NULL,
  PRIMARY KEY  (`report_snapshot_id`)
);
CREATE TABLE `reports` (
  `id` int(11) NOT NULL auto_increment,
  `dbase` varchar(255) NOT NULL default '',
  `user` varchar(255) NOT NULL default '',
  `label` varchar(255) NOT NULL default '',
  `query` text NOT NULL,
  `description` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='Report definitions TODO: change to Generic Seq';
CREATE TABLE `rooms` (
  `id` int(11) NOT NULL default '0',
  `description` text NOT NULL,
  `number_seats` int(11) NOT NULL default '0',
  `building_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;
CREATE TABLE `route_slip` (
`route_slip_id` INT NOT NULL ,
`encounter_id` INT NOT NULL ,
`report_date` DATETIME NOT NULL ,
PRIMARY KEY ( `route_slip_id` )
);

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
INSERT INTO `schedules` VALUES (502530,'NS','No Shows','This is primarily for reporting purposes. When a patients appointment is set to no-show. They are assigned to this schedule. You must have two events groups names \"No Shows\" and \"Cancelations\"','Schedule No Shows are assigned to',0,0,0),(502531,'ADM','Admin Events','Anything added to the admin schedule will appear on every calendar. Use this for practice-wide meetings.','Admin Events appear on every schedule',0,0,0);
CREATE TABLE `sequences` (
  `id` int(11) NOT NULL default '0'
) TYPE=MyISAM;
INSERT INTO `sequences` VALUES (600000);
CREATE TABLE `statement_history` (
  `statement_history_id` int(11) NOT NULL default '0',
  `patient_id` INT NOT NULL,
  `report_statement_id` INT NOT NULL default '0',
  `statement_number` int(11) NOT NULL default '0',
  `date_generated` datetime NOT NULL default '0000-00-00 00:00:00',
  `amount` float(7,2) NOT NULL default '0.00',
  `type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`statement_history_id`)
);
CREATE TABLE `states` (
  `zone_code` varchar(32) NOT NULL default '',
  `zone_name` varchar(32) NOT NULL default '',
  `country` char(3) default NULL,
  PRIMARY KEY  (`zone_code`,`zone_name`),
  KEY `country` (`country`),
  KEY `zone_code` (`zone_code`)
) TYPE=MyISAM;
INSERT INTO `states` VALUES ('AL','Alabama','USA'),('AK','Alaska','USA'),('AS','American Samoa','USA'),('AZ','Arizona','USA'),('AR','Arkansas','USA'),('AF','Armed Forces Africa','USA'),('AA','Armed Forces Americas','USA'),('AC','Armed Forces Canada','USA'),('AE','Armed Forces Europe','USA'),('AM','Armed Forces Middle East','USA'),('AP','Armed Forces Pacific','USA'),('CA','California','USA'),('CO','Colorado','USA'),('CT','Connecticut','USA'),('DE','Delaware','USA'),('DC','District of Columbia','USA'),('FM','Federated States Of Micronesia','USA'),('FL','Florida','USA'),('GA','Georgia','USA'),('GU','Guam','USA'),('HI','Hawaii','USA'),('ID','Idaho','USA'),('IL','Illinois','USA'),('IN','Indiana','USA'),('IA','Iowa','USA'),('KS','Kansas','USA'),('KY','Kentucky','USA'),('LA','Louisiana','USA'),('ME','Maine','USA'),('MH','Marshall Islands','USA'),('MD','Maryland','USA'),('MA','Massachusetts','USA'),('MI','Michigan','USA'),('MN','Minnesota','USA'),('MS','Mississippi','USA'),('MO','Missouri','USA'),('MT','Montana','USA'),('NE','Nebraska','USA'),('NV','Nevada','USA'),('NH','New Hampshire','USA'),('NJ','New Jersey','USA'),('NM','New Mexico','USA'),('NY','New York','USA'),('NC','North Carolina','USA'),('ND','North Dakota','USA'),('MP','Northern Mariana Islands','USA'),('OH','Ohio','USA'),('OK','Oklahoma','USA'),('OR','Oregon','USA'),('PW','Palau','USA'),('PA','Pennsylvania','USA'),('PR','Puerto Rico','USA'),('RI','Rhode Island','USA'),('SC','South Carolina','USA'),('SD','South Dakota','USA'),('TN','Tennessee','USA'),('TX','Texas','USA'),('UT','Utah','USA'),('VT','Vermont','USA'),('VI','Virgin Islands','USA'),('VA','Virginia','USA'),('WA','Washington','USA'),('WV','West Virginia','USA'),('WI','Wisconsin','USA'),('WY','Wyoming','USA'),('AB','Alberta','CAN'),('BC','British Columbia','CAN'),('MB','Manitoba','CAN'),('NF','Newfoundland','CAN'),('NB','New Brunswick','CAN'),('NS','Nova Scotia','CAN'),('NT','Northwest Territories','CAN'),('NU','Nunavut','CAN'),('ON','Ontario','CAN'),('PE','Prince Edward Island','CAN'),('QC','Quebec','CAN'),('SK','Saskatchewan','CAN'),('YT','Yukon Territory','CAN'),('NDS','Niedersachsen','DEU'),('BAW','Baden-WÃ¼rttemberg','DEU'),('BAY','Bayern','DEU'),('BER','Berlin','DEU'),('BRG','Brandenburg','DEU'),('BRE','Bremen','DEU'),('HAM','Hamburg','DEU'),('HES','Hessen','DEU'),('MEC','Mecklenburg-Vorpommern','DEU'),('NRW','Nordrhein-Westfalen','DEU'),('RHE','Rheinland-Pfalz','DEU'),('SAR','Saarland','DEU'),('SAS','Sachsen','DEU'),('SAC','Sachsen-Anhalt','DEU'),('SCN','Schleswig-Holstein','DEU'),('THE','ThÃ¼ringen','DEU'),('WI','Wien','AUT'),('NO','NiederÃ¶sterreich','AUT'),('OO','OberÃ¶sterreich','AUT'),('SB','Salzburg','AUT'),('KN','KÃ¤rnten','AUT'),('ST','Steiermark','AUT'),('TI','Tirol','AUT'),('BL','Burgenland','AUT'),('VB','Voralberg','AUT'),('AG','Aargau','CHE'),('AI','Appenzell Innerrhoden','CHE'),('AR','Appenzell Ausserrhoden','CHE'),('BE','Bern','CHE'),('BL','Basel-Landschaft','CHE'),('BS','Basel-Stadt','CHE'),('FR','Freiburg','CHE'),('GE','Genf','CHE'),('GL','Glarus','CHE'),('JU','GraubÃ¼nden','CHE'),('JU','Jura','CHE'),('LU','Luzern','CHE'),('NE','Neuenburg','CHE'),('NW','Nidwalden','CHE'),('OW','Obwalden','CHE'),('SG','St. Gallen','CHE'),('SH','Schaffhausen','CHE'),('SO','Solothurn','CHE'),('SZ','Schwyz','CHE'),('TG','Thurgau','CHE'),('TI','Tessin','CHE'),('UR','Uri','CHE'),('VD','Waadt','CHE'),('VS','Wallis','CHE'),('ZG','Zug','CHE'),('ZH','ZÃ¼rich','CHE'),('A CoruÃ±a','A CoruÃ±a','ESP'),('Alava','Alava','ESP'),('Albacete','Albacete','ESP'),('Alicante','Alicante','ESP'),('Almeria','Almeria','ESP'),('Asturias','Asturias','ESP'),('Avila','Avila','ESP'),('Badajoz','Badajoz','ESP'),('Baleares','Baleares','ESP'),('Barcelona','Barcelona','ESP'),('Burgos','Burgos','ESP'),('Caceres','Caceres','ESP'),('Cadiz','Cadiz','ESP'),('Cantabria','Cantabria','ESP'),('Castellon','Castellon','ESP'),('Ceuta','Ceuta','ESP'),('Ciudad Real','Ciudad Real','ESP'),('Cordoba','Cordoba','ESP'),('Cuenca','Cuenca','ESP'),('Girona','Girona','ESP'),('Granada','Granada','ESP'),('Guadalajara','Guadalajara','ESP'),('Guipuzcoa','Guipuzcoa','ESP'),('Huelva','Huelva','ESP'),('Huesca','Huesca','ESP'),('Jaen','Jaen','ESP'),('La Rioja','La Rioja','ESP'),('Las Palmas','Las Palmas','ESP'),('Leon','Leon','ESP'),('Lleida','Lleida','ESP'),('Lugo','Lugo','ESP'),('Madrid','Madrid','ESP'),('Malaga','Malaga','ESP'),('Melilla','Melilla','ESP'),('Murcia','Murcia','ESP'),('Navarra','Navarra','ESP'),('Ourense','Ourense','ESP'),('Palencia','Palencia','ESP'),('Pontevedra','Pontevedra','ESP'),('Salamanca','Salamanca','ESP'),('Santa Cruz de Tenerife','Santa Cruz de Tenerife','ESP'),('Segovia','Segovia','ESP'),('Sevilla','Sevilla','ESP'),('Soria','Soria','ESP'),('Tarragona','Tarragona','ESP'),('Teruel','Teruel','ESP'),('Toledo','Toledo','ESP'),('Valencia','Valencia','ESP'),('Valladolid','Valladolid','ESP'),('Vizcaya','Vizcaya','ESP'),('Zamora','Zamora','ESP'),('Zaragoza','Zaragoza','ESP');
CREATE TABLE `storage_date` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(50) NOT NULL default '',
  `value` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`foreign_key`,`value_key`)
) TYPE=MyISAM COMMENT='Generic way to store date values';
CREATE TABLE `storage_int` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(50) NOT NULL default '',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`foreign_key`,`value_key`)
) TYPE=MyISAM COMMENT='Generic way to store integer values (also boolean)';
CREATE TABLE `storage_string` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(50) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`foreign_key`,`value_key`)
) TYPE=MyISAM COMMENT='Generic way to string values';
CREATE TABLE `storage_text` (
  `foreign_key` int(11) NOT NULL default '0',
  `value_key` varchar(255) NOT NULL default '',
  `value` longtext NOT NULL,
  PRIMARY KEY  (`foreign_key`,`value_key`)
) TYPE=MyISAM COMMENT='Generic way to string values';
CREATE TABLE `superbill_data` (
  `superbill_data_id` int(11) NOT NULL default '0',
  `superbill_id` int(11) NOT NULL default '0',
  `code_id` int(11) NOT NULL default '0',
  `status` int(11) NOT NULL default '0',
  PRIMARY KEY  (`superbill_data_id`)
) TYPE=MyISAM;
INSERT INTO `superbill_data` VALUES (1000,1,0,1);
CREATE TABLE `coding_data_dental` (
	`coding_data_id` INT( 11 ) NOT NULL ,
	`tooth` ENUM( 'N/A', 'All', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', 'All (Primary)', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T' ) DEFAULT 'N/A' NOT NULL ,
	`toothside` ENUM( 'N/A', 'Front', 'Back', 'Top', 'Left', 'Right' ) DEFAULT 'N/A' NOT NULL,
	PRIMARY KEY (`coding_data_id`)
) TYPE = MYISAM ;CREATE TABLE `user` (
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
INSERT INTO `user` VALUES (1,'admin','admin','','',NULL,'no',500009);
CREATE TABLE `users_groups` (
  `id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  `foreign_id` int(11) NOT NULL default '0',
  `table` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_id` (`user_id`,`group_id`,`foreign_id`,`table`)
) TYPE=MyISAM;
INSERT INTO `users_groups` VALUES (1,1,1,0,'');

INSERT INTO `menu` ( `menu_id` , `site_section` , `parent` , `dynamic_key` , `section` , `display_order` , `title` , `action` , `prefix` )
VALUES (
'', 'admin', '45', '', 'children', '40', 'Fee Schedule Discounts', 'FeeScheduleDiscount/list', 'main'
);

ALTER TABLE `patient` ADD `confidentiality` INT NOT NULL ;

CREATE TABLE `account_note` (
  `account_note_id` int(11) NOT NULL default '0',
  `patient_id` INT(11) NOT NULL,
  `claim_id` varchar(100) NOT NULL default '',
  `user_id` int(11) NOT NULL default '0',
  `date_posted` datetime NOT NULL default '0000-00-00 00:00:00',
  `note` text NOT NULL,
  `note_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`account_note_id`)
);

update menu set prefix = 'main' where prefix = 'freeb2';
ALTER TABLE `clearhealth_claim` ADD INDEX ( `encounter_id` ) ;
ALTER TABLE `coding_data` ADD INDEX ( `parent_id` ) ;
ALTER TABLE `coding_data` ADD INDEX ( `foreign_id` ) ;
ALTER TABLE `payment_claimline` ADD INDEX ( `payment_id` ) ;
ALTER TABLE `payment` ADD INDEX ( `encounter_id` ) ;
ALTER TABLE `occurences` ADD INDEX ( `start` ) ;
ALTER TABLE `occurences` ADD INDEX ( `end` ) ;
