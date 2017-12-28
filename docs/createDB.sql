CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `test`.`users`
ADD COLUMN `name` VARCHAR(20) NULL AFTER `id`,
ADD COLUMN `username` VARCHAR(20) NULL AFTER `name`,
ADD COLUMN `password` VARCHAR(40) NULL AFTER `username`,
ADD COLUMN `passwordsalt` VARCHAR(32) NULL AFTER `password`,
ADD COLUMN `email` VARCHAR(256) NULL AFTER `passwordsalt`,
ADD COLUMN `role` TINYINT NULL AFTER `email`;
ADD COLUMN `timeAdded` TIMESTAMP NULL AFTER `role`,
ADD COLUMN `timeModified` TIMESTAMP NULL AFTER `timeAdded`,
ADD COLUMN `deleted` TINYINT NULL AFTER `timeModified`;