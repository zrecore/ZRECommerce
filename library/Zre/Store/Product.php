<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Store
 * @subpackage Store
 * @category Store
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * Zre_Store_Product - Provides product quantity and product property calculations.
 *
 */
class Zre_Store_Product {
	public static function countPending($product_id, $lifetime_minutes = 30) {
	    $productPendingDataset = new Zre_Dataset_ProductPending();
	    $productPending = $productPendingDataset->listAll(
		    array(
			'ppend.access_date' => array(
			    'operator' => ' >= ',
			    'value' => ' DATE_SUB(NOW, INTERVAL ' . $lifetime_minutes . ' MINUTE)'
			),
			'product_id' => $product_id
		    ),
		    array(
			'from' => array(
			    'name' => array( 'ppend' => $productPendingDataset->getModel()->info('name') ),
			    'cols' => array(
				'total' => new Zend_Db_Expr('COUNT(*)')
			    )
			)
		    ),
		    false
	    );
	    $productPending = $productPending->current()->total;

	    return $productPending;
	}
	/**
	 * Add a record to the 'product_pending' table.
	 * @param Cart_Container_Item $item The item to mark as pending.
	 * @return int The insert ID on success.
	 */
	public static function makePending($item) {
	    $productPendingDataset = new Zre_Dataset_ProductPending();

	    $productId = $item->getSku();
	    $productQuantity = $item->getQuantity();
	    
	    $id = $productPendingDataset->create(array(
		'product_id' => $productId,
		'session_id' => Zend_Session::getId(),
		'quantity' => (int) $productQuantity,
		'ip_address' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : new Zend_Db_Expr('NULL'),
		'access_date' => new Zend_Db_Expr('NOW()')
	    ));

	    return $id;
	}
	public static function flushPending($product_id) {
	    $ppend = new Zre_Dataset_ProductPending();
	    $a = $ppend->getModel()->getAdapter();

	    $query = "DELETE FROM " . $ppend->getModel()->info('name') . "
		WHERE product_id=" . $a->quote($product_id) . " AND
		      session_id=" . $a->quote(Zend_Session::getId());

	    $result = $a->query($query);

	    return $result;
	}
}
?>