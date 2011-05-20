<?php

/**
 *
 * NAME
 *        import-old-data.php - Command line interface script for importing old University of Kent reading list system data.
 * 
 * SYNOPSIS
 *        php ./import-old-data.php [options] 
 * 
 * DESCRIPTION
 *        Imports data from the University of Kents old reading list system to the new list8D reading list system. 
 *        The University of Kent's old system is based on loughborough's, although its been hacked to bits so there are some inconsistancies.
 *				 
 * OPTIONS
 *        -c						 Clear old data. This will truncate the list, list_data and user tables before the import.
 *        -d             Debug mode. Using this option will prevent any data being writen to the database.
 *        -l limit       Limit the number of lists to import.
 *        -h             This help.
 *
 * AUTHORS
 *        list8D development team, Matthew Spence, Matthew Slowe, Matthew Bull, Ben Charlton, Simon Yeldon, Mark Fendley.
 *				
 *        This script was developed by Matthew Spence.
 *
 * COPYRIGHT
 *        Copyright (c) 2009 University of Kent (http://www.kent.ac.uk)
 *        
 *        This program is free software; you can redistribute it and/or
 *        modify it under the terms of the GNU General Public License
 *        as published by the Free Software Foundation; either version 2
 *        of the License, or (at your option) any later version.
 *        
 *        This program is distributed in the hope that it will be useful,
 *        but WITHOUT ANY WARRANTY; without even the implied warranty of
 *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *        GNU General Public License for more details.
 *        
 *        You should have received a copy of the GNU General Public License
 *        along with this program; if not, write to the Free Software
 *        Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 */
 
 	
 	// include db settings
 	require("import-old-data-settings.php");
 	
 	// create log file
 	global $log;
 	global $logCombined;
 	$log = fopen("import-log-".time().".txt",'w');
 	$logCombined = fopen("import-log-combined.txt",'w');
 	unlink("import-log-resource.txt"); 
 	$logFailedResource = fopen("import-log-resource.txt",'w');
 	function wlog($message) {
 	
 		global $log;
	 	global $logCombined;
 		fwrite($log, $message."\n");
 		fwrite($logCombined, $message."\n");
 	}
 	  
	// Must be run from the cli
	if(!empty($_SERVER['HTTP_HOST'])) {
		echo "Sorry this script must be run from the command line.";
		wlog("Sorry this script must be run from the command line.");
		die;
	}
	
	/**
	 * Get CLI options.
	 */
	 
	function sort_items($a,$b) {
	  if ($a['rank'] == $b['rank'])
	  	return 0;
	  return ($a['rank'] < $b['rank']) ? -1 : 1;	
	}
  
    // Get cli options
	$args = getopt('hdl:cr');
	
	// Debug -d
	if(isset($args['d'])) {
		$save = false;
	} else {
		$save = true;
	}
	
	// Limit -l
	if (isset($args['l'])) {
		$limit = $args['l'];
	} else {
		$limit = false;
	}
	
	// Clear -c
	if (isset($args['c'])) {
		$clear = true;
	} else {
		$clear = false;
	}
	
	// Clear -r
	if (isset($args['r'])) {
		$clearResource = true;
	} else {
		$clearResource = false;
	}
	
	// Help -h
	if (isset($args['h'])) {
		
		// echo help and die
		echo "\n";
		echo <<< QQ
NAME
       import-old-data.php - Command line interface script for importing old University of Kent reading list system data.

SYNOPSIS
       php ./import-old-data.php [options] 

DESCRIPTION
       Imports data from the University of Kents old reading list system to the new list8D reading list system. 
       The University of Kent's old system is based on loughborough's, although its been hacked to bits so there are some inconsistancies.
			 
OPTIONS
       -c             Clear old data. This will truncate the list, list_data and user tables before the import.
       -d             Debug mode. Using this option will prevent any data being writen to the database.
       -l limit       Limit the number of lists to import.
       -h             This help.

AUTHORS
       list8D development team, Matthew Spence, Matthew Slowe, Matthew Bull, Ben Charlton, Simon Yeldon, Mark Fendley.
			
       This script was developed by Matthew Spence.

COPYRIGHT
       Copyright (c) 2009 University of Kent (http://www.kent.ac.uk)
       
       This program is free software; you can redistribute it and/or
       modify it under the terms of the GNU General Public License
       as published by the Free Software Foundation; either version 2
       of the License, or (at your option) any later version.
       
       This program is distributed in the hope that it will be useful,
       but WITHOUT ANY WARRANTY; without even the implied warranty of
       MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
       GNU General Public License for more details.
       
       You should have received a copy of the GNU General Public License
       along with this program; if not, write to the Free Software
       Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
QQ;
		echo "\n\n";
		die;
		
	}
		
  // Increase memory limit as this script may take a while to run.
	ini_set('memory_limit','256M');
	wlog("memory set to 256M");
	wlog("error reporting set to E_ERROR");
	
	// Initialize the application path and autoloading
	defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
	set_include_path(implode(PATH_SEPARATOR, array(
												   APPLICATION_PATH . '/../library',
												   APPLICATION_PATH . '/models',
												   get_include_path(),
							 )));
	
	require_once 'Zend/Loader/Autoloader.php';
	$autoloader = Zend_Loader_Autoloader::getInstance();
	$autoloader->registerNamespace("List8D");
	$autoloader->registerNamespace("LegacyModel_");
	$resourceLoader = new Zend_Application_Module_Autoloader(array(
																   'namespace' => 'List8D_',
																   'basePath'  => dirname(__FILE__).'/../application',
																   ));														   
	$legacyLoader = new Zend_Loader_Autoloader_Resource(array(
    'basePath'  => dirname(__FILE__).'/LegayModel',
    'namespace' => 'LegacyModel',
	));
	
	//! Enviroment
 	define('APPLICATION_ENV','staging');
 	
	// Initialize Zend_Application
	$application = new Zend_Application(
										APPLICATION_ENV,
										APPLICATION_PATH . '/configs/application.ini'
	);
	
	// Initialize and retrieve DB resource
	$bootstrap = $application->getBootstrap();
	$bootstrap->bootstrap('db');
    Zend_Registry::set('dbResource', $bootstrap->getPluginResource('db'));
    
	wlog("set up autoloader and bootstrap");
	
	// Get metatrons
	$conf = $application->getOptions();
	// $metatronList = $conf['list8d']['metatrons'];
	$metatronList = array('List8D_Model_Metatron_KentVoyager', 'List8D_Model_Metatron_Amazon');
	
	// Connect to old db
	$db = Zend_Db::factory('Pdo_Mysql', $old_db_settings);
	wlog("connected to old db");
		
	// Display a bit of info on what the script will do.
	echo "\nThis script will import data from the old read list system at:\n";	
	echo "  Host: {$old_db_settings['host']}\n";
	echo "  Database: {$old_db_settings['dbname']}\n";
	echo "and import it to your list8D system at:\n";
	echo "  Host: {$conf['resources']['db']['params']['host']}\n";
	echo "  Database: {$conf['resources']['db']['params']['dbname']}\n";
	
	// Display and bit of info on what options are set and what they will do	
	if (!$save || $clear || $limit) {
		echo "\nSelected options:\n";
	}
	if(!$save) {
		echo "  - Debug: no data will be written to the database.\n";
	}	
	if($limit) {
		echo "  - Limit: number of lists imported will be limited to $limit.\n";
	}
	if($clear && $save) {
		echo "  - Clear data: old list8D data will be removed from user and list table.\n";
	}
	
	// Save number of lines 
    $lines = 1;
  
	//! Display warning if saving to or truncating destination tables
	if ($save) {
		echo "\n\033[7s\033[41;38;5;1mWARNING!\033[0m\033[41;38m\n";
		$lines++; $lines++;
		if ($save) {
			echo "Data will be written to the database!\n";
			$lines++;
		}
		
		if ($clear && $save && !$clearResource) {
			echo "Existing data will be removed from all tables (apart from resource and resource_data)!\n";
			$lines++;
		} else if ($clear && $save && $clearResource) {
			echo "Existing data will be removed from all tables!\n";
			$lines++;
		} else if ($save && $clearResource) {
			echo "Existing data will be removed from the resource and resource-data tables!\n";
			$lines++;
		}

		echo "This action cannot be undone!\n";
		$lines++;
		echo "\033[0m";
	
	} else {
	   echo "\n\033[7s\033[41;38mNB: No data will be saved to the database.\033[0m\n";
	   $lines++;$lines++;
	   
	} 
			
	// Get confirmation
	fwrite(STDOUT, "\nAre you sure you wish to continue (y/n): ");
	$sure = trim(fgets(STDIN));
	wlog("got confirmation");
			
	// Go back and erase warning as its a bit noisey on the console
	echo "\033[{$lines}A";
	echo "\033[0J";
	
	
	// If they dont confirm exit
	if ($sure != 'yes' && $sure != 'y') {
		echo "Import of old reading list data aborted by user.\n\n";
		die;
	}
	
	echo "\nStarting import of old reading list data...\n";	
	
	// We need to do some changes to the source DB for Zend to work
	if ($save) {
        if(count($db->fetchAll("SHOW KEYS FROM owner"))<3) {
	       $db->query("ALTER TABLE `rl_old`.`owner` MODIFY COLUMN `list_id` INTEGER NOT NULL DEFAULT 0,
 MODIFY COLUMN `name` VARCHAR(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 0,
 MODIFY COLUMN `userid` VARCHAR(12) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 0,
 MODIFY COLUMN `type` CHAR(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 0,
 MODIFY COLUMN `email` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 0,
 ADD PRIMARY KEY (`list_id`, `name`, `email`, `type`, `userid`);");
  	      echo "\nModified old database for compatibility with Zend.\n";
			    wlog("Modified old database for compatibility with Zend.");
        }
        
    }

    	
	// Truncate destination tables (if clear and save options are set)
	if ($save && $clear) {
    wlog("Starting to empty tables.");
		$new_db = Zend_Db::factory('Pdo_Mysql', array(
			'host'     => $conf['resources']['db']['params']['host'],
  	  'username' => $conf['resources']['db']['params']['username'],
  	  'password' => $conf['resources']['db']['params']['password'],
  	  'dbname'   => $conf['resources']['db']['params']['dbname']
		));
		$new_db->query("SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `list`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `list` ;

CREATE  TABLE IF NOT EXISTS `list` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `list_id` INT NULL DEFAULT NULL ,
  `order` INT NULL DEFAULT NULL ,
  `class` VARCHAR(255) NOT NULL ,
  `created` DATETIME NOT NULL ,
  `updated` DATETIME NOT NULL ,
  `author` INT NULL DEFAULT NULL ,
  `start` DATETIME ,
  `end` DATETIME ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_list_list1` (`list_id` ASC) ,
  INDEX `class` (`class` ASC) ,
  INDEX `order` (`order` ASC) ,
  INDEX `start_end` (`start` ASC, `end` ASC) ,
  CONSTRAINT `fk_list_list1`
    FOREIGN KEY (`list_id` )
    REFERENCES `list` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `list_data`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `list_data` ;

CREATE  TABLE IF NOT EXISTS `list_data` (
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
    REFERENCES `list` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;



-- -----------------------------------------------------
-- Table `item`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `item` ;

CREATE  TABLE IF NOT EXISTS `item` (
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
    REFERENCES `list` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_item_resource1`
    FOREIGN KEY (`resource_id` )
    REFERENCES `resource` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `item_data`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `item_data` ;

CREATE  TABLE IF NOT EXISTS `item_data` (
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
    REFERENCES `item` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user` ;
CREATE  TABLE IF NOT EXISTS `user` (
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
-- Table `change_log`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `change_log` ;

CREATE  TABLE IF NOT EXISTS `change_log` (
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
    REFERENCES `user` (`id` )
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tag`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tag` ;

CREATE  TABLE IF NOT EXISTS `tag` (
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
    REFERENCES `tag` (`id` )
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tagmap`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tagmap` ;

CREATE  TABLE IF NOT EXISTS `tagmap` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `list_id` INT NOT NULL ,
  `tag_id` INT NOT NULL ,
  INDEX `fk_tagmap_list1` (`list_id` ASC) ,
  INDEX `fk_tagmap_tag1` (`tag_id` ASC) ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `listtag` (`list_id` ASC, `tag_id` ASC) ,
  CONSTRAINT `fk_tagmap_list1`
    FOREIGN KEY (`list_id` )
    REFERENCES `list` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_tagmap_tag1`
    FOREIGN KEY (`tag_id` )
    REFERENCES `tag` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `usermap`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `usermap` ;

CREATE  TABLE IF NOT EXISTS `usermap` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `user_id` INT NOT NULL ,
  `tag_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `listtag` (`user_id` ASC, `tag_id` ASC) ,
  INDEX `fk_usermap_user1` (`user_id` ASC) ,
  INDEX `fk_usermap_tag1` (`tag_id` ASC) ,
  CONSTRAINT `fk_usermap_user1`
    FOREIGN KEY (`user_id` )
    REFERENCES `user` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_usermap_tag1`
    FOREIGN KEY (`tag_id` )
    REFERENCES `tag` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;


");
		
    echo "\nPrevious data on the source database all tables (apart from resource and resource_data) removed.\n";
    wlog("Previous data on the source database from all tables (apart from resource and resource_data) removed.");
	}
	
	if ($save && $clearResource) {
	wlog("Starting to empty resource table");
		$new_db = Zend_Db::factory('Pdo_Mysql', array(
			'host'     => $conf['resources']['db']['params']['host'],
  	  'username' => $conf['resources']['db']['params']['username'],
  	  'password' => $conf['resources']['db']['params']['password'],
  	  'dbname'   => $conf['resources']['db']['params']['dbname']
		));
		$new_db->query("SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `resource`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `resource` ;

CREATE  TABLE IF NOT EXISTS `resource` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `class` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;



-- -----------------------------------------------------
-- Table `resource_data`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `resource_data` ;

CREATE  TABLE IF NOT EXISTS `resource_data` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `row_id` INT NOT NULL ,
  `key` VARCHAR(200) NOT NULL ,
  `value` TEXT NULL DEFAULT NULL ,
  `created` DATETIME NOT NULL ,
  `updated` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_resource_data_resource1` (`row_id` ASC) ,
  UNIQUE INDEX `resid_key_unique` (`row_id` ASC, `key` ASC) ,
  INDEX `key_value` (`key` ASC, `value`(32) ASC) ,
  CONSTRAINT `fk_resource_data_resource1`
    FOREIGN KEY (`row_id` )
    REFERENCES `resource` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

");
		
    echo "\nPrevious data on the source database from the user, list, list_data, item, item_data, resource and resource_data tables removed.\n";
    wlog("Previous data on the source database from the user, list, list_data, item, item_data, resource and resource_data tables removed.");
	}
	
	

	/**
	 * Import lists
	 */
	//! list import
	// Get old lists
	$listTable = new LegacyModel_DbTable_List(array ('db'=>$db));
	$listQuery = $listTable->select()->where("year = 2009");
	//$listQuery->where("module_code in ('SO304','SO304','HI300','SA303','SO600','LS556','PO554','PO616','FR551','FR870','GE509','DR575', 'SA531', 'DR592','SO533','CL313','AC501')");
	//$listQuery->where("module_code in ('FR551')");
	if ($limit) {
		$listQuery->limit($limit);
	}
	$lists = $db->query($listQuery)->fetchAll();
	
	// Get all old users (ignores limit) and store in array for quick access
	$userTable = new LegacyModel_DbTable_User(array ('db'=>$db));
	$owners = $userTable->fetchAll($userTable->select()
			->group('list_id') 
			->where('userid != ""')
			->where('type = 0')
			->where('userid is not null')
	);
	$new_owners = array();
	foreach($owners as $key =>$value) {
		$new_owners[$value->list_id]=$value->userid;
	}
	$owners = $new_owners;
	
	// Display 'starting list import' and progress
	echo "\nImporting ".count($lists)." old lists...";	
	wlog("Importing ".count($lists)." old lists.");	
	$progress = new List8D_CliProgress(count($lists));
  
  // Array to keep track of imported arrays incase of limit option
  $imported_lists = array();
  
  $i=0;
  
	// Foreach list
	foreach($lists as $old) {
		
	  	foreach($old as $key => &$value) {
	  		$value = utf8_decode($value);
	  	}
		// Create new list
		$list = new List8D_Model_List();
    $list->setStart("2009-09-01 00:00:00");
    $list->setEnd("2010-07-01 00:00:00");
		$list->setData("title", $old['module_name']);
		$list->setData("code", $old['module_code']);
		$list->setData("year", $old['year']);
		$list->setData("old_list_id",$old['list_id']);
		if ($old['publish']=="Y")
			$list->setData("is_published", true);
		else
			$list->setData("is_published", false);
		
		// Find owner in destination user table
		if (!empty($owners[$old['list_id']]))
			$owner = $owners[$old['list_id']];
		$newOwner = new List8D_Model_User();
		if (!empty($owner)) {
			$newOwner->findByLogin($owner);
			$ownerId = $newOwner->getId();
		} else {
			$ownerId = null;
		}		
		if (!empty($ownerId)) {
			$list->setData('convener',$ownerId);			
		}	
		
		// Save
		if ($save) {
			$list->save();
		}
		// Make note of import
		$imported_lists[$old['list_id']] = $list->getId();
		
		// Update progress bar
		$progress->update();
		
		$i++;
		
		wlog("imported list with id ".$old['list_id']." to id ".$list->getId());
		
	}
  
	// Display 'Finished importing lists'
	echo "\nFinished importing old lists.\n";
	wlog("Finished importing $i lists, from ".count($lists));
	
	//! user import
	/**
	 * Import users
	 */
	
	// Get old users
	$userTable = new LegacyModel_DbTable_User(array ('db'=>$db));
	$userQuery = $userTable->select()
			->group('userid') 
			->where('userid != ""')
			->where('type = 0')
			->where('userid is not null')
			->where('list_id in ('.implode(",",array_keys($imported_lists)).')');
	$users = $userTable->fetchAll($userQuery);
	
	// Display 'starting user import' and progress bar
	echo "\nImporting ".count($users)." old list owners...\n";	
	wlog("\nImporting ".count($users)." old list owners.");	
	$progress = new List8D_CliProgress(count($users));
	
    // Foreach user create new user and update progress bar
	foreach($users as $old) {
	  	foreach($old as $key => &$value) {
	  		$value = utf8_decode($value);
	  	}
		$new = new List8D_Model_User();
		$new->findByLogin($old->userid);
		$new->setLogin($old->userid);
		$new->setDisplayName($old->name);
		$new->setEmail($old->email);
		$new->setInstitutionId(1);
		$new->setRole('academic');	
		if ($save) {
			$new->save();	
		}
		wlog("imported user with id ".$old->userid." to id ".$new->getId());
		$progress->update();
	}
	
	// Display 'finished user import'
	echo "\nFinished importing old list owners.\n\n";
	wlog("finished ".count($users)."importing users");
	
	//! item import
	/**
	 * Import items
	 */
	 
	// Get old items
	$itemTable = new LegacyModel_DbTable_Item(array ('db'=>$db));
	
  // Display 'starting list import' and progress
	echo "Importing old list items for ".count($imported_lists)." lists (including notes)...";	
	wlog("importing old list items for ".count($imported_lists)." lists (including notes)");
	
  // count imported items
  $imported_items = 0;
  $previousItems= 0;
  $legacy_items = 0;
  $failedItemImports = array();
  $previousTime = time();
  
  
  echo "\n\n\n\n\n";
  $i=1;
  $k=0;
	$progress = new List8D_CliProgress(count($imported_lists));
	$notesC = 0;
  // Foreach item 
  foreach($imported_lists as $key => $listId) {
					  	
  	echo "\033[4F";
		echo "\033[0J";
  	
		echo "Total progress:\n";	
		$progress->step = $i;
		$progress->draw();
		echo "\n";
		
		// Fetch the list items
		$items = $db->fetchAll("SELECT `material`.*, `sequence`.*, `annotation`.* FROM `material` INNER JOIN `sequence` ON sequence.material_id = material.material_id INNER JOIN `annotation` ON sequence.note_id = annotation.note_id WHERE list_id = '$key'");
		// Fetch the list notes and merge them with items
		//$notesC += count($db->fetchAll("SELECT `sequence`.*, `annotation`.* FROM `sequence` LEFT JOIN `annotation` ON sequence.note_id = annotation.note_id WHERE material_id IS NULL AND list_id='$key'"));
		$items = array_merge($items,$db->fetchAll("SELECT `sequence`.*, `annotation`.* FROM `sequence` LEFT JOIN `annotation` ON sequence.note_id = annotation.note_id WHERE material_id IS NULL AND list_id='$key'"));
		
		
		usort($items,'sort_items');
		
		wlog("importing ".count($items)." for list with old id ".$key." to list with new id ".$listId);
		$importingList = $listId;
	  $list = new List8D_Model_List();
	  
	  $list = $list->getById($listId);
	  $currentSubList = $list;

	  $j=1;
	  
	  if (count($items)) {
		  $listProgress = new List8D_CliProgress(count($items)); 
		}
	  echo "\n";
	  echo "\n";
	  
		$p=0;
	  foreach ($items as $old) {


	  	foreach($old as $key => &$value) {
	  		//$value = utf8_decode($value);
	  		$value = strip_tags($value);
	  	}

	  	echo "\033[2F";
			echo "\033[0J";
			echo "List progress (".count($items)." items):\n";	
  		$listProgress->step = $j;
  		$listProgress->draw();	
			echo "\nUsing ".round((memory_get_usage()/1024)/1024,3)." MB";
			
	  	$imported_items++;
	  	
	  	$item = new List8D_Model_Item();
    	
    	// If there is no material_id ie its a note create sub list
    	if (empty($old['material_id']) && !empty($old['content'])) {
    	
    		$item = new List8D_Model_NestedList();
    		$item->setData('title',strip_tags($old['content']));
    		$item->setData('is_published',true);
    		$item->setPosition($old['rank']);
				$item->setListId($listId);
				$currentSubList = $item;
				
				$item->setData("old_note_id",$old['note_id']);
    	} else {
    		
    		// if the title contains the phrase dvd 
    		// and it doesnt have isbn
    		// and it has a control_no
    		// its a dvd
    		
    		if ((stripos($old['title'],"dvd")!==false || stripos($old['private_note'],"dvd")!==false || stripos($old['content'],"dvd")!==false) && empty($old['isbn']) && !empty($old['control_no'])) {
	    		
    			$pm = new List8D_Model_Resource_PhysicalMedia();
    			
    			$pm->setData('Voyager_rcn',$old['control_no']);
	    			
    			if (!empty($old['title']))
	    			$pm->setData('title',$old['title']);
    			
    			if (!empty($old['title']))
	    			$pm->setData('title',$old['title']);
	    			
    			if (!empty($old['authors']))
	    			$pm->setData('authors',$old['authors']);
	    		
	    		if (!empty($old['edition']))	
	    			$pm->setData('edition',$old['edition']);
	
	    		if (!empty($old['publisher']))	
	    			$pm->setData('publisher',$old['publisher']);

	    		if (!empty($old['pubdate']))	
	    			$pm->setData('publication_date',$old['pubdate']);
	    			
    			$position = $old['rank'];
    		  
    			if ($save) {
    				$pm->save();
  	  		  $item = $currentSubList->addItem($pm);
  	  		  $item->setPosition($position);
  	  		  $item->save();
    		  }
    		}
    		// if the title or notes contains the phrase dvd 
    		// and it doesnt have isbn
    		// and it doesnt have a control_no
    		// its a legacydvd
				elseif ((stripos($old['title'],"dvd")!==false || stripos($old['private_note'],"dvd")!==false || stripos($old['content'],"dvd")!==false) && empty($old['isbn']) && empty($old['control_no'])) {
					
					$legacy_items++;
    			$legacy = new List8D_Model_Resource_LegacyPhysicalMedia();
    			
    			if (!empty($old['title']))
	    			$legacy->setData('title',$old['title']);
	    			
    			if (!empty($old['authors']))
	    			$legacy->setData('authors',$old['authors']);
	    		
	    		if (!empty($old['edition']))	
	    			$legacy->setData('edition',$old['edition']);
	
	    		if (!empty($old['publisher']))	
	    			$legacy->setData('publisher',$old['publisher']);

	    		if (!empty($old['pubdate']))	
	    			$legacy->setData('publication_date',$old['pubdate']);
	    			
    			$position = $old['rank'];
    		  
    			if ($save) {
    				$legacy->save();
  	  		  $item = $currentSubList->addItem($legacy);
  	  		  $item->setPosition($position);
  	  		  $item->save();
    		  }
				}
    		// if the material has an isbn or rcn create a book resource 
				elseif (!empty($old['control_no']) || (!empty($old['isbn']) && (count($old['isbn'])==13 || count($old['isbn'])==10))) {

    			$book = new List8D_Model_Resource_Book();
    			
    			$find = array();
    			

    			if (!empty($old['control_no'])) {
    				$find['Voyager_rcn'] = $old['control_no'];
    			}
    			
    			if (!empty($old['isbn']) && (count($old['isbn'])==13 || count($old['isbn'])==10)) {
    				if (strlen($old['isbn'])==10) {
    					$find['isbn10']=$old['isbn'];
    				} else if (strlen($old['isbn'])==13) {
    					$find['isbn13']=$old['isbn'];
    				} else if (isset($old['isbn'])) {
    					$find['isbn']=$old['isbn'];
    				}
    			} 
  		
    			if ($book->findByData($find,0,1)) {
    				$book = $book->findByData($find,0,1);
    			} else {
    			
    				$book = new List8D_Model_Resource_Book();
    				$book->setDataByArray($find);  				
    				try {
    					$book->loadMetadata(true);
    					
              $book->setClass($book->getDataValue('classHint'));

    				} catch (Exception $e) {
    					$failedItemImports[] = $old['material_id'];
    					wlog("failed to import item ".$old['sequence_id']." because: ".$e->getMessage());
              fwrite($logFailedResource, "failed to import item ".$old['sequence_id']." because: ".$e->getMessage()."\n");
    					$legacy_items++;
              $book = new List8D_Model_Resource_Legacy();
              $book->setData('title',$old['title']);
              $book->setData('authors',$old['authors']);
              $book->setData('edition',$old['edition']);
              $book->setData('publisher',$old['publisher']);
              $book->setData('publication_date',$old['pubdate']);
              if (!empty($old['volume'])) 
	    					$book->setData('volume',$old['volume']);
							if (!empty($old['issue']))
		  	  			$book->setData('issue',$old['issue']);
				      $position = $old['rank'];
				      
    				}

 						if (!empty($old['url']) && !preg_match("/^http:\/\/opac.kent.ac.uk/",$old['url']))
				      $book->setData('url',$old['url']);
				      
    				if ($save) {
    					$book->save();
    				}
    			}
		  	  	
    		  $position = $old['rank'];
    		  
    		  if ($save) {
  	  		  	$item = $currentSubList->addItem($book);
  	  		 }
  	  		 
					if ($save) {
  	  		  	$item->setPosition($position);
  	  		  	$item->save();
    		  }
    		}
    		// has volume, issue or url that starts with a serial solutions create legacy journal
    		else if (
    			(isset($old['volume']) && $old['volume']!="") ||
    			(isset($old['issue']) && $old['issue']!="") ||
    			(
    				(isset($old['url']) && $old['url']!="") && (
    					preg_match("/^http:\/\/library\.kent\.ac\.uk\/library\/online\/journals/",$old['url']) ||
    					preg_match("/^http:\/\/df7sm3xp4s\.search\.serialssolutions\.com/",$old['url'])
    				)
    			)
    		) {
    		
    			$legacy_items++;
    			$legacy = new List8D_Model_Resource_LegacyJournal();
    			
    			if (!empty($old['parttitle'])) {
		    		$legacy->setData('title',$old['parttitle']);
		    		unset($old['parttitle']);
	    			$legacy->setData('journal',$old['title']);
		    	} else {
		    		$legacy->setData('title',$old['title']);
		    	}
		    	
		    	if (!empty($old['partauthor'])) {
		    		$legacy->setData('authors',$old['partauthor']);
		    	} else {
		    		$legacy->setData('authors',$old['authors']);
		    	}
					if (!empty($old['volume']))
	    			$legacy->setData('volume',$old['volume']);
					if (!empty($old['issue']))
	    			$legacy->setData('issue',$old['issue']);
					if (!empty($old['edition']) && $old ['edition'] !== 0)
	    			$legacy->setData('edition',$old['edition']);
    			$legacy->setData('publisher',$old['publisher']);
    			$legacy->setData('publication_date',$old['pubdate']);

				  if (!empty($old['url']))
				    $legacy->setData('url',$old['url']);
				    
    			$position = $old['rank'];
    		  
    			if ($save) {
    				$legacy->save();
  	  		  $item = $currentSubList->addItem($legacy);
  	  		  $item->setPosition($position);
  	  		  $item->save();
    		  }
    		
    		}
				else if (!empty($old['url'])) {
				
    		    $uri = new List8D_Model_Resource_Url();
    		    
    		    if($uri = $uri->findByData('url',$old['url'])) {
    		    	
				
    		    } else {
							$uri = new List8D_Model_Resource_Url();
	    		    $uri->setData('url',$old['url']);
							$uri->setData('title',$old['title']);
	    				$uri->setData('authors',$old['authors']);
							$uri->setData('publication_date',$old['pubdate']);
							if($save) {
								$uri->save();      	    	
							}
							
    		    }   	    
    		    
    		    $position = $old['rank'];
    		    
    		    if ($save) {
  	  		    $item = $currentSubList->addItem($uri);
  	  		  	       	$item->setPosition($position);
  	  		  	       	$item->save();
    		    }
    		    
    		}
    		// otherwise create a legacy item
    		else {
    			
    			$legacy_items++;
    			$legacy = new List8D_Model_Resource_Legacy();
    			
    			if (!empty($old['title']))
	    			$legacy->setData('title',$old['title']);
	    		else if (!empty($old['parttitle']))
	    			$legacy->setData('title',$old['parttitle']);
	    			unset($old['partitle']);
	    			
    			if (!empty($old['authors']))
	    			$legacy->setData('authors',$old['authors']);
	    		else if (!empty($old['partauthor']))
	    			$legacy->setData('authors',$old['partauthor']);
	    		
	    		if (!empty($old['edition']))	
	    			$legacy->setData('edition',$old['edition']);
	
	    		if (!empty($old['publisher']))	
	    			$legacy->setData('publisher',$old['publisher']);

	    		if (!empty($old['pubdate']))	
	    			$legacy->setData('publication_date',$old['pubdate']);
	    		
				     
    			$position = $old['rank'];
    		  
    			if ($save) {
    				$legacy->save();
  	  		  $item = $currentSubList->addItem($legacy);
  	  		  $item->setPosition($position);
  	  		  $item->save();
    		  }
    			
    		}
    		
    		// set other item data values
    		if (!empty($old['pages']))
	    		$item->setData('part',"pages ".$old['pages']);
    		if (!empty($old['parttitle']))
	    		$item->setData('part_title',$old['parttitle']);
    		if (!empty($old['partauthor']))
	    		$item->setData('part_author',$old['partauthor']);
    		if (!empty($old['keytext']))
	    		$item->setData('core_text',$old['keytext']);
    		$item->setData('recommended_for_purchase',$old['purchase']);
    		if (!empty($old['private_note']))
	    		$item->setData('private_notes',$old['private_note']);
	    	if (!empty($old['content']))	
	    		$item->setData('public_notes',$old['content']);
    		$item->setData('is_published',true);
    		if (!empty($old['volume'])) 
	    		$item->setData('volume',$old['volume']);
    		if ($item->getResource()->getType() == 'Journal' && !empty($old['pubdate'])) 
	    		$item->setData('publication_date',$old['pubdate']);
    		if ($item->getResource()->getType() == 'Uri' && !empty($old['authors'])) 
	    		$item->setData('resource_authors',$old['authors']);
    		if ($item->getResource()->getType() == 'Uri' && !empty($old['title'])) 
	    		$item->setData('resource_title',$old['title']);
				if (!empty($old['issue']))
		  	  $item->setData('issue',$old['issue']);
		  	  
				$item->setData("old_material_id",$old['material_id']);
	    	$k++;   
	    	
	    			
	    	
    	}
    	

    	if ($save) {
    		$item->save();
    	}
    	$new_db_settings = $new_db_settings;
	  $newdb = Zend_Db::factory('Pdo_Mysql', $new_db_settings);
		$itemCheck = $newdb->query("SELECT COUNT(*) FROM item")->fetch();
	    	
	    	
      if($itemCheck["COUNT(*)"]<$k) {
       fwrite($logFailedResource, "number of items not right!\nlast import was {$importingList} going to {$item->getId()} {$itemCheck["COUNT(*)"]}!={$k}\n");
			 echo "\n\nnumber of items not right!\nlast import was {$importingList} going to {$item->getId()}  {$itemCheck["COUNT(*)"]}!={$k}\n";
    	 exit;
    	}

    	wlog("imported list item with old id ".$old['sequence_id']." to new id ".$item->getId());
    	$p++;
    	$j++;
    	
	  }     
	  wlog("finished importing $p items for list ".$list->getId()." out of ".count($items));
    
    $i++;
    
  }
  
  echo "\033[4F";
	echo "\033[0J";  
	
	$progress->draw();
  
	// Display 'Finished import'
	echo "\nFinished importing $imported_items list items.\n";	

	wlog("Finished importing $imported_items list items.");	

	if(count($failedItemImports))
		echo count($failedItemImports)." resources failed to load, probably due to metatron exceptions.";
	
	// Display 'Finished import'
	echo "\nFinished importing old reading list system.\n";		
	if (!$save) {
	   echo "No data was saved to the database.\n";
	}
	echo "\n";
	
	
	//! testing
	echo "\nPerforming tests...\n";
	wlog("Staring tests.\n");
	$failed = 0;
	$new_db_settings = $new_db_settings;
	$newdb = Zend_Db::factory('Pdo_Mysql', $new_db_settings);
	
	//! testing number of old lists
	$lists = $db->query($listQuery)->fetchAll();
	$numberOfOldLists = count($lists);
	$numberOfNewLists = $newdb->query("SELECT COUNT(*) FROM list WHERE list_id IS NULL")->fetch();
	$numberOfNewLists = $numberOfNewLists["COUNT(*)"];
	echo "\nList import:\t";
	if ($numberOfOldLists == $numberOfNewLists) {
		echo "\033[42;38m Passed: $numberOfNewLists list imported \n";
		wlog("all list imported ok");
	} else {
		echo "\033[41;38m Failed: ".($numberOfOldLists-$numberOfNewLists)." lists missing \n";
		
		wlog(($numberOfOldLists-$numberOfNewLists)." lists failed to import");
		$failed++;
	}
	echo "\033[0m";
	
  //! testing number of items
	$numberOfOldItems = $db->fetchAll("SELECT COUNT(*) FROM `material` INNER JOIN `sequence` ON sequence.material_id = material.material_id INNER JOIN `readinglist` ON sequence.list_id = readinglist.list_id WHERE readinglist.year = 2009 AND readinglist.list_id in (".implode(",",array_keys($imported_lists)).")");
	$numberOfOldItems = $numberOfOldItems[0]["COUNT(*)"];
	$numberOfNewItems = $newdb->query("SELECT COUNT(*) FROM item")->fetch();
	$numberOfNewItems = $numberOfNewItems["COUNT(*)"];
	echo "Item import:\t";
	if ($numberOfNewItems == $numberOfOldItems) {
		echo "\033[42;38m Passed: $numberOfNewItems items imported \n";
		wlog("all items imported ok");
	} else {
		echo "\033[41;38m Failed: (missing ".($numberOfOldItems-$numberOfNewItems)." items)\n";
		wlog(($numberOfOldItems-$numberOfNewItems)." items failed to import");
		$failed++;
	}
	echo "\033[0m";
	
	//! testing number of users
	$numberOfOldUsers = $db->fetchAll("SELECT DISTINCT userid FROM `owner` WHERE userid != '' AND userid IS NOT NULL AND type=0 AND list_id in (".implode(",",array_keys($imported_lists)).")");
	$numberOfOldUsers = count($numberOfOldUsers);

	$numberOfNewUsers = $newdb->query("SELECT COUNT(*) FROM user")->fetch();
	$numberOfNewUsers = $numberOfNewUsers["COUNT(*)"];

	echo "User import:\t";
	if ($numberOfNewUsers == $numberOfOldUsers) {
		echo "\033[42;38m Passed: $numberOfNewUsers users imported \n";
		wlog("all items imported ok");
	} else {
		echo "\033[41;38m Failed: (missing ".($numberOfOldUsers-$numberOfNewUsers)." users)\n";
		wlog(($numberOfOldUsers-$numberOfNewUsers)." users failed to import");
		$failed++;
	}
	echo "\033[0m";
	
	//! testing number of notes
	$numberOfOldNotes = $db->fetchAll("SELECT COUNT(*) FROM `sequence` INNER JOIN `readinglist` ON sequence.list_id = readinglist.list_id WHERE readinglist.year = 2009 AND readinglist.list_id in (".implode(",",array_keys($imported_lists)).") AND sequence.material_id is null");
	$numberOfOldNotes = $numberOfOldNotes[0]["COUNT(*)"];
	$numberOfNewNotes = $newdb->query("SELECT COUNT(*) FROM list WHERE list_id IS NOT NULL")->fetch();
	$numberOfNewNotes = $numberOfNewNotes["COUNT(*)"];
	echo "Note import:\t";
	if ($numberOfNewNotes == $numberOfOldNotes) {
		echo "\033[42;38m Passed $numberOfNewNotes notes imported \n";
		wlog("all items imported ok");
	} else {
		echo "\033[41;38m Failed (missing ".($numberOfOldNotes-$numberOfNewNotes)." notes)\n";
		wlog(($numberOfOldNotes-$numberOfNewNotes)." notes failed to import");
		$failed++;
	}
	echo "\033[0m";
	
	//! testing empty resources
	$numberOfEmptyResources = $newdb->query("SELECT COUNT(*) FROM resource INNER JOIN resource_data ON resource.id = resource_data.row_id WHERE resource_data.key='title' AND (resource_data.value = '' OR resource_data.value is null)")->fetch();
	$numberOfEmptyResources = $numberOfEmptyResources["COUNT(*)"];

	echo "Empty res':\t";
	if (!$numberOfEmptyResources) {
		echo "\033[42;38m Passed \n";
		wlog("no empty resources\n");
	} else {
		echo "\033[41;38m Failed ($numberOfEmptyResources empty resources)\n";
		wlog(($numberOfEmptyResources)." empty resources");
		$failed++;
	}
	echo "\033[0m\n";
	
	if (!$failed) {
		echo "all tests passed\n";
	} else {
		echo "$failed tests failed!\n";
	}
	//echo $notesC;
	echo "\n";
	
	fclose($log);
	fclose($logCombined);
		
	return true;