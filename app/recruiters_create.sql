CREATE TABLE `recruiters` (
`id` int(11) NOT NULL AUTO_INCREMENT,
  `position` varchar(50) DEFAULT NULL,
  `city` varchar(75) DEFAULT NULL,
  `state` varchar(14) DEFAULT NULL,
  `state_name` varchar(75) DEFAULT NULL,
  `qualifications` varchar(75) DEFAULT NULL,
  `recruiter` varchar(75) DEFAULT NULL,
  `region` longtext,
  `status` varchar(50) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;