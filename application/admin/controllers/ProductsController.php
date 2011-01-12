<?php
/**
 * ZRECommerce e-commerce web application.
 *
 * @author ZRECommerce
 *
 * @package Admin
 * @subpackage Admin_Products
 * @category Controllers
 *
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. See README file.
 * @license GPL v3 or higher. See README file.
 */

/**
 * ProductController - Product inventory administration controller.
 *
 */
class Admin_ProductsController extends Zend_Controller_Action {
	public function preDispatch() {
		$zend_auth = Zend_Auth::getInstance();
		$zend_auth->setStorage( new Zend_Auth_Storage_Session() );
		$settings = Zre_Config::getSettingsCached();

		if (!Zre_Template::isHttps() && $settings->site->enable_ssl == 'yes') {
			$this->_redirect('https://' . $settings->site->url . '/admin/', array('exit' => true));
		}

		// All pages here require a valid login. Kick out if invalid.
		if ( $zend_auth->hasIdentity() &&
			(Zre_Acl::isAllowed('products', 'view') ||
				Zre_Acl::isAllowed('administration', 'ALL')) ) {
//			Zend_Session::rememberUntil( (int)$settings->site->session_timeout );
		} else {
			$this->_redirect('/admin/login', array('exit'=>'true'));
		}

		$this->view->assign('enable_admin_menu', 1);

		Zre_Registry_Session::set('selectedMenuItem', 'Products');
		Zre_Registry_Session::save();
		$this->_helper->layout->disableLayout();
	}
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		$params = '';

		$start_index = $this->getRequest()->getParam('start_index');
		$max_per_page = $this->getRequest()->getParam('max_per_page');

		$params_array = array(
			'start_index' => isset($start_index)?$start_index:0,
			'max_per_page' => $max_per_page?$max_per_page:10
		);

		// Grab our key/value POST vars, for use with our datagrid links.
		foreach($params_array as $key => $value) {
			$params .= '/'.$key . '/' . $value;
		}
		$url_params = $params;

		$this->view->assign('url_params', 	$url_params);
		$this->view->assign('start_index', 	$params_array['start_index']);
		$this->view->assign('max_per_page', $params_array['max_per_page']);

		$articles = new Zre_Dataset_Article();
		$settings = Zre_Config::getSettingsCached();
		$pre = $settings->db->table_name_prepend;
		
		$articleListing = $articles->listAll(
			array(
			    'published' => 'yes'
			),
			array(
				'setIntegrityCheck' => false,
				'from' => array(
					'name' => array('a' => $pre . 'article'),
					'cols' => array(
						'article_id',
						'title'
					)
				)
			),
			false
		);

		$logisticAdapters = Zre_Store_Shipping::logisticAdapters();
		
		$this->view->assign('articles', $articleListing);
		$this->view->assign('logistic_adapters', $logisticAdapters);
	}

	public function jsonListAction() {

		$request = $this->getRequest();
		$data = null;

		try {
			$settings = Zre_Config::getSettingsCached();

			$pre = $settings->db->table_name_prepend;

			$dataset = new Zre_Dataset_Product();

			$sort = $request->getParam('sort', 'title');
			$order = $request->getParam('order', 'ASC');
			$page = $request->getParam('pageIndex', 1);
			$rowCount = $request->getParam('rowCount', 5);

			$options = array(
				'order' => $sort . ' ' . $order,
				'limit' => array(
					'page' => $page,
					'rowCount' => $rowCount
				)
			);

			$totalRecords = $dataset->listAll(null, array(
				'from' => array(
					'name' => array('p' => $dataset->getModel()->info('name')),
					'cols' => array(new Zend_Db_Expr('COUNT(*)'))
				)
			), false)->current()->offsetGet('COUNT(*)');

			$records = $dataset->listAll(null, $options, $pre);

			$data = array(
				'result' => 'ok',
				'totalRows' => $totalRecords,
				'data' => $records
			);

		} catch (Exception $e) {
			$data = array(
				'result' => 'error',
				'data' => (string) $e
			);
			Debug::log((string) $e);
		}
		$this->_helper->json($data);
	}

	public function jsonCreateAction() {
		$request = $this->getRequest();
		$data = null;

		try {

			$dataset = new Zre_Dataset_Product();
			$record = $request->getParams();

			$record['date_created'] = new Zend_Db_Expr('NOW()');
			$record['date_modified'] = new Zend_Db_Expr('NOW()');
			$data = $dataset->create($record);
		} catch (Exception $e) {
			Debug::log((string)$e);
			$data = 0;
		}

		$this->_helper->json($data);
	}

	public function jsonUpdateAction() {
		$request = $this->getRequest();
		$reply = null;

		try {
			$dataset = new Zre_Dataset_Product();

			$product_id = $request->getParam('product_id', null);
			$article_id = $request->getParam('article_id', null);
			$product_title = $request->getParam('title', null);
			$published = $request->getParam('published', null);
			$description = $request->getParam('description', null);
			$price = $request->getParam('price', null);
			$allotment = $request->getParam('allotment', null);
			$delivery_method = $request->getParam('delivery_method', null);

			$date = new Zend_Date(null, 'yyyy-MM-dd HH:mm:ss');
			$date_modified = $date->get('yyyy-MM-dd HH:mm:ss');

			if (!isset($product_id)) throw new Zend_Exception('No product ID specified.');

			$updateData = array(
				'product_id' => $product_id,
				'article_id' => $article_id,
				'title' => $product_title,
				'published' => $published,
				'description' => $description,
				'price' => $price,
				'allotment' => $allotment,
				'date_modified' => $date_modified,
				'delivery_method' => $delivery_method
			);

			$result = $dataset->update(
				$updateData,
				$product_id
			);

			$record = $dataset->read($product_id)->current();

			$reply = array(
				'result' => 'ok',
				'product_id' => $product_id,
				'article_id' => $record->article_id,
				'price' => $record->price,
				'allotment' => $record->allotment,
				'pending' => $record->pending,
				'sold' => $record->sold,
				'description' => $record->description,
				'title' => $record->title,
				'published' => $record->published,
				'date_modified' => $record->date_modified,
				'date_created' => $record->date_created,
				'delivery_method' => $record->delivery_method,
				'data' => $result
			);
		} catch (Exception $e) {
			Debug::log((string) $e);
			$reply = array(
				'result' => 'error',
				'product_id' => $product_id,
				'date_modified' => $date_modified,
				'data' => (string) $e
			);
		}

		$this->_helper->json($reply);
	}
}