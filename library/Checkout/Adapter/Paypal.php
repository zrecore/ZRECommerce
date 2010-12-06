<?php
class Checkout_Adapter_Paypal implements Checkout_Adapter_Interface {
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
	 * @param Cart_Container $cartContainer
	 * @param mixed $paymentData
	 * @return mixed Return the order ID on success, or null on failure.
	 */
	public function pay(Cart_Container $cartContainer, $paymentData)
	{
		// @todo Implement using Paypal NVP here!
		/**
		 * @todo Add a method called getRequiredFields() to auto-gen
		 * any required fields directly onto the checkout form,
		 * depending on what adapter the products specify.
		 *
		 * Fields can be merged from multiple adapters, if products use
		 * different adapters per product.
		 */
		$order_id = null;
		$adapter = new Checkout_Adapter_Paypal_Client();
		$settings = Zre_Config::getSettingsCached();
		$db = Zend_Db_Table::getDefaultAdapter();

		$credit_card_type	= $paymentData->creditCardType;
		$credit_card_number	= $paymentData->accountNumber;
		$expiration_month	= $paymentData->expirationMonth;
		$expiration_year	= $paymentData->expirationYear;
		$cvv2			= $paymentData->cvv2;
		$first_name		= $paymentData->firstName;
		$last_name		= $paymentData->lastName;
		$address1		= $paymentData->street1;
		$address2		= $paymentData->street2;
		$city			= $paymentData->city;
		$state			= $paymentData->state;
		$zip			= $paymentData->postalCode;
		$country		= $paymentData->country;
		$currency_code		= (string)$settings->site->currency;

		$amount			= $cartContainer->getTotal();

		$reply = $adapter->doDirectPayment(
			$amount,
			$credit_card_type,
			$credit_card_number,
			$expiration_month,
			$expiration_year,
			$cvv2,
			$first_name,
			$last_name,
			$address1,
			$address2,
			$city,
			$state,
			$zip,
			$country,
			$currency_code
		);

		if ($reply->isSuccessful()) {
			$data = $adapter->parse($reply->getBody());
			
			// ...Save our results to the database
			if ($data->ACK == 'Success') {
				
				$ordersDataset = new Zre_Dataset_Orders();
				$ordersProductDataset = new Zre_Dataset_OrdersProducts();
				$ordersPaypalDataset = new Zre_Dataset_OrdersPaypal();
				$productDataset = new Zre_Dataset_Product();

				$orderIds = array();

				$result = $data;

				$order = array(
					'decision' => $result->ACK,
					'order_date' => new Zend_Db_Expr('NOW()'),
					'merchant' => 'paypal'
				);

				$order_id = $ordersDataset->create($order);

				$ordersPaypalData = array(
					'order_id'	 => $order_id,
					'transaction_id' => $result->TRANSACTIONID,
					'decision'	 => $result->ACK,
					'version'	 => $result->VERSION,
					'build'		 => $result->BUILD,
					'currency'	 => $result->CURRENCYCODE,
					'avs_code'	 => $result->AVSCODE,
					'cvv2_match'	 => $result->CVV2MATCH,
					'time_stamp'	 => $result->TIMESTAMP,
					'response_blob'	 => serialize($result)
				);

				$orders_paypal_id = $ordersPaypalDataset->create($ordersPaypalData);

				foreach($cartContainer->getItems() as $cartItem) {
					$item = Cart_Container_Item::factory($cartItem);
					$orderProduct = array(
						'order_id'	=> $order_id,
						'product_id'	=> $item->getSku(),
						'unit_price'	=> $item->getCostOptions()->calculate(),
						'quantity'	=> $item->getQuantity()
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
			} else {
				throw new Exception(__CLASS__ . "::pay() failed.\n\n" . $reply->getBody());
			}
			
		} else {
			throw new Exception(__CLASS__ . "::pay() failed.\n\n" . $reply->getBody());
		}
		return $order_id;
	}

	public function postProcess($data) {

	}
}