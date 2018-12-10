-- MySQL dump 10.13  Distrib 5.5.59, for debian-linux-gnu (x86_64)
-- Server version	5.6.34-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `wp_oa_chapters`
--

DROP TABLE IF EXISTS `wp_oa_chapters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_oa_chapters` (
  `chapter_num` int(11) NOT NULL,
  `ChapterName` varchar(45) CHARACTER SET utf8 NOT NULL,
  `SelectorName` varchar(120) CHARACTER SET utf8 NOT NULL,
  `ChiefEmail` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`chapter_num`),
  UNIQUE KEY `ChapterName_UNIQUE` (`ChapterName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wp_oa_districts`
--

DROP TABLE IF EXISTS `wp_oa_districts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_oa_districts` (
  `district_num` int(11) NOT NULL AUTO_INCREMENT,
  `district_name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`district_num`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wp_oa_ue_adult_nominations`
--

DROP TABLE IF EXISTS `wp_oa_ue_adult_nominations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_oa_ue_adult_nominations` (
  `row_id` int(11) NOT NULL AUTO_INCREMENT,
  `Submitted` varchar(128) DEFAULT NULL,
  `ElectionDate` date DEFAULT NULL,
  `ChapterName` varchar(128) DEFAULT NULL,
  `UnitType` varchar(128) DEFAULT NULL,
  `UnitNumber` int(11) DEFAULT NULL,
  `FirstName` varchar(128) DEFAULT NULL,
  `MiddleName` varchar(128) DEFAULT NULL,
  `LastName` varchar(128) DEFAULT NULL,
  `Suffix` varchar(128) DEFAULT NULL,
  `BSAMemberID` int(11) DEFAULT NULL,
  `HomeEmail` varchar(128) DEFAULT NULL,
  `HomePhone` varchar(128) DEFAULT NULL,
  `AddressLine1` varchar(128) DEFAULT NULL,
  `AddressLine2` varchar(128) DEFAULT NULL,
  `City` varchar(128) DEFAULT NULL,
  `State` varchar(128) DEFAULT NULL,
  `ZipCode` varchar(128) DEFAULT NULL,
  `Gender` varchar(1) NOT NULL DEFAULT 'M',
  `DateOfBirth` varchar(128) DEFAULT NULL,
  `SubmitterName` varchar(128) DEFAULT NULL,
  `SubmittedFrom` varchar(128) DEFAULT NULL,
  `Approved` int(11) NOT NULL DEFAULT '0',
  `RowExported` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`row_id`),
  UNIQUE KEY `BSAMemberID_UNIQUE` (`BSAMemberID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `wp_oa_ue_adults`
--

DROP TABLE IF EXISTS `wp_oa_ue_adults`;
/*!50001 DROP VIEW IF EXISTS `wp_oa_ue_adults`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `wp_oa_ue_adults` (
  `Submitted` tinyint NOT NULL,
  `FirstName` tinyint NOT NULL,
  `MiddleName` tinyint NOT NULL,
  `LastName` tinyint NOT NULL,
  `Suffix` tinyint NOT NULL,
  `ChapterName` tinyint NOT NULL,
  `UnitType` tinyint NOT NULL,
  `UnitNumber` tinyint NOT NULL,
  `CurrentPosition` tinyint NOT NULL,
  `AddressLine1` tinyint NOT NULL,
  `AddressLine2` tinyint NOT NULL,
  `City` tinyint NOT NULL,
  `State` tinyint NOT NULL,
  `ZipCode` tinyint NOT NULL,
  `Gender` tinyint NOT NULL,
  `DateOfBirth` tinyint NOT NULL,
  `BSAMemberID` tinyint NOT NULL,
  `HomeEmail` tinyint NOT NULL,
  `HomePhone` tinyint NOT NULL,
  `numyears` tinyint NOT NULL,
  `training` tinyint NOT NULL,
  `prevposition` tinyint NOT NULL,
  `RankAsYouth` tinyint NOT NULL,
  `commactivities` tinyint NOT NULL,
  `employment` tinyint NOT NULL,
  `camping` tinyint NOT NULL,
  `abilities` tinyint NOT NULL,
  `purpose` tinyint NOT NULL,
  `rolemodel` tinyint NOT NULL,
  `recommendation` tinyint NOT NULL,
  `accept` tinyint NOT NULL,
  `smname` tinyint NOT NULL,
  `ccpname` tinyint NOT NULL,
  `submitter-name` tinyint NOT NULL,
  `submitter-email` tinyint NOT NULL,
  `submitter-phone` tinyint NOT NULL,
  `Submitted From` tinyint NOT NULL,
  `fields_with_file` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `wp_oa_ue_candidates`
--

DROP TABLE IF EXISTS `wp_oa_ue_candidates`;
/*!50001 DROP VIEW IF EXISTS `wp_oa_ue_candidates`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `wp_oa_ue_candidates` (
  `Submitted` tinyint NOT NULL,
  `SubmitterType` tinyint NOT NULL,
  `ElectionDate` tinyint NOT NULL,
  `ChapterName` tinyint NOT NULL,
  `UnitType` tinyint NOT NULL,
  `UnitNumber` tinyint NOT NULL,
  `NumberElected` tinyint NOT NULL,
  `FirstName` tinyint NOT NULL,
  `MiddleName` tinyint NOT NULL,
  `LastName` tinyint NOT NULL,
  `Suffix` tinyint NOT NULL,
  `BSAMemberID` tinyint NOT NULL,
  `HomeEmail` tinyint NOT NULL,
  `HomePhone` tinyint NOT NULL,
  `AddressLine1` tinyint NOT NULL,
  `AddressLine2` tinyint NOT NULL,
  `City` tinyint NOT NULL,
  `State` tinyint NOT NULL,
  `ZipCode` tinyint NOT NULL,
  `DateOfBirth` tinyint NOT NULL,
  `SubmitterName` tinyint NOT NULL,
  `Submitted From` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `wp_oa_ue_candidates_merged`
--

DROP TABLE IF EXISTS `wp_oa_ue_candidates_merged`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_oa_ue_candidates_merged` (
  `row_id` int(11) NOT NULL AUTO_INCREMENT,
  `Submitted` varchar(128) DEFAULT NULL,
  `SubmitterType` varchar(128) DEFAULT NULL,
  `ElectionDate` date DEFAULT NULL,
  `ChapterName` varchar(128) DEFAULT NULL,
  `UnitType` varchar(128) DEFAULT NULL,
  `UnitNumber` int(11) DEFAULT NULL,
  `NumberElected` varchar(128) DEFAULT NULL,
  `FirstName` varchar(128) DEFAULT NULL,
  `MiddleName` varchar(128) DEFAULT NULL,
  `LastName` varchar(128) DEFAULT NULL,
  `Suffix` varchar(128) DEFAULT NULL,
  `BSAMemberID` int(11) DEFAULT NULL,
  `HomeEmail` varchar(128) DEFAULT NULL,
  `HomePhone` varchar(128) DEFAULT NULL,
  `AddressLine1` varchar(128) DEFAULT NULL,
  `AddressLine2` varchar(128) DEFAULT NULL,
  `City` varchar(128) DEFAULT NULL,
  `State` varchar(128) DEFAULT NULL,
  `ZipCode` varchar(128) DEFAULT NULL,
  `DateOfBirth` varchar(128) DEFAULT NULL,
  `SubmitterName` varchar(128) DEFAULT NULL,
  `SubmittedFrom` varchar(128) DEFAULT NULL,
  `RowExported` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`row_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `wp_oa_ue_schedules`
--

DROP TABLE IF EXISTS `wp_oa_ue_schedules`;
/*!50001 DROP VIEW IF EXISTS `wp_oa_ue_schedules`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `wp_oa_ue_schedules` (
  `Submitted` tinyint NOT NULL,
  `your-name` tinyint NOT NULL,
  `your-email` tinyint NOT NULL,
  `your-phone` tinyint NOT NULL,
  `chapter-selector` tinyint NOT NULL,
  `ChapterName` tinyint NOT NULL,
  `UnitType` tinyint NOT NULL,
  `UnitNum` tinyint NOT NULL,
  `position` tinyint NOT NULL,
  `meeting-location` tinyint NOT NULL,
  `meeting-day-time` tinyint NOT NULL,
  `oa-unit-rep` tinyint NOT NULL,
  `e-date-1` tinyint NOT NULL,
  `e-date-2` tinyint NOT NULL,
  `e-date-3` tinyint NOT NULL,
  `information` tinyint NOT NULL,
  `Submitted Login` tinyint NOT NULL,
  `Submitted From` tinyint NOT NULL,
  `fields_with_file` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `wp_oa_ue_units`
--

DROP TABLE IF EXISTS `wp_oa_ue_units`;
/*!50001 DROP VIEW IF EXISTS `wp_oa_ue_units`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `wp_oa_ue_units` (
  `Submitted` tinyint NOT NULL,
  `SubmitterType` tinyint NOT NULL,
  `ChapterName` tinyint NOT NULL,
  `UnitType` tinyint NOT NULL,
  `UnitNumber` tinyint NOT NULL,
  `ElectionDate` tinyint NOT NULL,
  `camp` tinyint NOT NULL,
  `notification` tinyint NOT NULL,
  `MeetingLocation` tinyint NOT NULL,
  `UETeamNames` tinyint NOT NULL,
  `RegActiveYouth` tinyint NOT NULL,
  `YouthPresent` tinyint NOT NULL,
  `NumberEligible` tinyint NOT NULL,
  `NumberBallotsReturned` tinyint NOT NULL,
  `NumberRequired` tinyint NOT NULL,
  `NumberElected` tinyint NOT NULL,
  `UnitLeaderName` tinyint NOT NULL,
  `UnitLeaderEmail` tinyint NOT NULL,
  `UnitLeaderPhone` tinyint NOT NULL,
  `AdditionalInfo` tinyint NOT NULL,
  `SubmitterName` tinyint NOT NULL,
  `SubmitterEmail` tinyint NOT NULL,
  `SubmitterPhone` tinyint NOT NULL,
  `Submitted Login` tinyint NOT NULL,
  `Submitted From` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `wp_oa_units`
--

DROP TABLE IF EXISTS `wp_oa_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_oa_units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chapter_num` int(11) NOT NULL,
  `district_num` int(11) NOT NULL,
  `unit_type` varchar(10) NOT NULL,
  `unit_num` int(11) NOT NULL,
  `unit_city` varchar(45) DEFAULT NULL,
  `unit_state` varchar(3) DEFAULT NULL,
  `unit_county` varchar(45) DEFAULT NULL,
  `charter_org` varchar(120) DEFAULT NULL,
  `ul_full_name` varchar(80) DEFAULT NULL,
  `ul_phone_number` varchar(45) DEFAULT NULL,
  `ul_email` varchar(60) DEFAULT NULL,
  `cc_full_name` varchar(80) DEFAULT NULL,
  `cc_phone_number` varchar(45) DEFAULT NULL,
  `cc_email` varchar(60) DEFAULT NULL,
  `adv_full_name` varchar(80) DEFAULT NULL,
  `adv_phone_number` varchar(45) DEFAULT NULL,
  `adv_email` varchar(60) DEFAULT NULL,
  `rep_full_name` varchar(80) DEFAULT NULL,
  `rep_email` varchar(45) DEFAULT NULL,
  `rep_phone_number` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `troop_UNIQUE` (`district_num`,`unit_type`,`unit_num`),
  KEY `chapter_num_idx` (`chapter_num`),
  KEY `district_num_fkey_idx` (`district_num`),
  CONSTRAINT `chapter_num_fkey` FOREIGN KEY (`chapter_num`) REFERENCES `wp_oa_chapters` (`chapter_num`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `district_ num_fkey` FOREIGN KEY (`district_num`) REFERENCES `wp_oa_districts` (`district_num`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=176 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wp_oalm_dues_data`
--

DROP TABLE IF EXISTS `wp_oalm_dues_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_oalm_dues_data` (
  `bsaid` int(11) NOT NULL,
  `max_dues_year` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dues_paid_date` date DEFAULT NULL,
  `level` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reg_audit_date` date DEFAULT NULL,
  `reg_audit_result` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`bsaid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Final view structure for view `wp_oa_ue_adults`
--

/*!50001 DROP TABLE IF EXISTS `wp_oa_ue_adults`*/;
/*!50001 DROP VIEW IF EXISTS `wp_oa_ue_adults`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`nslodge`@`67.205.0.0/255.255.192.0` SQL SECURITY DEFINER */
/*!50001 VIEW `wp_oa_ue_adults` AS select `wp_cf7dbplugin_submits`.`submit_time` AS `Submitted`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'FirstName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `FirstName`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'MiddleName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `MiddleName`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'LastName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `LastName`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'Suffix'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `Suffix`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'ChapterName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `ChapterName`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitType'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UnitType`,cast(max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitNumber'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) as unsigned) AS `UnitNumber`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'CurrentPosition'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `CurrentPosition`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'AddressLine1'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `AddressLine1`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'AddressLine2'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `AddressLine2`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'City'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `City`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'State'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `State`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'ZipCode'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `ZipCode`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'Gender'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `Gender`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'DateOfBirth'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `DateOfBirth`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'BSAMemberID'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `BSAMemberID`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'HomeEmail'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `HomeEmail`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'HomePhone'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `HomePhone`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'numyears'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `numyears`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'training'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `training`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'prevposition'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `prevposition`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'RankAsYouth'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `RankAsYouth`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'commactivities'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `commactivities`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'employment'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `employment`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'camping'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `camping`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'abilities'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `abilities`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'purpose'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `purpose`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'rolemodel'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `rolemodel`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'recommendation'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `recommendation`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'accept'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `accept`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'smname'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `smname`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'ccpname'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `ccpname`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'submitter-name'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `submitter-name`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'submitter-email'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `submitter-email`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'submitter-phone'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `submitter-phone`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'Submitted From'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `Submitted From`,group_concat(if((isnull(`wp_cf7dbplugin_submits`.`file`) or (length(`wp_cf7dbplugin_submits`.`file`) = 0)),NULL,`wp_cf7dbplugin_submits`.`field_name`) separator ',') AS `fields_with_file` from `wp_cf7dbplugin_submits` where (`wp_cf7dbplugin_submits`.`form_name` = 'UE Adult Nomination') group by `wp_cf7dbplugin_submits`.`submit_time` order by `wp_cf7dbplugin_submits`.`submit_time` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `wp_oa_ue_candidates`
--

/*!50001 DROP TABLE IF EXISTS `wp_oa_ue_candidates`*/;
/*!50001 DROP VIEW IF EXISTS `wp_oa_ue_candidates`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`nslodge`@`67.205.0.0/255.255.192.0` SQL SECURITY INVOKER */
/*!50001 VIEW `wp_oa_ue_candidates` AS select `wp_cf7dbplugin_submits`.`submit_time` AS `Submitted`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'SubmitterType'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `SubmitterType`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'ElectionDate'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `ElectionDate`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'ChapterName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `ChapterName`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitType'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UnitType`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitNumber'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UnitNumber`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'NumberElected'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `NumberElected`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'FirstName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `FirstName`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'MiddleName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `MiddleName`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'LastName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `LastName`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'Suffix'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `Suffix`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'BSAMemberID'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `BSAMemberID`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'HomeEmail'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `HomeEmail`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'HomePhone'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `HomePhone`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'AddressLine1'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `AddressLine1`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'AddressLine2'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `AddressLine2`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'City'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `City`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'State'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `State`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'ZipCode'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `ZipCode`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'DateOfBirth'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `DateOfBirth`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'SubmitterName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `SubmitterName`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'Submitted From'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `Submitted From` from `wp_cf7dbplugin_submits` where (`wp_cf7dbplugin_submits`.`form_name` = 'UE Report Candidates') group by `wp_cf7dbplugin_submits`.`submit_time` order by `wp_cf7dbplugin_submits`.`submit_time` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `wp_oa_ue_schedules`
--

/*!50001 DROP TABLE IF EXISTS `wp_oa_ue_schedules`*/;
/*!50001 DROP VIEW IF EXISTS `wp_oa_ue_schedules`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`justdave`@`66.33.192.0/255.255.224.0` SQL SECURITY INVOKER */
/*!50001 VIEW `wp_oa_ue_schedules` AS select `wp_cf7dbplugin_submits`.`submit_time` AS `Submitted`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'your-name'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `your-name`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'your-email'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `your-email`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'your-phone'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `your-phone`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'chapter-selector'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `chapter-selector`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'ChapterName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `ChapterName`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitType'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UnitType`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitNum'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UnitNum`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'position'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `position`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'meeting-location'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `meeting-location`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'meeting-day-time'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `meeting-day-time`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'oa-unit-rep'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `oa-unit-rep`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'e-date-1'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `e-date-1`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'e-date-2'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `e-date-2`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'e-date-3'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `e-date-3`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'information'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `information`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'Submitted Login'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `Submitted Login`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'Submitted From'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `Submitted From`,group_concat(if((isnull(`wp_cf7dbplugin_submits`.`file`) or (length(`wp_cf7dbplugin_submits`.`file`) = 0)),NULL,`wp_cf7dbplugin_submits`.`field_name`) separator ',') AS `fields_with_file` from `wp_cf7dbplugin_submits` where (`wp_cf7dbplugin_submits`.`form_name` = 'UE Scheduling Form') group by `wp_cf7dbplugin_submits`.`submit_time` order by `wp_cf7dbplugin_submits`.`submit_time` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `wp_oa_ue_units`
--

/*!50001 DROP TABLE IF EXISTS `wp_oa_ue_units`*/;
/*!50001 DROP VIEW IF EXISTS `wp_oa_ue_units`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`justdave`@`66.33.192.0/255.255.224.0` SQL SECURITY INVOKER */
/*!50001 VIEW `wp_oa_ue_units` AS select `wp_cf7dbplugin_submits`.`submit_time` AS `Submitted`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'SubmitterType'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `SubmitterType`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'ChapterName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `ChapterName`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitType'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UnitType`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitNumber'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UnitNumber`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'ElectionDate'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `ElectionDate`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'camp'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `camp`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'notification'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `notification`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'MeetingLocation'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `MeetingLocation`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UETeamNames'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UETeamNames`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'RegActiveYouth'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `RegActiveYouth`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'YouthPresent'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `YouthPresent`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'NumberEligible'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `NumberEligible`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'NumberBallotsReturned'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `NumberBallotsReturned`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'NumberRequired'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `NumberRequired`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'NumberElected'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `NumberElected`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitLeaderName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UnitLeaderName`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitLeaderEmail'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UnitLeaderEmail`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitLeaderPhone'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UnitLeaderPhone`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'AdditionalInfo'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `AdditionalInfo`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'SubmitterName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `SubmitterName`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'SubmitterEmail'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `SubmitterEmail`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'SubmitterPhone'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `SubmitterPhone`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'Submitted Login'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `Submitted Login`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'Submitted From'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `Submitted From` from `wp_cf7dbplugin_submits` where (`wp_cf7dbplugin_submits`.`form_name` = 'UE Report Units') group by `wp_cf7dbplugin_submits`.`submit_time` order by `wp_cf7dbplugin_submits`.`submit_time` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Dumping routines for database 'wp_dev_nslodge_org'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-12-09 19:04:10
