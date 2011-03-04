SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

DROP SCHEMA IF EXISTS `bcc_food_client` ;
CREATE SCHEMA IF NOT EXISTS `bcc_food_client` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ;
USE `bcc_food_client` ;

-- -----------------------------------------------------
-- Table `bcc_food_client`.`houses`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bcc_food_client`.`houses` ;

CREATE  TABLE IF NOT EXISTS `bcc_food_client`.`houses` (
  `house_id` INT NOT NULL AUTO_INCREMENT ,
  `address` VARCHAR(45) NOT NULL ,
  `city` VARCHAR(45) NOT NULL ,
  `zip` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`house_id`) ,
  UNIQUE INDEX `location_UNIQUE` (`address` ASC, `city` ASC, `zip` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bcc_food_client`.`reasons`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bcc_food_client`.`reasons` ;

CREATE  TABLE IF NOT EXISTS `bcc_food_client`.`reasons` (
  `reason_id` INT NOT NULL AUTO_INCREMENT ,
  `reason_desc` VARCHAR(45) NOT NULL ,
  `explanation` VARCHAR(100) NULL ,
  PRIMARY KEY (`reason_id`) ,
  UNIQUE INDEX `reason_expl_UNIQUE` (`reason_desc` ASC, `explanation` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bcc_food_client`.`genders`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bcc_food_client`.`genders` ;

CREATE  TABLE IF NOT EXISTS `bcc_food_client`.`genders` (
  `gender_id` INT NOT NULL AUTO_INCREMENT ,
  `gender_desc` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`gender_id`) ,
  UNIQUE INDEX `gender_desc_UNIQUE` (`gender_desc` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bcc_food_client`.`ethnicities`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bcc_food_client`.`ethnicities` ;

CREATE  TABLE IF NOT EXISTS `bcc_food_client`.`ethnicities` (
  `ethnicity_id` INT NOT NULL AUTO_INCREMENT ,
  `ethnicity_desc` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`ethnicity_id`) ,
  UNIQUE INDEX `ethnicity_desc_UNIQUE` (`ethnicity_desc` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bcc_food_client`.`clients`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bcc_food_client`.`clients` ;

CREATE  TABLE IF NOT EXISTS `bcc_food_client`.`clients` (
  `client_id` INT NOT NULL AUTO_INCREMENT ,
  `first_name` VARCHAR(45) NOT NULL ,
  `last_name` VARCHAR(45) NOT NULL ,
  `age` INT NOT NULL ,
  `phone_number` VARCHAR(45) NULL ,
  `house_id` INT NULL ,
  `ethnicity_id` INT NOT NULL ,
  `gender_id` INT NOT NULL ,
  `reason_id` INT NOT NULL ,
  `unemployment_date` DATE NULL ,
  `application_date` DATE NOT NULL ,
  PRIMARY KEY (`client_id`) ,
  INDEX `client_house_id` (`house_id` ASC) ,
  INDEX `client_reason` (`reason_id` ASC) ,
  INDEX `client_gender` (`gender_id` ASC) ,
  INDEX `client_ethnicity` (`ethnicity_id` ASC) ,
  CONSTRAINT `client_house_id`
    FOREIGN KEY (`house_id` )
    REFERENCES `bcc_food_client`.`houses` (`house_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `client_reason`
    FOREIGN KEY (`reason_id` )
    REFERENCES `bcc_food_client`.`reasons` (`reason_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `client_gender`
    FOREIGN KEY (`gender_id` )
    REFERENCES `bcc_food_client`.`genders` (`gender_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `client_ethnicity`
    FOREIGN KEY (`ethnicity_id` )
    REFERENCES `bcc_food_client`.`ethnicities` (`ethnicity_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bcc_food_client`.`distribution_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bcc_food_client`.`distribution_type` ;

CREATE  TABLE IF NOT EXISTS `bcc_food_client`.`distribution_type` (
  `dist_type_id` INT NOT NULL AUTO_INCREMENT ,
  `dist_type_desc` VARCHAR(15) NOT NULL ,
  PRIMARY KEY (`dist_type_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bcc_food_client`.`usage`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bcc_food_client`.`usage` ;

CREATE  TABLE IF NOT EXISTS `bcc_food_client`.`usage` (
  `dist_id` INT NOT NULL AUTO_INCREMENT ,
  `client_id` INT NOT NULL ,
  `type_id` INT NOT NULL ,
  `date` DATE NOT NULL ,
  INDEX `usage_client_id` (`client_id` ASC) ,
  INDEX `usage_dist_type_id` (`type_id` ASC) ,
  PRIMARY KEY (`dist_id`) ,
  CONSTRAINT `usage_client_id`
    FOREIGN KEY (`client_id` )
    REFERENCES `bcc_food_client`.`clients` (`client_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `usage_dist_type_id`
    FOREIGN KEY (`type_id` )
    REFERENCES `bcc_food_client`.`distribution_type` (`dist_type_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bcc_food_client`.`family_members`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bcc_food_client`.`family_members` ;

CREATE  TABLE IF NOT EXISTS `bcc_food_client`.`family_members` (
  `fam_member_id` INT NOT NULL AUTO_INCREMENT ,
  `guardian_client_id` INT NOT NULL ,
  `age` INT NOT NULL ,
  `gender_id` INT NOT NULL ,
  PRIMARY KEY (`fam_member_id`) ,
  INDEX `fam_gender_id` (`gender_id` ASC) ,
  INDEX `fam_client_id` (`guardian_client_id` ASC) ,
  CONSTRAINT `fam_gender_id`
    FOREIGN KEY (`gender_id` )
    REFERENCES `bcc_food_client`.`genders` (`gender_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fam_client_id`
    FOREIGN KEY (`guardian_client_id` )
    REFERENCES `bcc_food_client`.`clients` (`client_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
