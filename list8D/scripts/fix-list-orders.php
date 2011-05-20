<?php

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
    
  $db = $bootstrap->getPluginResource('db')->getDbAdapter();
  
  $results = $db->query("SELECT id FROM list")->fetchAll();


	$count = count($results);
 	$progress = new List8D_CliProgress($count);  
 	$listPeer = new List8D_Model_List();
 	$i=0;
  foreach ($results as $row) {
  	$i++;
  	$list = $listPeer->getById($row['id']);
  	$list->fixPositions();
		$progress->update();
		echo " ".	$i."/".$count;
  }
  echo "\n\n";
  
  
  
  
    	