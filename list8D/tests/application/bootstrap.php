<?php

//error_reporting( E_ALL | E_STRICT );
ini_set('display_startup_errors', 1);
ini_set('display_errors', 0);
date_default_timezone_set('Europe/London');

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));
define('APPLICATION_ENV', 'testing');
define('TESTS_PATH', realpath(dirname(__FILE__)));
define("LIST8D_APPLICATION",'admin');


// change this to your domain
//$_SERVER['SERVER_NAME'] = 'http://list8d.local';

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

require_once "Zend/Loader/Autoloader.php";
require_once "Zend/Application/Module/Autoloader.php";

$autoloader = new Zend_Application_Module_Autoloader(array(
    'namespace' => 'List8D',
    'basePath'  => dirname(__FILE__),
));

$loader = Zend_Loader_Autoloader::getInstance();
$loader->registerNamespace("List8D");
$loader->registerNamespace("PHPUnit");



Zend_Session::$_unitTestEnabled = true;
Zend_Session::start();
