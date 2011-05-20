<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

define('LIST8D_END',"front");

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

define("LIST8D_APPLICATION",'front');

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php'; 

/** misc functions */
include_once '../library/misc.php'; 

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV, 
    APPLICATION_PATH . '/configs/application.ini'
);

$front = Zend_Controller_Front::getInstance();
$router = $front->getRouter();
if (LIST8D_APPLICATION == 'front') {
  $router->addRoute(
		'list',
		new Zend_Controller_Router_Route(
			":action/*",
			array(
				'controller'=>'front',
				'action'=>'index'
			)
		)
	);
	$router->addRoute(
		'lti',
		new Zend_Controller_Router_Route(
			"/lti/:action/*",
			array(
				'controller'=>'lti',
				'action'=>'index'
			)
		)
	);
}

$frontController = Zend_Controller_Front::getInstance();
$frontController->setDefaultControllerName("front");

$application->bootstrap()
            ->run();
            

            
            
