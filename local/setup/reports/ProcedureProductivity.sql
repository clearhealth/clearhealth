SELECT  
	concat(p.last_name,', ',p.first_name) Provider,
	c.code, 
	c.code_text as Description, 
	cdc.category_name,
	cdc.category_id,
	count(`procedure`) as count, 
	SUM(c.fee) as charges, 
	cl.units, 
	cl.amount_paid, 
	c.units,
	ROUND(AVG(c.fee),2) as Average
FROM 
	fbclaimline as cl 
	inner join fbclaim claim on cl.claim_id = claim.claim_id
	inner join fblatest_revision fblr on fblr.claim_identifier = claim.claim_identifier and fblr.revision = claim.revision
	inner join codes as c ON  `procedure` = c.code
	inner join clearhealth_claim cc on claim.claim_identifier = cc.identifier
	inner join encounter e on cc.encounter_id = e.encounter_id
	inner join person p on e.treating_person_id = p.person_id

	/* Use this subquery to make sure each code only turns up once, im not sure how to handle a code being in multiple categories */
	left join (select
		code_id, category_name, category_id
	from
		code_to_category ctc
		inner join code_category cc on ctc.code_category_id = cc.code_category_id
	group by code_id
	) cdc on c.code_id = cdc.code_id

/* end from */
WHERE 
	cl.date_of_treatment >= '[start:date]' and cl.date_of_treatment <= '[end:date]'
	and if (
			length('[provider]') > 0,
			p.person_id = '[provider:query:select person.person_id, concat(last_name,', ',first_name) name from person inner join provider using(person_id)]',
			1
	)
GROUP BY  
	p.person_id,
	`procedure`
