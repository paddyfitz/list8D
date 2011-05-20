<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$undone = array();

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

/*$resourceLoader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'List8D_',
            'basePath'  => dirname(__FILE__).'/../application',
        ));
*/
//set up the option processor.
$getopt = new Zend_Console_Getopt(array(
	"year|y=s"			=> "Search for a specific year, if not specified will use the current year.",
	"verbose|v"			=> "Display verbose output",
	"env|e=s"		=> "Specify the application environment to use",
	"help|h"				=> "Display this help message",
));

try {
	$getopt->parse();
} catch (Zend_Console_Getopt_Exception $e) {
	echo $e->getUsageMessage();
	return false;
}

$year = $getopt->getOption('y');
$verbose = $getopt->getOption('v');
$env = $getopt->getOption('e');

if($year === NULL) {
	echo "*** You must specify a year option.\n";
	echo $getopt->getUsageMessage();
	return false;
}

//set up the environment.
defined('APPLICATION_ENV')
	|| define('APPLICATION_ENV', (null === $env) ? 'development' : $env);

// Initialize Zend_Application
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

// Initialize and retrieve DB resource
$bootstrap = $application->getBootstrap();
$bootstrap->bootstrap('autoload');
$bootstrap->bootstrap('registry');
$db = $bootstrap->getResource('db');

include(APPLICATION_PATH."/../library/List8D/console.php");
include(APPLICATION_PATH."/../library/List8D/CliProgress.php");

//var_dump($year);
//exit;

if($verbose) {
	print "Initialisation complete.\nUsing environment '".APPLICATION_ENV."'\n";
}

//create the empty pier.
$list = new List8D_Model_List();

if($verbose)
	print "List pier created\n";

$select = $db->select()
		->from(array('l' => 'list_data'), 'row_id')
//		->distinct()
		->where("l.key = 'year' AND l.value = '".serialize($year)."'");
		//->limit(200);

if($verbose)
	print "Select statement prepared\n{$select}\n";
	

$searchResults = $db->fetchAll($select);
if($verbose)
	print "Result set fetched, ".count($searchResults)." results returned\n";

if(!$verbose)
	$progress = new List8D_CliProgress(count($searchResults));

$soFar = 1; $total = count($searchResults);

//time to loop through and duplicate.
foreach($searchResults as $listId) {

	if(!$verbose) {
		print " ({$soFar} of {$total}) ";
		$soFar++;
		$progress->update();
	}

	$list = $list->getById($listId['row_id']);
	if($verbose) {
		print "* Duplicating list: {$list->getTitle()}.....";
	}

	//check to see if the list already exists.
	$preExistingSelect = $db->select()
		->from(array('l' => 'list_data'), array("count(*)", "row_id"))
		->where("l.key = 'sds_id' AND l.value = '".serialize((string)$list->getDataValue("sds_id"))."'")
		->orWhere("l.key = 'year' AND l.value = '".serialize((string)($year+1))."'")
		->orWhere("l.key = 'code' AND l.value = '".serialize($list->getCode())."'")
		->group("l.row_id")
		->having("count(*) >= 2");
		
	$preExistingResults = $db->fetchAll($preExistingSelect);
	if(count($preExistingResults) > 1) {
		//there is multiple results in the db, ignore list and carry on
		$undone[] = "id: {$list->getId()}, {$list->getTitle()}";
		continue;
	}

	if($duplicate = $list->duplicate()) {
				$duplicate->setData("year", $year+1);
				//date format YYYY-MM-DD HH:MM:SS
				$duplicate->setEnd(date("Y-m-d H:i:s", mktime(0, 0, 0, 7, 1, $year+2)));
				$duplicate->setStart(date("Y-m-d H:i:s", mktime(0, 0, 0, 9, 1, $year+1)));
				$duplicate->setData("student_numbers", 0);
				$duplicate->setIsPublished(FALSE);
				$duplicate->save($duplicate, true);

				//duplicate tags..?
				$tags = $list->getTagIds();
				foreach($tags as $tag) {
					$tagmap = new List8D_Model_TagMap();
					$tagmap->setTagId($tag)
						->setListId($duplicate->getId())
						->save();
				}
				if($verbose)
					print "done.";
	}
	if($verbose)
		print "\n";
	
		

}

if($verbose)
	print "Finished duplications\n";

print "\nThe following lists were not copied as it appears their duplicate already exists:\n";
foreach($undone as $l) {
	print "\t* {$l}\n";
}
?>
