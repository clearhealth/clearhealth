SELECT  C.code, C.code_text as Description, count(`procedure`) as count, SUM(C.fee) as Charges, CL.units, 
CL.amount_paid, C.units,AVG(C.fee) as Average
FROM fbclaimline as CL inner join codes  as C 
ON  `procedure` = C.code
WHERE date_of_treatment >= '[start_date_token:date]' and date_of_treatment <= '[end_date_token:date]'
GROUP BY  `procedure`