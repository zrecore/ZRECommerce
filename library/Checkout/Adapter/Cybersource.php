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
	 * NOTE: You must configure the Smart Authorization settings in the
	 * Business Center before you accept orders! See page seven (7) of the
	 * Cybersource 'Small_Business_API_Guide.pdf'

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
			$data = new stdClass();

			$data->firstName	= $paymentData['firstName'];
			$data->lastName		= $paymentData['lastName'];
			$data->street1		= $paymentData['street1'];
			$data->street2		= $paymentData['street2'];
			$data->city		= $paymentData['city'];
			$data->state		= $paymentData['state'];
			$data->postalCode	= $paymentData['postalCode'];
			$data->country		= $paymentData['country'];
			$data->accountNumber	= $paymentData['accountNumber'];
			$data->email		= $paymentData['email'];
			$data->expirationMonth	= $paymentData['expirationMonth'];
			$data->expirationYear	= $paymentData['expirationYear'];
			$data->ipAddress	= isset($_SERVER['REMOTE_ADDR']) ?
							$_SERVER['REMOTE_ADDR'] :
							null;

			$adapter = new Checkout_Adapter_Cybersource_Client();
			$settings = Zre_Config::getSettingsCached();

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
			$ccAuthService->run	= "true";
			$request->ccAuthService	= $ccAuthService;
			
			$billTo = new stdClass();
			$billTo->firstName	= $data->firstName;
			$billTo->lastName	= $data->lastName;
			$billTo->street1	= $data->street1;
			$billTo->street2	= $data->street2;
			$billTo->city		= $data->city;
			$billTo->state		= $data->state;
			$billTo->postalCode	= $data->postalCode;
			$billTo->country	= $data->country;
			$billTo->email		= $data->email;
			$billTo->ipAddress	= $data->ipAddress;
			$request->billTo	= $billTo;
			
			$card = new stdClass();
			$card->accountNumber	= $data->accountNumber;
			$card->expirationMonth	= $data->expirationMonth;
			$card->expirationYear	= $data->expirationYear;

			$request->card		= $card;
			
			$purchaseTotals = new stdClass();
			$purchaseTotals->currency	= (string)$settings->site->currency;
			$request->purchaseTotals	= $purchaseTotals;
			
			$items = array();
			
			foreach($cartContainer->getItems() as $cartItem) {
				$item = new stdClass();
				
				$cartItem = Cart_Container_Item::factory($cartItem);
				$item->unitPrice	= $cartItem->getCostOptions()->calculate();
				$item->quantity		= $cartItem->getQuantity();
				$item->id		= $cartItem->getSku();
				
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
				$db = Zend_Db_Table::getDefaultAdapter();

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

		} catch (Exception $e) {
			// Save the result to the log.
			Debug::logException($e);
			$result = null;
		}
		return $result;
	}

	public function getRequiredFields($options = null) {

		$vital_ccards = array(
			'Visa'			=> 'Visa',
			'MasterCard'		=> 'MasterCard',
			'American Express'	=> 'American Express',
			'Discover'		=> 'Discover'
		);

		$fdms_nashville_ccards = array(
			'Visa'			=> 'Visa',
			'MasterCard'		=> 'MasterCard'
		);

		$fdms_south_ccards = array(
			'Visa'			=> 'Visa',
			'MasterCard'		=> 'MasterCard',
			'American Express'	=> 'American Express',
			'Discover'		=> 'Discover'
		);

		$ccard_types = $vital_ccards;
		if (isset($options)) {
			if (isset($options['card_processor'])) {
				$card_processor = $options['card_processor'];
				switch ($card_processor) {
					case 'fdms_nashville':
						$ccard_types = $fdms_nashville_ccards;
						break;
					case 'fdms_south':
						$ccard_types = $fdms_south_ccards;
						break;
					case 'vital': // Break statement omitted.
					default:
						$ccard_types = $vital_ccards;
						break;

				}
			}
		}

		$thisYear = date('Y');
		$expYears = array();

		for ($i = 0; $i < 5; $i++) {
			$expYears[] = $thisYear + $i;
		}

		$countries = array(
			'ca' => 'Canada',
			'us' => 'United States',
			'uk' => 'United Kingdom'
		);
		
		$values = array(
			'creditCardType'	=> array(
							'label' => 'Card Type',
							'type' => $ccard_types
						),
			'accountNumber'	=> array(
							'label' => 'Card Number',
							'type' => $ccard_types
						),
			'expirationMonth'	=> array(
							'label' => 'Exp. Month',
							'type' => $expMonths
						),
			'expirationYear'	=> array(
							'label' => 'Exp. Year',
							'type' => $expYears
						),
			'cvv2'			=> array(
							'label' => 'CVV2',
							'type' => 'text'
						),
			'firstName'		=> array(
							'label' => 'First Name',
							'type' => 'text'
						),
			'lastName'		=> array(
							'label' => 'Last Name',
							'type' => 'text'
						),
			'street1'		=> array(
							'label' => 'Street',
							'type' => 'text'
						),
			'street2'		=> array(
							'label' => 'Street (Line 2)',
							'type' => 'text'
						),
			'city'			=> array(
							'label' => 'City',
							'type' => 'text'
						),
			'state'			=> array(
							'label' => 'State',
							'type' => 'text'
						),
			'postalCode'		=> array(
							'label' => 'Zip',
							'type' => 'text'
						),
			'country'		=> array(
							'label' => 'Country',
							'type' => $countries
						)
		);

		return $values;
	}

	public function getOptionalFields($options = null) {
		return null;
	}

	public function postProcess($data, $options = null) {

	}
}