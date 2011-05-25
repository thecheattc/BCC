SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

DROP SCHEMA IF EXISTS `bcc_food_client` ;
CREATE SCHEMA IF NOT EXISTS `bcc_food_client` DEFAULT CHARACTER SET latin1 ;
DROP SCHEMA IF EXISTS `bcc_admin` ;
CREATE SCHEMA IF NOT EXISTS `bcc_admin` DEFAULT CHARACTER SET latin1 ;
USE `bcc_food_client` ;

-- -----------------------------------------------------
-- Table `bcc_food_client`.`houses`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bcc_food_client`.`houses` ;

CREATE  TABLE IF NOT EXISTS `bcc_food_client`.`houses` (
  `house_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `street_number` VARCHAR(45) NOT NULL ,
  `street_name` VARCHAR(45) NOT NULL ,
  `street_type` VARCHAR(45) NULL DEFAULT NULL ,
  `line2` VARCHAR(45) NULL ,
  `city` VARCHAR(45) NOT NULL ,
  `zip` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`house_id`) ,
  UNIQUE INDEX `house_UNIQUE` (`house_id` ASC, `street_number` ASC, `street_name` ASC, `street_type` ASC, `line2` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bcc_food_client`.`reasons`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bcc_food_client`.`reasons` ;

CREATE  TABLE IF NOT EXISTS `bcc_food_client`.`reasons` (
  `reason_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `reason_desc` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`reason_id`) ,
  UNIQUE INDEX `reason_expl_UNIQUE` (`reason_desc` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bcc_food_client`.`genders`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bcc_food_client`.`genders` ;

CREATE  TABLE IF NOT EXISTS `bcc_food_client`.`genders` (
  `gender_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `gender_desc` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`gender_id`) ,
  UNIQUE INDEX `gender_desc_UNIQUE` (`gender_desc` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bcc_food_client`.`ethnicities`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bcc_food_client`.`ethnicities` ;

CREATE  TABLE IF NOT EXISTS `bcc_food_client`.`ethnicities` (
  `ethnicity_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `ethnicity_desc` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`ethnicity_id`) ,
  UNIQUE INDEX `ethnicity_desc_UNIQUE` (`ethnicity_desc` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bcc_food_client`.`clients`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bcc_food_client`.`clients` ;

CREATE  TABLE IF NOT EXISTS `bcc_food_client`.`clients` (
  `client_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `spouse_id` INT UNSIGNED NULL ,
  `first_name` VARCHAR(45) NOT NULL ,
  `last_name` VARCHAR(45) NOT NULL ,
  `age` INT NOT NULL ,
  `phone_number` VARCHAR(45) NULL ,
  `house_id` INT UNSIGNED NULL ,
  `ethnicity_id` INT UNSIGNED NOT NULL ,
  `gender_id` INT UNSIGNED NOT NULL ,
  `reason_id` INT UNSIGNED NOT NULL ,
  `explanation` VARCHAR(150) NULL ,
  `unemployment_date` DATE NULL ,
  `application_date` DATE NOT NULL ,
  `receives_stamps` TINYINT(1)  NOT NULL ,
  `wants_stamps` TINYINT(1)  NOT NULL ,
  PRIMARY KEY (`client_id`) ,
  INDEX `client_house_id` (`house_id` ASC) ,
  INDEX `client_reason` (`reason_id` ASC) ,
  INDEX `client_gender` (`gender_id` ASC) ,
  INDEX `client_ethnicity` (`ethnicity_id` ASC) ,
  UNIQUE INDEX `client_UNIQUE` (`first_name` ASC, `last_name` ASC, `age` ASC, `phone_number` ASC, `house_id` ASC, `ethnicity_id` ASC, `gender_id` ASC, `reason_id` ASC) ,
  INDEX `client_spouse` (`spouse_id` ASC) ,
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
    ON UPDATE NO ACTION,
  CONSTRAINT `client_spouse`
    FOREIGN KEY (`spouse_id` )
    REFERENCES `bcc_food_client`.`clients` (`client_id` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bcc_food_client`.`distribution_type`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bcc_food_client`.`distribution_type` ;

CREATE  TABLE IF NOT EXISTS `bcc_food_client`.`distribution_type` (
  `dist_type_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `dist_type_desc` VARCHAR(15) NOT NULL ,
  PRIMARY KEY (`dist_type_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bcc_food_client`.`locations`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bcc_food_client`.`locations` ;

CREATE  TABLE IF NOT EXISTS `bcc_food_client`.`locations` (
  `location_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `location_name` VARCHAR(128) NOT NULL ,
  PRIMARY KEY (`location_id`) ,
  UNIQUE INDEX `location_name_UNIQUE` (`location_name` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bcc_food_client`.`usage`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bcc_food_client`.`usage` ;

CREATE  TABLE IF NOT EXISTS `bcc_food_client`.`usage` (
  `dist_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `client_id` INT UNSIGNED NOT NULL ,
  `type_id` INT UNSIGNED NOT NULL ,
  `location_id` INT UNSIGNED NOT NULL ,
  `date` DATE NOT NULL ,
  `note` VARCHAR(250) NULL ,
  INDEX `usage_client_id` (`client_id` ASC) ,
  INDEX `usage_dist_type_id` (`type_id` ASC) ,
  PRIMARY KEY (`dist_id`) ,
  INDEX `usage_location_id` (`location_id` ASC) ,
  CONSTRAINT `usage_client_id`
    FOREIGN KEY (`client_id` )
    REFERENCES `bcc_food_client`.`clients` (`client_id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `usage_dist_type_id`
    FOREIGN KEY (`type_id` )
    REFERENCES `bcc_food_client`.`distribution_type` (`dist_type_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `usage_location_id`
    FOREIGN KEY (`location_id` )
    REFERENCES `bcc_food_client`.`locations` (`location_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bcc_food_client`.`family_members`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bcc_food_client`.`family_members` ;

CREATE  TABLE IF NOT EXISTS `bcc_food_client`.`family_members` (
  `fam_member_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `age` INT NOT NULL ,
  `gender_id` INT UNSIGNED NOT NULL ,
  `ethnicity_id` INT UNSIGNED NOT NULL ,
  `member_house_id` INT UNSIGNED NULL ,
  `guardian_id` INT UNSIGNED NULL ,
  PRIMARY KEY (`fam_member_id`) ,
  INDEX `fam_gender_id` (`gender_id` ASC) ,
  INDEX `fam_house_id` (`member_house_id` ASC) ,
  INDEX `guardian_id` (`guardian_id` ASC) ,
  INDEX `fam_eth_id` (`ethnicity_id` ASC) ,
  CONSTRAINT `fam_gender_id`
    FOREIGN KEY (`gender_id` )
    REFERENCES `bcc_food_client`.`genders` (`gender_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fam_house_id`
    FOREIGN KEY (`member_house_id` )
    REFERENCES `bcc_food_client`.`houses` (`house_id` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `guardian_id`
    FOREIGN KEY (`guardian_id` )
    REFERENCES `bcc_food_client`.`clients` (`client_id` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `fam_eth_id`
    FOREIGN KEY (`ethnicity_id` )
    REFERENCES `bcc_food_client`.`ethnicities` (`ethnicity_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

USE `bcc_admin` ;

-- -----------------------------------------------------
-- Table `bcc_admin`.`access_levels`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bcc_admin`.`access_levels` ;

CREATE  TABLE IF NOT EXISTS `bcc_admin`.`access_levels` (
  `access_level_id` INT NOT NULL AUTO_INCREMENT ,
  `access_level_name` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`access_level_id`) ,
  UNIQUE INDEX `access_level_name_UNIQUE` (`access_level_name` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bcc_admin`.`administrators`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bcc_admin`.`administrators` ;

CREATE  TABLE IF NOT EXISTS `bcc_admin`.`administrators` (
  `admin_id` INT NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(45) NOT NULL ,
  `password` CHAR(64) NOT NULL ,
  `salt` CHAR(64) NOT NULL ,
  `access_id` INT NOT NULL ,
  PRIMARY KEY (`admin_id`) ,
  UNIQUE INDEX `username_UNIQUE` (`username` ASC) ,
  INDEX `admin_access` (`access_id` ASC) ,
  CONSTRAINT `admin_access`
    FOREIGN KEY (`access_id` )
    REFERENCES `bcc_admin`.`access_levels` (`access_level_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
