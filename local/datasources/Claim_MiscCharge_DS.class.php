<?php
$loader->requireOnce('includes/Datasource_sql.class.php');
class Claim_MiscCharge_DS extends Datasource_Sql {

	var $_internalName = 'Claim_MiscCharge_DS';
	var $_type = 'html';

	function Claim_MiscCharge_DS($claimId) {
		$cId = EnforceType::int($claimId);

		$format = DateObject::getFormat();

		$db = Celini::dbInstance();
		$this->setup($db,array(
				'cols' 	=> "
					cc.claim_id,
					mc.amount total_billed,
					mc.title current_payer,
					date_format(mc.charge_date,'$format') billing_date,
					'Misc Charge' identifier,
					user.username user

					",
				'from' 	=> '
					misc_charge mc
					inner join encounter e on mc.encounter_id = e.encounter_id
					inner join clearhealth_claim cc on e.encounter_id = cc.encounter_id
					inner join ordo_registry oreg on oreg.ordo_id = mc.misc_charge_id
					inner join user on oreg.creator_id = user.user_id
					',
				'where' => "cc.claim_id = $cId"
			),
			false
		);
	}
}
