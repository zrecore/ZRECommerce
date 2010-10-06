<?php
class Checkout_Adapter_Paypal {
	public function getClient() {
		$options = array('wsdl' => 'https://www.sandbox.paypal.com/wsdl/PayPalSvc.wsdl');
		$client = new Zend_Soap_Client(null, $options);
		
		return $client;
	}
}