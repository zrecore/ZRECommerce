<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Default
 * @subpackage Default_Orders
 * @category Controllers
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. All rights reserved.
 * @license Creative Commons license - See public/license.txt
 */

/**
 * OrdersController
 * 
 */
class OrdersController extends Zend_Controller_Action {
	public function preDispatch() {
		$settings = Zre_Config::getSettingsCached();
		/**
		 * Only allow over a secure connection.
		 */
		if (!Zre_Template::isHttps() && $settings->site->enable_ssl == 'yes') {
			$this->_redirect('https://' . $settings->site->url . '/orders/', array('exit' => true));
		}
	}
	/**
	 * The default action - show the orders page
	 */
	public function indexAction() {
		// @todo Send cart through external order processing system.
		// @todo Poll for external order processor signal
		// @todo Display waiting .pthml untill order process signal returns ok
		// @todo If no ok signal is returned, display appropriate error instead.
		Zre_Registry_Session::set('selectedMenuItem', '');
		Zre_Registry_Session::save();
	}
	
	public function processAction() {
		// @todo If an ok signal was returned, add order to local db records.
		// @todo Display confirmation page, with printable view.
		Zre_Registry_Session::set('selectedMenuItem', '');
		Zre_Registry_Session::save();
	}
}