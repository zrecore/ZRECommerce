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
 * @copyright Copyrights 2008 ZRECommerce. All rights reserved.
 * @license Creative Commons license - See public/license.txt
 */
/**
 * Cart_Container - The shopping cart object. This object holds cart items.
 *
 */
class Cart_Container
{
	private $_items = array();
	
	/**
	 * Constructor. Instantiate a new instance of this class.
	 * You can specify a numerical array of Cart_Container_Item.
	 * 
	 * @example 
	 * <code>
	 * $cartItems = array( new Cart_Container_Item('123-456-7890', 10.00, 1, '3inx5inx4.25in', 'Test Item 1', 'This is a test item.'),
	 * 					   new Cart_Container_Item('123-456-7891', 5.00, 1, '2inx2inx4.25in', 'Test Item 2', 'This is another test item.'), 
	 * 					   new Cart_Container_Item('123-456-7891', 5.00, 1, '2inx2inx4.25in', 'Test Item 2', 'This is another test item.'), 
	 * 					   new Cart_Container_Item('123-456-7892', 45.00, 3, '8inx8inx5in', 'Test Item 3', 'This is a heavier test item.'));
	 * 					   
	 * $cart = new Cart_Container($cartItems);
	 * </code>
	 * 
	 * @param array $items
	 */
	public function __construct( $items = null )
	{
		if (isset($items)) {
			$this->setItems($items);
		}
	}
	
	/**
	 * Adds an item to the cart. If an item is already in 
	 * the cart, the item quantity is updated instead.
	 */
	public function addItem( $item ) {
		
		$exists = false;
		foreach($this->_items as $sku => $cartItem) {
			
			if ($cartItem instanceof Cart_Container_Item_Abstract) {
				$cartItem = Cart_Container_Item::factory($cartItem);
				
				// ...Check to see if item is already in the cart.
				if ($sku == $item->getSku()) {
					$exists = true;
					break;
				}
			} else {
				throw new Zend_Exception('Item is not an instance of Cart_Container_Item_Abstract');
			}
		}
		
		if ($exists == true) {
			
			$cartItem->setQuantity( $cartItem->getQuantity() + $item->getQuantity() );
			$this->_items[$sku] = $cartItem;
		} else {
			$sku = $item->getSku();
			$this->_items[$sku] = $item;
		}
	}
	
	/**
	 * Remove an item from the cart.
	 * 
	 * @param string $sku
	 * 
	 * @return boolean
	 */
	public function removeItem( $sku ) {
		
		$exists = false;
		
		foreach($this->_items as $cartItem) {
			
			// ...Check to see if item is already in the cart.
			if ($sku == $cartItem->getSku()) {
				$exists = true;
				break;
			}
		}
		
		if ($exists == true) {
			$cartItem->setQuantity( $cartItem->getQuantity() - 1 );
			if ($cartItem->getQuantity <= 0) {
				unset($this->_items[$sku]);
			} else {
				$this->updateItem($cartItem);
			}
			return true;
		}
	}
	
	/**
	 * Update a cart item.
	 * 
	 * @return boolean
	 */
	public function updateItem( $item ) {
		$exists = false;
		if ($item instanceof Cart_Container_Item_Abstract) {
			foreach($this->_items as $cartItem) {
				$sku = $cartItem->getSku();
				$cartItem = Cart_Container_Item::factory($item);
				
				// ...Check to see if item is already in the cart.
				if ($sku == $item->getSku()) {
					$exists = true;
					break;
				}
			}
			
			if ($exists == true) {
				$this->_items[$sku] = $item;
				return true;
			} else {
				return false;
			}
		} else {
			throw new Zend_Exception('Item is not an instance of Cart_Container_Item_Abstract');
		}
	}
	
	/**
	 * Calculates the gross total of all items in the cart.
	 * 
	 * @return float
	 */
	public function getTotal() {
		$total = 0;
		foreach($this->_items as $cartItem ) {
			$cartItem = Cart_Container_Item::factory($cartItem);
			$total += $cartItem->getCostOptions()->calculate() * $cartItem->getQuantity();
		}
		
		return $total;
	}
	
	/**
	 * Returns the array of Cart_Container_Item objects.
	 */
	public function getItems() {
		return $this->_items;
	}
	/**
	 * Clear out the current list of items, and add the new ones.
	 * 
	 * @param array $items
	 */
	public function setItems( $items ) {
		$this->_items = array();
		
		$this->appendItems($items);
	}
	
	/**
	 * Append and/or merge another list of items to the existing list of items.
	 * 
	 * @param array $items
	 */
	public function appendItems( $items ) {
		foreach ($items as $cartItem) {
			if ($cartItem instanceof Cart_Container_Item_Abstract) {
				$this->addItem($cartItem);
			} else {
				throw new Zend_Exception('Item is not an instance of Cart_Container_Item_Abstract');
			}
		}
	}
	
	/**
	 * Returns the total amount of items in this cart.
	 * 
	 * @return int
	 */
	public function count() {
		return count($this->_items);
	}
	
}
?>
