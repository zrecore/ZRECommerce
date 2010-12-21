<?php

interface Logistic_Adapter_Interface {
	/**
	 * Calculate the logistical cost of all items
	 * @param Cart_Container $cartContainer The cart to calculate.
         * @param array|null $options Additional options, if any.
	 * @return array The totals for each item.
	 */
	public function calculate(Cart_Container $cartContainer, $options = null);
	
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