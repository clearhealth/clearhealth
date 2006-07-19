UPDATE 
	person 
SET
	primary_practice_id = (SELECT id FROM practices LIMIT 1	)
WHERE
	primary_practice_id = 0
