<?php
class Zre_Dataset_Orders extends Data_Set_Abstract {
	protected $_modelName = 'Zre_Dataset_Model_Orders';
	
	public function listAllComposite($where = null, $options = null) {
		$model = $this->getModel();
		
		/**
		 * SELECT * FROM 
		 *  orders, orders_products, products 
		 * WHERE
		 *  orders.order_id = orders_products.order_id AND
		 *  products.product_id = orders_products.product_id
		 */
		$settings = Zre_Config::getSettingsCached();
		$pre = $settings->db->table_name_prepend;
		$select = $model->select()->setIntegrityCheck(false)
					->from(array('op' => $pre . 'orders_products'))
					->join(array('o' => $pre . 'orders'), 'op.order_id = o.order_id')
					->join(array('p' => $pre . 'product'), 'op.product_id = p.product_id');
		
		$select = parent::appendOptions($select, null, $options);
		
		$result = $model->fetchAll($select)->toArray();
		return $result;
	}
	
}