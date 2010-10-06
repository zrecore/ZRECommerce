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
 * Plugin_Menu_Main - Main menu navigation.
 *
 */
class Plugin_Menu_Main implements Plugin_Abstract {
	private $_options = null;
	
	public function __construct($options=null) {
		
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
	
	public function __toString() {
		return $this->getHtml();
	}
	public function getHtml() {
		try {
		$zendAuth = Zend_Auth::getInstance();
		$zendAuth->setStorage(new Zend_Auth_Storage_Session());
		$module =  Zend_Controller_Front::getInstance()->getRequest()->getParam('module');
		$selectedMenuItem = '';
		
		if (Zre_Registry_Session::isRegistered('selectedMenuItem')) {
			$selectedMenuItem = Zre_Registry_Session::get('selectedMenuItem'); 
		}
		
		if ($zendAuth->hasIdentity() && $module == 'admin') {
			$adminMenu = new Zre_Ui_Menu_Admin(null, null, null, $selectedMenuItem);
			$output = $adminMenu->__toString();
		} else {
			
			$shopMenu = new Zre_Ui_Menu_Shop(null, null, null, $selectedMenuItem);
			$searchMenu = new Plugin_Menu_Search();
			$viewCartButton = new Plugin_Menu_View_Cart();
			$breadCrumbMenu = new Plugin_Menu_Breadcrumb();
			
			$output = $shopMenu->__toString() . $searchMenu->__toString() . $viewCartButton->__toString() . $breadCrumbMenu->__toString();
		}
		} catch (Exception $e) {
			$output = print_r($e, true);
		}
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