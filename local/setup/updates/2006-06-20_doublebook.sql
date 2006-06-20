INSERT INTO `appointment_ruleset` ( `appointment_ruleset_id` , `name` , `error_message` , `provider_id` , `procedure_id` , `room_id` )
VALUES (
'12345', 'Double Book', 'This appointment is conflicting with the following appointments:', '0', '0', '0'
);
INSERT INTO `appointment_rule` ( `appointment_rule_id` , `appointment_ruleset_id` , `type` , `label` , `data` )
VALUES (
'12346', '12345', 'DoubleBook', 'Double Book', ''
);

