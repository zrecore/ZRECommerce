<?php
/**
 * Checkout_Adapter_Paypal_ClientTest - A Test Suite for your Application
 *
 * @author aalbino
 */
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Zend/Config/Writer/Xml.php';

/**
 * AllTests class - aggregates all tests of this project
 */
class Checkout_Adapter_Paypal_ClientTest extends PHPUnit_Framework_TestCase {



	public function testDoDirectPayment() {

		require_once 'Zre/Config.php';
		require_once 'Checkout/Adapter/Paypal/Client.php';

		$settings = Zre_Config::loadSettings(
			APPLICATION_PATH . '/settings/environment/settings.xml',
			true
		);

		$client = new Checkout_Adapter_Paypal_Client($settings->merchant->paypal->api_endpoint_uri);

		$amount = 1.00;
		$credit_card_type = 'Visa';
		$credit_card_number = $settings->merchant->paypal->api_test_credit_card_number;
		$expiration_month = $settings->merchant->paypal->api_test_expiration_month;
		$expiration_year = $settings->merchant->paypal->api_test_expiration_year;
		$cvv2 = '321';
		$first_name = 'phpUnit';
		$last_name = 'Tester';
		$address1 = '1 Test Ln';
		$address2 = null;
		$city = 'Testerville';
		$state = 'CA';
		$zip = '54321';
		$country = 'US';
		$currency_code = 'USD';

		// Do a 'Sale' direct payment (Default).
		$result = $client->doDirectPayment(
			$amount,
			$credit_card_type,
			$credit_card_number,
			$expiration_month,
			$expiration_year,
			$cvv2,
			$first_name,
			$last_name,
			$address1,
			$address2,
			$city,
			$state,
			$zip,
			$country,
			$currency_code
		);

		$reply = $result->getBody();
		$ppalResponse = $client->parse($reply);

		$resultDump = print_r($ppalResponse, true);

		$this->assertEquals(true,	$result->isSuccessful(), $resultDump);
		$this->assertEquals(200,	$result->getStatus(), $resultDump);
		$this->assertEquals('Success',	$ppalResponse->ACK, $resultDump);
		$this->assertEquals('1.00',	$ppalResponse->AMT, $resultDump);

		return $ppalResponse->TRANSACTIONID;
	}

	/**
	 *
	 * @param string $transaction_id
	 * @depends testDoDirectPayment
	 */
	public function testTransactionDetails($transaction_id)
	{
		$settings = Zre_Config::loadSettings(
			APPLICATION_PATH . '/settings/environment/settings.phpunit.xml',
			true
		);

		$client = new Checkout_Adapter_Paypal_Client($settings->merchant->paypal->api_endpoint_uri);

		$result = $client->getTransactionDetails($transaction_id);
		$transactionDetails = $client->parse($result->getBody());

		$resultDump = print_r($transactionDetails, true);

		// ...Assert we had a successful API call.
		$this->assertEquals('Success', $transactionDetails->ACK, $resultDump);
		
		// ...Assert the transaction was for the expected amount.
		$this->assertEquals(1.00, $transactionDetails->AMT, $resultDump);
	}
}

