<?php 

	// include db settings
 	require("import-old-data-settings.php");
 	
 	// Must be run from the cli
	if(!empty($_SERVER['HTTP_HOST'])) {
		echo "Sorry this script must be run from the command line.";
		wlog("Sorry this script must be run from the command line.");
		die;
	}
	
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
	
	
	
 	define('APPLICATION_ENV','development');
 	
 	// Initialize Zend_Application
	$application = new Zend_Application(
										APPLICATION_ENV,
										APPLICATION_PATH . '/configs/application.ini'
	);
	$conf = $application->getOptions();
	// Initialize and retrieve DB resource
	$bootstrap = $application->getBootstrap();
	$bootstrap->bootstrap('db');
    Zend_Registry::set('dbResource', $bootstrap->getPluginResource('db'));
  
  // Connect to old db
	$db = Zend_Db::factory('Pdo_Mysql', $old_db_settings);

	
	$new_db = Zend_Db::factory('Pdo_Mysql', array(
		'host'     => $conf['resources']['db']['params']['host'],
  	'username' => $conf['resources']['db']['params']['username'],
  	'password' => $conf['resources']['db']['params']['password'],
  	'dbname'   => $conf['resources']['db']['params']['dbname']
	));
	
	// create list tags and assign to lists
	// foreach list 
		// if the module code doesnt exist as a tag
			// create it
		// add it to the list
		
		// foreach users
		
			// if user doesnt exist
				// create user
				
			// if type 1 (accademic) and role undefined or role guest and not role deptAdmin
				// give user accademic role
			// else if type 2 or 3 and role not sysAdmin
				// give user librarian role
				
			// add list tag to user
			
			// if user is type 0 (convener)
				// add user to conveener list data
	
//	$users = $db->fetchAll("SELECT `owner`.*, `readinglist`.* FROM `owner` INNER JOIN `readinglist` ON readinglist.list_id = owner.list_id");	

		$lists = $new_db->fetchAll("SELECT id FROM list WHERE list_id IS NULL");
		echo "starting import\n";
		// foreach list
		foreach ($lists as $list_row) {
			// if the module code tag doesnt exist as a tag
			$tag = new List8D_Model_Tag();
			$list = new List8D_Model_List();
			$list = $list->getById($list_row['id']);
			
			echo "importing list {$list->getId()}\n";
			
			if ($tag->findTag('module',$list->getDataValue("code"))) {
			
			} else {
				// create it
				$tag->setTagName($list->getDataValue("code"))
					->setNamespace('module')
					->save();
			}
			
			// add it to the list
			$tagMap = new List8D_Model_TagMap();
			$tagMap->setTagId($tag->getId())
				->setListId($list->getId())
				->save();
			
			// foreach of the users for a list

			$users = $db->fetchAll("SELECT `owner`.name, `owner`.userid, `owner`.type, `owner`.email FROM owner INNER JOIN `readinglist` ON owner.list_id = readinglist.list_id where readinglist.module_code = '{$list->getDataValue('code')}' AND readinglist.year='2009'");
			foreach ($users as $user) {
				
				
				$existing = $new_db->fetchAll("SELECT * FROM user WHERE `login`='{$user['userid']}'");
				
				// if user doesnt exist
				if (!count($existing)) {
					
					// create user
					$new_db->query("INSERT INTO user (login,displayname,email,institutionid,created) VALUES ('{$user['userid']}','{$user['name']}','{$user['email']}',1,'".date ("Y-m-d H:i:s")."')");
					$existing = $new_db->fetchAll("SELECT * FROM user WHERE login='{$user['userid']}'");	
					
				} 
				$existing = $existing[0];
				echo "adding user {$existing['login']} to list {$list->getId()}\n";				
				// if type 1 (accademic) and role undefined or role guest and not role deptAdmin
				
					if ($user['userid']=='aac')
						echo '\n\nhello\n\n';
				if (($user['type'] == 1 || $user['type'] == 0) && ($existing['role'] == "" || !isset($existing['role']) || $existing['role']=='guest')) {
					// give user accademic role
					$new_db->query("UPDATE user SET `role`='academic' WHERE `login`='{$user['userid']}'");
					
				} 
				// else if type 2 or 3 and role not sysAdmin
				elseif (($user['type'] == 2 || $user['type'] == 3) && $existing['role'] != "sysAdmin") {
					// give user librarian role
					$new_db->query("UPDATE user SET role='librarian' WHERE login='{$user['userid']}'");					
				}
				
				// if user is type 0 (convener)
				if ($user['type'] == 0) {
					
					// get current convener
					$row = $new_db->query("select `value` from list_data where id={$list->getId()} and `key`='convener'")->fetchAll();
					if (count($row)) {
						$conveners = unserialize($row[0]['value']);
					} else {
						$conveners = array();
					}
					
					$conveners[] = $existing['id'];
					
					// add user to convener list data
					$new_db->query("REPLACE INTO list_data (`row_id`,`key`,`value`) VALUES ({$list->getId()},'convener','". serialize($conveners) ."')");
					
				}
				
				// add list tag to user
				$userMap = new List8D_Model_UserMap();
				try {
					$userMap->setTagId($tag->getId())
					->setUserId($existing['id'])
					->save();
				} catch (Exception $e) {}
				
			}
		
		}
		echo "finished import\n";