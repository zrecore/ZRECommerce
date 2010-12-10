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
class ReadControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
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
		parent::tearDown ();
	}

	public function testIndexAction() {
		// TODO Auto-generated FooControllerTest->testBarAction()
		$this->dispatch ( '/read/index' );
		$this->assertController ( 'read' );
		$this->assertAction('index');
	}

	public function testArticleAction() {
		$this->dispatch( '/read/article' );
		$this->assertController( 'read' );
		$this->assertAction('article');
	}

}