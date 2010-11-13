<?
/**
 * IndexControllerTest - Test the default index controller
 * 
 * @author
 * @version 
 */
// Zend_Application
require_once 'Zend/Application.php';
require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';
//require_once 'application/Initializer.php';

/**
 * IndexController Test Case
 */
class IndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase {

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		 // Assign and instantiate in one step:
        
        define('BASE_PATH', realpath(dirname(__FILE__) . '/../../../../'));
		define('APPLICATION_PATH', BASE_PATH . '/application');
		
		// Include path
		set_include_path(
		    BASE_PATH . '/library'
		    . PATH_SEPARATOR . get_include_path()
		);
		
		// Define application environment
		define('APPLICATION_ENV', 'test');
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
//		$this->frontController->registerPlugin ( new Initializer( 'test' ) );
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
//		$this->assertAction ( 'index' );
	}
}
?>
