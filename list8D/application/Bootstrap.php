<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
		public function __construct($application)
    {
        parent::__construct($application);
        date_default_timezone_set('UTC');
        
    }	
    
    protected function _initAutoload()
    {
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'List8D',
            'basePath'  => dirname(__FILE__),
        ));
        $autoloader->addResourceType ( 'reports', 'reports', 'Report');
        $autoloader->addResourceType ( 'references', 'references', 'Reference');
        
 				$loader = Zend_Loader_Autoloader::getInstance();
 				$loader->registerNamespace("List8D");

        return $autoloader;

    }
		
		protected function _initRoutes()
    {
    	/*
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
      }
*/
    	
	    
    }
    
    protected function _initDoctype()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_TRANSITIONAL');
    }

    protected function _initRegistry()
    {
	    $this->bootstrap('db');
	    Zend_Registry::set('dbResource', $this->getPluginResource('db'));
	    $themeSettings = List8D_Config_Ini::getThemeSettings();
	    $refClass = "List8D_Reference_".$themeSettings->referenceStyle;
	    Zend_Registry::set('reference', new $refClass );

    } 


}

