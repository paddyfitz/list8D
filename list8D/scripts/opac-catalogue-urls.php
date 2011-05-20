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

$res = new List8D_Model_ResourceMapper();
$resm = $res->getDbTable();
$sel = $resm->select( 'id' )->where( 'class = ?', 'List8D_Model_Resource_Book' );
$rows = $resm->fetchAll($sel);

try {
  $bootstrap->getPluginResource('db')->getDbAdapter()->beginTransaction();
  foreach( $rows as $id ) {
    $r = new List8D_Model_Resource();
    $res->find( $id['id'], $r );

    if( 'KentVoyager' == $r->getDataValue( 'namespace', false ) ) {
      echo $r->getDataValue( 'namespace', false ) . ": " . $id['id'] . " : " . $r->getDataValue( 'title' ) . "\n";
      $mu = $r->getDataValue( 'meta_url', false );
      $mu['Voyager']['url'] = 'https://catalogue.kent.ac.uk/Record/' . $r->getDataValue( 'Voyager_rcn' );
      $r->setData( 'KentVoyager_url', $mu['Voyager']['url'] );
      $r->setData( 'meta_url', $mu );
      $r->save();
    } else {
      echo $r->getDataValue( 'namespace', false ) . ": " . $id['id'] . " : ignoring\n";
    }
  }
  $bootstrap->getPluginResource('db')->getDbAdapter()->commit();
} catch( Exception $e ) {
  var_dump( $e );
  $bootstrap->getPluginResource('db')->getDbAdapter()->rollback();
}
