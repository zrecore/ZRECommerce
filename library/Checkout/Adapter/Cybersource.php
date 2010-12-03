<?php
class Checkout_Adapter_Cybersource implements Checkout_Adapter_Interface {
	private $_lastReply = null;

	public function getLastReply()
	{
		return $this->_lastReply;
	}
	/**
	 * Calculate the gross total of all items
	 * @param Cart_Container The cart to calculate.
	 * @return float The total.
	 */
	public function calculate(Cart_Container $cartContainer)
	{
		return $cartContainer->getTotal();
	}
	/**
	 * Charge the total using the specified payment method.
	 * @param Cart_Container $cartContainer The items to pay for.
	 * @param mixed $paymentData
	 * @return mixed Returns the order ID on success, or null on failure.
	 */
	public function pay(Cart_Container $cartContainer, $paymentData)
	{
		/**
		 * Get total.
		 * Get Payment info.
		 * Get Shipping info.
		 * Run transaction.
		 */
		try {
			
			$adapter = new Checkout_Adapter_Cybersource_Client();
			
			$request = new stdClass();
			
			$request->merchantID = $adapter->getMerchantId();
			
			// Before using this example, replace the generic value with your own.
			$request->merchantReferenceCode = $adapter->getMerchantId();
			
			// To help us troubleshoot any problems that you may encounter,
			    // please include the following information about your PHP application.
			$request->clientLibrary = "PHP";
			        $request->clientLibraryVersion = phpversion();
			        $request->clientEnvironment = php_uname();
			
			// This section contains a sample transaction request for the authorization
			    // service with complete billing, payment card, and purchase (two items) information.
			$ccAuthService = new stdClass();
			$ccAuthService->run = "true";
			$request->ccAuthService = $ccAuthService;
			
			$billTo = new stdClass();
			$billTo->firstName = $paymentData->firstName;
			$billTo->lastName = $paymentData->lastName;
			$billTo->street1 = $paymentData->street1;
			$billTo->street2 = $paymentData->street2;
			$billTo->city = $paymentData->city;
			$billTo->state = $paymentData->state;
			$billTo->postalCode = $paymentData->postalCode;
			$billTo->country = $paymentData->country;
			$billTo->email = $paymentData->email;
			$billTo->ipAddress = $paymentData->ipAddress;
			$request->billTo = $billTo;
			
			$card = new stdClass();
			$card->accountNumber = $paymentData->accountNumber;
			$card->expirationMonth = $paymentData->expirationMonth;
			$card->expirationYear = $paymentData->expirationYear;
			$request->card = $card;
			
			$purchaseTotals = new stdClass();
			$purchaseTotals->currency = "USD";
			$request->purchaseTotals = $purchaseTotals;
			
			$items = array();
			
			foreach($cartContainer->getItems() as $cartItem) {
				$item = new stdClass();
				
				$cartItem = Cart_Container_Item::factory($cartItem);
				$item->unitPrice = $cartItem->getCostOptions()->calculate();
				$item->quantity = $cartItem->getQuantity();
				$item->id = $cartItem->getSku();
				
				$items[] = $item;
			}
			
			$request->item = $items;
			
			$reply = $adapter->runTransaction($request);
			
			// This section will show all the reply fields.
			// var_dump($reply);
			
//			// To retrieve individual reply fields, follow these examples.
//			printf( "decision = $reply->decision<br>" );
//			printf( "reasonCode = $reply->reasonCode<br>" );
//			printf( "requestID = $reply->requestID<br>" );
//			printf( "requestToken = $reply->requestToken<br>" );
//			printf( "ccAuthReply->reasonCode = " . $reply->ccAuthReply->reasonCode . "<br>");
			
			if ($reply->decision == 'ACCEPT')
			{
				$db = Zre_Db_Mysql::getInstance();

				$ordersDataset = new Zre_Dataset_Orders();
				$ordersProductDataset = new Zre_Dataset_OrdersProducts();
				$ordersCybersourceDataset = new Zre_Dataset_OrdersCybersource();
				$productDataset = new Zre_Dataset_Product();

				$orderIds = array();
				$result = $reply;
				$order = array(
					'decision' => $result->decision,
					'order_date' => new Zend_Db_Expr('NOW()'),
					'merchant' => 'cybersource'
				);

				$order_id = $ordersDataset->create($order);

				$ordersCybersource = array(
					'order_id' => $order_id,
					'decision' => $result->decision,
					'reason_code' => $result->reasonCode,
					'request_id' => $result->requestID,
					'request_token' => $result->requestToken,
					'currency' => isset($result->purchaseTotals) ?
							$result->purchaseTotals->currency :
							'',
					'cc_auth_blob' => serialize($result->ccAuthReply)
				);

				$orders_cybersource_id = $ordersCybersourceDataset->create($ordersCybersource);

				foreach($cartContainer->getItems() as $cartItem) {
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

				$result = $order_id;
			} else {
				$result = null;
			}

			return $result;
		} catch (Exception $e) {
			// Save the result to the database.
			Debug::logException($e);
			$result = null;
		}
		return $result;
	}
}