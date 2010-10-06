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
 * Cart_Container_Item_Options_Detail - This class holds a collection of 
 * 'detail' strings.
 *
 */
class Cart_Container_Item_Options_Detail extends Cart_Container_Item_Options_Abstract {
	public function calculate() {
		return parent::items();
	}
}