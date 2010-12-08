<?php

require_once 'PHPUnit/Framework/TestCase.php';

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Test credit card numbers:
 *
 * Visa			4111 1111 1111 1111
 * MasterCard		5555 5555 5555 4444
 * American Express	3782 8224 6310 005
 * Discover		6011 1111 1111 1117
 * JCB			3566 1111 1111 1113
 * Diners Club		3800 000000 0006
 * Maestro (UK Domestic)6759 4111 0000 0008
 * Solo			6334 5898 9800 0001
 */

class Checkout_Adapter_Cybersource_ClientTest extends PHPUnit_Framework_TestCase
{
	public function testDoRequest() {
		$this->markTestSkipped('This is adapter uses a SOAP client API. SKIPPED.');
	}
}
?>
