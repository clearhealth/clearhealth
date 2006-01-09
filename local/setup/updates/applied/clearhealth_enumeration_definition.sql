CREATE TABLE enumeration_definition (
  enumeration_id int(11) NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  title varchar(255) NOT NULL default '',
  type varchar(255) NOT NULL default '',
  PRIMARY KEY  (enumeration_id),
  UNIQUE KEY name (name)
) TYPE=MyISAM;

INSERT INTO enumeration_definition VALUES (600466,'address_type','Address Type','Default');
INSERT INTO enumeration_definition VALUES (600472,'appointment_reasons','Appointment Reason','Default');
INSERT INTO enumeration_definition VALUES (600480,'assigning','Assigning','Default');
INSERT INTO enumeration_definition VALUES (600485,'code_modifier','Code Modifier','Default');
INSERT INTO enumeration_definition VALUES (600492,'company_number_type','Company Number Type','Default');
INSERT INTO enumeration_definition VALUES (600495,'company_type','Company Type','Default');
INSERT INTO enumeration_definition VALUES (600497,'disposition','Disposition','Default');
INSERT INTO enumeration_definition VALUES (600501,'encounter_date_type','Encounter Date Type','Default');
INSERT INTO enumeration_definition VALUES (600510,'encounter_person_type','Encounter Person Type','Default');
INSERT INTO enumeration_definition VALUES (600512,'encounter_reason','Encounter Reason','Default');
INSERT INTO enumeration_definition VALUES (600515,'encounter_value_type','Encounter Value Type','Default');
INSERT INTO enumeration_definition VALUES (600521,'ethnicity','Ethnicity','Default');
INSERT INTO enumeration_definition VALUES (600524,'gender','Gender','Default');
INSERT INTO enumeration_definition VALUES (600528,'group_list','File Groups','Default');
INSERT INTO enumeration_definition VALUES (600532,'identifier_type','Identifier Type','Default');
INSERT INTO enumeration_definition VALUES (600535,'income','Income','Default');
INSERT INTO enumeration_definition VALUES (600540,'language','Languages','Default');
INSERT INTO enumeration_definition VALUES (600560,'marital_status','Marital Status','Default');
INSERT INTO enumeration_definition VALUES (600564,'migrant_status','Migrant Status','Default');
INSERT INTO enumeration_definition VALUES (600566,'number_type','Phone Number Type','Default');
INSERT INTO enumeration_definition VALUES (600572,'payer_type','Payer Type','Default');
INSERT INTO enumeration_definition VALUES (600582,'payment_type','Payment Type','Default');
INSERT INTO enumeration_definition VALUES (600589,'person_to_person_relation_type','Person to person relation type','Default');
INSERT INTO enumeration_definition VALUES (600594,'person_type','Person Type','Default');
INSERT INTO enumeration_definition VALUES (600600,'provider_number_type','Provider Number Type','Default');
INSERT INTO enumeration_definition VALUES (600602,'provider_reporting_type','Provider Reporting Type','Default');
INSERT INTO enumeration_definition VALUES (600608,'quality_of_file','Quality of File','Default');
INSERT INTO enumeration_definition VALUES (600611,'race','Race','Default');
INSERT INTO enumeration_definition VALUES (600617,'relation_of_information_code','Relation Of Information Code','Default');
INSERT INTO enumeration_definition VALUES (600624,'state','State','Default');
INSERT INTO enumeration_definition VALUES (600677,'subscriber_to_patient','Subscriber to patient','Default');

