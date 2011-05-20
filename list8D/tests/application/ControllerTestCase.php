<?php

class ControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase {
  public $application;

  public function setUp() {

    $this->application = new Zend_Application(
      APPLICATION_ENV,
      APPLICATION_PATH . '/configs/application.ini'
    );

    global $application;
    $application = $this->application;

    $_SERVER['HTTP_HOST'] = 'test.host.list8d';

    $this->bootstrap = array($this, 'appBootstrap');
    parent::setUp();
  }

  public function tearDown() {
    Zend_Controller_Front::getInstance()->resetInstance();

    $this->resetRequest();
    $this->resetResponse();

    $this->request->setPost(array());
    $this->request->setQuery(array());
  }

  public function appBootstrap() {
    $this->application->bootstrap();
  }
}

