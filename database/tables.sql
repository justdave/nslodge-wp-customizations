CREATE TABLE `wp_oa_chapters` (
  `chapter_num` int(11) NOT NULL,
  `ChapterName` varchar(45) CHARACTER SET utf8 NOT NULL,
  `SelectorName` varchar(120) CHARACTER SET utf8 NOT NULL,
  `District` varchar(45) CHARACTER SET utf8 NOT NULL,
  `ChiefEmail` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`chapter_num`),
  UNIQUE KEY `ChapterName_UNIQUE` (`ChapterName`),
  UNIQUE KEY `ChiefEmail_UNIQUE` (`District`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE VIEW `wp_oa_ue_adults` AS 
select `wp_cf7dbplugin_submits`.`submit_time` AS `Submitted`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'FirstName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `FirstName`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'MiddleName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `MiddleName`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'LastName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `LastName`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'Suffix'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `Suffix`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'ChapterName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `ChapterName`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitType'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UnitType`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitNumber'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UnitNumber`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'CurrentPosition'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `CurrentPosition`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'AddressLine1'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `AddressLine1`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'AddressLine2'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `AddressLine2`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'City'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `City`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'State'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `State`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'ZipCode'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `ZipCode`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'Gender'),`wp_cf7dbplugin_submits`.`field_value`,'M')) AS `Gender`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'DateOfBirth'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `DateOfBirth`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'BSAMemberID'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `BSAMemberID`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'HomeEmail'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `HomeEmail`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'HomePhone'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `HomePhone`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'numyears'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `numyears`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'training'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `training`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'prevposition'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `prevposition`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'menu-511'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `menu-511`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'commactivities'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `commactivities`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'employment'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `employment`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'camping'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `camping`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'abilities'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `abilities`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'purpose'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `purpose`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'rolemodel'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `rolemodel`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'recommendation'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `recommendation`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'accept'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `accept`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'smname'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `smname`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'ccpname'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `ccpname`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'submitter-name'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `submitter-name`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'submitter-email'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `submitter-email`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'submitter-phone'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `submitter-phone`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'Submitted From'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `Submitted From`,
       group_concat(if((isnull(`wp_cf7dbplugin_submits`.`file`) or (length(`wp_cf7dbplugin_submits`.`file`) = 0)),NULL,`wp_cf7dbplugin_submits`.`field_name`) separator ',') AS `fields_with_file`
from `wp_cf7dbplugin_submits`
where (`wp_cf7dbplugin_submits`.`form_name` = 'UE Adult Nomination')
group by `wp_cf7dbplugin_submits`.`submit_time`
order by `wp_cf7dbplugin_submits`.`submit_time` desc;

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
) ENGINE=InnoDB AUTO_INCREMENT=296 DEFAULT CHARSET=utf8;

CREATE VIEW `wp_oa_ue_candidates` AS
       select `wp_cf7dbplugin_submits`.`submit_time` AS `Submitted`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'SubmitterType'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `SubmitterType`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'ElectionDate'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `ElectionDate`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'ChapterName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `ChapterName`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitType'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UnitType`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitNumber'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UnitNumber`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'NumberElected'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `NumberElected`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'FirstName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `FirstName`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'MiddleName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `MiddleName`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'LastName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `LastName`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'Suffix'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `Suffix`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'BSAMemberID'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `BSAMemberID`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'HomeEmail'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `HomeEmail`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'HomePhone'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `HomePhone`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'AddressLine1'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `AddressLine1`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'AddressLine2'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `AddressLine2`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'City'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `City`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'State'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `State`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'ZipCode'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `ZipCode`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'DateOfBirth'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `DateOfBirth`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'SubmitterName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `SubmitterName`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'Submitted From'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `Submitted From`
from `wp_cf7dbplugin_submits`
where (`wp_cf7dbplugin_submits`.`form_name` = 'UE Report Candidates')
group by `wp_cf7dbplugin_submits`.`submit_time`
order by `wp_cf7dbplugin_submits`.`submit_time` desc;

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
) ENGINE=InnoDB AUTO_INCREMENT=299 DEFAULT CHARSET=utf8;

CREATE VIEW `wp_oa_ue_schedules` AS select `wp_cf7dbplugin_submits`.`submit_time` AS `Submitted`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'your-name'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `your-name`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'your-email'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `your-email`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'your-phone'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `your-phone`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'tnum'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `tnum`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'position'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `position`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'Chapter'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `Chapter`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'chapter-selector'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `chapter-selector`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'chapter-selector'),(select `wp_oa_chapters`.`chapter_num` from `wp_oa_chapters` where (locate((`wp_oa_chapters`.`ChiefEmail` collate utf8_unicode_ci),(`wp_cf7dbplugin_submits`.`field_value` collate utf8_unicode_ci)) > 0)),NULL)) AS `ChapterNumber`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'meeting-location'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `meeting-location`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'meeting-day-time'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `meeting-day-time`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'oa-troop-rep'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `oa-troop-rep`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'e-date-1'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `e-date-1`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'e-date-2'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `e-date-2`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'e-date-3'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `e-date-3`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'information'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `information`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'Submitted From'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `Submitted From`
from `wp_cf7dbplugin_submits`
where (`wp_cf7dbplugin_submits`.`form_name` = 'UE Scheduling Form')
group by `wp_cf7dbplugin_submits`.`submit_time`
order by `wp_cf7dbplugin_submits`.`submit_time` desc;

CREATE VIEW `wp_oa_ue_troops` AS
       select `wp_cf7dbplugin_submits`.`submit_time` AS `Submitted`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'SubmitterType'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `SubmitterType`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'ElectionDate'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `ElectionDate`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'ChapterName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `ChapterName`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitType'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UnitType`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitNumber'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UnitNumber`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'camp'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `camp`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'MeetingLocation'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `MeetingLocation`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UETeamNames'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UETeamNames`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'RegActiveYouth'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `RegActiveYouth`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'YouthPresent'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `YouthPresent`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'NumberEligible'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `NumberEligible`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'NumberBallotsReturned'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `NumberBallotsReturned`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'NumberRequired'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `NumberRequired`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'NumberElected'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `NumberElected`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitLeaderName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UnitLeaderName`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitLeaderEmail'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UnitLeaderEmail`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'UnitLeaderPhone'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `UnitLeaderPhone`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'AdditionalInfo'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `AdditionalInfo`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'SubmitterName'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `SubmitterName`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'SubmitterEmail'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `SubmitterEmail`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'SubmitterPhone'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `SubmitterPhone`,
       max(if((`wp_cf7dbplugin_submits`.`field_name` = 'Submitted From'),`wp_cf7dbplugin_submits`.`field_value`,NULL)) AS `Submitted From`
from `wp_cf7dbplugin_submits`
where (`wp_cf7dbplugin_submits`.`form_name` = 'UE Report Troops')
group by `wp_cf7dbplugin_submits`.`submit_time`
order by `wp_cf7dbplugin_submits`.`submit_time` desc;

