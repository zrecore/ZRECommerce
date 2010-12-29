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
	
//	public function postProccessAction() {
//		//@todo Handle post-order processing here, such as from
//		//	Paypal's Instant Payment Notification, depending on
//		//	the info received.
//		//
//		//	Uses a pre-defined router!
//
//		$request = $this->getRequest();
//
//		$source = $request->getParam('source', null);
//		$source = preg_replace('[^a-zA-Z]','', $source);
//
//		$postProcessResult = false;
//		if (isset($source)) {
//			$postProcessResult = Checkout_Payment::postProcess($source, $data);
//		}
//
//		$this->view->postProcessResult = $postProcessResult;
//		$this->view->source = $source;
//	}

	public function downloadAction() {
	    $request = $this->getRequest();

	    $order_id = $request->getParam('order_id');
	    $product_id = $request->getParam('product_id');

	    $orders = new Zre_Dataset_Orders();
	    $ordersProduct = new Zre_Dataset_OrdersProducts();
	    $products = new Zre_Dataset_Product();
	    $productOptions = new Zre_Dataset_ProductOptions();

	    $order = $orders->read($order_id);

	    $message = '';

	    if ($order->count() > 0) {
		$orderDate = new Zend_Date($order->current()->order_date, 'yyyy-MM-dd HH:mm:ss');
		$today = new Zend_Date(null, 'yyyy-MM-dd HH:mm:ss');
		$cutOff = clone $today;
		$cutOff->subDay(1);
		$data = null;
		$message = '';
		
		if ($orderDate->isLater($cutOff) || $orderDate->equals($cutOff)) {

		    $data = $orders->listAll(
			    array(
				'o.order_id' => $order_id
			    ),
			    array(
				'setIntegrityCheck' => false,
				'from' => array(
				    'name' => array('o' => $orders->info('name')),
				    'cols' => array(
					'order_id'
				    )
				),
				'join' => array(
				    'name' => array('oP' => $ordersProduct->info('name')),
				    'cols' => array('product_id', 'unit_price', 'quantity'),
				    'cond' => 'oP.order_id = o.order_id'
				),
				'join ' => array(
				    'name' => array('p' => $products->info('name')),
				    'cols' => array('title', 'description', 'date_created', 'date_modified'),
				    'cond' => 'p.product_id = oP.product_id'
				),
				'leftJoin  ' => array(
				    'name' => array('pO' => $productOptions->info('name')),
				    'cols' => array('key', 'value'),
				    'cond' => "pO.product_id = p.product_id AND pO.key='download'"
				)
			    ),
			    false
		    );
		    $message = 'Here are your purchased downloads.';
		} else {
		    $message = 'Your download access has expired.';
		}

		$this->view->message = $message;
		$this->view->data = $data;
	    } else {
		// Invalid order ID
		$this->_redirect('/');
	    }
	}
}