<?php
abstract class Cart_Container_Item_Options_Abstract {
	private $_items = array();
	
	public function __construct($items) {
		$this->_items = $items;
	}
	public function items() {
		return $this->_items;
	}
	
	public function get($key) {
		return $this->_items[$key];
	}
	
	public function set($key, $value) {
		$this->_items[$key] = $value;
	}
	
	public function calculate() {
		$total = 0;
		foreach($this->_items as $value) {
			$total += $value;
		}
		
		return $total;
	}
}