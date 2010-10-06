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
 * Cart - This class provides static CRUD methods.
 *
 */
class Cart {
	/**
	 * Internal Cart_Container object
	 *
	 * @var Cart_Container
	 */
	private static $_cartContainer;
	/**
	 * Cart session namespace.
	 *
	 */
	const DEFAULT_NAMESPACE = 'CART';
	/**
	 * Saves the Cart_Container object to the session namespace.
	 *
	 */
	public static function saveSession() {
		$cartContainer = self::getCartContainer();
		Zre_Registry_Session::set(self::DEFAULT_NAMESPACE, $cartContainer);
		Zre_Registry_Session::save();
	}
	/**
	 * Loads the Cart_Container object from the session namespace
	 * Returns false upon failure.
	 * 
	 * @return Cart_Container|boolean
	 */
	public static function loadSession() {
		if (Zre_Registry_Session::isRegistered(self::DEFAULT_NAMESPACE)) {
			$cartContainer = Zre_Registry_Session::get(self::DEFAULT_NAMESPACE);
			return self::setCartContainer($cartContainer);
		} else {
			return false;
		}
	}
	/**
	 * Flushes the current Cart_Container. Replaces it with an empty one.
	 *
	 */
	public static function flushSession() {
		if (Zre_Registry_Session::isRegistered(self::DEFAULT_NAMESPACE)) {
			Zre_Registry_Session::flush(self::DEFAULT_NAMESPACE);
			
			self::$_cartContainer = null;
		}
	}
	/**
	 * Returns the Cart_Container object that is currently in use.
	 *
	 * @return Cart_Container
	 */
	public static function getCartContainer() {
		if (!isset(self::$_cartContainer)) {
			self::$_cartContainer = new Cart_Container();
		}
		return self::$_cartContainer;
	}
	/**
	 * Sets the Cart_Container object to use.
	 *
	 * @param Cart_Container $cartContainer
	 * @return boolean
	 */
	public static function setCartContainer( $cartContainer ) {
		if ($cartContainer instanceof Cart_Container ) {
			self::$_cartContainer = $cartContainer;
			return true;
		} else {
			return false;
		}
	}
}
?>