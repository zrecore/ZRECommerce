<?php
/**
 * Checkout_Adapter_Paypal_ClientTest - A Test Suite for your Application
 *
 * @author aalbino
 */
require_once 'PHPUnit/Framework/TestSuite.php';

/**
 * AllTests class - aggregates all tests of this project
 */
class Checkout_Adapter_Paypal_ClientTest extends PHPUnit_Framework_TestSuite {

	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'Checkout_Adapter_Paypal_ClientTest' );
	}

	public function testConnection()
	{
	    $client = new Checkout_Adapter_Paypal_Client();
	    $request = new stdClass();

//	    $request->
	}

	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ( );
	}
}

