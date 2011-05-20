<?php

/*
 * 3 step process:
 * 1) Create Institution
 * 2) Create Faculties
 * 3) Create Departments & Course codes.
 */



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

$getopt = new Zend_Console_Getopt(array(
	"test|t" => "test the script, creating a visual output of the tree to be created",
	"verbose|v" => "provide verbose output",
	"help|h"	=> "show this help message",
));


try {
	$getopt->parse();
} catch (Zend_Console_Getopt_Exception $e) {
	echo $e->getUsageMessage();
	return false;
}

if($getopt->getOption('h')) {
	echo $getopt->getUsageMessage();
	return true;
}

$test = $getopt->getOption("t");
$verbose = $getopt->getOption("v");




//set up the environment.
defined('APPLICATION_ENV')
	|| define('APPLICATION_ENV', (null === $env) ? 'development' : $env);

defined('YEAR')
	|| define('YEAR', (null === $year) ? (date('Y')+1) : $year);

// Initialize Zend_Application
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

// Initialize and retrieve DB resource
$bootstrap = $application->getBootstrap();
//$bootstrap->bootstrap('db');
$bootstrap->bootstrap('registry');

//no to go through the lists and save the tags
$listPier = new List8D_Model_List();

//get every top level list
$idRowset = $listPier->getMapper()->getDbTable()->fetchAll(
	$listPier->getMapper()->getDbTable()->select("id")->where("list_id IS NULL")
);

if($verbose)
	print "I have found ".count($idRowset)." lists".PHP_EOL;

foreach($idRowset as $id) {
	$list = $listPier->getById($id->id);

	if($verbose)
		print "List: {$list->getDataValue("title")}".PHP_EOL;

	$departmentName = $list->getDataValue("department");
	$facultyName = $list->getDataValue("faculty");
	$courseCode = $list->getDataValue("code");
	$institutionName = $list->getDataValue("institution");
	if(empty($departmentName) || empty($facultyName) || empty($courseCode) || empty($institutionName)) {
		continue;
	}

	//set institution
	$institution = new List8D_Model_Tag();
	$institution->setNamespace("institution")->setTagName($institutionName)->save();
	if($verbose) {
		print "Tags:".PHP_EOL;
		print "\t * {$institution->getNamespace()}:{$institution->getTagName()}".PHP_EOL;
	}
	
	//set up faculty tag
	$faculty = new List8D_Model_Tag();
	$faculty->setNamespace("faculty")->setTagName($facultyName)->setParentId($institution->getId())->save();
	if($verbose) {
		print "\t  * {$faculty->getNamespace()}:{$faculty->getTagName()}".PHP_EOL;
	}

	//set up department tag
	$department = new List8D_Model_Tag();
	$department->setNamespace("department")->setTagName($departmentName)->setParentId($faculty->getId())->save();
	if($verbose)
		print "\t   * {$department->getNamespace()}:{$department->getTagName()}".PHP_EOL;

	//set up code tag
	$course = new List8D_Model_Tag();
	$course->setNamespace("module")->setTagName($courseCode)->setParentId($department->getId())->save();
	if($verbose)
		print "\t    * {$course->getNamespace()}:{$course->getTagName()}".PHP_EOL;

	//save to list
	$tagMap = new List8D_Model_TagMap();
	$tagMap->setTagId($course->getId())->setListId($list->getId())->save();

	if($progressIndex == 4) {
		$progressIndex = 0;
	}
	print $progess[$progressIndex]."\r";
	$progressIndex++;
}
?>
