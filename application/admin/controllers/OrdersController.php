<?php
/**
 * ZRECommerce e-commerce web application.
 * 
 * @author ZRECommerce
 * 
 * @package Admin
 * @subpackage Admin_Orders
 * @category Controllers
 * 
 * @version $Id$
 * @copyright Copyrights 2008 ZRECommerce. All rights reserved.
 * @license Creative Commons license - See public/license.txt
 */

/**
 * OrdersController - Order administration controller.
 *
 */
class Admin_OrdersController extends Zend_Controller_Action {
	
	public function preDispatch() {
		$zend_auth = Zend_Auth::getInstance();
		$zend_auth->setStorage( new Zend_Auth_Storage_Session() );
		$settings = Zre_Config::getSettingsCached();
		
		if (!Zre_Template::isHttps() && $settings->site->enable_ssl == 'yes') {
			$this->_redirect('https://' . $settings->site->url . '/admin/', array('exit' => true));
		}
		
		// All pages here require a valid login. Kick out if invalid.
		if ( $zend_auth->hasIdentity() && 
			(Zre_Acl::isAllowed('orders', 'view') ||
			Zre_Acl::isAllowed('administration', 'ALL')) ) 
		{
//			Zend_Session::rememberUntil( (int)$settings->site->session_timeout );
		} else {
			Zre_Log::log(Zre_Acl::isAllowed('administration', 'ALL'), LOG_DEBUG);
			$this->_redirect('/admin/login', array('exit'=>'true'));
		}
		
		$this->view->assign('enable_admin_menu', 1);
		Zre_Registry_Session::set('selectedMenuItem', 'Orders');
		Zre_Registry_Session::save();
		
		$this->_helper->layout->disableLayout();
	}
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		// TODO Auto-generated OrdersController::indexAction() default action
		$this->view->title = 'Orders';
		
		$request = $this->getRequest();
		$orderObject = new Zre_Dataset_Orders();
		$headers = $orderObject->info('cols');
		$page = $request->getParam('page', 1);
		$rowCount = $request->getParam('count', 30);
		
		$orderCol = strtolower( $request->getParam('col', $headers[0]) );
		$orderSort = strtolower( $request->getParam('sort', 'asc') );
		
		if (!in_array($orderCol, $headers)) $orderCol = $headers[0];
		if (!in_array($orderSort, array('asc', 'desc'))) $orderSort = 'asc';
		
		$options = array(
			'order' => 'o.' . $orderCol . ' ' . $orderSort,
			'limit' => array('page' => $page, 'rowCount' => $rowCount)
		);
		
		$orders = $orderObject->listAllComposite(null, $options);
		
		$options = array(
			'from' => array(
				'name' => $orderObject->getModel()->info('name'), 
				'cols' => new Zend_Db_Expr('COUNT(*) AS total')
			)
		);
		
		$count = $orderObject->listAll(null, $options, false)->current()->total;
		
		$this->view->col = $orderCol;
		$this->view->sort = $orderSort;
		$this->view->page = $page;
		$this->view->rowCount = $rowCount;
		
		$this->view->orders = $orders;
		$this->view->headers = $headers;
	}
	
	public function jsonListAction() {
		
		$request = $this->getRequest();
		$data = null;
		
		try {
			$t = Zend_Registry::get('Zend_Translate');
			$settings = Zre_Config::getSettingsCached();
			
			$dataset = new Zre_Dataset_Orders();
			$products = new Zre_Dataset_Product();
			
			$sort = $request->getParam('sort', 'order_id');
			$order = $request->getParam('order', 'ASC');
			
			$orderCols = $dataset->info('cols');
			$productCols = $products->info('cols');
			
			$tblName = 'o';
			if ( in_array($sort, $productCols) ) $tblName = 'p';
			
			$options = array(
				'order' => $tblName . '.' . $sort . ' ' . $order
			);
			
			$records = $dataset->listAllComposite(null, $options);
			
			$data = array(
				'result' => 'ok',
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
	
	public function jsonUpdateAction() {
		$request = $this->getRequest();
		
		try {
			$orders = new Zre_Dataset_Orders();
			$order_id = $request->getParam('order_id', null);
			$status = $request->getParam('status', null);
			
			if (isset($order_id) && isset($status)) {
				$result = (int)$orders->update(array('status' => $status), (int)$order_id);
				
				$data = array(
					'result' => 'ok',
					'data' => $result,
					'order_id' => $order_id,
					'status' => $status
				);
			} else {
				throw new Zend_Exception('Invalid parameters. No order ID or status specified.');
			}
		} catch (Exception $e) {
			$data = array(
				'result' => 'error',
				'data' => (string) $e
			);
			Debug::log((string) $e);
		}
		$this->_helper->json($data);
	}

}