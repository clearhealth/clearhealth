SELECT
	ev.value funds_source,
	sum(amount) payment
FROM
	payment p
	inner join insurance_program ip on p.payer_id = ip.insurance_program_id
	inner join enumeration_value ev on ip.funds_source = ev.key
	inner join enumeration_definition ed on ev.enumeration_id = ed.enumeration_id and ed.name = 'funds_source'
WHERE
	p.payment_date >= '[start:date]' and p.payment_date <= '[end:date]'
GROUP BY
	ip.funds_source
