# ************************************************************
# Sequel Pro SQL dump
# Version 4500
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.5.42)
# Database: arc_biomed_database
# Generation Time: 2016-05-05 16:15:32 +0000
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
  `phleb_cdl` varchar(3) DEFAULT NULL,
  `medtech_license` varchar(3) DEFAULT NULL,
  `medtech_cert` varchar(50) DEFAULT NULL,
  `nurse_license` varchar(3) DEFAULT NULL,
  `phleb_variable_sched` varchar(3) DEFAULT NULL,
  `driving_record` varchar(3) DEFAULT NULL,
  `resume` varchar(50) DEFAULT NULL,
  `utm_campaign` varchar(50) DEFAULT NULL,
  `utm_medium` varchar(50) DEFAULT NULL,
  `utm_source` varchar(50) DEFAULT NULL,
  `submitted` datetime DEFAULT NULL,
  `submitted_ip` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `apps_biomed` WRITE;
/*!40000 ALTER TABLE `apps_biomed` DISABLE KEYS */;

INSERT INTO `apps_biomed` (`id`, `first_name`, `last_name`, `email`, `phone`, `location`, `position`, `recruiter`, `phleb_cdl`, `medtech_license`, `medtech_cert`, `nurse_license`, `phleb_variable_sched`, `driving_record`, `resume`, `utm_campaign`, `utm_medium`, `utm_source`, `submitted`, `submitted_ip`)
VALUES
	(1,'test','test','',0,'Oakland, CA','Medical Technologist','sara.sutherland@redcross.org','','','','','',NULL,'',NULL,NULL,'','0000-00-00 00:00:00',1270),
	(2,'test','test','',0,'Oakland, CA','Medical Technologist','sara.sutherland@redcross.org','','','','','',NULL,'',NULL,NULL,'','0000-00-00 00:00:00',1270),
	(3,'test','test','test@test.com',888,'Atlanta, GA','Medical Technologist','niki.bratchette@redcross.org ','','No','test','','',NULL,'',NULL,NULL,'','0000-00-00 00:00:00',1270),
	(4,'','','',0,'Oakland, CA','','','','','','','',NULL,'',NULL,NULL,'','0000-00-00 00:00:00',1270),
	(5,'','','',0,'Oakland, CA','','','','','','','',NULL,'',NULL,NULL,'','0000-00-00 00:00:00',1270),
	(6,'test','test','test@test.com',888,'Atlanta, GA','Medical Technologist','niki.bratchette@redcross.org ','','No','','','','No','',NULL,NULL,'','0000-00-00 00:00:00',1270),
	(7,'test','test','test@test.com',888,'Atlanta, GA','Medical Technologist','niki.bratchette@redcross.org ','','Yes','','','','No','',NULL,NULL,'','0000-00-00 00:00:00',1270),
	(8,'test','test','test@test.com',888,'Atlanta, GA','Medical Technologist','niki.bratchette@redcross.org ','','Yes','TEST','','','Yes','',NULL,NULL,'','0000-00-00 00:00:00',1270),
	(9,'test','test','test@test.com',888,'Atlanta, GA','Medical Technologist','niki.bratchette@redcross.org ','','Yes','TEST','','','Yes','',NULL,NULL,'','0000-00-00 00:00:00',1270),
	(10,'test','test','test@test.com',888,'Atlanta, GA','Medical Technologist','niki.bratchette@redcross.org ','','Yes','TEST','','','Yes','',NULL,NULL,'','0000-00-00 00:00:00',1270),
	(11,'test','test','test@test.com',888,'Atlanta, GA','Medical Technologist','niki.bratchette@redcross.org ','','Yes','TEST','','','Yes','',NULL,NULL,'','0000-00-00 00:00:00',1270),
	(12,'test','test','test@test.com',888,'Atlanta, GA','Medical Technologist','niki.bratchette@redcross.org ','','Yes','TEST','','','Yes','',NULL,NULL,'','0000-00-00 00:00:00',1270),
	(13,'test','test','test@test.com',888,'Atlanta, GA','Medical Technologist','niki.bratchette@redcross.org ','','Yes','TEST','','','Yes','',NULL,NULL,'','0000-00-00 00:00:00',1270),
	(14,'test','test','test@test.com',888,'Sacramento, CA','Nurse','dena.gray@redcross.org','','','','Yes','','Yes','05-04-2016-testtest-test-doc.pdf','','','','0000-00-00 00:00:00',1270),
	(15,'test','test','test@test.com',888,'Sacramento, CA','Nurse','dena.gray@redcross.org','','','','Yes','','Yes','05-04-2016-testtest-test-doc.pdf','','','','0000-00-00 00:00:00',1270),
	(16,'test','test','test@test.com',888,'Sacramento, CA','Nurse','dena.gray@redcross.org','','','','Yes','','Yes','05-04-2016-testtest-test-doc.pdf','','','','0000-00-00 00:00:00',1270),
	(17,'test','test','test@test.com',888,'Atlanta, GA','Medical Technologist','niki.bratchette@redcross.org ','','Yes','adsf','','','Yes','05-04-2016-testtest-test-doc.docx',NULL,NULL,'','0000-00-00 00:00:00',1270),
	(18,'','','',0,'Oakland, California','Nurse','dena.gray@redcross.org','','','','','','','','','','','0000-00-00 00:00:00',1270),
	(19,'','','',0,'Pomona, California','Phlebotomist','dena.gray@redcross.org','','','','','','','','','','','0000-00-00 00:00:00',1270),
	(20,'','','',0,'Bloomington, Indiana','Driver/Phlebotomist','karen.whitford@redcross.org','','','','','','','','','','','0000-00-00 00:00:00',1270),
	(21,'','','',0,'Charlotte, North+Carolina','Account Manager/DRD','','','','','','','','','','','','0000-00-00 00:00:00',1270),
	(22,'','','',0,'Sacramento, California','Nurse','dena.gray@redcross.org','','','','No','','','','','','','0000-00-00 00:00:00',1270),
	(23,'','','',0,'Pomona, California','Phlebotomist','dena.gray@redcross.org','','','','','No','','','','','','0000-00-00 00:00:00',1270),
	(24,'TEST','test','test@test.com',888,'Pomona, California','Medical Technologist','sara.sutherland@redcross.org','','No','','','','No','05-05-2016-TESTtest-test-doc.docx','','','','0000-00-00 00:00:00',1270);

/*!40000 ALTER TABLE `apps_biomed` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
