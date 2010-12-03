<?php
class Checkout_Adapter_Paypal_Client extends Zend_Http_Client
{
//	private static $_wsdl_url_test = 'https://www.sandbox.paypal.com/wsdl/PayPalSvc.wsdl';
//	private static $_wsdl_url_live = 'https://www.paypal.com/wsdl/PaPalSvc.wsdl';
//	private static $_client = null;
//	
//	public function getClient() {
//		$options = array('wsdl' => 'https://www.sandbox.paypal.com/wsdl/PayPalSvc.wsdl');
//		$client = new Zend_Soap_Client(null, $options);
//		
//		return $client;
//	}
	private static $_api_version = '56.0';

	static function getApiUserName() {
		$settings = Zre_Config::getSettingsCached();
		return (string)$settings->merchant->paypal->api_user_name;
	}
	static function getApiPassword() {
		$settings = Zre_Config::getSettingsCached();
		return (string)$settings->merchant->paypal->api_password;
	}
	static function getApiSignature() {
		$settings = Zre_Config::getSettingsCached();
		return (string) $settings->merchant->paypal->api_signature;
	}
	static function getApiVersion() {
	    $settings = Zre_Config::getSettingsCached();
	    $version = isset($settings->merchant->paypal->api_version) ?
			(string) $settings->merchant->paypal->api_version :
			self::$_api_version;
	    
	    return (string) $version;
	}
//	static function getWsdlUrl() {
//		return (string) self::$_wsdl_url;
//	}
	static function getUri() {
	    $settings = Zre_Config::getSettingsCached();
	    return (string)$settings->merchant->paypal->nvp->uri;
	}

	function __construct($uri = null, $options = null) {
//		if (!isset($wsdl)) $wsdl = self::getWsdlUrl();
		if (!isset($uri)) $uri = self::getUri();
		if (!isset($options)) $options = null;

//		parent::__construct($wsdl, $options);
		parent::__construct($uri, $options);

		parent::setParameterPost('USER', self::getApiUserName());
		parent::setParameterPost('PWD', self::getApiPassword());
		parent::setParameterPost('SIGNATURE', self::getApiSignature());
		parent::setParameterPost('VERSION', self::getApiVersion());
	}

	function doDirectPayment()
	{
	    parent::setParameterPost('METHOD', 'DoDirectPayment');
	}


	// This section inserts the UsernameToken information in the outgoing SOAP message.
//	function __doRequest($request, $location, $action, $version) {
//		$settings = Zre_Config::getSettingsCached();
//
//		$user = (string)$settings->merchant->paypal->api_user_name;
//		$password = (string)$settings->merchant->paypal->api_password;
//		$signature = (string)$settings->merchant->paypal->api_signature;
//		$authorizing_account_emailaddress = (string)$settings
//		    ->merchant
//		    ->paypal
//		    ->authorizing_account_emailaddress;
//
//		$soapHeader =
//			    "<SOAP-ENV:Header>" .
//				"<RequestCredentials xmlns=\"urn:ebay:api:PayPalAPI\" xsi:type=\"ebl:CustomSecurityHeaderType\">" .
//				    "<Credentials xmlns=\"urn:ebay:apis:eBLBaseComponents\" xsi:type=\"ebl:UserIdPasswordType\">" .
//					"<Username>$user</Username>" .
//					"<Password>$password</Password>" .
//					"<Signature>$signature</Signature>" .
//					"<Subject>$authorizing_account_emailaddress</Subject>" .
//				    "</Credentials>" .
//				"</RequestCredentials>" .
//			    "</SOAP-ENV:Header>";
//
//		$requestDOM = new DOMDocument('1.0');
//		$soapHeaderDOM = new DOMDocument('1.0');
//
//		try {
//
//			$requestDOM->loadXML($request);
//			$soapHeaderDOM->loadXML($soapHeader);
//
//			$node = $requestDOM->importNode($soapHeaderDOM->firstChild, true);
//			$requestDOM->firstChild->insertBefore($node, $requestDOM->firstChild->firstChild);
//
//			$request = $requestDOM->saveXML();
//
//		} catch (DOMException $e) {
//				die( 'Error adding UsernameToken: ' . $e->code);
//		}
//
//		return parent::__doRequest($request, $location, $action, $version);
//	}
}