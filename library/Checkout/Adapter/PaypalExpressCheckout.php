<?php

class Checkout_Adapter_PaypalExpressCheckout implements Checkout_Adapter_Interface {

    /**
     * Calculate the gross total of all items
     * @param Cart_Container The cart to calculate.
     * @return float The total.
     */
    public function calculate(Cart_Container $cartContainer) {
	return $cartContainer->getTotal();
    }

    /**
     * Charge the total using the specified payment method.
     * @param Cart_Container $cartContainer
     * @param mixed $paymentData
     * @return mixed Return the order ID on success, or null on failure.
     */
    public function pay(Cart_Container $cartContainer, $paymentData) {

	$order_id = null;
	$adapter = new Checkout_Adapter_Paypal_Client();
	$settings = Zre_Config::getSettingsCached();
	$db = Zend_Db_Table::getDefaultAdapter();

	$data = new stdClass();
	if (is_object($paymentData))
	    $paymentData = (array) $paymentData;

	$amount = $cartContainer->getTotal();
	/**
	 * @todo Finish implementing express checkout, dynamically
	 * add production or dev urls below using settings.xml
	 */
	if (empty($paymentData['token']) || empty($paymentData['PayerID'])) {

	    $baseUrl = ((string) $settings->site->enable_ssl == 'yes' ? 'https://' : 'http://') . ((string) $settings->site->url);
	    $returnURL = $baseUrl . '/shop/payment-complete/';
	    $cancelURL = $baseUrl . '/shop/payment-cancelled/';

	    // ...Perform an authorization.
	    $reply = $adapter->ecSetSexpressCheckout($amount, $returnURL, $cancelURL, $currency_code);

	    if ($reply->isSuccessful()) {
		$data = $adapter->parse($reply->getBody());

		if (strtoupper($data->ACK) == 'SUCCESS' || strtoupper($data->ACK) == 'SUCCESSWITHWARNING') {
		    $token = urldecode($data->TOKEN);
//			    $payPalURL = "https://www.paypal.com/webscr&cmd=_express-checkout&token=$token";

		    $payPalURL = (string) $settings->merchant->paypal->api_expresscheckout_uri;
		    $payPalURL .= '?&cmd=_express-checkout&token=' . urlencode($token);
//			    if("sandbox" === $environment || "beta-sandbox" === $environment) {
//				    $payPalURL = "https://www.$environment.paypal.com/webscr&cmd=_express-checkout&token=$token";
//			    }
		    header("Location: $payPalURL");
		} else {
		    throw new Exception('Payment failed.');
		}
	    }
	} else {
	    $token = $paymentData['token'];
	    $payer_id = $paymentData['PayerID'];

	    $currency_code = (string) $settings->site->currency;

	    $payment_amount = $amount;

	    // ...Perform a capture of funds.
	    $reply = $adapter->ecDoExpressCheckout($token, $payer_id, $payment_amount, $currency_code);
	}

	if ($reply->isSuccessful()) {
	    $data = $adapter->parse($reply->getBody());

	    // ...Save our results to the database
	    if (strtoupper($data->ACK) == 'SUCCESS' || strtoupper($data->ACK) == 'SUCCESSWITHWARNING') {

		$ordersDataset = new Zre_Dataset_Orders();
		$ordersProductDataset = new Zre_Dataset_OrdersProducts();
		$ordersPaypalExpressDataset = new Zre_Dataset_OrdersPaypalExpress();
		$productDataset = new Zre_Dataset_Product();

		$orderIds = array();

		$result = $data;

		$correlationId = $result->CORRELATIONID;

		$reply = $adapter->ecDoExpressCheckout($token, $payer_id, $payment_amount, $currency_code);

		if ($reply->isSuccessful()) {
		    $data = $adapter->parse($reply->getBody());
		    $order = array(
			'decision' => $result->ACK,
			'order_date' => new Zend_Db_Expr('NOW()'),
			'merchant' => 'paypalExpress'
		    );

		    $order_id = $ordersDataset->create($order);

		    $timestamp = new Zend_Date($result->TIMESTAMP, 'yyyy-MM-dd HH:mm:ss');
		    $orderTime = new Zend_Date($result->ORDERTIME, 'yyyy-MM-dd HH:mm:ss');

		    $ordersPaypalExpressData = array(
			'order_id' => $order_id,
			'token' => $result->TOKEN,
			'timestamp' => $timestamp->get('yyyy-MM-dd HH:mm:ss'),
			'correlation_id' => $result->CORRELATIONID,
			'decision' => $result->ACK,
			'version' => $result->VERSION,
			'build' => $result->BUILD,
			'transaction_id' => $result->TRANSACTIONID,
			'transaction_type' => $result->TRANSACTIONTYPE,
			'payment_type' => $result->PAYMENTTYPE,
			'order_time' => $orderTime->get('yyyy-MM-dd HH:mm:ss'),
			'amt' => $result->AMT,
			'fee_amt' => $result->FEEAMT,
			'tax_amt' => $result->TAXAMT,
			'currency' => $result->CURRENCYCODE,
			'payemnt_status' => $result->PAYMENTSTATUS,
			'pending_reason' => $result->PENDINGREASON,
			'reason_code' => $result->REASONCODE
		    );

		    $orders_paypal_id = $ordersPaypalExpressDataset->create($ordersPaypalExpressData);

		    foreach ($cartContainer->getItems() as $cartItem) {
			$item = Cart_Container_Item::factory($cartItem);
			$orderProduct = array(
			    'order_id' => $order_id,
			    'product_id' => $item->getSku(),
			    'unit_price' => $item->getCostOptions()->calculate(),
			    'quantity' => $item->getQuantity()
			);

			// Update our inventory audit.
			$prod = $productDataset->read($item->getSku())->current();

			$productDataset->update(
				array(
				    'sold' => $prod->sold + $item->getQuantity(),
				    'allotment' => $prod->allotment - $item->getQuantity()
				),
				$db->quoteInto('product_id = ?', $item->getSku())
			);

			$ordersProductDataset->create($orderProduct);
		    }
		}
	    } else {
		throw new Exception(__CLASS__ . "::pay() failed.\n\n" . print_r($adapter->parse($reply->getBody()), true));
	    }
	} else {
	    throw new Exception(__CLASS__ . "::pay() failed.\n\n" . $reply->getBody());
	}
	return $order_id;
    }

    public function getRequiredFields($options = null) {
	$settings = Zre_Config::getSettingsCached();
	$values = array(
	    'token' => array(
		'type' => 'hidden',
		'value' => ''
	    ),
	    'payerID' => array(
		'type' => 'hidden'
	    ),
	    'checkout_button' => array(
		'label' => 'Click to continue',
		'type' => 'image_link',
		'url' => 'https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif',
		'href' => '/shop/checkout/?billingSubmitted=1'
	    )
	);
	return $values;
    }

    /**
     *
     * @param Cart_Container $cart
     * @param array $request
     */
    public function getPostProcessFields($cart, $request) {
	$data = null;
	if (is_array($request)) {
	    $data = (object) $request;
	} else {
	    throw new Exception('Request must be an array.');
	}

	$reply = array(
	    'token' => $data->token,
	    'PayerID' => $data->PayerID,
	    'Amt' => $cart->getTotal()
	);

	return $reply;
    }

    /**
     * There are no optional fields for this adapter.
     * @param array|null $options
     * @return array|null No optional fields for this adapter.
     */
    public function getOptionalFields($options = null) {
	// ...No optional fields.
	return null;
    }

    public function postProcess($data, $options = null) {
	
    }

}