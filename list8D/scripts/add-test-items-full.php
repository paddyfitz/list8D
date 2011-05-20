<?php
	
	/* Test data importer
	 * $Id: add-test-items-full.php 713 2010-01-26 20:00:30Z msaspence $
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
	
	$conf = $application->getOptions();
	// $metatronList = $conf['list8d']['metatrons'];
	$metatronList = array('List8D_Model_Metatron_KentVoyager', 'List8D_Model_Metatron_Amazon');
	$resourceType = 'List8D_Model_Resource_Book';
	
	try {
		echo "Loading data...";
		
		$lines = file('../../files/item-no.data');
		$items = array();
		
		foreach ($lines as $line_num => $line) {
			if($line_num % 1000 == 0) echo ".";
			$arr = explode("\t", trim($line));
			foreach ($arr as $a) {
				if(strlen($a) == 10) $items[] = $a;
			}
		}
		echo " shuffling...";
		shuffle($items);

		echo "\nLoaded " . count ($items) . " items\n";

		$l = new List8D_Model_List;
		$listIDs = $l->getMapper()->fetchAllID();
		
		// $meta = new List8D_Model_Metatron_KentVoyager;
		
		$count = 0;
		foreach($listIDs as $listID) {
			echo "[ $listID ]\n";
			$list = new List8D_Model_List();
			$list = $list->getById($listID);
			echo $list->getTitle() . "\n";
			for ($i=0; $i < rand(1,40); $i++) {
				$count++;
				if ($items[$count] == '')
					continue;
				
				print " -> $items[$count]: ";
				$data = null;
				$ndata = null; // used for merging
				$resource = null;
				
				foreach ($metatronList as $metatron) {
					$ns = call_user_func(array($metatron, "getNamespace"));
					$metatronInstance = new $metatron($conf['list8d'][$ns]);
					
					try {
						if(empty($data)) {
							echo "*[$ns] "; 
							$data = $metatronInstance->getAdditionalMetadata($resourceType, array('isbn10'=>$items[$count]));
							//var_dump($data);
							$resource = List8D_Model_Resource::getResource($data, $resourceType);
						} else {
							echo "[$ns] "; 
							$ndata = $metatronInstance->getAdditionalMetadata($resourceType, $data);
							//var_dump($ndata);
							$resource->setAdditionalMetadata($ndata);
						}
					} catch (Exception $e) {
						echo "     *** AN ERROR HAS OCCURED IN $ns: ";
						echo $e->getMessage() . PHP_EOL;
						
						$ndata = null;
						
						continue;
					}
					
				}
				if(empty($data)) continue;
				
				print "  ->  " . $data['title'] . " (" . $data['authors'] .  "). Saving...";
				$item = new List8D_Model_Item;
				$item->setResourceID($resource->getId());
				$item->setListID($listID);
				$item->save();
				echo " ok\n";
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
