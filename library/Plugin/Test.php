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
 * Plugin_Test - A sample plugin.
 *
 */
class Plugin_Test implements Plugin_Abstract
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
	public function getScript() {
		return '';
	}
	public function getHtml() {
		if (isset($this->_options['message']) ) {
			return '<b>'.$this->_options['message'].'</b>';
		} else {
			return '<b>'."[Test]".'</b>';
		}
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