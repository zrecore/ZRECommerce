<?php
require_once 'Zend/Application.php';
require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';

class OrdersControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		 // Assign and instantiate in one step:
        
        if (!defined('BASE_PATH')) define('BASE_PATH', realpath(dirname(__FILE__) . '/../../../../'));
		if (!defined('APPLICATION_PATH'))  define('APPLICATION_PATH', BASE_PATH . '/application');
		
		// Include path
		set_include_path(
		    BASE_PATH . '/library'
		    . PATH_SEPARATOR . get_include_path()
		);
		
		// Define application environment
		if (!defined('APPLICATION_ENV')) define('APPLICATION_ENV', 'test');
		defined('APPLICATION_ENV')
		    || define('APPLICATION_ENV',
		              (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV')
		                                         : 'production'));
		
		// Zend_Application
		require_once 'Zend/Application.php';
		
		$application = new Zend_Application(
		    APPLICATION_ENV,
		    APPLICATION_PATH . '/configs/application.ini'
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
		$this->dispatch( '/orders/process' );
		$this->assertController( 'orders' );
		Zend_Session::destroy();
	}
	
}