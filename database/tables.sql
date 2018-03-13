-- MySQL dump 10.13  Distrib 5.7.17, for macos10.12 (x86_64)
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
-- Table structure for table `wp_oa_troops`
--

DROP TABLE IF EXISTS `wp_oa_troops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_oa_troops` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chapter_num` int(11) NOT NULL,
  `district_num` int(11) NOT NULL,
  `unit_num` int(11) NOT NULL,
  `sm_full_name` varchar(80) DEFAULT NULL,
  `sm_phone_number` varchar(45) DEFAULT NULL,
  `sm_email` varchar(60) DEFAULT NULL,
  `sm_street` varchar(60) DEFAULT NULL,
  `sm_city` varchar(45) DEFAULT NULL,
  `sm_state` varchar(3) DEFAULT NULL,
  `sm_zip_code` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `troop_UNIQUE` (`chapter_num`,`unit_num`),
  KEY `chapter_num_idx` (`chapter_num`),
  KEY `district_num_fkey_idx` (`district_num`),
  CONSTRAINT `chapter_num_fkey` FOREIGN KEY (`chapter_num`) REFERENCES `wp_oa_chapters` (`chapter_num`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `district_ num_fkey` FOREIGN KEY (`district_num`) REFERENCES `wp_oa_districts` (`district_num`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=154 DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `wp_oa_ue_adults`
--

DROP TABLE IF EXISTS `wp_oa_ue_adults`;
/*!50001 DROP VIEW IF EXISTS `wp_oa_ue_adults`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `wp_oa_ue_adults` AS SELECT 
 1 AS `Submitted`,
 1 AS `FirstName`,
 1 AS `MiddleName`,
 1 AS `LastName`,
 1 AS `Suffix`,
 1 AS `ChapterName`,
 1 AS `UnitType`,
 1 AS `UnitNumber`,
 1 AS `CurrentPosition`,
 1 AS `AddressLine1`,
 1 AS `AddressLine2`,
 1 AS `City`,
 1 AS `State`,
 1 AS `ZipCode`,
 1 AS `Gender`,
 1 AS `DateOfBirth`,
 1 AS `BSAMemberID`,
 1 AS `HomeEmail`,
 1 AS `HomePhone`,
 1 AS `numyears`,
 1 AS `training`,
 1 AS `prevposition`,
 1 AS `RankAsYouth`,
 1 AS `commactivities`,
 1 AS `employment`,
 1 AS `camping`,
 1 AS `abilities`,
 1 AS `purpose`,
 1 AS `rolemodel`,
 1 AS `recommendation`,
 1 AS `accept`,
 1 AS `smname`,
 1 AS `ccpname`,
 1 AS `submitter-name`,
 1 AS `submitter-email`,
 1 AS `submitter-phone`,
 1 AS `Submitted From`,
 1 AS `fields_with_file`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `wp_oa_ue_candidates`
--

DROP TABLE IF EXISTS `wp_oa_ue_candidates`;
/*!50001 DROP VIEW IF EXISTS `wp_oa_ue_candidates`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `wp_oa_ue_candidates` AS SELECT 
 1 AS `Submitted`,
 1 AS `SubmitterType`,
 1 AS `ElectionDate`,
 1 AS `ChapterName`,
 1 AS `UnitType`,
 1 AS `UnitNumber`,
 1 AS `NumberElected`,
 1 AS `FirstName`,
 1 AS `MiddleName`,
 1 AS `LastName`,
 1 AS `Suffix`,
 1 AS `BSAMemberID`,
 1 AS `HomeEmail`,
 1 AS `HomePhone`,
 1 AS `AddressLine1`,
 1 AS `AddressLine2`,
 1 AS `City`,
 1 AS `State`,
 1 AS `ZipCode`,
 1 AS `DateOfBirth`,
 1 AS `SubmitterName`,
 1 AS `Submitted From`*/;
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
) ENGINE=InnoDB AUTO_INCREMENT=576 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `wp_oa_ue_schedules`
--

DROP TABLE IF EXISTS `wp_oa_ue_schedules`;
/*!50001 DROP VIEW IF EXISTS `wp_oa_ue_schedules`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `wp_oa_ue_schedules` AS SELECT 
 1 AS `Submitted`,
 1 AS `your-name`,
 1 AS `your-email`,
 1 AS `your-phone`,
 1 AS `tnum`,
 1 AS `position`,
 1 AS `Chapter`,
 1 AS `chapter-selector`,
 1 AS `ChapterNumber`,
 1 AS `meeting-location`,
 1 AS `meeting-day-time`,
 1 AS `oa-troop-rep`,
 1 AS `e-date-1`,
 1 AS `e-date-2`,
 1 AS `e-date-3`,
 1 AS `information`,
 1 AS `Submitted From`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `wp_oa_ue_troops`
--

DROP TABLE IF EXISTS `wp_oa_ue_troops`;
/*!50001 DROP VIEW IF EXISTS `wp_oa_ue_troops`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `wp_oa_ue_troops` AS SELECT 
 1 AS `Submitted`,
 1 AS `SubmitterType`,
 1 AS `ElectionDate`,
 1 AS `ChapterName`,
 1 AS `UnitType`,
 1 AS `UnitNumber`,
 1 AS `camp`,
 1 AS `MeetingLocation`,
 1 AS `UETeamNames`,
 1 AS `RegActiveYouth`,
 1 AS `YouthPresent`,
 1 AS `NumberEligible`,
 1 AS `NumberBallotsReturned`,
 1 AS `NumberRequired`,
 1 AS `NumberElected`,
 1 AS `UnitLeaderName`,
 1 AS `UnitLeaderEmail`,
 1 AS `UnitLeaderPhone`,
 1 AS `AdditionalInfo`,
 1 AS `SubmitterName`,
 1 AS `SubmitterEmail`,
 1 AS `SubmitterPhone`,
 1 AS `Submitted From`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `wp_oa_ue_troops_consolidated`
--

DROP TABLE IF EXISTS `wp_oa_ue_troops_consolidated`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wp_oa_ue_troops_consolidated` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ElectionDate` date NOT NULL,
  `ChapterName` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `UnitType` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `UnitNumber` int(4) NOT NULL,
  `camp` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `MeetingLocation` mediumtext COLLATE utf8_unicode_ci,
  `UETeamNames` mediumtext COLLATE utf8_unicode_ci,
  `RegActiveYouth` int(11) NOT NULL,
  `YouthPresent` int(11) NOT NULL,
  `NumberEligible` int(11) NOT NULL,
  `NumberBallotsReturned` int(11) NOT NULL,
  `NumberRequired` int(11) NOT NULL,
  `NumberElected` int(11) NOT NULL,
  `UnitLeaderName` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `UnitLeaderEmail` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `UnitLeaderPhone` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `AdditionalInfo` mediumtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unit_id` (`ChapterName`,`UnitType`,`UnitNumber`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Final view structure for view `wp_oa_ue_adults`
--

/*!50001 DROP VIEW IF EXISTS `wp_oa_ue_adults`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `wp_oa_ue_adults` AS select `wp_cf7dbplugin_submits`.`submit_time` AS `Submitted`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'FirstName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `FirstName`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'MiddleName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `MiddleName`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'LastName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `LastName`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'Suffix'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `Suffix`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'ChapterName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `ChapterName`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitType'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UnitType`,cast(max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitNumber'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) as unsigned) AS `UnitNumber`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'CurrentPosition'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `CurrentPosition`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'AddressLine1'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `AddressLine1`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'AddressLine2'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `AddressLine2`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'City'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `City`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'State'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `State`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'ZipCode'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `ZipCode`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'Gender'),`wp_cf7dbplugin_submits`.`field_value`,'M')) AS `Gender`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'DateOfBirth'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `DateOfBirth`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'BSAMemberID'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `BSAMemberID`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'HomeEmail'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `HomeEmail`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'HomePhone'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `HomePhone`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'numyears'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `numyears`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'training'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `training`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'prevposition'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `prevposition`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'RankAsYouth'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `RankAsYouth`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'commactivities'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `commactivities`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'employment'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `employment`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'camping'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `camping`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'abilities'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `abilities`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'purpose'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `purpose`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'rolemodel'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `rolemodel`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'recommendation'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `recommendation`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'accept'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `accept`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'smname'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `smname`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'ccpname'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `ccpname`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'submitter-name'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `submitter-name`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'submitter-email'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `submitter-email`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'submitter-phone'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `submitter-phone`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'Submitted From'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `Submitted From`,group_concat(if((isnull(`wp_cf7dbplugin_submits`.`file`) or (length(`wp_cf7dbplugin_submits`.`file`) = 0)),NULL,`wp_cf7dbplugin_submits`.`field_name`) separator ',') AS `fields_with_file` from `wp_cf7dbplugin_submits` where (`wp_cf7dbplugin_submits`.`form_name` = 'UE Adult Nomination') group by `wp_cf7dbplugin_submits`.`submit_time` order by `wp_cf7dbplugin_submits`.`submit_time` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `wp_oa_ue_candidates`
--

/*!50001 DROP VIEW IF EXISTS `wp_oa_ue_candidates`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `wp_oa_ue_candidates` AS select `wp_cf7dbplugin_submits`.`submit_time` AS `Submitted`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'SubmitterType'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `SubmitterType`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'ElectionDate'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `ElectionDate`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'ChapterName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `ChapterName`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitType'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UnitType`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitNumber'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UnitNumber`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'NumberElected'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `NumberElected`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'FirstName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `FirstName`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'MiddleName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `MiddleName`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'LastName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `LastName`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'Suffix'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `Suffix`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'BSAMemberID'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `BSAMemberID`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'HomeEmail'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `HomeEmail`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'HomePhone'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `HomePhone`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'AddressLine1'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `AddressLine1`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'AddressLine2'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `AddressLine2`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'City'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `City`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'State'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `State`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'ZipCode'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `ZipCode`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'DateOfBirth'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `DateOfBirth`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'SubmitterName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `SubmitterName`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'Submitted From'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `Submitted From` from `wp_cf7dbplugin_submits` where (`wp_cf7dbplugin_submits`.`form_name` = 'UE Report Candidates') group by `wp_cf7dbplugin_submits`.`submit_time` order by `wp_cf7dbplugin_submits`.`submit_time` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `wp_oa_ue_schedules`
--

/*!50001 DROP VIEW IF EXISTS `wp_oa_ue_schedules`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `wp_oa_ue_schedules` AS select `wp_cf7dbplugin_submits`.`submit_time` AS `Submitted`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'your-name'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `your-name`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'your-email'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `your-email`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'your-phone'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `your-phone`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'tnum'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `tnum`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'position'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `position`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'Chapter'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `Chapter`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'chapter-selector'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `chapter-selector`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'chapter-selector'),(select `wp_oa_chapters`.`chapter_num` from `wp_oa_chapters` where (locate((`wp_oa_chapters`.`ChiefEmail` collate utf8_unicode_ci),(`wp_cf7dbplugin_submits`.`field_value` collate utf8_unicode_ci)) > 0)),NULL)) AS `ChapterNumber`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'meeting-location'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `meeting-location`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'meeting-day-time'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `meeting-day-time`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'oa-troop-rep'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `oa-troop-rep`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'e-date-1'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `e-date-1`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'e-date-2'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `e-date-2`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'e-date-3'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `e-date-3`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'information'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `information`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'Submitted From'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `Submitted From` from `wp_cf7dbplugin_submits` where (`wp_cf7dbplugin_submits`.`form_name` = 'UE Scheduling Form') group by `wp_cf7dbplugin_submits`.`submit_time` order by `wp_cf7dbplugin_submits`.`submit_time` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `wp_oa_ue_troops`
--

/*!50001 DROP VIEW IF EXISTS `wp_oa_ue_troops`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50001 VIEW `wp_oa_ue_troops` AS select `wp_cf7dbplugin_submits`.`submit_time` AS `Submitted`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'SubmitterType'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `SubmitterType`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'ElectionDate'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `ElectionDate`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'ChapterName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `ChapterName`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitType'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UnitType`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitNumber'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UnitNumber`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'camp'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `camp`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'MeetingLocation'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `MeetingLocation`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UETeamNames'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UETeamNames`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'RegActiveYouth'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `RegActiveYouth`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'YouthPresent'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `YouthPresent`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'NumberEligible'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `NumberEligible`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'NumberBallotsReturned'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `NumberBallotsReturned`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'NumberRequired'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `NumberRequired`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'NumberElected'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `NumberElected`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitLeaderName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UnitLeaderName`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitLeaderEmail'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UnitLeaderEmail`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitLeaderPhone'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UnitLeaderPhone`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'AdditionalInfo'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `AdditionalInfo`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'SubmitterName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `SubmitterName`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'SubmitterEmail'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `SubmitterEmail`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'SubmitterPhone'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `SubmitterPhone`,max(if((`wp_cf7dbplugin_submits`.`field_name` = 'Submitted From'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `Submitted From` from `wp_cf7dbplugin_submits` where (`wp_cf7dbplugin_submits`.`form_name` = 'UE Report Troops') group by `wp_cf7dbplugin_submits`.`submit_time` order by `wp_cf7dbplugin_submits`.`submit_time` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-03-13  3:49:22
