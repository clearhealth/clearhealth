<?php
$loader->requireOnce('includes/Datasource_sql.class.php');

class Patient_Statement_DS extends Datasource_sql {
	var $primaryKey = 'encounter_id';

	var $_internalName = 'Patient_Statement_DS';
	var $_type = 'html';
	function Patient_Statement_DS($patientId,$includeDependants,$withbalance=true,$filters=array()) {
		$format = DateObject::getFormat();

		$patientSelectSql = "
		e.patient_id = $patientId
		";
		if ($includeDependants) {
			$patientSelectSql = "(e.patient_id =$patientId or e.patient_id 
				in(select person_id from person_person where related_person_id = $patientId and guarantor = 1))";
		}

		$withbalance = $withbalance == true ? 'AND balance > 0' : '';
		$encounterBalanceSql = "
		select
			feeData.encounter_id,
			(charge - ifnull(credit,0.00)) balance
		from
			/* Fee total */
			(
			select
				e.encounter_id,
				sum(cd.fee) charge
			from
				encounter e
				inner join clearhealth_claim cc using(encounter_id)
				inner join coding_data cd on e.encounter_id = cd.foreign_id and cd.parent_id = 0
			where
				$patientSelectSql
			group by
				e.encounter_id
			) feeData
		left join
			/* Payment totals */
			(
			select
				e.encounter_id,
				(sum(pl.paid) + sum(pl.writeoff)) credit
			from
				encounter e
				inner join clearhealth_claim cc using(encounter_id)
				inner join payment p on cc.claim_id = p.foreign_id
				inner join payment_claimline pl on p.payment_id = pl.payment_id
			where
				$patientSelectSql
			group by
				e.encounter_id
			) paymentData on feeData.encounter_id = paymentData.encounter_id
		WHERE feeData.encounter_id NOT IN (
			select e.encounter_id FROM encounter AS e
			INNER JOIN relationship EPPP ON EPPP.parent_type = 'Encounter' AND EPPP.parent_id=e.encounter_id AND EPPP.child_type='PatientPaymentPlan'
			INNER JOIN patient_payment_plan ppp ON ppp.patient_payment_plan_id=EPPP.child_id AND ppp.balance > 0
			)
		";

		$this->setup(
			Celini::dbInstance(),
			array(
				'cols' 	=> '*',
				'from' => "(
		select
			date_format(e.date_of_treatment,'$format') item_date,
			c.code_text,
			c.code,
			cc.total_billed charge,
			0.00 credit,
			0.00 outstanding,
			e.encounter_id,
			concat_ws(', ',pr.last_name,pr.first_name) patient_name
		from
			encounter e
			inner join clearhealth_claim cc using(encounter_id)
			inner join coding_data cd on e.encounter_id = cd.foreign_id and cd.parent_id = 0
			inner join codes c using(code_id)
			inner join ($encounterBalanceSql) b on e.encounter_id = b.encounter_id
			inner join person pr on e.patient_id = pr.person_id
		where
			(e.status = 'billed' or e.status = 'closed') and
			$patientSelectSql and balance > 0
		union
		select
			date_format(p.payment_date,'$format') item_date,
			'Co-Pay' code_text,
			'' code,
			0.00 charge,
			(pl.paid+pl.writeoff) credit,
			0.00 outstanding,
			e.encounter_id,
			concat_ws(', ',pr.last_name,pr.first_name) patient_name
		from
			encounter e
			inner join clearhealth_claim cc using(encounter_id)
			inner join payment p on e.encounter_id = p.encounter_id
			inner join payment_claimline pl using(payment_id)
			inner join ($encounterBalanceSql) b on e.encounter_id = b.encounter_id
			inner join person pr on e.patient_id = pr.person_id
		where
			(e.status = 'billed' or e.status = 'closed') and
			$patientSelectSql
			and balance > 0
		union
		select
			date_format(p.payment_date,'$format') item_date,
			c.code_text,
			c.code,
			0 charge,
			(pl.paid+pl.writeoff) credit,
			0.00,
			e.encounter_id,
			concat_ws(', ',pr.last_name,pr.first_name) patient_name
		from
			encounter e
			inner join clearhealth_claim cc using(encounter_id)
			inner join payment p on cc.claim_id = p.foreign_id
			inner join payment_claimline pl using(payment_id)
			inner join coding_data cd using(coding_data_id)
			inner join codes c ON(cd.code_id=c.code_id)
			inner join ($encounterBalanceSql) b on e.encounter_id = b.encounter_id
			inner join person pr on e.patient_id = pr.person_id
		where
			(e.status = 'billed' or e.status = 'closed') and
			$patientSelectSql
			and p.encounter_id = 0
			$withbalance
		) data
		"
		),
		array('item_date'=>'Date','code_text'=>'Code','charge'=>'Charge','credit'=>'Credit','outstanding'=>'Outstanding','patient_name'=>'Patient')
		);
		
	}
}