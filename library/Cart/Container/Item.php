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
 * Cart_Container_Item - This is the basic 'item' class.
 *
 */
class Cart_Container_Item extends Cart_Container_Item_Abstract {
	public function __construct($sku, $detailOptions, $costOptions, $metricOptions, $quantity, $validators = null, $purchasedMessage = '') {
		parent::__construct($sku, $detailOptions, $costOptions, $metricOptions, $quantity, $validators, $purchasedMessage);
	}
	public function calculateCost($runValidators = true) {
		return parent::calculateCost();
	}
	/**
	 * If an object has the same methods as Cart_Container_Item, a
	 * Cart_Container_Item will be returned.
	 * 
	 * @param mixed $object
	 * 
	 * @return Cart_Container_Item 
	 */
	public static function factory( $object ) {
		
		$item = new Cart_Container_Item($object->getSku(), 
										$object->getDetailOptions(), 
										$object->getCostOptions(), 
										$object->getMetricsOptions(), 
										$object->getQuantity(),  
										$object->getValidator(),
										$object->getPurchasedMessage());
		
		return $item;
	}
}
?>
