<?php
class Checkout_Adapter_Paypal_Client extends SoapClient
{
	private static $_wsdl_url_test = 'https://www.sandbox.paypal.com/wsdl/PayPalSvc.wsdl';
	private static $_wsdl_url_live = 'https://www.paypal.com/wsdl/PaPalSvc.wsdl';
//	private static $_client = null;
//	
//	public function getClient() {
//		$options = array('wsdl' => 'https://www.sandbox.paypal.com/wsdl/PayPalSvc.wsdl');
//		$client = new Zend_Soap_Client(null, $options);
//		
//		return $client;
//	}

	static function getApiUserName() {
		$settings = Zre_Config::getSettingsCached();
		return (string)$settings->merchant->paypal->api_user_name;
	}
	static function getApiPassword() {
		$settings = Zre_Config::getSettingsCached();
		return (string)$settings->merchant->paypal->api_password;
	}
	static function getWsdlUrl() {
		return (string) self::$_wsdl_url;
	}
	function __construct($wsdl = null, $options = null) {
		if (!isset($wsdl)) $wsdl = self::getWsdlUrl();
		if (!isset($options)) $options = array();
		parent::__construct($wsdl, $options);
	}

	// This section inserts the UsernameToken information in the outgoing SOAP message.
	function __doRequest($request, $location, $action, $version) {
		$settings = Zre_Config::getSettingsCached();
		
		$user = (string)$settings->merchant->cybersource->merchant_id;
		$password = (string)$settings->merchant->cybersource->transaction_key;
		
		$soapHeader = "<SOAP-ENV:Header xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:wsse=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd\"><wsse:Security SOAP-ENV:mustUnderstand=\"1\"><wsse:UsernameToken><wsse:Username>$user</wsse:Username><wsse:Password Type=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText\">$password</wsse:Password></wsse:UsernameToken></wsse:Security></SOAP-ENV:Header>";
	
		$requestDOM = new DOMDocument('1.0');
		$soapHeaderDOM = new DOMDocument('1.0');
	
		try {
	
			$requestDOM->loadXML($request);
			$soapHeaderDOM->loadXML($soapHeader);
			
			$node = $requestDOM->importNode($soapHeaderDOM->firstChild, true);
			$requestDOM->firstChild->insertBefore($node, $requestDOM->firstChild->firstChild);
			
			$request = $requestDOM->saveXML();
	
		} catch (DOMException $e) {
				die( 'Error adding UsernameToken: ' . $e->code);
		}
	
		return parent::__doRequest($request, $location, $action, $version);
	}
}