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
//$autoloader->registerNamespace('List8D_');

$resourceLoader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'List8D_',
            'basePath'  => dirname(__FILE__).'/../application',
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

//print get_include_path();

try {
    $lines = file('../../files/sample-lists.data');

    foreach ($lines as $line_num => $line) {
	    #echo "Line #<b>{$line_num}</b> : " . htmlspecialchars($line) . "<br />\n";
	$arr = explode("\t", trim($line));
	$list = new List8D_Model_List();
	$list->setStart("2009-09-01 00:00:00");
	$list->setEnd("2009-12-31 00:00:00");
	$list->setData("title", $arr[2]);
	$list->setData("code", $arr[0]);
	$list->setData("is_published", 0);

	print "Saving to the database: $arr[0] - $arr[2]... ";
	$list->save();
	print "saved \n";

    }
    
} catch (Exception $e) {
    echo 'AN ERROR HAS OCCURED:' . PHP_EOL;
    echo $e->getMessage() . PHP_EOL;
    return false;
}

// generally speaking, this script will be run from the command line
return true;
