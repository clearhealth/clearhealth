-- phpMyAdmin SQL Dump
-- version 2.6.1-rc2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Mar 29, 2005 at 08:12 PM
-- Server version: 4.0.23
-- PHP Version: 4.3.10

SET FOREIGN_KEY_CHECKS=0;

SET AUTOCOMMIT=0;
START TRANSACTION;

-- 
-- Database: `clearhealth`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `report_templates`
-- 

DROP TABLE IF EXISTS `report_templates`;
CREATE TABLE `report_templates` (
  `report_template_id` int(11) NOT NULL default '0',
  `report_id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `is_default` enum('yes','no') NOT NULL default 'yes',
  PRIMARY KEY  (`report_template_id`),
  KEY `report_id` (`report_id`)
) TYPE=MyISAM COMMENT='Report templates';

-- 
-- Dumping data for table `report_templates`
-- 

INSERT INTO `report_templates` VALUES (9, 8, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (10, 8, 'List View', 'no');
INSERT INTO `report_templates` VALUES (11, 10, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (792, 791, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1716, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1717, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1718, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1719, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1720, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1721, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1722, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1723, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1724, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1725, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1726, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1727, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1728, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1729, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1730, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1731, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1732, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1733, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1734, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1735, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1736, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1737, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1738, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1739, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1740, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1741, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1742, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1743, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1744, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1745, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1746, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1747, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1748, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1749, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1750, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1751, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1752, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1753, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1754, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1755, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1756, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1757, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1758, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1759, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1760, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1761, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1762, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1763, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1764, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1765, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1766, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1767, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1768, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1769, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1770, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1771, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1772, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1773, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1774, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1775, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1776, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1777, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1778, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1779, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1780, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1781, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1782, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1783, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1784, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1785, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1786, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1787, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1788, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1789, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1790, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1791, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1792, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1793, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1794, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1795, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1796, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1797, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1798, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1799, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1800, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1801, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1802, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1803, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1804, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (1805, 0, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (8169, 8168, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (8183, 8182, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (17076, 17075, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (17077, 17075, 'Invoice View', 'no');
INSERT INTO `report_templates` VALUES (17078, 17077, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (17079, 1705, 'Default Template', 'yes');
INSERT INTO `report_templates` VALUES (17080, 1775, 'Default Template', 'yes');

-- --------------------------------------------------------

-- 
-- Table structure for table `reports`
-- 

DROP TABLE IF EXISTS `reports`;
CREATE TABLE `reports` (
  `id` int(11) NOT NULL auto_increment,
  `dbase` varchar(255) NOT NULL default '',
  `user` varchar(255) NOT NULL default '',
  `label` varchar(255) NOT NULL default '',
  `query` text NOT NULL,
  `description` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='Report definitions TODO: change to Generic Seq' AUTO_INCREMENT=17076 ;

-- 
-- Dumping data for table `reports`
-- 

INSERT INTO `reports` VALUES (8, '', '', 'User List', 'select * from user where user_id = [user_id]', '');
INSERT INTO `reports` VALUES (791, '', '', 'Codes with Fee Schedule', 'select code, code_text, data as fee from codes c inner join fee_schedule_data fsd using(code_id)', 'Codes that have had a feed added to them');
INSERT INTO `reports` VALUES (8168, '', '', 'Multi-query test', '---[users]---\r\nselect * from user\r\n---[reports]---\r\nselect * from reports', '');
INSERT INTO `reports` VALUES (8182, '', '', 'Sub Query test', 'select * from encounter where treating_person_id = ''[provider:query-select p.person_id, concat_ws('' '',last_name,first_name) name from person p inner join person_type using(person_id) where person_type = 2]''', '');
INSERT INTO `reports` VALUES (17075, '', '', 'Exit Report', '---[practice]---\r\nselect \r\n p.name,\r\n a.line1,\r\n a.line2,\r\n a.city,\r\n a.state,\r\n a.postal_code\r\nfrom practices p \r\ninner join buildings b on p.id = b.practice_id\r\ninner join encounter e on b.id = e.building_id\r\nleft join practice_address pa on p.id = pa.practice_id\r\nleft join address a using(address_id)\r\nwhere address_type = 4 and e.encounter_id = ''[encounter_id:GET]''\r\n---[treating_facility]---\r\nselect \r\n b.name,\r\n a.line1,\r\n a.line2,\r\n a.city,\r\n a.state,\r\n a.postal_code\r\nfrom buildings b\r\ninner join encounter e on b.id = e.building_id\r\nleft join building_address ba on b.id = ba.building_id\r\nleft join address a using(address_id)\r\nwhere e.encounter_id = ''[encounter_id:GET]''\r\n---[treating_provider]---\r\nselect \r\n per.salutation,\r\n per.last_name,\r\n per.first_name,\r\n p.state_license_number,\r\n a.line1,\r\n a.line2,\r\n a.city,\r\n a.state,\r\n a.postal_code,\r\n n.number\r\n\r\nfrom provider p\r\ninner join person per using(person_id)\r\ninner join encounter e on p.person_id = e.treating_person_id\r\nleft join person_address pa on p.person_id = pa.person_id\r\nleft join address a using(address_id)\r\nleft join person_number pn on p.person_id = pn.person_id\r\nleft join number n using(number_id)\r\nwhere n.number_type = 1 and address_type =1  and e.encounter_id = ''[encounter_id:GET]''\r\n---[patient]---\r\nselect * from person p\r\ninner join patient pat using(person_id)\r\ninner join encounter e on p.person_id = e.patient_id\r\nleft join person_address pa on p.person_id = pa.person_id\r\nleft join address a using(address_id)\r\nleft join person_number pn on p.person_id = pn.person_id\r\nleft join number n using(number_id)\r\nwhere n.number_type = 1 and address_type =1  and e.encounter_id = ''[encounter_id:GET]''\r\n---[code_list]--- \r\nselect cpt.code_text `Procedure`, cpt.code Code, \r\nconcat_ws('', ''\r\n,max(case code_order when 1 then c.code else null end) \r\n,max(case code_order when 2 then c.code else null end)\r\n,max(case code_order when 3 then c.code else null end)\r\n,max(case code_order when 4 then c.code else null end) \r\n) Diagnosis, cd.modifier, cd.units, cd.fee\r\nfrom coding_data cd\r\ninner join codes c using(code_id)\r\ninner join codes cpt on cd.parent_id = cpt.code_id\r\ninner join encounter e on cd.foreign_id = e.encounter_id\r\nwhere e.encounter_id = ''[encounter_id:GET]''\r\ngroup by cd.parent_id\r\nunion\r\nselect ''Total'','''','''',null,sum(units),sum(fee)\r\nfrom coding_data cd\r\nwhere foreign_id = ''[encounter_id:GET]'' and primary_code = 1\r\n---[payment_history]---\r\nselect \r\npayment_date, amount, payment_type\r\nfrom payment\r\nwhere encounter_id = ''[encounter_id:GET]''\r\n---[encounter]---\r\nselect * from encounter e where e.encounter_id = ''[encounter_id:GET]''', '');

SET FOREIGN_KEY_CHECKS=1;

COMMIT;
