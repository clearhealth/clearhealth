-- 
-- Dumping data for table `enumeration_definition`
-- 

INSERT INTO `enumeration_definition` VALUES (513682, 'refSpecialty', 'Specialists', 'Default');
INSERT INTO `enumeration_definition` VALUES (513700, 'refEligibility', 'Referal Eligibility', 'Default');
INSERT INTO `enumeration_definition` VALUES (513706, 'refRequested_time', 'Referal: Requested Time', 'Default');
INSERT INTO `enumeration_definition` VALUES (513718, 'days', 'Days of the Week', 'Default');
INSERT INTO `enumeration_definition` VALUES (513726, 'yesNo', 'Yes or No', 'Default');
INSERT INTO `enumeration_definition` VALUES (513734, 'refStatus', 'Referral: Status', 'Default');
INSERT INTO `enumeration_definition` VALUES (55, 'refEligibilitySchema', 'Referral: Eligibility Schema', 'PointToObject');
INSERT INTO `enumeration_definition` VALUES (255, 'refRejectionReason', 'Referral Rejection Reason', 'default');
INSERT INTO `enumeration_definition` VALUES (288, 'chlFollowUpReason', 'Follow Up Reason', 'default');
INSERT INTO `enumeration_definition` VALUES (363, 'emergency_contact_relationship', 'Emergency Contact Relationship', 'Default');
INSERT INTO `enumeration_definition` VALUES (394, 'refUserType', 'Referral: User Type', 'default');

-- 
-- Dumping data for table `enumeration_value`
-- 

INSERT INTO `enumeration_value` VALUES (513683, 513682, 1, 'Endocrinology', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (513684, 513682, 2, 'Cardiology', 1, '', '', 1);
INSERT INTO `enumeration_value` VALUES (513703, 513700, 3, 'No Status', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (513702, 513700, 2, 'In-Eligible', 0, 'In-Elig.', '', 1);
INSERT INTO `enumeration_value` VALUES (513701, 513700, 1, 'Eligible', 0, 'Elig.', '', 1);
INSERT INTO `enumeration_value` VALUES (513704, 513700, 4, 'Not Required', 0, 'Not Rqd.', '', 1);
INSERT INTO `enumeration_value` VALUES (513707, 513706, 1, '8:00 AM - Noon', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (513708, 513706, 2, '10:00 AM - 2:00 PM', 1, '', '', 1);
INSERT INTO `enumeration_value` VALUES (513709, 513706, 3, 'Noon - 4:00 PM', 2, '', '', 1);
INSERT INTO `enumeration_value` VALUES (513710, 513706, 4, '2:00 PM - 6:00 PM', 3, '', '', 1);
INSERT INTO `enumeration_value` VALUES (513711, 513706, 5, '4:00 PM - 8:00 PM', 4, '', '', 1);
INSERT INTO `enumeration_value` VALUES (513712, 513706, 6, 'Evening', 5, '', '', 1);
INSERT INTO `enumeration_value` VALUES (513719, 513718, 1, 'Monday', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (513720, 513718, 2, 'Tuesday', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (513721, 513718, 3, 'Wednesday', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (513722, 513718, 4, 'Thursday', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (513723, 513718, 5, 'Friday', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (513724, 513718, 6, 'Saturday', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (513725, 513718, 7, 'Sunday', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (513727, 513726, 1, 'Yes', 0, 'Y', '', 1);
INSERT INTO `enumeration_value` VALUES (513728, 513726, -1, 'No', 0, 'N', '', 1);
INSERT INTO `enumeration_value` VALUES (513735, 513734, 1, 'Requested', 1, '', '', 1);
INSERT INTO `enumeration_value` VALUES (513736, 513734, 2, 'Requested / Eligibility Pending', 0, 'Requested / Elig. Pending', '', 1);
INSERT INTO `enumeration_value` VALUES (513737, 513734, 3, 'Appointment Pending', 2, 'Appt Pending', '', 1);
INSERT INTO `enumeration_value` VALUES (513738, 513734, 4, 'Appointment Confirmed', 3, 'Appt Confirmed', '', 1);
INSERT INTO `enumeration_value` VALUES (513739, 513734, 5, 'Appointment Kept', 4, 'Appt Kept', '', 1);
INSERT INTO `enumeration_value` VALUES (513740, 513734, 6, 'Appointment No-Show', 5, 'Appt No-Show', '', 1);
INSERT INTO `enumeration_value` VALUES (513741, 513734, 7, 'Returned', 6, '', '', 1);
INSERT INTO `enumeration_value` VALUES (513742, 513734, 8, 'Cancelled', 7, '', '', 0);
INSERT INTO `enumeration_value` VALUES (1, 0, 0, '', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (26, 513682, 3, 'Sleep Specialist', 3, '', '', 1);
INSERT INTO `enumeration_value` VALUES (56, 55, 0, 'Not Applicable', 0, 'null', '', 1);
INSERT INTO `enumeration_value` VALUES (57, 55, 2, 'Default', 1, 'refEligibility', '', 1);
INSERT INTO `enumeration_value` VALUES (248, 513682, 4, 'Oncology', 4, '', '', 0);
INSERT INTO `enumeration_value` VALUES (249, 513682, 5, 'Podiatry', 2, '', '', 1);
INSERT INTO `enumeration_value` VALUES (256, 255, 1, 'Wait List Closed', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (257, 255, 2, 'Patient Ineligible', 1, '', '', 1);
INSERT INTO `enumeration_value` VALUES (258, 255, 3, 'No Spec Available', 2, '', '', 1);
INSERT INTO `enumeration_value` VALUES (259, 255, 4, 'Other', 3, '', '', 1);
INSERT INTO `enumeration_value` VALUES (289, 288, 1, 'Check Progress', 1, '', '', 1);
INSERT INTO `enumeration_value` VALUES (290, 288, 2, 'Other', 2, '', '', 1);
INSERT INTO `enumeration_value` VALUES (291, 288, 0, 'N/A', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (364, 363, 0, '', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (365, 363, 0, 'Aunt or Uncle', 1, '', '', 1);
INSERT INTO `enumeration_value` VALUES (395, 394, 1, 'Referral Initiator', 0, '', '', 1);
INSERT INTO `enumeration_value` VALUES (396, 394, 2, 'Referral Manager', 1, '', '', 1);
        
