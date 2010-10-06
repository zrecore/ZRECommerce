<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Cart
 * @category Cart
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. All rights reserved.
 * @license Creative Commons license - See public/license.txt
 */
/**
 * Cart_Soap_Data_Item - This is the basic 'item' class.
 *
 */
class Cart_Soap_Data_Item {
	/**
	 * Stock Keeping Unit, the unique identifier used by this item.
	 * @var string
	 */
	private $_sku;
	/**
	 * Collection of detail strings.
	 * @var Cart_Container_Item_Options_Detail
	 */
	private $_detailOptions;
	/**
	 * Collection of cost values.
	 * @var Cart_Container_Item_Options_Cost
	 */
	private $_costOptions;
	/**
	 * Collection of measurement metrics
	 * @var Cart_Container_Item_Options_Metric
	 */
	private $_metricOptions;
	/**
	 * Quantity of whatever this item represents.
	 * @var int
	 */
	private $_quantity;
	/**
	 * The string of text to display when this item is paid for.
	 * @var string
	 */
	private $_purchasedMessage = '';
	/**
	 * Internal array of validators. Validators must implement
	 * Cart_Container_Validate_Interface
	 * @var array
	 */
	private $_validators;
}