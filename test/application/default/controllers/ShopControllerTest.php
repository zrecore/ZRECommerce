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
class ShopControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
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

	public function testAddAction() {
		$this->markTestIncomplete('Implement addAction() test.');
	}

	public function testCartAction() {
		$this->markTestIncomplete('Implement cartAction() test.');
	}

	public function testCheckoutAction() {
		$this->markTestIncomplete('Implement checkoutAction() test.');
	}

	public function testCheckoutCompleteAction() {
		$this->markTestIncomplete('Implement checkoutCompleteAction() test.');
	}

	public function testCheckoutEmptyAction() {
		$this->markTestIncomplete('Implement checkoutEmptyAction() test.');
	}

	public function testCheckoutErrorAction() {
		$this->markTestIncomplete('Implement checkoutErrorAction() test.');
	}

	public function testFlushAction() {
		$this->markTestIncomplete('Implement flushAction() test.');
	}

	public function testIndexAction() {
		// TODO Auto-generated FooControllerTest->testBarAction()
		$this->dispatch ( '/shop/index' );
		$this->assertController ( 'shop' );
		$this->assertAction('index');
	}

	public function testProductAction() {
		$this->markTestIncomplete('Implement productAction() test.');
	}

	public function testUpdateAction() {
		$this->markTestIncomplete('Implement updateAction() test.');
	}
}