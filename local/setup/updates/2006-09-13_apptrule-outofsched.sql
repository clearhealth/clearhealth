INSERT INTO `appointment_rule` (`appointment_rule_id`, `appointment_ruleset_id`, `type`, `label`, `data`) 
VALUES (12348, 12347, 'OutOfSchedule', 'Out Of Schedule', '');

INSERT INTO `appointment_ruleset` (`appointment_ruleset_id`, `name`, `error_message`, `enabled`, `provider_id`, `procedure_id`, `room_id`) 
VALUES (12347, 'Out Of Schedule', 'This appointment is outside of the provider/room\'s schedule.', 0, 0, 0, 0);
