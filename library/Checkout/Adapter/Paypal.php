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
		$order_id	= null;
		$adapter	= new Checkout_Adapter_Paypal_Client();
		$settings	= Zre_Config::getSettingsCached();
		$db		= Zend_Db_Table::getDefaultAdapter();

		$data = new stdClass();
		if (is_object($paymentData)) $paymentData = (array) $paymentData;
		
		$data->firstName	= $paymentData['firstName'];
		$data->lastName		= $paymentData['lastName'];
		$data->street1		= $paymentData['street1'];
		$data->street2		= $paymentData['street2'];
		$data->city		= $paymentData['city'];
		$data->state		= $paymentData['state'];
		$data->postalCode	= $paymentData['postalCode'];
		$data->country		= $paymentData['country'];
		$data->accountNumber	= $paymentData['accountNumber'];
		$data->creditCardType	= $paymentData['creditCardType'];
		$data->expirationMonth	= $paymentData['expirationMonth'];
		$data->expirationYear	= $paymentData['expirationYear'];
		$data->cvv2		= $paymentData['cvv2'];
		$data->ipAddress	= isset($_SERVER['REMOTE_ADDR']) ?
						$_SERVER['REMOTE_ADDR'] :
						null;

		$credit_card_type	= $data->creditCardType;
		$credit_card_number	= $data->accountNumber;
		$expiration_month	= $data->expirationMonth;
		$expiration_year	= $data->expirationYear;
		$cvv2			= $data->cvv2;
		$first_name		= $data->firstName;
		$last_name		= $data->lastName;
		$address1		= $data->street1;
		$address2		= $data->street2;
		$city			= $data->city;
		$state			= $data->state;
		$zip			= $data->postalCode;
		$country		= $data->country;
		$currency_code		= (string)$settings->site->currency;
		$ip_address		= $data->ipAddress;

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
			$currency_code,
			$ip_address
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

	public function getRequiredFields($options = null) {
//		$credit_card_type	= $paymentData->creditCardType;
//		$credit_card_number	= $paymentData->accountNumber;
//		$expiration_month	= $paymentData->expirationMonth;
//		$expiration_year	= $paymentData->expirationYear;
//		$cvv2			= $paymentData->cvv2;
//		$first_name		= $paymentData->firstName;
//		$last_name		= $paymentData->lastName;
//		$address1		= $paymentData->street1;
//		$address2		= $paymentData->street2;
//		$city			= $paymentData->city;
//		$state			= $paymentData->state;
//		$zip			= $paymentData->postalCode;
//		$country		= $paymentData->country;

		$us_ccards = array(
			'Visa'		=> 'Visa',
			'MasterCard'	=> 'MasterCard',
			'Discover'	=> 'Discover',
			'Amex'		=> 'American Express'
		);

		$uk_ccards = array(
			'Maestro'	=> 'Maestro',
			'Solo'		=> 'Solo',
			'MasterCard'	=> 'MasterCard',
			'Discover'	=> 'Discover',
			'Visa'		=> 'Visa'
		);

		$ca_ccards = array(
			'MasterCard'	=> 'MasterCard',
			'Visa'		=> 'Visa'
		);

		$ccard_types = $us_ccards;

		if (isset($options)) {
			if (isset($options['credit_card_country'])) {
				$ccard_country = $optinons['credit_card_country'];

				switch ($ccard_country) {
					case 'UK':
						$ccard_types = $uk_ccards;
						break;
					case 'CA':
						$ccard_types = $ca_ccards;
						break;
					case 'US': // Break statement omitted.
					default:
						$ccard_types = $us_ccards;
						break;
				}
			}
		}

		
		$expMonths = array(
			'01' => 'Jan',
			'02' => 'Feb',
			'03' => 'Mar',
			'04' => 'Apr',
			'05' => 'May',
			'06' => 'Jun',
			'07' => 'Jul',
			'08' => 'Aug',
			'09' => 'Sep',
			'10' => 'Oct',
			'11' => 'Nov',
			'12' => 'Dec'
		);

		$thisYear = date('Y');
		$expYears = array();

		for ($i = $thisYear; $i <= ($thisYear + 5); $i++) {
			$expYears[$i] = $i;
		}

		$countries = array(
			'ca' => 'Canada',
			'us' => 'United States',
			'uk' => 'United Kingdom'
		);

		$values = array(
			'accountNumber'		=> array(
							'label' => 'Card Number',
							'type' => 'text'
						),
			'creditCardType'	=> array(
							'label' => 'Card Type',
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
						),
		);

		return $values;
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