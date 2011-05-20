<?php

require_once TESTS_PATH . '/ControllerTestCase.php';


class DuplicateItemTest extends ControllerTestCase {

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

  public function testDuplicateItem() {
    $uri = '/list/duplicate/id/1';//destination//admin/list/view/id/1/copyitem/171212';
    $_SERVER['REQUEST_URI'] = $uri;
    $_SERVER['HTTP_USER_AGENT'] = 'bananas';
    $_SERVER['REMOTE_USER'] = 'admin';

    $this->request->setMethod('GET')
      ->setQuery(array('copyitem' => 171212));

    $this->dispatch( $uri );
    $this->assertController('list');
    $this->assertAction('duplicate');

    $this->assertRedirectTo('/list/view/id/1#item_171213');

    $rs = $this->connection->query( 'select * from change_log where row_id = 171213' );
    $this->assertEquals( 1, count($rs->fetchAll()) );
    foreach( $rs->fetchAll() as $row ) {
      $this->assertEquals( 'duplicate', $row['action'] );
      $this->assertEquals( 'item', $row['table'] );
      $this->assertEquals( 171213, $row['row_id'] );
      $this->assertEquals( 547, $row['user'] );
      $this->assertEquals( 171212, $row['value_from'] );
    }

    $rs = $this->connection->query( 'select * from item where id = 171213' );
    $this->assertEquals( 1, count($rs->fetchAll()) );
    foreach( $rs->fetchAll() as $row ) {
      $this->assertEquals( 1, $row['list_id'] );
      $this->assertEquals( 38727, $row['resource_id'] );
    }

    $orig = $this->connection->query( 'select `key`, `value` from item_data where row_id = 171212 order by `key`' );
    $copy = $this->connection->query( 'select `key`, `value` from item_data where row_id = 171213 order by `key`' );
    $this->assertEquals( $orig->fetchAll(), $copy->fetchAll() );

  }

}

