<?php
/**
 * IndexControllerTest - Test the default index controller
 * 
 * @author
 * @version 
 */
require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';
require_once realpath(dirname(__FILE__) . '/../../../../application/bootstrap.php');

/**
 * IndexController Test Case
 */
class OrdersControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		
		Bootstrap::setupPaths();
		
		$application = new Zend_Application(
		    APPLICATION_ENV,
		    APPLICATION_PATH . '/settings/application.ini'
		);
		
		$this->bootstrap = $application;
		
		parent::setUp ();
	}
	
	/**
	 * Prepares the environment before running a test.
	 */
	public function appBootstrap() {
		
		$this->frontController->registerPlugin ( new Bootstrap( 'test' ) );
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated FooControllerTest::tearDown()
		Zend_Session::destroy();
		parent::tearDown ();
	}
	
	public function testIndexAction() {
		// TODO Auto-generated FooControllerTest->testBarAction()
		$this->dispatch ( '/orders/index' );
		$this->assertController ( 'orders' );
		Zend_Session::destroy();
	}
	
	public function testProcessAction() {
		$this->markTestIncomplete('Need to finish setting up /orders/process unit test.');
		$this->dispatch( '/orders/process' );
		$this->assertController( 'orders' );
		Zend_Session::destroy();
	}
	
}