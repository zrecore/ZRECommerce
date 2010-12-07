<?php
class Checkout_Payment {
	/**
	 * The checkout adapter to use for payment.
	 * @var Checkout_Adapter_Interface
	 */
	private static $_paymentAdapter;

	/**
	 * Constructor. Set the payment adapter to use for payment.
	 * @param Checkout_Adapter_Interface $paymentAdapter
	 * @throws Exception
	 */
	public static function getAdapter() {
		if (!isset(self::$_paymentAdapter))
			throw new Checkout_Exception('No payment adapter set.');
		
		return self::$_paymentAdapter;
	}

	public static function setAdapter($paymentAdapter)
	{
		self::$_paymentAdapter = $paymentAdapter;
	}
	/**
	 *
	 * @param Cart_Container $cartContainer
	 * @param mixed $paymentData
	 * @return int|null The order id, or null upon failure.
	 */
	public static function pay($cart, $data)
	{
		return self::getAdapter()->pay($cart, $data);
	}
	/**
	 * Post-processing hook for any payment adapters that have a call back
	 * url setup through the merchant gatway (such as Paypal).
	 * @param string $source
	 * @param array $data
	 */
	public static function postProcess($adapterName, $data) {
		$adapterName = preg_replace('[^a-zA-Z]','', $adapterName);
		$class_exists = @class_exists('Checkout_Adapter_' . $adapterName);

		$result = false;
		if ($class_exists == true) {
			$adapterFullName = 'Checkout_Adapter_' . $adapterName;
			$adapter = new $adapterFullName;

			$adapter->postProcess($data);
		}

		return $result;
	}
	
	public static function getRequiredFields($options = null) {
		return self::getAdapter()->getRequiredFields($options);
	}

	public static function getOptionalFields($options = null) {
		return self::getAdapter()->getOptionalFields();
	}
}