SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- CREATE USER 'geobase'@'localhost' IDENTIFIED WITH mysql_native_password AS 'secret';GRANT USAGE ON *.* TO 'geobase'@'localhost' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;CREATE DATABASE IF NOT EXISTS `geobase`;GRANT ALL PRIVILEGES ON `geobase`.* TO 'geobase'@'localhost';

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `geobase` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `geobase`;

DROP TABLE IF EXISTS `photos`;
CREATE TABLE `photos` (
  `PhotoID` int(10) UNSIGNED NOT NULL,
  `SiteID` int(10) UNSIGNED NOT NULL,
  `ImageFile` varchar(2048) DEFAULT NULL,
  `Description` text,
  `Modified` datetime DEFAULT NULL,
  `ModifiedBy` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `rocks`;
CREATE TABLE `rocks` (
  `RockID` int(10) UNSIGNED NOT NULL,
  `UnitID` int(10) UNSIGNED NOT NULL,
  `SiteID` int(10) UNSIGNED NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Minerals` varchar(255) DEFAULT NULL,
  `Description` text,
  `Modified` datetime DEFAULT NULL,
  `ModifiedBy` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `samples`;
CREATE TABLE `samples` (
  `SampleID` int(10) UNSIGNED NOT NULL,
  `RockID` int(10) UNSIGNED NOT NULL,
  `Label` varchar(12) NOT NULL DEFAULT '',
  `Type` varchar(12) NOT NULL,
  `Description` text,
  `Modified` datetime DEFAULT NULL,
  `ModifiedBy` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sites`;
CREATE TABLE `sites` (
  `SiteID` int(10) UNSIGNED NOT NULL,
  `Name` varchar(6) NOT NULL,
  `Lon` float(10,6) DEFAULT NULL,
  `Lat` float(10,6) DEFAULT NULL,
  `Year` int(11) NOT NULL DEFAULT '-9999',
  `Description` text,
  `Modified` datetime DEFAULT NULL,
  `ModifiedBy` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `strdata`;
CREATE TABLE `strdata` (
  `StrdataID` int(10) UNSIGNED NOT NULL,
  `RockID` int(10) UNSIGNED NOT NULL,
  `PstrtypeID` int(10) UNSIGNED DEFAULT NULL,
  `Pazimuth` double DEFAULT NULL,
  `Pinclination` double DEFAULT NULL,
  `LstrtypeID` int(10) UNSIGNED DEFAULT NULL,
  `Lazimuth` double DEFAULT NULL,
  `Linclination` double DEFAULT NULL,
  `Tags` varchar(255) DEFAULT NULL,
  `Plot` tinyint(1) DEFAULT '0',
  `Description` text,
  `Modified` datetime DEFAULT NULL,
  `ModifiedBy` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `strtypes`;
CREATE TABLE `strtypes` (
  `StrtypeID` int(10) UNSIGNED NOT NULL,
  `Label` varchar(12) NOT NULL,
  `Type` varchar(12) NOT NULL,
  `Description` text,
  `Modified` datetime DEFAULT NULL,
  `ModifiedBy` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `strtypes` (`StrtypeID`, `Label`, `Type`, `Description`, `Modified`, `ModifiedBy`) VALUES
(1, 'S', 'planar', 'Foliation', '2017-01-24 12:36:38', 'admin'),
(2, 'L', 'linear', 'Lineation', '2017-01-24 12:36:45', 'admin');

DROP TABLE IF EXISTS `thinsections`;
CREATE TABLE `thinsections` (
  `ThinsectionID` int(10) UNSIGNED NOT NULL,
  `SampleID` int(10) UNSIGNED NOT NULL,
  `Type` varchar(12) DEFAULT NULL,
  `Adjective` varchar(255) DEFAULT NULL,
  `Grainsize` varchar(255) DEFAULT NULL,
  `Texture` varchar(255) DEFAULT NULL,
  `Mainmin` varchar(255) DEFAULT NULL,
  `Adjmin` varchar(255) DEFAULT NULL,
  `Accmin` varchar(255) DEFAULT NULL,
  `Secmin` varchar(255) DEFAULT NULL,
  `Description` text,
  `Notes` varchar(255) DEFAULT NULL,
  `Modified` datetime DEFAULT NULL,
  `ModifiedBy` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tsphotos`;
CREATE TABLE `tsphotos` (
  `TsphotoID` int(10) UNSIGNED NOT NULL,
  `ThinsectionID` int(10) UNSIGNED NOT NULL,
  `ImageFile` varchar(2048) DEFAULT NULL,
  `Description` text,
  `Modified` datetime DEFAULT NULL,
  `ModifiedBy` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `units`;
CREATE TABLE `units` (
  `UnitID` int(10) UNSIGNED NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Description` text,
  `Modified` datetime DEFAULT NULL,
  `ModifiedBy` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `UserID` int(10) UNSIGNED NOT NULL,
  `FullName` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `UserName` varchar(32) NOT NULL,
  `Password` varchar(32) NOT NULL,
  `Role` enum('MANAGER','ADMIN','USER','NO ACCESS','READ ONLY','EDIT') NOT NULL DEFAULT 'READ ONLY'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP VIEW IF EXISTS status;
CREATE VIEW status AS SELECT A.Author, Total_Sites, Filled_Desc, CONCAT(FORMAT(IF(Total_Sites=0,0,(Filled_Desc*100.0)/Total_Sites),2),'%') AS `Sites_OK`, Total_Sites_with_Rocks, Total_Rocks, CONCAT(FORMAT(IF(Total_Sites=0,0,(Total_Sites_with_Rocks*100.0)/Total_Sites),2),'%') AS `Rocks_OK`, Total_Sites_with_Structures, Total_Structures, Total_Sites_with_Photos, Total_Photos, Modified FROM (SELECT IF(sites.Name REGEXP '[[:alpha:]]{2}', LEFT(sites.Name,2), LEFT(sites.Name,1)) as Author, MAX(Modified) as Modified, COUNT(sites.SiteID) AS Total_Sites FROM sites GROUP BY Author) AS A LEFT JOIN (SELECT IF(sites.Name REGEXP '[[:alpha:]]{2}', LEFT(sites.Name,2), LEFT(sites.Name,1)) as Author, COUNT(sites.SiteID) AS Filled_Desc FROM sites WHERE CHAR_LENGTH(sites.Description)>0 GROUP BY Author) AS B on A.Author=B.Author LEFT JOIN (SELECT IF(sites.Name REGEXP '[[:alpha:]]{2}', LEFT(sites.Name,2), LEFT(sites.Name,1)) as Author, COUNT(DISTINCT rocks.SiteID) AS Total_Sites_with_Rocks, COUNT(rocks.RockID) AS Total_Rocks FROM sites JOIN rocks on sites.SiteID = rocks.SiteID GROUP BY Author) AS C on A.Author=C.Author LEFT JOIN (SELECT IF(sites.Name REGEXP '[[:alpha:]]{2}', LEFT(sites.Name,2), LEFT(sites.Name,1)) as Author, COUNT(DISTINCT rocks.SiteID) AS Total_Sites_with_Structures, COUNT(strdata.StrdataID) AS Total_Structures FROM sites JOIN rocks on sites.SiteID = rocks.SiteID JOIN strdata ON rocks.RockID = strdata.RockID GROUP BY Author) AS D on A.Author=D.Author LEFT JOIN (SELECT IF(sites.Name REGEXP '[[:alpha:]]{2}', LEFT(sites.Name,2), LEFT(sites.Name,1)) as Author, COUNT(DISTINCT photos.SiteID) AS Total_Sites_with_Photos, COUNT(photos.PhotoID) AS Total_Photos FROM sites JOIN photos on sites.SiteID = photos.SiteID GROUP BY Author) AS E on A.Author=E.Author;

INSERT INTO `users` (`UserID`, `FullName`, `Email`, `UserName`, `Password`, `Role`) VALUES
(1, 'Administrator', 'admin@email.com', 'admin', 'admin', 'ADMIN');


ALTER TABLE `photos`
  ADD PRIMARY KEY (`PhotoID`),
  ADD KEY `fk_photos_sites_idx` (`SiteID`);

ALTER TABLE `rocks`
  ADD PRIMARY KEY (`RockID`),
  ADD KEY `fk_rocks_units_idx` (`UnitID`),
  ADD KEY `fk_rocks_sites_idx` (`SiteID`);

ALTER TABLE `samples`
  ADD PRIMARY KEY (`SampleID`),
  ADD KEY `fk_samples_rocks_idx` (`RockID`);

ALTER TABLE `sites`
  ADD PRIMARY KEY (`SiteID`),
  ADD UNIQUE KEY `Name_idx` (`Name`);

ALTER TABLE `strdata`
  ADD PRIMARY KEY (`StrdataID`),
  ADD KEY `fk_strdata_rocks_idx` (`RockID`),
  ADD KEY `fk_strdata_strtypes_planar_idx` (`PstrtypeID`),
  ADD KEY `fk_strdata_strtypes_linear_idx` (`LstrtypeID`);

ALTER TABLE `strtypes`
  ADD PRIMARY KEY (`StrtypeID`);

ALTER TABLE `thinsections`
  ADD PRIMARY KEY (`ThinsectionID`),
  ADD KEY `fk_thinsections_samples_idx` (`SampleID`);

ALTER TABLE `tsphotos`
  ADD PRIMARY KEY (`TsphotoID`),
  ADD KEY `fk_tsphotos_thinsections_idx` (`ThinsectionID`);

ALTER TABLE `units`
  ADD PRIMARY KEY (`UnitID`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `UserName_idx` (`UserName`);


ALTER TABLE `photos`
  MODIFY `PhotoID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `rocks`
  MODIFY `RockID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `samples`
  MODIFY `SampleID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `sites`
  MODIFY `SiteID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `strdata`
  MODIFY `StrdataID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `strtypes`
  MODIFY `StrtypeID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `thinsections`
  MODIFY `ThinsectionID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `tsphotos`
  MODIFY `TsphotoID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `units`
  MODIFY `UnitID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `users`
  MODIFY `UserID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `photos`
  ADD CONSTRAINT `fk_photos_sites` FOREIGN KEY (`SiteID`) REFERENCES `sites` (`SiteID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `rocks`
  ADD CONSTRAINT `fk_rocks_sites` FOREIGN KEY (`SiteID`) REFERENCES `sites` (`SiteID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_rocks_units` FOREIGN KEY (`UnitID`) REFERENCES `units` (`UnitID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `samples`
  ADD CONSTRAINT `fk_samples_rocks` FOREIGN KEY (`RockID`) REFERENCES `rocks` (`RockID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `strdata`
  ADD CONSTRAINT `fk_strdata_rocks` FOREIGN KEY (`RockID`) REFERENCES `rocks` (`RockID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_strdata_strtypes_linear` FOREIGN KEY (`LstrtypeID`) REFERENCES `strtypes` (`StrtypeID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_strdata_strtypes_planar` FOREIGN KEY (`PstrtypeID`) REFERENCES `strtypes` (`StrtypeID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `thinsections`
  ADD CONSTRAINT `fk_thinsections_samples` FOREIGN KEY (`SampleID`) REFERENCES `samples` (`SampleID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `tsphotos`
  ADD CONSTRAINT `fk_tsphotos_thinsections` FOREIGN KEY (`ThinsectionID`) REFERENCES `thinsections` (`ThinsectionID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
