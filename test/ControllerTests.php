<?php
/**
 * ControllerTests - A Test Suite for controllers.
 * 
 * @author aalbino
 */
require_once 'application/default/controllers/IndexControllerTest.php';
require_once 'application/default/controllers/OrdersControllerTest.php';

/**
 * ControllerTests class - aggregates all controller tests of this project
 */
class ControllerTests extends PHPUnit_Framework_TestSuite {
	
	protected function setUp() {
			
	}
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'ControllerTests' );
		
		$this->addTestSuite ( 'IndexControllerTest' );
		$this->addTestSuite ( 'OrdersControllerTest' );
	
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ( );
	}
}

