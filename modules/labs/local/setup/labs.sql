-- phpMyAdmin SQL Dump
-- version 2.6.1-rc2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Feb 02, 2006 at 04:36 PM
-- Server version: 4.1.15
-- PHP Version: 4.3.11
-- 
-- Database: `clearhealth`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `lab_note`
-- 

CREATE TABLE `lab_note` (
  `lab_note_id` int(11) NOT NULL default '0',
  `lab_test_id` int(11) NOT NULL default '0',
  `note` text NOT NULL,
  PRIMARY KEY  (`lab_note_id`)
)  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `lab_order`
-- 

CREATE TABLE `lab_order` (
  `lab_order_id` int(11) NOT NULL default '0',
  `patient_id` int(11) NOT NULL default '0',
  `type` char(2) NOT NULL default '',
  `status` char(2) NOT NULL default '',
  `ordering_provider` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`lab_order_id`)
)  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `lab_result`
-- 

CREATE TABLE `lab_result` (
  `lab_result_id` int(11) NOT NULL default '0',
  `lab_test_id` int(11) NOT NULL default '0',
  `identifier` varchar(255) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  `units` varchar(255) NOT NULL default '',
  `reference_range` varchar(255) NOT NULL default '',
  `abnormal_flag` char(2) NOT NULL default '',
  `result_status` char(1) NOT NULL default '',
  `observation_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `producer_id` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`lab_result_id`)
)  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `lab_test`
-- 

CREATE TABLE `lab_test` (
  `lab_test_id` int(11) NOT NULL default '0',
  `lab_order_id` int(11) NOT NULL default '0',
  `order_num` varchar(255) NOT NULL default '',
  `filer_order_num` varchar(255) NOT NULL default '',
  `observation_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `specimen_received_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `report_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering_provider` varchar(255) NOT NULL default '',
  `service` varchar(255) NOT NULL default '',
  `component_code` varchar(255) NOT NULL default '',
  `status` char(1) NOT NULL default '',
  `clia_disclosure` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`lab_test_id`)
)  DEFAULT CHARSET=latin1;
        

