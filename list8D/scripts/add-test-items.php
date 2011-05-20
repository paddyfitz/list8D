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

    
    $lines = file('../../files/item-no.data');
    $items = array();

    foreach ($lines as $line_num => $line) {
	$arr = explode("\t", trim($line));
	if ($arr[1] == '') 
		continue;
	$items[] = $arr[0];

    }
    shuffle($items);
    $l = new List8D_Model_List;
    $listIDs = $l->getMapper()->fetchAllID();

    $meta = new List8D_Model_Metatron_KentVoyager;

    $count = 0;

    foreach($listIDs as $listID) {
	echo $listID . " ";
	$list = new List8D_Model_List();
	$list = $list->getById($listID);
	echo $list->getTitle() . "\n";
	for ($i=0; $i < rand(1,40); $i++) {
		$count++;
		if ($items[$count] == '')
		    continue;
		print "  $items[$count]  ";
		try {
		$data = $meta->getMetadata($items[$count]);
		} catch (Exception $e) {
			echo 'AN ERROR HAS OCCURED:' . PHP_EOL;
			echo $e->getMessage() . PHP_EOL;
			
			continue;
		}
		print $data['title'] . " (" . $data['authors'] .  ")\n";
		$resource = List8D_Model_Resource::getResource($data);
		$item = new List8D_Model_Item;
		$item->setResourceID($resource->getId());
		$item->setListID($listID);
		$item->save();
	}
    }
    
} catch (Exception $e) {
    echo 'AN ERROR HAS OCCURED:' . PHP_EOL;
    echo $e->getMessage() . PHP_EOL;
    echo $items[$count];
    return false;
}

// generally speaking, this script will be run from the command line
return true;
