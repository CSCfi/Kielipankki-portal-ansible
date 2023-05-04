DROP TABLE IF EXISTS `pid_map`;
CREATE TABLE `pid_map` (
  `pid` varchar(256) DEFAULT NULL,
  `url` varchar(8192) DEFAULT NULL
) DEFAULT CHARSET=latin1;
