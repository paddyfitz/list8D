<?php 
	ini_set('memory_limit','512M');
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
  
  

	$args = getopt('y:');
	
	// Debug -d
	if(!isset($args['y'])) {
		echo "you must define a year\n"; exit;
	} 
	
	$year = $args['y'];
	
	$listPeer = new List8D_Model_List();
	echo "\nLoading lists for year $year\n\n";
	$lists = $listPeer->findByData(array(
		'year'=>$year,
	),true);
	echo "\nLists loaded\n\n";
	$listCount = count($lists);
	echo "\nPublishing lists for year $year ($listCount lists)\n\n";
	$progress = new List8D_CliProgress(count($lists));
	$i=0;
	// forevery list in this year
	$bootstrap->getPluginResource('db')->getDbAdapter()->beginTransaction();
	try {
	  foreach($lists as $list) {
	
	  	// find the previous year for this list
	  	$previousYear = $listPeer->findByData(array(
	  		'year'=>$year-1,
	  		'code'=>$list->getDataValue('code'),
	  	),false,2);
	  	
	  	
	  	// if we were unable to find a previous year or a previous year list was found and it was published
	  	if($previousYear===false || (is_object($previousYear) && $previousYear->getDataValue('is_published'))) {
	  		$i++;
	  		// set this years list to published to true
	  		$list->setData("is_published",true);
	  		$list->setData("was_auto_published",true);
	  		$list->save();
	  	} else {
	  		$list->setData("was_auto_published",false);
	  	}
	  	$previousYear = null;
	  	$list = null;
	  	$progress->update();
	  	echo "   $i/$listCount ".(memory_get_usage()/1000000)."Mb";
	  }
	  $bootstrap->getPluginResource('db')->getDbAdapter()->commit();
	} catch (Exception $e) {
		$bootstrap->getPluginResource('db')->getDbAdapter()->rollBack();
    echo $e->getMessage();
	}
	echo "\n\n";
