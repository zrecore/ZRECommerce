<?php
class Checkout_Adapter_Paypal_Client extends Zend_Http_Client
{
	private static $_api_endpoint_live = 'https://api-3t.paypal.com/nvp';
	private static $_api_endpoint_end = 'https://api-3t.sandbox.paypal.com/nvp';

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

	static function getUri() {
	    $settings = Zre_Config::getSettingsCached();
	    return (string)$settings->merchant->paypal->nvp->uri;
	}

	function __construct($uri = null, $options = null) {

		if (!isset($uri)) $uri = self::getUri();
		if (!isset($options)) $options = null;

		parent::__construct($uri, $options);

		parent::setParameterPost('USER', self::getApiUserName());
		parent::setParameterPost('PWD', self::getApiPassword());
		parent::setParameterPost('SIGNATURE', self::getApiSignature());
		parent::setParameterPost('VERSION', self::getApiVersion());
	}

	function doDirectPayment()
	{
		throw Exception('Method not implemented yet.');
	    parent::setParameterPost('METHOD', 'DoDirectPayment');
	}


	function eCDoExpressCheckout($token, $payer_id, $payment_amount, $currency_code, $payment_action = 'Authorization') {
		parent::setParameterPost('TOKEN', $token);
		parent::setParameterPost('PAYERID', $payer_id);
		parent::setParameterPost('PAYMENTACTION', $payment_action); // Can be 'Authorization', 'Sale', or 'Order'
		
		parent::request('post');
	}
}