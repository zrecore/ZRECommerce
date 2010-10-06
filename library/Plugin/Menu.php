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
 * Plugin_Menu - A plugin to display a menu inline.
 *
 */
class Plugin_Menu implements Plugin_Abstract
{
	private $_options = null;
	private $_entries = array();
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
		$output = $this->getHtml();
		return $output;
	}
	public function getHtml() {
		$output = '';
		
		$zendAuth = Zend_Auth::getInstance();
		$zendAuth->setStorage(new Zend_Auth_Storage_Session());
		if ($zendAuth->hasIdentity()) {
			$output = new Zre_Ui_Menu_Admin();
		} else {
			$output = new Zre_Ui_Menu_Shop();
		}
		
		$output = $output->__toString();
		return $output;
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