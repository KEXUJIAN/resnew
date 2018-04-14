CREATE DATABASE `resmanager` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `resmanager`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `username` varchar(20) DEFAULT NULL,
  `password` varchar(40) DEFAULT NULL,
  `passwordsalt` varchar(32) DEFAULT NULL,
  `email` varchar(256) DEFAULT NULL,
  `role` tinyint(4) DEFAULT NULL,
  `timeadded` timestamp NULL DEFAULT NULL,
  `timemodified` timestamp NULL DEFAULT NULL,
  `deleted` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `phones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `type` varchar(512) DEFAULT NULL,
  `os` varchar(128) DEFAULT NULL,
  `resolution` varchar(32) DEFAULT NULL,
  `ram` int(11) DEFAULT NULL,
  `carrier` varchar(32) DEFAULT NULL COMMENT '手机支持的运营商\n格式：0,1,2\n0 - 电信, 1 - 移动, 2 - 联通',
  `screensize` varchar(32) DEFAULT NULL,
  `label` varchar(32) DEFAULT NULL,
  `imei` varchar(128) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `statusdescription` varchar(1024) DEFAULT NULL,
  `timeadded` timestamp NULL DEFAULT NULL,
  `timemodified` timestamp NULL DEFAULT NULL,
  `deleted` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_phones_users_id_idx` (`userid`),
  CONSTRAINT `fk_phones_users_id` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `simcards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `phonenumber` varchar(32) DEFAULT NULL,
  `label` text DEFAULT NULL,
  `carrier` varchar(32) DEFAULT NULL,
  `place` varchar(32) DEFAULT NULL,
  `imsi` varchar(32) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `statusdescription` varchar(1024) DEFAULT NULL,
  `timeadded` timestamp NULL DEFAULT NULL,
  `timemodified` timestamp NULL DEFAULT NULL,
  `deleted` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_simcards_users_id_idx` (`userid`),
  CONSTRAINT `fk_simcards_users_id` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fromuserid` int(11) DEFAULT NULL,
  `touserid` int(11) DEFAULT NULL,
  `assetid` int(11) DEFAULT NULL,
  `assettype` tinyint(4) DEFAULT NULL,
  `type` tinyint(4) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `timeadded` timestamp NULL DEFAULT NULL,
  `timemodified` timestamp NULL DEFAULT NULL,
  `deleted` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `message` text,
  `read` tinyint(4) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `extra` text,
  `timeadded` timestamp NULL DEFAULT NULL,
  `timemodified` timestamp NULL DEFAULT NULL,
  `deleted` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
