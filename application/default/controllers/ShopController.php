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
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */
/**
 * ShopController - Browses product inventory.
 * 
 */
class ShopController extends Zend_Controller_Action {
	
	public function preDispatch() {
		$settings = Zre_Config::getSettingsCached();
		
		if (!Zre_Template::isHttps() && $settings->site->enable_ssl == 'yes') {
			$this->_redirect('https://' . $settings->site->url . '/shop/');
		}

		// @todo Use settings.xml to define the checkout adapter.
		Checkout_Payment::setAdapter( new Checkout_Adapter_Cybersource() );
	}
	/**
	 * The default action - show the product listing page
	 */
	public function indexAction() {
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
		
		$cssBase = substr( Zre_Template::baseCssTemplateUrl(), 1 );
		
		$this->view->assign('extra_css', array( $cssBase . '/components/content/product.css' ));
		$this->view->assign('params', $this->getRequest()->getParams());
		
		$productId = $this->getRequest()->getParam('id');
		if (!isset($productId) || !is_numeric($productId)) {
			$this->_redirect( '/shop/');
		}
		
		Zre_Registry_Session::set('selectedMenuItem', 'Shop');
		Zre_Registry_Session::save();
	}
	
	public function cartAction() {
		
		$this->view->assign('params', $this->getRequest()->getParams());
		
		Zre_Registry_Session::set('selectedMenuItem', 'Shop');
		Zre_Registry_Session::save();
	}
	
	public function updateAction() {
		$this->_helper->layout->disableLayout();
		$this->_helper->getExistingHelper('ViewRenderer')->setNoRender(true);
		
		/**
		 * @todo Update cart here.
		 */
		
		$this->_redirect('/shop/cart/');
	}
	
	public function flushAction() {
		$this->_helper->layout->disableLayout();
		$this->_helper->getExistingHelper('ViewRenderer')->setNoRender(true);
		
		/**
		 * Flush the cart, redirect to the shop index page.
		 */
		Cart::loadSession();
		
		$cart = Cart::getCartContainer();
		echo print_r($cart, true);
		if (isset($cart) && count($cart->getItems()) > 0) {
			$productDataset = new Zre_Dataset_Product();
			foreach($cart->getItems() as $cartItem) {
				$item = Cart_Container_Item::factory($cartItem);

				$p = $productDataset->read($item->getSku())->current();
				$p->pending -= $item->getQuantity();
				$p->save();

				unset($p);
			}
		}
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
					'title' => $productTitle )
			);
									
			$cart->addItem( new Cart_Container_Item(
				$productId,
				$detailOptions,
				$costOptions,
				$metricOptions,
				(int) $productQuantity,
				null, // validators
				''
			));
			
			$productDataset = new Zre_Dataset_Product();

			$p = $productDataset->read($productId)->current();
			$p->pending += $productQuantity;
			$p->save();
			
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

		$settings = Zre_Config::getSettingsCached();

		$request = $this->getRequest();
		$billingSubmitted = $request->getParam('billingSubmitted', null);
		$cart = Cart::getCartContainer();
		$p = array();

		$adapter = isset($settings->merchant->adapter) ?
				$settings->merchant->adapter :
				'Cybersource';
		$adapterClass = 'Checkout_Adapter_' . $adapter;
		
		Checkout_Payment::setAdapter(new $adapterClass);
		
		$fields = Checkout_Payment::getRequiredFields();

		if (count($cart->getItems()) <= 0) $this->_redirect('/shop/checkout-empty');
		
		if (isset($billingSubmitted)) {
			$db = Zre_Db_Mysql::getInstance();
			
			$productDataset = new Zre_Dataset_Product();
			
			foreach($cart->getItems() as $cartItem) {
				$item = Cart_Container_Item::factory($cartItem);

				$p = $productDataset->read($item->getSku())->current();

				if ($p->pending <= 0) {
					// ...Something doesn't add up!
					// ...Don't allow this item.

					$cart->removeItem($p->product_id);
				}

				unset($p);
			}
			
			Cart::setCartContainer($cart);
			Cart::saveSession();

			if (count($cart->getItems()) <= 0) {
				// Emptied cart, no valid items.
				$this->_redirect('/shop/cart/');
			}
			
			$keys = array_keys($fields);
			$params = array();

			foreach($keys as $k) {
				$params[$k] = $request->getParam($k, null);
			}
			
			$isValid = true;
			// @todo do some kind of field validation.
			if ($isValid == true) {
				$order_id = Checkout_Payment::pay($cart, $params);
				
				// Redirect to OK page.
				if (isset($order_id) && is_numeric($order_id)) {
					try {
						// Let's keep the order ID secret to prevent someone from just guessing it.
						/**
						 * @todo Add this to the settings.xml
						 */
						$cryptSalt = isset($settings->site->cryptographicSalt) ?
						$settings->site->cryptographicSalt :
						'salty.Pop!c0rN';
						
						$order_hash = urlencode(base64_encode( crypt($order_id , $cryptSalt) ));
						Zre_Registry_Session::load();
						Zre_Registry_Session::set('shop.checkout.order_id', $order_id);
						Zre_Registry_Session::save();
						
						$this->_redirect('/shop/checkout-complete/order/' . $order_hash, array('exit' => true) );
						
					} catch (Exception $e) {
						Debug::logException($e);
						Debug::mail('Could not save a completed order:' . "\n" . print_r($result, true) . "\n" . print_r($data, true) . "\n" . print_r($cart, true));
						$this->_forward('checkout-error', 'shop', 'default', array('error' => $e));
					}
				} elseif (isset($order_id) && is_string ($order_id)) {
				    $redir = $order_id;

				    $this->_redirect($redir);
				} else {
					$this->_redirect('/shop/checkout-error/error/no-result');
				}
			}
		}

		// ...Assign the adapter's required fields to our view.
		$this->view->fields = $fields;
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
		$settings = Zre_Config::getSettingsCached();

		$request = $this->getRequest();
		$cryptSalt = isset($settings->site->cryptographicSalt) ?
					(string)$settings->site->cryptographicSalt :
					'salty.Pop!c0rN';
		
		$cart = Cart::getCartContainer();
		
		$orderHash = $request->getParam('order', null);
		$orderHashReg = null;
		if (Zre_Registry_Session::isRegistered('shop.checkout.order_id')) {
			$orderId = Zre_Registry_Session::get('shop.checkout.order_id');

			$orderHashReg = Zre_Registry_Session::get('shop.checkout.order_hash');
		} else {
			$this->_redirect('/shop/');
			return;
		}
		
		if (isset($orderHash) && isset($orderHashReg) && $orderHashReg == $orderHash ) {

			Cart::flushSession();
			Cart::saveSession();
			
			Zre_Registry_Session::flush('shop.checkout.order_id');
			Zre_Registry_Session::save();
			
			Zend_Session::regenerateId();
			
			$this->view->assign('cart_container', $cart);
			$this->view->assign('order_id', $orderId);
		} else {
			// What? No security hash?
			$this->_redirect('/shop/');
			return;
		}
	}

	public function paymentCompleteAction() {

	    $action = 'checkout-complete';
	    $controller = 'shop';
	    $module = 'default';
	    
	    $settings = Zre_Config::getSettingsCached();

	    $request = $this->getRequest();

	    Cart::loadSession();
	    $cart = Cart::getCartContainer();
	    
	    $confirmed = $request->getParam('confirm_payment', false);
	    
	    $adapter = isset($settings->merchant->adapter) ?
			    $settings->merchant->adapter :
			    'Cybersource';

	    $adapterClass = 'Checkout_Adapter_' . $adapter;
	    $adapter = new $adapterClass;
	    Checkout_Payment::setAdapter($adapter);

	    $data = $adapter->getPostProcessFields($cart, $request->getParams());

	    if ($confirmed == true) {
		$order_id = Checkout_Payment::pay($cart, $data);

		// Redirect to OK page.
		if (isset($order_id)) {
			try {
				// Let's keep the order ID secret to prevent someone from just guessing it.
				$cryptSalt = isset($settings->site->cryptographicSalt) ?
				    (string)$settings->site->cryptographicSalt :
				    'salty.Pop!c0rN';

				$order_hash = base64_encode( crypt($order_id , $cryptSalt) );

				Zre_Registry_Session::load();
				Zre_Registry_Session::set('shop.checkout.order_id', $order_id);
				Zre_Registry_Session::set('shop.checkout.order_hash', $order_hash);
				Zre_Registry_Session::save();

				$this->_redirect('/shop/checkout-complete/order/' . $order_hash );

			} catch (Exception $e) {
				Debug::logException($e);
				Debug::mail('Could not save a completed order:' . "\n" . print_r($result, true) . "\n" . print_r($data, true) . "\n" . print_r($cart, true));
				$this->_forward('checkout-error', 'shop', 'default', array('error' => $e));
			}
		} else {
			$this->_redirect('/shop/checkout-error/error/no-result');
		}
	    } else {
		$this->view->data = $data;
		$this->view->cart = $cart;
	    }
	}

	public function paymentCancelledAction() {

	}
}
