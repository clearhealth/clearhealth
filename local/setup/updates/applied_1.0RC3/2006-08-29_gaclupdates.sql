UPDATE gacl_axo_seq SET id=111;

INSERT INTO gacl_groups_axo_map (group_id,axo_id) VALUES
(11,110),
(11,111);

INSERT INTO gacl_axo (id,section_value,`value`,order_value,`name`,hidden) VALUES
(110,'resources','payergroup',10,'Section - PayerGroup',0),
(111,'resources','formrule',10,'Section - FormRule',0);

