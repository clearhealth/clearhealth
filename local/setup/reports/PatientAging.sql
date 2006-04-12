SELECT FP.claim_id,FP.record_number,SUM(CL.amount), SUM(CL.amount_paid) as paid, 
P.first_name, P.last_name
FROM fbperson as FP
INNER JOIN patient as PT on FP.record_number = PT.record_number
INNER JOIN `person` as P on PT.person_id = P.person_id
INNER JOIN fbclaimline as CL  on FP.claim_id = CL.claim_id
INNER JOIN fbclaim as C on CL.claim_id = C.claim_id
GROUP BY PT.record_number