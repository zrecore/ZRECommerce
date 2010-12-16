<?php

class Logistic_Delivery {
	/**
	 * The internal adapter.
	 * @var Logistic_Adapter_Interface
	 */
	private static $_adapter = null;

	/**
	 * Set the logistic adapter.
	 * @param Logistic_Adapter_Interface $adapter
	 */
	public static function setAdapter($adapter) {
		self::$_adapter = $adapter;
	}
	/**
	 * Get the logistic adapter.
	 * @return Logistic_Adapter_Interface The adapter in use.
	 */
	public static function getAdapter() {
		return self::$_adapter;
	}
	/**
	 * Calculate the logistical cost of all items
	 * @param Cart_Container The cart to calculate.
	 * @return float The total.
	 */
	public static function calculate(Cart_Container $cartContainer);
	/**
	 * Charge the logistical cost using the specified logistic data.
	 * @param Cart_Container $cartContainer The items to calculate for.
	 * @param mixed $logisticData
	 * @return mixed Return data on success, or null on failure.
	 */
	public static function pay(Cart_Container $cartContainer, $logisticData);

	/**
	 * Get the list of required fields for this adapter.
	 * @param array|null $options The array of required fields.
	 */
	public static function getRequiredFields($options = null);
	/**
	 * Get the list of optional fields for this adapter.
	 * @param array|null $options The array of optional fields.
	 */
	public static function getOptionalFields($options = null);
	/**
	 *
	 * @param <type> $data The data to process
	 * @param <type> $options Additional array of options
	 */
	public static function postProcess($data, $options = null);
}