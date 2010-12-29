<?php
class Zre_Dataset_Orders extends Data_Set_Abstract {
	protected $_modelName = 'Zre_Dataset_Model_Orders';
	
//	public function listAllComposite($where = null, $options = null) {
//		$model = $this->getModel();
//
//		/**
//		 * SELECT * FROM
//		 *  orders, orders_products, products
//		 * WHERE
//		 *  orders.order_id = orders_products.order_id AND
//		 *  products.product_id = orders_products.product_id
//		 */
//		$settings = Zre_Config::getSettingsCached();
//		$pre = $settings->db->table_name_prepend;
//		$select = $model->select()->setIntegrityCheck(false)
//					->from(array('op' => $pre . 'orders_products'))
//					->join(array('o' => $pre . 'orders'), 'op.order_id = o.order_id')
//					->join(array('p' => $pre . 'product'), 'op.product_id = p.product_id');
//
//		$select = parent::appendOptions($select, null, $options);
//
//		$result = $model->fetchAll($select)->toArray();
//		return $result;
//	}

	public function getProfiles($data = null, $options = null, $table_name_prepend = null) {
	    $pre = '';
		if (isset($table_name_prepend)) $pre = $table_name_prepend;

		$columnOptions = array(
			'setIntegrityCheck' => false,
			'from' => array(
			    'name' => array('oP' => $pre . 'orders_products'),
			    'cols' => array(
				'order_id',
				'product_id',
				'unit_price',
				'quantity'
			    )
			),
			'leftJoin' => array(
			    'name' => array('o' => $pre . 'orders'),
			    'cond' => 'o.order_id = oP.order_id',
			    'cols' => array(
				'decision',
				'order_date',
				'status'
			    )
			),
			'leftJoin ' => array(
			    'name' => array('p' => $pre . 'product'),
			    'cond' => 'p.product_id = oP.product_id',
			    'cols' => array(
				'article_id',
				'published',
				'title',
				'description',
				'date_created',
				'date_modified',
				'image',
				'price',
				'weight',
				'size',
				'allotment',
				'pending',
				'sold'
			    )
			)
		);
		if (isset($options)) {
			$allOptions = array_merge($options, $columnOptions);
			$options = $allOptions;
		} else {
			$options = $columnOptions;
		}
		return parent::listAll($data,$options);
	}
}