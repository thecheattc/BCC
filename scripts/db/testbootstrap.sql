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
  UNIQUE INDEX `house_UNIQUE` (`address` ASC, `city` ASC, `zip` ASC) )
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
  UNIQUE INDEX `client_UNIQUE` (`first_name` ASC, `last_name` ASC, `age` ASC, `phone_number` ASC, `house_id` ASC, `ethnicity_id` ASC, `gender_id` ASC, `unemployment_date` ASC, `reason_id` ASC) ,
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


INSERT INTO genders(gender_desc)
VALUES ('Male'), ('Female');

INSERT INTO ethnicities(ethnicity_desc)
VALUES ('White'), ('Black'), ('Hispanic'),
('Asian or Pacific Islander'), ('American Indian');

INSERT INTO reasons(reason_desc)
VALUES ('Lost job'), ('Unusual expenses this month'),
('To make ends meet'), ('Assistance lost/reduced'),
('DHS application in progress'), ('Homeless'),
('Other');

INSERT INTO houses(address, city, zip)
VALUES
('811 Brookwood Pl', 'Ann Arbor', '48104'),
('123 Fake St', 'Ann Arbor', '48109'),
('Near the Dumpster', 'Ann Arbor', '48107'),
(NULL, 'no', 'address'),
('no', NULL, 'city'),
('no', 'zip', NULL);

INSERT INTO clients(first_name, last_name, age, phone_number, house_id,
ethnicity_id, gender_id, reason_id, unemployment_date, application_date)
VALUES
('Evan', 'Lindell', '20', '5178994272', '1', '1', '1', '1', CURDATE(), CURDATE()),
('Mike', 'onna Bike', '27', '1800luvcoke', NULL, '1', '1', '6', CURDATE(), CURDATE()),
('Trisha', 'Takinawa', '42', '6124952284', '2', '4', '2', '3', CURDATE(), CURDATE());

INSERT INTO family_members(guardian_client_id, age, gender_id)
VALUES
('1', '14', '1'),
('1', '12', '2'),
('2', '20', '2'),
('3', '2', '1'),
('3', '3', '2'),
('3', '4', '2'),
('3', '5', '1'),
('3', '5', '2');

INSERT INTO distribution_type(dist_type_desc) VALUES ('Normal'), ('Emergency'), ('Rejected');

INSERT INTO usage(client_id, type_id, date) VALUES
(1,1,'2011-03-03'),
(1,1,'2011-04-03'),
(1,1,'2010-02-13'),
(1,1,'2007-12-26'),
(2,2,'2005-02-01'),
(3,1,'2007-07-07'),
(3,1,'2001-01-01'),
(3,1,'2003-11-17');
