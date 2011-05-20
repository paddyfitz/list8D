<?php

require_once TESTS_PATH . '/ControllerTestCase.php';

class CreateListTest extends ControllerTestCase {

  public function setUp() {
    parent::setUp();

    $options = $this->application->getOptions();

    Zend_Registry::set('dbResource', $this->application->getBootstrap()->getPluginResource('db'));
    $connection = new Zend_Test_PHPUnit_Db_Connection( Zend_Registry::get('dbResource')->getDbAdapter(), $options['resources']['db']['params']['dbname'] );
    $this->connection = $connection->getConnection();

    $seed = dirname(__FILE__) . '/../../fixtures/' . strtolower( __CLASS__ ) . '.xml';
    $tester = new Zend_Test_PHPUnit_Db_SimpleTester( $connection );
    $fixture = new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet( $seed );
    $tester->setUpDatabase( $fixture );
    $this->tester = $tester;
  }

  public function testCreateNewList() {

    $uri = '/list/create';
    $_SERVER['REQUEST_URI'] = $uri;
    $_SERVER['HTTP_USER_AGENT'] = 'bananas';
    $_SERVER['REMOTE_USER'] = 'admin';

    $this->request->setMethod('GET');
    $this->dispatch( $uri );
    $this->assertController('list');
    $this->assertAction('create');

    $this->assertQuery( '#csrf' );
    $dq = new Zend_Dom_Query( $this->response->outputBody() );
    $csrf = $dq->query('#csrf')->current()->getAttribute('value');

    $this->resetRequest()->resetResponse();

    $td = array(
      'title' => 'title-d',
      'code' => 'code-d',
      'year' => '2010',
      'is_published' => '1',
      'department' => 'department-d',
      'start' => '2010-01-01 00:00:00',
      'end' => '2010-01-02 00:00:00',
      'public_notes' => 'public_notes-d',
      'private_notes' => 'private_notes-d',
      'csrf' => $csrf,
    );

    $this->request->setMethod('POST')->setPost( $td );
    $this->dispatch( $uri );
    $this->assertController('list');
    $this->assertAction('create');

    $this->assertRedirectTo('/list/view/id/1');

    $rs = $this->connection->query( 'select * from list' )->fetchAll();
    $this->assertEquals( 1, count($rs) );

		$list = new List8D_Model_List();
		$list = $list->getById( 1 );
    $d = $list->getData();

    $tdd = array_diff( $td, array( 'csrf' => $csrf ) );
    foreach( $tdd as $k => $v ) {
      $this->assertTrue( isset( $d[$k]['value'] ), "'$k' doesnt seem to have a value in the new lists data when it should have" );
      $this->assertEquals( $v, $d[$k]['value'] );
    }

  }

}

