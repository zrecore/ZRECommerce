<?
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
class IndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase 
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
		Zend_Session::destroy();
		parent::tearDown ();
	}
	
	
	/**
	 * Tests FooController->barAction()
	 */
	public function testIndexAction() {
		// TODO Auto-generated FooControllerTest->testBarAction()
		$this->dispatch ( '/index/index' );
		$this->assertController ( 'index' );
		$this->assertAction ( 'index' );
		Zend_Session::destroy();
	}
}
?>
