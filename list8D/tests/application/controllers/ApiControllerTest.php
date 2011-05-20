<?php

require_once TESTS_PATH . '/ControllerTestCase.php';


class ApiControllerTest extends ControllerTestCase
{
    public function testSetListJson() {
      $this->markTestSkipped();
	    $this->request->setMethod('POST')
	          ->setPost(array(
	              'method' => 'setList',
	              'key' => '123456789',
	              'title' => 'test list',
	              'code' => 'ABC123',
	              'private_notes' => 'blah blah',
								'core_text' => '1',
								'recommended_for_purchase' => '1',
								'start' => '2009-11-17 00:00:00',
								'end' => '2010-11-17 00:00:00',
								'json' => true,
	          ));
	    
	    // ugly hack because Server.php assumes $_REQUEST not $_POST
	    $_REQUEST = $this->request->getPost();
	    
	   	// call the action
	    $this->dispatch('/api');
	    
	    // get the response and convert to object for easier testing
	    $response = json_decode($this->getResponse()->getBody());
	    // tests
	    $this->assertEquals('success', $response->response->status);
    }
    
    public function testSetListXML() {

      $this->markTestSkipped();

	    $this->request->setMethod('POST')
	          ->setPost(array(
	              'method' => 'setList',
	              'key' => '123456789',
	              'title' => 'test list',
	              'code' => 'ABC123',
	              'private_notes' => 'blah blah',
								'core_text' => '1',
								'recommended_for_purchase' => '1',
								'start' => '2009-11-17 00:00:00',
								'end' => '2010-11-17 00:00:00',
	          ));
	    
	    // ugly hack because Server.php assumes $_REQUEST not $_POST
	    $_REQUEST = $this->request->getPost();
	    
	   	// call the action
	    $this->dispatch('/api');
	    
	    // get the response and convert to object for easier testing
	    $response = simplexml_load_string($this->getResponse()->getBody());
	    // tests
	    $this->assertEquals('success', (String)$response->status);
    }
    
}

