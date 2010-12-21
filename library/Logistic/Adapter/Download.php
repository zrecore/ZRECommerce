<?php

class Logistic_Adapter_Download implements Logistic_Adapter_Interface
{
	/**
	 * Calculate the logistical cost of all items
	 * @param Cart_Container $cartContainer The cart to calculate.
         * @param array|null Additional options, if any.
	 * @return array The total for each item.
	 */
	public function calculate(Cart_Container $cartContainer, $options = null) {
            // ...There isn't any additional charge to download.
            $result = array();
            foreach($cartContainer->getItems() as $item) {
                $item = Cart_Container_Item::factory($item);

                // Assume we aren't charging additional fees for a download.
                $result[$item->getSku()] = 0.00;
            }

            return $result;
        }

	/**
	 * Get the list of required fields for this adapter.
	 * @param array|null $options The array of required fields.
	 */
	public function getRequiredFields($options = null) {
            $downloadMethods = array(
                'download' => 'Internet Download'
            );

            if (isset($options) && isset($options['download_methods'])) {
                $downloadMethods = $options['download_methods'];
            }

            $result = array(
                'download_method' => array(
                    'label' => 'Download method',
                    'type'  => $downloadMethods
                )
            );

            return $result;
        }
	/**
	 * Get the list of optional fields for this adapter.
	 * @param array|null $options The array of optional fields.
	 */
	public function getOptionalFields($options = null) {
            return null;
        }
	/**
	 *
	 * @param array $data The data to process
	 * @param array $options Additional array of options
	 */
	public function postProcess($data, $options = null) {
            /**
             * @todo Generate one-time download link
             */
             $result = array();

             $productIds = $data;
             $products = new Zre_Dataset_Product();

             foreach($productIds as $id) {
                 $product = $products->read($id);


             }

             return $result;
        }
}