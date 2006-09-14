-- This clears out some of the tables before importing the old data --

TRUNCATE table category;
TRUNCATE table `user`;
TRUNCATE TABLE preferences;
TRUNCATE TABLE record_sequence;
TRUNCATE TABLE report_templates;
TRUNCATE TABLE reports;
TRUNCATE TABLE sequences;
TRUNCATE TABLE audit_log;
TRUNCATE TABLE audit_log_field;
TRUNCATE TABLE payment_claimline;
