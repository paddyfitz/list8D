<?php
	// Source database settings
	$old_db_settings = array(
  	  'host'     => '127.0.0.1',
  	  'username' => 'list8D',
  	  'password' => 'list8D_pwd',
  	  'dbname'   => 'readingListOld'
    );
	
	// Must be run from the cli
	if(!empty($_SERVER['HTTP_HOST'])) {
		echo "Sorry this script must be run from the command line.";
		die;
	}
	
	// Increase memory limit as this script may take a while to run.
	ini_set('memory_limit','256M');
	//error_reporting(E_ERROR);
	
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
	define('APPLICATION_ENV', 'development');
	
	// Initialize Zend_Application
	$application = new Zend_Application(
										APPLICATION_ENV,
										APPLICATION_PATH . '/configs/application.ini'
	);
	
	// Initialize and retrieve DB resource
	$bootstrap = $application->getBootstrap();
	$bootstrap->bootstrap('db');
	
	// Get metatrons
	$conf = $application->getOptions();
	// $metatronList = $conf['list8d']['metatrons'];
	$metatronList = array('List8D_Model_Metatron_KentVoyager', 'List8D_Model_Metatron_Amazon');
	
	// Connect to old db
	$db = Zend_Db::factory('Pdo_Mysql', $old_db_settings);
