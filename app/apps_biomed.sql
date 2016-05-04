# ************************************************************
# Sequel Pro SQL dump
# Version 4500
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.5.42)
# Database: arc_biomed_database
# Generation Time: 2016-05-04 18:41:00 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table apps_biomed
# ------------------------------------------------------------

DROP TABLE IF EXISTS `apps_biomed`;

CREATE TABLE `apps_biomed` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `phone` decimal(50,0) DEFAULT NULL,
  `location` varchar(50) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `recruiter` varchar(50) DEFAULT NULL,
  `phleb_driver_variable_sched` varchar(3) DEFAULT NULL,
  `phleb_cdl` varchar(3) DEFAULT NULL,
  `medtech_license` varchar(3) DEFAULT NULL,
  `medtech_cert` varchar(50) DEFAULT NULL,
  `nurse_license` varchar(3) DEFAULT NULL,
  `phleb_variable_sched` varchar(3) DEFAULT NULL,
  `phleb_pt` varchar(3) DEFAULT NULL,
  `driving_record` varchar(3) DEFAULT NULL,
  `resume` varchar(50) DEFAULT NULL,
  `utm_source` varchar(50) DEFAULT NULL,
  `submitted` datetime DEFAULT NULL,
  `submitted_ip` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `apps_biomed` WRITE;
/*!40000 ALTER TABLE `apps_biomed` DISABLE KEYS */;

INSERT INTO `apps_biomed` (`id`, `first_name`, `last_name`, `email`, `phone`, `location`, `position`, `recruiter`, `phleb_driver_variable_sched`, `phleb_cdl`, `medtech_license`, `medtech_cert`, `nurse_license`, `phleb_variable_sched`, `phleb_pt`, `driving_record`, `resume`, `utm_source`, `submitted`, `submitted_ip`)
VALUES
	(1,'test','test','',0,'Oakland, CA','Medical Technologist','sara.sutherland@redcross.org','','','','','','','',NULL,'','','0000-00-00 00:00:00',1270),
	(2,'test','test','',0,'Oakland, CA','Medical Technologist','sara.sutherland@redcross.org','','','','','','','',NULL,'','','0000-00-00 00:00:00',1270),
	(3,'test','test','test@test.com',888,'Atlanta, GA','Medical Technologist','niki.bratchette@redcross.org ','','','No','test','','','',NULL,'','','0000-00-00 00:00:00',1270),
	(4,'','','',0,'Oakland, CA','','','','','','','','','',NULL,'','','0000-00-00 00:00:00',1270),
	(5,'','','',0,'Oakland, CA','','','','','','','','','',NULL,'','','0000-00-00 00:00:00',1270);

/*!40000 ALTER TABLE `apps_biomed` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
