<?php
class Checkout_Adapter_Paypal implements Checkout_Adapter_Interface {
	/**
	 * Calculate the gross total of all items
	 * @param Cart_Container The cart to calculate.
	 * @return float The total.
	 */
	public function calculate(Cart_Container $cartContainer)
	{
		return $cartContainer->getTotal();
	}
	/**
	 * Charge the total using the specified payment method.
	 * @param Cart_Container $cartContainer
	 * @param mixed $paymentData
	 * @return mixed Return the order ID on success, or null on failure.
	 */
	public function pay(Cart_Container $cartContainer, $paymentData)
	{
		// @todo Implement using Paypal NVP here!
	}
}