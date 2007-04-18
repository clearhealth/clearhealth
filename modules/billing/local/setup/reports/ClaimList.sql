---[Claims_List]---
SELECT
c.claim_identifier AS 'Identifier',
c.revision AS 'Rev',
c.status AS 'Status',
c.claim_id AS 'Trns',
CONCAT_WS(', ',p.last_name,p.first_name) AS 'Name',
p.record_number AS 'Rec #',
co.name AS 'Facility',
CONCAT_WS(', ',pro.last_name,pro.first_name) AS 'Provider',
pa.name AS 'Payer',
SUM(cl.amount) AS 'Amount',
DATE_FORMAT(MIN(cl.date_of_treatment),'%m/%d/%Y') AS 'Date of Service',
CASE 
	WHEN c.date_sent != '0000-00-00 00:00:00' 
	THEN DATE_FORMAT(c.date_sent,'%m/%d/%Y')
	ELSE DATE_FORMAT(c.timestamp,'%m/%d/%Y')
END AS 'Rev Date'
FROM 
fblatest_revision AS lr
LEFT JOIN fbclaim AS c USING (claim_identifier,revision)
LEFT JOIN fbclaimline AS cl USING(claim_id)
LEFT JOIN fbperson AS p ON(p.claim_id = c.claim_id and p.type='FBPatient' and p.`index` = 0) 
LEFT JOIN fbcompany AS co ON(p.claim_id = co.claim_id and co.type='FBTreatingFacility' and co.`index` = 0)
LEFT JOIN fbcompany AS pa ON(p.claim_id = pa.claim_id and pa.type='FBPayer' and pa.`index` = 0)
LEFT JOIN fbperson AS pro ON(pro.claim_id = c.claim_id and pro.type='FBProvider' and pro.`index` = 0) 
WHERE
c.status != 'deleted' AND
IF (LENGTH('[status]'), c.status = '[status:sqlenum:fbclaim:status]', 1) AND
IF (LENGTH('[revision_start]'), IF (c.date_sent-0 != 0, c.date_sent >= '[revision_start:date]', c.timestamp >= '[revision_start:date]'), 1) AND
IF (LENGTH('[revision_end]'), IF (c.date_sent-0 != 0, c.date_sent <= '[revision_end:date]', c.timestamp <= '[revision_end:date]'), 1) AND
IF (LENGTH('[date_of_service_start]'), cl.date_of_treatment >= '[date_of_service_start:date]', 1) AND
IF (LENGTH('[date_of_service_end]'), cl.date_of_treatment <= '[date_of_service_end:date]', 1) AND
IF (LENGTH('[first_name]'), p.first_name = '[first_name]', 1) AND
IF (LENGTH('[last_name]'), p.last_name = '[last_name]', 1) AND
IF (LENGTH('[facility]'),co.name = '[facility:query:SELECT name, name FROM fbcompany WHERE `type` = "FBTreatingFacility" GROUP BY identifier]', 1) AND
IF (LENGTH('[identifier]'), c.claim_identifier = '[identifier]', 1) AND
IF (LENGTH('[payer]'), pa.name = '[payer]', 1) AND
IF (LENGTH('[provider]'),CONCAT(pro.first_name, ' ', pro.last_name) = "[provider:query:SELECT CONCAT(first_name, ' ', last_name), CONCAT(first_name, ' ', last_name) FROM fbperson WHERE type = 'FBProvider' GROUP BY CONCAT(first_name, last_name)]",1) 
GROUP BY c.claim_id

---[Claim_List_Totals,hideFilter]---
SELECT
COUNT(DISTINCT c.claim_identifier) AS 'Total # of Claims',
SUM(cl.amount) AS 'Total Amount'
FROM 
fblatest_revision AS lr
LEFT JOIN fbclaim AS c USING (claim_identifier,revision)
LEFT JOIN fbclaimline AS cl USING(claim_id)
LEFT JOIN fbperson AS p ON(p.claim_id = c.claim_id and p.type='FBPatient' and p.`index` = 0) 
LEFT JOIN fbcompany AS co ON(p.claim_id = co.claim_id and co.type='FBTreatingFacility' and co.`index` = 0)
LEFT JOIN fbcompany AS pa ON(p.claim_id = pa.claim_id and pa.type='FBPayer' and pa.`index` = 0)
LEFT JOIN fbperson AS pro ON(pro.claim_id = c.claim_id and pro.type='FBProvider' and pro.`index` = 0) 
WHERE
c.status != 'deleted' AND
IF (LENGTH('[status]'), c.status = '[status:sqlenum:fbclaim:status]', 1) AND
IF (LENGTH('[revision_start]'), IF (c.date_sent-0 != 0, c.date_sent >= '[revision_start:date]', c.timestamp >= '[revision_start:date]'), 1) AND
IF (LENGTH('[revision_end]'), IF (c.date_sent-0 != 0, c.date_sent <= '[revision_end:date]', c.timestamp <= '[revision_end:date]'), 1) AND
IF (LENGTH('[date_of_service_start]'), cl.date_of_treatment >= '[date_of_service_start:date]', 1) AND
IF (LENGTH('[date_of_service_end]'), cl.date_of_treatment <= '[date_of_service_end:date]', 1) AND
IF (LENGTH('[first_name]'), p.first_name = '[first_name]', 1) AND
IF (LENGTH('[last_name]'), p.last_name = '[last_name]', 1) AND
IF (LENGTH('[facility]'),co.name = '[facility:query:SELECT name, name FROM fbcompany WHERE `type` = "FBTreatingFacility" GROUP BY identifier]', 1) AND
IF (LENGTH('[identifier]'), c.claim_identifier = '[identifier]', 1) AND
IF (LENGTH('[payer]'), pa.name = '[payer]', 1) AND
IF (LENGTH('[provider]'),CONCAT(pro.first_name, ' ', pro.last_name) = '[provider]',1)
