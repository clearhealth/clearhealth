ALTER TABLE `form` ADD `system_name` VARCHAR( 255 ) NOT NULL ;
update form set system_name = name where system_name = '';
ALTER TABLE `form` ADD UNIQUE ( `system_name`);

