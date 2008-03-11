<?php
class clniAudit {

	/**
	 * Create an audit entry for an ordo that based on its current values
	 */
	function logOrdo(&$ordo,$type,$message) {
		$me =& me::getInstance();
		$user_id = $me->get_id();

		$a =& Celini::newOrdo('AuditLog');
		$a->set('ordo',$ordo->name());
		$a->set('ordo_id',$ordo->get('id'));
		$a->set('user_id',$user_id);
		$a->set('type',$type);
		$a->set('log_date',date('Y-m-d H:i:s'));
		$a->set('message',$message);
		$a->persist();

		$fields = $ordo->metadata->listFields();
		foreach($fields as $field) {
			$f =& Celini::newOrdo('AuditLogField');
			$f->set('audit_log_id',$a->get('id'));
			$f->set('field',$field);
			$f->set('old_value',$ordo->metadata->dbValue($field));
			$f->persist();
		}
	}
	function logAccessAttempt($username, $status) {

                $a =& Celini::newOrdo('AuditLog');
                $a->set('type',5); //access type
                $a->set('ordo',$username); 
                $a->set('log_date',date('Y-m-d H:i:s'));
                $a->set('message', "Access attempt for " . $username . (($status === false) ? " failed" : " succeeded"));
                $a->persist();

	}

	/** 
	 * Create an audit log entry for an ordo, storing changes
	 */
	function logOrdoChanges(&$ordo,$meta,$auditFields) {
		$me =& me::getInstance();
		$user_id = $me->get_id();

		if(!$meta->isModified()) {
			return false;
		}

		$a =& Celini::newOrdo('AuditLog');
		$a->set('ordo',$ordo->name());
		$a->set('ordo_id',$ordo->get('id'));
		$a->set('user_id',$user_id);
		if ($meta->isNew()) {
			$a->set('type','insert');
		} else {
			$a->set('type','update');
		}
		$a->set('log_date',date('Y-m-d H:i:s'));
		$a->set('message',$ordo->getAuditMessage());
		$a->persist();

		if ($auditFields) {
			$fields = $meta->modifiedFields();
			foreach($fields as $field) {
				$nv = $ordo->get($field);
				if (is_string($nv)) {
					$f =& Celini::newOrdo('AuditLogField');
					$f->set('audit_log_id',$a->get('id'));
					$f->set('field',$field);
					$f->set('old_value',$meta->dbValue($field));
					$f->set('new_value',$nv);
					$f->persist();
				}
			}
		}
	}

	function logOrdoDrop(&$ordo,$meta,$auditFields) {
		$me =& me::getInstance();
		$user_id = $me->get_id();

		$a =& Celini::newOrdo('AuditLog');
		$a->set('ordo',$ordo->name());
		$a->set('ordo_id',$ordo->get('id'));
		$a->set('user_id',$user_id);
		$a->set('type','delete');

		$a->set('log_date',date('Y-m-d H:i:s'));
		$a->set('message',$ordo->getAuditMessage());
		$a->persist();

		if ($auditFields) {
			$fields = $meta->modifiedFields();
			foreach($fields as $field) {
				$f =& Celini::newOrdo('AuditLogField');
				$f->set('audit_log_id',$a->get('id'));
				$f->set('field',$field);
				$f->set('old_value',$meta->dbValue($field));
				$f->set('new_value','');
				$f->persist();
			}
		}
	}

	/**
	 * Field from log entry
	 */
	function oldFieldFromLogEntry($auditLogId,$field) {
		$f =& Celini::newOrdo('AuditLogField',array($auditLogId,$field),'ByLogAndField');
		$ret = $f->get('old_value');
		return $ret;

	}
}
?>
