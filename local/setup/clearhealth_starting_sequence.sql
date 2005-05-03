-- phpMyAdmin SQL Dump
-- version 2.6.1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: May 03, 2005 at 01:03 PM
-- Server version: 4.1.10
-- PHP Version: 4.3.10
-- 
-- Database: `clearhealth`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `sequences`
-- 

CREATE TABLE `sequences` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `sequences`
-- 

INSERT INTO `sequences` VALUES (500000);

-- --------------------------------------------------------

-- 
-- Table structure for table `record_sequence`
-- 

CREATE TABLE `record_sequence` (
  `id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `record_sequence`
-- 

INSERT INTO `record_sequence` VALUES (200000);


