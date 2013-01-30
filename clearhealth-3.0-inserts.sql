INSERT INTO `user` ( `user_id` , `username` , `password` , `nickname` , `color` , `person_id` , `disabled` , `default_location_id` , `permissionTemplateId` ) VALUES ('1', 'admin', 'admin', 'adm', '', 1 , 'no', '0', 'superadmin');

INSERT INTO `config` VALUES ('enableCache', '0');

INSERT INTO `person` (`person_id`, `salutation`, `last_name`, `first_name`, `middle_name`, `suffix`, `gender`, `initials`, `date_of_birth`, `summary`, `title`, `notes`, `email`, `secondary_email`, `has_photo`, `identifier`, `identifier_type`, `marital_status`, `inactive`, `active`, `primary_practice_id`) VALUES 
(1, '', 'Clearhealth', 'Test', 'J', '', 1, '', '1970-01-01', '', '', '', '', '', '0', '111223333', 0, 0, 0, 1, 0);
INSERT INTO `sequences` ( `id` ) VALUES ( '1000000' );
INSERT INTO `auditSequences` ( `id` ) VALUES ( '1' );
INSERT INTO `record_sequence` ( `id` ) VALUES ( '10000' );
