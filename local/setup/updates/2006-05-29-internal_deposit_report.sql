---[checks]---
SELECT payment_date, username, payment_type, ref_num as 'Check Num', amount FROM payment p INNER JOIN encounter e using (encounter_id) INNER JOIN buildings b on (e.building_id = b.id) INNER JOIN user u on (u.user_id=p.user_id) WHERE payment_type = 4 and payment_date = '[Date:date]' and e.building_id = '[facility:query:select b.id, CONCAT(p.name, '->', b.name)
from practices p INNER JOIN buildings b on b.practice_id = p.id order by p.name, b.name]'  GROUP BY payment_id 

/*** dsFilters-payment_type|enumLookup&ds|payment_type ***/

---[user_check_total,hideFilter]---
SELECT payment_date,  username, payment_type, SUM(amount) as 'Total' FROM payment p INNER JOIN encounter e using (encounter_id) INNER JOIN user u on (u.user_id=p.user_id) INNER JOIN buildings b on(e.building_id = b.id) WHERE payment_type = 4 and payment_date = '[Date:date]' and e.building_id = '[facility:query:select b.id, CONCAT(p.name, '->', b.name)
from practices p INNER JOIN buildings b on b.practice_id = p.id order by p.name, b.name]' GROUP BY username, payment_date

/*** dsFilters-payment_type|enumLookup&ds|payment_type ***/

---[check_total,hideFilter]---
SELECT payment_date,  payment_type, SUM(amount) as 'Total' FROM payment INNER JOIN encounter e using (encounter_id) INNER JOIN buildings b on(e.building_id = b.id) WHERE payment_type = 4 and payment_date = '[Date:date]' and e.building_id = '[facility:query:select b.id, CONCAT(p.name, '->', b.name)
from practices p INNER JOIN buildings b on b.practice_id = p.id order by p.name, b.name]' GROUP BY payment_date

/*** dsFilters-payment_type|enumLookup&ds|payment_type ***/


---[cash,hideFilter]---
SELECT payment_date, username, payment_type, SUM(amount) as 'Total Cash' FROM payment p INNER JOIN encounter e using (encounter_id) INNER JOIN user u on (u.user_id=p.user_id) INNER JOIN buildings b on(e.building_id = b.id) WHERE payment_type = 5 and payment_date = '[Date:date]' and e.building_id = '[facility:query:select b.id, CONCAT(p.name, '->', b.name)
from practices p INNER JOIN buildings b on b.practice_id = p.id order by p.name, b.name]' GROUP BY payment_date

/*** dsFilters-payment_type|enumLookup&ds|payment_type ***/

---[user_cash_total,hideFilter]---
SELECT payment_date,  username, payment_type, SUM(amount) as 'Total' FROM payment p INNER JOIN encounter e using (encounter_id) INNER JOIN user u on (u.user_id=p.user_id) INNER JOIN buildings b on(e.building_id = b.id) WHERE payment_type = 5 and payment_date = '[Date:date]' and e.building_id = '[facility:query:select b.id, CONCAT(p.name, '->', b.name)
from practices p INNER JOIN buildings b on b.practice_id = p.id order by p.name, b.name]' GROUP BY username, payment_date

/*** dsFilters-payment_type|enumLookup&ds|payment_type ***/


---[cash_total,hideFilter]---
SELECT payment_date, payment_type, SUM(amount) as 'Total Cash' FROM payment INNER JOIN encounter e using (encounter_id) INNER JOIN buildings b on(e.building_id = b.id) WHERE payment_type = 5 and payment_date = '[Date:date]' and e.building_id = '[facility:query:select b.id, CONCAT(p.name, '->', b.name)
from practices p INNER JOIN buildings b on b.practice_id = p.id order by p.name, b.name]' GROUP BY payment_date

/*** dsFilters-payment_type|enumLookup&ds|payment_type ***/
