<?php
class Zre_Cart_Validate_CartQuantity implements Cart_Container_Item_Validate_Interface {
	
	public function validate(Cart_Container_Item_Abstract $cartContainerItem) {
		$cartItemSku = $cartContainerItem->getSku();
		$cartItemQuantity = $cartContainerItem->getQuantity();
		
		$product = new Zre_Dataset_Product();
		$datasetData = $product->read($cartItemSku)->current()->toArray();
		
		$datasetAllotment = $datasetData['allotment'];
		$datasetPending = $datasetData['pending'];
		$datasetSold = $datasetData['sold'];
		
		$datasetQuantityLeft = $datasetAllotment - ($datasetPending + $datasetSold);
		
		// ... If the requested amount exceeds whats left, give them only whats left
		// ... WARNING: this must be checked again upon checkout to prevent over-sell.
		if ($cartItemQuantity > $datasetQuantityLeft) {
			$cartItemQuantity = $datasetQuantityLeft;
			$cartContainerItem->setQuantity($cartItemQuantity);
		// ... If the requested amount is less than zero, set it to zero.
		} elseif ($cartItemQuantity < 0) {
			$cartItemQuantity = 0;
			$cartContainerItem->setQuantity($cartItemQuantity);
		}
		
		return true;
	}
}