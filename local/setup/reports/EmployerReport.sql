SELECT
 CONCAT("<a href=\"", "{url:controller=Report&action=viewByCID}", "cid=employerDetail&rf%5bemployer_name%5d=", a.name, "\">", a.name, "</a>") AS 'Employer',
 COUNT(p.person_id) AS '# Patients'
FROM
 patient AS p
 INNER JOIN person_address AS pa USING(person_id)
 INNER JOIN address AS a USING(address_id)
WHERE
 pa.address_type IN (
   SELECT
    ev.key
   FROM
    enumeration_value AS ev
   WHERE
    ev.value = 'Employer')
GROUP BY
 a.name