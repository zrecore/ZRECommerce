<?php
class Checkout_Adapter_Paypal implements Checkout_Adapter_Interface {
	/**
	 * Calculate the gross total of all items
	 * @param Cart_Container The cart to calculate.
	 * @return float The total.
	 */
	public function calculate(Cart_Container $cartContainer)
	{
		
	}
	/**
	 * Charge the total using the specified payment method.
	 * @param float $total
	 * @param Checkout_Payment_Interface $payment
	 * @return unknown_type
	 */
	public function pay(float $total, Checkout_Payment_Interface $payment)
	{
		
	}
}