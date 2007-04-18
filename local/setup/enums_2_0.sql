-- MySQL dump 10.10
--
-- Host: localhost    Database: chtrunk
-- ------------------------------------------------------
-- Server version	5.0.22

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `enumeration_definition`
--

/*!40000 ALTER TABLE `enumeration_definition` DISABLE KEYS */;
LOCK TABLES `enumeration_definition` WRITE;
INSERT INTO `enumeration_definition` VALUES (300466,'address_type','Address Type','Default'),(300472,'appointment_reasons','Appointment Reason','AppointmentReason'),(300480,'assigning','Assigning','Default'),(300485,'code_modifier','Code Modifier','Default'),(300492,'company_number_type','Company Number Type','Default'),(300495,'company_type','Company Type','Default'),(300497,'disposition','Disposition','Default'),(300501,'encounter_date_type','Encounter Date Type','Default'),(300510,'encounter_person_type','Encounter Person Type','Default'),(300512,'encounter_reason','Encounter Reason','EncounterReason'),(300515,'encounter_value_type','Encounter Value Type','Default'),(300521,'ethnicity','Ethnicity','Default'),(300524,'gender','Gender','Default'),(300528,'group_list','File Groups','Default'),(300532,'identifier_type','Identifier Type','Default'),(300535,'income','Income','Default'),(300540,'language','Languages','Default'),(300560,'marital_status','Marital Status','Default'),(300564,'migrant_status','Migrant Status','Default'),(300566,'number_type','Phone Number Type','Default'),(300572,'payer_type','Payer Type','Default'),(300582,'payment_type','Payment Type','Default'),(300589,'person_to_person_relation_type','Person to person relation type','Default'),(300594,'person_type','Person Type','PersonType'),(608614,'provider_number_type','Provider Number Type','MappedValue'),(300602,'provider_reporting_type','Provider Reporting Type','Default'),(300608,'quality_of_file','Quality of File','Default'),(300611,'race','Race','Default'),(608378,'confidential_family_planning_and_disease_codes','Confidential Family Planning and Disease Codes','ConfidentialFamilyPlanningAndDisease'),(300624,'state','State','Default'),(300677,'subscriber_to_patient_relationship','Subscriber to patient relationship','Default'),(300525,'system_reports','System Reports','Url'),(300818,'chronic_care_codes','Chronic Care Codes','Default'),(300852,'funds_source','Funds Source','Default'),(607809,'audit_type','Audit Type','Default'),(601227,'confidentiality_levels','Confidentiality Levels','Default'),(601942,'account_note_type','Account Note Type','Default'),(607814,'confidential_family_planning_codes','Confidential family planning codes','Default'),(607816,'confidential_disease_codes','Confidential_disease_codes','Default'),(607818,'days_of_week','Days of Week','Default'),(607826,'eob_adjustment_type','Eob Adjustment Type','Default'),(607830,'months_of_year','Months of Year','Default'),(607843,'recurrence_pattern_type','Recurrence Pattern Type','MappedValue'),(607849,'subscriber_to_patient','Subscriber to patient','Default'),(607852,'weeks_of_month','Weeks of Month','Default'),(812287,'dm_group_list','Document Group List','Default'),(812288,'value_type','Value Type','Default'),(1080302,'widget_type','Widget Type','Default'),(513682,'refSpecialty','Specialists','Default'),(513700,'refEligibility','Referal Eligibility','Default'),(513706,'refRequested_time','Referal: Requested Time','Default'),(513718,'days','Days of the Week','Default'),(513726,'yesNo','Yes or No','Default'),(513734,'refStatus','Referral: Status','Default'),(55,'refEligibilitySchema','Referral: Eligibility Schema','PointToObject'),(255,'refRejectionReason','Referral Rejection Reason','default'),(288,'chlFollowUpReason','Follow Up Reason','default'),(363,'emergency_contact_relationship','Emergency Contact Relationship','Default'),(394,'refUserType','Referral: User Type','default'),(465,'federal_poverty_level','federal_poverty_level','Expanded'),(1977,'insurance_program_type','Insurance Program Type','Default'),(2009,'meds_program_inventory_item_type','Meds Program Inventory Item Type','Default'),(2041,'meds_program_inventory_item_class','Meds Program Inventory Item Class','Default'),(2055,'meds_program_inventory_item_class_type','Meds Program Inventory Item Class Type','Default'),(2078,'meds_program_inventory_item_use_type','Meds Program Inventory Item Use Type','Default'),(2110,'meds_program_inventory_item_warning','Meds Program Inventory Item Warning','MappedValue'),(14125,'planned_care_codes','planned_care_codes','Default'),(14338,'risk_factors_codes','risk_factors_codes','Default'),(34831,'self_mgmt_goals','Self Management Goals','Default'),(27698,'pp_clinic_eligibility','PP Clinic eligibility','Default'),(37577,'lab_manual_service_list','Lab Manual Service List','Default'),(37861,'lab_manual_company_list','Lab Manual Company List','Default'),(38471,'lab_manual_description_list','Lab Manual Description List','Default'),(38524,'lab_manual_abnormal_list','Lab Manual Abnormal List','Default'),(40637,'household_status','Household Status','Default'),(40642,'english_proficiency','English Proficiency','Default'),(40647,'country_of_origin','Country of Origin','Default'),(40652,'religion','Religion','Default'),(40657,'employment_status','Employment Status','Default'),(40665,'occupation','Occupation','Default'),(40670,'education_level','Education Level','Default'),(40675,'us_veteran','US Veteran','Default'),(40680,'medication_coverage','Medication Coverage','Default'),(40685,'housing_type','Housing Type','Default');
UNLOCK TABLES;
/*!40000 ALTER TABLE `enumeration_definition` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

