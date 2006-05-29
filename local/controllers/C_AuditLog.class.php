<?php

$loader->requireOnce('/includes/Datasource_sql.class.php');

class C_AuditLog extends controller {
    
    /**
     * 
     * For a given table, what column holds the patient_id?
     * 
     * @var array
     * 
     */
    var $_table_patient = array(
		'account_note'         => 'patient_id',
		'appointment'          => 'patient_id',
		'encounter'            => 'patient_id',
		'encounter_person'     => 'person_id',
		'form_data'            => 'external_id',
		'identifier'           => 'person_id',
		'insured_relationship' => 'person_id',
		'lab_order'            => 'patient_id',
		'patient_chronic_code' => 'patient_id',
		'patient_note'         => 'patient_id',
		'patient_payment_plan' => 'patient_id',
		'patient_statistics'   => 'person_id',
		'person_address'       => 'person_id',
		'person_number'        => 'person_id',
		'person_person'        => 'person_id',
    );
    
    /**
     * 
     * For a given table, what is its primary key?
     * 
     * @var array
     * 
     */
    var $_table_primary = array (
        'account_note'         => 'account_note_id',
        'appointment'          => 'appointment_id',
        'encounter'            => 'encounter_id',
        'encounter_person'     => 'encounter_person_id',
        'form_data'            => 'form_data_id',
        'identifier'           => 'identifier_id',
        'insured_relationship' => 'insured_relationship_id',
        'lab_order'            => 'lab_order_id',
        'patient_chronic_code' => 'chronic_care_code', // artificial
        'patient_note'         => 'patient_note_id',
        'patient_payment_plan' => 'patient_payment_plan_id',
        'patient_statistics'   => 'person_id',
        'person_address'       => 'address_id', // artificial
        'person_number'        => 'number_id',
        'person_person'        => 'person_person_id',
    );
    
    /**
     * 
     * What table maps to which ORDO?
     * 
     * @var array
     * 
     */
    var $_table_ordo = array (
        'account_note' => 'AccountNote',
        'appointment' => 'Appointment',
        'encounter' => 'Encounter',
        'encounter_person' => 'EncounterPerson',
        'form_data' => 'FormData',
        'identifier' => 'Identifier',
        'insured_relationship' => 'InsuredRelationship',
        'lab_order' => 'LabOrder',
        'patient_chronic_code' => 'PatientChronicCode',
        'patient_note' => 'PatientNote',
        'patient_payment_plan' => 'PatientPaymentPlan',
        'patient_statistics' => 'PatientStatistics',
        'person_address' => 'PersonAddress',
        'person_number' => 'PersonNumber',
        'person_person' => 'PersonPerson',
    );
    
    /**
     * 
     * Renders a list of change events in the audit log for a given patient_id.
     * 
     */
    function actionList()
    {
        // needed for database connection and quoting
        $db =& $GLOBALS['db'];
        
        // get the patient_id from the c_patient controller.
        // this means you need to have selected a patient before
        // coming to this screen.
        $patient_id = $db->quote($this->get('patient_id', 'c_patient'));
        
        // the query array for the datasource
        $query = array(
            'union'   => array(),
            'orderby' => 'log_date DESC',
        );
        
        // build a UNION query for all related tables
        foreach ($this->_table_patient as $table => $patient_col) {
            
            // needed for the query string
            $primary_key = $this->_table_primary[$table];
            $ordo        = $this->_table_ordo[$table];
            
            // get the audit_log_id list for all changes related to this
            // patient, and which ORDOs were affected.
            $query['union'][] = array(
                'cols'    => "audit_log.audit_log_id, audit_log.log_date, audit_log.ordo, "
                           . "COUNT(audit_log_field.audit_log_field_id) AS num_fields, "
                           . "CONCAT(person.first_name, ' ', person.last_name) AS name ",
                           
                'from'    => "audit_log "
                           . "INNER JOIN $table ON "
                           . "    audit_log.ordo = '$ordo' AND "
                           . "    audit_log.ordo_id = {$table}.{$primary_key} "
                           . "INNER JOIN audit_log_field ON "
                           . "    audit_log_field.audit_log_id = audit_log.audit_log_id "
                           . "LEFT JOIN user ON user.user_id = audit_log.user_id "
                           . "LEFT JOIN person ON person.person_id = user.person_id ",
                           
                'where'   => "{$table}.$patient_col = $patient_id ",
                
                'groupby' => "audit_log.audit_log_id ",
            );
        }
        
        // set up labels for the datasource
        $labels = array(
            'log_date'   => 'On',
            'name'       => 'User',
            'ordo'       => 'Changed',
            'num_fields' => 'Num. Changes',
        );
        
        // build the datasource from the query
        $ds =& new Datasource_sql();
        $ds->setup($db, $query, $labels);
		$ds->template['log_date'] = "<a href='".Celini::link('view','AuditLog')."id={\$audit_log_id}'>{\$log_date}</a>";
		
		// build the grid from the datasource
		$grid =& new cGrid($ds);
		$this->assign_by_ref('grid',$grid);
        
        // render output
		return $this->view->render("list.html");
    }
    
    /**
     * 
     * Renders all fields changed in a specific audit log event.
     * 
     */
    function actionView()
    {
        // needed for database connection and quoting
        $db =& $GLOBALS['db'];
        $id = $db->quote($this->GET->get('id'));
        
        // query for the audit log overview
        $cmd = "SELECT audit_log.audit_log_id, audit_log.log_date, audit_log.ordo, "
             . "CONCAT(person.first_name, ' ', person.last_name) AS name "
             . "FROM audit_log "
             . "LEFT JOIN user ON user.user_id = audit_log.user_id "
             . "LEFT JOIN person ON person.person_id = user.person_id "
             . "WHERE audit_log.audit_log_id = $id ";
        $result = $db->Execute($cmd);
        $rows = $result->getAll();
        $this->assign($rows[0]);
        
        // query for audit fields that were changed
        $query = array(
            'cols'  => '*',
            'from'  =>  'audit_log_field',
            'where' => "audit_log_id = $id",
        );
        
        // set up labels for the datasource
        $labels = array(
            'field'              => 'Changed',
            'old_value'          => 'From',
            'new_value'          => 'To',
        );
        
        // build datasource
        $ds =& new Datasource_sql();
        $ds->setup($db, $query, $labels);
        
		// build a grid from the datasource
		$grid =& new cGrid($ds);
		$this->assign_by_ref('grid', $grid);
        
        // render output
		return $this->view->render("view.html");
    }
}
?>