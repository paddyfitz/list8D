SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

DROP SCHEMA IF EXISTS `list8D` ;
CREATE SCHEMA IF NOT EXISTS `list8D` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `list8D`;

-- -----------------------------------------------------
-- Table `list8D`.`list`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `list8D`.`list` ;

CREATE  TABLE IF NOT EXISTS `list8D`.`list` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `list_id` INT NULL DEFAULT NULL ,
  `order` INT NULL DEFAULT NULL ,
  `class` VARCHAR(255) NOT NULL ,
  `created` DATETIME NOT NULL ,
  `updated` DATETIME NOT NULL ,
  `author` INT NULL DEFAULT NULL ,
  `start` DATETIME NULL DEFAULT NULL ,
  `end` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_list_list1` (`list_id` ASC) ,
  INDEX `class` (`class` ASC) ,
  INDEX `order` (`order` ASC) ,
  INDEX `start_end` (`start` ASC, `end` ASC) ,
  CONSTRAINT `fk_list_list1`
    FOREIGN KEY (`list_id` )
    REFERENCES `list8D`.`list` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `list8D`.`list_data`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `list8D`.`list_data` ;

CREATE  TABLE IF NOT EXISTS `list8D`.`list_data` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `row_id` INT NOT NULL ,
  `key` VARCHAR(200) NOT NULL ,
  `value` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_list_data_list` (`row_id` ASC) ,
  UNIQUE INDEX `listid_key_unique` (`row_id` ASC, `key` ASC) ,
  INDEX `key_value` (`key` ASC, `value`(32) ASC) ,
  CONSTRAINT `fk_list_data_list`
    FOREIGN KEY (`row_id` )
    REFERENCES `list8D`.`list` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `list8D`.`resource`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `list8D`.`resource` ;

CREATE  TABLE IF NOT EXISTS `list8D`.`resource` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `class` VARCHAR(255) NOT NULL ,
  `created` DATETIME NOT NULL ,
  `updated` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `list8D`.`item`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `list8D`.`item` ;

CREATE  TABLE IF NOT EXISTS `list8D`.`item` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `list_id` INT NOT NULL ,
  `class` VARCHAR(255) NOT NULL ,
  `resource_id` INT NOT NULL ,
  `order` INT NULL DEFAULT NULL ,
  `start` DATETIME NULL DEFAULT NULL ,
  `end` DATETIME NULL DEFAULT NULL ,
  `created` DATETIME NOT NULL ,
  `updated` DATETIME NOT NULL ,
  `author` INT NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_item_list1` (`list_id` ASC) ,
  INDEX `fk_item_resource1` (`resource_id` ASC) ,
  INDEX `order` (`order` ASC) ,
  INDEX `class` (`class` ASC) ,
  INDEX `start_end` (`start` ASC, `end` ASC) ,
  INDEX `author` (`author` ASC) ,
  CONSTRAINT `fk_item_list1`
    FOREIGN KEY (`list_id` )
    REFERENCES `list8D`.`list` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_item_resource1`
    FOREIGN KEY (`resource_id` )
    REFERENCES `list8D`.`resource` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `list8D`.`item_data`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `list8D`.`item_data` ;

CREATE  TABLE IF NOT EXISTS `list8D`.`item_data` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `row_id` INT NOT NULL ,
  `key` VARCHAR(200) NOT NULL ,
  `value` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_item_data_item1` (`row_id` ASC) ,
  UNIQUE INDEX `itemid_key_unique` (`row_id` ASC, `key` ASC) ,
  INDEX `key_value` (`key` ASC, `value`(32) ASC) ,
  CONSTRAINT `fk_item_data_item1`
    FOREIGN KEY (`row_id` )
    REFERENCES `list8D`.`item` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `list8D`.`resource_data`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `list8D`.`resource_data` ;

CREATE  TABLE IF NOT EXISTS `list8D`.`resource_data` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `row_id` INT NOT NULL ,
  `key` VARCHAR(200) NOT NULL ,
  `value` TEXT NULL DEFAULT NULL ,
  `created` DATETIME NOT NULL ,
  `updated` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_resource_data_resource1` (`row_id` ASC) ,
  UNIQUE INDEX `resid_key_unique` (`row_id` ASC, `key` ASC) ,
  INDEX `key_value` (`key` ASC, `value`(32) ASC) ,
  CONSTRAINT `fk_resource_data_resource1`
    FOREIGN KEY (`row_id` )
    REFERENCES `list8D`.`resource` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `list8D`.`user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `list8D`.`user` ;

CREATE  TABLE IF NOT EXISTS `list8D`.`user` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `login` VARCHAR(128) NOT NULL ,
  `displayname` VARCHAR(255) NOT NULL ,
  `email` VARCHAR(255) NULL DEFAULT NULL ,
  `institutionid` VARCHAR(255) NULL DEFAULT NULL ,
  `role` VARCHAR(255) NOT NULL ,
  `created` DATETIME NOT NULL ,
  `updated` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `login` (`login` ASC) ,
  INDEX `email` (`email` ASC) ,
  INDEX `institutionid` (`institutionid` ASC) ,
  INDEX `role_id` (`role` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `list8D`.`change_log`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `list8D`.`change_log` ;

CREATE  TABLE IF NOT EXISTS `list8D`.`change_log` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `action` VARCHAR(45) NOT NULL ,
  `table` VARCHAR(255) NOT NULL ,
  `row_id` INT NOT NULL ,
  `changed` DATETIME NOT NULL ,
  `user` INT NULL DEFAULT NULL ,
  `column` VARCHAR(200) NOT NULL ,
  `value_from` TEXT NULL DEFAULT NULL ,
  `value_to` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `action_table` (`action` ASC, `table` ASC) ,
  INDEX `table_rowid` (`table` ASC, `row_id` ASC) ,
  INDEX `changed` (`changed` ASC) ,
  INDEX `user` (`user` ASC) ,
  INDEX `valfrom` (`value_from`(32) ASC) ,
  INDEX `valto` (`value_to`(32) ASC) ,
  INDEX `valfromto` (`value_from`(32) ASC, `value_to`(32) ASC) ,
  INDEX `fk_change_log_user1` (`user` ASC) ,
  CONSTRAINT `fk_change_log_user1`
    FOREIGN KEY (`user` )
    REFERENCES `list8D`.`user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB
PACK_KEYS = Default;


-- -----------------------------------------------------
-- Table `list8D`.`tag`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `list8D`.`tag` ;

CREATE  TABLE IF NOT EXISTS `list8D`.`tag` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `parent_id` INT NULL DEFAULT NULL ,
  `namespace` VARCHAR(24) NOT NULL ,
  `tagname` VARCHAR(128) NOT NULL ,
  `immutable` TINYINT NOT NULL ,
  `created` DATETIME NOT NULL ,
  `updated` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_tag_tag1` (`parent_id` ASC) ,
  UNIQUE INDEX `namespace_tagname` (`namespace` ASC, `tagname` ASC) ,
  INDEX `namespace` (`namespace` ASC) ,
  INDEX `tagename` (`tagname` ASC) ,
  INDEX `immutable` (`immutable` ASC) ,
  INDEX `created` (`created` ASC) ,
  INDEX `updated` (`updated` ASC) ,
  INDEX `updated_desc` (`updated` DESC) ,
  CONSTRAINT `fk_tag_tag1`
    FOREIGN KEY (`parent_id` )
    REFERENCES `list8D`.`tag` (`id` )
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `list8D`.`tagmap`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `list8D`.`tagmap` ;

CREATE  TABLE IF NOT EXISTS `list8D`.`tagmap` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `list_id` INT NOT NULL ,
  `tag_id` INT NOT NULL ,
  INDEX `fk_tagmap_list1` (`list_id` ASC) ,
  INDEX `fk_tagmap_tag1` (`tag_id` ASC) ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `listtag` (`list_id` ASC, `tag_id` ASC) ,
  CONSTRAINT `fk_tagmap_list1`
    FOREIGN KEY (`list_id` )
    REFERENCES `list8D`.`list` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_tagmap_tag1`
    FOREIGN KEY (`tag_id` )
    REFERENCES `list8D`.`tag` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `list8D`.`usermap`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `list8D`.`usermap` ;

CREATE  TABLE IF NOT EXISTS `list8D`.`usermap` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `user_id` INT NOT NULL ,
  `tag_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `listtag` (`user_id` ASC, `tag_id` ASC) ,
  INDEX `fk_usermap_user1` (`user_id` ASC) ,
  INDEX `fk_usermap_tag1` (`tag_id` ASC) ,
  CONSTRAINT `fk_usermap_user1`
    FOREIGN KEY (`user_id` )
    REFERENCES `list8D`.`user` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_usermap_tag1`
    FOREIGN KEY (`tag_id` )
    REFERENCES `list8D`.`tag` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `list8D`.`recent_lists`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `list8D`.`recent_lists` ;

CREATE  TABLE IF NOT EXISTS `list8D`.`recent_lists` (
  `created` DATETIME NOT NULL ,
  `user_id` INT NOT NULL ,
  `list_id` INT NOT NULL ,
  INDEX `fk_recent_lists_user` (`user_id` ASC) ,
  INDEX `fk_recent_lists_list` (`list_id` ASC) ,
  PRIMARY KEY (`list_id`, `user_id`) ,
  CONSTRAINT `fk_recent_lists_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `list8D`.`user` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_recent_lists_list`
    FOREIGN KEY (`list_id` )
    REFERENCES `list8D`.`list` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
