<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Plugin
 * @subpackage Product
 * @category Plugin
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * Zre_Ui_Widgets_Plugin_Product_Array - Display an array of products.
 *
 */
class Plugin_Product_Array implements Plugin_Abstract
{
	private $_options = null;
	public function __construct($options=null)
	{
		$this->_options = $options;
	}
	public function setOptions($options)
	{
		$this->_options = $options;
		return true;
	}
	public function getOptions()
	{
		return $this->_options;
	}
	public function __toString()
	{
		return $this->getHtml();
	}
	public function getHtml() {
		if (isset($this->_options['message']) )
		{
			return '<b>'.$this->_options['message'].'</b>';
		} else {
			return '<b>'."[Product Array]".'</b>';
		}
	}
	
	public function getScript() {
		
	}
	/**
	 * Outputs the configuration form
	 *
	 * @return Zend_Form
	 */
	public function getForm() {
		
	}
	
	/**
	 * Validate submitted values.
	 *
	 * @param array $params
	 */
	public function validateForm( $params ) {
		
	}
	
	/**
	 * Process the form values.
	 *
	 * @param array $params
	 */
	public function processForm( $params ) {
		
	}
	/**
	 * Install the plugin
	 *
	 */
	public function install() {
		
	}
	/**
	 * Uninstall the plugin
	 *
	 */
	public function uninstall() {
		
	}
}
?>