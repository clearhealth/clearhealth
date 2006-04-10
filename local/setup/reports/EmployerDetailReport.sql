---[employer_detail,hideFilter]---
SELECT
 {link:controller=PatientDashboard&action=view&columnName=CONCAT(first_name,' ',last_name)&id=p.person_id} AS 'Name',
 n.number AS 'Phone'
FROM
 patient AS p
 INNER JOIN person AS e USING(person_id)
 INNER JOIN person_address AS pa USING(person_id)
 INNER JOIN address AS a USING(address_id)
 LEFT JOIN person_number AS pn ON (pa.person_id = pn.person_id)
 LEFT JOIN number AS n ON (pn.number_id = n.number_id AND n.number_type = 1)
WHERE
 a.name = '[employer_name]'
GROUP BY
 p.person_id
ORDER BY
 first_name