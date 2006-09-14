CREATE TABLE `payer_group` (
`payer_group_id` INT NOT NULL ,
`name` VARCHAR( 255 ) NOT NULL ,
`description` TEXT NOT NULL ,
PRIMARY KEY ( `payer_group_id` )
) ENGINE = MYISAM ;

INSERT INTO `payer_group` ( `payer_group_id` , `name` , `description` )
VALUES (
'1', 'Default Group', 'Default payer group to exist for all payers/encounters (unless patient does not have Self Pay)'
);

CREATE TABLE `insurance_payergroup` (
  `payer_group_id` int(11) NOT NULL,
  `insurance_program_id` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  KEY `payer_group_id` (`payer_group_id`)
) ENGINE=MyISAM ;

INSERT INTO `insurance_payergroup` ( `payer_group_id` , `insurance_program_id` , `order` )
VALUES (
'1', '100001', '1'
);

INSERT INTO `menu` ( `menu_id` , `site_section` , `parent` , `dynamic_key` , `section` , `display_order` , `title` , `action` , `prefix` )
VALUES (
80 , 'billing', '24', '', 'children', '110', 'Payer Groups', 'PayerGroup/List', 'main'
), (
81 , 'admin', '37', '', 'children', '110', 'Payer Groups', 'PayerGroup/List', 'main'
);

ALTER TABLE `insured_relationship` ADD INDEX ( `person_id` ) ;
ALTER TABLE `insured_relationship` ADD INDEX ( `subscriber_id` ) ;
ALTER TABLE `insured_relationship` ADD INDEX ( `active` ) ;

