<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Default
 * @subpackage Default_Shop
 * @category Controllers
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. All rights reserved.
 * @license Creative Commons license - See public/license.txt
 */
/**
 * ShopController - Browses product inventory.
 * 
 */
class ShopController extends Zend_Controller_Action {
	
	public function preDispatch() {
		$settings = Zre_Config::getSettingsCached();
		
		if (!Zre_Template::isHttps() && $settings->site->enable_ssl == 'yes') {
			$this->_redirect('https://' . $settings->site->url . '/shop/', array('exit' => true));
		}
	}
	/**
	 * The default action - show the product listing page
	 */
	public function indexAction() {

		$this->view->assign('disable_cache', 1);
		$this->view->assign('params', $this->getRequest()->getParams());
		
		$cssBase = substr( Zre_Template::baseCssTemplateUrl(), 1 );
		
		$this->view->assign('extra_css', array( $cssBase . '/components/content/product.listing.css' ));
		
		Zre_Registry_Session::set('selectedMenuItem', 'Shop');
		Zre_Registry_Session::save();
	}
	/**
	 * Product description view action.
	 *
	 */
	public function productAction() {
		// @todo Display product details.
		$this->view->assign('disable_cache', 1);
		$this->view->assign('enable_jquery', 1);
		
		$cssBase = substr( Zre_Template::baseCssTemplateUrl(), 1 );
		
		$this->view->assign('extra_css', array( $cssBase . '/components/content/product.css' ));
		$this->view->assign('params', $this->getRequest()->getParams());
		
		$productId = $this->getRequest()->getParam('id');
		if (!isset($productId) || !is_numeric($productId)) {
			$this->_redirect( '/shop/', array('exit' => true) );
		}
		
		Zre_Registry_Session::set('selectedMenuItem', 'Shop');
		Zre_Registry_Session::save();
	}
	
	public function cartAction() {
		// @todo Display cart
		
		$this->view->assign('disable_cache', 1);
		$this->view->assign('params', $this->getRequest()->getParams());
		
		Zre_Registry_Session::set('selectedMenuItem', 'Shop');
		Zre_Registry_Session::save();
	}
	
	public function updateAction() {
		$this->view->assign('disable_cache', 1);
		$this->_helper->layout->disableLayout();
		$this->_helper->getExistingHelper('ViewRenderer')->setNoRender(true);
		
		/**
		 * @todo Update cart here.
		 */
		
		$this->_redirect('/shop/cart/');
	}
	
	public function flushAction() {
		$this->view->assign('disable_cache', 1);
		$this->_helper->layout->disableLayout();
		$this->_helper->getExistingHelper('ViewRenderer')->setNoRender(true);
		
		/**
		 * Flush the cart, redirect to the shop index page.
		 */
		Cart::flushSession();
		
		$this->_redirect('/shop/');
	}
	
	public function addAction() {
		$t = Zend_Registry::get('Zend_Translate');
		
		$productId = $this->getRequest()->getParam('id', null);
		$productQuantity = $this->getRequest()->getParam('quantity');
		
		if ($productQuantity < 1) $productQuantity = 1;
		if (!isset($productId)) {
			$error = '<h3>' . $t->_('Cart Error') . '</h3><p>' . $t->_('invalid_product_id') . '</p>';
			$this->view->assign('content', $error);
			
		}
		$datasetProduct = new Zre_Dataset_Product();
		$product = $datasetProduct->read( (int)$productId )->current()->toArray();
		
		$productAllotment = $product['allotment'];
		$productPending = $product['pending'];
		$productSold = $product['sold'];
		$productLeft = $productAllotment - ($productPending + $productSold);
		$productPrice = $product['price'];
		$productWeight = $product['weight'];
		$productSize = $product['size'];
		$productPropertyId = $product['property_id'];
		
		/**
		 * @todo Grab product properties here
		 */
		$productProperties = array();
		
		$productTitle = $product['title'];
		$productDescription = $product['description'];
		
		if (isset($productId) && $productAllotment - ($productQuantity + $productPending + $productSold) >= 0 ) {
			// @todo Add item to cart (grab 'id' param)
			
			Cart::loadSession();
			
			$cart = Cart::getCartContainer();
			
			$costOptions = new Cart_Container_Item_Options_Cost(array($productPrice));
			$metricOptions = new Cart_Container_Item_Options_Metrics(array($productWeight));
			$detailOptions = new Cart_Container_Item_Options_Detail(
									array(	'desc' => $productDescription, 
											'weight' => $productWeight,
											'title' => $productTitle ));
									
			$cart->addItem( new Cart_Container_Item(	$productId, 
														$detailOptions, 
														$costOptions, 
														$metricOptions, 
														(int) $productQuantity, 
														null, // validators
														'') );
			$productDataset = new Zre_Dataset_Product();
			$productDataset->update(
				array(
					'pending' => $productPending + $productQuantity
				), 
				$productId
			);
			
			Cart::setCartContainer($cart);
			Cart::saveSession();
			
			// ...Display cart
			$this->_redirect('/shop/cart/');
		} else {
			$error = '<h3>' . $t->_('Cart Error') . '</h3><p>' . $t->_('product_message_sold_out') . '</p>';
			$this->view->assign('content', $error);
		}
		
		Zre_Registry_Session::set('selectedMenuItem', 'Shop');
		Zre_Registry_Session::save();
	}
	
	public function checkoutAction() {
		Cart::loadSession();
		
		$request = $this->getRequest();
		$billingSubmitted = $request->getParam('billingSubmitted', null);
		$cart = Cart::getCartContainer();
		$p = array();
		
		if (count($cart->getItems()) <= 0) $this->_redirect('/shop/checkout-empty');
		
		if (isset($billingSubmitted)) {
			$db = Zre_Db_Mysql::getInstance();
			
			$ordersDataset = new Zre_Dataset_Orders();
			$ordersProductDataset = new Zre_Dataset_OrdersProducts();
			$ordersCybersourceDataset = new Zre_Dataset_OrdersCybersource();
			$productDataset = new Zre_Dataset_Product();
			
			foreach($cart->getItems() as $cartItem) {
				$item = Cart_Container_Item::factory($cartItem);
				
				$p[$item->getSku()] = $productDataset->read($item->getSku())->current();
			}
			
			$checkout = new Checkout_Adapter_Cybersource();
			
			$data = new stdClass();
			
			$data->firstName = $request->getParam('firstName');
			$data->lastName = $request->getParam('lastName');
			$data->street1 = $request->getParam('street1');
			$data->street2 = $request->getParam('street2');
			$data->city = $request->getParam('city');
			$data->state = $request->getParam('state');
			$data->postalCode = $request->getParam('postalCode');
			$data->country = $request->getParam('country');
			$data->accountNumber = $request->getParam('accountNumber');
			$data->email = $request->getParam('email');
			$data->expirationMonth = $request->getParam('expirationMonth');
			$data->expirationYear = $request->getParam('expirationYear');
			$data->ipAddress = $_SERVER["REMOTE_ADDR"];
			
			$isValid = true;
			// @todo do some kind of field validation.
			if ($isValid == true) {
				$result = $checkout->pay($cart, $data);
				
				// Redirect to OK page.
				if (isset($result)) {
					try {
						$orders = array();
						$orderIds = array();
						
						$order = array(
							'decision' => $result->decision,
							'order_date' => new Zend_Db_Expr('NOW()')
						);
						
						$order_id = $ordersDataset->create($order);
						
						$ordersCybersource = array(
							'order_id' => $order_id,
							'decision' => $result->decision,
							'reason_code' => $result->reasonCode,
							'request_id' => $result->requestID,
							'request_token' => $result->requestToken,
							'currency' => $result->purchaseTotals->currency,
							'cc_auth_blob' => serialize($result->ccAuthReply)
						);
						
						$orders_cybersource_id = $ordersCybersourceDataset->create($ordersCybersource);
						
						foreach($cart->getItems() as $cartItem) {
							$item = Cart_Container_Item::factory($cartItem);
							$orderProduct = array(
								'order_id' => $order_id,
								'product_id' => $item->getSku(),
								'unit_price' => $item->getCostOptions()->calculate(),
								'quantity' => $item->getQuantity()
							);
							
							// Update our inventory audit.
							$prod = $p[$item->getSku()];
							$productDataset->update(
								array(
									'sold' => $prod->sold + $item->getQuantity(),
									'pending' => $prod->pending - $item->getQuantity(),
									'allotment' => $prod->allotment - $item->getQuantity()
								), 
								$db->quoteInto('product_id = ?', $item->getSku())
							);
							
							$ordersProductDataset->create($orderProduct);
						}
						
						// Let's keep the order ID secret to prevent someone from just guessing it.
						/**
						 * @todo Add this to the settings.xml
						 */
						$cryptSalt = 'salty.Pop!c0rN';
						
						$order_hash = crypt($order_id , $cryptSalt);
						Zre_Registry_Session::load();
						Zre_Registry_Session::set('shop.checkout.order_id', $order_id);
						Zre_Registry_Session::save();
						
						$this->_redirect('/shop/checkout-complete/order/' . rawurlencode( $order_hash ), array('exit' => true) );
						
					} catch (Exception $e) {
						Debug::logException($e);
						Debug::mail('Could not save a completed order:' . "\n" . print_r($result, true) . "\n" . print_r($data, true) . "\n" . print_r($cart, true));
						$this->_forward('checkout-error', 'shop', 'default', array('error' => $e));
//						$this->_redirect('/shop/checkout-error/error/could-not-save', array('exit' => true));
					}
				} else {
					$this->_redirect('/shop/checkout-error/error/no-result');
				}
			}
		}
	}
	
	public function checkoutErrorAction() {
		$request = $this->getRequest();
		$error = $request->getParam('error', null);
		
		$this->view->error = $error;
	}
	
	public function checkoutEmptyAction() {
		
	}
	
	public function checkoutCompleteAction() {
		Zre_Registry_Session::load();
		
		$isLoaded = Cart::loadSession();
		
		if (!$isLoaded) $this->_redirect('/shop', array('exit' => true));
		
		$request = $this->getRequest();
		$cryptSalt = 'salty.Pop!c0rN';
		
		$cart = Cart::getCartContainer();
		
		$orderHash = $request->getParam('order', null);
		if (Zre_Registry_Session::isRegistered('shop.checkout.order_id')) {
			$orderId = Zre_Registry_Session::get('shop.checkout.order_id');
		} else {
			$this->_redirect('/shop/', array('exit' => true));
		}
		
		if (isset($orderHash) && $orderHash == crypt($orderId, $cryptSalt) ) {
			Cart::flushSession();
			Cart::saveSession();
			
			Zre_Registry_Session::flush('shop.checkout.order_id');
			Zre_Registry_Session::save();
			
			Zend_Session::regenerateId();
			
			$this->view->assign('cart_container', $cart);
			$this->view->assign('order_id', $orderId);
		} else {
			// What? No security hash?
			$this->_redirect('/shop/', array('exit' => true));
		}
	}
}
