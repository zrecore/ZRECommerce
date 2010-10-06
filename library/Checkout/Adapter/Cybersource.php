<?php
class Checkout_Adapter_Cybersource {
	private static $_client;
	
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
	 * @param Checkout_Payment_Interface $paymentData
	 * @return object|null Returns the cybersource reply, or null on failure.
	 */
	public function pay($cartContainer, $paymentData)
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
			
			return $reply;
		} catch (Exception $e) {
			// Save the result to the database.
			Debug::logException($e);
			$result = null;
		}
		return $result;
	}
}