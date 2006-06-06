<?php
$loader->requireOnce('/includes/Datasource_sql.class.php');

class ClearhealthClaim_Claimlines_DS extends Datasource_sql 
{
	var $_internalName = 'ClearhealthClaim_Claimlines_DS';

	/**
	 * The default output type for this datasource.
	 *
	 * @var string
	 */
	var $_type = 'html';


	function ClearhealthClaim_Claimlines_DS($claimId) {
		$claimId = EnforceType::int($claimId);
		$em =& Celini::enumManagerInstance();
		$format = DateObject::getFormat();
		
		$this->setup(Celini::dbInstance(),
			array(
				'cols'    => "
						fbcl.procedure,
						fbcl.modifier,
						fbcl.units,
						fbcl.amount,
						u2.username coded,
						u.username billed",
				'from'    => "
					clearhealth_claim cc
					inner JOIN fbclaim  fbc on fbc.claim_identifier = cc.identifier
					inner JOIN fblatest_revision fblr on fbc.claim_identifier = fblr.claim_identifier and fblr.revision = fbc.revision
					inner JOIN fbclaimline fbcl on fbc.claim_id = fbcl.claim_id
					LEFT JOIN ordo_registry AS oreg ON(fbcl.claimline_id = oreg.ordo_id)
					LEFT JOIN user AS u ON(oreg.creator_id = u.user_id)
					LEFT JOIN codes c ON(fbcl.procedure = c.code)
					LEFT JOIN coding_data cd on (c.code_id = cd.code_id and cc.encounter_id = cd.foreign_id and parent_id = 0)
					LEFT JOIN ordo_registry AS oreg2 ON(cd.coding_data_id = oreg2.ordo_id)
					LEFT JOIN user AS u2 ON(oreg2.creator_id = u2.user_id)
					",
				'where'   => "cc.claim_id = {$claimId}"
			),
			array(
				'procedure' => "Procedure",
				'modifier' => "Modifier",
				'units'	=> 'Units',
				'amount' => 'Fee',
				'coded' => 'Coded By',
				'billed' => 'Billed By'
			)	
		);
		
		$this->orderHints['date_posted'] = 'formatted_date_posted';
	}
}

?>
