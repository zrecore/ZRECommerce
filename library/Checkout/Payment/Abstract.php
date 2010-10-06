<?php
class Checkout_Payment_Abstract {
	private $_fields = array(
	);
	public function validateFields($data) {
		
	}
	public function getFields() {
		
	}
	public function authenticate($data) {
		
	}
	/**
	 * @method 
	 * @param unknown_type $name
	 */
	public function __get($name) {
		return isset($this->_fields[$name]) ? $this->_fields[$name] : null;
	}
	
	public function __set($name, $value) {
		$this->_fields[$name] = $value;
	}
}