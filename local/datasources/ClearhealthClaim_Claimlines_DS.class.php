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
						fbcl.amount",
				'from'    => "
					clearhealth_claim cc
					inner JOIN fbclaim  fbc on fbc.claim_identifier = cc.identifier
					inner JOIN fblatest_revision fblr on fbc.claim_identifier = fblr.claim_identifier and fblr.revision = fbc.revision
					inner JOIN fbclaimline fbcl on fbc.claim_id = fbcl.claim_id
					",
				'where'   => "cc.claim_id = {$claimId}"
			),
			array(
				'procedure' => "Procedure",
				'modifier' => "Modifier",
				'units'	=> 'Units',
				'amount' => 'Fee'
			)	
		);
		
		$this->orderHints['date_posted'] = 'formatted_date_posted';
	}
}

?>
