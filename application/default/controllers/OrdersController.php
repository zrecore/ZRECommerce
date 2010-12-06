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
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
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
	
	public function postProccessAction() {
		//@todo Handle post-order processing here, such as from
		//	Paypal's Instant Payment Notification, depending on
		//	the info received.
		//	
		//	Uses a pre-defined router!

		$request = $this->getRequest();

		$source = $request->getParam('source', null);
		$source = preg_replace('[^a-zA-Z]','', $source);

		$postProcessResult = false;
		if (isset($source)) {
			$postProcessResult = Checkout_Payment::postProcess($source, $data);
		}

		$this->view->postProcessResult = $postProcessResult;
		$this->view->source = $source;
	}
}