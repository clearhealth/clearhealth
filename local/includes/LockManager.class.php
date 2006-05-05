<?php
/* LockManager
 * class and functions for managing the data locking for CH
 * 
 */
class LockManager {

	// detect if an ordo has changed, returns a hash of changed fields and 
	// their newer values
	// $fieldlist will be an optional parameter which, if included, will 
	// give us a list of fields to check, otherwise return the hash of 
	// everything that has changed
	function hasOrdoChanged ($ordoName,$ordoID,$timestamp,$fieldlist=Array()) {
		$changedFields = Array();
		
		if (empty($ordoName) || empty($ordoID) || empty($timestamp)) {
			Celini::raiseError("Ordo name, Ordo id, or timestamp is invalid.");
		} else {
			$limitfields = false ;
			if (count($fieldlist) > 0) {
				$limitfields = true ;
			}
			$db = new clniDb();
			$sql = 	"
				SELECT 
					alf.field,
					alf.old_value,
					alf.new_value,
					u.username
				FROM 
					audit_log al
					inner join audit_log_field alf on al.audit_log_id = alf.audit_log_id
					inner join user u on al.user_id = u.user_id
				WHERE 
					al.ordo = ".$db->quote($ordoName)." AND
					al.ordo_id='$ordoID' AND 
					al.log_date > '".date('Y-m-d H:i:s',$timestamp)."'
					".($limitfields ? ' AND alf.field IN ("'.join('","',$fieldlist).'")' : '')."
				ORDER BY 
					al.log_date ASC
				";
			// we've sorted it in ASCending order so that as it loops, it'll overwrite older changes with newer changes
			$results = $db->execute($sql);
			while ($results && !$results->EOF) {
				$changedFields[$results->fields['field']] = $results->fields;
				$results->moveNext();
			}
		}
		return $changedFields;
	}

}
?>
