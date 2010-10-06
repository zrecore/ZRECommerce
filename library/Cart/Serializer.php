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
 * Cart_Serializer - This class uses the appropriate adapter to serialize
 * and unserialize a shopping cart.
 *
 */
class Cart_Serializer
{
	/**
	 * Use JSON encode/decode
	 * @var string
	 */
	const USE_JSON = 'json';
	/**
	 * Serialize a Cart_Container object.
	 * 
	 * @param Cart_Container $cartContainer
	 * @param string $mode Default is Cart_Serializer::USE_JSON
	 * @return string
	 */
	public static function serialize($cartContainer, $mode = Cart_Serializer::USE_JSON) {
		switch ($mode) {
			default:
				break;
		}
		
		return $mode;
	}
	/**
	 * Unserialize a JSON object back to a Cart_Container object.
	 * @param $mode
	 * @return unknown_type
	 */
	public static function unserialize($mode = Cart_Serializer::USE_JSON) {
		switch ($mode) {
			default:
				break;
		}
		
		return $mode;
	}
}
?>