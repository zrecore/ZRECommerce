<?php
/**
 * CheckoutTest - A Test Suite for the /library/Checkout classes
 *
 * @author aalbino
 */
require_once 'PHPUnit/Framework/TestSuite.php';

require_once 'library/Checkout/Adapter/Paypal/ClientTest.php';
require_once 'library/Checkout/Adapter/PaypalTest.php';
/**
 * CheckoutTest class - aggregates all tests of this project
 */
class CheckoutTest extends PHPUnit_Framework_TestSuite {

	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'CheckoutTest' );

		$this->addTestSuite ( 'Checkout_Adapter_Paypal_ClientTest' );
		$this->addTestSuite ( 'Checkout_Adapter_PaypalTest' );

	}

	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ( );
	}
}

