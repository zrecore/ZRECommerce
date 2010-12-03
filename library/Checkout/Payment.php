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
	 */
	
	public static function getAdapter() {
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
}