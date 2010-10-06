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
 * Cart_Container_Item_Options_Metrics - This class represents measurement 
 * metrics.
 */
class Cart_Container_Item_Options_Metrics extends Cart_Container_Item_Options_Abstract {
	private $_metricDecimal = array(
		'TERRA' => 1000000000000,
		'GIGA' => 1000000000,
		'MEGA' => 1000000,
		'KILO' => 1000,
		'HECTO' => 100,
		'DECA' => 10,
		'' => 1,
		'DECI' => 	.1,
		'CENTI' => 	.01,
		'MILI' => 	.001,
		'MICRO' => 	.000001,
		'NANO' => 	.000000001
	);
	/**
	 * The unit of measure to append to the metric value.
	 * @var string
	 */
	private $_unitOfMeasure;
	/**
	 * The unit of measure to append to the metric value.
	 * @param $unitOfMeasure
	 * @return void
	 */
	public function setUnitOfMeasure($unitOfMeasure) {
		$this->_unitOfMeasure = $unitOfMeasure;
	}
	/**
	 * Get the unit of measure.
	 * @return string
	 */
	public function getUnitOfMeasure() {
		return $this->_unitOfMeasure;
	}
	/**
	 * (non-PHPdoc)
	 * @see library/Cart/Container/Item/Options/Cart_Container_Item_Options_Abstract#calculate()
	 */
	public function calculate() {
		return parent::calculate() . ' ' . $this->getUnitOfMeasure();
	}
}