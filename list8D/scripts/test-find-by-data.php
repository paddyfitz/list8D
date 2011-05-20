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
$legacyLoader = new Zend_Loader_Autoloader_Resource(array(
  'basePath'  => dirname(__FILE__).'/LegayModel',
  'namespace' => 'LegacyModel',
));
define(APPLICATION_ENV, 'development');

// Initialize Zend_Application
$application = new Zend_Application(
  								APPLICATION_ENV,
  								APPLICATION_PATH . '/configs/application.ini'
);

// Initialize and retrieve DB resource
$bootstrap = $application->getBootstrap();
$bootstrap->bootstrap('db');

$list = new List8D_Model_List();
var_dump($list->findByData(array('code'=>'PO505'),true,2));