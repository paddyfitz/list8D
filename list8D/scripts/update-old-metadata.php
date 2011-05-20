<?php
	
	// Initialize the application path and autoloading
	echo "initialising...\n";
	defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
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
																   
	//! Enviroment
 	define('APPLICATION_ENV','staging');
 	
	// Initialize Zend_Application
	$application = new Zend_Application(
										APPLICATION_ENV,
										APPLICATION_PATH . '/configs/application.ini'
	);
	
	echo "retrieving database...\n";
	
	// Initialize and retrieve DB resource
	$bootstrap = $application->getBootstrap();
	$bootstrap->bootstrap('db');
    Zend_Registry::set('dbResource', $bootstrap->getPluginResource('db'));
    
	$db = $bootstrap->getPluginResource('db')->getDbAdapter();
  	
	echo "updating manually flagged items...\n";
	
	//first get the manual ones
	$results = $db->query("SELECT row_id from item_data where `key` = 'needs_updating' and value = 's:1:\"1\";'")->fetchAll();
	
	$count = count($results);
	echo "results obtained... $count resource to update\n";
	
	//$progress = new List8D_CliProgress($count);
	$itemPeer = new List8D_Model_Item();
	$i = 1;
	foreach($results as $row){
		try{
			$item = $itemPeer->getById($row['row_id']);
			$item->updateResource();
			$item->setData("needs_updating", false);
			$item->getResource()->save();
			//$progress->update();
			echo " ".$i."/".$count;
			$i++;
		}
		catch(Exception $e){
			echo " exception occurred with item ".$i."/".$count;
		}
	}
	//first get the manual ones
	$results = $db->query("SELECT row_id from resource_data where `key` = 'needs_updating' and value = 's:1:\"1\";'")->fetchAll();
	
	$count = count($results);
	echo "resources needing update obtained... $count resource to update\n";
	
	//$progress = new List8D_CliProgress($count);
	$itemPeer = new List8D_Model_Resource();
	$i = 1;
	foreach($results as $row){
		
		$item = $itemPeer->getById($row['row_id']);
		$item->update();
		$item->setData("needs_updating", false);
		$item->save();
		//$progress->update();
		echo " ".$i."/".$count;
		$i++;
	}
	
	echo "\n\n";
	echo "updating old items...\n";
	//now run on the oldest 100
	$results = $db->query("SELECT id from item order by updated asc limit 100");
	//$count = count($results);
	//$progress = new List8D_CliProgress($count);
	$itemPeer = new List8D_Model_Item();
	$i = 1;
	foreach($results as $row){
		try{
			$item = $itemPeer->getById($row['id']);
			$item->updateResource();
			//to update updated date
			$item->setData("needs_updating", false);
			$item->getResource()->save();
			//$progress->update();
			echo " ".$i." done\n";
		}
		catch(Exception $e){}
		$i++;
	}
	echo "\n\n";   

	
	?>