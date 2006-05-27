SELECT
	a.*,
	e.*,
	DATE_FORMAT(e.start, "%W, the %D of %M, %Y, at %l:%i %p") AS appointment_date,
	pat.*,
	per.*,
	ad.line1, ad.line2, ad.city, ad.postal_code,
	ad_state_enum.value AS state,
	prac.name AS practice_name,
	prac_ad.line1 AS practice_line1, 
	prac_ad.line2 AS practice_line2,
	prac_ad.city AS practice_city,
	prac_ad_state_enum.value AS practice_state,
	prac_ad.postal_code AS practice_postal_code,
	CONCAT_WS('-', 
		LEFT(prac_num.number, 3),
		LEFT(RIGHT(prac_num.number, 7), 3),
		RIGHT(prac_num.number, 4)
	) AS practice_phone
FROM
	appointment AS a
	INNER JOIN event AS e USING(event_id)
	INNER JOIN patient AS pat ON(a.patient_id = pat.person_id)
	INNER JOIN person AS per USING(person_id)
	INNER JOIN person_address AS per_a USING(person_id)
	INNER JOIN address AS ad USING(address_id)
	INNER JOIN enumeration_value AS ad_state_enum ON(ad.state = ad_state_enum.key)
	INNER JOIN enumeration_definition AS ad_state_enum_def ON(
		ad_state_enum.enumeration_id = ad_state_enum_def.enumeration_id AND
		ad_state_enum_def.name = "state"
	)
	INNER JOIN practices AS prac ON (a.practice_id = prac.id)
	INNER JOIN practice_address AS prac_add_tie ON(prac.id = prac_add_tie.practice_id)
	INNER JOIN address AS prac_ad USING(address_id)
	INNER JOIN enumeration_value AS prac_ad_type ON (
		prac_add_tie.address_type = prac_ad_type.key AND
		prac_ad_type.value = 'Main'
	)
	INNER JOIN enumeration_definition AS prac_ad_type_def ON (
		prac_ad_type.enumeration_id = prac_ad_type_def.enumeration_id AND 
		prac_ad_type_def.name = "address_type"
	)
	INNER JOIN enumeration_value AS prac_ad_state_enum ON(prac_ad.state = prac_ad_state_enum.key)
	INNER JOIN enumeration_definition AS prac_ad_state_enum_def ON(
		prac_ad_state_enum.enumeration_id = prac_ad_state_enum_def.enumeration_id AND
		prac_ad_state_enum_def.name = "state"
	)
	INNER JOIN practice_number ON(prac.id = practice_number.practice_id)
	INNER JOIN number AS prac_num USING(number_id)
WHERE
	e.start >= NOW() AND
	e.start <= DATE_ADD(NOW(), INTERVAL 14 DAY)
GROUP BY
	a.appointment_id
