# ************************************************************
# Sequel Pro SQL dump
# Version 4500
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.5.42)
# Database: arc_biomed_database
# Generation Time: 2016-05-05 17:53:25 +0000
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
  `b2b_sales_experience` varchar(3) DEFAULT NULL,
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
  `submitted` varchar(50) DEFAULT NULL,
  `submitted_ip` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
