<?
/**
 * IndexControllerTest - Test the default index controller
 * 
 * @author
 * @version 
 */
require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';
require_once 'application/Initializer.php';

/**
 * IndexController Test Case
 */
class IndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase {

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->bootstrap = array ($this, 'appBootstrap' );
		parent::setUp ();
		// TODO Auto-generated FooControllerTest::setUp()		
	}

	/**
	 * Prepares the environment before running a test.
	 */
	public function appBootstrap() {
		$this->frontController->registerPlugin ( new Initializer( 'test' ) );
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated FooControllerTest::tearDown()
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests FooController->barAction()
	 */
	public function testIndexAction() {
		// TODO Auto-generated FooControllerTest->testBarAction()
		$this->dispatch ( '/index/index' );
		$this->assertController ( 'index' );
		$this->assertAction ( 'index' );
	}
}
?>
