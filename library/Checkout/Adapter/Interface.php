<?php
interface Checkout_Adapter_Interface {
	/**
	 * Calculate the gross total of all items
	 * @param Cart_Container The cart to calculate.
	 * @return float The total.
	 */
	public function calculate(Cart_Container $cartContainer);
	/**
	 * Charge the total using the specified payment data.
	 * @param Cart_Container $cartContainer The items to pay for.
	 * @param mixed $paymentData
	 * @return mixed Return the order ID on success, or null on failure.
	 */
	public function pay(Cart_Container $cartContainer, $paymentData);
}