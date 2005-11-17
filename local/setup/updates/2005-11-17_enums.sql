update enumeration_definition set type = 'PersonType' where name = 'person_type';
update enumeration_value set extra1 = 1 where enumeration_value_id in (600596,600597,600598);
