USE `resmanager`;
ALTER TABLE `users`
DROP COLUMN `passwordsalt`,
MODIFY COLUMN `password`  varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `username`;
INSERT INTO `users` VALUES (1, '柯许剑', 'admin', '$2y$09$pkjEMNcfQlPcc3d3uFZqwek2qcE.7/4DwFzDt370TbjyDf./.42j6', 'lixin@zhexinit.com', 0, '2017-12-28 20:53:42', '2018-4-10 13:57:17', 0);
