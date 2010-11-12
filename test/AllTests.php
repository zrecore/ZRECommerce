<?php
/**
 * AllTests - A Test Suite for your Application 
 * 
 * @author
 * @version 
 */
require_once 'PHPUnit/Framework/TestSuite.php';
//require_once 'application/Initializer.php';

require_once 'application/default/controllers/IndexControllerTest.php';

/**
 * AllTests class - aggregates all tests of this project
 */
class AllTests extends PHPUnit_Framework_TestSuite {
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'AllTests' );
		
		$this->addTestSuite ( 'IndexControllerTest' );
	
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ( );
	}
}

