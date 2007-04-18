ALTER TABLE `fbclaim` ADD INDEX ( `claim_identifier` ) ;
ALTER TABLE `fbcompany` ADD INDEX ( `claim_id` ) ;
ALTER TABLE `fbdiagnoses` ADD INDEX ( `claimline_id` ) ;
ALTER TABLE `fblatest_revision` ADD INDEX ( `revision` ) ;
ALTER TABLE `fbperson` ADD INDEX ( `claim_id` ) ;
ALTER TABLE `fbpractice` ADD INDEX ( `claim_id` ) ;
ALTER TABLE `fbclaim` ADD INDEX ( `status` ) ;
ALTER TABLE `fbclaim` ADD INDEX ( `revision` ) ;
