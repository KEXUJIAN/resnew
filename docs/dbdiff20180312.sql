CREATE TABLE `uploadfiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uploadbyuser` int(11) DEFAULT NULL,
  `type` smallint(6) DEFAULT NULL COMMENT '0 => ''user'',\r\n1 => ''phone'',\r\n2 => ''simcard'',',
  `originname` varchar(255) DEFAULT NULL,
  `filename` varchar(1024) DEFAULT NULL,
  `data` text,
  `status` smallint(6) DEFAULT NULL,
  `timeadded` timestamp NULL DEFAULT NULL,
  `timemodified` timestamp NULL DEFAULT NULL,
  `deleted` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `phones`
MODIFY COLUMN `remark`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `statusdescription`;

ALTER TABLE `simcards`
ADD COLUMN `remark`  text NULL AFTER `servicepassword`;
