<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Cart
 * @category Cart
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * Cart_Container_Item_Abstract - This is the base class for all cart item classes.
 *
 */
abstract class Cart_Container_Item_Abstract {
	/**
	 * Stock Keeping Unit, the unique identifier used by this item.
	 * @var string
	 */
	private $_sku;
	/**
	 * Collection of detail strings.
	 * @var Cart_Container_Item_Options_Detail
	 */
	private $_detailOptions;
	/**
	 * Collection of cost values.
	 * @var Cart_Container_Item_Options_Cost
	 */
	private $_costOptions;
	/**
	 * Collection of measurement metrics
	 * @var Cart_Container_Item_Options_Metric
	 */
	private $_metricOptions;
	/**
	 * Quantity of whatever this item represents.
	 * @var int
	 */
	private $_quantity;
	/**
	 * The string of text to display when this item is paid for.
	 * @var string
	 */
	private $_purchasedMessage = '';
	/**
	 * Internal array of validators. Validators must implement
	 * Cart_Container_Validate_Interface
	 * @var array
	 */
	private $_validators;
	
	/**
	 * Constructor. Create a new Item object.
	 * @param $sku The unique identifier of this product instance.
	 * @param $detailOptions The details associated with this product
	 * @param $costOptions
	 * @param $metricOptions
	 * @param $purchasedMessage
	 * @return void
	 */
	public function __construct( $sku, 
								Cart_Container_Item_Options_Detail $detailOptions, 
								Cart_Container_Item_Options_Cost  $costOptions, 
								Cart_Container_Item_Options_Metrics  $metricOptions, 
								$quantity, 
								$validators = null,
								$purchasedMessage = '' ) {
		// if only one metric option is found, then it should be used by default (no select box needed)
		// if only one cost option is found, then it should be used by default (no select box needed)
		$this->_sku = $sku;
		$this->_detailOptions = $detailOptions;
		$this->_costOptions = $costOptions;
		$this->_metricsOptions = $metricOptions;
		
		$this->_quantity = $quantity;
		$this->setValidators($validators);
		
		$this->_purchasedMessage = $purchasedMessage;
	}
	
	public function getSku() {
		return $this->_sku;
	}
	/**
	 * Get the Detail options object.
	 * @return Cart_Container_Item_Options_Detail
	 */
	public function getDetailOptions() {
		return $this->_detailOptions;
	}
	/**
	 * Get the Cost options object.
	 * @return Cart_Container_Item_Options_Cost
	 */
	public function getCostOptions() {
		return $this->_costOptions;
	}
	/**
	 * Get the Metrics options object.
	 * @return Cart_Container_Item_Options_Metrics
	 */
	public function getMetricsOptions() {
		return $this->_metricsOptions;
	}
	/**
	 * Get the item quantity.
	 * @return int
	 */
	public function getQuantity() {
		return $this->_quantity;
	}
	/**
	 * Get the message string that is output when this item is purchased.
	 * @return string
	 */
	public function getPurchasedMessage() {
		return $this->_purchasedMessage;
	}
	
	public function setDetailOptions($detailOptions) {
		$this->_detailOptions = $detailOptions;
	}
	
	public function setCostOptions($costOptions) {
		$this->_costOptions = $costOptions;
	}
	
	public function setMetricsOptions($metricsOptions) {
		$this->_metricsOptions = $metricsOptions;
	}
	
	public function setQuantity($quantity) {
		$this->_quantity = $quantity;
	}
	/**
	 * Set message string that is output when this item is purchased.
	 * @param $message
	 * @return void
	 */
	public function setPurchasedMessage($message) {
		$this->_purchasedMessage = $message;
	}
	/**
	 * Key/Value pair of validator objects.
	 * @param array $validators
	 * @return void
	 */
	public function setValidators($validators) {
		$this->_validators = array();
		if (is_array($validators)) {
			foreach ($validators as $key => $validator) {
				$this->addValidator($key, $validator);
			}
		}
	}
	/**
	 * Return the specified validator. If $key is not set, all validators
	 * are returned. If the specified validator does not exist, null is
	 * returned.
	 * @param string $key
	 * @return Cart_Container_Item_Validate_Interface
	 */
	public function getValidator($key = null) {
		if (isset($key)) {
			if ($this->hasValidator($key)) {
				return $this->_validators[$key];
			} else {
				return null;
			}
		} else {
			return $this->_validators;
		}
	}
	/**
	 * Check to see if the specified validator exists internally.
	 * @param string $key
	 * @return bool
	 */
	public function hasValidator($key) {
		return isset($this->_validators[$key]);
	}
	/**
	 * Append or overwrite the specified validator.
	 * @param string $key
	 * @param Cart_Container_Item_Validate_Interface $validator
	 * @return void
	 */
	public function addValidator($key, $validator) {
		if ($validator instanceof Cart_Container_Item_Validate_Interface) {
			$this->_validators[$key] = $validator;
		} else {
			throw new Zend_Exception('Validator does not implement Cart_Container_Item_Validate_Interface');
		}
	}
	/**
	 * Remove the specified validator.
	 * @param $key
	 * @return bool
	 */
	public function removeValidator($key) {
		$result = false;
		if ($this->hasValidator($key)) {
			unset($key);
			$result = true;
		}
		
		return $result;
	}
	/**
	 * Run the internal validator objects.
	 * @return bool
	 */
	public function validate() {
		$isValid = true;
		foreach($this->_validators as $validator) {
			$isValid = $validator->validate($this);
			if (!$isValid) break;
		}
		
		return $isValid;
	}
	/**
	 * Return internally calculated cost.
	 * @param bool $runValidators If set to true, the internal validate() method will run.
	 * @return float
	 */
	public function calculateCost($runValidators = true) {
		if ($runValidators == true) {
			$isValid = $this->validate();
		}
		// ...return calculated cost.
		return $this->_quantity * $this->_costOptions->calculate();
	}
	
}