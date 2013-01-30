<?php
/*****************************************************************************
*       AllTests.php
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

require_once dirname(__FILE__) . '/../TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Models_AllTests::main');
}

/**
 * Models_NSDRDefinitionTest
 */
require_once 'Models/NSDRDefinitionTest.php';

class Models_AllTests {

	public static function main() {
		PHPUnit_TextUI_TestRunner::run(self::suite());
	}

	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('ClearHealth 3.0 - Models');
		$suite->addTestSuite('Models_DbTablesTest');
		$suite->addTestSuite('Models_PHPModulesTest');
		$suite->addTestSuite('Models_NSDR2Test');
		$suite->addTestSuite('Models_NSDRDefinitionTest');
		$suite->addTestSuite('Models_ClinicalNoteTest');
		$suite->addTestSuite('Models_ProblemListTest');
		$suite->addTestSuite('Models_UserKeyTest');
		$suite->addTestSuite('Models_HealthStatusAlertTest');

		$suite->addTestSuite('Models_AuditTest');
		$suite->addTestSuite('Models_AddressTest');
		//$suite->addTestSuite('Models_AdmissionTest');
		$suite->addTestSuite('Models_AppointmentTest');
		$suite->addTestSuite('Models_AppointmentTemplateTest');
		$suite->addTestSuite('Models_AttachmentTest');
		$suite->addTestSuite('Models_AuditValueTest');
		$suite->addTestSuite('Models_BarcodeMacroTest');
		//$suite->addTestSuite('Models_BaseMed24Test');
		$suite->addTestSuite('Models_BuildingProgramIdentifierTest');
		$suite->addTestSuite('Models_BuildingTest');
		$suite->addTestSuite('Models_ClinicalNoteAnnotationTest');
		$suite->addTestSuite('Models_ClinicalNoteDefinitionTest');
		$suite->addTestSuite('Models_ClinicalNoteTemplateTest');
		$suite->addTestSuite('Models_CompanyAddressTest');
		$suite->addTestSuite('Models_CompanyNumberTest');
		$suite->addTestSuite('Models_CompanyTest');
		$suite->addTestSuite('Models_CompanyTypeTest');
		$suite->addTestSuite('Models_ConfigItemTest');
		$suite->addTestSuite('Models_DashboardComponentTest');
		$suite->addTestSuite('Models_DataIntegrationActionTest');
		$suite->addTestSuite('Models_DataIntegrationDatasourceTest');
		$suite->addTestSuite('Models_DataIntegrationDestinationTest');
		$suite->addTestSuite('Models_DataIntegrationTemplateTest');
		$suite->addTestSuite('Models_DiagnosisCodesAllergyTest');
		$suite->addTestSuite('Models_DiagnosisCodesICDTest');
		$suite->addTestSuite('Models_DiagnosisCodesSNOMEDTest');
		$suite->addTestSuite('Models_DrugScheduleDaysSupplyTest');
		$suite->addTestSuite('Models_EnumerationTest');
		$suite->addTestSuite('Models_EnumerationsClosureTest');
		$suite->addTestSuite('Models_ESignatureTest');
		$suite->addTestSuite('Models_ExternalTeamMemberTest');
		$suite->addTestSuite('Models_FilterStateTest');
		$suite->addTestSuite('Models_FormTest');
		$suite->addTestSuite('Models_FormularyItemTest');
		$suite->addTestSuite('Models_GeneralAlertTest');
		$suite->addTestSuite('Models_GeneralAlertHandlerTest');
		$suite->addTestSuite('Models_GenericDataTest');
		$suite->addTestSuite('Models_HealthStatusHandlerTest');
		$suite->addTestSuite('Models_HealthStatusHandlerPatientTest');
		$suite->addTestSuite('Models_HandlerTest');
		$suite->addTestSuite('Models_HL7MessageTest');
		$suite->addTestSuite('Models_InsuranceProgramTest');
		$suite->addTestSuite('Models_InsuredRelationshipTest');
		$suite->addTestSuite('Models_LabNoteTest');
		$suite->addTestSuite('Models_LabOrderTest');
		$suite->addTestSuite('Models_LabResultTest');
		$suite->addTestSuite('Models_LabTestTest');
		$suite->addTestSuite('Models_LegacyAppointmentTest');
		//$suite->addTestSuite('Models_LegacyEnumTest');
		//$suite->addTestSuite('Models_LocationTest');
		$suite->addTestSuite('Models_MedicationTest');
		$suite->addTestSuite('Models_MenuItemTest');
		$suite->addTestSuite('Models_NSDRDefinitionMethodTest');
		$suite->addTestSuite('Models_OrderTest');
		$suite->addTestSuite('Models_PatientAllergyTest');
		$suite->addTestSuite('Models_PatientDiagnosisTest');
		$suite->addTestSuite('Models_PatientEducationTest');
		$suite->addTestSuite('Models_PatientExamTest');
		$suite->addTestSuite('Models_PatientImmunizationTest');
		$suite->addTestSuite('Models_PatientNoteTest');
		$suite->addTestSuite('Models_PatientProcedureTest');
		$suite->addTestSuite('Models_PatientTest');
		$suite->addTestSuite('Models_PatientStatisticsTest');
		$suite->addTestSuite('Models_PatientVisitTypeTest');
		$suite->addTestSuite('Models_PersonTest');
		$suite->addTestSuite('Models_PharmacyTest');
		$suite->addTestSuite('Models_PhoneNumberTest');
		$suite->addTestSuite('Models_PracticeTest');
		$suite->addTestSuite('Models_ProblemListCommentTest');
		$suite->addTestSuite('Models_ProcedureCodesCPTTest');
		$suite->addTestSuite('Models_ProcedureCodesImmunizationTest');
		$suite->addTestSuite('Models_ProcessingErrorTest');
		$suite->addTestSuite('Models_ProviderDashboardStateTest');
		$suite->addTestSuite('Models_ProviderTest');
		$suite->addTestSuite('Models_ReportTest');
		$suite->addTestSuite('Models_ReportQueryTest');
		$suite->addTestSuite('Models_RoomTest');
		$suite->addTestSuite('Models_RoutingTest');
		$suite->addTestSuite('Models_ScheduleEventTest');
		$suite->addTestSuite('Models_StorageStringTest');
		$suite->addTestSuite('Models_TeamMemberTest');
		$suite->addTestSuite('Models_TemplatedTextTest');
		$suite->addTestSuite('Models_TemplateXSLTTest');
		//$suite->addTestSuite('Models_UpdateFileTest');
		$suite->addTestSuite('Models_UserTest');
		$suite->addTestSuite('Models_VisitTest');
		$suite->addTestSuite('Models_VitalSignGroupTest');
		$suite->addTestSuite('Models_VitalSignTemplateTest');
		$suite->addTestSuite('Models_VitalSignValueTest');
		return $suite;
	}

}

if (PHPUnit_MAIN_METHOD == 'Models_AllTests::main') {
	Models_AllTests::main();
}
