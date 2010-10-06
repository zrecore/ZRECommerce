<?php
interface Cart_Container_Item_Validate_Interface {
	/**
	 * Validate the specified data. Throw an exception if need be.
	 * @param Cart_Container_Item_Abstract $data
	 * @return bool
	 */
	public function validate(Cart_Container_Item_Abstract $data);
}