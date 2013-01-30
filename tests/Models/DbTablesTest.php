<?php
/*****************************************************************************
*       DbTablesTest.php
*
*       Author:  ClearHealth Inc. (www.clear-health.com)        2009
*       
*       ClearHealth(TM), HealthCloud(TM), WebVista(TM) and their 
*       respective logos, icons, and terms are registered trademarks 
*       of ClearHealth Inc.
*
*       Though this software is open source you MAY NOT use our 
*       trademarks, graphics, logos and icons without explicit permission. 
*       Derivitive works MUST NOT be primarily identified using our 
*       trademarks, though statements such as "Based on ClearHealth(TM) 
*       Technology" or "incoporating ClearHealth(TM) source code" 
*       are permissible.
*
*       This file is licensed under the GPL V3, you can find
*       a copy of that license by visiting:
*       http://www.fsf.org/licensing/licenses/gpl.html
*       
*****************************************************************************/

/**
 * Unit test for Problem List Model
 */

require_once dirname(dirname(__FILE__)).'/TestHelper.php';

/**
 * TestCase
 */
require_once 'TestCase.php';

class Models_DbTablesTest extends TestCase {

	private $_dbTablesList = array();

	public function setUp() {
		parent::setUp();
		$this->_dbTablesList[] = 'account_note';
		$this->_dbTablesList[] = 'aclModules';
		$this->_dbTablesList[] = 'aclPrivileges';
		$this->_dbTablesList[] = 'aclResources';
		$this->_dbTablesList[] = 'aclRolePrivileges';
		$this->_dbTablesList[] = 'aclRoles';
		$this->_dbTablesList[] = 'address';
		$this->_dbTablesList[] = 'adodbseq';
		$this->_dbTablesList[] = 'altnotice';
		$this->_dbTablesList[] = 'appointment';
		$this->_dbTablesList[] = 'appointment_breakdown';
		$this->_dbTablesList[] = 'appointment_rule';
		$this->_dbTablesList[] = 'appointment_ruleset';
		$this->_dbTablesList[] = 'appointment_template';
		$this->_dbTablesList[] = 'appointments';
		$this->_dbTablesList[] = 'attachmentBlobs';
		$this->_dbTablesList[] = 'attachments';
		$this->_dbTablesList[] = 'audits';
		$this->_dbTablesList[] = 'auditSequences';
		$this->_dbTablesList[] = 'auditValues';
		$this->_dbTablesList[] = 'audit_log';
		$this->_dbTablesList[] = 'audit_log_field';
		$this->_dbTablesList[] = 'barcodeMacros';
		$this->_dbTablesList[] = 'building_address';
		$this->_dbTablesList[] = 'building_program_identifier';
		$this->_dbTablesList[] = 'buildings';
		$this->_dbTablesList[] = 'category';
		$this->_dbTablesList[] = 'category_to_document';
		$this->_dbTablesList[] = 'clearhealth_claim';
		$this->_dbTablesList[] = 'clinicalNoteAnnotations';
		$this->_dbTablesList[] = 'clinicalNoteDefinitions';
		$this->_dbTablesList[] = 'clinicalNoteTemplates';
		$this->_dbTablesList[] = 'clinicalNotes';
		$this->_dbTablesList[] = 'code_category';
		$this->_dbTablesList[] = 'code_to_category';
		$this->_dbTablesList[] = 'codes';
		$this->_dbTablesList[] = 'coding_data';
		$this->_dbTablesList[] = 'coding_data_dental';
		$this->_dbTablesList[] = 'coding_template';
		$this->_dbTablesList[] = 'company';
		$this->_dbTablesList[] = 'company_address';
		$this->_dbTablesList[] = 'company_company';
		$this->_dbTablesList[] = 'company_number';
		$this->_dbTablesList[] = 'company_type';
		$this->_dbTablesList[] = 'config';
		$this->_dbTablesList[] = 'countries';
		$this->_dbTablesList[] = 'cronable';
		$this->_dbTablesList[] = 'dashboardComponent';
		$this->_dbTablesList[] = 'diagnosisCodesICD';
		$this->_dbTablesList[] = 'diagnosisCodesSNOMED';
		$this->_dbTablesList[] = 'document';
		$this->_dbTablesList[] = 'duplicate_queue';
		$this->_dbTablesList[] = 'eSignatures';
		$this->_dbTablesList[] = 'eligibility_log';
		$this->_dbTablesList[] = 'encounter';
		$this->_dbTablesList[] = 'encounter_date';
		$this->_dbTablesList[] = 'encounter_person';
		$this->_dbTablesList[] = 'encounter_value';
		$this->_dbTablesList[] = 'enumeration_definition';
		$this->_dbTablesList[] = 'enumeration_value';
		$this->_dbTablesList[] = 'enumeration_value_practice';
		$this->_dbTablesList[] = 'enumerations';
		$this->_dbTablesList[] = 'enumerationsClosure';
		$this->_dbTablesList[] = 'eob_adjustment';
		$this->_dbTablesList[] = 'event';
		$this->_dbTablesList[] = 'event_group';
		$this->_dbTablesList[] = 'facility_codes';
		$this->_dbTablesList[] = 'fbaddress';
		$this->_dbTablesList[] = 'fbclaim';
		$this->_dbTablesList[] = 'fbclaimline';
		$this->_dbTablesList[] = 'fbcompany';
		$this->_dbTablesList[] = 'fbdiagnoses';
		$this->_dbTablesList[] = 'fblatest_revision';
		$this->_dbTablesList[] = 'fbperson';
		$this->_dbTablesList[] = 'fbpractice';
		$this->_dbTablesList[] = 'fbqueue';
		$this->_dbTablesList[] = 'fee_schedule';
		$this->_dbTablesList[] = 'fee_schedule_data';
		$this->_dbTablesList[] = 'fee_schedule_data_modifier';
		$this->_dbTablesList[] = 'fee_schedule_discount';
		$this->_dbTablesList[] = 'fee_schedule_discount_by_code';
		$this->_dbTablesList[] = 'fee_schedule_discount_income';
		$this->_dbTablesList[] = 'fee_schedule_discount_level';
		$this->_dbTablesList[] = 'fee_schedule_revision';
		$this->_dbTablesList[] = 'filterStates';
		$this->_dbTablesList[] = 'folders';
		$this->_dbTablesList[] = 'form';
		$this->_dbTablesList[] = 'form_data';
		$this->_dbTablesList[] = 'form_rule';
		$this->_dbTablesList[] = 'form_structure';
		$this->_dbTablesList[] = 'formularyDefault';
		$this->_dbTablesList[] = 'gacl_acl';
		$this->_dbTablesList[] = 'gacl_acl_sections';
		$this->_dbTablesList[] = 'gacl_acl_seq';
		$this->_dbTablesList[] = 'gacl_aco';
		$this->_dbTablesList[] = 'gacl_aco_map';
		$this->_dbTablesList[] = 'gacl_aco_sections';
		$this->_dbTablesList[] = 'gacl_aco_sections_seq';
		$this->_dbTablesList[] = 'gacl_aco_seq';
		$this->_dbTablesList[] = 'gacl_aro';
		$this->_dbTablesList[] = 'gacl_aro_groups';
		$this->_dbTablesList[] = 'gacl_aro_groups_id_seq';
		$this->_dbTablesList[] = 'gacl_aro_groups_map';
		$this->_dbTablesList[] = 'gacl_aro_map';
		$this->_dbTablesList[] = 'gacl_aro_sections';
		$this->_dbTablesList[] = 'gacl_aro_sections_seq';
		$this->_dbTablesList[] = 'gacl_aro_seq';
		$this->_dbTablesList[] = 'gacl_axo';
		$this->_dbTablesList[] = 'gacl_axo_groups';
		$this->_dbTablesList[] = 'gacl_axo_groups_id_seq';
		$this->_dbTablesList[] = 'gacl_axo_groups_map';
		$this->_dbTablesList[] = 'gacl_axo_map';
		$this->_dbTablesList[] = 'gacl_axo_sections';
		$this->_dbTablesList[] = 'gacl_axo_sections_seq';
		$this->_dbTablesList[] = 'gacl_axo_seq';
		$this->_dbTablesList[] = 'gacl_groups_aro_map';
		$this->_dbTablesList[] = 'gacl_groups_axo_map';
		$this->_dbTablesList[] = 'gacl_phpgacl';
		$this->_dbTablesList[] = 'genericData';
		$this->_dbTablesList[] = 'generic_notes';
		$this->_dbTablesList[] = 'graph_definition';
		$this->_dbTablesList[] = 'group_occurence';
		$this->_dbTablesList[] = 'groups';
		$this->_dbTablesList[] = 'hl7Messages';
		$this->_dbTablesList[] = 'hl7_message';
		$this->_dbTablesList[] = 'identifier';
		$this->_dbTablesList[] = 'import_map';
		$this->_dbTablesList[] = 'insurance';
		$this->_dbTablesList[] = 'insurance_payergroup';
		$this->_dbTablesList[] = 'insurance_program';
		$this->_dbTablesList[] = 'insured_relationship';
		$this->_dbTablesList[] = 'lab_note';
		$this->_dbTablesList[] = 'lab_order';
		$this->_dbTablesList[] = 'lab_result';
		$this->_dbTablesList[] = 'lab_test';
		$this->_dbTablesList[] = 'mainmenu';
		$this->_dbTablesList[] = 'medications';
		$this->_dbTablesList[] = 'meds_bulk_quantity';
		$this->_dbTablesList[] = 'meds_case';
		$this->_dbTablesList[] = 'meds_inventory_item';
		$this->_dbTablesList[] = 'meds_inventory_item_price';
		$this->_dbTablesList[] = 'meds_inventory_item_status';
		$this->_dbTablesList[] = 'meds_item_to_location';
		$this->_dbTablesList[] = 'meds_item_to_program';
		$this->_dbTablesList[] = 'meds_program';
		$this->_dbTablesList[] = 'meds_unit_of_use';
		$this->_dbTablesList[] = 'meds_unit_of_use_warning';
		$this->_dbTablesList[] = 'meds_user_to_program';
		$this->_dbTablesList[] = 'menu';
		$this->_dbTablesList[] = 'menu_form';
		$this->_dbTablesList[] = 'menu_report';
		$this->_dbTablesList[] = 'misc_charge';
		$this->_dbTablesList[] = 'name_history';
		$this->_dbTablesList[] = 'note';
		$this->_dbTablesList[] = 'notes';
		$this->_dbTablesList[] = 'nsdrDefinitionMethods';
		$this->_dbTablesList[] = 'nsdrDefinitions';
		$this->_dbTablesList[] = 'number';
		$this->_dbTablesList[] = 'orders';
		$this->_dbTablesList[] = 'ordo_registry';
		$this->_dbTablesList[] = 'ownership';
		$this->_dbTablesList[] = 'participation_program';
		$this->_dbTablesList[] = 'participation_program_basic';
		$this->_dbTablesList[] = 'participation_program_clinic';
		$this->_dbTablesList[] = 'patient';
		$this->_dbTablesList[] = 'patientImmunizations';
		$this->_dbTablesList[] = 'patient_chronic_code';
		$this->_dbTablesList[] = 'patient_note';
		$this->_dbTablesList[] = 'patient_payment_plan';
		$this->_dbTablesList[] = 'patient_payment_plan_payment';
		$this->_dbTablesList[] = 'patient_statistics';
		$this->_dbTablesList[] = 'payer_group';
		$this->_dbTablesList[] = 'payment';
		$this->_dbTablesList[] = 'payment_claimline';
		$this->_dbTablesList[] = 'person';
		$this->_dbTablesList[] = 'person_address';
		$this->_dbTablesList[] = 'person_company';
		$this->_dbTablesList[] = 'person_number';
		$this->_dbTablesList[] = 'person_participation_program';
		$this->_dbTablesList[] = 'person_person';
		$this->_dbTablesList[] = 'person_type';
		$this->_dbTablesList[] = 'practice_address';
		$this->_dbTablesList[] = 'practice_number';
		$this->_dbTablesList[] = 'practice_setting';
		$this->_dbTablesList[] = 'practices';
		$this->_dbTablesList[] = 'preferences';
		$this->_dbTablesList[] = 'problemListComments';
		$this->_dbTablesList[] = 'problemLists';
		$this->_dbTablesList[] = 'procedureCodeImmunizations';
		$this->_dbTablesList[] = 'procedureCodesCPT';
		$this->_dbTablesList[] = 'procedureCodesImmunization';
		$this->_dbTablesList[] = 'provider';
		$this->_dbTablesList[] = 'providerDashboardState';
		$this->_dbTablesList[] = 'provider_to_insurance';
		$this->_dbTablesList[] = 'pull_list';
		$this->_dbTablesList[] = 'record_sequence';
		$this->_dbTablesList[] = 'recurrence';
		$this->_dbTablesList[] = 'recurrence_pattern';
		$this->_dbTablesList[] = 'refPracticeLocation';
		$this->_dbTablesList[] = 'refRequest';
		$this->_dbTablesList[] = 'refSpecialtyMap';
		$this->_dbTablesList[] = 'refappointment';
		$this->_dbTablesList[] = 'refpatient_eligibility';
		$this->_dbTablesList[] = 'refpractice';
		$this->_dbTablesList[] = 'refpractice_specialty';
		$this->_dbTablesList[] = 'refprogram';
		$this->_dbTablesList[] = 'refprogram_member';
		$this->_dbTablesList[] = 'refprogram_member_slot';
		$this->_dbTablesList[] = 'refprovider';
		$this->_dbTablesList[] = 'refreferral_visit';
		$this->_dbTablesList[] = 'refuser';
		$this->_dbTablesList[] = 'relationship';
		$this->_dbTablesList[] = 'report_snapshot';
		$this->_dbTablesList[] = 'report_templates';
		$this->_dbTablesList[] = 'reports';
		$this->_dbTablesList[] = 'revisions';
		$this->_dbTablesList[] = 'revisions_db';
		$this->_dbTablesList[] = 'rooms';
		$this->_dbTablesList[] = 'route_slip';
		$this->_dbTablesList[] = 'routing';
		$this->_dbTablesList[] = 'routing_archive';
		$this->_dbTablesList[] = 'schedule';
		$this->_dbTablesList[] = 'scheduleEvents';
		$this->_dbTablesList[] = 'schedule_event';
		$this->_dbTablesList[] = 'secondary_practice';
		$this->_dbTablesList[] = 'self_mgmt_goals';
		$this->_dbTablesList[] = 'sequences';
		$this->_dbTablesList[] = 'sequences_daily';
		$this->_dbTablesList[] = 'sequences_named';
		$this->_dbTablesList[] = 'splash';
		$this->_dbTablesList[] = 'statement_history';
		$this->_dbTablesList[] = 'statement_sequence';
		$this->_dbTablesList[] = 'states';
		$this->_dbTablesList[] = 'storables';
		$this->_dbTablesList[] = 'storage_date';
		$this->_dbTablesList[] = 'storage_int';
		$this->_dbTablesList[] = 'storage_string';
		$this->_dbTablesList[] = 'storage_text';
		$this->_dbTablesList[] = 'summary_columns';
		$this->_dbTablesList[] = 'superbill';
		$this->_dbTablesList[] = 'superbill_data';
		$this->_dbTablesList[] = 'tags';
		$this->_dbTablesList[] = 'tags_storables';
		$this->_dbTablesList[] = 'teamMembers';
		$this->_dbTablesList[] = 'templatedText';
		$this->_dbTablesList[] = 'tree';
		$this->_dbTablesList[] = 'user';
		$this->_dbTablesList[] = 'userKeys';
		$this->_dbTablesList[] = 'users_groups';
		$this->_dbTablesList[] = 'visit_queue';
		$this->_dbTablesList[] = 'visit_queue_reason';
		$this->_dbTablesList[] = 'visit_queue_template';
		$this->_dbTablesList[] = 'vitalSignGroups';
		$this->_dbTablesList[] = 'vitalSignTemplates';
		$this->_dbTablesList[] = 'vitalSignValueQualifiers';
		$this->_dbTablesList[] = 'vitalSignValues';
		$this->_dbTablesList[] = 'widget_form';
		$this->_dbTablesList[] = 'x12imported_data';
		$this->_dbTablesList[] = 'x12transaction_data';
		$this->_dbTablesList[] = 'x12transaction_history';
		$this->_dbTablesList[] = 'zipcodes';
	}

	public function testTablesExist() {
		$db = Zend_Registry::get('dbAdapter');
		$sql = "SHOW TABLES";
		$rows = $db->fetchAll($sql);

		$tableExists = array();
		foreach ($this->_dbTablesList as $table) {
			$tableExists[$table] = false;
		}
		foreach ($rows as $row) {
			foreach ($this->_dbTablesList as $table) {
				if ($row['Tables_in_'.TEST_DB_DBNAME] == $table) {
					$tableExists[$table] = true;
					break;
				}
			}
			if (!in_array(false,$tableExists)) {
				break;
			}
		}
		foreach ($tableExists as $tableName=>$exists) {
			$this->assertTrue($exists,"Table {$tableName} does not exists.");
		}
		$dbTablesListCtr = count($this->_dbTablesList);
		$rowsCtr = count($rows);
		$this->assertGreaterThanOrEqual($dbTablesListCtr,$rowsCtr,'There are not enough tables in the database. ' . $rowsCtr . ' should be more than or equal to: ' . $dbTablesListCtr);
	}

}

