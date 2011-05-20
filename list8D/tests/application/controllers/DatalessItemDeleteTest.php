<?php

require_once TESTS_PATH . '/ControllerTestCase.php';


class DatalessItemDeleteTest extends ControllerTestCase {

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

  public function testDatalessItemDelete() {

    $uri = '/item/remove/id/1/itemid/173096';
    $_SERVER['REQUEST_URI'] = $uri;
    $_SERVER['HTTP_USER_AGENT'] = 'bananas';
    $_SERVER['REMOTE_USER'] = 'admin';

    $this->request->setMethod('GET')
      ->setQuery(array('confirmed' => 'remove'));

    $this->dispatch( $uri );
    $this->assertController('item');
    $this->assertAction('remove');

    $this->assertRedirectTo('/list/view/id/1');

    $rs = $this->connection->query( 'select * from change_log where row_id = 173096' );
    $this->assertEquals( 1, count($rs->fetchAll()) );
    foreach( $rs->fetchAll() as $row ) {
      $this->assertEquals( 'delete', $row['action'] );
      $this->assertEquals( 'item', $row['table'] );
      $this->assertEquals( 173096, $row['row_id'] );
      $this->assertEquals( 547, $row['user'] );
    }

    $rs = $this->connection->query( 'select * from item where id = 173096' );
    $this->assertEquals( 0, count( $rs->fetchAll() ) );
  }

}

