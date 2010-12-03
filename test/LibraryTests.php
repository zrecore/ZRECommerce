<?php
/**
 * LibraryTests - A Test Suite for the /library classes
 *
 * @author aalbino
 */
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'library/CheckoutTest.php';
require_once realpath(dirname(__FILE__) . '/../application/bootstrap.php');

/**
 * LibraryTests class - aggregates all tests of this project
 */
class LibraryTests extends PHPUnit_Framework_TestSuite {

	public function setUp()
	{
		Bootstrap::setupPaths();
	}

	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'LibraryTests' );

		$this->addTestSuite ( 'CheckoutTest' );

	}

	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ( );
	}
}

