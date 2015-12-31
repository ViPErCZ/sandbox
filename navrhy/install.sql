-- MySQL Script generated by MySQL Workbench
-- 05/09/15 12:38:49
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';


-- -----------------------------------------------------
-- Table `acl`.`aclRole`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `aclRole` (
  `aclRoleID` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`aclRoleID`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `user` (
  `userID` INT NOT NULL AUTO_INCREMENT,
  `aclRoleID` INT NOT NULL,
  `login` VARCHAR(45) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `lastLogged` DATETIME NULL,
  `ip` VARCHAR(20) NULL,
  PRIMARY KEY (`userID`),
  INDEX `fk_user_aclRole1_idx` (`aclRoleID` ASC),
  UNIQUE INDEX `login_UNIQUE` (`login` ASC),
  CONSTRAINT `fk_user_aclRole1`
    FOREIGN KEY (`aclRoleID`)
    REFERENCES `aclRole` (`aclRoleID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `aclAction`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `aclAction` (
  `aclActionID` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `humanName` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`aclActionID`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `aclResource`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `aclResource` (
  `aclResourceID` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`aclResourceID`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `aclModel`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `aclModel` (
  `aclModelID` INT NOT NULL AUTO_INCREMENT,
  `aclResourceID` INT NOT NULL,
  `aclActionID` INT NOT NULL,
  PRIMARY KEY (`aclModelID`),
  INDEX `fk_aclModel_aclResource1_idx` (`aclResourceID` ASC),
  INDEX `fk_aclModel_aclAction1_idx` (`aclActionID` ASC),
  CONSTRAINT `fk_aclModel_aclResource1`
    FOREIGN KEY (`aclResourceID`)
    REFERENCES `aclResource` (`aclResourceID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_aclModel_aclAction1`
    FOREIGN KEY (`aclActionID`)
    REFERENCES `aclAction` (`aclActionID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `aclPermission`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `aclPermission` (
  `aclPermissionID` INT NOT NULL AUTO_INCREMENT,
  `aclRoleID` INT NOT NULL,
  `aclModelID` INT NOT NULL,
  `allowed` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`aclPermissionID`),
  INDEX `fk_aclPermission_aclRole_idx` (`aclRoleID` ASC),
  INDEX `fk_aclPermission_aclModel1_idx` (`aclModelID` ASC),
  CONSTRAINT `fk_aclPermission_aclRole`
    FOREIGN KEY (`aclRoleID`)
    REFERENCES `aclRole` (`aclRoleID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_aclPermission_aclModel1`
    FOREIGN KEY (`aclModelID`)
    REFERENCES `aclModel` (`aclModelID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `syslog`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `syslog` (
  `syslogID` INT NOT NULL AUTO_INCREMENT,
  `userID` INT NULL,
  `message` TEXT NOT NULL,
  `timestamp` TIMESTAMP NOT NULL,
  `ip` VARCHAR(17) NULL,
  PRIMARY KEY (`syslogID`),
  INDEX `fk_syslog_user1_idx` (`userID` ASC),
  CONSTRAINT `fk_syslog_user1`
    FOREIGN KEY (`userID`)
    REFERENCES `user` (`userID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `contact`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `contact` (
  `contactID` INT NOT NULL AUTO_INCREMENT,
  `userID` INT NULL,
  `name` VARCHAR(255) NULL,
  `firstname` VARCHAR(45) NULL,
  `lastname` VARCHAR(45) NULL,
  PRIMARY KEY (`contactID`),
  INDEX `fk_contact_user1_idx` (`userID` ASC),
  CONSTRAINT `fk_contact_user1`
    FOREIGN KEY (`userID`)
    REFERENCES `user` (`userID`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `aclRole`
-- -----------------------------------------------------
START TRANSACTION;

INSERT INTO `aclRole` (`aclRoleID`, `name`) VALUES (NULL, 'root');

COMMIT;


-- -----------------------------------------------------
-- Data for table `user`
-- -----------------------------------------------------
START TRANSACTION;

INSERT INTO `user` (`userID`, `aclRoleID`, `login`, `password`, `active`, `lastLogged`, `ip`) VALUES (NULL, 1, 'root', '4247a001e44edc9d8df4b47da8e4c97f', 1, NULL, NULL);

COMMIT;


-- -----------------------------------------------------
-- Data for table `aclAction`
-- -----------------------------------------------------
START TRANSACTION;

INSERT INTO `aclAction` (`aclActionID`, `name`, `humanName`) VALUES (1, 'view', 'Zobrazit');
INSERT INTO `aclAction` (`aclActionID`, `name`, `humanName`) VALUES (2, 'add', 'Přidávat');
INSERT INTO `aclAction` (`aclActionID`, `name`, `humanName`) VALUES (3, 'edit', 'Editovat');
INSERT INTO `aclAction` (`aclActionID`, `name`, `humanName`) VALUES (4, 'delete', 'Mazat');

COMMIT;


-- -----------------------------------------------------
-- Data for table `aclResource`
-- -----------------------------------------------------
START TRANSACTION;

INSERT INTO `aclResource` (`aclResourceID`, `name`) VALUES (1, 'permission');
INSERT INTO `aclResource` (`aclResourceID`, `name`) VALUES (2, 'user_management');
INSERT INTO `aclResource` (`aclResourceID`, `name`) VALUES (3, 'history');

COMMIT;

-- -----------------------------------------------------
-- Data for table `aclModel`
-- -----------------------------------------------------
START TRANSACTION;

INSERT INTO `aclModel` (`aclModelID`, `aclResourceID`, `aclActionID`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 2, 1),
(6, 3, 1),
(7, 2, 2),
(8, 2, 3);

COMMIT;