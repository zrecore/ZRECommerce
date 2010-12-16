<?php

interface Logistic_Adapter_Interface {
	/**
	 * Calculate the logistical cost of all items
	 * @param Cart_Container The cart to calculate.
	 * @return float The total.
	 */
	public function calculate(Cart_Container $cartContainer);
	/**
	 * Charge the logistical cost using the specified logistic data.
	 * @param Cart_Container $cartContainer The items to calculate for.
	 * @param mixed $logisticData
	 * @return mixed Return data on success, or null on failure.
	 */
	public function pay(Cart_Container $cartContainer, $logisticData);

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
	 *
	 * @param array $data The data to process
	 * @param array $options Additional array of options
	 */
	public function postProcess($data, $options = null);
}