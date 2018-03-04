ALTER TABLE `phones` ADD COLUMN `remark` VARCHAR(512) NULL AFTER `statusdescription`;
ALTER TABLE `resmanager`.`simcards`
ADD COLUMN `idcard` VARCHAR(32) NULL COMMENT '身份证' AFTER `statusdescription`,
ADD COLUMN `servicepassword` VARCHAR(45) NULL COMMENT '服务密码' AFTER `idcard`;