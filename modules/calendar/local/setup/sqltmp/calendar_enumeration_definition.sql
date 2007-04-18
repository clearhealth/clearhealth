--- Do not use these if at all possible.  An enumeration.xml file needs to be added.

INSERT INTO `enumeration_definition` VALUES (600297, 'confidentiality_levels', 'Confidentiality Levels', 'Default');
INSERT INTO `enumeration_definition` VALUES (600305, 'subscriber_to_patient_relationship', 'Subscriber To Patient Relationship', 'Default');
INSERT INTO `enumeration_definition` VALUES (600331, 'days_of_week', 'Days of Week', 'Default');
INSERT INTO `enumeration_definition` VALUES (600339, 'weeks_of_month', 'Weeks of Month', 'Default');
INSERT INTO `enumeration_definition` VALUES (600345, 'months_of_year', 'Months of Year', 'Default');
INSERT INTO `enumeration_definition` VALUES (600358, 'recurrence_pattern_type', 'Recurrence Pattern Type', 'Default');


INSERT INTO `enumeration_value` VALUES (600332, 600331, '7', 'Sunday', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600333, 600331, '1', 'Monday', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600334, 600331, '2', 'Tuesday', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600335, 600331, '3', 'Wednesday', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600336, 600331, '4', 'Thursday', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600337, 600331, '5', 'Friday', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600338, 600331, '6', 'Saturday', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600340, 600339, 'First', 'First', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600341, 600339, 'Second', 'Second', 1, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600342, 600339, 'Third', 'Third', 2, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600343, 600339, 'Fourth', 'Fourth', 3, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600344, 600339, 'Last', 'Last', 4, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600346, 600345, '01', 'January', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600347, 600345, '02', 'February', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600348, 600345, '03', 'March', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600349, 600345, '04', 'April', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600350, 600345, '05', 'May', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600351, 600345, '06', 'June', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600352, 600345, '07', 'July', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600353, 600345, '08', 'August', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600354, 600345, '09', 'September', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600355, 600345, '10', 'October', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600356, 600345, '11', 'November', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600357, 600345, '12', 'December', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600359, 600358, 'day', 'By Day (Every 3 Days)', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600360, 600358, 'monthweek', 'By Weekday Per Month (Every Third Tuesday)', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600361, 600358, 'monthday', 'By Day of Month (Every Fifth)', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600362, 600358, 'yearmonthday', 'By Day of Month Per Year (Every December 3rd)', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (600363, 600358, 'yearmonthweek', 'By Weekday Per Month Per Year (Every Third Tuesday of November)', 0, '', '', 1);
