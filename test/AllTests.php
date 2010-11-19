<?php
/**
 * AllTests - A Test Suite for your Application 
 * 
 * @author aalbino
 */
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'ControllerTests.php';
//require_once 'application/Initializer.php';

/**
 * AllTests class - aggregates all tests of this project
 */
class AllTests extends PHPUnit_Framework_TestSuite {
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'AllTests' );
		
		$this->addTestSuite ( 'ControllerTests' );
	
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ( );
	}
}

