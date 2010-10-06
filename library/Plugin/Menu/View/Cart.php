<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Plugin
 * @category Plugin
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. All rights reserved.
 * @license Creative Commons license - See public/license.txt
 */
/**
 * Plugin_Menu_View_Cart - "View Cart" link.
 *
 */
class Plugin_Menu_View_Cart {
	public function __toString() {
		return $this->getHtml();
	}
	public function getHtml() {
		$t = Zend_Registry::get('Zend_Translate');
		
		Cart::loadSession();
		$cart = Cart::getCartContainer();
		$totalCartItems = $cart->count();
		
		$viewCartButton = '<div id="viewCartButton"><a href="/shop/cart/">' . $t->_('View Cart') . ' <span class="viewCartAmount">( ' . ((int) $totalCartItems) . ' ' . $t->_('items') . ' )</span></a></div>';
		
		return $viewCartButton;
	}
	public function getScript() {
		
	}
}
?>