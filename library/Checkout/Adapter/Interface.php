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

	/**
	 * Get the list of required fields for this adapter.
	 * @param array|null $options The array of required fields.
	 */
	public function getRequiredFields($options = null);
	/**
	 * Get the list of optional fields for this adapter.
	 * @param array|null $options The array of optional fields.
	 */
	public function getOptionalFields($options = null);
	/**
	 * Get the data needed to post-process using the cart and the request
	 * variables.
	 *
	 * @param Cart_Container $cart
	 * @param array $request
	 * @return array
	 */
	public function getPostProcessFields($cart, $request);
	/**
	 * 
	 * @param array $data The data to process
	 * @param array $options Additional array of options
	 * @return bool
	 */
	public function postProcess($data, $options = null);
}