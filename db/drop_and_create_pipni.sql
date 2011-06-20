SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `course-manager` DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci ;
USE `course-manager` ;

-- -----------------------------------------------------
-- Table `course-manager`.`Course`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `course-manager`.`Course` ;

CREATE  TABLE IF NOT EXISTS `course-manager`.`Course` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  `description` TEXT NULL ,
  `created` DATETIME NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `course-manager`.`User`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `course-manager`.`User` ;

CREATE  TABLE IF NOT EXISTS `course-manager`.`User` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `email` VARCHAR(45) NULL ,
  `password` VARCHAR(45) NULL ,
  `firstname` VARCHAR(45) NULL ,
  `lastname` VARCHAR(45) NULL ,
  `created` DATETIME NULL ,
  `seclink` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `course-manager`.`student`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `course-manager`.`student` ;

CREATE  TABLE IF NOT EXISTS `course-manager`.`student` (
  `Course_id` INT NOT NULL ,
  `User_id` INT NOT NULL ,
  PRIMARY KEY (`Course_id`, `User_id`) ,
  INDEX `fk_Course_has_User_User1` (`User_id` ASC) ,
  INDEX `fk_Course_has_User_Course` (`Course_id` ASC) ,
  CONSTRAINT `fk_Course_has_User_Course`
    FOREIGN KEY (`Course_id` )
    REFERENCES `course-manager`.`Course` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Course_has_User_User1`
    FOREIGN KEY (`User_id` )
    REFERENCES `course-manager`.`User` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `course-manager`.`teacher`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `course-manager`.`teacher` ;

CREATE  TABLE IF NOT EXISTS `course-manager`.`teacher` (
  `Course_id` INT NOT NULL ,
  `User_id` INT NOT NULL ,
  PRIMARY KEY (`Course_id`, `User_id`) ,
  INDEX `fk_Course_has_User_User2` (`User_id` ASC) ,
  INDEX `fk_Course_has_User_Course1` (`Course_id` ASC) ,
  CONSTRAINT `fk_Course_has_User_Course1`
    FOREIGN KEY (`Course_id` )
    REFERENCES `course-manager`.`Course` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Course_has_User_User2`
    FOREIGN KEY (`User_id` )
    REFERENCES `course-manager`.`User` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `course-manager`.`Assignment`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `course-manager`.`Assignment` ;

CREATE  TABLE IF NOT EXISTS `course-manager`.`Assignment` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  `description` TEXT NULL ,
  `created` DATETIME NULL ,
  `assigndate` DATETIME NULL ,
  `duedate` DATETIME NULL ,
  `maxpoints` INT NULL ,
  `Course_id` INT NOT NULL ,
  `test` TINYINT(1)  NULL ,
  `online` TINYINT(1)  NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_Assignment_Course1` (`Course_id` ASC) ,
  CONSTRAINT `fk_Assignment_Course1`
    FOREIGN KEY (`Course_id` )
    REFERENCES `course-manager`.`Course` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `course-manager`.`OnlineSubmission`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `course-manager`.`OnlineSubmission` ;

CREATE  TABLE IF NOT EXISTS `course-manager`.`OnlineSubmission` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `text` TEXT NULL ,
  `submitted` DATETIME NULL ,
  `note` TEXT NULL ,
  `User_id` INT NOT NULL ,
  `Assignment_id` INT NOT NULL ,
  `points` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_Submission_User1` (`User_id` ASC) ,
  INDEX `fk_Submission_Assignment1` (`Assignment_id` ASC) ,
  CONSTRAINT `fk_Submission_User1`
    FOREIGN KEY (`User_id` )
    REFERENCES `course-manager`.`User` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Submission_Assignment1`
    FOREIGN KEY (`Assignment_id` )
    REFERENCES `course-manager`.`Assignment` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `course-manager`.`OfflineTask`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `course-manager`.`OfflineTask` ;

CREATE  TABLE IF NOT EXISTS `course-manager`.`OfflineTask` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  `date` DATETIME NULL ,
  `Course_id` INT NOT NULL ,
  `maxpoints` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_RealAssignment_Course1` (`Course_id` ASC) ,
  CONSTRAINT `fk_RealAssignment_Course1`
    FOREIGN KEY (`Course_id` )
    REFERENCES `course-manager`.`Course` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Napriklad test, ukol, aktivita z realne hodiny.' ;


-- -----------------------------------------------------
-- Table `course-manager`.`Result`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `course-manager`.`Result` ;

CREATE  TABLE IF NOT EXISTS `course-manager`.`Result` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `points` INT NULL ,
  `User_id` INT NOT NULL ,
  `OfflineTask_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_Result_User1` (`User_id` ASC) ,
  INDEX `fk_Result_RealTask1` (`OfflineTask_id` ASC) ,
  CONSTRAINT `fk_Result_User1`
    FOREIGN KEY (`User_id` )
    REFERENCES `course-manager`.`User` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Result_RealTask1`
    FOREIGN KEY (`OfflineTask_id` )
    REFERENCES `course-manager`.`OfflineTask` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `course-manager`.`Topic`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `course-manager`.`Topic` ;

CREATE  TABLE IF NOT EXISTS `course-manager`.`Topic` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `label` VARCHAR(45) NULL ,
  `content` TEXT NULL ,
  `created` DATETIME NULL ,
  `Course_id` INT NOT NULL ,
  `User_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_Topic_Course1` (`Course_id` ASC) ,
  INDEX `fk_Topic_User1` (`User_id` ASC) ,
  CONSTRAINT `fk_Topic_Course1`
    FOREIGN KEY (`Course_id` )
    REFERENCES `course-manager`.`Course` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Topic_User1`
    FOREIGN KEY (`User_id` )
    REFERENCES `course-manager`.`User` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `course-manager`.`Reply`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `course-manager`.`Reply` ;

CREATE  TABLE IF NOT EXISTS `course-manager`.`Reply` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `content` TEXT NULL ,
  `created` DATETIME NULL ,
  `Topic_id` INT NOT NULL ,
  `User_id` INT NOT NULL ,
  `Reply_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_Reply_Topic1` (`Topic_id` ASC) ,
  INDEX `fk_Reply_User1` (`User_id` ASC) ,
  INDEX `fk_Reply_Reply1` (`Reply_id` ASC) ,
  CONSTRAINT `fk_Reply_Topic1`
    FOREIGN KEY (`Topic_id` )
    REFERENCES `course-manager`.`Topic` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Reply_User1`
    FOREIGN KEY (`User_id` )
    REFERENCES `course-manager`.`User` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Reply_Reply1`
    FOREIGN KEY (`Reply_id` )
    REFERENCES `course-manager`.`Reply` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `course-manager`.`Message`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `course-manager`.`Message` ;

CREATE  TABLE IF NOT EXISTS `course-manager`.`Message` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `subject` VARCHAR(45) NULL ,
  `content` TEXT NULL ,
  `read` TINYINT(1)  NULL ,
  `sent` DATETIME NULL ,
  `from` INT NOT NULL ,
  `to` INT NOT NULL ,
  `reply_to_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_Message_User1` (`from` ASC) ,
  INDEX `fk_Message_User2` (`to` ASC) ,
  INDEX `fk_Message_Message1` (`reply_to_id` ASC) ,
  CONSTRAINT `fk_Message_User1`
    FOREIGN KEY (`from` )
    REFERENCES `course-manager`.`User` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Message_User2`
    FOREIGN KEY (`to` )
    REFERENCES `course-manager`.`User` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Message_Message1`
    FOREIGN KEY (`reply_to_id` )
    REFERENCES `course-manager`.`Message` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `course-manager`.`Lesson`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `course-manager`.`Lesson` ;

CREATE  TABLE IF NOT EXISTS `course-manager`.`Lesson` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `topic` VARCHAR(45) NULL ,
  `description` TEXT NULL ,
  `number` INT NULL ,
  `Course_id` INT NOT NULL ,
  `date` DATETIME NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_Lesson_Course1` (`Course_id` ASC) ,
  CONSTRAINT `fk_Lesson_Course1`
    FOREIGN KEY (`Course_id` )
    REFERENCES `course-manager`.`Course` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `course-manager`.`Resource`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `course-manager`.`Resource` ;

CREATE  TABLE IF NOT EXISTS `course-manager`.`Resource` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  `description` TEXT NULL ,
  `filename` VARCHAR(45) NULL ,
  `type` VARCHAR(45) NULL ,
  `added` DATETIME NULL ,
  `Lesson_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_Resource_Lesson1` (`Lesson_id` ASC) ,
  CONSTRAINT `fk_Resource_Lesson1`
    FOREIGN KEY (`Lesson_id` )
    REFERENCES `course-manager`.`Lesson` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `course-manager`.`Comment`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `course-manager`.`Comment` ;

CREATE  TABLE IF NOT EXISTS `course-manager`.`Comment` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `content` TEXT NULL ,
  `added` DATETIME NULL ,
  `User_id` INT NOT NULL ,
  `Lesson_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_Comment_User1` (`User_id` ASC) ,
  INDEX `fk_Comment_Lesson1` (`Lesson_id` ASC) ,
  CONSTRAINT `fk_Comment_User1`
    FOREIGN KEY (`User_id` )
    REFERENCES `course-manager`.`User` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Comment_Lesson1`
    FOREIGN KEY (`Lesson_id` )
    REFERENCES `course-manager`.`Lesson` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `course-manager`.`Event`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `course-manager`.`Event` ;

CREATE  TABLE IF NOT EXISTS `course-manager`.`Event` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  `date` DATETIME NULL ,
  `description` TEXT NULL ,
  `added` DATETIME NULL ,
  `Course_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_Event_Course1` (`Course_id` ASC) ,
  CONSTRAINT `fk_Event_Course1`
    FOREIGN KEY (`Course_id` )
    REFERENCES `course-manager`.`Course` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `course-manager`.`Notification`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `course-manager`.`Notification` ;

CREATE  TABLE IF NOT EXISTS `course-manager`.`Notification` (
  `idNotification` INT NOT NULL ,
  `period` INT NULL ,
  `User_id` INT NOT NULL ,
  `Assignment_id` INT NOT NULL ,
  PRIMARY KEY (`idNotification`, `User_id`, `Assignment_id`) ,
  INDEX `fk_Notification_User1` (`User_id` ASC) ,
  INDEX `fk_Notification_Assignment1` (`Assignment_id` ASC) ,
  CONSTRAINT `fk_Notification_User1`
    FOREIGN KEY (`User_id` )
    REFERENCES `course-manager`.`User` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Notification_Assignment1`
    FOREIGN KEY (`Assignment_id` )
    REFERENCES `course-manager`.`Assignment` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
