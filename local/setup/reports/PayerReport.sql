---[payer_report]---
SELECT 
  CONCAT("<a href=\"", "{url:controller=Report&action=viewByCID}", "cid=payercompanydrilldown&rf%5bcompany_id%5d=", c.company_id, "\">", c.name, "</a>") AS Payer,
  CONCAT("<a href=\"", "{url:controller=Report&action=viewByCID}", "cid=payerprogramdrilldown&rf%5binsurance_program_id%5d=", ip.insurance_program_id, "\">", ip.name, "</a>") AS Program_Name,
  COUNT(per.person_id) AS Total_Patients
FROM 
  company AS c
  LEFT JOIN insurance_program AS ip ON c.company_id = ip.company_id
  LEFT JOIN insured_relationship AS ir ON ip.insurance_program_id = ir.insurance_program_id
  LEFT JOIN person AS per ON ir.person_id = per.person_id
GROUP BY ip.insurance_program_id
ORDER BY Total_Patients, Payer, Program_Name
